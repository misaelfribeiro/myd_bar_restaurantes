<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pedido #{{ $pedido->id }} - Sistema Bar/Restaurante</title>
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
        
        .status-badge {
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-pendente { background: #fff3cd; color: #856404; }
        .status-em_preparo { background: #d1ecf1; color: #0c5460; }
        .status-pronto { background: #d4edda; color: #155724; }
        .status-entregue { background: #e2e3e5; color: #6c757d; }
        .status-cancelado { background: #f8d7da; color: #721c24; }
        
        .info-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
        }
        
        .icon-mesa { background: linear-gradient(135deg, #06b6d4, #0891b2); }
        .icon-garcom { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        .icon-data { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .icon-total { background: linear-gradient(135deg, #10b981, #059669); }
        
        .item-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            border: 1px solid #e9ecef;
            transition: transform 0.3s ease;
        }
        
        .item-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
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
        
        .total-summary {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            margin-top: 20px;
        }
        
        .total-amount {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .item-image {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            object-fit: cover;
            margin-right: 15px;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-name {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: #1f2937;
        }
        
        .item-price {
            color: #059669;
            font-weight: 600;
        }
        
        .item-quantity {
            background: #6366f1;
            color: white;
            border-radius: 20px;
            padding: 5px 12px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .observacoes-box {
            background: #fffbeb;
            border: 1px solid #fbbf24;
            border-radius: 10px;
            padding: 15px;
            margin-top: 10px;
        }
        
        .timeline-status {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 30px 0;
            position: relative;
        }
        
        .timeline-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            flex: 1;
        }
        
        .timeline-step::after {
            content: '';
            position: absolute;
            top: 20px;
            right: -50%;
            width: 100%;
            height: 2px;
            background: #e5e7eb;
            z-index: -1;
        }
        
        .timeline-step:last-child::after {
            display: none;
        }
        
        .timeline-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }
        
        .timeline-active {
            background: #10b981;
            color: white;
        }
        
        .timeline-inactive {
            background: #e5e7eb;
            color: #9ca3af;
        }
        
        @media (max-width: 768px) {
            .detail-card { padding: 20px; }
            .total-amount { font-size: 2rem; }
            .timeline-status { flex-direction: column; gap: 20px; }
            .timeline-step::after { display: none; }
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
                <a class="nav-link" href="{{ route('pedidos.index') }}">
                    <i class="fas fa-receipt me-1"></i>Pedidos
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Hero Section -->
        <div class="hero-section">
            <h1>
                <i class="fas fa-receipt me-3"></i>
                Pedido #{{ $pedido->id }}
            </h1>
            <p class="lead mb-0">
                Detalhes completos do pedido
            </p>
        </div>

        <!-- Status Timeline -->
        <div class="detail-card">
            <h5 class="mb-4">
                <i class="fas fa-clock me-2"></i>
                Status do Pedido
            </h5>
            <div class="timeline-status">
                <div class="timeline-step">
                    <div class="timeline-icon {{ in_array($pedido->status, ['pendente', 'em_preparo', 'pronto', 'entregue']) ? 'timeline-active' : 'timeline-inactive' }}">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <small class="text-muted">Pendente</small>
                </div>
                <div class="timeline-step">
                    <div class="timeline-icon {{ in_array($pedido->status, ['em_preparo', 'pronto', 'entregue']) ? 'timeline-active' : 'timeline-inactive' }}">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <small class="text-muted">Em Preparo</small>
                </div>
                <div class="timeline-step">
                    <div class="timeline-icon {{ in_array($pedido->status, ['pronto', 'entregue']) ? 'timeline-active' : 'timeline-inactive' }}">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <small class="text-muted">Pronto</small>
                </div>
                <div class="timeline-step">
                    <div class="timeline-icon {{ $pedido->status === 'entregue' ? 'timeline-active' : 'timeline-inactive' }}">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <small class="text-muted">Entregue</small>
                </div>
            </div>
            <div class="text-center mt-3">
                <span class="status-badge status-{{ $pedido->status }}">
                    {{ ucfirst(str_replace('_', ' ', $pedido->status)) }}
                </span>
            </div>
        </div>

        <!-- Informações do Pedido -->
        <div class="detail-card">
            <h5 class="mb-4">
                <i class="fas fa-info-circle me-2"></i>
                Informações do Pedido
            </h5>
            
            <div class="info-item">
                <div class="info-icon icon-mesa">
                    <i class="fas fa-table"></i>
                </div>
                <div>
                    <strong>Mesa</strong>
                    <div class="text-muted">{{ $pedido->mesa->identificador }} ({{ $pedido->mesa->lugares }} lugares)</div>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon icon-garcom">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <strong>Garçom Responsável</strong>
                    <div class="text-muted">{{ $pedido->usuario->nome }}</div>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon icon-data">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <strong>Data e Hora</strong>
                    <div class="text-muted">{{ $pedido->created_at->format('d/m/Y \à\s H:i') }}</div>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon icon-total">
                    <i class="fas fa-calculator"></i>
                </div>
                <div>
                    <strong>Total do Pedido</strong>
                    <div class="text-success fw-bold">R$ {{ number_format($pedido->total, 2, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Itens do Pedido -->
        <div class="detail-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Itens do Pedido ({{ $pedido->itens->count() }} itens)
                </h5>
                <a href="{{ route('pedidos.detalhes', $pedido) }}" class="btn btn-outline-gradient btn-sm">
                    <i class="fas fa-plus me-1"></i>
                    Gerenciar Itens
                </a>
            </div>

            @forelse($pedido->itens as $item)
                <div class="item-card">
                    <div class="d-flex align-items-center">
                        @if($item->produto->imagem)
                            <img src="{{ asset('storage/' . $item->produto->imagem) }}" 
                                 alt="{{ $item->produto->nome }}" class="item-image">
                        @else
                            <div class="item-image bg-light d-flex align-items-center justify-content-center">
                                <i class="fas fa-utensils text-muted"></i>
                            </div>
                        @endif
                        
                        <div class="item-details">
                            <div class="item-name">{{ $item->produto->nome }}</div>
                            <div class="text-muted small mb-2">{{ $item->produto->categoria->nome ?? 'Sem categoria' }}</div>
                            
                            @if($item->observacoes)
                                <div class="observacoes-box">
                                    <small>
                                        <i class="fas fa-sticky-note me-1"></i>
                                        <strong>Observações:</strong> {{ $item->observacoes }}
                                    </small>
                                </div>
                            @endif
                        </div>
                        
                        <div class="text-end">
                            <div class="item-quantity">{{ $item->quantidade }}x</div>
                            <div class="item-price mt-2">
                                R$ {{ number_format($item->preco_unitario, 2, ',', '.') }} cada
                            </div>
                            <div class="fw-bold text-success">
                                R$ {{ number_format($item->subtotal, 2, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-shopping-cart fa-2x mb-3"></i>
                    <p>Nenhum item adicionado ao pedido ainda</p>
                    <a href="{{ route('pedidos.detalhes', $pedido) }}" class="btn btn-gradient">
                        <i class="fas fa-plus me-2"></i>
                        Adicionar Itens
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Total Summary -->
        @if($pedido->itens->count() > 0)
            <div class="total-summary">
                <div class="total-amount">
                    R$ {{ number_format($pedido->total, 2, ',', '.') }}
                </div>
                <div>Total do Pedido</div>
                <small class="opacity-75">
                    {{ $pedido->itens->count() }} item{{ $pedido->itens->count() > 1 ? 'ns' : '' }}
                </small>
            </div>
        @endif

        <!-- Ações -->
        <div class="detail-card">
            <div class="row g-3">
                <div class="col-md-3">
                    <a href="{{ route('pedidos.index') }}" class="btn btn-outline-gradient w-100">
                        <i class="fas fa-arrow-left me-2"></i>
                        Voltar à Lista
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('pedidos.edit', $pedido) }}" class="btn btn-gradient w-100">
                        <i class="fas fa-edit me-2"></i>
                        Editar Pedido
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('pedidos.detalhes', $pedido) }}" class="btn btn-gradient w-100">
                        <i class="fas fa-list me-2"></i>
                        Gerenciar Itens
                    </a>
                </div>
                <div class="col-md-3">
                    <button onclick="deletePedido()" class="btn btn-outline-danger w-100">
                        <i class="fas fa-trash me-2"></i>
                        Excluir Pedido
                    </button>
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
                    <p>Tem certeza que deseja excluir o Pedido #{{ $pedido->id }}?</p>
                    <p class="text-danger">
                        <i class="fas fa-warning me-1"></i>
                        Esta ação não pode ser desfeita e todos os itens serão removidos.
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
        function deletePedido() {
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        document.getElementById('confirmDelete').addEventListener('click', async function() {
            try {
                const response = await fetch(`/pedidos/{{ $pedido->id }}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    window.location.href = '/pedidos';
                } else {
                    alert('Erro ao excluir pedido');
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao excluir pedido');
            }

            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
        });

        // Animação dos cartões ao carregar
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.item-card');
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
