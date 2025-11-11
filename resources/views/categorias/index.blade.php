<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Categorias - Sistema Bar/Restaurante</title>
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
        
        .controls-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .categorias-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
            margin-top: 25px;
        }
        
        .categoria-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 25px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .categoria-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
        }
        
        .categoria-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(99, 102, 241, 0.2);
        }
        
        .categoria-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        
        .categoria-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }
        
        .categoria-desc {
            color: #6b7280;
            font-size: 0.95rem;
            margin-bottom: 15px;
            line-height: 1.4;
        }
        
        .produtos-count {
            background: rgba(99, 102, 241, 0.1);
            color: #6366f1;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .action-btns {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }
        
        .btn-sm-custom {
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.875rem;
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-view { 
            background: linear-gradient(135deg, #06b6d4, #0891b2); 
            color: white; 
        }
        
        .btn-edit { 
            background: linear-gradient(135deg, #f59e0b, #d97706); 
            color: white; 
        }
        
        .btn-delete { 
            background: linear-gradient(135deg, #ef4444, #dc2626); 
            color: white; 
        }
        
        .btn-view:hover, .btn-edit:hover, .btn-delete:hover {
            transform: translateY(-1px);
            color: white;
        }
        
        .search-box {
            border: 2px solid rgba(99, 102, 241, 0.2);
            border-radius: 15px;
            padding: 12px 20px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .search-box:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            outline: none;
        }
        
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
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
            grid-column: 1 / -1;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .produtos-preview {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .produto-tag {
            background: #f3f4f6;
            color: #374151;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            margin-right: 5px;
            margin-bottom: 5px;
            display: inline-block;
        }
        
        .categoria-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-size: 0.875rem;
            color: #6b7280;
        }
        
        @media (max-width: 768px) {
            .hero-title { font-size: 2rem; }
            .categorias-grid { grid-template-columns: 1fr; gap: 15px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .action-btns { justify-content: center; }
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
                <a class="nav-link" href="{{ route('produtos.index') }}">
                    <i class="fas fa-box me-1"></i>Produtos
                </a>
                <a class="nav-link" href="{{ route('pedidos.index') }}">
                    <i class="fas fa-receipt me-1"></i>Pedidos
                </a>
                <a class="nav-link" href="{{ route('mesas.index') }}">
                    <i class="fas fa-table me-1"></i>Mesas
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Hero Section -->
        <div class="hero-section">
            <h1 class="hero-title">
                <i class="fas fa-tags me-3"></i>
                Gestão de Categorias
            </h1>
            <p class="lead mb-0">
                Organize seus produtos por categorias para melhor gestão
            </p>
        </div>

        <!-- Estatísticas -->
        <div class="stats-grid" id="statsGrid">
            <div class="stat-card">
                <div class="stat-number text-primary" id="totalCategorias">{{ count($categorias) }}</div>
                <div class="text-muted">Total de Categorias</div>
            </div>
            <div class="stat-card">
                <div class="stat-number text-success" id="totalProdutos">
                    {{ $categorias->sum(function($categoria) { return $categoria->produtos->count(); }) }}
                </div>
                <div class="text-muted">Produtos Categorizados</div>
            </div>
            <div class="stat-card">
                <div class="stat-number text-warning" id="categoriasMaisUsadas">
                    {{ $categorias->where('produtos_count', '>', 0)->count() }}
                </div>
                <div class="text-muted">Com Produtos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number text-info" id="mediaProdutos">
                    @if(count($categorias) > 0)
                        {{ number_format($categorias->sum(function($categoria) { return $categoria->produtos->count(); }) / count($categorias), 1) }}
                    @else
                        0
                    @endif
                </div>
                <div class="text-muted">Média por Categoria</div>
            </div>
        </div>

        <!-- Controles -->
        <div class="controls-card">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control search-box" id="searchInput" 
                               placeholder="Buscar por nome da categoria...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select search-box" id="sortBy">
                        <option value="nome_asc">Nome A-Z</option>
                        <option value="nome_desc">Nome Z-A</option>
                        <option value="produtos_desc">Mais Produtos</option>
                        <option value="produtos_asc">Menos Produtos</option>
                    </select>
                </div>
                <div class="col-md-3 text-end">
                    <a href="{{ route('categorias.create') }}" class="btn btn-gradient">
                        <i class="fas fa-plus me-2"></i>
                        Nova Categoria
                    </a>
                </div>
            </div>
        </div>

        <!-- Grid de Categorias -->
        <div class="categorias-grid" id="categoriasContainer">
            @forelse($categorias as $categoria)
                <div class="categoria-card" data-search="{{ strtolower($categoria->nome . ' ' . ($categoria->descricao ?? '')) }}" 
                     data-produtos="{{ $categoria->produtos->count() }}">
                    <div class="categoria-icon">
                        <i class="fas fa-tag"></i>
                    </div>
                    
                    <div class="categoria-name">{{ $categoria->nome }}</div>
                    
                    @if($categoria->descricao)
                        <div class="categoria-desc">{{ $categoria->descricao }}</div>
                    @else
                        <div class="categoria-desc text-muted fst-italic">Sem descrição</div>
                    @endif
                    
                    <div class="produtos-count">
                        <i class="fas fa-box me-1"></i>
                        {{ $categoria->produtos->count() }} produto{{ $categoria->produtos->count() != 1 ? 's' : '' }}
                    </div>
                    
                    @if($categoria->produtos->count() > 0)
                        <div class="produtos-preview">
                            <small class="text-muted">Produtos:</small>
                            <div class="mt-1">
                                @foreach($categoria->produtos->take(3) as $produto)
                                    <span class="produto-tag">{{ $produto->nome }}</span>
                                @endforeach
                                @if($categoria->produtos->count() > 3)
                                    <span class="produto-tag">+{{ $categoria->produtos->count() - 3 }} mais</span>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    <div class="categoria-stats">
                        <small>
                            <i class="fas fa-calendar me-1"></i>
                            {{ $categoria->created_at->format('d/m/Y') }}
                        </small>
                        @if($categoria->updated_at != $categoria->created_at)
                            <small>
                                <i class="fas fa-edit me-1"></i>
                                Editado em {{ $categoria->updated_at->format('d/m/Y') }}
                            </small>
                        @endif
                    </div>
                    
                    <div class="action-btns">
                        <a href="{{ route('categorias.show', $categoria) }}" 
                           class="btn btn-view btn-sm-custom" 
                           title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('categorias.edit', $categoria) }}" 
                           class="btn btn-edit btn-sm-custom" 
                           title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>                        <button onclick="deleteCategoria({{ $categoria->id }}, {{ json_encode($categoria->nome) }}, {{ $categoria->produtos->count() }})" 
                                class="btn btn-delete btn-sm-custom" 
                                title="Excluir">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-tags"></i>
                    <h3>Nenhuma categoria encontrada</h3>
                    <p>Comece criando sua primeira categoria para organizar seus produtos</p>
                    <a href="{{ route('categorias.create') }}" class="btn btn-gradient mt-3">
                        <i class="fas fa-plus me-2"></i>
                        Criar Primeira Categoria
                    </a>
                </div>
            @endforelse
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
                    <p id="deleteMessage"></p>
                    <p id="warningMessage" class="text-danger" style="display: none;">
                        <i class="fas fa-warning me-1"></i>
                        Esta categoria possui produtos vinculados e não pode ser excluída.
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
        let categoriaToDelete = null;

        // Filtros e busca
        document.getElementById('searchInput').addEventListener('input', filterCategorias);
        document.getElementById('sortBy').addEventListener('change', sortCategorias);

        function filterCategorias() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const categorias = document.querySelectorAll('.categoria-card');

            categorias.forEach(categoria => {
                const searchData = categoria.getAttribute('data-search');
                
                if (searchData.includes(searchTerm)) {
                    categoria.style.display = 'block';
                } else {
                    categoria.style.display = 'none';
                }
            });

            updateStats();
        }

        function sortCategorias() {
            const sortBy = document.getElementById('sortBy').value;
            const container = document.getElementById('categoriasContainer');
            const categorias = Array.from(container.querySelectorAll('.categoria-card'));

            categorias.sort((a, b) => {
                const nomeA = a.querySelector('.categoria-name').textContent.toLowerCase();
                const nomeB = b.querySelector('.categoria-name').textContent.toLowerCase();
                const produtosA = parseInt(a.getAttribute('data-produtos'));
                const produtosB = parseInt(b.getAttribute('data-produtos'));

                switch (sortBy) {
                    case 'nome_asc':
                        return nomeA.localeCompare(nomeB);
                    case 'nome_desc':
                        return nomeB.localeCompare(nomeA);
                    case 'produtos_asc':
                        return produtosA - produtosB;
                    case 'produtos_desc':
                        return produtosB - produtosA;
                    default:
                        return 0;
                }
            });

            categorias.forEach(categoria => container.appendChild(categoria));
        }

        function updateStats() {
            const visibleCategorias = document.querySelectorAll('.categoria-card[style="display: block"], .categoria-card:not([style])');
            let totalProdutos = 0;
            let categoriasComProdutos = 0;

            visibleCategorias.forEach(categoria => {
                const produtos = parseInt(categoria.getAttribute('data-produtos'));
                totalProdutos += produtos;
                if (produtos > 0) categoriasComProdutos++;
            });

            document.getElementById('totalCategorias').textContent = visibleCategorias.length;
            document.getElementById('totalProdutos').textContent = totalProdutos;
            document.getElementById('categoriasMaisUsadas').textContent = categoriasComProdutos;
            
            const media = visibleCategorias.length > 0 ? (totalProdutos / visibleCategorias.length).toFixed(1) : '0';
            document.getElementById('mediaProdutos').textContent = media;
        }

        function deleteCategoria(categoriaId, nome, produtosCount) {
            categoriaToDelete = categoriaId;
            
            if (produtosCount > 0) {
                document.getElementById('deleteMessage').textContent = 
                    `A categoria "${nome}" possui ${produtosCount} produto(s) vinculado(s).`;
                document.getElementById('warningMessage').style.display = 'block';
                document.getElementById('confirmDelete').style.display = 'none';
            } else {
                document.getElementById('deleteMessage').textContent = 
                    `Tem certeza que deseja excluir a categoria "${nome}"?`;
                document.getElementById('warningMessage').style.display = 'none';
                document.getElementById('confirmDelete').style.display = 'block';
            }
            
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        document.getElementById('confirmDelete').addEventListener('click', async function() {
            if (!categoriaToDelete) return;

            try {
                const response = await fetch(`/categorias/${categoriaToDelete}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    location.reload();
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

        // Animar estatísticas ao carregar
        document.addEventListener('DOMContentLoaded', function() {
            const stats = document.querySelectorAll('.stat-number');
            stats.forEach((stat, index) => {
                setTimeout(() => {
                    stat.style.transform = 'scale(1.1)';
                    setTimeout(() => {
                        stat.style.transform = 'scale(1)';
                    }, 200);
                }, index * 100);
            });

            // Animar cards
            const cards = document.querySelectorAll('.categoria-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>
