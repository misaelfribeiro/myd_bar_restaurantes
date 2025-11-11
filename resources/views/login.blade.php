<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema Bar & Restaurante</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: rgba(255,255,255,0.95);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 2rem;
        }
        .header p {
            color: #666;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }
        input[type="email"], input[type="password"], input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        input[type="email"]:focus, input[type="password"]:focus, input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.3s ease;
            margin-bottom: 10px;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        .toggle-btn {
            background: transparent;
            color: #667eea;
            border: 2px solid #667eea;
            background: white;
        }
        .message {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .token-display {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            word-break: break-all;
            font-family: monospace;
            font-size: 12px;
            margin-top: 15px;
        }
        .hidden {
            display: none;
        }
        .user-info {
            background: #e8f5e8;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .user-info h3 {
            color: #28a745;
            margin-bottom: 10px;
        }
        .api-test {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .api-test h4 {
            margin-bottom: 10px;
            color: #333;
        }
        .api-result {
            background: #fff;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-top: 10px;
            font-family: monospace;
            font-size: 12px;
            max-height: 200px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Sistema de Autenticação</h1>
            <p>Teste a autenticação Sanctum</p>
        </div>

        <div id="message"></div>

        <!-- Formulário de Login/Registro -->
        <form id="authForm">
            <div class="form-group">
                <label for="nome">Nome (apenas para registro):</label>
                <input type="text" id="nome" placeholder="Seu nome completo">
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" placeholder="seu@email.com" required>
            </div>
            
            <div class="form-group">
                <label for="password">Senha:</label>
                <input type="password" id="password" placeholder="Sua senha" required>
            </div>
            
            <div class="form-group" id="confirmGroup">
                <label for="passwordConfirm">Confirmar Senha:</label>
                <input type="password" id="passwordConfirm" placeholder="Confirme sua senha">
            </div>

            <button type="submit" class="btn" id="submitBtn">Fazer Login</button>
            <button type="button" class="btn toggle-btn" id="toggleBtn">Criar Conta</button>
        </form>

        <!-- Informações do usuário logado -->
        <div id="userInfo" class="hidden">
            <div class="user-info">
                <h3>✅ Usuário Logado</h3>
                <p><strong>Nome:</strong> <span id="userName"></span></p>
                <p><strong>Email:</strong> <span id="userEmail"></span></p>
                <p><strong>Token:</strong> <span id="tokenPreview"></span></p>
            </div>
            
            <button class="btn" id="logoutBtn">Fazer Logout</button>
            <button class="btn toggle-btn" id="testApiBtn">Testar APIs Protegidas</button>
        </div>

        <!-- Testes de API -->
        <div id="apiTest" class="api-test hidden">
            <h4>🧪 Teste de APIs Protegidas</h4>
            <button class="btn" onclick="testProtectedApi('/api/auth/me', 'GET')">Testar /auth/me</button>
            <button class="btn" onclick="testProtectedApi('/api/produtos', 'GET')">Testar /produtos</button>
            <button class="btn" onclick="testProtectedApi('/api/categorias', 'GET')">Testar /categorias</button>
            <div id="apiResult" class="api-result"></div>
        </div>
    </div>

    <script>
        let currentToken = null;
        let isLogin = true;
        
        const elements = {
            form: document.getElementById('authForm'),
            message: document.getElementById('message'),
            submitBtn: document.getElementById('submitBtn'),
            toggleBtn: document.getElementById('toggleBtn'),
            nome: document.getElementById('nome'),
            email: document.getElementById('email'),
            password: document.getElementById('password'),
            passwordConfirm: document.getElementById('passwordConfirm'),
            confirmGroup: document.getElementById('confirmGroup'),
            userInfo: document.getElementById('userInfo'),
            userName: document.getElementById('userName'),
            userEmail: document.getElementById('userEmail'),
            tokenPreview: document.getElementById('tokenPreview'),
            logoutBtn: document.getElementById('logoutBtn'),
            testApiBtn: document.getElementById('testApiBtn'),
            apiTest: document.getElementById('apiTest'),
            apiResult: document.getElementById('apiResult')
        };

        // Toggle entre login e registro
        elements.toggleBtn.addEventListener('click', () => {
            isLogin = !isLogin;
            
            if (isLogin) {
                elements.submitBtn.textContent = 'Fazer Login';
                elements.toggleBtn.textContent = 'Criar Conta';
                elements.nome.style.display = 'none';
                elements.confirmGroup.style.display = 'none';
                elements.nome.required = false;
                elements.passwordConfirm.required = false;
            } else {
                elements.submitBtn.textContent = 'Criar Conta';
                elements.toggleBtn.textContent = 'Fazer Login';
                elements.nome.style.display = 'block';
                elements.confirmGroup.style.display = 'block';
                elements.nome.required = true;
                elements.passwordConfirm.required = true;
            }
            
            showMessage('', '');
        });

        // Submissão do formulário
        elements.form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = elements.email.value;
            const password = elements.password.value;
            const nome = elements.nome.value;
            const passwordConfirm = elements.passwordConfirm.value;
            
            if (!isLogin && password !== passwordConfirm) {
                showMessage('As senhas não coincidem', 'error');
                return;
            }
            
            elements.submitBtn.disabled = true;
            showMessage('Processando...', 'info');
            
            try {
                const url = isLogin ? '/api/auth/login' : '/api/auth/register';
                const body = isLogin ? 
                    { email, password } : 
                    { nome, email, password, password_confirmation: passwordConfirm };
                
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(body)
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    currentToken = data.access_token;
                    showUserLoggedIn(data.usuario);
                    showMessage(data.message, 'success');
                } else {
                    showMessage(data.message || 'Erro na autenticação', 'error');
                }
            } catch (error) {
                showMessage('Erro de conexão: ' + error.message, 'error');
            }
            
            elements.submitBtn.disabled = false;
        });

        // Logout
        elements.logoutBtn.addEventListener('click', async () => {
            if (!currentToken) return;
            
            try {
                await fetch('/api/auth/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${currentToken}`,
                        'Accept': 'application/json'
                    }
                });
                
                currentToken = null;
                showUserLoggedOut();
                showMessage('Logout realizado com sucesso', 'success');
            } catch (error) {
                showMessage('Erro no logout: ' + error.message, 'error');
            }
        });

        // Mostrar/ocultar teste de API
        elements.testApiBtn.addEventListener('click', () => {
            elements.apiTest.classList.toggle('hidden');
        });

        function showMessage(text, type) {
            if (!text) {
                elements.message.innerHTML = '';
                return;
            }
            
            elements.message.innerHTML = `<div class="message ${type}">${text}</div>`;
        }

        function showUserLoggedIn(usuario) {
            elements.form.classList.add('hidden');
            elements.userInfo.classList.remove('hidden');
            elements.userName.textContent = usuario.nome;
            elements.userEmail.textContent = usuario.email;
            elements.tokenPreview.textContent = currentToken.substring(0, 20) + '...';
        }

        function showUserLoggedOut() {
            elements.form.classList.remove('hidden');
            elements.userInfo.classList.add('hidden');
            elements.apiTest.classList.add('hidden');
            elements.form.reset();
        }

        async function testProtectedApi(endpoint, method) {
            if (!currentToken) {
                elements.apiResult.textContent = 'Erro: Token não disponível';
                return;
            }
            
            elements.apiResult.textContent = 'Testando...';
            
            try {
                const response = await fetch(endpoint, {
                    method: method,
                    headers: {
                        'Authorization': `Bearer ${currentToken}`,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                elements.apiResult.innerHTML = `
                    <strong>Status:</strong> ${response.status}<br>
                    <strong>Endpoint:</strong> ${method} ${endpoint}<br>
                    <strong>Resposta:</strong><br>
                    <pre>${JSON.stringify(data, null, 2)}</pre>
                `;
            } catch (error) {
                elements.apiResult.textContent = 'Erro: ' + error.message;
            }
        }

        // Inicialização
        elements.nome.style.display = 'none';
        elements.confirmGroup.style.display = 'none';
    </script>
</body>
</html>
