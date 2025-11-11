<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>🔧 Debug - Modo Garçom</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px;">
    <!-- Debug Panel -->
    <div style="position: fixed; top: 20px; right: 20px; background: rgba(0,0,0,0.8); color: #0f0; padding: 15px; border-radius: 8px; font-family: monospace; font-size: 12px; max-width: 300px; max-height: 200px; overflow-y: auto; z-index: 9999;" id="debug-panel">
        <strong>🐛 DEBUG</strong><br>
        <div id="debug-log">Sistema carregando...</div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>⚡ Modo Garçom - Pedido Rápido (Debug)</h4>
                </div>
                <div class="card-body">
                    <!-- Seleção de Mesa -->
                    <h5>1. Selecionar Mesa</h5>
                    <div class="row mb-4">
                        @forelse($mesas as $mesa)
                            <div class="col-md-2 mb-2">
                                <div class="card mesa-card" style="cursor: pointer; border: 2px solid #ddd;" 
                                     data-mesa-id="{{ $mesa->id }}" 
                                     onclick="selecionarMesa({{ $mesa->id }}, '{{ $mesa->identificador }}')">
                                    <div class="card-body text-center p-2">
                                        <strong>{{ $mesa->identificador }}</strong><br>
                                        <small>{{ $mesa->lugares }} lugares</small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-warning">Nenhuma mesa disponível</div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Produtos -->
                    <h5>2. Adicionar Produtos</h5>
                    @forelse($categorias as $categoria)
                        @if($categoria->produtos->count() > 0)
                            <h6 class="mt-3 mb-2 text-primary">{{ $categoria->nome }}</h6>
                            <div class="row">
                                @foreach($categoria->produtos as $produto)
                                    <div class="col-md-6 mb-3">
                                        <div class="card produto-card">
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <div class="col-md-6">
                                                        <strong>{{ $produto->nome }}</strong><br>
                                                        <span class="text-success">R$ {{ number_format($produto->preco, 2, ',', '.') }}</span>
                                                        <br><small class="text-muted">ID: {{ $produto->id }}</small>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="input-group input-group-sm">
                                                            <button class="btn btn-outline-secondary" type="button" onclick="alterarQuantidade({{ $produto->id }}, -1)">-</button>
                                                            <input type="number" class="form-control text-center" id="qty-{{ $produto->id }}" value="0" min="0" max="10" style="max-width: 60px;">
                                                            <button class="btn btn-outline-secondary" type="button" onclick="alterarQuantidade({{ $produto->id }}, 1)">+</button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <button class="btn btn-primary btn-sm" onclick="adicionarProduto({{ $produto->id }})">
                                                            ➕ Add
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @empty
                        <div class="alert alert-info">Nenhum produto disponível</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Carrinho -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>🛒 Carrinho de Pedidos</h5>
                </div>
                <div class="card-body">
                    <!-- Mesa selecionada -->
                    <div class="mb-3">
                        <strong>Mesa:</strong> <span id="mesa-selecionada" class="text-muted">Nenhuma selecionada</span>
                    </div>

                    <!-- Itens -->
                    <div id="carrinho-itens" class="mb-3">
                        <div class="text-center text-muted">
                            <em>Carrinho vazio</em>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong id="valor-total" class="text-success">R$ 0,00</strong>
                        </div>
                    </div>

                    <!-- Observações -->
                    <div class="mt-3">
                        <textarea class="form-control" id="observacoes" placeholder="Observações..." rows="3"></textarea>
                    </div>

                    <!-- Finalizar -->
                    <button class="btn btn-success w-100 mt-3" id="finalizar-pedido" onclick="finalizarPedido()" disabled>
                        <i class="fas fa-check"></i> Finalizar Pedido
                    </button>
                    
                    <!-- Botões Debug -->
                    <div class="mt-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="testeAutomatico()">🧪 Teste Auto</button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="limparTudo()">🗑️ Limpar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dados dos produtos para JavaScript -->
<script id="dados-produtos" type="application/json">
@php
    $produtosDados = [];
    foreach($categorias as $categoria) {
        foreach($categoria->produtos as $produto) {
            $produtosDados[$produto->id] = [
                'id' => $produto->id,
                'nome' => $produto->nome,
                'preco' => $produto->preco,
                'categoria' => $categoria->nome
            ];
        }
    }
@endphp
{!! json_encode($produtosDados) !!}
</script>

<script>
    // Variáveis globais
    let mesaSelecionada = null;
    let carrinho = [];
    let produtos = {};

    // Função de debug
    function debug(message, data = null) {
        const timestamp = new Date().toLocaleTimeString();
        const logDiv = document.getElementById('debug-log');
        const entry = `[${timestamp}] ${message}${data ? ' | ' + JSON.stringify(data).substring(0, 100) : ''}`;
        logDiv.innerHTML = entry + '<br>' + (logDiv.innerHTML || '').split('<br>').slice(0, 10).join('<br>');
        console.log(entry, data);
    }

    // Inicialização
    document.addEventListener('DOMContentLoaded', function() {
        debug('Sistema carregando...');
        
        try {
            produtos = JSON.parse(document.getElementById('dados-produtos').textContent);
            debug('Produtos carregados', { total: Object.keys(produtos).length });
        } catch (error) {
            debug('ERRO ao carregar produtos', { error: error.message });
            produtos = {};
        }
        
        debug('Sistema pronto', { 
            produtos: Object.keys(produtos).length,
            mesas: document.querySelectorAll('[data-mesa-id]').length,
            inputs: document.querySelectorAll('[id^="qty-"]').length
        });
    });

    function selecionarMesa(id, numero) {
        debug('Selecionando mesa', { id, numero });
        
        // Limpar seleções
        document.querySelectorAll('.mesa-card').forEach(el => {
            el.style.borderColor = '#ddd';
            el.style.backgroundColor = '';
        });
        
        // Selecionar nova
        const mesa = document.querySelector(`[data-mesa-id="${id}"]`);
        if (mesa) {
            mesa.style.borderColor = '#007bff';
            mesa.style.backgroundColor = '#e7f3ff';
        }
        
        mesaSelecionada = { id: parseInt(id), numero };
        document.getElementById('mesa-selecionada').innerHTML = `<strong class="text-primary">${numero}</strong>`;
        
        debug('Mesa selecionada', mesaSelecionada);
        verificarPodeFinalizarPedido();
    }

    function alterarQuantidade(produtoId, delta) {
        debug('Alterando quantidade', { produtoId, delta });
        
        const input = document.getElementById(`qty-${produtoId}`);
        if (!input) {
            debug('ERRO: Input não encontrado', { produtoId });
            return;
        }
        
        let valor = parseInt(input.value || 0) + delta;
        valor = Math.max(0, Math.min(10, valor));
        input.value = valor;
        
        debug('Quantidade alterada', { produtoId, valor });
    }

    function adicionarProduto(produtoId) {
        debug('=== ADICIONANDO PRODUTO ===', { produtoId });
        
        const input = document.getElementById(`qty-${produtoId}`);
        if (!input) {
            debug('ERRO CRÍTICO: Input não encontrado!', { produtoId });
            alert('Erro: Input de quantidade não encontrado!');
            return;
        }
        
        const quantidade = parseInt(input.value || 0);
        debug('Quantidade capturada', { produtoId, quantidade });
        
        if (quantidade <= 0) {
            debug('Quantidade inválida', { quantidade });
            alert('Defina uma quantidade válida!');
            return;
        }
        
        const produto = produtos[produtoId];
        if (!produto) {
            debug('Produto não encontrado', { produtoId, produtosDisponiveis: Object.keys(produtos) });
            alert('Erro: Produto não encontrado!');
            return;
        }
        
        const itemExistente = carrinho.find(item => item.produto_id == produtoId);
        
        if (itemExistente) {
            debug('Atualizando item existente', itemExistente);
            itemExistente.quantidade += quantidade;
        } else {
            const novoItem = {
                produto_id: parseInt(produtoId),
                nome: produto.nome,
                preco: parseFloat(produto.preco),
                quantidade: quantidade
            };
            carrinho.push(novoItem);
            debug('Novo item adicionado', novoItem);
        }
        
        input.value = 0;
        debug('Carrinho atual', carrinho);
        
        atualizarCarrinho();
        verificarPodeFinalizarPedido();
    }

    function removerItem(produtoId) {
        debug('Removendo item', { produtoId });
        carrinho = carrinho.filter(item => item.produto_id != produtoId);
        atualizarCarrinho();
        verificarPodeFinalizarPedido();
    }

    function atualizarCarrinho() {
        const container = document.getElementById('carrinho-itens');
        
        if (carrinho.length === 0) {
            container.innerHTML = '<div class="text-center text-muted"><em>Carrinho vazio</em></div>';
            document.getElementById('valor-total').textContent = 'R$ 0,00';
            return;
        }
        
        let html = '';
        let total = 0;
        
        carrinho.forEach(item => {
            const subtotal = item.preco * item.quantidade;
            total += subtotal;
            
            html += `
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                    <div>
                        <strong>${item.nome}</strong><br>
                        <small>R$ ${item.preco.toFixed(2)} x ${item.quantidade}</small>
                    </div>
                    <div class="text-end">
                        <div>R$ ${subtotal.toFixed(2)}</div>
                        <button class="btn btn-sm btn-danger" onclick="removerItem(${item.produto_id})">❌</button>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
        document.getElementById('valor-total').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
        
        debug('Carrinho atualizado', { itens: carrinho.length, total });
    }

    function verificarPodeFinalizarPedido() {
        const btn = document.getElementById('finalizar-pedido');
        const pode = mesaSelecionada && carrinho.length > 0;
        
        btn.disabled = !pode;
        btn.className = pode ? 'btn btn-success w-100 mt-3' : 'btn btn-secondary w-100 mt-3';
        btn.innerHTML = pode ? '<i class="fas fa-check"></i> Finalizar Pedido' : '⚠️ Complete o pedido';
        
        debug('Status finalização', { pode, temMesa: !!mesaSelecionada, temItens: carrinho.length > 0 });
    }

    function finalizarPedido() {
        if (!mesaSelecionada || carrinho.length === 0) {
            alert('Complete o pedido!');
            return;
        }
        
        debug('Finalizando pedido...');
        
        const btn = document.getElementById('finalizar-pedido');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
        
        const dados = {
            mesa_id: mesaSelecionada.id,
            itens: carrinho.map(item => ({
                produto_id: item.produto_id,
                quantidade: item.quantidade
            })),
            observacoes: document.getElementById('observacoes').value
        };
        
        debug('Dados para envio', dados);
        
        fetch('{{ route("garcom.pedido-rapido.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(dados)
        })
        .then(response => {
            debug('Response status', { status: response.status });
            return response.json();
        })
        .then(data => {
            debug('Response data', data);
            if (data.success) {
                alert('✅ Pedido criado com sucesso!');
                window.location.href = '{{ route("garcom.dashboard") }}';
            } else {
                alert('❌ Erro: ' + data.message);
                btn.disabled = false;
                verificarPodeFinalizarPedido();
            }
        })
        .catch(error => {
            debug('Erro na requisição', { error: error.message });
            alert('Erro de conexão. Tente novamente.');
            btn.disabled = false;
            verificarPodeFinalizarPedido();
        });
    }

    function testeAutomatico() {
        debug('Executando teste automático...');
        
        // Selecionar primeira mesa
        const primeiraMesa = document.querySelector('[data-mesa-id]');
        if (primeiraMesa) {
            primeiraMesa.click();
        }
        
        // Adicionar primeiro produto
        setTimeout(() => {
            const primeiroInput = document.querySelector('[id^="qty-"]');
            if (primeiroInput) {
                primeiroInput.value = 2;
                const produtoId = primeiroInput.id.replace('qty-', '');
                adicionarProduto(produtoId);
            }
        }, 500);
    }

    function limparTudo() {
        debug('Limpando sistema...');
        carrinho = [];
        mesaSelecionada = null;
        
        document.getElementById('mesa-selecionada').textContent = 'Nenhuma selecionada';
        document.querySelectorAll('.mesa-card').forEach(el => {
            el.style.borderColor = '#ddd';
            el.style.backgroundColor = '';
        });
        
        document.querySelectorAll('[id^="qty-"]').forEach(input => {
            input.value = 0;
        });
        
        atualizarCarrinho();
        verificarPodeFinalizarPedido();        debug('Sistema limpo');
    }
</script>
</body>
</html>
