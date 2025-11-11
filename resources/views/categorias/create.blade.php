<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nova Categoria - Sistema Bar/Restaurante</title>
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
            margin-bottom: 15px;
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
        
        .tips-card {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .tip-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 10px;
        }
        
        .tip-item:last-child {
            margin-bottom: 0;
        }
        
        .tip-icon {
            color: #3b82f6;
            margin-right: 10px;
            margin-top: 2px;
        }
        
        .examples-section {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .example-item {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid #10b981;
        }
        
        .example-item:last-child {
            margin-bottom: 0;
        }
        
        .example-name {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .example-desc {
            color: #6b7280;
            font-size: 0.9rem;
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
        
        @media (max-width: 768px) {
            .form-card { padding: 25px; }
            .hero-section { padding: 20px; }
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
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Hero Section -->
        <div class="hero-section">
            <h1>
                <i class="fas fa-plus-circle me-3"></i>
                Criar Nova Categoria
            </h1>
            <p class="lead mb-0">
                Adicione uma nova categoria para organizar seus produtos
            </p>
        </div>

        <!-- Dicas -->
        <div class="tips-card">
            <h6 class="mb-3 text-primary">
                <i class="fas fa-lightbulb me-2"></i>
                Dicas para criar uma boa categoria
            </h6>
            <div class="tip-item">
                <i class="fas fa-check tip-icon"></i>
                <span>Use nomes claros e descritivos que facilitem a organização</span>
            </div>
            <div class="tip-item">
                <i class="fas fa-check tip-icon"></i>
                <span>Evite nomes muito longos ou complicados</span>
            </div>
            <div class="tip-item">
                <i class="fas fa-check tip-icon"></i>
                <span>Pense na descrição como uma forma de ajudar sua equipe a entender o uso da categoria</span>
            </div>
            <div class="tip-item">
                <i class="fas fa-check tip-icon"></i>
                <span>Considere como os produtos serão agrupados logicamente</span>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Formulário -->
                <div class="form-card">
                    <h5 class="mb-4">
                        <i class="fas fa-edit me-2"></i>
                        Informações da Categoria
                    </h5>

                    <form action="{{ route('categorias.store') }}" method="POST" id="categoriaForm">
                        @csrf
                        
                        <!-- Nome da Categoria -->
                        <div class="mb-4">
                            <label for="nome" class="form-label">
                                <i class="fas fa-tag me-1"></i>
                                Nome da Categoria *
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="nome" 
                                   name="nome" 
                                   placeholder="Ex: Bebidas, Pratos Principais, Sobremesas..."
                                   maxlength="255"
                                   required
                                   autocomplete="off">
                            <div class="form-help">
                                O nome será usado para organizar seus produtos. Deve ser único e descritivo.
                            </div>
                            <div class="char-counter">
                                <span id="nomeCounter">0</span>/255 caracteres
                            </div>
                            <div class="validation-feedback" id="nomeValidation"></div>
                        </div>

                        <!-- Descrição da Categoria -->
                        <div class="mb-4">
                            <label for="descricao" class="form-label">
                                <i class="fas fa-align-left me-1"></i>
                                Descrição (Opcional)
                            </label>
                            <textarea class="form-control" 
                                      id="descricao" 
                                      name="descricao" 
                                      rows="3"
                                      placeholder="Descreva o tipo de produtos que pertencem a esta categoria..."
                                      maxlength="500"></textarea>
                            <div class="form-help">
                                Adicione uma descrição para ajudar sua equipe a entender melhor esta categoria.
                            </div>
                            <div class="char-counter">
                                <span id="descricaoCounter">0</span>/500 caracteres
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('categorias.index') }}" class="btn btn-outline-gradient">
                                <i class="fas fa-arrow-left me-2"></i>
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-gradient" id="submitBtn">
                                <i class="fas fa-save me-2"></i>
                                Criar Categoria
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
                        Preview da Categoria
                    </h6>
                    
                    <div class="preview-card">
                        <div class="preview-icon">
                            <i class="fas fa-tag"></i>
                        </div>
                        <div class="preview-name" id="previewNome">Nome da Categoria</div>
                        <div class="preview-desc" id="previewDesc">Descrição aparecerá aqui...</div>
                    </div>
                </div>

                <!-- Exemplos -->
                <div class="examples-section">
                    <h6 class="mb-3 text-success">
                        <i class="fas fa-star me-2"></i>
                        Exemplos de Categorias
                    </h6>
                    
                    <div class="example-item">
                        <div class="example-name">Bebidas Alcoólicas</div>
                        <div class="example-desc">Cervejas, vinhos, coquetéis e destilados</div>
                    </div>
                    
                    <div class="example-item">
                        <div class="example-name">Pratos Principais</div>
                        <div class="example-desc">Pratos principais do cardápio, como carnes, peixes e massas</div>
                    </div>
                    
                    <div class="example-item">
                        <div class="example-name">Petiscos</div>
                        <div class="example-desc">Aperitivos e petiscos para compartilhar</div>
                    </div>
                    
                    <div class="example-item">
                        <div class="example-name">Sobremesas</div>
                        <div class="example-desc">Doces, bolos, sorvetes e sobremesas em geral</div>
                    </div>
                </div>
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

            // Atualizar preview em tempo real
            function updatePreview() {
                const nome = nomeInput.value.trim();
                const descricao = descricaoInput.value.trim();

                previewNome.textContent = nome || 'Nome da Categoria';
                previewDesc.textContent = descricao || 'Descrição aparecerá aqui...';
                
                // Atualizar counters
                nomeCounter.textContent = nomeInput.value.length;
                descricaoCounter.textContent = descricaoInput.value.length;
                
                // Atualizar cores dos counters
                updateCounterColor(nomeCounter, nomeInput.value.length, 255);
                updateCounterColor(descricaoCounter, descricaoInput.value.length, 500);
                
                // Validar nome
                validateNome(nome);
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
                    validation.textContent = '';
                    validation.classList.remove('show', 'valid', 'invalid');
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
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Criando...';
            });

            // Sugestões de nomes ao digitar
            nomeInput.addEventListener('input', function() {
                const value = this.value.toLowerCase();
                
                // Capitalizar primeira letra de cada palavra
                this.value = this.value.replace(/\b\w/g, function(char) {
                    return char.toUpperCase();
                });
            });

            // Animação inicial
            const cards = document.querySelectorAll('.form-card, .tips-card, .examples-section');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 200);
            });

            // Inicializar preview
            updatePreview();
        });
    </script>
</body>
</html>
