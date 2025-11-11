<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>➕ Adicionar Itens - Mesa {{ $mesa->identificador }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar-glass {
            background: rgba(255, 255, 255, 0.1) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .navbar-glass .navbar-brand,
        .navbar-glass .nav-link {
            color: white !important;
            font-weight: 600;
        }
        
        .hero-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            margin: 20px 0;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
        }
        
        .step-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        
        .step-header {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
            padding: 15px 20px;
            margin: -25px -25px 20px -25px;
            border-radius: 15px 15px 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .step-number {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 1.2rem;
            margin-right: 15px;
        }
        
        .pedido-atual {
            background: rgba(139, 92, 246, 0.1);
            border: 2px solid rgba(139, 92, 246, 0.3);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .pedido-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .pedido-detalhes h6 {
            color: #8b5cf6;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .pedido-meta {
            color: #6b7280;
            font-size: 0.9rem;
        }
        
        .pedido-total {
            text-align: right;
        }
        
        .total-atual {
            font-size: 1.5rem;
            font-weight: 900;
            color: #10b981;
        }
        
        .produto-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .produto-lista {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 20px;
        }

        .produto-item {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 15px;
            transition: all 0.3s ease;
        }

        .produto-item-lista {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s ease;
            gap: 15px;
        }

        .produto-item:hover {
            border-color: #8b5cf6;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .produto-item-lista:hover {
            border-color: #8b5cf6;
            background: rgba(139, 92, 246, 0.05);
            transform: translateX(3px);
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.2);
        }

        .produto-nome {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .produto-categoria {
            font-size: 0.8rem;
            color: #8b5cf6;
            margin-bottom: 8px;
        }

        .produto-preco {
            font-size: 1.2rem;
            font-weight: 700;
            color: #10b981;
            margin-bottom: 10px;
        }

        .quantidade-controls {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 10px;
        }

        .qty-btn {
            background: #8b5cf6;
            color: white;
            border: none;
            border-radius: 6px;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .qty-btn:hover {
            background: #7c3aed;
            transform: scale(1.1);
        }

        .qty-input {
            width: 60px;
            text-align: center;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 5px;
        }

        /* ESTILOS PARA VISUALIZAÇÃO EM LISTA */
        .produto-info-lista {
            flex: 1;
            min-width: 0;
        }

        .produto-principal {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4px;
        }

        .produto-nome-lista {
            font-weight: 700;
            color: #1f2937;
            margin-right: 15px;
        }

        .produto-preco-lista {
            font-weight: 700;
            color: #10b981;
            font-size: 1.1em;
        }

        .produto-descricao-lista {
            font-size: 0.85em;
            color: #6b7280;
            line-height: 1.3;
        }

        .produto-acoes-lista {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: 15px;
        }

        .btn-rapido {
            background: #8b5cf6;
            border: none;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            min-width: 35px;
        }

        .btn-rapido:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(139, 92, 246, 0.3);
        }

        .btn-add-1 { background: #28a745; }
        .btn-add-1:hover { 
            background: #218838;
            box-shadow: 0 2px 6px rgba(40, 167, 69, 0.3);
        }

        .btn-add-2 { background: #fd7e14; }
        .btn-add-2:hover { 
            background: #e55d00;
            box-shadow: 0 2px 6px rgba(253, 126, 20, 0.3);
        }

        .btn-add-3 { background: #dc3545; }
        .btn-add-3:hover { 
            background: #c82333;
            box-shadow: 0 2px 6px rgba(220, 53, 69, 0.3);
        }

        .quantidade-custom {
            display: flex;
            align-items: center;
            gap: 4px;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            padding: 2px;
            background: white;
        }

        .qty-input-lista {
            width: 40px;
            border: none;
            text-align: center;
            padding: 4px;
            font-size: 0.9em;
            background: transparent;
        }

        .btn-custom-add {
            background: #6c757d;
            border: none;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.8em;
            transition: all 0.2s ease;
        }

        .btn-custom-add:hover {
            background: #5a6268;
            transform: scale(1.05);
        }

        /* Botões de alternância de visualização */
        .btn-group .btn {
            border-radius: 20px !important;
            transition: all 0.2s ease;
        }

        .btn-group .btn-light {
            background: #8b5cf6;
            border-color: #8b5cf6;
            color: white;
        }

        .btn-group .btn-outline-light {
            color: rgba(255,255,255,0.7);
            border-color: rgba(255,255,255,0.3);
        }

        .btn-group .btn-outline-light:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.5);
            color: white;
        }

        .carrinho-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            position: sticky;
            top: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }

        .carrinho-header {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
            padding: 15px 20px;
            margin: -25px -25px 20px -25px;
            border-radius: 15px 15px 0 0;
            display: flex;
            align-items: center;
            justify-content: between;
        }

        .carrinho-item {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .carrinho-item:last-child {
            border-bottom: none;
        }

        .item-info {
            flex: 1;
        }

        .item-nome {
            font-weight: 600;
            color: #1f2937;
        }        .item-preco {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .observacoes-campo {
            margin-top: 8px;
        }

        .observacoes-campo textarea {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            font-size: 0.85em;
            padding: 6px 8px;
            transition: border-color 0.2s ease;
        }

        .observacoes-campo textarea:focus {
            border-color: #8b5cf6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }

        .item-quantidade {
            background: rgba(139, 92, 246, 0.1);
            color: #8b5cf6;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .remove-item {
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            font-size: 0.8rem;
            margin-left: 10px;
        }

        .total-section {
            border-top: 2px solid #e5e7eb;
            padding-top: 15px;
            margin-top: 15px;
        }

        .total-valor {
            font-size: 1.5rem;
            font-weight: 900;
            color: #8b5cf6;
        }

        .finalizar-btn {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 15px;
            width: 100%;
            font-size: 1.1rem;
            font-weight: 700;
            margin-top: 15px;
            transition: all 0.3s ease;
        }

        .finalizar-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(139, 92, 246, 0.3);
        }

        .finalizar-btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
        }

        @media (max-width: 768px) {
            .produto-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }            .carrinho-section {
                position: static;
                margin-top: 20px;
            }
        }
        
        /* Estilos para pesquisa de produtos */
        #resultados-busca {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            max-height: 300px;
            overflow-y: auto;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .resultado-produto {
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .resultado-produto:last-child {
            border-bottom: none;
        }
        
        .resultado-produto:hover {
            background: rgba(139, 92, 246, 0.05);
            border-left: 4px solid #8b5cf6;
        }
        
        .resultado-info {
            flex: 1;
        }
        
        .resultado-nome {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 2px;
        }
        
        .resultado-detalhes {
            font-size: 0.85em;
            color: #6b7280;
        }
        
        .resultado-preco {
            font-weight: 700;
            color: #10b981;
            font-size: 1.1em;
        }
        
        .resultado-codigo {
            background: #f3f4f6;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.75em;
            color: #6b7280;
            margin-left: 8px;
        }
        
        .badge-preparo {
            background: #fef3c7;
            color: #92400e;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75em;
            font-weight: 600;
            margin-left: 8px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-glass">
        <div class="container">
            <a class="navbar-brand" href="{{ route('garcom.dashboard') }}">
                <i class="fas fa-utensils me-2"></i>
                Modo Garçom
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('garcom.mesas') }}">
                    <i class="fas fa-arrow-left me-1"></i> Voltar às Mesas
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Hero -->
        <div class="hero-section">
            <h2 class="mb-2">
                <i class="fas fa-plus-circle me-2"></i>
                Adicionar Itens - Mesa {{ $mesa->identificador }}
            </h2>
            <p class="mb-0">Adicione novos itens ao pedido #{{ $pedido->id }} em andamento</p>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Informações do Pedido Atual -->
                <div class="step-section">
                    <div class="step-header">
                        <div>
                            <div class="step-number">📋</div>
                            <h5 class="mb-0">Pedido Atual</h5>
                        </div>
                    </div>
                    
                    <div class="pedido-atual">
                        <div class="pedido-info">
                            <div class="pedido-detalhes">
                                <h6>Pedido #{{ $pedido->id }}</h6>
                                <div class="pedido-meta">
                                    <i class="fas fa-chair me-1"></i> Mesa {{ $mesa->identificador }} •
                                    <i class="fas fa-user me-1"></i> {{ $pedido->usuario->nome }} •
                                    <i class="fas fa-clock me-1"></i> {{ $pedido->created_at->format('H:i') }}
                                </div>
                            </div>
                            <div class="pedido-total">
                                <div class="pedido-meta">Total Atual</div>
                                <div class="total-atual">R$ {{ number_format($pedido->total, 2, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Adicionando itens:</strong> Os novos itens serão adicionados ao pedido existente. O total será atualizado automaticamente.
                    </div>
                </div>

                <!-- Produtos -->
                <div class="step-section">
                    <div class="step-header">
                        <div>
                            <div class="step-number">➕</div>
                            <h5 class="mb-0">Adicionar Produtos</h5>
                        </div>
                        <div class="ms-auto">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-light" id="view-grid" onclick="trocarVisualizacao('grid')">
                                    <i class="fas fa-th-large"></i> Grade
                                </button>
                                <button type="button" class="btn btn-sm btn-light" id="view-list" onclick="trocarVisualizacao('list')">
                                    <i class="fas fa-list"></i> Lista
                                </button>
                            </div>
                        </div>                    </div>
                    
                    <!-- Campo de Pesquisa de Produtos -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" class="form-control" id="campo-busca-produto" 
                                       placeholder="Buscar por nome ou código do produto..." 
                                       autocomplete="off">
                                <button class="btn btn-outline-secondary" type="button" id="limpar-busca">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div id="resultados-busca" class="mt-2" style="display: none;"></div>
                        </div>
                    </div>
                    
                    @forelse($categorias as $categoria)
                        @if($categoria->produtos->count() > 0)
                            <h6 class="mt-4 mb-3 text-primary">
                                <i class="fas fa-tag me-1"></i> {{ $categoria->nome }}
                            </h6>
                            
                            <!-- Visualização em Grade (Padrão) -->
                            <div class="produto-grid view-grid">
                                @foreach($categoria->produtos as $produto)
                                    <div class="produto-item" data-produto-id="{{ $produto->id }}">
                                        <div class="produto-nome">{{ $produto->nome }}</div>
                                        <div class="produto-categoria">{{ $categoria->nome }}</div>
                                        @if($produto->descricao)
                                            <div class="produto-descricao text-muted">{{ Str::limit($produto->descricao, 60) }}</div>
                                        @endif
                                        <div class="produto-preco">R$ {{ number_format($produto->preco, 2, ',', '.') }}</div>
                                        <div class="quantidade-controls">
                                            <div class="d-flex align-items-center">
                                                <button class="qty-btn" data-produto-id="{{ $produto->id }}" onclick="alterarQuantidade(this.dataset.produtoId, -1)">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" class="qty-input mx-2" id="qty-{{ $produto->id }}" value="0" min="0" max="10" readonly>
                                                <button class="qty-btn" data-produto-id="{{ $produto->id }}" onclick="alterarQuantidade(this.dataset.produtoId, 1)">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                            <button class="btn btn-sm btn-outline-primary" data-produto-id="{{ $produto->id }}" onclick="adicionarProduto(this.dataset.produtoId)">
                                                <i class="fas fa-cart-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Visualização em Lista -->
                            <div class="produto-lista view-list" style="display: none;">
                                @foreach($categoria->produtos as $produto)
                                    <div class="produto-item-lista" data-produto-id="{{ $produto->id }}">
                                        <div class="produto-info-lista">
                                            <div class="produto-principal">
                                                <span class="produto-nome-lista">{{ $produto->nome }}</span>
                                                <span class="produto-preco-lista">R$ {{ number_format($produto->preco, 2, ',', '.') }}</span>
                                            </div>
                                            @if($produto->descricao)
                                                <div class="produto-descricao-lista">{{ Str::limit($produto->descricao, 80) }}</div>
                                            @endif
                                        </div>
                                        <div class="produto-acoes-lista">
                                            <button class="btn-rapido btn-add-1" data-produto-id="{{ $produto->id }}" onclick="adicionarRapido(this.dataset.produtoId, 1)" title="Adicionar 1">
                                                +1
                                            </button>
                                            <button class="btn-rapido btn-add-2" data-produto-id="{{ $produto->id }}" onclick="adicionarRapido(this.dataset.produtoId, 2)" title="Adicionar 2">
                                                +2
                                            </button>
                                            <button class="btn-rapido btn-add-3" data-produto-id="{{ $produto->id }}" onclick="adicionarRapido(this.dataset.produtoId, 3)" title="Adicionar 3">
                                                +3
                                            </button>
                                            <div class="quantidade-custom">
                                                <input type="number" class="qty-input-lista" id="qty-lista-{{ $produto->id }}" value="1" min="1" max="10" readonly>
                                                <button class="btn-custom-add" data-produto-id="{{ $produto->id }}" onclick="adicionarCustom(this.dataset.produtoId)" title="Adicionar quantidade personalizada">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @empty
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Nenhuma categoria com produtos disponível.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Carrinho de Itens a Adicionar -->
            <div class="col-lg-4">
                <div class="carrinho-section">
                    <div class="carrinho-header">
                        <h5 class="mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Itens a Adicionar
                        </h5>
                    </div>
                    
                    <!-- Itens do carrinho -->
                    <div id="carrinho-itens">
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-cart-plus fa-2x mb-2"></i>
                            <p>Nenhum item selecionado<br><small>Adicione produtos para continuar</small></p>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="total-section">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Total Adicional:</span>
                            <span class="total-valor" id="valor-total">R$ 0,00</span>
                        </div>
                    </div>

                    <!-- Finalizar -->
                    <button class="finalizar-btn" id="adicionar-itens" onclick="adicionarItensAoPedido()" disabled>
                        <i class="fas fa-plus me-2"></i>
                        Adicionar ao Pedido
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <!-- Dados do servidor para JavaScript -->
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
        let carrinho = [];
        let produtos = {};
        const pedidoId = {{ $pedido->id }};

        // Inicializar dados dos produtos
        try {
            const dadosElement = document.getElementById('dados-produtos');
            produtos = JSON.parse(dadosElement.textContent);
            console.log('✅ Produtos carregados:', Object.keys(produtos).length);
        } catch (error) {
            console.error('❌ Erro ao carregar produtos:', error);
            produtos = {};
        }

        function trocarVisualizacao(tipo) {
            const gridElements = document.querySelectorAll('.view-grid');
            const listElements = document.querySelectorAll('.view-list');
            const btnGrid = document.getElementById('view-grid');
            const btnList = document.getElementById('view-list');
            
            if (tipo === 'grid') {
                gridElements.forEach(el => el.style.display = 'grid');
                listElements.forEach(el => el.style.display = 'none');
                btnGrid.classList.remove('btn-outline-light');
                btnGrid.classList.add('btn-light');
                btnList.classList.remove('btn-light');
                btnList.classList.add('btn-outline-light');
            } else if (tipo === 'list') {
                gridElements.forEach(el => el.style.display = 'none');
                listElements.forEach(el => el.style.display = 'flex');
                btnList.classList.remove('btn-outline-light');
                btnList.classList.add('btn-light');
                btnGrid.classList.remove('btn-light');
                btnGrid.classList.add('btn-outline-light');
            }
        }

        function alterarQuantidade(produtoId, delta) {
            const qtyInput = document.getElementById(`qty-${produtoId}`);
            if (!qtyInput) return;
            
            let novaQty = parseInt(qtyInput.value || 0) + delta;
            novaQty = Math.max(0, Math.min(10, novaQty));
            qtyInput.value = novaQty;
        }

        function adicionarProduto(produtoId) {
            const qtyInput = document.getElementById(`qty-${produtoId}`);
            if (!qtyInput) return;
            
            const quantidade = parseInt(qtyInput.value || 0);
            if (quantidade <= 0) {
                alert('Selecione uma quantidade maior que zero!');
                return;
            }

            const produto = produtos[produtoId];
            if (!produto) {
                alert(`Produto ${produtoId} não encontrado!`);
                return;
            }

            const itemExistente = carrinho.find(item => item.produto_id == produtoId);
            if (itemExistente) {
                itemExistente.quantidade += quantidade;
            } else {
                carrinho.push({
                    produto_id: parseInt(produtoId),
                    nome: produto.nome,
                    preco: parseFloat(produto.preco),
                    quantidade: quantidade
                });
            }

            qtyInput.value = 0;
            atualizarCarrinho();
            verificarPodeAdicionar();
        }

        function adicionarRapido(produtoId, quantidade) {
            const produto = produtos[produtoId];
            if (!produto) {
                alert(`Produto ${produtoId} não encontrado!`);
                return;
            }
            
            const itemExistente = carrinho.find(item => item.produto_id == produtoId);
            if (itemExistente) {
                itemExistente.quantidade += quantidade;
            } else {
                carrinho.push({
                    produto_id: parseInt(produtoId),
                    nome: produto.nome,
                    preco: parseFloat(produto.preco),
                    quantidade: quantidade
                });
            }
            
            atualizarCarrinho();
            verificarPodeAdicionar();
            mostrarFeedbackAdicao(produtoId, quantidade);
        }

        function adicionarCustom(produtoId) {
            const qtyInput = document.getElementById(`qty-lista-${produtoId}`);
            if (!qtyInput) return;
            
            const quantidade = parseInt(qtyInput.value || 1);
            if (quantidade <= 0 || quantidade > 10) {
                alert('Quantidade deve ser entre 1 e 10!');
                return;
            }
            
            adicionarRapido(produtoId, quantidade);
            qtyInput.value = 1;
        }        function removerItem(produtoId) {
            carrinho = carrinho.filter(item => item.produto_id != produtoId);
            atualizarCarrinho();
            verificarPodeAdicionar();
        }

        function atualizarObservacoes(produtoId, observacoes) {
            console.log('📝 Atualizando observações:', { produtoId, observacoes });
            
            const item = carrinho.find(item => item.produto_id == produtoId);
            if (item) {
                item.observacoes = observacoes;
                console.log('✅ Observações atualizadas:', item);
            }
        }

        function atualizarCarrinho() {
            const carrinhoDiv = document.getElementById('carrinho-itens');
            
            if (carrinho.length === 0) {
                carrinhoDiv.innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-cart-plus fa-2x mb-2"></i>
                        <p>Nenhum item selecionado<br><small>Adicione produtos para continuar</small></p>
                    </div>
                `;
                document.getElementById('valor-total').textContent = 'R$ 0,00';
                return;
            }

            let html = '';
            let total = 0;            carrinho.forEach(item => {
                const subtotal = parseFloat(item.preco) * parseInt(item.quantidade);
                total += subtotal;
                const observacoesValue = item.observacoes || '';
                
                html += `
                    <div class="carrinho-item">
                        <div class="item-info">
                            <div class="item-nome">${item.nome}</div>
                            <div class="item-preco">R$ ${parseFloat(item.preco).toFixed(2).replace('.', ',')} x ${item.quantidade}</div>
                            <div class="observacoes-campo mt-2">
                                <textarea class="form-control form-control-sm" 
                                          placeholder="Observações especiais..." 
                                          onchange="atualizarObservacoes(${item.produto_id}, this.value)"
                                          rows="2"
                                          style="font-size: 0.85em; resize: none;">${observacoesValue}</textarea>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="item-quantidade">R$ ${subtotal.toFixed(2).replace('.', ',')}</span>
                            <button class="remove-item" onclick="removerItem(${item.produto_id})" title="Remover">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;
            });

            carrinhoDiv.innerHTML = html;
            document.getElementById('valor-total').textContent = 
                'R$ ' + total.toFixed(2).replace('.', ',');
        }

        function verificarPodeAdicionar() {
            const btn = document.getElementById('adicionar-itens');
            const podeAdicionar = carrinho.length > 0;
            
            btn.disabled = !podeAdicionar;
            
            if (podeAdicionar) {
                btn.innerHTML = '<i class="fas fa-plus me-2"></i>Adicionar ao Pedido';
            } else {
                btn.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Selecione itens';
            }
        }

        function adicionarItensAoPedido() {
            if (carrinho.length === 0) {
                alert('Adicione pelo menos um item antes de continuar');
                return;
            }

            const btn = document.getElementById('adicionar-itens');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adicionando...';            const dados = {
                pedido_id: pedidoId,
                itens: carrinho.map(item => ({
                    produto_id: item.produto_id,
                    quantidade: item.quantidade,
                    observacoes: item.observacoes || ''
                }))
            };

            console.log('Enviando dados:', dados);

            fetch('{{ route("garcom.pedido-rapido.adicionar.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(dados)
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response:', data);
                if (data.success) {
                    alert(`Itens adicionados com sucesso!\n\nTotal adicional: R$ ${data.total_adicional.toFixed(2).replace('.', ',')}\nNovo total do pedido: R$ ${data.total_novo.toFixed(2).replace('.', ',')}`);
                    window.location.href = '{{ route("garcom.pedidos.show", $pedido->id) }}';
                } else {
                    alert('Erro: ' + data.message);
                    btn.disabled = false;
                    verificarPodeAdicionar();
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao adicionar itens. Tente novamente.');
                btn.disabled = false;
                verificarPodeAdicionar();
            });
        }

        function mostrarFeedbackAdicao(produtoId, quantidade) {
            const produtoElementGrid = document.querySelector(`.view-grid .produto-item[data-produto-id="${produtoId}"]`);
            const produtoElementList = document.querySelector(`.view-list .produto-item-lista[data-produto-id="${produtoId}"]`);
            
            const elemento = produtoElementGrid && produtoElementGrid.offsetParent ? produtoElementGrid : produtoElementList;
            
            if (elemento) {
                const feedback = document.createElement('div');
                feedback.style.cssText = `
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    background: #28a745;
                    color: white;
                    padding: 8px 16px;
                    border-radius: 20px;
                    font-weight: bold;
                    z-index: 1000;
                    pointer-events: none;
                    font-size: 0.9em;
                `;
                feedback.innerHTML = `+${quantidade} adicionado!`;
                
                elemento.style.position = 'relative';
                elemento.appendChild(feedback);
                
                setTimeout(() => {
                    feedback.style.transition = 'all 0.5s ease';
                    feedback.style.transform = 'translate(-50%, -150%)';
                    feedback.style.opacity = '0';
                    setTimeout(() => {
                        if (feedback.parentNode) {
                            feedback.parentNode.removeChild(feedback);
                        }
                    }, 500);
                }, 100);
            }        }
        
        // Funcionalidade de pesquisa de produtos
        let timeoutBusca = null;
        
        function inicializarPesquisa() {
            const campoBusca = document.getElementById('campo-busca-produto');
            const limparBusca = document.getElementById('limpar-busca');
            const resultadosBusca = document.getElementById('resultados-busca');
            
            if (!campoBusca || !limparBusca || !resultadosBusca) {
                console.error('Elementos de pesquisa não encontrados');
                return;
            }
            
            // Event listener para o campo de busca
            campoBusca.addEventListener('input', function(e) {
                const termo = e.target.value.trim();
                
                // Limpar timeout anterior
                if (timeoutBusca) {
                    clearTimeout(timeoutBusca);
                }
                
                if (termo.length >= 2) {
                    timeoutBusca = setTimeout(() => {
                        buscarProdutos(termo);
                    }, 300);
                } else {
                    ocultarResultados();
                }
            });
            
            // Event listener para limpar busca
            limparBusca.addEventListener('click', function() {
                campoBusca.value = '';
                ocultarResultados();
                campoBusca.focus();
            });
            
            // Event listener para tecla Enter
            campoBusca.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const termo = e.target.value.trim();
                    if (termo.length >= 2) {
                        buscarProdutos(termo);
                    }
                }
            });
        }
        
        function buscarProdutos(termo) {
            console.log('🔍 Buscando produtos por:', termo);
            
            const resultadosDiv = document.getElementById('resultados-busca');
            resultadosDiv.innerHTML = '<div class="text-center py-2"><i class="fas fa-spinner fa-spin"></i> Buscando...</div>';
            resultadosDiv.style.display = 'block';
            
            fetch(`{{ route('garcom.buscar-produtos') }}?q=${encodeURIComponent(termo)}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(produtos => {
                console.log('📦 Produtos encontrados:', produtos);
                exibirResultados(produtos);
            })
            .catch(error => {
                console.error('❌ Erro na busca:', error);
                resultadosDiv.innerHTML = '<div class="text-center py-2 text-danger"><i class="fas fa-exclamation-triangle"></i> Erro na busca</div>';
            });
        }
        
        function exibirResultados(produtos) {
            const resultadosDiv = document.getElementById('resultados-busca');
            
            if (produtos.length === 0) {
                resultadosDiv.innerHTML = '<div class="text-center py-2 text-muted"><i class="fas fa-search"></i> Nenhum produto encontrado</div>';
                return;
            }
            
            let html = '';
            produtos.forEach(produto => {
                const codigoBadge = produto.codigo ? `<span class="resultado-codigo">${produto.codigo}</span>` : '';
                const preparoBadge = produto.tipo_preparo === 'preparo' ? `<span class="badge-preparo">Preparo</span>` : '';
                
                html += `
                    <div class="resultado-produto" onclick="adicionarProdutoBusca(${produto.id}, '${produto.nome}', ${produto.preco}, '${produto.tipo_preparo || 'pronto'}')">
                        <div class="resultado-info">
                            <div class="resultado-nome">
                                ${produto.nome} ${codigoBadge} ${preparoBadge}
                            </div>
                            <div class="resultado-detalhes">
                                ${produto.categoria} • ${produto.descricao || 'Sem descrição'}
                            </div>
                        </div>
                        <div class="resultado-preco">
                            R$ ${parseFloat(produto.preco).toFixed(2).replace('.', ',')}
                        </div>
                    </div>
                `;
            });
            
            resultadosDiv.innerHTML = html;
            resultadosDiv.style.display = 'block';
        }
        
        function adicionarProdutoBusca(produtoId, nome, preco, tipoPreparo) {
            console.log('🛒 Adicionando produto da busca:', { produtoId, nome, preco, tipoPreparo });
            
            let observacoesProduto = '';
            
            // Se o produto precisa de preparo, solicitar observações
            if (tipoPreparo === 'preparo') {
                observacoesProduto = prompt(
                    `🍽️ ${nome}\n\nEste prato precisa de preparo. Alguma observação especial?\n(Ex: ponto da carne, ingredientes, etc.)`,
                    ''
                );
                
                // Se o usuário cancelou o prompt, não adiciona o produto
                if (observacoesProduto === null) {
                    return;
                }
            }
            
            // Verificar item existente no carrinho
            const itemExistente = carrinho.find(item => item.produto_id == produtoId);
            
            if (itemExistente) {
                itemExistente.quantidade += 1;
                if (observacoesProduto) {
                    itemExistente.observacoes = (itemExistente.observacoes || '') + (itemExistente.observacoes ? ' | ' : '') + observacoesProduto;
                }
            } else {
                const novoItem = {
                    produto_id: parseInt(produtoId),
                    nome: nome,
                    preco: parseFloat(preco),
                    quantidade: 1,
                    observacoes: observacoesProduto || ''
                };
                carrinho.push(novoItem);
            }
            
            // Atualizar interface
            atualizarCarrinho();
            verificarPodeAdicionar();
            ocultarResultados();
            
            // Limpar campo de busca
            document.getElementById('campo-busca-produto').value = '';
            
            console.log('✅ Produto adicionado da busca com sucesso');
        }
        
        function ocultarResultados() {
            const resultadosDiv = document.getElementById('resultados-busca');
            resultadosDiv.style.display = 'none';
            resultadosDiv.innerHTML = '';
        }        // Inicializar interface
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Interface de adição de itens carregada');
            console.log('📋 Pedido ID:', pedidoId);
            console.log('📦 Produtos disponíveis:', Object.keys(produtos).length);
            
            // Inicializar funcionalidade de pesquisa
            inicializarPesquisa();
            
            verificarPodeAdicionar();
        });
    </script>
</body>
</html>
