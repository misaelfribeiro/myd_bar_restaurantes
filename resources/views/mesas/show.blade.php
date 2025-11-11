<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mesa {{ $mesa->identificador }} - Sistema Bar/Restaurante</title>
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
        
        .detail-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .mesa-icon-large {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #20c997, #17a085);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 4rem;
            margin: 0 auto 30px;
            box-shadow: 0 15px 40px rgba(32, 201, 151, 0.3);
        }
        
        .info-item {
            background: rgba(102, 126, 234, 0.1);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 5px solid #667eea;
        }
        
        .info-label {
            font-size: 0.9rem;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        
        .info-value {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
        }
        
        .status-badge {
            padding: 12px 20px;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 600;
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .status-livre {
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        
        .status-ocupada {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }
        
        .btn-action {
            padding: 12px 25px;
            border-radius: 25px;
            border: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 150px;
            justify-content: center;
        }
        
        .btn-edit {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            color: white;
        }
        
        .btn-delete {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }
        
        .btn-back {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
        }
        
        .btn-action:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            color: white;
            text-decoration: none;
        }
        
        .pedidos-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }
        
        .pedido-item {
            background: rgba(102, 126, 234, 0.05);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
        }
        
        .pedido-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }
        
        .pedido-id {
            font-weight: 700;
            color: #333;
            font-size: 1.1rem;
        }
        
        .pedido-status {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            color: white;
        }
        
        .status-pendente {
            background: linear-gradient(135deg, #ffc107, #e0a800);
        }
        
        .status-em_preparo {
            background: linear-gradient(135deg, #17a2b8, #138496);
        }
        
        .status-pronto {
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        
        .status-entregue {
            background: linear-gradient(135deg, #6c757d, #495057);
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            .action-buttons {
                flex-direction: column;
            }
            .btn-action {
                min-width: 100%;
            }
            .pedido-header {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
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
                <a class="nav-link" href="{{ route('mesas.index') }}">
                    <i class="fas fa-list me-1"></i>Todas as Mesas
                </a>
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
                <i class="fas fa-chair me-3"></i>
                Mesa {{ $mesa->identificador }}
            </h1>
            <p class="mb-4">Visualização detalhada da mesa</p>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: rgba(40, 167, 69, 0.9); border: none; color: white; border-radius: 15px;">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Informações da Mesa -->
            <div class="col-lg-8">
                <div class="detail-card">
                    <!-- Ícone da Mesa -->
                    <div class="mesa-icon-large">
                        <i class="fas fa-chair"></i>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">Identificador</div>
                                <div class="info-value">{{ $mesa->identificador }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">Número de Lugares</div>
                                <div class="info-value">
                                    <i class="fas fa-users me-2"></i>
                                    {{ $mesa->lugares }} {{ $mesa->lugares == 1 ? 'lugar' : 'lugares' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">Status Atual</div>
                                <div class="info-value">
                                    @if($mesa->pedidos->count() > 0)
                                        <span class="status-badge status-ocupada">
                                            <i class="fas fa-times-circle"></i>
                                            Ocupada
                                        </span>
                                    @else
                                        <span class="status-badge status-livre">
                                            <i class="fas fa-check-circle"></i>
                                            Livre
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">Pedidos Ativos</div>
                                <div class="info-value">
                                    <i class="fas fa-clipboard-list me-2"></i>
                                    {{ $mesa->pedidos->count() }} {{ $mesa->pedidos->count() == 1 ? 'pedido' : 'pedidos' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">Criado em</div>
                                <div class="info-value">
                                    <i class="fas fa-calendar me-2"></i>
                                    {{ $mesa->created_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">Última Atualização</div>
                                <div class="info-value">
                                    <i class="fas fa-clock me-2"></i>
                                    {{ $mesa->updated_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ações Rápidas -->
            <div class="col-lg-4">
                <div class="detail-card">
                    <h4 class="text-center mb-4">
                        <i class="fas fa-cogs me-2"></i>
                        Ações Disponíveis
                    </h4>
                    
                    <div class="action-buttons">
                        <a href="{{ route('mesas.edit', $mesa->id) }}" class="btn-action btn-edit">
                            <i class="fas fa-edit"></i>
                            Editar Mesa
                        </a>
                        
                        <button class="btn-action btn-delete" onclick="confirmarExclusao({{ $mesa->id }}, '{{ $mesa->identificador }}')">
                            <i class="fas fa-trash"></i>
                            Excluir Mesa
                        </button>
                        
                        <a href="{{ route('mesas.index') }}" class="btn-action btn-back">
                            <i class="fas fa-arrow-left"></i>
                            Voltar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seção de Pedidos -->
        @if($mesa->pedidos->count() > 0)
            <div class="pedidos-section">
                <h3 class="mb-4">
                    <i class="fas fa-clipboard-list me-2"></i>
                    Pedidos Associados
                    <span class="badge bg-primary ms-2">{{ $mesa->pedidos->count() }}</span>
                </h3>
                
                @foreach($mesa->pedidos as $pedido)
                    <div class="pedido-item">
                        <div class="pedido-header">
                            <div class="pedido-id">
                                <i class="fas fa-hashtag me-1"></i>
                                Pedido #{{ $pedido->id }}
                            </div>
                            <span class="pedido-status status-{{ $pedido->status }}">
                                {{ ucfirst(str_replace('_', ' ', $pedido->status)) }}
                            </span>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Total:</strong> R$ {{ number_format($pedido->total, 2, ',', '.') }}
                            </div>
                            <div class="col-md-4">
                                <strong>Data:</strong> {{ $pedido->created_at->format('d/m/Y H:i') }}
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('pedidos.show', $pedido->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i>
                                    Ver Detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="pedidos-section">
                <div class="text-center py-5">
                    <i class="fas fa-clipboard" style="font-size: 4rem; color: #e9ecef; margin-bottom: 20px;"></i>
                    <h4 class="text-muted">Nenhum pedido associado</h4>
                    <p class="text-muted">Esta mesa não possui pedidos ativos no momento.</p>
                </div>
            </div>
        @endif
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
                    <p class="text-center">Tem certeza que deseja excluir a <strong>Mesa <span id="mesaIdentificadorExcluir"></span></strong>?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        Esta ação não pode ser desfeita. A mesa será removida permanentemente.
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
                            Excluir Mesa
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmarExclusao(id, identificador) {
            document.getElementById('mesaIdentificadorExcluir').textContent = identificador;
            document.getElementById('formExcluir').action = `/mesas/${id}`;
            new bootstrap.Modal(document.getElementById('modalExcluir')).show();
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
