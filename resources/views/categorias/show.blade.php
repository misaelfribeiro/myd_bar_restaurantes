<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $categoria->nome }} - Sistema Bar/Restaurante</title>
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
        
        .detail-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .categoria-header {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid rgba(99, 102, 241, 0.1);
        }
        
        .categoria-icon-large {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            margin-right: 20px;
        }
        
        .categoria-info h1 {
            margin: 0;
            font-size: 2.2rem;
            font-weight: 700;
            color: #1f2937;
        }
        
        .categoria-desc {
            color: #6b7280;
            font-size: 1.1rem;
            margin: 10px 0 0 0;
        }
        
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 25px 0;
        }
        
        .stat-box {
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .produtos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 25px;
        }
        
        .produto-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .produto-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-color: #6366f1;
        }
        
        .produto-image {
            width: 100%;
            height: 120px;
            border-radius: 10px;
            object-fit: cover;
            margin-bottom: 15px;
        }
        
        .produto-placeholder {
            width: 100%;
            height: 120px;
            border-radius: 10px;
            background: linear-gradient(135deg, #e5e7eb, #d1d5db);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            color: #6b7280;
            font-size: 1.5rem;
        }
        
        .produto-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
        }
        
        .produto-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: #059669;
            margin-bottom: 10px;
        }
        
        .produto-status {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 4px 8px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-ativo { background: #dcfce7; color: #166534; }
        .status-inativo { background: #fee2e2; color: #991b1b; }
        
        .produto-actions {
            display: flex;
            gap: 5px;
            justify-content: flex-end;
        }
        
        .btn-sm-custom {
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-view { background: linear-gradient(135deg, #06b6d4, #0891b2); color: white; }
        .btn-edit { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
        
        .btn-gradient {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
            color: white;
        }
        
        .btn-outline-gradient {
            background: transparent;
            border: 2px solid #6366f1;
            color: #6366f1;
            font-weight: 600;
            padding: 10px 22px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-outline-gradient:hover {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            transform: translateY(-2px);
        }
        
        .empty-produtos {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
            grid-column: 1 / -1;
        }
        
        .empty-produtos i {
            font-size: 3rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .categoria-meta {
            background: rgba(99, 102, 241, 0.05);
            border-radius: 12px;
            padding: 15px;
            margin-top: 20px;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            font-size: 0.9rem;
            color: #6b7280;
        }
        
        .meta-item:last-child {
            margin-bottom: 0;
        }
        
        .meta-item i {
            margin-right: 8px;
            width: 16px;
        }
        
        @media (max-width: 768px) {
            .categoria-header {
                flex-direction: column;
                text-align: center;
            }
            
            .categoria-icon-large {
                margin-right: 0;
                margin-bottom: 15px;
            }
            
            .categoria-info h1 {
                font-size: 1.8rem;
            }
            
            .stats-row {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .produtos-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-glass">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-utensils me-2"></i>
                Sistema Bar/Restaurante
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-home me-1"></i>Dashboard
                </a>
                <a class="nav-link" href="{{ route('categorias.index') }}">
                    <i class="fas fa-tags me-1"></i>Categorias
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Hero Section -->
        <div class="hero-section">
            <h1>
                <i class="fas fa-tag me-3"></i>
                Detalhes da Categoria
            </h1>
            <p class="lead mb-0">
                Visualize todos os produtos desta categoria
            </p>
        </div>

        <!-- Cabeçalho da Categoria -->
        <div class="detail-card">
            <div class="categoria-header">
                <div class="categoria-icon-large">
                    <i class="fas fa-tag"></i>
                </div>
                <div class="categoria-info">
                    <h1>{{ $categoria->nome }}</h1>
                    @if($categoria->descricao)
                        <p class="categoria-desc">{{ $categoria->descricao }}</p>
                    @else
                        <p class="categoria-desc text-muted fst-italic">Esta categoria não possui descrição</p>
                    @endif
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-number text-primary">{{ $categoria->produtos->count() }}</div>
                    <div class="text-muted">Total de Produtos</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number text-success">{{ $categoria->produtos->where('ativo', true)->count() }}</div>
                    <div class="text-muted">Produtos Ativos</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number text-warning">{{ $categoria->produtos->where('ativo', false)->count() }}</div>
                    <div class="text-muted">Produtos Inativos</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number text-info">
                        @if($categoria->produtos->count() > 0)
                            R$ {{ number_format($categoria->produtos->avg('preco'), 2, ',', '.') }}
                        @else
                            R$ 0,00
                        @endif
                    </div>
                    <div class="text-muted">Preço Médio</div>
                </div>
            </div>

            <!-- Meta Informações -->
            <div class="categoria-meta">
                <div class="meta-item">
                    <i class="fas fa-calendar-plus"></i>
                    <span>Criado em {{ $categoria->created_at->format('d/m/Y \à\s H:i') }}</span>
                </div>
                @if($categoria->updated_at != $categoria->created_at)
                    <div class="meta-item">
                        <i class="fas fa-calendar-edit"></i>
                        <span>Última atualização em {{ $categoria->updated_at->format('d/m/Y \à\s H:i') }}</span>
                    </div>
                @endif
                <div class="meta-item">
                    <i class="fas fa-hashtag"></i>
                    <span>ID da categoria: {{ $categoria->id }}</span>
                </div>
            </div>
        </div>

        <!-- Produtos da Categoria -->
        <div class="detail-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">
                    <i class="fas fa-box me-2"></i>
                    Produtos desta Categoria
                </h5>
                <a href="{{ route('produtos.create') }}" class="btn btn-outline-gradient btn-sm">
                    <i class="fas fa-plus me-1"></i>
                    Adicionar Produto
                </a>
            </div>

            <div class="produtos-grid">
                @forelse($categoria->produtos as $produto)
                    <div class="produto-card">
                        <div class="produto-status status-{{ $produto->ativo ? 'ativo' : 'inativo' }}">
                            {{ $produto->ativo ? 'Ativo' : 'Inativo' }}
                        </div>
                        
                        @if($produto->imagem)
                            <img src="{{ asset('storage/' . $produto->imagem) }}" 
                                 alt="{{ $produto->nome }}" class="produto-image">
                        @else
                            <div class="produto-placeholder">
                                <i class="fas fa-image"></i>
                            </div>
                        @endif
                        
                        <div class="produto-name">{{ $produto->nome }}</div>
                        <div class="produto-price">R$ {{ number_format($produto->preco, 2, ',', '.') }}</div>
                        
                        @if($produto->descricao)
                            <p class="text-muted small mb-2">{{ Str::limit($produto->descricao, 80) }}</p>
                        @endif
                        
                        <div class="produto-actions">
                            <a href="{{ route('produtos.show', $produto) }}" 
                               class="btn btn-view btn-sm-custom" 
                               title="Visualizar">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('produtos.edit', $produto) }}" 
                               class="btn btn-edit btn-sm-custom" 
                               title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="empty-produtos">
                        <i class="fas fa-box-open"></i>
                        <h4>Nenhum produto nesta categoria</h4>
                        <p>Esta categoria ainda não possui produtos cadastrados</p>
                        <a href="{{ route('produtos.create') }}" class="btn btn-gradient mt-3">
                            <i class="fas fa-plus me-2"></i>
                            Adicionar Primeiro Produto
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Ações -->
        <div class="detail-card">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="mb-3">
                        <i class="fas fa-cogs me-2"></i>
                        Ações da Categoria
                    </h6>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('categorias.index') }}" class="btn btn-outline-gradient">
                            <i class="fas fa-arrow-left me-2"></i>
                            Voltar à Lista
                        </a>
                        <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-gradient">
                            <i class="fas fa-edit me-2"></i>
                            Editar Categoria
                        </a>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h6 class="mb-3">
                        <i class="fas fa-tools me-2"></i>
                        Ações Rápidas
                    </h6>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('produtos.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-1"></i>
                            Novo Produto
                        </a>
                        @if($categoria->produtos->count() == 0)
                            <button onclick="deleteCategoria()" class="btn btn-outline-danger">
                                <i class="fas fa-trash me-1"></i>
                                Excluir Categoria
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Confirmar Exclusão
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir a categoria "{{ $categoria->nome }}"?</p>
                    <p class="text-danger">
                        <i class="fas fa-warning me-1"></i>
                        Esta ação não pode ser desfeita.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">
                        <i class="fas fa-trash me-1"></i>
                        Excluir Categoria
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteCategoria() {
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        document.getElementById('confirmDelete').addEventListener('click', async function() {
            try {
                const response = await fetch(`/categorias/{{ $categoria->id }}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    window.location.href = '/categorias';
                } else {
                    const data = await response.json();
                    alert('Erro ao excluir categoria: ' + (data.error || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao excluir categoria');
            }

            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
        });

        // Animação dos cartões ao carregar
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.produto-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Animação das estatísticas
            const stats = document.querySelectorAll('.stat-number');
            stats.forEach((stat, index) => {
                setTimeout(() => {
                    stat.style.transform = 'scale(1.1)';
                    setTimeout(() => {
                        stat.style.transform = 'scale(1)';
                    }, 200);
                }, index * 150);
            });
        });
    </script>
</body>
</html>
