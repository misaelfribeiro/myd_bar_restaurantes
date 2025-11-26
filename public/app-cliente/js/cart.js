// Carrinho de Compras

async function showCart() {
    console.log('🛒 [CART] showCart() chamado');
    console.log('🛒 [CART] Itens no carrinho:', appState.cart.length);
    setActivePage('cart');
    
    if (appState.cart.length === 0) {
        const content = `
            <div class="fade-in text-center py-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                <h5>Carrinho Vazio</h5>
                <p class="text-muted mb-4">Adicione produtos do cardápio</p>
                <button class="btn btn-primary-custom" onclick="showMenu()">
                    <i class="fas fa-utensils me-2"></i>Ver Cardápio
                </button>
            </div>
        `;
        document.getElementById('mainContent').innerHTML = content;
        return;
    }
    
    // Buscar dados do restaurante da API
    const firstItem = appState.cart[0];
    const tenantCode = firstItem.tenant_code;
    
    let restaurantName = 'Restaurante';
    let taxaEntrega = 0;
    
    try {
        const response = await fetch(`${API_BASE_URL}/app/restaurantes`);
        const data = await response.json();
        const restaurant = data.restaurantes?.find(r => r.tenant_code === tenantCode);
        
        if (restaurant) {
            restaurantName = restaurant.nome_fantasia;
            taxaEntrega = parseFloat(restaurant.taxa_entrega_padrao || 0);
            
            // Verificar pedido mínimo
            const pedidoMinimo = parseFloat(restaurant.pedido_minimo || 0);
            if (pedidoMinimo > 0 && total < pedidoMinimo) {
                showAlert(`Valor mínimo do pedido é R$ ${pedidoMinimo.toFixed(2)}. Adicione mais R$ ${(pedidoMinimo - total).toFixed(2)} em produtos.`, 'warning');
                return;
            }
        }
    } catch (error) {
        console.error('Erro ao buscar restaurante:', error);
    }
    
    const total = calculateCartTotal();
    const taxaServico = 1.29;
    
    const content = `
        <div class="fade-in">
            <h5 class="mb-3 fw-bold">
                <i class="fas fa-shopping-cart me-2"></i>Meu Carrinho
            </h5>
            
            <!-- Info do Restaurante -->
            <div class="alert alert-info mb-3">
                <i class="fas fa-store me-2"></i>
                <strong>${restaurantName}</strong>
                <small class="d-block text-muted mt-1">
                    Produtos deste carrinho são de um único restaurante
                </small>
            </div>
            
            <!-- Items do Carrinho -->
            <div id="cartItems" class="mb-3">
                ${appState.cart.map((item, index) => renderCartItem(item, index)).join('')}
            </div>
            
            <!-- Resumo -->
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Resumo do Pedido</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span class="fw-bold">R$ ${total.toFixed(2)}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Taxa de entrega</span>
                        <span class="text-success fw-bold">R$ ${taxaEntrega.toFixed(2)}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Taxa de serviço</span>
                        <span>R$ ${taxaServico.toFixed(2)}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Total</span>
                        <span class="fw-bold text-primary fs-5">R$ ${(total + taxaEntrega + taxaServico).toFixed(2)}</span>
                    </div>
                </div>
            </div>
            
            <!-- Botões de Ação -->
            <div class="d-grid gap-2">
                <button class="btn btn-primary-custom btn-lg" onclick="proceedToCheckout()">
                    <i class="fas fa-check me-2"></i>Finalizar Pedido
                </button>
                <button class="btn btn-outline-danger" onclick="clearCart()">
                    <i class="fas fa-trash me-2"></i>Limpar Carrinho
                </button>
            </div>
        </div>
    `;
    
    document.getElementById('mainContent').innerHTML = content;
}

function renderCartItem(item, index) {
    return `
        <div class="card mb-2">
            <div class="card-body">
                <div class="d-flex gap-3">
                    <img src="${item.imagem || '/img/placeholder-food.jpg'}" 
                         style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;" 
                         alt="${item.nome}">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">${item.nome}</h6>
                        <p class="text-muted small mb-2">R$ ${item.preco.toFixed(2)}</p>
                        ${item.observacoes ? `<small class="text-muted"><i class="fas fa-comment me-1"></i>${item.observacoes}</small>` : ''}
                        
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-secondary" onclick="decreaseCartItem(${index})">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button class="btn btn-outline-secondary" disabled>
                                    ${item.quantidade}
                                </button>
                                <button class="btn btn-outline-secondary" onclick="increaseCartItem(${index})">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div>
                                <span class="fw-bold text-primary">R$ ${(item.preco * item.quantidade).toFixed(2)}</span>
                                <button class="btn btn-sm btn-link text-danger" onclick="removeCartItem(${index})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function calculateCartTotal() {
    return appState.cart.reduce((total, item) => total + (item.preco * item.quantidade), 0);
}

function increaseCartItem(index) {
    appState.cart[index].quantidade++;
    updateCartBadge();
    showCart();
}

function decreaseCartItem(index) {
    if (appState.cart[index].quantidade > 1) {
        appState.cart[index].quantidade--;
        updateCartBadge();
        showCart();
    } else {
        removeCartItem(index);
    }
}

function removeCartItem(index) {
    showConfirmModal(
        'Remover Item',
        'Deseja remover este item do carrinho?',
        'Remover',
        'Cancelar'
    ).then(confirmed => {
        if (confirmed) {
            appState.cart.splice(index, 1);
            updateCartBadge();
            showCart();
            showAlert('Item removido do carrinho', 'info');
        }
    });
}

async function clearCart() {
    const confirmed = await showConfirmModal(
        'Limpar Carrinho',
        'Deseja limpar todo o carrinho?',
        'Sim, limpar',
        'Cancelar'
    );
    
    if (confirmed) {
        appState.cart = [];
        updateCartBadge();
        showCart();
        showAlert('Carrinho limpo', 'info');
    }
}

async function proceedToCheckout() {
    if (!appState.user) {
        showAlert('Faça login para finalizar o pedido', 'warning');
        showLogin();
        return;
    }
    
    if (appState.cart.length === 0) {
        showAlert('Carrinho vazio', 'warning');
        return;
    }
    
    // Verificar pedido mínimo
    const firstItem = appState.cart[0];
    const tenantCode = firstItem?.tenant_code;
    const total = calculateCartTotal();
    
    try {
        const response = await fetch(`${API_BASE_URL}/app/restaurantes`);
        const data = await response.json();
        const restaurant = data.restaurantes?.find(r => r.tenant_code === tenantCode);
        
        if (restaurant) {
            const pedidoMinimo = parseFloat(restaurant.pedido_minimo || 0);
            if (pedidoMinimo > 0 && total < pedidoMinimo) {
                showAlert(`Valor mínimo do pedido é R$ ${pedidoMinimo.toFixed(2)}. Faltam R$ ${(pedidoMinimo - total).toFixed(2)}.`, 'warning');
                return;
            }
        }
    } catch (error) {
        console.error('Erro ao verificar pedido mínimo:', error);
    }
    
    showCheckout();
}

async function loadCheckoutSummary() {
    // FUNÇÃO DESABILITADA - Retorna null para usar valores fixos
    return null;
}

async function showCheckout() {
    console.log('💳 [CHECKOUT] Iniciando checkout');
    
    // Verificar se está logado
    if (!appState.user || !appState.token) {
        showAlert('Faça login para finalizar o pedido', 'warning');
        showLogin();
        return;
    }
    
    setActivePage('checkout');
    
    const user = appState.user;
    const total = calculateCartTotal();

    // Buscar dados do restaurante da API
    const firstItem = appState.cart[0];
    const tenantCode = firstItem?.tenant_code;
    
    let restaurantName = 'Restaurante';
    let tempoEntrega = 45;
    let taxaEntrega = 0;
    let tipoRecebimento = 'manual'; // Padrão
    
    try {
        const response = await fetch(`${API_BASE_URL}/app/restaurantes`);
        const data = await response.json();
        const restaurant = data.restaurantes?.find(r => r.tenant_code === tenantCode);
        
        if (restaurant) {
            restaurantName = restaurant.nome_fantasia;
            tempoEntrega = restaurant.tempo_entrega_minutos || 45;
            taxaEntrega = parseFloat(restaurant.taxa_entrega_padrao || 0);
            tipoRecebimento = restaurant.tipo_recebimento_pagamento || 'manual';
        }
        
        // Armazenar no appState para usar depois
        appState.currentRestaurant = {
            tenantCode,
            tipoRecebimento,
            restaurantName,
            taxaEntrega
        };
        
    } catch (error) {
        console.error('Erro ao buscar restaurante:', error);
    }
    
    const taxaServico = 1.29;
    
    // Pre-preencher com endereço salvo se existir
    const savedAddress = {
        rua: user.endereco_rua || '',
        numero: user.endereco_numero || '',
        bairro: user.endereco_bairro || '',
        cidade: user.endereco_cidade || 'São Luís',
        cep: user.endereco_cep || ''
    };
    
    const content = `
        <div class="fade-in">
            <div class="d-flex align-items-center mb-3">
                <button class="btn btn-link text-dark p-0 me-3" onclick="showCart()">
                    <i class="fas fa-arrow-left fa-lg"></i>
                </button>
                <h5 class="mb-0 fw-bold">Finalizar Pedido</h5>
            </div>
            
            <!-- Info do Restaurante -->
            <div class="alert alert-info mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-store me-2"></i>
                        <strong>${restaurantName}</strong>
                    </div>
                    <div class="text-end">
                        <small class="d-block"><i class="fas fa-clock me-1"></i>${tempoEntrega} min</small>
                    </div>
                </div>
            </div>
            
            <!-- Dados do Cliente -->
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-user me-2"></i>Seus Dados
                    </h6>
                    <p class="mb-1"><strong>${user.nome}</strong></p>
                    <p class="mb-0 text-muted">${user.telefone}</p>
                </div>
            </div>
            
            <!-- Endereço de Entrega -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">
                            <i class="fas fa-map-marker-alt me-2"></i>Endereço de Entrega
                        </h6>
                    </div>
                    <form id="addressForm">
                        <div class="mb-2">
                            <input type="text" class="form-control" id="rua" placeholder="Rua / Avenida" 
                                   value="${savedAddress.rua}" required>
                            <small class="text-muted">Mínimo 3 caracteres</small>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4">
                                <input type="text" class="form-control" id="numero" placeholder="Nº" 
                                       value="${savedAddress.numero}" required>
                            </div>
                            <div class="col-8">
                                <input type="text" class="form-control" id="complemento" placeholder="Complemento">
                            </div>
                        </div>
                        <div class="mb-2">
                            <input type="text" class="form-control" id="bairro" placeholder="Bairro" 
                                   value="${savedAddress.bairro}" required>
                        </div>
                        <div class="row mb-2">
                            <div class="col-8">
                                <input type="text" class="form-control" id="cidade" placeholder="Cidade" 
                                       value="${savedAddress.cidade}" required>
                            </div>
                            <div class="col-4">
                                <input type="text" class="form-control" id="cep" placeholder="CEP" 
                                       value="${savedAddress.cep}">
                            </div>
                        </div>
                        <div class="mb-0">
                            <textarea class="form-control" id="referencia" rows="2" 
                                      placeholder="Ponto de referência (opcional)"></textarea>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Forma de Pagamento -->
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-credit-card me-2"></i>Forma de Pagamento
                    </h6>
                    ${tipoRecebimento === 'automatico' ? `
                        <!-- PAGAMENTO AUTOMÁTICO VIA EATSFOOD -->
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Este restaurante aceita <strong>pagamento online via EATSFOOD</strong>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="paymentMethod" id="payment_mp_pix" value="mercado_pago_pix" checked>
                            <label class="form-check-label" for="payment_mp_pix">
                                <i class="fas fa-qrcode me-2 text-success"></i>
                                <strong>PIX via EATSFOOD</strong>
                                <br><small class="text-muted">Pagamento instantâneo via QR Code</small>
                            </label>
                        </div>
                        <div class="alert alert-success mt-2">
                            <i class="fas fa-lock me-2"></i>
                            <small>Pagamento seguro processado pela EATSFOOD</small>
                        </div>
                    ` : `
                        <!-- PAGAMENTO TRADICIONAL (MANUAL) -->
                        <div class="alert alert-warning mb-3">
                            <i class="fas fa-hand-holding-usd me-2"></i>
                            Pagamento na entrega ou no local
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="paymentMethod" id="payment_dinheiro" value="dinheiro" checked>
                            <label class="form-check-label" for="payment_dinheiro">
                                <i class="fas fa-money-bill-wave me-2 text-success"></i>
                                <strong>Dinheiro</strong>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="paymentMethod" id="payment_pix" value="pix">
                            <label class="form-check-label" for="payment_pix">
                                <i class="fas fa-qrcode me-2 text-primary"></i>
                                <strong>PIX</strong>
                                <br><small class="text-muted">Chave PIX fornecida pelo restaurante</small>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="paymentMethod" id="payment_debito" value="cartao_debito">
                            <label class="form-check-label" for="payment_debito">
                                <i class="fas fa-credit-card me-2 text-info"></i>
                                <strong>Cartão de Débito</strong>
                                <br><small class="text-muted">Maquininha na entrega</small>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="paymentMethod" id="payment_credito" value="cartao_credito">
                            <label class="form-check-label" for="payment_credito">
                                <i class="fas fa-credit-card me-2 text-warning"></i>
                                <strong>Cartão de Crédito</strong>
                                <br><small class="text-muted">Maquininha na entrega</small>
                            </label>
                        </div>
                        <!-- Campo de troco para dinheiro -->
                        <div id="trocoField" style="display: none;" class="mt-3">
                            <label class="form-label">Troco para quanto?</label>
                            <input type="number" class="form-control" id="trocoValue" placeholder="Ex: 50.00" step="0.01">
                        </div>
                    `}
                </div>
            </div>
            
            <!-- Observações do Pedido -->
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-2">
                        <i class="fas fa-comment me-2"></i>Observações do Pedido
                    </h6>
                    <textarea class="form-control" id="orderNotes" rows="3" 
                              placeholder="Alguma observação especial?"></textarea>
                </div>
            </div>
            
            <!-- Resumo do Pedido -->
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Resumo do Pedido</h6>
                    
                    <!-- Produtos -->
                    ${appState.cart.map(item => `
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">${item.quantidade}x ${item.nome}</span>
                            <span>R$ ${(item.preco * item.quantidade).toFixed(2)}</span>
                        </div>
                    `).join('')}
                    
                    <hr>
                    
                    <!-- Subtotal -->
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal (${appState.cart.length} ${appState.cart.length > 1 ? 'itens' : 'item'})</span>
                        <span class="fw-bold">R$ ${total.toFixed(2)}</span>
                    </div>
                    
                    <!-- Taxa de Entrega -->
                    <div class="d-flex justify-content-between mb-2">
                        <span>Taxa de entrega</span>
                        <span class="text-success fw-bold">R$ ${taxaEntrega.toFixed(2)}</span>
                    </div>
                    
                    <!-- Taxa de Serviço da Plataforma -->
                    <div class="d-flex justify-content-between mb-2">
                        <span>Taxa de serviço</span>
                        <span>R$ 1,29</span>
                    </div>
                    
                    <hr>
                    
                    <!-- Total -->
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold fs-5">Total</span>
                        <span class="fw-bold text-primary fs-4">R$ ${(total + taxaEntrega + 1.29).toFixed(2)}</span>
                    </div>
                </div>
            </div>
            
            <!-- Botão Confirmar -->
            <div class="d-grid">
                <button class="btn btn-success btn-lg" onclick="confirmOrder()">
                    <i class="fas fa-check me-2"></i>Confirmar Pedido
                </button>
            </div>
        </div>
    `;
    
    document.getElementById('mainContent').innerHTML = content;
    
    // Event listener para campo de troco (apenas se manual)
    if (tipoRecebimento === 'manual') {
        document.querySelectorAll('input[name="paymentMethod"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const trocoField = document.getElementById('trocoField');
                if (this.value === 'dinheiro') {
                    trocoField.style.display = 'block';
                } else {
                    trocoField.style.display = 'none';
                }
            });
        });
    }
}

async function confirmOrder() {
    // Validar endereço
    const rua = document.getElementById('rua').value;
    const numero = document.getElementById('numero').value;
    const bairro = document.getElementById('bairro').value;
    const cidade = document.getElementById('cidade').value;
    const cep = document.getElementById('cep').value;
    
    if (!rua || !numero || !bairro || !cidade || !cep) {
        showAlert('Por favor, preencha todos os campos obrigatórios do endereço', 'warning');
        return;
    }
    
    // Validar tamanho mínimo da rua
    if (rua.length < 3) {
        showAlert('Nome da rua deve ter no mínimo 3 caracteres', 'warning');
        return;
    }
    
    // Pegar forma de pagamento
    const paymentMethodInput = document.querySelector('input[name="paymentMethod"]:checked');
    const paymentMethod = paymentMethodInput ? paymentMethodInput.value : null;
    
    if (!paymentMethod) {
        showAlert('Selecione uma forma de pagamento', 'warning');
        return;
    }
    
    // Verificar se é pagamento automático via MP
    if (appState.currentRestaurant && appState.currentRestaurant.tipoRecebimento === 'automatico') {
        // PAGAMENTO AUTOMÁTICO: Precisa do CPF para gerar PIX
        await solicitarCPFePagarPIX();
        return;
    }
    
    // PAGAMENTO MANUAL: Cria pedido normalmente (cliente paga na entrega)
    await criarPedidoManual(paymentMethod);
}

async function solicitarCPFePagarPIX() {
    // Verificar se já tem CPF salvo
    if (appState.user.cpf && appState.user.cpf !== '00000000000') {
        await processarPagamentoMercadoPago(appState.user.cpf);
        return;
    }
    
    // Mostrar modal para solicitar CPF
    const modalHTML = `
        <div class="modal fade" id="cpfModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">
                            <i class="fas fa-id-card me-2"></i>CPF necessário
                        </h5>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">Para gerar o QR Code PIX, precisamos do seu CPF conforme exigido pela EATSFOOD.</p>
                        <div class="form-group">
                            <label for="cpfInput" class="form-label">CPF:</label>
                            <input type="text" 
                                   class="form-control form-control-lg" 
                                   id="cpfInput" 
                                   placeholder="000.000.000-00"
                                   maxlength="14"
                                   required>
                            <small class="form-text text-muted">Digite apenas números</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="cancelarPagamentoPIX()">
                            Cancelar
                        </button>
                        <button type="button" class="btn btn-primary" onclick="confirmarCPFePagar()">
                            <i class="fas fa-check me-2"></i>Confirmar e Gerar PIX
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Aplicar máscara de CPF
    const cpfInput = document.getElementById('cpfInput');
    cpfInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length <= 11) {
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            e.target.value = value;
        }
    });
    
    const modal = new bootstrap.Modal(document.getElementById('cpfModal'));
    modal.show();
}

window.cancelarPagamentoPIX = function() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('cpfModal'));
    modal.hide();
    setTimeout(() => {
        document.getElementById('cpfModal').remove();
    }, 300);
}

window.confirmarCPFePagar = async function() {
    const cpfInput = document.getElementById('cpfInput');
    const cpf = cpfInput.value.replace(/\D/g, '');
    
    if (cpf.length !== 11) {
        showAlert('CPF inválido. Digite os 11 dígitos', 'warning');
        cpfInput.focus();
        return;
    }
    
    // Salvar CPF no perfil do usuário
    appState.user.cpf = cpf;
    
    // Fechar modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('cpfModal'));
    modal.hide();
    setTimeout(() => {
        document.getElementById('cpfModal').remove();
    }, 300);
    
    // Processar pagamento
    await processarPagamentoMercadoPago(cpf);
}

async function processarPagamentoMercadoPago(cpf) {
    // ========================================
    // PAGAMENTO AUTOMÁTICO (EATSFOOD)
    // Gera PIX primeiro → Aguarda pagamento → Cria pedido DEPOIS
    // ========================================
    
    try {
        showAlert('Gerando QR Code PIX...', 'info');
        
        // Calcular total do carrinho (IGUAL ao resumo)
        const subtotal = appState.cart.reduce((sum, item) => sum + (item.preco * item.quantidade), 0);
        const taxaEntrega = 15.00;
        const taxaServico = 1.29; // Taxa fixa de serviço da plataforma
        const total = subtotal + taxaEntrega + taxaServico;
        
        console.log('💵 Cálculo do total:', { subtotal, taxaEntrega, taxaServico, total });
        
        // Preparar dados do pedido (para criar DEPOIS da aprovação do pagamento)
        const dadosPedido = {
            tipo_pedido: 'delivery',
            cliente_id: appState.user.id,
            cliente_nome: appState.user.nome,
            cliente_telefone: appState.user.telefone,
            cliente_endereco: `${document.getElementById('rua').value}, ${document.getElementById('numero').value}`,
            cliente_bairro: document.getElementById('bairro').value,
            endereco_numero: document.getElementById('numero').value,
            observacoes: document.getElementById('orderNotes').value || null,
            tenant_code: appState.cart[0]?.tenant_code,
            itens: appState.cart.map(item => ({
                produto_id: item.produto_id,
                nome: item.nome,
                quantidade: item.quantidade,
                preco_unitario: item.preco
            }))
        };
        
        console.log('💳 Gerando pagamento PIX (pedido será criado após aprovação)...');
        
        // Gerar pagamento PIX via EATSFOOD
        // IMPORTANTE: NÃO envia pedido_id porque ainda não existe!
        const mpResponse = await fetch(`${API_BASE_URL}/mercadopago/pix`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                amount: total,
                email: appState.user.email || appState.user.telefone + '@cliente.eatsfood.com',
                cpf: cpf, // CPF informado pelo usuário
                description: `Pedido - ${appState.cart.length} itens`,
                // Enviar dados do pedido como metadata (para criar depois da aprovação)
                pedido_data: JSON.stringify(dadosPedido)
            })
        });
        
        const mpData = await mpResponse.json();
        console.log('💰 Resposta EATSFOOD:', mpData);
        
        if (!mpResponse.ok || !mpData.success) {
            console.error('❌ Erro na resposta:', mpData);
            throw new Error(mpData.message || 'Erro ao gerar QR Code PIX');
        }
        
        console.log('🎯 Chamando mostrarTelaPagamentoPIX...');
        console.log('📦 Dados do payment:', mpData.payment);
        
        // Verificar se a função existe
        if (typeof mostrarTelaPagamentoPIX !== 'function') {
            console.error('❌ Função mostrarTelaPagamentoPIX não encontrada!');
            throw new Error('Erro ao carregar módulo de pagamento');
        }
        
        // Mostrar tela de pagamento PIX
        // pedidoId = null porque o pedido só será criado após aprovação
        mostrarTelaPagamentoPIX(mpData.payment, null);
        
    } catch (error) {
        console.error('Erro ao processar pagamento MP:', error);
        showAlert('Erro ao gerar pagamento: ' + error.message, 'danger');
    }
}

async function criarPedidoManual(paymentMethod) {
    // ========================================
    // PAGAMENTO MANUAL (TRADICIONAL)
    // Pedido é criado AGORA e cliente paga na entrega
    // ========================================
    
    const orderData = {
        tipo_pedido: 'delivery',
        cliente_id: appState.user.id,
        cliente_nome: appState.user.nome,
        cliente_telefone: appState.user.telefone,
        cliente_endereco: `${document.getElementById('rua').value}, ${document.getElementById('numero').value}`,
        cliente_bairro: document.getElementById('bairro').value,
        endereco_numero: document.getElementById('numero').value,
        observacoes: document.getElementById('orderNotes').value || null,
        forma_pagamento: paymentMethod,
        troco_para: paymentMethod === 'dinheiro' ? (document.getElementById('trocoValue')?.value || null) : null,
        itens: appState.cart.map(item => ({
            produto_id: item.produto_id,
            quantidade: item.quantidade,
            preco_unitario: item.preco,
            observacoes: item.observacoes
        }))
    };
    
    try {
        showAlert('Processando pedido...', 'info');
        
        const response = await fetch(`${API_BASE_URL}/pedidos-public`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(orderData)
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            // Limpar carrinho
            appState.cart = [];
            updateCartBadge();
            
            showAlert('Pedido realizado com sucesso!', 'success');
            
            // Mostrar página de sucesso
            showOrderSuccess(data.pedido);
        } else {
            showAlert(data.message || 'Erro ao criar pedido', 'danger');
        }
    } catch (error) {
        console.error('Erro ao confirmar pedido:', error);
        if (error.message !== 'Unauthorized' && error.message !== 'No token') {
            showAlert('Erro ao criar pedido. Tente novamente.', 'danger');
        }
    }
}

function showOrderSuccess(pedido) {
    const content = `
        <div class="fade-in text-center py-5">
            <div class="mb-4">
                <i class="fas fa-check-circle fa-5x text-success"></i>
            </div>
            <h4 class="fw-bold mb-2">Pedido Confirmado!</h4>
            <p class="text-muted mb-4">Seu pedido #${pedido.id} foi recebido<br>e está sendo preparado</p>
            
            <div class="card mb-4 text-start">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Detalhes do Pedido</h6>
                    <p class="mb-2">
                        <strong>Número:</strong> #${pedido.id}
                    </p>
                    <p class="mb-2">
                        <strong>Total:</strong> R$ ${parseFloat(pedido.total).toFixed(2)}
                    </p>
                    <p class="mb-0">
                        <strong>Status:</strong> 
                        <span class="badge bg-warning">Aguardando confirmação</span>
                    </p>
                </div>
            </div>
            
            <div class="d-grid gap-2">
                <button class="btn btn-primary-custom" onclick="showOrders()">
                    <i class="fas fa-receipt me-2"></i>Ver Meus Pedidos
                </button>
                <button class="btn btn-outline-primary" onclick="showHome()">
                    <i class="fas fa-home me-2"></i>Voltar ao Início
                </button>
            </div>
        </div>
    `;
    
    document.getElementById('mainContent').innerHTML = content;
}

