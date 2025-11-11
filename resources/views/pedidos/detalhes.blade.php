<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Pedido - Sistema Bar/Restaurante</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            border-radius: 0.5rem;
        }
        .card-header {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
            border-radius: 0.5rem 0.5rem 0 0 !important;
        }
        .btn-primary {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
        }
        .btn-success {
            background: linear-gradient(135deg, #28a745, #1e7e34);
            border: none;
        }
        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
        }
        .item-card {
            transition: transform 0.2s;
        }
        .item-card:hover {
            transform: translateY(-2px);
        }
        .status-badge {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }
        .total-card {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-utensils me-2"></i>
                Sistema Bar/Restaurante
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-home me-1"></i>Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <!-- Cabeçalho do Pedido -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-receipt me-2"></i>
                            Pedido #<span id="pedido-numero">-</span>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Mesa:</strong>
                                <div id="mesa-info" class="text-primary">
                                    <i class="fas fa-table me-1"></i>
                                    Carregando...
                                </div>
                            </div>
                            <div class="col-md-3">
                                <strong>Garçom:</strong>
                                <div id="garcom-info" class="text-info">
                                    <i class="fas fa-user me-1"></i>
                                    Carregando...
                                </div>
                            </div>
                            <div class="col-md-3">
                                <strong>Status:</strong>
                                <div id="status-info">
                                    <span class="badge status-badge">Carregando...</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <strong>Data/Hora:</strong>
                                <div id="data-info" class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    Carregando...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Itens do Pedido -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Itens do Pedido
                        </h5>
                        <button class="btn btn-success btn-sm" onclick="adicionarItem()">
                            <i class="fas fa-plus me-1"></i>
                            Adicionar Item
                        </button>
                    </div>
                    <div class="card-body" id="itens-container">
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-spinner fa-spin fa-2x mb-3"></i>
                            <p>Carregando itens...</p>
                        </div>
                    </div>
                </div>

                <!-- Total do Pedido -->
                <div class="card total-card">
                    <div class="card-body text-center">
                        <h3 class="mb-0">
                            <i class="fas fa-calculator me-2"></i>
                            Total: R$ <span id="total-pedido">0,00</span>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Adicionar Item -->
    <div class="modal fade" id="modalAdicionarItem" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>
                        Adicionar Item ao Pedido
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formAdicionarItem">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="produto_id" class="form-label">Produto</label>
                                    <select class="form-select" id="produto_id" required>
                                        <option value="">Selecione um produto...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="quantidade" class="form-label">Quantidade</label>
                                    <input type="number" class="form-control" id="quantidade" min="1" value="1" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="observacoes" class="form-label">Observações</label>
                            <textarea class="form-control" id="observacoes" rows="3" placeholder="Ex: Sem cebola, bem passado..."></textarea>
                        </div>
                        <div class="mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <strong>Preço Unitário:</strong>
                                            <span id="preco-unitario" class="text-primary">R$ 0,00</span>
                                        </div>
                                        <div class="col">
                                            <strong>Subtotal:</strong>
                                            <span id="subtotal-preview" class="text-success">R$ 0,00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Cancelar
                    </button>                    <button type="button" class="btn btn-success" onclick="salvarItem()">
                        <i class="fas fa-check me-1"></i>
                        Adicionar Item
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Item -->
    <div class="modal fade" id="modalEditarItem" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>
                        Editar Item do Pedido
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarItem">
                        <input type="hidden" id="edit_item_id" name="item_id">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_produto_id" class="form-label">Produto</label>
                                    <select class="form-select" id="edit_produto_id" required>
                                        <option value="">Selecione um produto...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_quantidade" class="form-label">Quantidade</label>
                                    <input type="number" class="form-control" id="edit_quantidade" min="1" value="1" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_observacoes" class="form-label">Observações</label>
                            <textarea class="form-control" id="edit_observacoes" rows="3" placeholder="Ex: Sem cebola, bem passado..."></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <strong>Preço Unitário:</strong>
                                            <span id="edit-preco-unitario" class="text-primary">R$ 0,00</span>
                                        </div>
                                        <div class="col">
                                            <strong>Subtotal:</strong>
                                            <span id="edit-subtotal-preview" class="text-success">R$ 0,00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-warning" onclick="atualizarItem()">
                        <i class="fas fa-save me-1"></i>
                        Atualizar Item
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>    <script>
        let pedidoId = {{ $pedidoId ?? 9 }}; // ID do pedido vindo do backend
        let produtos = [];
        let pedidoAtual = null;// Carregar dados iniciais
        document.addEventListener('DOMContentLoaded', function() {
            carregarPedido();
            carregarProdutos();
            carregarItens();
            
            // Listeners para preview de adição
            document.getElementById('produto_id').addEventListener('change', atualizarPreview);
            document.getElementById('quantidade').addEventListener('input', atualizarPreview);
            
            // Listeners para preview de edição
            document.getElementById('edit_produto_id').addEventListener('change', atualizarPreviewEdicao);
            document.getElementById('edit_quantidade').addEventListener('input', atualizarPreviewEdicao);
        });// Carregar dados do pedido
        async function carregarPedido() {
            try {
                console.log('Carregando pedido:', pedidoId);
                const response = await fetch(`/api/pedidos-public/${pedidoId}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                console.log('Response status pedido:', response.status);
                
                if (response.ok) {
                    const data = await response.json();
                    console.log('Dados do pedido:', data);
                    pedidoAtual = data.pedido;
                    
                    document.getElementById('pedido-numero').textContent = pedidoAtual.id;
                    document.getElementById('mesa-info').innerHTML = `<i class="fas fa-table me-1"></i>Mesa ${pedidoAtual.mesa.identificador}`;
                    document.getElementById('garcom-info').innerHTML = `<i class="fas fa-user me-1"></i>${pedidoAtual.usuario.nome}`;
                    document.getElementById('data-info').innerHTML = `<i class="fas fa-clock me-1"></i>${formatarData(pedidoAtual.created_at)}`;
                    
                    const statusBadge = document.getElementById('status-info');
                    statusBadge.innerHTML = `<span class="badge status-badge bg-${getStatusColor(pedidoAtual.status)}">${pedidoAtual.status}</span>`;
                    
                    document.getElementById('total-pedido').textContent = formatarMoeda(pedidoAtual.total);
                } else {
                    const errorData = await response.text();
                    console.error('Erro ao carregar pedido:', response.status, errorData);
                    alert('Erro ao carregar dados do pedido: ' + response.status);
                }
            } catch (error) {
                console.error('Erro ao carregar pedido:', error);
                alert('Erro ao carregar dados do pedido: ' + error.message);
            }
        }// Carregar lista de produtos
        async function carregarProdutos() {
            try {
                const response = await fetch('/api/produtos-public', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    produtos = data.produtos;
                    
                    const select = document.getElementById('produto_id');
                    select.innerHTML = '<option value="">Selecione um produto...</option>';
                    
                    produtos.forEach(produto => {
                        const option = document.createElement('option');
                        option.value = produto.id;
                        option.textContent = `${produto.nome} - R$ ${formatarMoeda(produto.preco)}`;
                        option.dataset.preco = produto.preco;
                        select.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Erro ao carregar produtos:', error);
            }
        }        // Carregar itens do pedido
        async function carregarItens() {
            try {
                console.log('Carregando itens do pedido:', pedidoId);
                const response = await fetch(`/api/test-itens/${pedidoId}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                console.log('Response status:', response.status);
                
                if (response.ok) {
                    const data = await response.json();
                    console.log('Dados recebidos:', data);
                    exibirItens(data.itens || []);
                } else {
                    const errorData = await response.text();
                    console.error('Erro na resposta:', response.status, errorData);
                    document.getElementById('itens-container').innerHTML = `
                        <div class="text-center text-warning py-4">
                            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                            <p>Erro ${response.status} ao carregar itens</p>
                            <small class="text-muted">Verifique o console para mais detalhes</small>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Erro ao carregar itens:', error);
                document.getElementById('itens-container').innerHTML = `
                    <div class="text-center text-danger py-4">
                        <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                        <p>Erro de conexão ao carregar itens</p>
                        <small class="text-muted">${error.message}</small>
                    </div>
                `;
            }
        }

        // Exibir itens na interface
        function exibirItens(itens) {
            const container = document.getElementById('itens-container');
            
            if (itens.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-shopping-cart fa-2x mb-3"></i>
                        <p>Nenhum item adicionado ao pedido</p>
                        <button class="btn btn-success" onclick="adicionarItem()">
                            <i class="fas fa-plus me-1"></i>
                            Adicionar Primeiro Item
                        </button>
                    </div>
                `;
                return;
            }

            let html = '<div class="row">';
            
            itens.forEach(item => {
                html += `
                    <div class="col-md-6 mb-3">
                        <div class="card item-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-1">
                                        <i class="fas fa-utensils me-1"></i>
                                        ${item.produto.nome}
                                    </h6>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-warning" onclick="editarItem(${item.id})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" onclick="removerItem(${item.id})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="row text-sm">
                                    <div class="col-6">
                                        <strong>Quantidade:</strong> ${item.quantidade}
                                    </div>
                                    <div class="col-6">
                                        <strong>Preço Unit.:</strong> R$ ${formatarMoeda(item.preco_unitario)}
                                    </div>
                                    <div class="col-12 mt-1">
                                        <strong>Subtotal:</strong> 
                                        <span class="text-success">R$ ${formatarMoeda(item.subtotal)}</span>
                                    </div>
                                    ${item.observacoes ? `
                                    <div class="col-12 mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-sticky-note me-1"></i>
                                            ${item.observacoes}
                                        </small>
                                    </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            container.innerHTML = html;
        }

        // Adicionar novo item
        function adicionarItem() {
            document.getElementById('formAdicionarItem').reset();
            document.getElementById('preco-unitario').textContent = 'R$ 0,00';
            document.getElementById('subtotal-preview').textContent = 'R$ 0,00';
            
            const modal = new bootstrap.Modal(document.getElementById('modalAdicionarItem'));
            modal.show();
        }        // Atualizar preview do preço
        function atualizarPreview() {
            const select = document.getElementById('produto_id');
            const quantidade = parseInt(document.getElementById('quantidade').value) || 0;
            const preco = parseFloat(select.options[select.selectedIndex]?.dataset.preco || 0);
            
            document.getElementById('preco-unitario').textContent = `R$ ${formatarMoeda(preco)}`;
            document.getElementById('subtotal-preview').textContent = `R$ ${formatarMoeda(preco * quantidade)}`;
        }

        // Salvar novo item
        async function salvarItem() {
            const formData = {
                pedido_id: pedidoId,
                produto_id: document.getElementById('produto_id').value,
                quantidade: document.getElementById('quantidade').value,
                observacoes: document.getElementById('observacoes').value
            };

            if (!formData.produto_id || !formData.quantidade) {
                alert('Por favor, preencha todos os campos obrigatórios');
                return;
            }

            try {
                const response = await fetch('/api/item-pedidos-public', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();
                
                if (response.ok) {
                    alert('Item adicionado com sucesso!');
                    bootstrap.Modal.getInstance(document.getElementById('modalAdicionarItem')).hide();
                    carregarItens();
                    carregarPedido(); // Atualizar total
                } else {
                    alert('Erro ao adicionar item: ' + (data.message || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro ao salvar item:', error);
                alert('Erro ao salvar item');
            }
        }        // Remover item
        async function removerItem(itemId) {
            if (!confirm('Tem certeza que deseja remover este item?')) {
                return;
            }

            try {
                const response = await fetch(`/api/item-pedidos-public/${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    alert('Item removido com sucesso!');
                    carregarItens();
                    carregarPedido(); // Atualizar total
                } else {
                    const data = await response.json();
                    alert('Erro ao remover item: ' + (data.message || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro ao remover item:', error);
                alert('Erro ao remover item');
            }
        }

        // Editar item
        async function editarItem(itemId) {
            try {
                // Buscar dados do item
                const response = await fetch(`/api/item-pedidos-public/${itemId}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    const item = data.item;

                    // Preencher o formulário de edição
                    document.getElementById('edit_item_id').value = item.id;
                    document.getElementById('edit_produto_id').value = item.produto_id;
                    document.getElementById('edit_quantidade').value = item.quantidade;
                    document.getElementById('edit_observacoes').value = item.observacoes || '';

                    // Carregar produtos no select de edição se ainda não foi carregado
                    await carregarProdutosEdicao();

                    // Atualizar preview de preços
                    atualizarPreviewEdicao();

                    // Mostrar o modal
                    const modal = new bootstrap.Modal(document.getElementById('modalEditarItem'));
                    modal.show();
                } else {
                    const data = await response.json();
                    alert('Erro ao carregar item: ' + (data.message || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro ao carregar item para edição:', error);
                alert('Erro ao carregar item para edição');
            }
        }

        // Carregar produtos para o select de edição
        async function carregarProdutosEdicao() {
            try {
                const response = await fetch('/api/produtos-public', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    const produtos = data.produtos;
                    
                    const select = document.getElementById('edit_produto_id');
                    // Manter o valor atual se já existe
                    const currentValue = select.value;
                    
                    select.innerHTML = '<option value="">Selecione um produto...</option>';
                    
                    produtos.forEach(produto => {
                        const option = document.createElement('option');
                        option.value = produto.id;
                        option.textContent = `${produto.nome} - R$ ${formatarMoeda(produto.preco)}`;
                        option.dataset.preco = produto.preco;
                        if (produto.id == currentValue) {
                            option.selected = true;
                        }
                        select.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Erro ao carregar produtos para edição:', error);
            }
        }        // Adicionar listeners para o formulário de edição
        function atualizarPreviewEdicao() {
            const select = document.getElementById('edit_produto_id');
            const quantidade = parseInt(document.getElementById('edit_quantidade').value) || 0;
            const preco = parseFloat(select.options[select.selectedIndex]?.dataset.preco || 0);
            
            document.getElementById('edit-preco-unitario').textContent = `R$ ${formatarMoeda(preco)}`;
            document.getElementById('edit-subtotal-preview').textContent = `R$ ${formatarMoeda(preco * quantidade)}`;
        }

        // Atualizar item
        async function atualizarItem() {
            const formData = {
                produto_id: document.getElementById('edit_produto_id').value,
                quantidade: document.getElementById('edit_quantidade').value,
                observacoes: document.getElementById('edit_observacoes').value
            };

            const itemId = document.getElementById('edit_item_id').value;

            if (!formData.produto_id || !formData.quantidade) {
                alert('Por favor, preencha todos os campos obrigatórios');
                return;
            }

            try {
                const response = await fetch(`/api/item-pedidos-public/${itemId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();
                
                if (response.ok) {
                    alert('Item atualizado com sucesso!');
                    bootstrap.Modal.getInstance(document.getElementById('modalEditarItem')).hide();
                    carregarItens();
                    carregarPedido(); // Atualizar total
                } else {
                    alert('Erro ao atualizar item: ' + (data.message || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro ao atualizar item:', error);
                alert('Erro ao atualizar item');
            }
        }

        // Funções utilitárias
        function formatarMoeda(valor) {
            return parseFloat(valor || 0).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function formatarData(dataString) {
            const data = new Date(dataString);
            return data.toLocaleString('pt-BR');
        }

        function getStatusColor(status) {
            const cores = {
                'pendente': 'warning',
                'preparando': 'info',
                'pronto': 'primary',
                'entregue': 'success',
                'cancelado': 'danger'
            };
            return cores[status] || 'secondary';
        }

        // Simular token para testes (em produção viria do login)
        if (!localStorage.getItem('token')) {
            localStorage.setItem('token', 'fake-token-for-testing');
        }
    </script>
</body>
</html>
