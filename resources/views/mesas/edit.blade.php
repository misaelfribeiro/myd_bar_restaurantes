<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editar Mesa {{ $mesa->identificador }} - Sistema Bar/Restaurante</title>
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
        
        .form-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-control {
            border-radius: 15px;
            border: 2px solid #e9ecef;
            padding: 15px 20px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: white;
        }
        
        .form-text {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            min-width: 200px;
        }
        
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 193, 7, 0.3);
            color: white;
        }
        
        .btn-cancel {
            background: linear-gradient(135deg, #6c757d, #495057);
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-width: 200px;
            justify-content: center;
        }
        
        .btn-cancel:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(108, 117, 125, 0.3);
            color: white;
            text-decoration: none;
        }
        
        .mesa-preview {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            border-radius: 20px;
            padding: 30px;
            color: white;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .mesa-icon-preview {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 20px;
        }
        
        .info-atual {
            background: rgba(102, 126, 234, 0.1);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 5px solid #667eea;
        }
        
        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        
        .form-control.is-invalid {
            border-color: #dc3545;
        }
        
        .historico-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .historico-item:last-child {
            border-bottom: none;
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            .form-container {
                padding: 20px;
            }
            .btn-submit,
            .btn-cancel {
                min-width: 100%;
                margin-bottom: 10px;
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
                <a class="nav-link" href="{{ route('mesas.show', $mesa->id) }}">
                    <i class="fas fa-eye me-1"></i>Visualizar Mesa
                </a>
                <a class="nav-link" href="{{ route('mesas.index') }}">
                    <i class="fas fa-list me-1"></i>Todas as Mesas
                </a>
                <a class="nav-link" href="{{ route('dashboard') ?? '/' }}">
                    <i class="fas fa-home me-1"></i>Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <!-- Hero Section -->
        <div class="hero-section">
            <h1 class="hero-title">
                <i class="fas fa-edit me-3"></i>
                Editar Mesa
            </h1>
            <p class="mb-4">Atualize as informações da Mesa {{ $mesa->identificador }}</p>
        </div>

        <!-- Alerts -->
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background: rgba(220, 53, 69, 0.9); border: none; color: white; border-radius: 15px;">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Ops! Há alguns problemas:</strong>
                <ul class="mt-2 mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Formulário -->
            <div class="col-lg-8">
                <!-- Informações Atuais -->
                <div class="info-atual">
                    <h5>
                        <i class="fas fa-info-circle me-2"></i>
                        Informações Atuais
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Identificador:</strong> {{ $mesa->identificador }}
                        </div>
                        <div class="col-md-6">
                            <strong>Lugares:</strong> {{ $mesa->lugares }}
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <strong>Criado em:</strong> {{ $mesa->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong> 
                            @if($mesa->pedidos->count() > 0)
                                <span class="badge bg-danger">Ocupada</span>
                            @else
                                <span class="badge bg-success">Livre</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="form-container">
                    <h3 class="mb-4">
                        <i class="fas fa-chair me-2"></i>
                        Atualizar Informações
                    </h3>
                    
                    <form method="POST" action="{{ route('mesas.update', $mesa->id) }}" id="mesaForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="identificador" class="form-label">
                                <i class="fas fa-tag"></i>
                                Identificador da Mesa
                            </label>
                            <input type="text" 
                                   class="form-control @error('identificador') is-invalid @enderror" 
                                   id="identificador" 
                                   name="identificador" 
                                   value="{{ old('identificador', $mesa->identificador) }}" 
                                   placeholder="Ex: A1, Mesa 01, Varanda 3..."
                                   required
                                   autocomplete="off">
                            <div class="form-text">
                                Digite um identificador único para a mesa (letras, números ou texto)
                            </div>
                            @error('identificador')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="lugares" class="form-label">
                                <i class="fas fa-users"></i>
                                Número de Lugares
                            </label>
                            <input type="number" 
                                   class="form-control @error('lugares') is-invalid @enderror" 
                                   id="lugares" 
                                   name="lugares" 
                                   value="{{ old('lugares', $mesa->lugares) }}" 
                                   min="1" 
                                   max="20"
                                   required>
                            <div class="form-text">
                                Quantidade de pessoas que a mesa comporta (1 a 20 lugares)
                            </div>
                            @error('lugares')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        @if($mesa->pedidos->count() > 0)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Atenção:</strong> Esta mesa possui {{ $mesa->pedidos->count() }} pedido(s) ativo(s). 
                                As alterações não afetarão os pedidos existentes.
                            </div>
                        @endif
                        
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-submit me-3">
                                <i class="fas fa-save me-2"></i>
                                Atualizar Mesa
                            </button>
                            <a href="{{ route('mesas.show', $mesa->id) }}" class="btn-cancel">
                                <i class="fas fa-times me-2"></i>
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Preview e Informações -->
            <div class="col-lg-4">
                <div class="mesa-preview">
                    <div class="mesa-icon-preview">
                        <i class="fas fa-chair"></i>
                    </div>
                    <h4 id="previewIdentificador">Mesa {{ $mesa->identificador }}</h4>
                    <p id="previewLugares">{{ $mesa->lugares }} {{ $mesa->lugares == 1 ? 'lugar' : 'lugares' }}</p>
                    <small>Preview das alterações</small>
                </div>
                
                <!-- Histórico -->
                <div class="form-container">
                    <h5>
                        <i class="fas fa-history me-2 text-info"></i>
                        Histórico
                    </h5>
                    <div class="historico-item">
                        <span><i class="fas fa-plus text-success me-2"></i>Criada</span>
                        <span>{{ $mesa->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($mesa->updated_at != $mesa->created_at)
                        <div class="historico-item">
                            <span><i class="fas fa-edit text-warning me-2"></i>Última Edição</span>
                            <span>{{ $mesa->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                    @if($mesa->pedidos->count() > 0)
                        <div class="historico-item">
                            <span><i class="fas fa-clipboard text-info me-2"></i>Pedidos Ativos</span>
                            <span>{{ $mesa->pedidos->count() }}</span>
                        </div>
                    @endif
                </div>
                
                <!-- Ações Rápidas -->
                <div class="form-container">
                    <h5>
                        <i class="fas fa-bolt me-2 text-warning"></i>
                        Ações Rápidas
                    </h5>
                    <div class="d-grid gap-2">
                        <a href="{{ route('mesas.show', $mesa->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-2"></i>
                            Visualizar Mesa
                        </a>
                        @if($mesa->pedidos->count() == 0)
                            <button class="btn btn-outline-danger" onclick="confirmarExclusao({{ $mesa->id }}, '{{ $mesa->identificador }}')">
                                <i class="fas fa-trash me-2"></i>
                                Excluir Mesa
                            </button>
                        @endif
                    </div>
                </div>
            </div>
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
        // Preview em tempo real
        document.getElementById('identificador').addEventListener('input', function() {
            const valor = this.value || '{{ $mesa->identificador }}';
            document.getElementById('previewIdentificador').textContent = `Mesa ${valor}`;
        });
        
        document.getElementById('lugares').addEventListener('input', function() {
            const valor = this.value || {{ $mesa->lugares }};
            const texto = valor == 1 ? 'lugar' : 'lugares';
            document.getElementById('previewLugares').textContent = `${valor} ${texto}`;
        });
        
        function confirmarExclusao(id, identificador) {
            document.getElementById('mesaIdentificadorExcluir').textContent = identificador;
            document.getElementById('formExcluir').action = `/mesas/${id}`;
            new bootstrap.Modal(document.getElementById('modalExcluir')).show();
        }
        
        // Validação em tempo real
        document.getElementById('mesaForm').addEventListener('submit', function(e) {
            const identificador = document.getElementById('identificador').value.trim();
            const lugares = document.getElementById('lugares').value;
            
            if (!identificador) {
                e.preventDefault();
                alert('Por favor, informe o identificador da mesa.');
                document.getElementById('identificador').focus();
                return;
            }
            
            if (!lugares || lugares < 1) {
                e.preventDefault();
                alert('Por favor, informe um número válido de lugares (mínimo 1).');
                document.getElementById('lugares').focus();
                return;
            }
        });
        
        // Auto-hide alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 8000);
    </script>
</body>
</html>
