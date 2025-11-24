// Pedidos e Acompanhamento

let orderFilter = 'all'; // all, active, completed, cancelled

function showOrders() {
    setActivePage('orders');
    
    // Parar tracking se estiver ativo
    stopOrderTracking();
    
    const content = `
        <div class="fade-in">
            <h5 class="mb-3 fw-bold">
                <i class="fas fa-receipt me-2"></i>Meus Pedidos
            </h5>
            
            <!-- Filtros de Status -->
            <div class="btn-group w-100 mb-3" role="group">
                <button type="button" class="btn btn-sm btn-outline-primary" 
                        onclick="filterOrders('all')" id="filterAll">
                    Todos
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary" 
                        onclick="filterOrders('active')" id="filterActive">
                    Ativos
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary" 
                        onclick="filterOrders('completed')" id="filterCompleted">
                    Finalizados
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" 
                        onclick="filterOrders('cancelled')" id="filterCancelled">
                    Cancelados
                </button>
            </div>
            
            <div id="ordersList">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Carregando pedidos...</p>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('mainContent').innerHTML = content;
    updateFilterButtons();
    loadOrders();
}

async function loadOrders() {
    if (!appState.user || !appState.token) {
        document.getElementById('ordersList').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-user-lock fa-3x text-muted mb-3"></i>
                <h6>Faça login para ver seus pedidos</h6>
                <button class="btn btn-primary-custom mt-3" onclick="setTimeout(() => showLogin(), 100)">
                    <i class="fas fa-sign-in-alt me-2"></i>Entrar
                </button>
            </div>
        `;
        return;
    }
    
    try {
        // Buscar pedidos do cliente autenticado
        const response = await authFetch(`${API_BASE_URL}/app/pedidos`);
        const data = await response.json();
        
        appState.orders = data.data || data || [];
        
        if (appState.orders.length === 0) {
            document.getElementById('ordersList').innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                    <h6>Nenhum pedido encontrado</h6>
                    <p class="text-muted">Faça seu primeiro pedido!</p>
                    <button class="btn btn-primary-custom mt-3" onclick="showMenu()">
                        <i class="fas fa-utensils me-2"></i>Ver Cardápio
                    </button>
                </div>
            `;
            return;
        }
        
        // Ordenar por data (mais recente primeiro)
        appState.orders.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        
        // Aplicar filtro
        const filteredOrders = filterOrdersByStatus(appState.orders);
        
        if (filteredOrders.length === 0) {
            const filterMessages = {
                'all': 'Nenhum pedido encontrado',
                'active': 'Nenhum pedido ativo',
                'completed': 'Nenhum pedido finalizado',
                'cancelled': 'Nenhum pedido cancelado'
            };
            
            document.getElementById('ordersList').innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h6>${filterMessages[orderFilter]}</h6>
                    ${orderFilter !== 'all' ? '<button class="btn btn-sm btn-outline-primary mt-2" onclick="filterOrders(\'all\')">Ver Todos</button>' : ''}
                </div>
            `;
            return;
        }
        
        const ordersHtml = filteredOrders.map(order => renderOrderCard(order)).join('');
        document.getElementById('ordersList').innerHTML = ordersHtml;
        
    } catch (error) {
        console.error('Erro ao carregar pedidos:', error);
        if (error.message !== 'Unauthorized' && error.message !== 'No token') {
            document.getElementById('ordersList').innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                    <p class="text-muted">Erro ao carregar pedidos</p>
                    <button class="btn btn-primary" onclick="loadOrders()">Tentar novamente</button>
                </div>
            `;
        }
    }
}

function renderOrderCard(order) {
    const statusLabels = {
        'pendente': 'Aguardando',
        'aberto': 'Aguardando',
        'confirmado': 'Confirmado',
        'preparando': 'Em Preparo',
        'pronto': 'Pronto',
        'em_transito': 'Saiu para Entrega',
        'saiu_entrega': 'Saiu para Entrega',
        'entregue': 'Entregue',
        'cancelado': 'Cancelado',
        'pago': 'Pago'
    };
    
    const statusIcons = {
        'pendente': 'clock',
        'aberto': 'clock',
        'confirmado': 'check',
        'preparando': 'fire',
        'pronto': 'check-circle',
        'em_transito': 'motorcycle',
        'saiu_entrega': 'motorcycle',
        'entregue': 'flag-checkered',
        'cancelado': 'times-circle',
        'pago': 'dollar-sign'
    };
    
    // Usar status do delivery se existir, senão usar status do pedido
    const currentStatus = order.delivery?.status || order.status;
    
    const date = new Date(order.created_at);
    const formattedDate = date.toLocaleDateString('pt-BR');
    const formattedTime = date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    
    return `
        <div class="card mb-3" onclick="showOrderDetail(${order.id})" style="cursor: pointer; transition: all 0.2s;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="mb-1 fw-bold">Pedido #${order.id}</h6>
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>${formattedDate} às ${formattedTime}
                        </small>
                    </div>
                    <span class="order-status ${currentStatus}">
                        <i class="fas fa-${statusIcons[currentStatus] || 'question'} me-1"></i>
                        ${statusLabels[currentStatus] || currentStatus}
                    </span>
                </div>
                
                <div class="mb-2">
                    <small class="text-muted">
                        ${order.itens_count || order.itens?.length || 0} ${(order.itens_count || order.itens?.length) === 1 ? 'item' : 'itens'}
                    </small>
                </div>
                
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-primary fs-5">
                        R$ ${parseFloat(order.total).toFixed(2)}
                    </span>
                    <i class="fas fa-chevron-right text-muted"></i>
                </div>
            </div>
        </div>
    `;
}

async function showOrderDetail(orderId) {
    console.log('🔍 Abrindo detalhes do pedido #' + orderId);
    
    // Parar tracking anterior se houver
    stopOrderTracking();
    
    const content = `
        <div class="fade-in">
            <div class="d-flex align-items-center mb-3">
                <button class="btn btn-link text-dark p-0 me-3" onclick="showOrders()">
                    <i class="fas fa-arrow-left fa-lg"></i>
                </button>
                <h5 class="mb-0 fw-bold">Pedido #${orderId}</h5>
            </div>
            
            <div id="orderDetailContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('mainContent').innerHTML = content;
    
    try {
        console.log('📡 Buscando pedido #' + orderId + ' na API...');
        const response = await authFetch(`${API_BASE_URL}/app/pedidos/${orderId}`);
        const data = await response.json();
        
        // A API pode retornar {success: true, pedido: {...}} ou apenas o pedido
        const order = data.pedido || data;
        
        console.log('✅ Dados do pedido recebidos:', order);
        console.log('🚚 Dados do delivery:', order.delivery);
        console.log('👤 Entregador Nome:', order.entregador_nome);
        console.log('🚗 Veículo:', order.entregador_veiculo);
        
        renderOrderDetail(order);
        
        // Iniciar atualização automática do status
        startOrderTracking(orderId);
        
    } catch (error) {
        console.error('❌ Erro ao carregar detalhes do pedido:', error);
        console.error('Stack trace:', error.stack);
        
        document.getElementById('orderDetailContent').innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                <p class="text-muted">Erro ao carregar detalhes do pedido</p>
                <p class="small text-danger">${error.message}</p>
                <button class="btn btn-primary mt-3" onclick="showOrderDetail(${orderId})">
                    <i class="fas fa-redo me-2"></i>Tentar Novamente
                </button>
                <button class="btn btn-outline-secondary mt-3" onclick="showOrders()">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </button>
            </div>
        `;
    }
}

function renderOrderDetail(order) {
    console.log('Renderizando pedido:', order);
    console.log('Status do pedido:', order.status);
    console.log('Status do delivery:', order.delivery?.status);
    
    // Usar status do delivery se existir, senão usar status do pedido
    const currentStatus = order.delivery?.status || order.status;
    console.log('Status atual (efetivo):', currentStatus);
    
    const statusLabels = {
        'pendente': 'Aguardando Confirmação',
        'aberto': 'Aguardando Confirmação',
        'confirmado': 'Pedido Confirmado',
        'preparando': 'Em Preparo',
        'pronto': 'Pedido Pronto',
        'em_transito': 'Saiu para Entrega',
        'saiu_entrega': 'Saiu para Entrega',
        'entregue': 'Entregue',
        'cancelado': 'Cancelado',
        'pago': 'Pago'
    };
    
    const date = new Date(order.created_at);
    const formattedDate = date.toLocaleDateString('pt-BR', { day: '2-digit', month: 'long', year: 'numeric' });
    const formattedTime = date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    
    const content = `
        <!-- Status Atual -->
        <div class="card mb-3 text-center" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; border: none;">
            <div class="card-body py-4">
                <i class="fas ${getStatusIcon(currentStatus)} fa-3x mb-2"></i>
                <h5 class="mb-1">${statusLabels[currentStatus] || currentStatus}</h5>
                <small class="opacity-75">
                    <i class="fas fa-sync fa-spin"></i> Atualizando automaticamente
                </small>
            </div>
        </div>
        
        <!-- Timeline do Pedido -->
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-route text-primary me-2"></i>
                        Acompanhamento
                    </h5>
                    <span class="badge bg-success" id="liveIndicator">
                        <i class="fas fa-circle pulse-dot"></i> Ao Vivo
                    </span>
                </div>
                ${renderOrderTimeline(currentStatus, {
                    delivery_id: order.delivery?.id,
                    entregador: order.delivery?.entregador_nome || order.entregador_nome || 'Entregador',
                    tempo: order.delivery?.tempo_estimado || '30',
                    veiculo: order.delivery?.entregador?.tipo_veiculo || order.entregador_veiculo || null,
                    itens: order.itens || [],
                    total: order.total || 0,
                    // Dados do delivery para o mapa
                    endereco_rua: order.delivery?.endereco_rua,
                    endereco_numero: order.delivery?.endereco_numero,
                    endereco_bairro: order.delivery?.endereco_bairro,
                    endereco_cidade: order.delivery?.endereco_cidade,
                    destino_latitude: order.delivery?.destino_latitude,
                    destino_longitude: order.delivery?.destino_longitude,
                    entregador_latitude: order.delivery?.entregador_latitude,
                    entregador_longitude: order.delivery?.entregador_longitude
                })}
            </div>
        </div>
        
        <!-- Informações do Pedido -->
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Informações</h6>
                <div class="mb-2">
                    <small class="text-muted">Data do Pedido</small>
                    <p class="mb-0">${formattedDate} às ${formattedTime}</p>
                </div>
                ${order.delivery ? `
                    <div class="mt-3">
                        <small class="text-muted">Endereço de Entrega</small>
                        <p class="mb-0">
                            ${order.delivery.endereco_rua || 'N/A'}, ${order.delivery.endereco_numero || 'S/N'}<br>
                            ${order.delivery.endereco_bairro || ''} - ${order.delivery.endereco_cidade || ''}
                        </p>
                    </div>
                ` : ''}
            </div>
        </div>
        
        <!-- Itens do Pedido -->
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Itens do Pedido</h6>
                ${order.itens ? order.itens.map(item => `
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <div>
                            <p class="mb-0">${item.quantidade}x ${item.produto?.nome || 'Produto'}</p>
                            ${item.observacoes ? `<small class="text-muted">${item.observacoes}</small>` : ''}
                        </div>
                        <span class="fw-bold">R$ ${parseFloat(item.subtotal).toFixed(2)}</span>
                    </div>
                `).join('') : '<p class="text-muted">Sem itens</p>'}
                
                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                    <span class="fw-bold">Total</span>
                    <span class="fw-bold text-primary fs-5">R$ ${parseFloat(order.total).toFixed(2)}</span>
                </div>
            </div>
        </div>
        
        <!-- Botões de Ação -->
        ${order.status !== 'cancelado' && order.status !== 'entregue' ? `
            <div class="d-grid gap-2 mb-3">
                <button class="btn btn-outline-danger" onclick="cancelOrder(${order.id})">
                    <i class="fas fa-times me-2"></i>Cancelar Pedido
                </button>
            </div>
        ` : ''}
        
        <!-- Ajuda -->
        <div class="card">
            <div class="card-body text-center">
                <p class="mb-2"><small class="text-muted">Precisa de ajuda com seu pedido?</small></p>
                <a href="tel:1140028922" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-phone me-2"></i>Ligar para o Restaurante
                </a>
            </div>
        </div>
    `;
    
    document.getElementById('orderDetailContent').innerHTML = content;
}

function renderOrderTimeline(currentStatus, deliveryInfo = null) {
    console.log('Renderizando timeline para status:', currentStatus);
    
    // Normalizar status
    const normalizedStatus = currentStatus === 'pendente' ? 'aberto' : 
                             currentStatus === 'em_transito' ? 'saiu_entrega' : 
                             currentStatus;
    
    const steps = [
        { status: 'aberto', label: 'Pedido Recebido', icon: '📋', color: '#6366f1', desc: 'Aguardando confirmação' },
        { status: 'confirmado', label: 'Confirmado', icon: '✅', color: '#10b981', desc: 'Pedido aceito' },
        { status: 'preparando', label: 'Em Preparo', icon: '👨‍🍳', color: '#f59e0b', desc: 'Sendo preparado com carinho' },
        { status: 'pronto', label: 'Pronto!', icon: '🎉', color: '#8b5cf6', desc: 'Pedido finalizado' },
        { status: 'saiu_entrega', label: 'A Caminho', icon: '🛵', color: '#3b82f6', desc: 'Saiu para entrega' },
        { status: 'entregue', label: 'Entregue', icon: '🏁', color: '#10b981', desc: 'Pedido entregue com sucesso!' }
    ];
    
    const statusIndex = steps.findIndex(s => s.status === normalizedStatus);
    const finalStatusIndex = statusIndex >= 0 ? statusIndex : 0;
    
    // Se cancelado
    if (normalizedStatus === 'cancelado') {
        return `
            <div class="text-center py-4">
                <div class="mb-2" style="font-size: 48px;">❌</div>
                <h6 class="fw-bold mb-1">Pedido Cancelado</h6>
                <p class="text-muted small">Este pedido foi cancelado</p>
            </div>
        `;
    }
    
    // Card do entregador com mapa (se em rota)
    let deliveryCard = '';
    if (normalizedStatus === 'saiu_entrega' && deliveryInfo) {
        // Ícone baseado no veículo
        const veiculoInfo = {
            'moto': { icon: '🏍️', texto: 'de moto' },
            'carro': { icon: '🚗', texto: 'de carro' },
            'bicicleta': { icon: '🚲', texto: 'de bicicleta' },
            'pe': { icon: '🚶', texto: 'a pé' },
            'a pe': { icon: '🚶', texto: 'a pé' },
            'default': { icon: '🛵', texto: 'até você' }
        };
        
        const veiculo = deliveryInfo.veiculo?.toLowerCase() || 'default';
        const veiculoData = veiculoInfo[veiculo] || veiculoInfo['default'];
        
        deliveryCard = `
            <!-- Mapa de Rastreamento -->
            <div class="card mb-4 border-0 shadow-lg overflow-hidden">
                <div id="trackingMap" style="height: 300px; width: 100%; position: relative;">
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; z-index: 1000;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando mapa...</span>
                        </div>
                        <p class="mt-2 text-muted">Carregando localização...</p>
                    </div>
                </div>
                
                <!-- Controles do Mapa -->
                <div class="card-body bg-light p-2">
                    <div class="d-flex justify-content-around">
                        <button class="btn btn-sm btn-outline-primary" onclick="centerOnEntregador()">
                            <i class="fas fa-biking me-1"></i> Entregador
                        </button>
                        <button class="btn btn-sm btn-outline-primary" onclick="showBothMarkers()">
                            <i class="fas fa-route me-1"></i> Rota
                        </button>
                        <button class="btn btn-sm btn-outline-primary" onclick="centerOnDestino()">
                            <i class="fas fa-home me-1"></i> Destino
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Info do Entregador -->
            <div class="card mb-4 border-0 shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white p-4">
                    <!-- Cabeçalho do Entregador -->
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-white d-flex align-items-center justify-content-center me-2" 
                             style="width: 48px; height: 48px; font-size: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                            ${veiculoData.icon}
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold">${deliveryInfo.entregador || 'Entregador'}</h6>
                            <p class="mb-0 opacity-90 small">
                                Está indo ${veiculoData.texto}
                            </p>
                        </div>
                        <div class="text-end">
                            <div class="badge bg-white text-primary px-2 py-1" style="font-size: 0.85rem;">
                                <i class="fas fa-clock me-1"></i> ${deliveryInfo.tempo || '30'}'
                            </div>
                        </div>
                    </div>
                    
                    <!-- Distância -->
                    <div class="d-flex justify-content-around text-center mt-3">
                        <div>
                            <h6 class="mb-0 fw-bold" id="distanciaEntregador">-- km</h6>
                            <small class="opacity-75" style="font-size: 0.75rem;">Distância</small>
                        </div>
                        <div style="border-left: 1px solid rgba(255,255,255,0.3);"></div>
                        <div>
                            <h6 class="mb-0 fw-bold">${deliveryInfo.tempo || '30'} min</h6>
                            <small class="opacity-75" style="font-size: 0.75rem;">Tempo estimado</small>
                        </div>
                    </div>
                    
                    <!-- Divisor -->
                    <hr class="border-white opacity-25 my-3">
                    
                    <!-- Informação de entrega -->
                    <div class="text-center">
                        <p class="mb-0 small opacity-75">
                            <i class="fas fa-map-marker-alt me-1"></i> 
                            Seu pedido está a caminho
                        </p>
                    </div>
                </div>
            </div>
        `;
        
        // Inicializar mapa após renderização
        setTimeout(() => {
            if (typeof initTrackingMap === 'function' && deliveryInfo.delivery_id) {
                initTrackingMap('trackingMap', deliveryInfo);
                startDeliveryTracking(deliveryInfo.delivery_id);
            }
        }, 100);
    }
    
    // Timeline moderna estilo fast-food
    const timeline = steps.map((step, index) => {
        const isCompleted = index <= finalStatusIndex;
        const isCurrent = index === finalStatusIndex;
        const isPast = index < finalStatusIndex;
        
        return `
            <div class="position-relative mb-4">
                <div class="d-flex align-items-center">
                    <!-- Ícone do status -->
                    <div class="position-relative flex-shrink-0 me-2" style="z-index: 2;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center ${isCurrent ? 'pulse-animation' : ''}" 
                             style="width: 44px; height: 44px; 
                                    background: ${isCompleted ? step.color : '#e5e7eb'}; 
                                    box-shadow: ${isCurrent ? '0 0 0 3px rgba(99, 102, 241, 0.2)' : 'none'};
                                    transition: all 0.3s;">
                            <span style="font-size: 20px;">${step.icon}</span>
                        </div>
                    </div>
                    
                    <!-- Informações -->
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-1">
                            <h6 class="mb-0 ${isCurrent ? 'fw-bold' : 'fw-semibold'}" 
                                style="color: ${isCompleted ? step.color : '#6b7280'}; font-size: 0.95rem;">
                                ${step.label}
                            </h6>
                            ${isCurrent ? `
                                <span class="badge ms-2 pulse" 
                                      style="background: ${step.color}; font-size: 10px; padding: 4px 8px;">
                                    EM ANDAMENTO
                                </span>
                            ` : ''}
                            ${isPast ? `
                                <i class="fas fa-check-circle ms-2" style="color: ${step.color};"></i>
                            ` : ''}
                        </div>
                        <p class="mb-0 small ${isCompleted ? 'text-muted' : 'text-secondary'}">${step.desc}</p>
                        ${isCurrent ? `<div class="progress mt-2" style="height: 4px;"><div class="progress-bar bg-primary progress-bar-animated" style="width: 100%; background: ${step.color} !important;"></div></div>` : ''}
                    </div>
                </div>
                
                <!-- Linha conectora -->
                ${index < steps.length - 1 ? `
                    <div class="position-absolute" 
                         style="left: 27px; top: 56px; width: 2px; height: 40px; 
                                background: ${isCompleted ? 'linear-gradient(to bottom, ' + step.color + ', ' + steps[index + 1].color + ')' : '#e5e7eb'};">
                    </div>
                ` : ''}
            </div>
        `;
    }).join('');
    
    return deliveryCard + '<div class="py-2">' + timeline + '</div>';
}

function getStatusIcon(status) {
    const icons = {
        'pendente': 'fa-clock',
        'aberto': 'fa-clock',
        'confirmado': 'fa-check',
        'preparando': 'fa-fire',
        'pronto': 'fa-check-circle',
        'em_transito': 'fa-motorcycle',
        'saiu_entrega': 'fa-motorcycle',
        'entregue': 'fa-flag-checkered',
        'cancelado': 'fa-times-circle'
    };
    
    return icons[status] || 'fa-question';
}

if (typeof trackingInterval === 'undefined') var trackingInterval = null;
if (typeof lastKnownStatus === 'undefined') var lastKnownStatus = null;

function stopOrderTracking() {
    console.log('⏹️ Parando tracking de pedido...');
    if (trackingInterval) {
        clearInterval(trackingInterval);
        trackingInterval = null;
    }
    lastKnownStatus = null;
}

function startOrderTracking(orderId) {
    console.log('🔄 Iniciando tracking do pedido #' + orderId);
    
    // Limpar intervalo anterior se existir
    stopOrderTracking();
    
    // Atualizar a cada 5 segundos para tempo real
    trackingInterval = setInterval(async () => {
        try {
            console.log('🔎 Verificando atualizações do pedido #' + orderId + '...');
            const response = await authFetch(`${API_BASE_URL}/app/pedidos/${orderId}`);
            const data = await response.json();
            const order = data.pedido || data;
            
            // Usar status do delivery se existir, senão usar status do pedido
            const currentStatus = order.delivery?.status || order.status;
            
            // Verificar se houve mudança de status
            if (lastKnownStatus && lastKnownStatus !== currentStatus) {
                console.log('✅ STATUS MUDOU!', lastKnownStatus, '->', currentStatus);
                
                // Notificar usuário
                showStatusChangeNotification(currentStatus);
                
                // Vibrar com padrão específico para cada status
                vibrateForStatus(currentStatus);
            }
            
            lastKnownStatus = currentStatus;
            renderOrderDetail(order);
            
        } catch (error) {
            console.error('❌ Erro ao atualizar pedido:', error);
        }
    }, 5000); // 5 segundos
    
    console.log('✅ Tracking iniciado com intervalo de 5 segundos');
}

// Limpar intervalo quando sair da página de detalhes
window.addEventListener('beforeunload', () => {
    if (trackingInterval) {
        clearInterval(trackingInterval);
    }
});

/**
 * Vibra o celular com padrão específico para cada status
 */
function vibrateForStatus(status) {
    // Verificar se o dispositivo suporta vibração
    if (!('vibrate' in navigator)) {
        console.log('📵 Dispositivo não suporta vibração');
        return;
    }
    
    // Padrões de vibração: [vibrar, pausa, vibrar, pausa, ...]
    // Valores em milissegundos
    const vibrationPatterns = {
        'confirmado': [200, 100, 200], // Dois toques curtos
        'preparando': [300, 150, 300, 150, 300], // Três toques médios
        'pronto': [500, 200, 500], // Dois toques longos
        'em_transito': [100, 50, 100, 50, 100, 50, 100], // Quatro toques rápidos
        'saiu_entrega': [100, 50, 100, 50, 100, 50, 100], // Quatro toques rápidos
        'entregue': [800], // Um toque longo
        'cancelado': [200, 100, 200, 100, 200, 100, 200] // Quatro toques de alerta
    };
    
    const pattern = vibrationPatterns[status] || [200, 100, 200]; // Padrão padrão
    
    try {
        navigator.vibrate(pattern);
        console.log('📳 Vibrando:', status, pattern);
    } catch (error) {
        console.error('Erro ao vibrar:', error);
    }
}

function showStatusChangeNotification(newStatus) {
    const statusMessages = {
        'pendente': '📋 Pedido recebido!',
        'aberto': '📋 Pedido recebido!',
        'confirmado': '✅ Pedido confirmado pelo restaurante!',
        'preparando': '👨‍🍳 Seu pedido está sendo preparado!',
        'pronto': '🎉 Pedido pronto! Preparando para entrega...',
        'em_transito': '🚚 Seu pedido saiu para entrega!',
        'saiu_entrega': '🚚 Seu pedido saiu para entrega!',
        'entregue': '✅ Pedido entregue! Bom apetite!',
        'cancelado': '❌ Pedido cancelado'
    };
    
    const message = statusMessages[newStatus] || 'Status do pedido atualizado!';
    const type = newStatus === 'cancelado' ? 'danger' : 'success';
    
    // Criar notificação toast
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed top-0 start-50 translate-middle-x shadow-lg`;
    toast.style.cssText = 'z-index: 9999; margin-top: 80px; min-width: 300px; animation: slideDown 0.3s ease-out;';
    toast.innerHTML = `
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <strong>${message}</strong>
                <div class="small">Atualizado agora</div>
            </div>
            <button type="button" class="btn-close ms-3" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Remover após 5 segundos
    setTimeout(() => {
        toast.style.animation = 'slideUp 0.3s ease-out';
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

async function cancelOrder(orderId) {
    if (!confirm('Deseja realmente cancelar este pedido?')) {
        return;
    }
    
    try {
        const response = await authFetch(`${API_BASE_URL}/app/pedidos/${orderId}/cancelar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            showAlert('Pedido cancelado com sucesso', 'success');
            showOrderDetail(orderId);
        } else {
            showAlert('Erro ao cancelar pedido', 'danger');
        }
    } catch (error) {
        console.error('Erro ao cancelar:', error);
        if (error.message !== 'Unauthorized' && error.message !== 'No token') {
            showAlert('Erro ao cancelar pedido', 'danger');
        }
    }
}

// ============================================
// SISTEMA DE FILTROS DE PEDIDOS
// ============================================

function filterOrders(filter) {
    orderFilter = filter;
    updateFilterButtons();
    
    // Re-renderizar lista filtrada
    if (appState.orders && appState.orders.length > 0) {
        const filteredOrders = filterOrdersByStatus(appState.orders);
        
        if (filteredOrders.length === 0) {
            const filterMessages = {
                'all': 'Nenhum pedido encontrado',
                'active': 'Nenhum pedido ativo',
                'completed': 'Nenhum pedido finalizado',
                'cancelled': 'Nenhum pedido cancelado'
            };
            
            document.getElementById('ordersList').innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h6>${filterMessages[orderFilter]}</h6>
                    ${orderFilter !== 'all' ? '<button class="btn btn-sm btn-outline-primary mt-2" onclick="filterOrders(\'all\')">Ver Todos</button>' : ''}
                </div>
            `;
        } else {
            const ordersHtml = filteredOrders.map(order => renderOrderCard(order)).join('');
            document.getElementById('ordersList').innerHTML = ordersHtml;
        }
    }
}

function filterOrdersByStatus(orders) {
    switch(orderFilter) {
        case 'active':
            return orders.filter(o => ['aberto', 'confirmado', 'preparando', 'pronto', 'saiu_entrega'].includes(o.status));
        case 'completed':
            return orders.filter(o => ['entregue', 'finalizado', 'pago'].includes(o.status));
        case 'cancelled':
            return orders.filter(o => o.status === 'cancelado');
        default:
            return orders;
    }
}

function updateFilterButtons() {
    // Remover classe active de todos
    ['filterAll', 'filterActive', 'filterCompleted', 'filterCancelled'].forEach(id => {
        const btn = document.getElementById(id);
        if (btn) {
            btn.classList.remove('active');
        }
    });
    
    // Adicionar classe active no filtro selecionado
    const filterMap = {
        'all': 'filterAll',
        'active': 'filterActive',
        'completed': 'filterCompleted',
        'cancelled': 'filterCancelled'
    };
    
    const activeBtn = document.getElementById(filterMap[orderFilter]);
    if (activeBtn) {
        activeBtn.classList.add('active');
    }
}
