<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Novo Pedido - Sistema Bar/Restaurante</title>
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
        
        .mesa-card {
            border: 2px solid #e5e7eb;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
            background: #f9fafb;
        }
        
        .mesa-card:hover {
            border-color: #6366f1;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.15);
        }
        
        .mesa-card.selected {
            border-color: #6366f1;
            background: rgba(99, 102, 241, 0.05);
        }
        
        .mesa-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-livre { background: #dcfce7; color: #166534; }
        .status-ocupada { background: #fee2e2; color: #991b1b; }
        
        .preview-card {
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
            border-radius: 15px;
            padding: 25px;
            margin-top: 25px;
        }
        
        .preview-item {
            display: flex;
            justify-content: between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .preview-item:last-child {
            border-bottom: none;
        }
        
        .garcom-card {
            border: 2px solid #e5e7eb;
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
            background: #f9fafb;
        }
        
        .garcom-card:hover {
            border-color: #8b5cf6;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(139, 92, 246, 0.15);
        }
        
        .garcom-card.selected {
            border-color: #8b5cf6;
            background: rgba(139, 92, 246, 0.05);
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        
        .step {
            display: flex;
            align-items: center;
            margin: 0 15px;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: 600;
        }
        
        .step-active {
            background: #6366f1;
            color: white;
        }
        
        .step-inactive {
            background: #e5e7eb;
            color: #9ca3af;
        }
        
        .alert-info {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
            color: #1e40af;
        }
        
        @media (max-width: 768px) {
            .form-card { padding: 25px; }
            .mesa-grid { grid-template-columns: 1fr; }
            .step-indicator { flex-direction: column; gap: 10px; }
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
                <i class="fas fa-plus-circle me-3"></i>
                Criar Novo Pedido
            </h1>
            <p class="lead mb-0">
                Preencha as informações para iniciar um novo pedido
            </p>
        </div>

        <form action="{{ route('pedidos.store') }}" method="POST" id="pedidoForm">
            @csrf
            
            <!-- Indicador de Passos -->
            <div class="step-indicator">
                <div class="step">
                    <div class="step-number step-active">1</div>
                    <span class="text-white">Selecionar Mesa</span>
                </div>
                <div class="step">
                    <div class="step-number step-inactive">2</div>
                    <span class="text-white-50">Escolher Garçom</span>
                </div>
                <div class="step">
                    <div class="step-number step-inactive">3</div>
                    <span class="text-white-50">Finalizar</span>
                </div>
            </div>

            <!-- Seleção de Mesa -->
            <div class="form-card" id="step1">
                <h5 class="mb-4">
                    <i class="fas fa-table me-2"></i>
                    Selecionar Mesa
                </h5>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Escolha uma mesa disponível para o pedido. Mesas ocupadas são indicadas em vermelho.
                </div>
                
                <input type="hidden" name="mesa_id" id="mesa_id" required>
                
                <div class="row">
                    @forelse($mesas as $mesa)
                        <div class="col-md-6 col-lg-4">
                            <div class="mesa-card" onclick="selecionarMesa({{ $mesa->id }}, '{{ $mesa->identificador }}', {{ $mesa->lugares }}, '{{ $mesa->pedidos->where("status", "!=", "entregue")->count() > 0 ? "ocupada" : "livre" }}')">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">
                                        <i class="fas fa-table me-2"></i>
                                        Mesa {{ $mesa->identificador }}
                                    </h6>
                                    <span class="mesa-status status-{{ $mesa->pedidos->where('status', '!=', 'entregue')->count() > 0 ? 'ocupada' : 'livre' }}">
                                        {{ $mesa->pedidos->where('status', '!=', 'entregue')->count() > 0 ? 'Ocupada' : 'Livre' }}
                                    </span>
                                </div>
                                <div class="text-muted">
                                    <i class="fas fa-users me-1"></i>
                                    {{ $mesa->lugares }} lugares
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-table fa-2x mb-3"></i>
                                <p>Nenhuma mesa cadastrada ainda</p>
                                <a href="{{ route('mesas.create') }}" class="btn btn-gradient">
                                    <i class="fas fa-plus me-2"></i>
                                    Cadastrar Mesa
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
                
                <div class="text-end mt-4">
                    <button type="button" class="btn btn-gradient" id="nextStep1" onclick="proximoPasso(2)" disabled>
                        Próximo Passo
                        <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

            <!-- Seleção de Garçom -->
            <div class="form-card" id="step2" style="display: none;">
                <h5 class="mb-4">
                    <i class="fas fa-user me-2"></i>
                    Escolher Garçom
                </h5>
                
                <input type="hidden" name="usuario_id" id="usuario_id" required>
                
                <div class="row">
                    @forelse($usuarios as $usuario)
                        <div class="col-md-6">
                            <div class="garcom-card" onclick="selecionarGarcom({{ $usuario->id }}, '{{ $usuario->nome }}', '{{ $usuario->role }}')">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">{{ $usuario->nome }}</h6>
                                        <small class="text-muted text-capitalize">{{ $usuario->role }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-user fa-2x mb-3"></i>
                                <p>Nenhum usuário cadastrado ainda</p>
                                <a href="{{ route('usuarios.create') }}" class="btn btn-gradient">
                                    <i class="fas fa-plus me-2"></i>
                                    Cadastrar Usuário
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-outline-gradient" onclick="voltarPasso(1)">
                        <i class="fas fa-arrow-left me-2"></i>
                        Voltar
                    </button>
                    <button type="button" class="btn btn-gradient" id="nextStep2" onclick="proximoPasso(3)" disabled>
                        Próximo Passo
                        <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

            <!-- Confirmação -->
            <div class="form-card" id="step3" style="display: none;">
                <h5 class="mb-4">
                    <i class="fas fa-check-circle me-2"></i>
                    Confirmar Pedido
                </h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status Inicial</label>
                            <select class="form-select" name="status" id="status" required>
                                <option value="pendente" selected>Pendente</option>
                                <option value="em_preparo">Em Preparo</option>
                                <option value="pronto">Pronto</option>
                                <option value="entregue">Entregue</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Preview do Pedido -->
                <div class="preview-card">
                    <h6 class="mb-3">
                        <i class="fas fa-eye me-2"></i>
                        Resumo do Pedido
                    </h6>
                    
                    <div class="preview-item">
                        <span class="fw-bold">Mesa:</span>
                        <span id="previewMesa">-</span>
                    </div>
                    
                    <div class="preview-item">
                        <span class="fw-bold">Garçom:</span>
                        <span id="previewGarcom">-</span>
                    </div>
                    
                    <div class="preview-item">
                        <span class="fw-bold">Status:</span>
                        <span id="previewStatus">Pendente</span>
                    </div>
                    
                    <div class="preview-item">
                        <span class="fw-bold">Total Inicial:</span>
                        <span class="text-success">R$ 0,00</span>
                    </div>
                </div>
                
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Após criar o pedido, você poderá adicionar itens na página de detalhes.
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-outline-gradient" onclick="voltarPasso(2)">
                        <i class="fas fa-arrow-left me-2"></i>
                        Voltar
                    </button>
                    <button type="submit" class="btn btn-gradient">
                        <i class="fas fa-save me-2"></i>
                        Criar Pedido
                    </button>
                </div>
            </div>
        </form>

        <!-- Botão Cancelar -->
        <div class="text-center mb-4">
            <a href="{{ route('pedidos.index') }}" class="btn btn-outline-light">
                <i class="fas fa-times me-2"></i>
                Cancelar e Voltar
            </a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        let mesaSelecionada = null;
        let garcomSelecionado = null;

        function selecionarMesa(id, identificador, lugares, status) {
            if (status === 'ocupada') {
                alert('Esta mesa já possui um pedido ativo. Por favor, escolha outra mesa.');
                return;
            }

            // Remover seleção anterior
            document.querySelectorAll('.mesa-card').forEach(card => {
                card.classList.remove('selected');
            });

            // Adicionar seleção atual
            event.target.closest('.mesa-card').classList.add('selected');

            // Salvar dados
            mesaSelecionada = { id, identificador, lugares };
            document.getElementById('mesa_id').value = id;
            document.getElementById('nextStep1').disabled = false;

            // Atualizar preview
            atualizarPreview();
        }

        function selecionarGarcom(id, nome, role) {
            // Remover seleção anterior
            document.querySelectorAll('.garcom-card').forEach(card => {
                card.classList.remove('selected');
            });

            // Adicionar seleção atual
            event.target.closest('.garcom-card').classList.add('selected');

            // Salvar dados
            garcomSelecionado = { id, nome, role };
            document.getElementById('usuario_id').value = id;
            document.getElementById('nextStep2').disabled = false;

            // Atualizar preview
            atualizarPreview();
        }

        function proximoPasso(step) {
            // Esconder todos os steps
            document.querySelectorAll('[id^="step"]').forEach(el => {
                el.style.display = 'none';
            });

            // Mostrar step atual
            document.getElementById('step' + step).style.display = 'block';

            // Atualizar indicadores
            document.querySelectorAll('.step-number').forEach((el, index) => {
                if (index + 1 <= step) {
                    el.classList.remove('step-inactive');
                    el.classList.add('step-active');
                    el.nextElementSibling.classList.remove('text-white-50');
                    el.nextElementSibling.classList.add('text-white');
                } else {
                    el.classList.remove('step-active');
                    el.classList.add('step-inactive');
                    el.nextElementSibling.classList.remove('text-white');
                    el.nextElementSibling.classList.add('text-white-50');
                }
            });

            atualizarPreview();
        }

        function voltarPasso(step) {
            proximoPasso(step);
        }

        function atualizarPreview() {
            if (mesaSelecionada) {
                document.getElementById('previewMesa').textContent = 
                    `Mesa ${mesaSelecionada.identificador} (${mesaSelecionada.lugares} lugares)`;
            }

            if (garcomSelecionado) {
                document.getElementById('previewGarcom').textContent = 
                    `${garcomSelecionado.nome} (${garcomSelecionado.role})`;
            }

            const status = document.getElementById('status')?.value || 'pendente';
            document.getElementById('previewStatus').textContent = 
                status.charAt(0).toUpperCase() + status.slice(1).replace('_', ' ');
        }

        // Listener para mudança de status
        document.getElementById('status')?.addEventListener('change', atualizarPreview);

        // Animação inicial
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.mesa-card, .garcom-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 50);
            });
        });

        // Validação do formulário
        document.getElementById('pedidoForm').addEventListener('submit', function(e) {
            if (!mesaSelecionada || !garcomSelecionado) {
                e.preventDefault();
                alert('Por favor, selecione uma mesa e um garçom antes de continuar.');
            }
        });
    </script>
</body>
</html>
