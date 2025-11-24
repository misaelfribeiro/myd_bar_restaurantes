// Real-time order status updates usando polling

let orderPollingInterval = null;
let currentOrderId = null;

/**
 * Inicia o polling de status para um pedido específico
 */
function startOrderPolling(orderId) {
    currentOrderId = orderId;
    
    // Limpar polling anterior se existir
    if (orderPollingInterval) {
        clearInterval(orderPollingInterval);
    }
    
    // Atualizar imediatamente
    updateOrderStatus(orderId);
    
    // Configurar polling a cada 10 segundos
    orderPollingInterval = setInterval(() => {
        updateOrderStatus(orderId);
    }, 10000); // 10 segundos
}

/**
 * Para o polling de status
 */
function stopOrderPolling() {
    if (orderPollingInterval) {
        clearInterval(orderPollingInterval);
        orderPollingInterval = null;
    }
    currentOrderId = null;
}

/**
 * Atualiza o status do pedido
 */
async function updateOrderStatus(orderId) {
    try {
        const response = await authFetch(`${API_BASE_URL}/app/pedidos/${orderId}`);
        const data = await response.json();
        
        if (data.success && data.pedido) {
            const pedido = data.pedido;
            
            // Atualizar o badge de status se existir na página
            const statusBadge = document.getElementById('orderStatusBadge');
            if (statusBadge) {
                statusBadge.className = `badge ${getStatusClass(pedido.status)}`;
                statusBadge.textContent = getStatusText(pedido.status);
            }
            
            // Atualizar status de delivery se existir
            if (pedido.delivery) {
                const deliveryBadge = document.getElementById('deliveryStatusBadge');
                if (deliveryBadge) {
                    deliveryBadge.className = `badge ${getDeliveryStatusClass(pedido.delivery.status)}`;
                    deliveryBadge.textContent = getDeliveryStatusText(pedido.delivery.status);
                }
                
                // Atualizar tempo estimado
                const tempoEstimado = document.getElementById('tempoEstimado');
                if (tempoEstimado && pedido.delivery.tempo_estimado) {
                    tempoEstimado.textContent = `${pedido.delivery.tempo_estimado} min`;
                }
            }
            
            // Notificar mudanças de status importantes
            notifyStatusChange(pedido);
            
            // Parar polling se pedido foi finalizado ou cancelado
            if (['finalizado', 'cancelado'].includes(pedido.status)) {
                stopOrderPolling();
            }
        }
    } catch (error) {
        console.error('Erro ao atualizar status do pedido:', error);
        
        // Parar polling em caso de erro repetido
        if (error.message.includes('401') || error.message.includes('404')) {
            stopOrderPolling();
        }
    }
}

/**
 * Notifica o usuário sobre mudanças importantes de status
 */
let lastNotifiedStatus = null;
function notifyStatusChange(pedido) {
    const statusAtual = `${pedido.status}_${pedido.delivery?.status || ''}`;
    
    if (lastNotifiedStatus === statusAtual) {
        return; // Sem mudança
    }
    
    lastNotifiedStatus = statusAtual;
    
    // Mensagens de notificação baseadas no status
    const messages = {
        'aberto_pendente': '📋 Pedido recebido! Aguardando confirmação...',
        'aberto_confirmado': '✅ Pedido confirmado! Em preparação...',
        'em_preparo_confirmado': '👨‍🍳 Seu pedido está sendo preparado!',
        'pronto_confirmado': '🎉 Pedido pronto! Preparando para entrega...',
        'pronto_em_transito': '🚗 Seu pedido saiu para entrega!',
        'entregue_entregue': '✓ Pedido entregue! Bom apetite!',
        'cancelado_cancelado': '❌ Pedido cancelado'
    };
    
    const message = messages[statusAtual];
    if (message) {
        showNotification(message, pedido.status === 'cancelado' ? 'warning' : 'success');
        
        // Vibrar dispositivo se disponível (mobile)
        if ('vibrate' in navigator) {
            navigator.vibrate(200);
        }
        
        // Tocar som de notificação (opcional)
        // playNotificationSound();
    }
}

/**
 * Mostra notificação visual
 */
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'info'} 
                             position-fixed top-0 start-50 translate-middle-x mt-3 shadow-lg`;
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    notification.style.animation = 'slideDown 0.3s ease-out';
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <span class="me-2">${message}</span>
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Remover automaticamente após 5 segundos
    setTimeout(() => {
        notification.style.animation = 'slideUp 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

/**
 * Retorna a classe CSS apropriada para o status
 */
function getStatusClass(status) {
    const classes = {
        'aberto': 'bg-warning text-dark',
        'em_preparo': 'bg-info text-white',
        'pronto': 'bg-success text-white',
        'entregue': 'bg-success text-white',
        'finalizado': 'bg-secondary text-white',
        'cancelado': 'bg-danger text-white'
    };
    return classes[status] || 'bg-secondary';
}

function getStatusText(status) {
    const texts = {
        'aberto': 'Aberto',
        'em_preparo': 'Em Preparo',
        'pronto': 'Pronto',
        'entregue': 'Entregue',
        'finalizado': 'Finalizado',
        'cancelado': 'Cancelado'
    };
    return texts[status] || status;
}

function getDeliveryStatusClass(status) {
    const classes = {
        'pendente': 'bg-warning text-dark',
        'confirmado': 'bg-info text-white',
        'em_transito': 'bg-primary text-white',
        'entregue': 'bg-success text-white',
        'cancelado': 'bg-danger text-white'
    };
    return classes[status] || 'bg-secondary';
}

function getDeliveryStatusText(status) {
    const texts = {
        'pendente': 'Pendente',
        'confirmado': 'Confirmado',
        'em_transito': 'Em Trânsito',
        'entregue': 'Entregue',
        'cancelado': 'Cancelado'
    };
    return texts[status] || status;
}

// Adicionar animações CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes slideDown {
        from {
            transform: translate(-50%, -100%);
            opacity: 0;
        }
        to {
            transform: translate(-50%, 0);
            opacity: 1;
        }
    }
    
    @keyframes slideUp {
        from {
            transform: translate(-50%, 0);
            opacity: 1;
        }
        to {
            transform: translate(-50%, -100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Exportar para uso global
window.startOrderPolling = startOrderPolling;
window.stopOrderPolling = stopOrderPolling;
