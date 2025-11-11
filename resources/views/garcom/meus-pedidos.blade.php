<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>📋 Meus Pedidos - Modo Garçom</title>
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
        
        .stats-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            border-left: 4px solid;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card.pedidos { border-color: #6366f1; }
        .stat-card.valor { border-color: #10b981; }
        .stat-card.abertos { border-color: #f59e0b; }
        .stat-card.finalizados { border-color: #8b5cf6; }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 900;
            margin-bottom: 5px;
        }
        
        .stat-number.pedidos { color: #6366f1; }
        .stat-number.valor { color: #10b981; }
        .stat-number.abertos { color: #f59e0b; }
        .stat-number.finalizados { color: #8b5cf6; }
        
        .stat-label {
            font-size: 0.9rem;
            color: #6b7280;
            font-weight: 600;
        }
        
        .filters-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        
        .pedidos-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        
        .pedido-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .pedido-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
        }
        
        .pedido-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .pedido-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }
        
        .pedido-numero {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1f2937;
        }
        
        .pedido-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-aberto {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        
        .status-finalizado {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        
        .status-cancelado {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        .pedido-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .info-item {
            display: flex;
            align-items: center;
        }
        
        .info-icon {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-size: 0.9rem;
        }
        
        .icon-mesa { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; }
        .icon-tempo { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
        .icon-valor { background: linear-gradient(135deg, #10b981, #059669); color: white; }
        .icon-itens { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; }
        
        .info-text {
            flex: 1;
        }
        
        .info-label {
            font-size: 0.8rem;
            color: #6b7280;
            margin-bottom: 2px;
        }
        
        .info-value {
            font-weight: 600;
            color: #1f2937;
        }
        
        .pedido-itens {
            background: rgba(243, 244, 246, 0.5);
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 15px;
        }
          .item-lista {
            font-size: 0.9rem;
            color: #4b5563;
            line-height: 1.4;
        }
        
        .item-individual {
            padding: 8px 0;
            border-bottom: 1px solid rgba(229, 231, 235, 0.5);
        }
        
        .item-individual:last-child {
            border-bottom: none;
        }
        
        .item-produto {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4px;
        }
        
        .item-preco-unitario {
            color: #10b981;
            font-weight: 600;
            font-size: 0.85em;
        }
          .item-observacoes {
            background: rgba(139, 92, 246, 0.05);
            border: 1px solid rgba(139, 92, 246, 0.1);
            border-radius: 6px;
            padding: 6px 8px;
            margin-top: 4px;
            font-style: italic;
            font-size: 0.8em;
        }
        
        .item-observacoes i {
            color: #8b5cf6;
        }
        
        .pedido-observacoes-gerais {
            background: rgba(16, 185, 129, 0.05);
            border: 1px solid rgba(16, 185, 129, 0.1);
            border-radius: 8px;
            padding: 12px;
            margin-top: 10px;
        }
        
        .pedido-observacoes-gerais i {
            color: #10b981;
        }
        
        .pedido-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        
        .action-btn {
            padding: 8px 16px;
            border-radius: 8px;
            border: none;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-ver {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
        }
        
        .btn-editar {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            color: white;
        }
        
        .filter-btn {
            background: rgba(99, 102, 241, 0.1);
            color: #6366f1;
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 20px;
            padding: 8px 16px;
            margin: 5px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .filter-btn.active,
        .filter-btn:hover {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            transform: translateY(-2px);
        }
        
        .date-input {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 8px 12px;
            transition: border-color 0.3s ease;
        }
        
        .date-input:focus {
            border-color: #6366f1;
            outline: none;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }
        
        .pagination-wrapper {
            margin-top: 30px;
            display: flex;
            justify-content: center;
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
            .pedido-info {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            
            .pedido-actions {
                justify-content: center;
                flex-wrap: wrap;
            }
            
            .filters-section .row {
                text-align: center;
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
                        <a class="nav-link" href="{{ route('garcom.cardapio') }}">
                            <i class="fas fa-book me-1"></i> Cardápio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('garcom.mesas') }}">
                            <i class="fas fa-chair me-1"></i> Mesas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('garcom.meus-pedidos') }}">
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
                        <i class="fas fa-receipt me-2"></i>
                        Meus Pedidos
                    </h2>
                    <p class="mb-0">Acompanhe todos os seus pedidos e vendas</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('garcom.dashboard') }}" class="back-btn">
                        <i class="fas fa-arrow-left me-1"></i> Voltar
                    </a>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="stats-section">
            <h6 class="mb-3">📊 Estatísticas do Dia</h6>
            <div class="row">
                <div class="col-6 col-md-3">
                    <div class="stat-card pedidos">
                        <div class="stat-number pedidos">{{ $estatisticas['total_pedidos'] }}</div>
                        <div class="stat-label">Total Pedidos</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card valor">
                        <div class="stat-number valor">R$ {{ number_format($estatisticas['valor_total'], 2, ',', '.') }}</div>
                        <div class="stat-label">Valor Total</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card abertos">
                        <div class="stat-number abertos">{{ $estatisticas['pedidos_abertos'] }}</div>
                        <div class="stat-label">Em Andamento</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card finalizados">
                        <div class="stat-number finalizados">{{ $estatisticas['pedidos_finalizados'] }}</div>
                        <div class="stat-label">Finalizados</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filters-section">
            <h6 class="mb-3">🔍 Filtros</h6>
            <form method="GET" id="filter-form">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Data:</label>
                        <input type="date" 
                               class="form-control date-input" 
                               name="data" 
                               value="{{ request('data', today()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status:</label>
                        <select class="form-select date-input" name="status">
                            <option value="">Todos os Status</option>
                            <option value="aberto" {{ request('status') == 'aberto' ? 'selected' : '' }}>Em Andamento</option>
                            <option value="finalizado" {{ request('status') == 'finalizado' ? 'selected' : '' }}>Finalizados</option>
                            <option value="cancelado" {{ request('status') == 'cancelado' ? 'selected' : '' }}>Cancelados</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
              <!-- Filtros rápidos -->
            <div class="mt-3 text-center">
                <button class="filter-btn {{ !request('data') || request('data') == today()->format('Y-m-d') ? 'active' : '' }}" 
                        onclick="filtrarData('')">Hoje</button>
                <button class="filter-btn" onclick="filtrarData('{{ \Carbon\Carbon::today()->subDay()->format('Y-m-d') }}')">Ontem</button>
                <button class="filter-btn" onclick="filtrarData('{{ \Carbon\Carbon::today()->startOfWeek()->format('Y-m-d') }}')">Esta Semana</button>
                <button class="filter-btn" onclick="filtrarData('{{ \Carbon\Carbon::today()->startOfMonth()->format('Y-m-d') }}')">Este Mês</button>
            </div>
        </div>

        <!-- Pedidos -->
        <div class="pedidos-section">
            <h5 class="mb-4">
                <i class="fas fa-list me-2"></i>
                Lista de Pedidos
                @if(request('data'))
                    <small class="text-muted">- {{ \Carbon\Carbon::parse(request('data'))->format('d/m/Y') }}</small>
                @endif
            </h5>

            @forelse($pedidos as $pedido)
                <div class="pedido-card">
                    <div class="pedido-header">
                        <div class="pedido-numero">Pedido #{{ $pedido->id }}</div>
                        <div class="pedido-status status-{{ $pedido->status }}">
                            @if($pedido->status == 'aberto')
                                <i class="fas fa-clock me-1"></i> Em andamento
                            @elseif($pedido->status == 'finalizado')
                                <i class="fas fa-check me-1"></i> Finalizado
                            @else
                                <i class="fas fa-times me-1"></i> Cancelado
                            @endif
                        </div>
                    </div>

                    <div class="pedido-info">
                        <div class="info-item">
                            <div class="info-icon icon-mesa">
                                <i class="fas fa-chair"></i>
                            </div>
                            <div class="info-text">
                                <div class="info-label">Mesa</div>
                                <div class="info-value">Mesa {{ $pedido->mesa->numero }}</div>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-icon icon-tempo">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="info-text">
                                <div class="info-label">Horário</div>
                                <div class="info-value">{{ $pedido->created_at->format('H:i') }}</div>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-icon icon-valor">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="info-text">
                                <div class="info-label">Valor Total</div>
                                <div class="info-value">R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}</div>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-icon icon-itens">
                                <i class="fas fa-utensils"></i>
                            </div>
                            <div class="info-text">
                                <div class="info-label">Itens</div>
                                <div class="info-value">{{ $pedido->itens->count() }} {{ $pedido->itens->count() == 1 ? 'item' : 'itens' }}</div>
                            </div>
                        </div>
                    </div>                    @if($pedido->itens->count() > 0)
                        <div class="pedido-itens">
                            <div class="item-lista">
                                <strong>Produtos:</strong><br>
                                @foreach($pedido->itens as $item)
                                    <div class="item-individual mb-2">
                                        <span class="item-produto">
                                            <strong>{{ $item->quantidade }}x {{ $item->produto->nome }}</strong>
                                            <span class="item-preco-unitario">- R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</span>
                                        </span>
                                        @if($item->observacoes)
                                            <div class="item-observacoes">
                                                <i class="fas fa-comment-dots text-muted me-1"></i>
                                                <small class="text-muted">{{ $item->observacoes }}</small>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif                    @if($pedido->observacoes)
                        <div class="pedido-observacoes-gerais">
                            <strong><i class="fas fa-sticky-note me-1"></i>Observações do Pedido:</strong> {{ $pedido->observacoes }}
                        </div>
                    @endif

                    <div class="pedido-actions">
                        <a href="/pedidos/{{ $pedido->id }}" class="action-btn btn-ver">
                            <i class="fas fa-eye"></i> Ver Detalhes
                        </a>
                        @if($pedido->status == 'aberto')
                            <a href="/pedidos/{{ $pedido->id }}/edit" class="action-btn btn-editar">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-receipt fa-3x mb-3"></i>
                    <h5>Nenhum pedido encontrado</h5>
                    @if(request('data') || request('status'))
                        <p>Tente alterar os filtros ou selecionar outra data.</p>
                        <a href="{{ route('garcom.meus-pedidos') }}" class="btn btn-primary">
                            <i class="fas fa-refresh me-1"></i> Ver Todos
                        </a>
                    @else
                        <p>Você ainda não fez nenhum pedido hoje.</p>
                        <a href="{{ route('garcom.pedido-rapido') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Criar Primeiro Pedido
                        </a>
                    @endif
                </div>
            @endforelse

            <!-- Paginação -->
            @if($pedidos->hasPages())
                <div class="pagination-wrapper">
                    {{ $pedidos->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function filtrarData(data) {
            const form = document.getElementById('filter-form');
            const dataInput = form.querySelector('input[name="data"]');
            dataInput.value = data;
            form.submit();
        }

        // Auto submit on status change
        document.querySelector('select[name="status"]').addEventListener('change', function() {
            document.getElementById('filter-form').submit();
        });

        // Auto submit on date change
        document.querySelector('input[name="data"]').addEventListener('change', function() {
            document.getElementById('filter-form').submit();
        });

        // Animações de entrada
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.pedido-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateX(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>
