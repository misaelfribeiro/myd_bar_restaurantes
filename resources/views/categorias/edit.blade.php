<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editar {{ $categoria->nome }} - Sistema Bar/Restaurante</title>
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
        
        .info-card {
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid rgba(0, 0, 0, 0.1);
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
        .icon-products { background: linear-gradient(135deg, #10b981, #059669); }
        .icon-updated { background: linear-gradient(135deg, #06b6d4, #0891b2); }
        
        .form-help {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 5px;
        }
        
        .preview-card {
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
            border-radius: 15px;
            padding: 25px;
            margin-top: 25px;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .preview-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin: 0 auto 15px auto;
        }
        
        .preview-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1f2937;
            text-align: center;
            margin-bottom: 8px;
        }
        
        .preview-desc {
            color: #6b7280;
            text-align: center;
            font-style: italic;
        }
        
        .char-counter {
            text-align: right;
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 5px;
        }
        
        .char-counter.warning {
            color: #f59e0b;
        }
        
        .char-counter.danger {
            color: #ef4444;
        }
        
        .current-info {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .validation-feedback {
            display: none;
            font-size: 0.875rem;
            margin-top: 5px;
        }
        
        .validation-feedback.show {
            display: block;
        }
        
        .validation-feedback.valid {
            color: #059669;
        }
        
        .validation-feedback.invalid {
            color: #dc2626;
        }
        
        .changes-indicator {
            background: rgba(251, 191, 36, 0.1);
            border: 1px solid rgba(251, 191, 36, 0.3);
            color: #92400e;
            border-radius: 8px;
            padding: 10px 15px;
            margin-bottom: 20px;
            display: none;
        }
        
        .changes-indicator.show {
            display: block;
        }
        
        .produtos-warning {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #dc2626;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .form-card { padding: 25px; }
            .hero-section { padding: 20px; }
            .info-item { flex-direction: column; text-align: center; }
            .info-icon { margin-right: 0; margin-bottom: 8px; }
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
                <a class="nav-link" href="{{ route('categorias.index') }}">
                    <i class="fas fa-tags me-1"></i>Categorias
                </a>
                <a class="nav-link" href="{{ route('categorias.show', $categoria) }}">
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
                Editar Categoria
            </h1>
            <p class="lead mb-0">
                Atualize as informações da categoria "{{ $categoria->nome }}"
            </p>
        </div>

        <!-- Informações Atuais -->
        <div class="info-card">
            <h6 class="mb-3">
                <i class="fas fa-info-circle me-2"></i>
                Informações Atuais
            </h6>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="info-item">
                        <div class="info-icon icon-id">
                            <i class="fas fa-hashtag"></i>
                        </div>
                        <div>
                            <small class="text-muted">ID</small>
                            <div class="fw-bold">#{{ $categoria->id }}</div>
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
                            <div class="fw-bold">{{ $categoria->created_at->format('d/m/Y') }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="info-item">
                        <div class="info-icon icon-products">
                            <i class="fas fa-box"></i>
                        </div>
                        <div>
                            <small class="text-muted">Produtos</small>
                            <div class="fw-bold">{{ $categoria->produtos->count() }} item{{ $categoria->produtos->count() != 1 ? 's' : '' }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="info-item">
                        <div class="info-icon icon-updated">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <small class="text-muted">Atualizado</small>
                            <div class="fw-bold">{{ $categoria->updated_at->format('d/m/Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Formulário -->
                <div class="form-card">
                    <h5 class="mb-4">
                        <i class="fas fa-edit me-2"></i>
                        Editar Informações
                    </h5>

                    @if($categoria->produtos->count() > 0)
                        <div class="produtos-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Atenção:</strong> Esta categoria possui {{ $categoria->produtos->count() }} produto(s) vinculado(s). 
                            Mudanças no nome podem afetar a organização dos produtos.
                        </div>
                    @endif

                    <div class="changes-indicator" id="changesIndicator">
                        <i class="fas fa-info-circle me-2"></i>
                        Você tem alterações não salvas. Clique em "Salvar Alterações" para aplicá-las.
                    </div>

                    <form action="{{ route('categorias.update', $categoria) }}" method="POST" id="categoriaForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Nome da Categoria -->
                        <div class="mb-4">
                            <label for="nome" class="form-label">
                                <i class="fas fa-tag me-1"></i>
                                Nome da Categoria *
                            </label>
                            <div class="current-info">
                                <small class="text-muted">Valor atual:</small>
                                <strong class="ms-2">{{ $categoria->nome }}</strong>
                            </div>
                            <input type="text" 
                                   class="form-control" 
                                   id="nome" 
                                   name="nome" 
                                   value="{{ old('nome', $categoria->nome) }}"
                                   placeholder="Digite o novo nome da categoria..."
                                   maxlength="255"
                                   required
                                   autocomplete="off">
                            <div class="form-help">
                                O nome será usado para organizar seus produtos. Deve ser único e descritivo.
                            </div>
                            <div class="char-counter">
                                <span id="nomeCounter">{{ strlen($categoria->nome) }}</span>/255 caracteres
                            </div>
                            <div class="validation-feedback" id="nomeValidation"></div>
                        </div>

                        <!-- Descrição da Categoria -->
                        <div class="mb-4">
                            <label for="descricao" class="form-label">
                                <i class="fas fa-align-left me-1"></i>
                                Descrição (Opcional)
                            </label>
                            @if($categoria->descricao)
                                <div class="current-info">
                                    <small class="text-muted">Valor atual:</small>
                                    <strong class="ms-2">{{ $categoria->descricao }}</strong>
                                </div>
                            @else
                                <div class="current-info">
                                    <small class="text-muted">Valor atual:</small>
                                    <em class="ms-2 text-muted">Nenhuma descrição definida</em>
                                </div>
                            @endif
                            <textarea class="form-control" 
                                      id="descricao" 
                                      name="descricao" 
                                      rows="3"
                                      placeholder="Digite uma nova descrição ou mantenha vazio para remover..."
                                      maxlength="500">{{ old('descricao', $categoria->descricao) }}</textarea>
                            <div class="form-help">
                                Ajude sua equipe a entender melhor esta categoria com uma descrição clara.
                            </div>
                            <div class="char-counter">
                                <span id="descricaoCounter">{{ strlen($categoria->descricao ?? '') }}</span>/500 caracteres
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('categorias.show', $categoria) }}" class="btn btn-outline-gradient me-2">
                                    <i class="fas fa-eye me-2"></i>
                                    Visualizar
                                </a>
                                <a href="{{ route('categorias.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Voltar à Lista
                                </a>
                            </div>
                            <button type="submit" class="btn btn-gradient" id="submitBtn">
                                <i class="fas fa-save me-2"></i>
                                Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Preview -->
                <div class="form-card">
                    <h6 class="mb-3">
                        <i class="fas fa-eye me-2"></i>
                        Preview das Alterações
                    </h6>
                    
                    <div class="preview-card">
                        <div class="preview-icon">
                            <i class="fas fa-tag"></i>
                        </div>
                        <div class="preview-name" id="previewNome">{{ $categoria->nome }}</div>
                        <div class="preview-desc" id="previewDesc">
                            {{ $categoria->descricao ?: 'Sem descrição definida' }}
                        </div>
                    </div>
                </div>

                <!-- Produtos Vinculados -->
                @if($categoria->produtos->count() > 0)
                    <div class="form-card">
                        <h6 class="mb-3">
                            <i class="fas fa-link me-2"></i>
                            Produtos Vinculados ({{ $categoria->produtos->count() }})
                        </h6>
                        
                        <div class="list-group">
                            @foreach($categoria->produtos->take(5) as $produto)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">{{ $produto->nome }}</div>
                                        <small class="text-muted">R$ {{ number_format($produto->preco, 2, ',', '.') }}</small>
                                    </div>
                                    <span class="badge bg-{{ $produto->ativo ? 'success' : 'secondary' }} rounded-pill">
                                        {{ $produto->ativo ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </div>
                            @endforeach
                            
                            @if($categoria->produtos->count() > 5)
                                <div class="list-group-item text-center text-muted">
                                    <small>+{{ $categoria->produtos->count() - 5 }} produto(s) adicional(is)</small>
                                </div>
                            @endif
                        </div>
                        
                        <div class="mt-3 text-center">
                            <a href="{{ route('categorias.show', $categoria) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>
                                Ver Todos os Produtos
                            </a>
                        </div>
                    </div>
                @else
                    <div class="form-card">
                        <h6 class="mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Status da Categoria
                        </h6>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-box-open me-2"></i>
                            Esta categoria não possui produtos vinculados ainda.
                        </div>
                        
                        <div class="text-center">
                            <a href="{{ route('produtos.create') }}" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-plus me-1"></i>
                                Adicionar Produto
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nomeInput = document.getElementById('nome');
            const descricaoInput = document.getElementById('descricao');
            const nomeCounter = document.getElementById('nomeCounter');
            const descricaoCounter = document.getElementById('descricaoCounter');
            const previewNome = document.getElementById('previewNome');
            const previewDesc = document.getElementById('previewDesc');
            const nomeValidation = document.getElementById('nomeValidation');
            const submitBtn = document.getElementById('submitBtn');
            const changesIndicator = document.getElementById('changesIndicator');            // Valores originais
            const originalNome = {{ json_encode($categoria->nome) }};
            const originalDescricao = {{ json_encode($categoria->descricao ?? '') }};

            // Atualizar preview e detectar mudanças
            function updatePreview() {
                const nome = nomeInput.value.trim();
                const descricao = descricaoInput.value.trim();

                previewNome.textContent = nome || 'Nome da Categoria';
                previewDesc.textContent = descricao || 'Sem descrição definida';
                
                // Atualizar counters
                nomeCounter.textContent = nomeInput.value.length;
                descricaoCounter.textContent = descricaoInput.value.length;
                
                // Atualizar cores dos counters
                updateCounterColor(nomeCounter, nomeInput.value.length, 255);
                updateCounterColor(descricaoCounter, descricaoInput.value.length, 500);
                
                // Validar nome
                validateNome(nome);
                
                // Detectar mudanças
                detectChanges();
            }

            function updateCounterColor(counter, length, max) {
                const parent = counter.parentElement;
                parent.classList.remove('warning', 'danger');
                
                const percentage = (length / max) * 100;
                if (percentage >= 90) {
                    parent.classList.add('danger');
                } else if (percentage >= 75) {
                    parent.classList.add('warning');
                }
            }

            function validateNome(nome) {
                const validation = document.getElementById('nomeValidation');
                
                if (nome.length === 0) {
                    validation.textContent = '✗ O nome é obrigatório';
                    validation.classList.add('show', 'invalid');
                    validation.classList.remove('valid');
                    return false;
                }
                
                if (nome.length < 2) {
                    validation.textContent = '✗ O nome deve ter pelo menos 2 caracteres';
                    validation.classList.add('show', 'invalid');
                    validation.classList.remove('valid');
                    return false;
                }
                
                if (nome.length > 255) {
                    validation.textContent = '✗ O nome não pode ter mais que 255 caracteres';
                    validation.classList.add('show', 'invalid');
                    validation.classList.remove('valid');
                    return false;
                }
                
                // Verificar caracteres especiais problemáticos
                const invalidChars = /[<>{}[\]\\]/;
                if (invalidChars.test(nome)) {
                    validation.textContent = '✗ O nome contém caracteres inválidos';
                    validation.classList.add('show', 'invalid');
                    validation.classList.remove('valid');
                    return false;
                }
                
                validation.textContent = '✓ Nome válido';
                validation.classList.add('show', 'valid');
                validation.classList.remove('invalid');
                return true;
            }

            function detectChanges() {
                const currentNome = nomeInput.value.trim();
                const currentDescricao = descricaoInput.value.trim();
                
                const hasChanges = currentNome !== originalNome || currentDescricao !== originalDescricao;
                
                if (hasChanges) {
                    changesIndicator.classList.add('show');
                    submitBtn.classList.add('btn-warning');
                    submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Salvar Alterações *';
                } else {
                    changesIndicator.classList.remove('show');
                    submitBtn.classList.remove('btn-warning');
                    submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Salvar Alterações';
                }
            }

            // Event listeners
            nomeInput.addEventListener('input', updatePreview);
            descricaoInput.addEventListener('input', updatePreview);

            // Validação do formulário
            document.getElementById('categoriaForm').addEventListener('submit', function(e) {
                const nome = nomeInput.value.trim();
                
                if (!validateNome(nome)) {
                    e.preventDefault();
                    nomeInput.focus();
                    return false;
                }
                
                // Desabilitar botão durante o envio
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Salvando...';
            });

            // Capitalizar primeira letra ao digitar
            nomeInput.addEventListener('input', function() {
                this.value = this.value.replace(/\b\w/g, function(char) {
                    return char.toUpperCase();
                });
            });

            // Animação inicial
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

            // Inicializar
            updatePreview();
        });
    </script>
</body>
</html>
