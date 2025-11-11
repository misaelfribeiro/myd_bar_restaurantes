<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>📋 Pedido #{{ $pedido->id }} - Modo Garçom</title>
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
        
        .pedido-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        
        .pedido-header {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            padding: 20px;
            margin: -25px -25px 25px -25px;
            border-radius: 15px 15px 0 0;
        }
        
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-aberto {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        
        .status-finalizado {
            background: rgba(99, 102, 241, 0.1);
            color: #6366f1;
            border: 1px solid rgba(99, 102, 241, 0.3);
        }
        
        .status-cancelado {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        .info-card {
            background: rgba(255, 255, 255, 0.7);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .info-label {
            font-size: 0.85rem;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 1.1rem;
            color: #1f2937;
            font-weight: 600;
        }
        
        .itens-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        
        .item-produto {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        
        .item-produto:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-color: #6366f1;
        }
        
        .item-produto:last-child {
            margin-bottom: 0;
        }
        
        .produto-nome {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .produto-detalhes {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .quantidade-info {
            background: rgba(99, 102, 241, 0.1);
            color: #6366f1;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .preco-unitario {
            color: #6b7280;
            font-size: 0.9rem;
        }
        
        .preco-total {
            font-size: 1.1rem;
            font-weight: 700;
            color: #10b981;
        }
        
        .total-section {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            margin: 25px 0;
        }
        
        .total-label {
            font-size: 0.9rem;
            margin-bottom: 5px;
            opacity: 0.9;
        }
        
        .total-valor {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 0;
        }
        
        .action-buttons {
            text-align: center;
            margin-top: 30px;
        }
        
        .action-btn {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            margin: 5px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.3);
            color: white;
        }
        
        .btn-voltar {
            background: linear-gradient(135deg, #6b7280, #4b5563);
        }
        
        .btn-finalizar {
            background: linear-gradient(135deg, #10b981, #059669);
        }
        
        .btn-finalizar:hover {
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
        }
        
        .observacoes-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        
        .tempo-info {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
        }
        
        .tempo-icon {
            font-size: 1.5rem;
            margin-bottom: 8px;
            display: block;
        }
        
        @media (max-width: 768px) {
            .pedido-container {
                padding: 15px;
            }
            
            .pedido-header {
                padding: 15px;
                margin: -15px -15px 20px -15px;
            }
            
            .total-valor {
                font-size: 2rem;
            }
            
            .action-btn {
                display: block;
                margin: 10px 0;
                width: 100%;
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
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('garcom.mesas') }}">
                    <i class="fas fa-arrow-left me-1"></i> Voltar às Mesas
                </a>
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
                        Pedido #{{ $pedido->id }}
                    </h2>
                    <p class="mb-0">Detalhes completos do pedido e itens solicitados</p>
                </div>
                <div class="col-md-4 text-end">
                    <span class="status-badge status-{{ $pedido->status }}">
                        @if($pedido->status == 'aberto')
                            <i class="fas fa-clock me-1"></i> Em Andamento
                        @elseif($pedido->status == 'finalizado')
                            <i class="fas fa-check me-1"></i> Finalizado
                        @else
                            <i class="fas fa-times me-1"></i> {{ ucfirst($pedido->status) }}
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Informações do Pedido -->
        <div class="pedido-container">
            <div class="pedido-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Informações do Pedido
                        </h4>
                    </div>
                    <div class="col-md-6 text-end">
                        <h5 class="mb-0">{{ $pedido->created_at->format('d/m/Y H:i') }}</h5>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="info-card">
                        <div class="info-label">Mesa</div>
                        <div class="info-value">
                            <i class="fas fa-chair me-2 text-primary"></i>
                            {{ $pedido->mesa->identificador }}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-card">
                        <div class="info-label">Garçom</div>
                        <div class="info-value">
                            <i class="fas fa-user me-2 text-success"></i>
                            {{ $pedido->usuario->nome }}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-card">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            @if($pedido->status == 'aberto')
                                <i class="fas fa-clock me-2 text-warning"></i>
                                Em Andamento
                            @elseif($pedido->status == 'finalizado')
                                <i class="fas fa-check me-2 text-success"></i>
                                Finalizado
                            @else
                                <i class="fas fa-times me-2 text-danger"></i>
                                {{ ucfirst($pedido->status) }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tempo do Pedido -->
            <div class="tempo-info">
                <i class="fas fa-stopwatch tempo-icon"></i>
                <strong>Tempo desde o pedido:</strong> {{ $pedido->created_at->diffForHumans() }}
                <br>
                <small>Iniciado às {{ $pedido->created_at->format('H:i') }}</small>
            </div>
        </div>

        <!-- Itens do Pedido -->
        <div class="itens-container">
            <h5 class="mb-4">
                <i class="fas fa-utensils me-2"></i>
                Itens do Pedido ({{ $pedido->itens->count() }} {{ $pedido->itens->count() == 1 ? 'item' : 'itens' }})
            </h5>

            @forelse($pedido->itens as $item)
                <div class="item-produto">
                    <div class="produto-nome">{{ $item->produto->nome }}</div>
                    <div class="produto-detalhes">
                        <div>
                            <span class="quantidade-info">
                                <i class="fas fa-times me-1"></i>{{ $item->quantidade }}
                            </span>
                            <span class="preco-unitario ms-3">
                                R$ {{ number_format($item->preco_unitario, 2, ',', '.') }} cada
                            </span>
                        </div>
                        <div class="preco-total">
                            R$ {{ number_format($item->preco_unitario * $item->quantidade, 2, ',', '.') }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-utensils fa-2x mb-3"></i>
                    <p>Nenhum item encontrado neste pedido.</p>
                </div>
            @endforelse
        </div>

        <!-- Observações -->
        @if($pedido->observacoes)
            <div class="observacoes-section">
                <h5 class="mb-3">
                    <i class="fas fa-sticky-note me-2"></i>
                    Observações
                </h5>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ $pedido->observacoes }}
                </div>
            </div>
        @endif

        <!-- Total -->
        <div class="total-section">
            <div class="total-label">Valor Total do Pedido</div>
            <div class="total-valor">R$ {{ number_format($pedido->total, 2, ',', '.') }}</div>
        </div>

        <!-- Botões de Ação -->
        <div class="action-buttons">
            <a href="{{ route('garcom.mesas') }}" class="action-btn btn-voltar">
                <i class="fas fa-arrow-left me-2"></i>
                Voltar às Mesas
            </a>

            @if($pedido->status == 'aberto')
                <button class="action-btn btn-finalizar" onclick="finalizarPedido({{ $pedido->id }})">
                    <i class="fas fa-check me-2"></i>
                    Finalizar Pedido
                </button>
            @endif

            <a href="{{ route('garcom.dashboard') }}" class="action-btn">
                <i class="fas fa-tachometer-alt me-2"></i>
                Dashboard
            </a>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function finalizarPedido(pedidoId) {
            if (!confirm('Tem certeza que deseja finalizar este pedido?')) {
                return;
            }

            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Finalizando...';
            btn.disabled = true;            fetch(`{{ route('garcom.pedidos.atualizar-status', $pedido->id) }}`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ status: 'finalizado' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Pedido finalizado com sucesso!');
                    window.location.reload();
                } else {
                    alert('Erro: ' + (data.message || 'Não foi possível finalizar o pedido'));
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao finalizar pedido. Tente novamente.');
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }

        // Atualização automática do tempo
        function atualizarTempo() {
            const tempoElement = document.querySelector('.tempo-info');
            if (tempoElement) {
                fetch(`/api/pedidos/${{{ $pedido->id }}}/tempo`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.tempo_formatado) {
                            tempoElement.innerHTML = `
                                <i class="fas fa-stopwatch tempo-icon"></i>
                                <strong>Tempo desde o pedido:</strong> ${data.tempo_formatado}
                                <br>
                                <small>Iniciado às ${data.horario_inicio}</small>
                            `;
                        }
                    })
                    .catch(error => {
                        console.log('Erro ao atualizar tempo:', error);
                    });
            }
        }

        // Atualizar tempo a cada minuto
        setInterval(atualizarTempo, 60000);

        // Animações de entrada
        document.addEventListener('DOMContentLoaded', function() {
            const items = document.querySelectorAll('.item-produto');
            items.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    item.style.transition = 'all 0.5s ease';
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>
