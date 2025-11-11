<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editar Pedido #{{ $pedido->id }} - Sistema Bar/Restaurante</title>
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
        
        .form-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 35px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }
        
        .form-control,
        .form-select {
            border: 2px solid rgba(99, 102, 241, 0.2);
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus,
        .form-select:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            outline: none;
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            color: white;
            font-weight: 600;
            padding: 14px 28px;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-size: 1.1rem;
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
            padding: 12px 26px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .btn-outline-gradient:hover {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            transform: translateY(-2px);
        }
        
        .info-card {
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .info-item:last-child {
            margin-bottom: 0;
        }
        
        .info-icon {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            color: white;
            font-size: 0.9rem;
        }
        
        .icon-id { background: linear-gradient(135deg, #6366f1, #8b5cf6); }
        .icon-date { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .icon-total { background: linear-gradient(135deg, #10b981, #059669); }
        .icon-items { background: linear-gradient(135deg, #06b6d4, #0891b2); }
        
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
        
        .mesa-preview {
            background: rgba(99, 102, 241, 0.05);
            border: 2px solid rgba(99, 102, 241, 0.2);
            border-radius: 12px;
            padding: 15px;
            margin-top: 10px;
        }
        
        .garcom-preview {
            background: rgba(139, 92, 246, 0.05);
            border: 2px solid rgba(139, 92, 246, 0.2);
            border-radius: 12px;
            padding: 15px;
            margin-top: 10px;
        }
        
        .alert-warning {
            background: rgba(251, 191, 36, 0.1);
            border: 1px solid rgba(251, 191, 36, 0.3);
            color: #92400e;
        }
        
        .form-help {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 5px;
        }
        
        .quick-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        
        .quick-btn {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #6366f1;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .quick-btn:hover {
            background: #6366f1;
            color: white;
        }
        
        @media (max-width: 768px) {
            .form-card { padding: 25px; }
            .quick-actions { flex-direction: column; }
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
                <a class="nav-link" href="{{ route('pedidos.show', $pedido) }}">
                    <i class="fas fa-eye me-1"></i>Visualizar
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Hero Section -->
        <div class="hero-section">
            <h1>
                <i class="fas fa-edit me-3"></i>
                Editar Pedido #{{ $pedido->id }}
            </h1>
            <p class="lead mb-0">
                Atualize as informações do pedido conforme necessário
            </p>
        </div>

        <!-- Informações Atuais -->
        <div class="info-card">
            <h6 class="mb-3">
                <i class="fas fa-info-circle me-2"></i>
                Informações Atuais do Pedido
            </h6>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="info-item">
                        <div class="info-icon icon-id">
                            <i class="fas fa-hashtag"></i>
                        </div>
                        <div>
                            <small class="text-muted">Número</small>
                            <div class="fw-bold">#{{ $pedido->id }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="info-item">
                        <div class="info-icon icon-date">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div>
                            <small class="text-muted">Criado em</small>
                            <div class="fw-bold">{{ $pedido->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="info-item">
                        <div class="info-icon icon-total">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div>
                            <small class="text-muted">Total</small>
                            <div class="fw-bold text-success">R$ {{ number_format($pedido->total, 2, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="info-item">
                        <div class="info-icon icon-items">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div>
                            <small class="text-muted">Itens</small>
                            <div class="fw-bold">{{ $pedido->itens->count() }} item{{ $pedido->itens->count() != 1 ? 's' : '' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('pedidos.update', $pedido) }}" method="POST" id="editForm">
            @csrf
            @method('PUT')
            
            <!-- Formulário Principal -->
            <div class="form-card">
                <h5 class="mb-4">
                    <i class="fas fa-edit me-2"></i>
                    Editar Informações
                </h5>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Atenção:</strong> Mudanças na mesa ou garçom podem afetar o fluxo do pedido. Certifique-se de que a alteração é necessária.
                </div>

                <div class="row">
                    <!-- Seleção de Mesa -->
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="mesa_id" class="form-label">
                                <i class="fas fa-table me-1"></i>
                                Mesa
                            </label>
                            <select class="form-select" name="mesa_id" id="mesa_id" required>
                                @foreach($mesas as $mesa)
                                    <option value="{{ $mesa->id }}" 
                                            {{ $mesa->id == $pedido->mesa_id ? 'selected' : '' }}
                                            data-lugares="{{ $mesa->lugares }}"
                                            data-status="{{ $mesa->pedidos->where('status', '!=', 'entregue')->where('id', '!=', $pedido->id)->count() > 0 ? 'ocupada' : 'livre' }}">
                                        Mesa {{ $mesa->identificador }} ({{ $mesa->lugares }} lugares)
                                        @if($mesa->pedidos->where('status', '!=', 'entregue')->where('id', '!=', $pedido->id)->count() > 0)
                                            - OCUPADA
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-help">
                                Mesas ocupadas por outros pedidos são indicadas como "OCUPADA"
                            </div>
                            
                            <!-- Preview da Mesa Atual -->
                            <div class="mesa-preview">
                                <small class="text-muted">Mesa atual:</small>
                                <div class="fw-bold">
                                    <i class="fas fa-table me-1"></i>
                                    Mesa {{ $pedido->mesa->identificador }} ({{ $pedido->mesa->lugares }} lugares)
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seleção de Garçom -->
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="usuario_id" class="form-label">
                                <i class="fas fa-user me-1"></i>
                                Garçom Responsável
                            </label>
                            <select class="form-select" name="usuario_id" id="usuario_id" required>
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" 
                                            {{ $usuario->id == $pedido->usuario_id ? 'selected' : '' }}
                                            data-role="{{ $usuario->role }}">
                                        {{ $usuario->nome }} ({{ ucfirst($usuario->role) }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-help">
                                Selecione o garçom que ficará responsável por este pedido
                            </div>
                            
                            <!-- Preview do Garçom Atual -->
                            <div class="garcom-preview">
                                <small class="text-muted">Garçom atual:</small>
                                <div class="fw-bold">
                                    <i class="fas fa-user me-1"></i>
                                    {{ $pedido->usuario->nome }} ({{ ucfirst($pedido->usuario->role) }})
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Status do Pedido -->
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="status" class="form-label">
                                <i class="fas fa-flag me-1"></i>
                                Status do Pedido
                            </label>
                            
                            <!-- Ações Rápidas de Status -->
                            <div class="quick-actions">
                                <button type="button" class="quick-btn" onclick="setStatus('pendente')">
                                    <i class="fas fa-clock me-1"></i>Pendente
                                </button>
                                <button type="button" class="quick-btn" onclick="setStatus('em_preparo')">
                                    <i class="fas fa-utensils me-1"></i>Em Preparo
                                </button>
                                <button type="button" class="quick-btn" onclick="setStatus('pronto')">
                                    <i class="fas fa-check me-1"></i>Pronto
                                </button>
                                <button type="button" class="quick-btn" onclick="setStatus('entregue')">
                                    <i class="fas fa-handshake me-1"></i>Entregue
                                </button>
                                <button type="button" class="quick-btn" onclick="setStatus('cancelado')">
                                    <i class="fas fa-times me-1"></i>Cancelado
                                </button>
                            </div>
                            
                            <select class="form-select" name="status" id="status" required>
                                <option value="pendente" {{ $pedido->status == 'pendente' ? 'selected' : '' }}>
                                    Pendente
                                </option>
                                <option value="em_preparo" {{ $pedido->status == 'em_preparo' ? 'selected' : '' }}>
                                    Em Preparo
                                </option>
                                <option value="pronto" {{ $pedido->status == 'pronto' ? 'selected' : '' }}>
                                    Pronto
                                </option>
                                <option value="entregue" {{ $pedido->status == 'entregue' ? 'selected' : '' }}>
                                    Entregue
                                </option>
                                <option value="cancelado" {{ $pedido->status == 'cancelado' ? 'selected' : '' }}>
                                    Cancelado
                                </option>
                            </select>
                            
                            <!-- Preview do Status Atual -->
                            <div class="mt-2">
                                <small class="text-muted">Status atual:</small>
                                <span class="status-badge status-{{ $pedido->status }} ms-2">
                                    {{ ucfirst(str_replace('_', ' ', $pedido->status)) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Total (Somente Leitura) -->
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-calculator me-1"></i>
                                Total do Pedido
                            </label>
                            <div class="form-control bg-light" readonly>
                                R$ {{ number_format($pedido->total, 2, ',', '.') }}
                            </div>
                            <div class="form-help">
                                O total é calculado automaticamente baseado nos itens do pedido
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('pedidos.detalhes', $pedido) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-edit me-1"></i>
                                    Gerenciar Itens
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ações -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-outline-gradient me-2">
                                    <i class="fas fa-eye me-2"></i>
                                    Visualizar
                                </a>
                                <a href="{{ route('pedidos.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Voltar à Lista
                                </a>
                            </div>
                            
                            <button type="submit" class="btn btn-gradient">
                                <i class="fas fa-save me-2"></i>
                                Salvar Alterações
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Modal de Confirmação para Status Críticos -->
    <div class="modal fade" id="confirmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Confirmar Mudança de Status
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="confirmMessage"></p>
                    <p class="text-warning">
                        <i class="fas fa-info-circle me-1"></i>
                        Esta ação pode afetar o fluxo do pedido.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-warning" id="confirmStatus">
                        <i class="fas fa-check me-1"></i>
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function setStatus(status) {
            const statusSelect = document.getElementById('status');
            
            // Para status críticos, mostrar confirmação
            if (status === 'cancelado' || status === 'entregue') {
                const messages = {
                    'cancelado': 'Tem certeza que deseja cancelar este pedido?',
                    'entregue': 'Confirma que este pedido foi entregue ao cliente?'
                };
                
                document.getElementById('confirmMessage').textContent = messages[status];
                const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
                modal.show();
                
                document.getElementById('confirmStatus').onclick = function() {
                    statusSelect.value = status;
                    modal.hide();
                };
                
                return;
            }
            
            statusSelect.value = status;
        }

        // Validação de mesa ocupada
        document.getElementById('mesa_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const status = selectedOption.getAttribute('data-status');
            
            if (status === 'ocupada') {
                const confirmChange = confirm('A mesa selecionada está ocupada por outro pedido. Deseja continuar mesmo assim?');
                if (!confirmChange) {
                    // Voltar para a mesa original
                    this.value = '{{ $pedido->mesa_id }}';
                }
            }
        });

        // Animação dos elementos ao carregar
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.form-card, .info-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });

        // Feedback visual para mudanças
        const form = document.getElementById('editForm');
        const originalValues = new FormData(form);
        
        form.addEventListener('input', function() {
            const currentValues = new FormData(form);
            let hasChanges = false;
            
            for (let [key, value] of currentValues) {
                if (originalValues.get(key) !== value) {
                    hasChanges = true;
                    break;
                }
            }
            
            const submitBtn = form.querySelector('button[type="submit"]');
            if (hasChanges) {
                submitBtn.classList.add('btn-warning');
                submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Salvar Alterações *';
            } else {
                submitBtn.classList.remove('btn-warning');
                submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Salvar Alterações';
            }
        });
    </script>
</body>
</html>
