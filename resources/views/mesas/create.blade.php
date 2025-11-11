<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nova Mesa - Sistema Bar/Restaurante</title>
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
            background: linear-gradient(135deg, #28a745, #20c997);
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
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
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
            background: linear-gradient(135deg, #20c997, #17a085);
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
        
        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        
        .form-control.is-invalid {
            border-color: #dc3545;
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
                <i class="fas fa-plus-circle me-3"></i>
                Nova Mesa
            </h1>
            <p class="mb-4">Cadastre uma nova mesa para o seu restaurante</p>
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
                <div class="form-container">
                    <h3 class="mb-4">
                        <i class="fas fa-chair me-2"></i>
                        Informações da Mesa
                    </h3>
                    
                    <form method="POST" action="{{ route('mesas.store') }}" id="mesaForm">
                        @csrf
                        
                        <div class="form-group">
                            <label for="identificador" class="form-label">
                                <i class="fas fa-tag"></i>
                                Identificador da Mesa
                            </label>
                            <input type="text" 
                                   class="form-control @error('identificador') is-invalid @enderror" 
                                   id="identificador" 
                                   name="identificador" 
                                   value="{{ old('identificador') }}" 
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
                                   value="{{ old('lugares', 4) }}" 
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
                        
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-submit me-3">
                                <i class="fas fa-save me-2"></i>
                                Criar Mesa
                            </button>
                            <a href="{{ route('mesas.index') }}" class="btn-cancel">
                                <i class="fas fa-times me-2"></i>
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Preview -->
            <div class="col-lg-4">
                <div class="mesa-preview">
                    <div class="mesa-icon-preview">
                        <i class="fas fa-chair"></i>
                    </div>
                    <h4 id="previewIdentificador">Nova Mesa</h4>
                    <p id="previewLugares">4 lugares</p>
                    <small>Preview da mesa que será criada</small>
                </div>
                
                <!-- Dicas -->
                <div class="form-container">
                    <h5>
                        <i class="fas fa-lightbulb me-2 text-warning"></i>
                        Dicas
                    </h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Use identificadores únicos e fáceis de lembrar
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Considere a localização (Ex: "Varanda 1", "Salão A2")
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Defina o número correto de lugares
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info text-info me-2"></i>
                            Você poderá editar essas informações depois
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview em tempo real
        document.getElementById('identificador').addEventListener('input', function() {
            const valor = this.value || 'Nova Mesa';
            document.getElementById('previewIdentificador').textContent = `Mesa ${valor}`;
        });
        
        document.getElementById('lugares').addEventListener('input', function() {
            const valor = this.value || 4;
            const texto = valor == 1 ? 'lugar' : 'lugares';
            document.getElementById('previewLugares').textContent = `${valor} ${texto}`;
        });
        
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
