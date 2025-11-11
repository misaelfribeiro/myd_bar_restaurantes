<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>🪑 Mesas - Modo Garçom</title>
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
        
        .mesas-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        
        .mesa-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border: 3px solid;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .mesa-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, transparent, currentColor, transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .mesa-card:hover::before {
            opacity: 1;
        }
        
        .mesa-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        .mesa-card.disponivel {
            border-color: #10b981;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.05), rgba(16, 185, 129, 0.02));
        }
        
        .mesa-card.ocupada {
            border-color: #ef4444;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.05), rgba(239, 68, 68, 0.02));
        }
        
        .mesa-card.manutencao {
            border-color: #f59e0b;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.05), rgba(245, 158, 11, 0.02));
        }
        
        .mesa-numero {
            font-size: 2.5rem;
            font-weight: 900;
            text-align: center;
            margin-bottom: 10px;
        }
        
        .mesa-numero.disponivel { color: #10b981; }
        .mesa-numero.ocupada { color: #ef4444; }
        .mesa-numero.manutencao { color: #f59e0b; }
        
        .mesa-status {
            text-align: center;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .mesa-status.disponivel { color: #10b981; }
        .mesa-status.ocupada { color: #ef4444; }
        .mesa-status.manutencao { color: #f59e0b; }
        
        .mesa-info {
            background: rgba(255, 255, 255, 0.7);
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
        }
        
        .pedido-info {
            border-left: 4px solid #6366f1;
            padding-left: 15px;
            margin: 10px 0;
        }
        
        .tempo-pedido {
            font-size: 0.85rem;
            color: #6b7280;
        }
        
        .valor-pedido {
            font-size: 1.2rem;
            font-weight: 700;
            color: #10b981;
        }
        
        .garcom-info {
            font-size: 0.9rem;
            color: #8b5cf6;
            font-weight: 600;
        }
        
        .mesa-actions {
            text-align: center;
            margin-top: 15px;
        }
        
        .action-btn {
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            margin: 5px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-ver {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
        }
        
        .btn-finalizar {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }
          .btn-pedido {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }
        
        .btn-adicionar {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
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
        
        .stat-item {
            text-align: center;
            padding: 15px;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 900;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: #6b7280;
            font-weight: 600;
        }
        
        .stat-disponivel { color: #10b981; }
        .stat-ocupada { color: #ef4444; }
        .stat-manutencao { color: #f59e0b; }
        .stat-total { color: #6366f1; }
        
        .filter-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
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
            .mesa-card {
                margin-bottom: 15px;
                padding: 15px;
            }
            
            .mesa-numero {
                font-size: 2rem;
            }
            
            .action-btn {
                font-size: 0.8rem;
                padding: 6px 12px;
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
                        <a class="nav-link active" href="{{ route('garcom.mesas') }}">
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
                        <i class="fas fa-chair me-2"></i>
                        Gerenciamento de Mesas
                    </h2>
                    <p class="mb-0">Visualize o status e gerencie todas as mesas do restaurante</p>
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
            <h6 class="mb-3">📊 Status das Mesas</h6>
            <div class="row">                @php
                    $totalMesas = $mesas->count();
                    $mesasOcupadas = $mesas->filter(function($mesa) { return $mesa->pedidos->count() > 0; })->count();
                    $mesasDisponiveis = $mesas->filter(function($mesa) { return $mesa->pedidos->count() == 0; })->count();
                    $mesasManutencao = 0; // Não temos status de manutenção na estrutura atual
                @endphp
                
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number stat-total">{{ $totalMesas }}</div>
                        <div class="stat-label">Total</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number stat-disponivel">{{ $mesasDisponiveis }}</div>
                        <div class="stat-label">Disponíveis</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number stat-ocupada">{{ $mesasOcupadas }}</div>
                        <div class="stat-label">Ocupadas</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number stat-manutencao">{{ $mesasManutencao }}</div>
                        <div class="stat-label">Manutenção</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filter-section">
            <h6 class="mb-3">🔍 Filtrar Mesas</h6>
            <div class="text-center">
                <button class="filter-btn active" onclick="filtrarMesas('todas')">
                    <i class="fas fa-list me-1"></i> Todas
                </button>
                <button class="filter-btn" onclick="filtrarMesas('disponivel')">
                    <i class="fas fa-check-circle me-1"></i> Disponíveis
                </button>
                <button class="filter-btn" onclick="filtrarMesas('ocupada')">
                    <i class="fas fa-user-friends me-1"></i> Ocupadas
                </button>
                <button class="filter-btn" onclick="filtrarMesas('manutencao')">
                    <i class="fas fa-tools me-1"></i> Manutenção
                </button>
            </div>
        </div>

        <!-- Mesas -->
        <div class="mesas-container">
            <h5 class="mb-4">
                <i class="fas fa-chair me-2"></i>
                Layout do Restaurante
            </h5>
              <div class="row" id="mesas-grid">
                @forelse($mesas as $mesa)
                <div class="col-md-4 col-lg-3 mesa-item" data-status="@if($mesa->pedidos->count() > 0)ocupada @else disponivel @endif">
                    <div class="mesa-card @if($mesa->pedidos->count() > 0)ocupada @else disponivel @endif">
                        <div class="mesa-numero @if($mesa->pedidos->count() > 0)ocupada @else disponivel @endif">
                            {{ $mesa->identificador }}
                        </div>
                        
                        <div class="mesa-status @if($mesa->pedidos->count() > 0)ocupada @else disponivel @endif">
                            @if($mesa->pedidos->count() > 0)
                                <i class="fas fa-user-friends me-1"></i> Ocupada
                            @else
                                <i class="fas fa-check-circle me-1"></i> Disponível
                            @endif
                        </div>                        @if($mesa->pedidos->count() > 0)
                            @php $pedido = $mesa->pedidos->first(); @endphp
                            <div class="mesa-info">
                                <div class="pedido-info">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong>Pedido #{{ $pedido->id }}</strong>
                                            <div class="tempo-pedido">
                                                <i class="fas fa-clock me-1"></i>
                                                Há {{ $pedido->created_at->diffForHumans(null, true) }}
                                            </div>
                                        </div>
                                        <div class="valor-pedido">
                                            R$ {{ number_format($pedido->total, 2, ',', '.') }}
                                        </div>
                                    </div>
                                    <div class="garcom-info mt-2">
                                        <i class="fas fa-user me-1"></i>
                                        {{ $pedido->usuario->nome }}
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="mesa-actions">
                            @if($mesa->pedidos->count() == 0)
                                <a href="{{ route('garcom.pedido-rapido') }}?mesa={{ $mesa->id }}" class="action-btn btn-pedido">
                                    <i class="fas fa-plus me-1"></i> Novo Pedido
                                </a>                            @else
                                <a href="{{ route('garcom.pedidos.show', $mesa->pedidos->first()->id) }}" class="action-btn btn-ver">
                                    <i class="fas fa-eye me-1"></i> Ver Pedido
                                </a>
                                
                                <a href="{{ route('garcom.pedido-rapido.adicionar') }}?mesa={{ $mesa->id }}&pedido={{ $mesa->pedidos->first()->id }}" class="action-btn btn-adicionar">
                                    <i class="fas fa-plus me-1"></i> Adicionar Itens
                                </a>
                                
                                @if($mesa->pedidos->first()->usuario_id == (Auth::id() ?? 1))
                                    <button class="action-btn btn-finalizar" data-mesa-id="{{ $mesa->id }}" onclick="finalizarMesa(this.dataset.mesaId)">
                                        <i class="fas fa-check me-1"></i> Finalizar
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-chair fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhuma mesa cadastrada</h5>
                        <p class="text-muted">Entre em contato com o administrador para cadastrar mesas.</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function filtrarMesas(status) {
            const mesaItems = document.querySelectorAll('.mesa-item');
            const filterButtons = document.querySelectorAll('.filter-btn');
            
            // Atualizar botões ativos
            filterButtons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            // Filtrar mesas
            mesaItems.forEach(item => {
                if (status === 'todas' || item.dataset.status === status) {
                    item.style.display = 'block';
                    item.style.opacity = '0';
                    setTimeout(() => {
                        item.style.transition = 'opacity 0.3s ease';
                        item.style.opacity = '1';
                    }, 100);
                } else {
                    item.style.opacity = '0';
                    setTimeout(() => {
                        item.style.display = 'none';
                    }, 300);
                }
            });
        }

        function finalizarMesa(mesaId) {
            if (!confirm('Tem certeza que deseja finalizar esta mesa? Esta ação finalizará todos os pedidos abertos.')) {
                return;
            }

            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Finalizando...';
            btn.disabled = true;

            fetch(`/garcom/mesas/${mesaId}/finalizar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Recarregar a página para atualizar o status
                    window.location.reload();
                } else {
                    alert('Erro: ' + data.message);
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao finalizar mesa. Tente novamente.');
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }

        // Atualização automática a cada minuto
        setInterval(() => {
            window.location.reload();
        }, 60000);

        // Animações de entrada
        document.addEventListener('DOMContentLoaded', function() {
            const mesaCards = document.querySelectorAll('.mesa-card');
            mesaCards.forEach((card, index) => {
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
