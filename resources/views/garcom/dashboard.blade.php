<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>🍽️ Modo Garçom - Dashboard</title>
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
        
        .quick-actions {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        
        .action-btn {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            border: none;
            border-radius: 15px;
            padding: 20px;
            margin: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
            text-align: center;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }
        
        .action-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
            color: white;
        }
        
        .action-btn i {
            font-size: 2rem;
            margin-bottom: 10px;
            display: block;
        }
        
        .stats-row {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 20px;
            margin: 10px;
            text-align: center;
            border-left: 5px solid;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card.pedidos { border-color: #6366f1; }
        .stat-card.vendas { border-color: #10b981; }
        .stat-card.mesas-disp { border-color: #f59e0b; }
        .stat-card.mesas-ocup { border-color: #ef4444; }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: #1f2937;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: #6b7280;
            font-weight: 600;
        }
        
        .recent-orders {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        
        .order-item {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid #6366f1;
            transition: all 0.3s ease;
        }
        
        .order-item:hover {
            transform: translateX(5px);
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
        }
        
        .mesas-status {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        
        .mesa-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 15px;
            margin: 10px;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid;
        }
        
        .mesa-card.ocupada {
            border-color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
        }
        
        .mesa-card.disponivel {
            border-color: #10b981;
            background: rgba(16, 185, 129, 0.1);
        }
        
        .mesa-numero {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .mesa-status {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .refresh-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
            transition: all 0.3s ease;
        }
        
        .refresh-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.6);
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 1.8rem;
            }
            
            .action-btn {
                margin: 5px 0;
            }
            
            .stat-card {
                margin: 5px 0;
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
                        <a class="nav-link active" href="{{ route('garcom.dashboard') }}">
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
                        <a class="nav-link" href="{{ route('garcom.meus-pedidos') }}">
                            <i class="fas fa-receipt me-1"></i> Meus Pedidos
                        </a>
                    </li>
                </ul>                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/" title="Voltar à Gestão Administrativa">
                            <i class="fas fa-cog me-1"></i> Gestão Admin
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i> {{ $user->nome ?? 'Demo User' }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/">
                                <i class="fas fa-home me-1"></i> Dashboard Geral
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-1"></i> Logout
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="container">
        <div class="hero-section">            <h1 class="hero-title">
                <i class="fas fa-utensils me-3"></i>
                Bem-vindo, {{ $user->nome ?? 'Demo User' }}!
            </h1>
            <p class="mb-0">Interface otimizada para operações diárias do restaurante</p>
            <small class="text-light">Última atualização: <span id="ultimo-update">{{ now()->format('H:i:s') }}</span></small>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="container">
        <div class="quick-actions">
            <h5 class="mb-4">⚡ Ações Rápidas</h5>
            <div class="row">
                <div class="col-md-3 col-6">
                    <a href="{{ route('garcom.pedido-rapido') }}" class="action-btn">
                        <i class="fas fa-plus-circle"></i>
                        <strong>Novo Pedido</strong>
                        <small class="d-block">Criar pedido rápido</small>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="{{ route('garcom.cardapio') }}" class="action-btn">
                        <i class="fas fa-book-open"></i>
                        <strong>Cardápio</strong>
                        <small class="d-block">Consultar produtos</small>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="{{ route('garcom.mesas') }}" class="action-btn">
                        <i class="fas fa-chair"></i>
                        <strong>Mesas</strong>
                        <small class="d-block">Gerenciar mesas</small>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="{{ route('garcom.meus-pedidos') }}" class="action-btn">
                        <i class="fas fa-receipt"></i>
                        <strong>Meus Pedidos</strong>
                        <small class="d-block">Pedidos do dia</small>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="container">
        <div class="stats-row">
            <h5 class="mb-4">📊 Minhas Estatísticas de Hoje</h5>
            <div class="row">
                <div class="col-md-3 col-6">
                    <div class="stat-card pedidos">
                        <div class="stat-number" id="meus-pedidos">{{ $meusPedidosHoje }}</div>
                        <div class="stat-label">Meus Pedidos</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card vendas">
                        <div class="stat-number" id="minha-venda">R$ {{ number_format($minhaVendaHoje, 2, ',', '.') }}</div>
                        <div class="stat-label">Minha Venda</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card mesas-disp">
                        <div class="stat-number" id="mesas-disponiveis">{{ $mesasDisponiveis }}</div>
                        <div class="stat-label">Mesas Livres</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card mesas-ocup">
                        <div class="stat-number" id="mesas-ocupadas">{{ $mesasOcupadas }}</div>
                        <div class="stat-label">Mesas Ocupadas</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status das Mesas -->
    <div class="container">
        <div class="mesas-status">
            <h5 class="mb-4">🪑 Status das Mesas em Tempo Real</h5>
            <div class="row">                @foreach($mesasOcupadasInfo as $mesa)
                <div class="col-md-2 col-4">
                    <div class="mesa-card ocupada" onclick="verDetalheMesa({{ $mesa->id }})">
                        <div class="mesa-numero">{{ $mesa->identificador ?? 'Mesa ' . $mesa->numero }}</div>
                        <div class="mesa-status text-danger">Ocupada</div>
                        @if($mesa->pedidos->count() > 0)
                            <small class="d-block mt-1">
                                Pedido #{{ $mesa->pedidos->first()->id }}
                            </small>
                        @endif
                    </div>
                </div>                @endforeach
                
                @foreach($mesasDisponiveisInfo as $mesa)
                <div class="col-md-2 col-4">
                    <div class="mesa-card disponivel">
                        <div class="mesa-numero">{{ $mesa->identificador ?? 'Mesa ' . $mesa->numero }}</div>
                        <div class="mesa-status text-success">Disponível</div>
                        <small class="d-block mt-1">Pronta para usar</small>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="text-center mt-3">
                <a href="{{ route('garcom.mesas') }}" class="btn btn-outline-primary">
                    <i class="fas fa-eye me-1"></i> Ver Todas as Mesas
                </a>
            </div>
        </div>
    </div>

    <!-- Últimos Pedidos -->
    <div class="container">
        <div class="recent-orders">
            <h5 class="mb-4">📋 Meus Últimos Pedidos</h5>
            @forelse($ultimosPedidos as $pedido)
                <div class="order-item">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <strong>Pedido #{{ $pedido->id }}</strong>
                            <br><small class="text-muted">{{ $pedido->created_at->format('H:i') }}</small>
                        </div>                        <div class="col-md-2">
                            <span class="badge bg-info">{{ $pedido->mesa->identificador ?? 'Mesa ' . $pedido->mesa->numero }}</span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">
                                {{ $pedido->itens->count() }} {{ $pedido->itens->count() == 1 ? 'item' : 'itens' }}
                                @if($pedido->itens->count() > 0)
                                    - {{ $pedido->itens->first()->produto->nome }}
                                    @if($pedido->itens->count() > 1)
                                        e mais {{ $pedido->itens->count() - 1 }}
                                    @endif
                                @endif
                            </small>
                        </div>                        <div class="col-md-2">
                            <strong class="text-success">R$ {{ number_format($pedido->total, 2, ',', '.') }}</strong>
                        </div>
                        <div class="col-md-2">
                            @if($pedido->status == 'aberto')
                                <span class="badge bg-warning">Em andamento</span>
                            @else
                                <span class="badge bg-success">Finalizado</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Nenhum pedido ainda hoje. Que tal criar o primeiro?</p>
                    <a href="{{ route('garcom.pedido-rapido') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Criar Primeiro Pedido
                    </a>
                </div>
            @endforelse
            
            @if($ultimosPedidos->count() > 0)
                <div class="text-center mt-3">
                    <a href="{{ route('garcom.meus-pedidos') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-1"></i> Ver Todos os Meus Pedidos
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Botão de Refresh -->
    <button class="refresh-btn" onclick="atualizarDados()" title="Atualizar dados">
        <i class="fas fa-sync-alt" id="refresh-icon"></i>
    </button>

    <!-- Form de Logout (hidden) -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        console.log('📊 Dashboard script carregado');
        
        // Atualização automática a cada 30 segundos
        setInterval(atualizarDados, 30000);function atualizarDados() {
            const icon = document.getElementById('refresh-icon');
            icon.classList.add('fa-spin');
            
            fetch('/garcom/dashboard-data', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na resposta: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Dados recebidos:', data);
                
                // Atualizar estatísticas
                document.getElementById('meus-pedidos').textContent = data.meusPedidosHoje;
                document.getElementById('minha-venda').textContent = 'R$ ' + data.minhaVendaHoje;
                document.getElementById('mesas-disponiveis').textContent = data.mesasDisponiveis;
                document.getElementById('mesas-ocupadas').textContent = data.mesasOcupadas;
                  // Atualizar seção de mesas ocupadas
                atualizarMesasOcupadas(data.mesasOcupadasInfo, data.mesasDisponiveisInfo);
                
                // Atualizar últimos pedidos
                atualizarUltimosPedidos(data.ultimosPedidos);
                
                // Atualizar timestamp
                document.getElementById('ultimo-update').textContent = data.timestamp;
                
                icon.classList.remove('fa-spin');
            })
            .catch(error => {
                console.error('Erro ao atualizar dados:', error);
                icon.classList.remove('fa-spin');
                
                // Mostrar erro visual (opcional)
                const icon = document.getElementById('refresh-icon');
                icon.style.color = 'red';
                setTimeout(() => {
                    icon.style.color = '';
                }, 2000);
            });
        }        function atualizarMesasOcupadas(mesasOcupadas, mesasDisponiveis) {
            const container = document.querySelector('.mesas-status .row');
            if (!container) return;
            
            let html = '';
              // Adicionar mesas ocupadas
            mesasOcupadas.forEach(mesa => {
                const nomeExibicao = mesa.identificador || `Mesa ${mesa.numero}`;
                html += `
                    <div class="col-md-2 col-4">
                        <div class="mesa-card ocupada" onclick="verDetalheMesa(${mesa.id})">
                            <div class="mesa-numero">${nomeExibicao}</div>
                            <div class="mesa-status text-danger">Ocupada</div>
                            <small class="d-block mt-1">
                                Pedido #${mesa.pedido_id || 'N/A'}
                            </small>
                        </div>
                    </div>
                `;
            });
            
            // Adicionar mesas disponíveis reais (não números sequenciais)
            mesasDisponiveis.forEach(mesa => {
                const nomeExibicao = mesa.identificador || `Mesa ${mesa.numero}`;
                html += `
                    <div class="col-md-2 col-4">
                        <div class="mesa-card disponivel">
                            <div class="mesa-numero">${nomeExibicao}</div>
                            <div class="mesa-status text-success">Disponível</div>
                            <small class="d-block mt-1">Pronta para usar</small>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }

        function atualizarUltimosPedidos(pedidos) {
            const container = document.querySelector('.recent-orders');
            const pedidosContainer = container.querySelector('.order-item')?.parentNode;
            
            if (!pedidosContainer) return;
            
            // Remover pedidos existentes
            pedidosContainer.querySelectorAll('.order-item').forEach(item => item.remove());
            
            if (pedidos.length === 0) {
                const emptyState = `
                    <div class="text-center py-4">
                        <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Nenhum pedido ainda hoje. Que tal criar o primeiro?</p>
                        <a href="/garcom/pedido-rapido" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Criar Primeiro Pedido
                        </a>
                    </div>
                `;
                pedidosContainer.innerHTML = emptyState;
                return;
            }
              pedidos.forEach(pedido => {
                const statusBadge = pedido.status === 'aberto' ? 
                    '<span class="badge bg-warning">Em andamento</span>' : 
                    '<span class="badge bg-success">Finalizado</span>';
                
                const pedidoHtml = `
                    <div class="order-item">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <strong>Pedido #${pedido.id}</strong>
                                <br><small class="text-muted">${pedido.horario}</small>
                            </div>
                            <div class="col-md-2">
                                <span class="badge bg-info">${pedido.mesa_identificador}</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">
                                    ${pedido.itens_count} ${pedido.itens_count == 1 ? 'item' : 'itens'}
                                    ${pedido.primeiro_item ? '- ' + pedido.primeiro_item : ''}
                                    ${pedido.itens_count > 1 ? ' e mais ' + (pedido.itens_count - 1) : ''}
                                </small>
                            </div>
                            <div class="col-md-2">
                                <strong class="text-success">R$ ${pedido.valor_total}</strong>
                            </div>
                            <div class="col-md-2">
                                ${statusBadge}
                            </div>
                        </div>
                    </div>
                `;
                pedidosContainer.insertAdjacentHTML('beforeend', pedidoHtml);
            });
        }

        function verDetalheMesa(mesaId) {
            window.location.href = `/mesas/${mesaId}`;
        }

        // Animações de entrada
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card, .order-item, .mesa-card');
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
