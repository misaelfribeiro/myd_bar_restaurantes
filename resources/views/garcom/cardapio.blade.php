<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>📖 Cardápio - Modo Garçom</title>
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
        
        .search-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        
        .categoria-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        
        .categoria-header {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            padding: 15px 20px;
            margin: -25px -25px 20px -25px;
            border-radius: 15px 15px 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .produto-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .produto-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            border-color: #6366f1;
        }
        
        .produto-nome {
            font-weight: 700;
            font-size: 1.1rem;
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .produto-descricao {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 10px;
            line-height: 1.4;
        }
        
        .produto-preco {
            font-size: 1.3rem;
            font-weight: 700;
            color: #10b981;
        }
        
        .produto-disponivel {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .disponivel {
            background: #dcfce7;
            color: #166534;
        }
        
        .indisponivel {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .search-input {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px 45px 12px 15px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        
        .search-input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            outline: none;
        }
        
        .search-btn {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 20px;
            transition: all 0.3s ease;
        }
        
        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }
        
        .categoria-badge {
            background: rgba(99, 102, 241, 0.1);
            color: #6366f1;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid rgba(99, 102, 241, 0.2);
        }
        
        .quick-add-btn {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        
        .quick-add-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 3px 10px rgba(16, 185, 129, 0.3);
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6b7280;
        }
        
        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 10px 20px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .produto-card {
                margin-bottom: 10px;
                padding: 12px;
            }
            
            .categoria-header {
                margin: -25px -25px 15px -25px;
                padding: 12px 15px;
                font-size: 1rem;
            }
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
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('garcom.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('garcom.cardapio') }}">
                            <i class="fas fa-book me-1"></i> Cardápio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('garcom.mesas') }}">
                            <i class="fas fa-chair me-1"></i> Mesas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('garcom.meus-pedidos') }}">
                            <i class="fas fa-receipt me-1"></i> Meus Pedidos
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('garcom.pedido-rapido') }}">
                            <i class="fas fa-plus-circle me-1"></i> Novo Pedido
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Hero -->
        <div class="hero-section">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2">
                        <i class="fas fa-book-open me-2"></i>
                        Cardápio Completo
                    </h2>
                    <p class="mb-0">Consulte produtos, preços e disponibilidade em tempo real</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('garcom.dashboard') }}" class="back-btn">
                        <i class="fas fa-arrow-left me-1"></i> Voltar
                    </a>
                </div>
            </div>
        </div>

        <!-- Busca -->
        <div class="search-section">
            <form action="{{ route('garcom.cardapio') }}" method="GET" id="search-form">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="position-relative">
                            <input type="text" 
                                   class="form-control search-input" 
                                   name="busca" 
                                   id="search-input"
                                   placeholder="Digite o nome do produto que está procurando..."
                                   value="{{ request('busca') }}"
                                   autocomplete="off">
                            <i class="fas fa-search position-absolute" 
                               style="right: 15px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="search-btn w-100">
                            <i class="fas fa-search me-2"></i>
                            Buscar Produtos
                        </button>
                    </div>
                </div>
            </form>

            @if(request('busca'))
                <div class="mt-3">
                    <span class="categoria-badge">
                        <i class="fas fa-search me-1"></i>
                        Resultados para: "{{ request('busca') }}"
                    </span>
                    <a href="{{ route('garcom.cardapio') }}" class="ms-2 text-decoration-none">
                        <i class="fas fa-times"></i> Limpar busca
                    </a>
                </div>
            @endif
        </div>

        @if(request('busca') && $produtosBusca->count() > 0)
            <!-- Resultados da Busca -->
            <div class="categoria-section">
                <div class="categoria-header">
                    <div>
                        <h5 class="mb-0">
                            <i class="fas fa-search me-2"></i>
                            Produtos Encontrados
                        </h5>
                    </div>
                    <span class="categoria-badge">{{ $produtosBusca->count() }} produtos</span>
                </div>                <div class="row">
                    @foreach($produtosBusca as $produto)
                    <div class="col-md-6 col-lg-4">
                        <div class="produto-card" data-produto-id="{{ $produto->id }}" onclick="verDetalheProduto(this.dataset.produtoId)">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="produto-nome">{{ $produto->nome }}</div>
                                <span class="produto-disponivel {{ $produto->ativo ? 'disponivel' : 'indisponivel' }}">
                                    {{ $produto->ativo ? '✓ Disponível' : '✗ Indisponível' }}
                                </span>
                            </div>
                            
                            @if($produto->descricao)
                                <div class="produto-descricao">{{ $produto->descricao }}</div>
                            @endif
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="produto-preco">R$ {{ number_format($produto->preco, 2, ',', '.') }}</div>
                                <div>
                                    <small class="text-muted">{{ $produto->categoria->nome }}</small>
                                    @if($produto->ativo)
                                        <button class="quick-add-btn ms-2" data-produto-id="{{ $produto->id }}" onclick="event.stopPropagation(); adicionarAoPedido(this.dataset.produtoId)">
                                            <i class="fas fa-plus me-1"></i> Adicionar
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        @elseif(request('busca'))
            <!-- Nenhum resultado -->
            <div class="categoria-section">
                <div class="empty-state">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <h5>Nenhum produto encontrado</h5>
                    <p>Tente buscar com outros termos ou navegue pelas categorias abaixo.</p>
                    <a href="{{ route('garcom.cardapio') }}" class="btn btn-primary">
                        <i class="fas fa-list me-1"></i> Ver Todas as Categorias
                    </a>
                </div>
            </div>
        @endif

        @if(!request('busca'))
            <!-- Categorias -->
            @forelse($categorias as $categoria)
                @if($categoria->produtos->count() > 0)
                <div class="categoria-section">
                    <div class="categoria-header">
                        <div>
                            <h5 class="mb-0">
                                <i class="fas fa-tag me-2"></i>
                                {{ $categoria->nome }}
                            </h5>
                            @if($categoria->descricao)
                                <small class="opacity-75">{{ $categoria->descricao }}</small>
                            @endif
                        </div>
                        <span class="categoria-badge">{{ $categoria->produtos->count() }} produtos</span>
                    </div>                    <div class="row">
                        @foreach($categoria->produtos as $produto)
                        <div class="col-md-6 col-lg-4">
                            <div class="produto-card" data-produto-id="{{ $produto->id }}" onclick="verDetalheProduto(this.dataset.produtoId)">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="produto-nome">{{ $produto->nome }}</div>
                                    <span class="produto-disponivel {{ $produto->ativo ? 'disponivel' : 'indisponivel' }}">
                                        {{ $produto->ativo ? '✓ Disponível' : '✗ Indisponível' }}
                                    </span>
                                </div>
                                
                                @if($produto->descricao)
                                    <div class="produto-descricao">{{ $produto->descricao }}</div>
                                @endif
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="produto-preco">R$ {{ number_format($produto->preco, 2, ',', '.') }}</div>
                                    @if($produto->ativo)
                                        <button class="quick-add-btn" data-produto-id="{{ $produto->id }}" onclick="event.stopPropagation(); adicionarAoPedido(this.dataset.produtoId)">
                                            <i class="fas fa-plus me-1"></i> Adicionar
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            @empty
                <div class="categoria-section">
                    <div class="empty-state">
                        <i class="fas fa-utensils fa-3x mb-3"></i>
                        <h5>Nenhuma categoria encontrada</h5>
                        <p>O cardápio ainda não possui categorias cadastradas.</p>
                    </div>
                </div>
            @endforelse
        @endif
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Busca em tempo real
        let searchTimeout;
        document.getElementById('search-input').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length >= 2) {
                    buscarProdutos(this.value);
                }
            }, 300);
        });

        function buscarProdutos(termo) {
            // Implementar busca AJAX se necessário
            console.log('Buscando:', termo);
        }

        function verDetalheProduto(produtoId) {
            window.location.href = `/produtos/${produtoId}`;
        }

        function adicionarAoPedido(produtoId) {
            // Simular adição ao carrinho/pedido
            const btn = event.target.closest('button');
            const originalText = btn.innerHTML;
            
            btn.innerHTML = '<i class="fas fa-check me-1"></i> Adicionado!';
            btn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
            
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
            }, 1500);
            
            // Aqui você pode implementar a lógica real de adição
            console.log('Produto adicionado:', produtoId);
        }

        // Animações de entrada
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.produto-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 50);
            });
        });
    </script>
</body>
</html>
