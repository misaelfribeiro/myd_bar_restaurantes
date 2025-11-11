<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mesas - Sistema Bar/Restaurante</title>
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
        
        .mesas-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        
        .mesas-header {
            background: linear-gradient(135deg, #20c997, #17a085);
            color: white;
            padding: 20px;
            margin: 0;
        }
        
        .mesa-item {
            padding: 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .mesa-item:hover {
            background: rgba(102, 126, 234, 0.05);
            transform: translateX(5px);
        }
        
        .mesa-item:last-child {
            border-bottom: none;
        }
        
        .mesa-info {
            flex: 1;
            min-width: 200px;
        }
        
        .mesa-identificador {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        
        .mesa-meta {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .mesa-lugares {
            font-size: 1.1rem;
            color: #6c757d;
        }
        
        .status-badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            color: white;
        }
        
        .status-livre {
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        
        .status-ocupada {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }
        
        .mesa-actions {
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
        
        .mesa-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #20c997, #17a085);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .stats-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
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
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            .mesa-item {
                flex-direction: column;
                align-items: flex-start;
            }
            .mesa-actions {
                width: 100%;
                justify-content: center;
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
                <i class="fas fa-chair me-3"></i>
                Gerenciamento de Mesas
            </h1>
            <p class="mb-4">Gerencie as mesas do seu restaurante de forma eficiente</p>
            <a href="{{ route('mesas.create') }}" class="btn btn-new">
                <i class="fas fa-plus me-2"></i>
                Adicionar Nova Mesa
            </a>
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

        <!-- Stats -->
        <div class="stats-section">
            <div class="row">
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number">{{ $mesas->count() }}</div>
                        <small class="text-muted">Total de Mesas</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number text-success">{{ $mesas->where('pedidos_count', 0)->count() }}</div>
                        <small class="text-muted">Mesas Livres</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number text-danger">{{ $mesas->where('pedidos_count', '>', 0)->count() }}</div>
                        <small class="text-muted">Mesas Ocupadas</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mesas List -->
        <div class="mesas-container">
            <div class="mesas-header">
                <h3 class="mb-0">
                    <i class="fas fa-list-ul me-2"></i>
                    Lista de Mesas
                    <span class="float-end">
                        <small>{{ $mesas->count() }} {{ $mesas->count() == 1 ? 'mesa' : 'mesas' }}</small>
                    </span>
                </h3>
            </div>
            
            @if($mesas->count() > 0)
                @foreach($mesas as $mesa)
                    <div class="mesa-item">
                        <!-- Mesa Icon -->
                        <div class="mesa-icon">
                            <i class="fas fa-chair"></i>
                        </div>
                        
                        <!-- Mesa Info -->
                        <div class="mesa-info">
                            <div class="mesa-identificador">Mesa {{ $mesa->identificador }}</div>
                            <div class="mesa-meta">
                                <div class="mesa-lugares">
                                    <i class="fas fa-users me-1"></i>
                                    {{ $mesa->lugares }} {{ $mesa->lugares == 1 ? 'lugar' : 'lugares' }}
                                </div>
                                @if($mesa->pedidos->count() > 0)
                                    <span class="status-badge status-ocupada">
                                        <i class="fas fa-times-circle me-1"></i>
                                        Ocupada ({{ $mesa->pedidos->count() }} {{ $mesa->pedidos->count() == 1 ? 'pedido' : 'pedidos' }})
                                    </span>
                                @else
                                    <span class="status-badge status-livre">
                                        <i class="fas fa-check-circle me-1"></i>
                                        Livre
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="mesa-actions">
                            <a href="{{ route('mesas.show', $mesa->id) }}" 
                               class="btn-action btn-view" 
                               title="Visualizar"
                               data-bs-toggle="tooltip">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('mesas.edit', $mesa->id) }}" 
                               class="btn-action btn-edit" 
                               title="Editar"
                               data-bs-toggle="tooltip">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn-action btn-delete" 
                                    onclick="confirmarExclusao({{ $mesa->id }}, '{{ $mesa->identificador }}')" 
                                    title="Excluir"
                                    data-bs-toggle="tooltip">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            @else
                <div style="text-align: center; padding: 60px 20px; color: #6c757d;">
                    <i class="fas fa-chair" style="font-size: 5rem; margin-bottom: 30px; opacity: 0.3;"></i>
                    <h3>Nenhuma mesa cadastrada</h3>
                    <p>Comece adicionando mesas ao seu restaurante!</p>
                    <a href="{{ route('mesas.create') }}" class="btn btn-new">
                        <i class="fas fa-plus me-2"></i>
                        Adicionar Primeira Mesa
                    </a>
                </div>
            @endif
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
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });

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
