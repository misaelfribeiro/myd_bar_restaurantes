<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - MyD Bar & Restaurantes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        // Variáveis globais
        window.API_BASE = '/api';
        window.csrfToken = '{{ csrf_token() }}';
    </script>
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --admin-color: #dc3545;
            --gerente-color: #ffc107;
            --garcom-color: #28a745;
            --caixa-color: #17a2b8;
        }
        
        body {
            background: var(--primary-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            display: flex;
        }
        
        .left-side {
            flex: 1;
            padding: 40px;
            background: white;
        }
        
        .right-side {
            flex: 1;
            padding: 40px;
            background: var(--primary-gradient);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo i {
            font-size: 3rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .logo h2 {
            color: #333;
            margin-top: 10px;
            font-weight: 700;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 12px 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-login {
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            background: var(--primary-gradient);
            border: none;
            color: white;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }
        
        .role-card {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .role-card:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(5px);
        }
        
        .role-card.selected {
            background: rgba(255, 255, 255, 0.3);
            border-color: white;
        }
        
        .role-card h5 {
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .role-card small {
            opacity: 0.9;
        }
        
        .quick-login {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 10px;
            margin-top: 15px;
        }
        
        .quick-login small {
            display: block;
            margin-bottom: 5px;
            opacity: 0.8;
        }
        
        .alert-custom {
            border-radius: 10px;
            border: none;
        }
        
        .divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #ddd;
        }
        
        .divider span {
            background: white;
            padding: 0 15px;
            position: relative;
            color: #999;
        }
        
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }
            
            .right-side {
                order: -1;
            }
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
        }
        
        .feature-list li {
            padding: 10px 0;
            display: flex;
            align-items: center;
        }
        
        .feature-list li i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Lado Esquerdo - Formulário -->
        <div class="left-side">
            <div class="logo">
                <i class="fas fa-utensils"></i>
                <h2>MyD Bar & Restaurantes</h2>
                <p class="text-muted mb-0">Sistema de Gestão</p>
            </div>

            <div id="alertContainer"></div>

            <form id="loginForm">
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope me-2"></i>Email
                    </label>
                    <input type="email" class="form-control" id="email" placeholder="seu@email.com" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-2"></i>Senha
                    </label>
                    <input type="password" class="form-control" id="password" placeholder="Sua senha" required>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">
                        Lembrar-me
                    </label>
                </div>

                <button type="submit" class="btn btn-login w-100 mb-3">
                    <i class="fas fa-sign-in-alt me-2"></i>Entrar
                </button>

                <div class="text-center">
                    <a href="#" class="text-decoration-none">Esqueceu sua senha?</a>
                </div>
            </form>

            <div class="divider">
                <span>ou</span>
            </div>

            <div class="text-center">
                <button class="btn btn-outline-primary w-100" onclick="toggleDemoUsers()">
                    <i class="fas fa-users me-2"></i>Usar Usuários Demo
                </button>
            </div>

            <div id="demoUsers" class="mt-3" style="display: none;">
                <div class="quick-login">
                    <small><i class="fas fa-info-circle me-1"></i>Clique para fazer login rápido</small>
                    <div class="d-grid gap-2">
                        <button class="btn btn-sm btn-danger" onclick="quickLogin('admin@exemplo.com', '123456')">
                            <i class="fas fa-user-shield me-2"></i>Admin
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="quickLogin('gerente@exemplo.com', '123456')">
                            <i class="fas fa-user-tie me-2"></i>Gerente
                        </button>
                        <button class="btn btn-sm btn-success" onclick="quickLogin('garcom@exemplo.com', '123456')">
                            <i class="fas fa-concierge-bell me-2"></i>Garçom
                        </button>
                        <button class="btn btn-sm btn-info" onclick="quickLogin('caixa@exemplo.com', '123456')">
                            <i class="fas fa-cash-register me-2"></i>Caixa
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lado Direito - Informações -->
        <div class="right-side">
            <h3 class="mb-4">
                <i class="fas fa-shield-alt me-2"></i>
                Níveis de Acesso
            </h3>

            <div class="role-card" data-role="admin">
                <h5><i class="fas fa-user-shield me-2"></i>Administrador</h5>
                <small>Acesso total ao sistema, gerenciamento de usuários e configurações</small>
            </div>

            <div class="role-card" data-role="gerente">
                <h5><i class="fas fa-user-tie me-2"></i>Gerente</h5>
                <small>Gestão de produtos, categorias, mesas e relatórios</small>
            </div>

            <div class="role-card" data-role="garcom">
                <h5><i class="fas fa-concierge-bell me-2"></i>Garçom</h5>
                <small>Gerenciamento de pedidos, consulta de produtos e mesas</small>
            </div>

            <div class="role-card" data-role="caixa">
                <h5><i class="fas fa-cash-register me-2"></i>Caixa</h5>
                <small>Processamento de pagamentos e fechamento de contas</small>
            </div>

            <hr class="my-4" style="border-color: rgba(255,255,255,0.3)">

            <h5 class="mb-3">
                <i class="fas fa-check-circle me-2"></i>
                Recursos do Sistema
            </h5>
            <ul class="feature-list">
                <li>
                    <i class="fas fa-lock"></i>
                    <span>Autenticação segura com Sanctum</span>
                </li>
                <li>
                    <i class="fas fa-mobile-alt"></i>
                    <span>Interface responsiva e mobile</span>
                </li>
                <li>
                    <i class="fas fa-cloud"></i>
                    <span>Sincronização offline</span>
                </li>
                <li>
                    <i class="fas fa-chart-line"></i>
                    <span>Relatórios em tempo real</span>
                </li>
            </ul>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle demo users
        function toggleDemoUsers() {
            const demoDiv = document.getElementById('demoUsers');
            if (demoDiv) {
                demoDiv.style.display = demoDiv.style.display === 'none' ? 'block' : 'none';
            }
        }

        // Quick login
        function quickLogin(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
            // Trigger form submit
            const form = document.getElementById('loginForm');
            if (form) {
                form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
            }
        }

        // Show alert
        function showAlert(message, type = 'info') {
            const alertContainer = document.getElementById('alertContainer');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-custom alert-dismissible fade show`;
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            alertContainer.innerHTML = '';
            alertContainer.appendChild(alert);

            setTimeout(() => {
                alert.remove();
            }, 5000);
        }

        // Login form submission
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            console.log('Form submitted');

            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const submitBtn = this.querySelector('button[type="submit"]');

            if (!email || !password) {
                showAlert('Por favor, preencha todos os campos', 'warning');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Entrando...';

            try {
                const csrfToken = window.csrfToken || document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                console.log('Enviando login para:', email);
                
                const response = await fetch('/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        email: email,
                        password: password,
                        _token: csrfToken
                    })
                });

                console.log('Response status:', response.status);
                const data = await response.json();
                console.log('Response data:', data);

                if (response.ok && data.success) {
                    showAlert(`
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Login realizado com sucesso!</strong><br>
                        Bem-vindo, ${data.user.nome || 'Usuário'}!
                    `, 'success');

                    setTimeout(function() {
                        window.location.href = data.redirect || '/';
                    }, 1000);
                } else {
                    showAlert(`
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Erro ao fazer login</strong><br>
                        ${data.message || 'Email ou senha incorretos'}
                    `, 'danger');
                    
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-sign-in-alt me-2"></i>Entrar';
                }
            } catch (error) {
                console.error('Erro no login:', error);
                showAlert(`
                    <i class="fas fa-times-circle me-2"></i>
                    <strong>Erro de conexão</strong><br>
                    ${error.message || 'Não foi possível conectar ao servidor'}
                `, 'danger');
                
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-sign-in-alt me-2"></i>Entrar';
            }
        });

        // Role card selection (visual feedback)
        document.querySelectorAll('.role-card').forEach(function(card) {
            card.addEventListener('click', function() {
                document.querySelectorAll('.role-card').forEach(function(c) {
                    c.classList.remove('selected');
                });
                this.classList.add('selected');
            });
        });

        // Log para debug
        console.log('Login page loaded, CSRF token:', window.csrfToken);
    </script>
</body>
</html>
