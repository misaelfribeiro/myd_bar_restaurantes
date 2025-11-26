// ===== FUNÇÕES DE PAGAMENTO EATSFOOD PIX =====

function mostrarTelaPagamentoPIX(payment, pedidoId) {
    // Calcular tempo de expiração
    const expiresAt = new Date(payment.expires_at);
    const now = new Date();
    const minutosRestantes = Math.floor((expiresAt - now) / 1000 / 60);
    
    const content = `
        <div class="fade-in">
            <div class="text-center mb-4">
                <h5 class="fw-bold mb-2">Pague com PIX</h5>
                <p class="text-muted">Escaneie o QR Code ou copie o código</p>
            </div>
            
            <!-- QR Code -->
            <div class="card mb-3">
                <div class="card-body text-center">
                    <div class="mb-3">
                        ${payment.pix.qr_code ? 
                            `<img src="data:image/png;base64,${payment.pix.qr_code}" alt="QR Code PIX" style="max-width: 280px; width: 100%;">` :
                            payment.pix.qr_code_url ? 
                            `<img src="${payment.pix.qr_code_url}" alt="QR Code PIX" style="max-width: 280px; width: 100%;">` :
                            `<p style="color: #666;">QR Code não disponível</p>`
                        }
                    </div>
                    
                    <!-- Valor -->
                    <h3 class="text-primary fw-bold mb-2">R$ ${parseFloat(payment.amount).toFixed(2)}</h3>
                    
                    <!-- Tempo restante -->
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-clock me-2"></i>
                        <strong>Válido até:</strong> ${expiresAt.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'})}
                        <br><small id="countdown">(${minutosRestantes} minutos restantes)</small>
                    </div>
                    
                    <!-- Botão copiar código PIX -->
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="copiarCodigoPix('${payment.pix.copy_paste}')">
                        <i class="fas fa-copy me-2"></i>Copiar Código PIX
                    </button>
                    
                    <!-- Status do pagamento -->
                    <div id="paymentStatus" class="mt-3">
                        <div class="spinner-border spinner-border-sm me-2" role="status">
                            <span class="visually-hidden">Aguardando...</span>
                        </div>
                        <span class="text-muted">Aguardando pagamento...</span>
                    </div>
                </div>
            </div>
            
            <!-- Instruções -->
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Como pagar:</h6>
                    <ol class="mb-0">
                        <li class="mb-2">Abra o app do seu banco</li>
                        <li class="mb-2">Escolha pagar com PIX</li>
                        <li class="mb-2">Escaneie o QR Code ou cole o código</li>
                        <li class="mb-0">Confirme o pagamento</li>
                    </ol>
                </div>
            </div>
            
            <!-- Botão cancelar -->
            <button class="btn btn-outline-danger w-100" onclick="cancelarPagamento(${pedidoId})">
                <i class="fas fa-times me-2"></i>Cancelar
            </button>
        </div>
    `;
    
    document.getElementById('mainContent').innerHTML = content;
    
    // Iniciar polling de status
    iniciarPollingPagamento(payment.id, pedidoId);
    
    // Iniciar contador regressivo
    iniciarContadorRegressivo(expiresAt);
}

function copiarCodigoPix(codigoPix) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(codigoPix).then(() => {
            showAlert('Código PIX copiado!', 'success');
        }).catch(() => {
            // Fallback
            fallbackCopiarTexto(codigoPix);
        });
    } else {
        fallbackCopiarTexto(codigoPix);
    }
}

function fallbackCopiarTexto(texto) {
    const textarea = document.createElement('textarea');
    textarea.value = texto;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    try {
        document.execCommand('copy');
        showAlert('Código PIX copiado!', 'success');
    } catch (err) {
        showAlert('Não foi possível copiar. Copie manualmente.', 'warning');
    }
    document.body.removeChild(textarea);
}

let pollingInterval = null;

function iniciarPollingPagamento(paymentId, pedidoId) {
    // Limpar intervalo anterior se existir
    if (pollingInterval) {
        clearInterval(pollingInterval);
    }
    
    let tentativas = 0;
    const maxTentativas = 120; // 10 minutos (5 segundos * 120)
    
    pollingInterval = setInterval(async () => {
        tentativas++;
        
        if (tentativas >= maxTentativas) {
            clearInterval(pollingInterval);
            mostrarPagamentoExpirado();
            return;
        }
        
        try {
            const response = await fetch(`${API_BASE_URL}/mercadopago/payment/${paymentId}/status`);
            const data = await response.json();
            
            if (data.success && data.payment.is_approved) {
                clearInterval(pollingInterval);
                // Limpar carrinho
                appState.cart = [];
                updateCartBadge();
                // Mostrar sucesso
                mostrarPagamentoAprovado(pedidoId);
            }
        } catch (error) {
            console.error('Erro ao verificar status:', error);
        }
    }, 5000); // Verificar a cada 5 segundos
}

function iniciarContadorRegressivo(expiresAt) {
    const countdownElement = document.getElementById('countdown');
    if (!countdownElement) return;
    
    const interval = setInterval(() => {
        const now = new Date();
        const diff = expiresAt - now;
        
        if (diff <= 0) {
            clearInterval(interval);
            countdownElement.textContent = '(expirado)';
            countdownElement.parentElement.classList.remove('alert-warning');
            countdownElement.parentElement.classList.add('alert-danger');
            return;
        }
        
        const minutos = Math.floor(diff / 1000 / 60);
        const segundos = Math.floor((diff / 1000) % 60);
        countdownElement.textContent = `(${minutos}:${segundos.toString().padStart(2, '0')} restantes)`;
    }, 1000);
}

function mostrarPagamentoAprovado(pedidoId) {
    const content = `
        <div class="fade-in text-center py-5">
            <div class="mb-4">
                <i class="fas fa-check-circle fa-5x text-success"></i>
            </div>
            <h4 class="fw-bold mb-2 text-success">Pagamento Confirmado!</h4>
            <p class="text-muted mb-4">Seu pedido #${pedidoId} foi aprovado<br>e está sendo preparado</p>
            
            <div class="alert alert-success mb-4">
                <i class="fas fa-receipt me-2"></i>
                <strong>Pagamento processado com sucesso via EATSFOOD</strong>
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

function mostrarPagamentoExpirado() {
    const content = `
        <div class="fade-in text-center py-5">
            <div class="mb-4">
                <i class="fas fa-exclamation-triangle fa-5x text-warning"></i>
            </div>
            <h4 class="fw-bold mb-2">QR Code Expirado</h4>
            <p class="text-muted mb-4">O tempo para pagamento expirou.<br>Gere um novo código para continuar.</p>
            
            <div class="d-grid gap-2">
                <button class="btn btn-primary-custom" onclick="showCheckout()">
                    <i class="fas fa-redo me-2"></i>Gerar Novo QR Code
                </button>
                <button class="btn btn-outline-secondary" onclick="showCart()">
                    <i class="fas fa-arrow-left me-2"></i>Voltar ao Carrinho
                </button>
            </div>
        </div>
    `;
    
    document.getElementById('mainContent').innerHTML = content;
}

function cancelarPagamento(pedidoId) {
    if (pollingInterval) {
        clearInterval(pollingInterval);
    }
    
    showAlert('Pagamento cancelado', 'info');
    showCart();
}
