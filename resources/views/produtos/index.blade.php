<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cardápio - Sistema Bar/Restaurante</title>
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
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            text-align: center;
        }
        
        .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .filters-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        
        .products-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        
        .products-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 20px;
            margin: 0;
        }
        
        .product-item {
            padding: 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .product-item:hover {
            background: rgba(102, 126, 234, 0.05);
            transform: translateX(5px);
        }
        
        .product-item:last-child {
            border-bottom: none;
        }
        
        .product-info {
            flex: 1;
            min-width: 300px;
        }
        
        .product-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        
        .product-description {
            color: #6c757d;
            font-size: 0.95rem;
            margin-bottom: 8px;
        }
        
        .product-meta {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .product-price {
            font-size: 1.4rem;
            font-weight: 700;
            color: #28a745;
        }
        
        .category-badge {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            color: white;
        }
        
        .status-ativo {
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        
        .status-inativo {
            background: linear-gradient(135deg, #6c757d, #495057);
        }
        
        .product-actions {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
        }
        
        .btn-action {
            padding: 8px 12px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
        }
        
        .btn-view {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
        }
        
        .btn-edit {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            color: white;
        }
        
        .btn-toggle {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
        }
        
        .btn-delete {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            color: white;
            text-decoration: none;
        }
        
        .btn-new {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            color: white;
            padding: 15px 30px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        
        .btn-new:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
            color: white;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: white;
        }
        
        .empty-icon {
            font-size: 5rem;
            margin-bottom: 30px;
            opacity: 0.7;
        }
        
        .stats-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
        }
        
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
        
        .product-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            .product-item {
                flex-direction: column;
                align-items: flex-start;
            }
            .product-actions {
                width: 100%;
                justify-content: center;
            }
            .product-info {
                min-width: auto;
                width: 100%;
            }
        }
        
        .fade-in {
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-glass">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-utensils me-2"></i>
                Sistema Bar/Restaurante
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('dashboard') ?? '/' }}">
                    <i class="fas fa-home me-1"></i>Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <!-- Hero Section -->
        <div class="hero-section">
            <h1 class="hero-title">
                <i class="fas fa-utensils me-3"></i>
                Gerenciamento de Produtos
            </h1>
            <p class="mb-4">Gerencie todos os produtos do seu cardápio de forma fácil e eficiente</p>
            <button class="btn btn-new" data-bs-toggle="modal" data-bs-target="#newProductModal">
                <i class="fas fa-plus me-2"></i>
                Adicionar Novo Produto
            </button>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: rgba(40, 167, 69, 0.9); border: none; color: white; border-radius: 15px;">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background: rgba(220, 53, 69, 0.9); border: none; color: white; border-radius: 15px;">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filters -->
        <div class="filters-section">
            <div class="row align-items-end">
                <div class="col-lg-4 col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-search me-1"></i>
                        Buscar Produto
                    </label>
                    <input type="text" class="form-control form-control-lg" id="searchInput" 
                           placeholder="Digite o nome do produto..." style="border-radius: 15px;">
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-tags me-1"></i>
                        Categoria
                    </label>
                    <select class="form-select form-select-lg" id="categoriaFilter" style="border-radius: 15px;">
                        <option value="">Todas as categorias</option>
                        @foreach($produtos->pluck('categoria')->unique()->filter() as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-toggle-on me-1"></i>
                        Status
                    </label>
                    <select class="form-select form-select-lg" id="statusFilter" style="border-radius: 15px;">
                        <option value="">Todos os status</option>
                        <option value="1">Disponível</option>
                        <option value="0">Indisponível</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6 mb-3">
                    <button class="btn btn-outline-secondary btn-lg w-100" onclick="limparFiltros()" style="border-radius: 15px;">
                        <i class="fas fa-times me-1"></i>
                        Limpar
                    </button>
                </div>
            </div>
            
            <!-- Stats -->
            <div class="row mt-3">
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number" id="totalProdutos">{{ $produtos->count() }}</div>
                        <small class="text-muted">Total de Produtos</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number text-success">{{ $produtos->where('ativo', true)->count() }}</div>
                        <small class="text-muted">Disponíveis</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number text-secondary">{{ $produtos->where('ativo', false)->count() }}</div>
                        <small class="text-muted">Indisponíveis</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number text-info">{{ $produtos->pluck('categoria')->unique()->filter()->count() }}</div>
                        <small class="text-muted">Categorias</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products List -->
        <div class="products-container" id="produtosContainer">
            <div class="products-header">
                <h3 class="mb-0">
                    <i class="fas fa-list-ul me-2"></i>
                    Lista de Produtos
                    <span class="float-end">
                        <small>{{ $produtos->count() }} {{ $produtos->count() == 1 ? 'item' : 'itens' }}</small>
                    </span>
                </h3>
            </div>
            
            @if($produtos->count() > 0)
                @foreach($produtos as $produto)
                    <div class="product-item fade-in" 
                         data-categoria="{{ $produto->categoria_id }}" 
                         data-status="{{ $produto->ativo ? 1 : 0 }}"
                         data-nome="{{ strtolower($produto->nome) }}">
                        
                        <!-- Product Icon -->
                        <div class="product-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        
                        <!-- Product Info -->
                        <div class="product-info">
                            <div class="product-name">{{ $produto->nome }}</div>
                            @if($produto->descricao)
                                <div class="product-description">{{ $produto->descricao }}</div>
                            @else
                                <div class="product-description text-muted">Sem descrição disponível</div>
                            @endif
                            <div class="product-meta">
                                <div class="product-price">{{ $produto->preco_formatado }}</div>
                                @if($produto->categoria)
                                    <span class="category-badge">{{ $produto->categoria->nome }}</span>
                                @endif
                                <span class="status-badge {{ $produto->ativo ? 'status-ativo' : 'status-inativo' }}">
                                    <i class="fas {{ $produto->ativo ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                    {{ $produto->ativo ? 'Disponível' : 'Indisponível' }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="product-actions">
                            <a href="{{ route('produtos.show', $produto->id) }}" 
                               class="btn-action btn-view" 
                               title="Visualizar"
                               data-bs-toggle="tooltip">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('produtos.edit', $produto->id) }}" 
                               class="btn-action btn-edit" 
                               title="Editar"
                               data-bs-toggle="tooltip">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn-action btn-toggle" 
                                    onclick="toggleStatus({{ $produto->id }})" 
                                    title="{{ $produto->ativo ? 'Desativar' : 'Ativar' }}"
                                    data-bs-toggle="tooltip">
                                <i class="fas fa-{{ $produto->ativo ? 'eye-slash' : 'eye' }}"></i>
                            </button>
                            <button class="btn-action btn-delete" 
                                    onclick="confirmarExclusao({{ $produto->id }}, '{{ $produto->nome }}')" 
                                    title="Excluir"
                                    data-bs-toggle="tooltip">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12">
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h3>Nenhum produto cadastrado</h3>
                        <p>Comece adicionando produtos deliciosos ao seu cardápio!</p>
                        <button class="btn btn-new" data-bs-toggle="modal" data-bs-target="#newProductModal">
                            <i class="fas fa-plus me-2"></i>
                            Adicionar Primeiro Produto
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Criar Produto -->
    <div class="modal fade" id="newProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="border-radius: 20px; border: none;">
                <div class="modal-header" style="background: linear-gradient(135deg, #28a745, #20c997); color: white; border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle me-2"></i>
                        Adicionar Novo Produto
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center">
                        <i class="fas fa-utensils text-success" style="font-size: 3rem; margin-bottom: 20px;"></i>
                        <h5>Vamos criar um novo produto!</h5>
                        <p class="text-muted mb-4">Clique no botão abaixo para ir para o formulário de cadastro completo.</p>
                        <a href="{{ route('produtos.create') }}" class="btn btn-success btn-lg" style="border-radius: 25px;">
                            <i class="fas fa-arrow-right me-2"></i>
                            Ir para Formulário de Cadastro
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade" id="modalExcluir" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 20px; border: none;">
                <div class="modal-header bg-danger text-white" style="border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Confirmar Exclusão
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-3">
                        <i class="fas fa-trash-alt text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <p class="text-center">Tem certeza que deseja excluir o produto <strong id="nomeProdutoExcluir"></strong>?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        Esta ação não pode ser desfeita. O produto será removido permanentemente do cardápio.
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 25px;">
                        <i class="fas fa-times me-1"></i>
                        Cancelar
                    </button>
                    <form id="formExcluir" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="border-radius: 25px;">
                            <i class="fas fa-trash me-1"></i>
                            Excluir Produto
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
            
            // Animation on load
            const items = document.querySelectorAll('.product-item');
            items.forEach((item, index) => {
                setTimeout(() => {
                    item.style.opacity = '1';
                }, index * 100);
            });
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            filterProducts();
        });

        document.getElementById('categoriaFilter').addEventListener('change', function() {
            filterProducts();
        });

        document.getElementById('statusFilter').addEventListener('change', function() {
            filterProducts();
        });

        function filterProducts() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const categoriaFilter = document.getElementById('categoriaFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;
            const items = document.querySelectorAll('.product-item');
            let visibleCount = 0;

            items.forEach(item => {
                const nome = item.dataset.nome;
                const categoria = item.dataset.categoria;
                const status = item.dataset.status;

                let show = true;

                if (searchTerm && !nome.includes(searchTerm)) {
                    show = false;
                }

                if (categoriaFilter && categoria !== categoriaFilter) {
                    show = false;
                }

                if (statusFilter && status !== statusFilter) {
                    show = false;
                }

                if (show) {
                    item.style.display = 'flex';
                    item.classList.add('fade-in');
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                    item.classList.remove('fade-in');
                }
            });

            document.getElementById('totalProdutos').textContent = visibleCount;
        }

        function limparFiltros() {
            document.getElementById('searchInput').value = '';
            document.getElementById('categoriaFilter').value = '';
            document.getElementById('statusFilter').value = '';
            filterProducts();
        }

        function confirmarExclusao(id, nome) {
            document.getElementById('nomeProdutoExcluir').textContent = nome;
            document.getElementById('formExcluir').action = `/produtos/${id}`;
            new bootstrap.Modal(document.getElementById('modalExcluir')).show();
        }

        async function toggleStatus(id) {
            const item = document.querySelector(`[data-produto-id="${id}"]`);
            if (item) item.classList.add('loading');

            try {
                const response = await fetch(`/produtos/${id}/toggle-status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    setTimeout(() => {
                        location.reload();
                    }, 300);
                } else {
                    alert('Erro ao alterar status do produto.');
                }
            } catch (error) {
                alert('Erro ao alterar status do produto.');
            } finally {
                if (item) item.classList.remove('loading');
            }
        }

        // Auto-hide alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>
