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
                <h5 class="mb-0 fw-bold" id="orderDetailTitle">Pedido #${orderId}</h5>
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
        
        // Atualizar título com numero_pedido
        document.getElementById('orderDetailTitle').textContent = `Pedido #${order.numero_pedido || order.id}`;
        
        console.log('✅ Dados do pedido recebidos:', order);
        
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
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">Acompanhamento</h6>
                    <span class="badge bg-success" id="liveIndicator">
                        <i class="fas fa-circle pulse-dot"></i> Ao Vivo
                    </span>
                </div>
                ${renderOrderTimeline(currentStatus)}
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

function renderOrderTimeline(currentStatus) {
    console.log('Renderizando timeline para status:', currentStatus);
    
    // Normalizar status (pendente e aberto são equivalentes)
    const normalizedStatus = currentStatus === 'pendente' ? 'aberto' : 
                             currentStatus === 'em_transito' ? 'saiu_entrega' : 
                             currentStatus;
    
    const steps = [
        { status: 'aberto', label: 'Pedido Recebido', icon: 'receipt', desc: 'Aguardando confirmação' },
        { status: 'confirmado', label: 'Confirmado', icon: 'check', desc: 'Pedido aceito' },
        { status: 'preparando', label: 'Em Preparo', icon: 'fire', desc: 'Sendo preparado' },
        { status: 'pronto', label: 'Pronto', icon: 'check-circle', desc: 'Pronto para sair' },
        { status: 'saiu_entrega', label: 'Em Rota', icon: 'motorcycle', desc: 'A caminho' },
        { status: 'entregue', label: 'Entregue', icon: 'flag-checkered', desc: 'Finalizado' }
    ];
    
    const statusIndex = steps.findIndex(s => s.status === normalizedStatus);
    const finalStatusIndex = statusIndex >= 0 ? statusIndex : 0;
    
    // Se status cancelado, mostrar apenas isso
    if (normalizedStatus === 'cancelado') {
        return `
            <div class="text-center py-4">
                <div class="rounded-circle bg-danger mx-auto mb-3" 
                     style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-times text-white fa-2x"></i>
                </div>
                <h6 class="text-danger fw-bold">Pedido Cancelado</h6>
                <p class="text-muted small mb-0">Este pedido foi cancelado</p>
            </div>
        `;
    }
    
    return '<div class="position-relative">' + steps.map((step, index) => {
        const isCompleted = index <= finalStatusIndex;
        const isCurrent = index === finalStatusIndex;
        const hasNext = index < steps.length - 1;
        
        return `
            <div class="d-flex align-items-start mb-3 position-relative" style="min-height: 50px;">
                ${hasNext ? `
                    <div class="position-absolute" style="left: 19px; top: 40px; bottom: -15px; width: 2px; background: ${isCompleted ? '#10b981' : '#e5e7eb'};"></div>
                ` : ''}
                <div class="flex-shrink-0 me-3 position-relative" style="z-index: 1;">
                    <div class="rounded-circle ${isCompleted ? 'bg-success' : 'bg-light border border-2'}" 
                         style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; transition: all 0.3s;">
                        <i class="fas fa-${step.icon} ${isCompleted ? 'text-white' : 'text-muted'}"></i>
                    </div>
                </div>
                <div class="flex-grow-1 pt-2">
                    <p class="mb-0 ${isCurrent ? 'fw-bold text-primary' : isCompleted ? 'fw-semibold' : 'text-muted'}">
                        ${step.label}
                        ${isCurrent ? '<span class="badge bg-primary ms-2 pulse">Agora</span>' : ''}
                        ${isCompleted && !isCurrent ? '<i class="fas fa-check text-success ms-2"></i>' : ''}
                    </p>
                    <small class="text-muted">${step.desc}</small>
                </div>
            </div>
        `;
    }).join('') + '</div>';
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

let trackingInterval = null;
let lastKnownStatus = null;

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
