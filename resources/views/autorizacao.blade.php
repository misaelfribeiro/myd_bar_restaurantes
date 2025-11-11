<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔐 Teste de Autorização - Sistema Bar & Restaurante</title>
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
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .main {
            padding: 30px;
        }

        .login-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .roles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .role-card {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }

        .role-card:hover {
            border-color: #007bff;
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .role-card.active {
            border-color: #28a745;
            background: #f8fff9;
        }

        .role-card h3 {
            margin-bottom: 10px;
            color: #333;
        }

        .role-card p {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 15px;
        }

        .login-info {
            background: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 0.8em;
        }

        .test-section {
            margin-top: 30px;
        }

        .test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .test-button {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border: none;
            padding: 15px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1em;
            transition: all 0.3s ease;
            text-align: left;
        }

        .test-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,123,255,0.3);
        }

        .test-button:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
        }

        .results {
            margin-top: 20px;
            padding: 20px;
            border-radius: 8px;
            font-family: monospace;
            white-space: pre-wrap;
            max-height: 400px;
            overflow-y: auto;
        }

        .results.success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .results.error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .user-info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }

        .emoji {
            font-size: 1.2em;
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Teste de Sistema de Autorização</h1>
            <p>Sistema Laravel para Bar & Restaurante com Controle de Perfis</p>
        </div>

        <div class="main">
            <div class="login-section">
                <h2>👥 Selecione um Perfil para Testar</h2>
                <div class="roles-grid">
                    <div class="role-card" data-role="admin">
                        <h3>🔴 Administrador</h3>
                        <p>Acesso total ao sistema. Pode gerenciar usuários, visualizar relatórios e controlar tudo.</p>
                        <div class="login-info">
                            Email: admin@sistema.com<br>
                            Senha: admin123
                        </div>
                    </div>

                    <div class="role-card" data-role="gerente">
                        <h3>🟠 Gerente</h3>
                        <p>Gerenciamento de produtos, categorias e mesas. Controle operacional do restaurante.</p>
                        <div class="login-info">
                            Email: gerente@restaurante.com<br>
                            Senha: gerente123
                        </div>
                    </div>

                    <div class="role-card" data-role="garcom">
                        <h3>🟡 Garçom</h3>
                        <p>Gestão de pedidos, consulta de produtos e acesso ao dashboard operacional.</p>
                        <div class="login-info">
                            Email: maria2@restaurante.com<br>
                            Senha: garcom123
                        </div>
                    </div>

                    <div class="role-card" data-role="cliente">
                        <h3>🟢 Cliente</h3>
                        <p>Acesso limitado apenas para consultar produtos e categorias disponíveis.</p>
                        <div class="login-info">
                            Email: ana@email.com<br>
                            Senha: cliente123
                        </div>
                    </div>
                </div>

                <button id="loginBtn" disabled>🔐 Fazer Login com Perfil Selecionado</button>
            </div>

            <div id="userInfo" class="user-info" style="display: none;">
                <h3>👤 Usuário Logado</h3>
                <div id="userDetails"></div>
            </div>

            <div class="test-section">
                <h2>🧪 Testar Acesso às APIs</h2>
                <p>Clique nos botões para testar diferentes endpoints com o perfil logado:</p>
                
                <div class="test-grid">
                    <button class="test-button" data-endpoint="/api/usuarios" data-method="GET" disabled>
                        <span class="emoji">👥</span>Listar Usuários (Admin apenas)
                    </button>
                    
                    <button class="test-button" data-endpoint="/api/produtos" data-method="POST" data-body='{"nome":"Produto Teste","descricao":"Teste","preco":"10.00","categoria_id":1}' disabled>
                        <span class="emoji">🍽️</span>Criar Produto (Admin/Gerente)
                    </button>
                    
                    <button class="test-button" data-endpoint="/api/pedidos" data-method="GET" disabled>
                        <span class="emoji">📋</span>Listar Pedidos (Admin/Gerente/Garçom)
                    </button>
                    
                    <button class="test-button" data-endpoint="/api/produtos" data-method="GET" disabled>
                        <span class="emoji">🔍</span>Consultar Produtos (Todos exceto Cliente)
                    </button>
                    
                    <button class="test-button" data-endpoint="/api/relatorios/vendas" data-method="GET" disabled>
                        <span class="emoji">📊</span>Relatório Vendas (Admin apenas)
                    </button>
                    
                    <button class="test-button" data-endpoint="/api/dashboard/stats" data-method="GET" disabled>
                        <span class="emoji">📈</span>Dashboard Stats (Admin/Gerente/Garçom)
                    </button>
                </div>
            </div>

            <div id="results" class="results" style="display: none;"></div>
        </div>
    </div>

    <script>
        const API_BASE = 'http://localhost:8000/api';
        let currentToken = null;
        let currentUser = null;

        const roleCredentials = {
            admin: { email: 'admin@sistema.com', password: 'admin123' },
            gerente: { email: 'gerente@restaurante.com', password: 'gerente123' },
            garcom: { email: 'maria2@restaurante.com', password: 'garcom123' },
            cliente: { email: 'ana@email.com', password: 'cliente123' }
        };

        // Seleção de perfil
        document.querySelectorAll('.role-card').forEach(card => {
            card.addEventListener('click', () => {
                document.querySelectorAll('.role-card').forEach(c => c.classList.remove('active'));
                card.classList.add('active');
                document.getElementById('loginBtn').disabled = false;
                document.getElementById('loginBtn').dataset.role = card.dataset.role;
            });
        });

        // Login
        document.getElementById('loginBtn').addEventListener('click', async () => {
            const role = document.getElementById('loginBtn').dataset.role;
            const credentials = roleCredentials[role];
            
            try {
                const response = await fetch(`${API_BASE}/auth/login`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(credentials)
                });

                const data = await response.json();
                
                if (response.ok) {
                    currentToken = data.access_token;
                    currentUser = data.usuario;
                    
                    document.getElementById('userInfo').style.display = 'block';
                    document.getElementById('userDetails').innerHTML = `
                        <strong>Nome:</strong> ${currentUser.nome}<br>
                        <strong>Email:</strong> ${currentUser.email}<br>
                        <strong>Perfil:</strong> ${currentUser.role}<br>
                        <strong>Token:</strong> ${currentToken.substring(0, 20)}...
                    `;

                    // Habilitar botões de teste
                    document.querySelectorAll('.test-button').forEach(btn => {
                        btn.disabled = false;
                    });

                    showResults(`✅ Login realizado com sucesso!\nPerfil: ${currentUser.role}\nToken obtido: ${currentToken.substring(0, 30)}...`, 'success');
                } else {
                    showResults(`❌ Erro no login: ${data.message}`, 'error');
                }
            } catch (error) {
                showResults(`❌ Erro de conexão: ${error.message}`, 'error');
            }
        });

        // Testes de API
        document.querySelectorAll('.test-button').forEach(button => {
            button.addEventListener('click', async () => {
                if (!currentToken) {
                    showResults('❌ Faça login primeiro!', 'error');
                    return;
                }

                const endpoint = button.dataset.endpoint;
                const method = button.dataset.method;
                const body = button.dataset.body;

                try {
                    const options = {
                        method: method,
                        headers: {
                            'Authorization': `Bearer ${currentToken}`,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    };

                    if (body && method !== 'GET') {
                        options.body = body;
                    }

                    const response = await fetch(`${API_BASE}${endpoint}`, options);
                    const data = await response.json();

                    if (response.ok) {
                        showResults(`✅ Sucesso - ${method} ${endpoint}\nStatus: ${response.status}\nPerfil: ${currentUser.role}\n\nResposta:\n${JSON.stringify(data, null, 2)}`, 'success');
                    } else {
                        showResults(`❌ Acesso Negado - ${method} ${endpoint}\nStatus: ${response.status}\nPerfil: ${currentUser.role}\n\nErro:\n${JSON.stringify(data, null, 2)}`, 'error');
                    }
                } catch (error) {
                    showResults(`❌ Erro de conexão: ${error.message}`, 'error');
                }
            });
        });

        function showResults(text, type) {
            const results = document.getElementById('results');
            results.textContent = text;
            results.className = `results ${type}`;
            results.style.display = 'block';
            results.scrollTop = results.scrollHeight;
        }
    </script>
</body>
</html>
