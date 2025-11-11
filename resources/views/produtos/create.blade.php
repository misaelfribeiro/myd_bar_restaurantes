<!DOCTYPE html>
<html lang="pt-BR">
<head>    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Novo Produto - Sistema Bar/Restaurante</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            border-radius: 0.5rem;
        }
        .card-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border-radius: 0.5rem 0.5rem 0 0 !important;
        }
        .btn-primary {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
        }
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #495057);
            border: none;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        .form-control:focus, .form-select:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .invalid-feedback {
            display: block;
        }
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        @media (max-width: 576px) {
            .header-actions {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <div class="header-actions">
                            <h4 class="mb-0">
                                <i class="fas fa-plus-circle me-2"></i>
                                Novo Produto
                            </h4>
                            <a href="{{ route('produtos.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>
                                Voltar
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <h6 class="alert-heading">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Erro na validação
                                </h6>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('produtos.store') }}" method="POST" id="formProduto">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="nome" class="form-label">
                                        <i class="fas fa-utensils me-1"></i>
                                        Nome do Produto *
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('nome') is-invalid @enderror" 
                                           id="nome" 
                                           name="nome" 
                                           value="{{ old('nome') }}" 
                                           placeholder="Ex: Hambúrguer Especial"
                                           maxlength="255"
                                           required>
                                    @error('nome')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Máximo 255 caracteres
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="preco" class="form-label">
                                        <i class="fas fa-dollar-sign me-1"></i>
                                        Preço *
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" 
                                               class="form-control @error('preco') is-invalid @enderror" 
                                               id="preco" 
                                               name="preco" 
                                               value="{{ old('preco') }}" 
                                               step="0.01" 
                                               min="0" 
                                               max="9999.99"
                                               placeholder="0,00"
                                               required>
                                        @error('preco')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="categoria_id" class="form-label">
                                        <i class="fas fa-tags me-1"></i>
                                        Categoria *
                                    </label>
                                    <select class="form-select @error('categoria_id') is-invalid @enderror" 
                                            id="categoria_id" 
                                            name="categoria_id" 
                                            required>
                                        <option value="">Selecione uma categoria</option>
                                        @foreach($categorias as $categoria)
                                            <option value="{{ $categoria->id }}" 
                                                    {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                                {{ $categoria->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('categoria_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="ativo" class="form-label">
                                        <i class="fas fa-toggle-on me-1"></i>
                                        Status
                                    </label>
                                    <select class="form-select @error('ativo') is-invalid @enderror" 
                                            id="ativo" 
                                            name="ativo">
                                        <option value="1" {{ old('ativo', 1) == 1 ? 'selected' : '' }}>
                                            <i class="fas fa-check-circle"></i> Ativo
                                        </option>
                                        <option value="0" {{ old('ativo') == 0 ? 'selected' : '' }}>
                                            <i class="fas fa-times-circle"></i> Inativo
                                        </option>
                                    </select>
                                    @error('ativo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="descricao" class="form-label">
                                    <i class="fas fa-align-left me-1"></i>
                                    Descrição
                                </label>
                                <textarea class="form-control @error('descricao') is-invalid @enderror" 
                                          id="descricao" 
                                          name="descricao" 
                                          rows="4" 
                                          placeholder="Descreva o produto (ingredientes, características, etc.)"
                                          maxlength="1000">{{ old('descricao') }}</textarea>
                                @error('descricao')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <span id="charCount">0</span>/1000 caracteres
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('produtos.index') }}" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-times me-1"></i>
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary" id="btnSalvar">
                                    <i class="fas fa-save me-1"></i>
                                    <span class="btn-text">Salvar Produto</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const descricaoTextarea = document.getElementById('descricao');
            const charCount = document.getElementById('charCount');
            const btnSalvar = document.getElementById('btnSalvar');
            const form = document.getElementById('formProduto');

            // Contador de caracteres
            function updateCharCount() {
                const count = descricaoTextarea.value.length;
                charCount.textContent = count;
                
                if (count > 900) {
                    charCount.style.color = '#dc3545';
                } else if (count > 800) {
                    charCount.style.color = '#ffc107';
                } else {
                    charCount.style.color = '#6c757d';
                }
            }

            descricaoTextarea.addEventListener('input', updateCharCount);
            updateCharCount(); // Inicializa contador

            // Formatação do preço
            const precoInput = document.getElementById('preco');
            precoInput.addEventListener('input', function() {
                let value = this.value;
                if (value && !isNaN(value)) {
                    // Remove caracteres inválidos
                    value = parseFloat(value);
                    if (value > 9999.99) {
                        this.value = 9999.99;
                    }
                }
            });

            // Loading no botão ao submeter
            form.addEventListener('submit', function() {
                btnSalvar.disabled = true;
                btnSalvar.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Salvando...';
            });

            // Validação em tempo real do nome
            const nomeInput = document.getElementById('nome');
            let timeoutId;
            
            nomeInput.addEventListener('input', function() {
                clearTimeout(timeoutId);
                const feedback = this.parentNode.querySelector('.invalid-feedback.ajax-validation');
                
                if (feedback) {
                    feedback.remove();
                }
                
                if (this.value.length >= 3) {
                    timeoutId = setTimeout(() => {
                        // Aqui poderia fazer validação AJAX para verificar duplicatas
                        // Por simplicidade, não implementamos agora
                    }, 500);
                }
            });
        });
    </script>
</body>
</html>
