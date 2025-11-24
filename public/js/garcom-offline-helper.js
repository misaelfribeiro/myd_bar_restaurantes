<!-- Exemplo de uso do banco de dados local no modo garçom -->

<script>
// Aguardar banco estar pronto
function aguardarBancoLocal() {
    return new Promise((resolve) => {
        const check = setInterval(() => {
            if (window.localDB && window.localDB.isInitialized) {
                clearInterval(check);
                resolve();
            }
        }, 100);
    });
}

// Função para carregar mesas do banco local
async function carregarMesasLocal() {
    await aguardarBancoLocal();
    
    try {
        const mesas = window.localDB.getMesas();
        console.log('📋 Mesas carregadas do banco local:', mesas);
        return mesas;
    } catch (error) {
        console.error('❌ Erro ao carregar mesas:', error);
        return [];
    }
}

// Função para carregar produtos do banco local
async function carregarProdutosLocal(categoriaId = null) {
    await aguardarBancoLocal();
    
    try {
        const produtos = window.localDB.getProdutos(categoriaId);
        console.log('📋 Produtos carregados do banco local:', produtos);
        return produtos;
    } catch (error) {
        console.error('❌ Erro ao carregar produtos:', error);
        return [];
    }
}

// Função para criar pedido no banco local
async function criarPedidoLocal(mesaId, itens, observacoes = null) {
    await aguardarBancoLocal();
    
    try {
        // Criar pedido
        const pedidoId = window.localDB.criarPedido(mesaId, 1, observacoes);
        console.log('✅ Pedido criado localmente:', pedidoId);
        
        // Adicionar itens
        for (const item of itens) {
            window.localDB.adicionarItemPedido(
                pedidoId,
                item.produto_id,
                item.quantidade,
                item.preco_unitario,
                item.observacoes
            );
        }
        
        // Mostrar notificação
        mostrarNotificacao('Pedido criado localmente! Será sincronizado quando houver internet.', 'success');
        
        return pedidoId;
    } catch (error) {
        console.error('❌ Erro ao criar pedido:', error);
        mostrarNotificacao('Erro ao criar pedido: ' + error.message, 'danger');
        return null;
    }
}

// Função para registrar pagamento no banco local
async function registrarPagamentoLocal(pedidoId, formaPagamento, valor, valorRecebido = null, troco = null) {
    await aguardarBancoLocal();
    
    try {
        const pagamentoId = window.localDB.registrarPagamento(
            pedidoId,
            formaPagamento,
            valor,
            valorRecebido,
            troco
        );
        
        console.log('✅ Pagamento registrado localmente:', pagamentoId);
        mostrarNotificacao('Pagamento registrado localmente! Será sincronizado quando houver internet.', 'success');
        
        return pagamentoId;
    } catch (error) {
        console.error('❌ Erro ao registrar pagamento:', error);
        mostrarNotificacao('Erro ao registrar pagamento: ' + error.message, 'danger');
        return null;
    }
}

// Função auxiliar para mostrar notificações
function mostrarNotificacao(mensagem, tipo = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${tipo} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 80px; right: 20px; z-index: 9999; min-width: 350px; max-width: 500px;';
    alertDiv.innerHTML = `
        ${mensagem}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Verificar status da conexão e banco
async function verificarStatus() {
    await aguardarBancoLocal();
    
    const status = {
        online: navigator.onLine,
        bancoLocal: window.localDB?.isInitialized || false,
        stats: window.localDB?.getStats() || null
    };
    
    console.log('📊 Status do Sistema:', status);
    
    return status;
}

// Sincronizar manualmente
async function sincronizarAgora() {
    await aguardarBancoLocal();
    
    if (!navigator.onLine) {
        mostrarNotificacao('Sem conexão com internet. Sincronização não disponível.', 'warning');
        return;
    }
    
    mostrarNotificacao('Iniciando sincronização...', 'info');
    
    const result = await window.localDB.sincronizarComServidor();
    
    if (result.success) {
        mostrarNotificacao(result.message, 'success');
    } else {
        mostrarNotificacao('Erro na sincronização: ' + result.message, 'danger');
    }
}

// Exportar funções globalmente
window.carregarMesasLocal = carregarMesasLocal;
window.carregarProdutosLocal = carregarProdutosLocal;
window.criarPedidoLocal = criarPedidoLocal;
window.registrarPagamentoLocal = registrarPagamentoLocal;
window.verificarStatus = verificarStatus;
window.sincronizarAgora = sincronizarAgora;
</script>
