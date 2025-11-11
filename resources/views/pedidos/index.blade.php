<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pedidos - Sistema Bar/Restaurante</title>
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
        
        .pedidos-list {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .pedidos-header {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            padding: 20px 25px;
            margin: 0;
            font-weight: 600;
        }
        
        .pedido-item {
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            padding: 20px 25px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .pedido-item:hover {
            background: rgba(99, 102, 241, 0.05);
            transform: translateX(5px);
        }
        
        .pedido-item:last-child {
            border-bottom: none;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-pendente { background: #fff3cd; color: #856404; }
        .status-em_preparo { background: #d1ecf1; color: #0c5460; }
        .status-pronto { background: #d4edda; color: #155724; }
        .status-entregue { background: #e2e3e5; color: #6c757d; }
        .status-cancelado { background: #f8d7da; color: #721c24; }
        
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
        
        .action-btns {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        
        .btn-sm-custom {
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.875rem;
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-view { background: linear-gradient(135deg, #06b6d4, #0891b2); color: white; }
        .btn-edit { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
        .btn-delete { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
        
        .btn-view:hover, .btn-edit:hover, .btn-delete:hover {
            transform: translateY(-1px);
            color: white;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .pedido-info {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .pedido-meta {
            display: flex;
            gap: 20px;
            align-items: center;
            font-size: 0.9rem;
            color: #6b7280;
        }
        
        .pedido-total {
            font-size: 1.2rem;
            font-weight: 700;
            color: #059669;
        }
        
        @media (max-width: 768px) {
            .hero-title { font-size: 2rem; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .action-btns { flex-direction: column; }
            .pedido-meta { flex-direction: column; align-items: flex-start; gap: 5px; }
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
                <i class="fas fa-receipt me-3"></i>
                Gestão de Pedidos
            </h1>
            <p class="lead mb-0">
                Gerencie todos os pedidos do seu estabelecimento de forma eficiente
            </p>
        </div>

        <!-- Estatísticas -->
        <div class="stats-grid" id="statsGrid">
            <div class="stat-card">
                <div class="stat-number text-warning" id="totalPedidos">{{ count($pedidos) }}</div>
                <div class="text-muted">Total de Pedidos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number text-info" id="pendentes">
                    {{ $pedidos->where('status', 'pendente')->count() }}
                </div>
                <div class="text-muted">Pendentes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number text-primary" id="emPreparo">
                    {{ $pedidos->where('status', 'em_preparo')->count() }}
                </div>
                <div class="text-muted">Em Preparo</div>
            </div>
            <div class="stat-card">
                <div class="stat-number text-success" id="entregues">
                    {{ $pedidos->where('status', 'entregue')->count() }}
                </div>
                <div class="text-muted">Entregues</div>
            </div>
        </div>

        <!-- Controles -->
        <div class="controls-card">
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control search-box" id="searchInput" 
                               placeholder="Buscar por mesa, garçom ou número do pedido...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select search-box" id="statusFilter">
                        <option value="">Todos os status</option>
                        <option value="pendente">Pendente</option>
                        <option value="em_preparo">Em Preparo</option>
                        <option value="pronto">Pronto</option>
                        <option value="entregue">Entregue</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select search-box" id="sortBy">
                        <option value="created_at_desc">Mais Recentes</option>
                        <option value="created_at_asc">Mais Antigos</option>
                        <option value="total_desc">Maior Valor</option>
                        <option value="total_asc">Menor Valor</option>
                    </select>
                </div>
                <div class="col-md-3 text-end">
                    <a href="{{ route('pedidos.create') }}" class="btn btn-gradient">
                        <i class="fas fa-plus me-2"></i>
                        Novo Pedido
                    </a>
                </div>
            </div>
        </div>

        <!-- Lista de Pedidos -->
        <div class="pedidos-list">
            <div class="pedidos-header">
                <i class="fas fa-list me-2"></i>
                Lista de Pedidos
            </div>
            <div id="pedidosContainer">
                @forelse($pedidos as $pedido)
                    <div class="pedido-item" data-search="{{ strtolower($pedido->mesa->identificador . ' ' . $pedido->usuario->nome . ' ' . $pedido->id) }}" 
                         data-status="{{ $pedido->status }}" data-total="{{ $pedido->total }}" data-date="{{ $pedido->created_at }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="pedido-info flex-grow-1">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <h5 class="mb-0">
                                        <i class="fas fa-hashtag me-1"></i>
                                        Pedido #{{ $pedido->id }}
                                    </h5>
                                    <span class="status-badge status-{{ $pedido->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $pedido->status)) }}
                                    </span>
                                </div>
                                
                                <div class="pedido-meta">
                                    <span>
                                        <i class="fas fa-table me-1"></i>
                                        Mesa {{ $pedido->mesa->identificador }}
                                    </span>
                                    <span>
                                        <i class="fas fa-user me-1"></i>
                                        {{ $pedido->usuario->nome }}
                                    </span>
                                    <span>
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $pedido->created_at->format('d/m/Y H:i') }}
                                    </span>
                                    <span>
                                        <i class="fas fa-shopping-cart me-1"></i>
                                        {{ $pedido->itens->count() }} itens
                                    </span>
                                </div>
                                
                                <div class="mt-2">
                                    <span class="pedido-total">
                                        <i class="fas fa-dollar-sign me-1"></i>
                                        R$ {{ number_format($pedido->total, 2, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="action-btns">
                                <a href="{{ route('pedidos.show', $pedido) }}" 
                                   class="btn btn-view btn-sm-custom" 
                                   title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('pedidos.edit', $pedido) }}" 
                                   class="btn btn-edit btn-sm-custom" 
                                   title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deletePedido({{ $pedido->id }})" 
                                        class="btn btn-delete btn-sm-custom" 
                                        title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fas fa-receipt"></i>
                        <h3>Nenhum pedido encontrado</h3>
                        <p>Comece criando seu primeiro pedido</p>
                        <a href="{{ route('pedidos.create') }}" class="btn btn-gradient mt-3">
                            <i class="fas fa-plus me-2"></i>
                            Criar Primeiro Pedido
                        </a>
                    </div>
                @endforelse
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
                    <p>Tem certeza que deseja excluir este pedido?</p>
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
                        Excluir Pedido
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        let pedidoToDelete = null;

        // Filtros e busca
        document.getElementById('searchInput').addEventListener('input', filterPedidos);
        document.getElementById('statusFilter').addEventListener('change', filterPedidos);
        document.getElementById('sortBy').addEventListener('change', sortPedidos);

        function filterPedidos() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value;
            const pedidos = document.querySelectorAll('.pedido-item');

            pedidos.forEach(pedido => {
                const searchData = pedido.getAttribute('data-search');
                const pedidoStatus = pedido.getAttribute('data-status');
                
                const matchesSearch = searchData.includes(searchTerm);
                const matchesStatus = !statusFilter || pedidoStatus === statusFilter;
                
                if (matchesSearch && matchesStatus) {
                    pedido.style.display = 'block';
                } else {
                    pedido.style.display = 'none';
                }
            });

            updateStats();
        }

        function sortPedidos() {
            const sortBy = document.getElementById('sortBy').value;
            const container = document.getElementById('pedidosContainer');
            const pedidos = Array.from(container.querySelectorAll('.pedido-item'));

            pedidos.sort((a, b) => {
                switch (sortBy) {
                    case 'created_at_asc':
                        return new Date(a.getAttribute('data-date')) - new Date(b.getAttribute('data-date'));
                    case 'created_at_desc':
                        return new Date(b.getAttribute('data-date')) - new Date(a.getAttribute('data-date'));
                    case 'total_asc':
                        return parseFloat(a.getAttribute('data-total')) - parseFloat(b.getAttribute('data-total'));
                    case 'total_desc':
                        return parseFloat(b.getAttribute('data-total')) - parseFloat(a.getAttribute('data-total'));
                    default:
                        return 0;
                }
            });

            pedidos.forEach(pedido => container.appendChild(pedido));
        }

        function updateStats() {
            const visiblePedidos = document.querySelectorAll('.pedido-item[style="display: block"], .pedido-item:not([style])');
            const pendentes = Array.from(visiblePedidos).filter(p => p.getAttribute('data-status') === 'pendente').length;
            const emPreparo = Array.from(visiblePedidos).filter(p => p.getAttribute('data-status') === 'em_preparo').length;
            const entregues = Array.from(visiblePedidos).filter(p => p.getAttribute('data-status') === 'entregue').length;

            document.getElementById('totalPedidos').textContent = visiblePedidos.length;
            document.getElementById('pendentes').textContent = pendentes;
            document.getElementById('emPreparo').textContent = emPreparo;
            document.getElementById('entregues').textContent = entregues;
        }

        function deletePedido(pedidoId) {
            pedidoToDelete = pedidoId;
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        document.getElementById('confirmDelete').addEventListener('click', async function() {
            if (!pedidoToDelete) return;

            try {
                const response = await fetch(`/pedidos/${pedidoToDelete}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    location.reload();
                } else {
                    alert('Erro ao excluir pedido');
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao excluir pedido');
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
        });
    </script>
</body>
</html>
