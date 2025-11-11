<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema Bar & Restaurante</title>
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
        }
        .header {
            background: rgba(255,255,255,0.95);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            text-align: center;
        }
        .header h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 2.5rem;
        }
        .header p {
            color: #666;
            font-size: 1.1rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: rgba(255,255,255,0.95);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }
        .stat-label {
            color: #666;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .api-section {
            background: rgba(255,255,255,0.95);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .api-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .api-group h3 {
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        .api-endpoint {
            background: #f8f9fa;
            padding: 12px 15px;
            margin: 8px 0;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
        }
        .method {
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
            color: white;
            margin-right: 10px;
        }
        .method.get { background-color: #28a745; }
        .method.post { background-color: #007bff; }
        .method.put { background-color: #ffc107; color: #333; }
        .method.delete { background-color: #dc3545; }
        .refresh-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1rem;
            transition: transform 0.3s ease;
        }
        .refresh-btn:hover {
            transform: scale(1.05);
        }
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }        .pulse {
            animation: pulse 1.5s infinite;
        }
        .menu-btn {
            padding: 12px 24px;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .menu-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
            text-decoration: none;
        }
        .menu-btn.produtos {
            background: linear-gradient(135deg, #ff6b35, #f7931e);
        }
        .menu-btn.categorias {
            background: linear-gradient(135deg, #17a2b8, #138496);
        }
        .menu-btn.pedidos {
            background: linear-gradient(135deg, #fd7e14, #e67e22);
        }
        .menu-btn.mesas {
            background: linear-gradient(135deg, #20c997, #17a085);
        }
        .menu-btn.usuarios {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }
        .menu-btn.login {
            background: linear-gradient(135deg, #28a745, #218838);
        }
        .menu-btn.auth {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            color: #333 !important;
        }
        .menu-btn.logs {
            background: linear-gradient(135deg, #6f42c1, #5a32a3);
        }
    </style>
</head>
<body>
    <div class="container">        <div class="header">
            <h1>🍽️ Dashboard - Bar & Restaurante</h1>
            <p>Sistema de Gerenciamento Completo</p>            <div style="margin-top: 20px; display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <button class="refresh-btn" onclick="carregarEstatisticas()">🔄 Atualizar Dados</button>
            </div>
            
            <!-- Seção de Gestão Administrativa -->
            <div style="margin-top: 25px;">
                <h3 style="color: #333; margin-bottom: 15px; text-align: center;">⚙️ Gestão Administrativa</h3>
                <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                    <a href="/produtos" class="menu-btn produtos">🍽️ Gerenciar Produtos</a>
                    <a href="/categorias" class="menu-btn categorias">📋 Categorias</a>
                    <a href="/pedidos" class="menu-btn pedidos">📝 Pedidos</a>
                    <a href="/mesas" class="menu-btn mesas">🪑 Mesas</a>
                    <a href="/usuarios" class="menu-btn usuarios">👥 Gestão de Usuários</a>
                    <a href="/logs" class="menu-btn logs">📊 Logs de Acesso</a>
                </div>
            </div>
            
            <!-- Seção Operacional -->
            <div style="margin-top: 25px;">
                <h3 style="color: #333; margin-bottom: 15px; text-align: center;">🍽️ Interface Operacional</h3>
                <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                    <a href="/garcom/dashboard" class="menu-btn" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; font-size: 1.1rem; padding: 12px 20px;">🍽️ Modo Garçom</a>
                </div>
            </div>
            
            <!-- Seção de Testes -->
            <div style="margin-top: 25px;">
                <h3 style="color: #666; margin-bottom: 15px; text-align: center;">🧪 Testes e Desenvolvimento</h3>
                <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                    <a href="/login" class="menu-btn login">🔐 Testar Login</a>
                    <a href="/autorizacao" class="menu-btn auth">🔐 Teste Autorização</a>
                </div>
            </div>
        </div>

        <div class="stats-grid" id="statsGrid">
            <div class="stat-card">
                <div class="stat-number" id="totalCategorias">-</div>
                <div class="stat-label">Categorias</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="totalProdutos">-</div>
                <div class="stat-label">Produtos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="totalMesas">-</div>
                <div class="stat-label">Mesas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="totalUsuarios">-</div>
                <div class="stat-label">Usuários</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="totalPedidos">-</div>
                <div class="stat-label">Total Pedidos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="pedidosPendentes">-</div>
                <div class="stat-label">Pendentes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="pedidosEmPreparo">-</div>
                <div class="stat-label">Em Preparo</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="pedidosProntos">-</div>
                <div class="stat-label">Prontos</div>
            </div>
        </div>

        <div class="api-section">
            <h2 style="text-align: center; margin-bottom: 30px; color: #333;">🚀 APIs Disponíveis</h2>
            
            <div class="api-grid">
                <div class="api-group">
                    <h3>📋 Categorias</h3>
                    <div class="api-endpoint">
                        <span class="method get">GET</span> /api/categorias
                    </div>
                    <div class="api-endpoint">
                        <span class="method post">POST</span> /api/categorias
                    </div>
                    <div class="api-endpoint">
                        <span class="method put">PUT</span> /api/categorias/{id}
                    </div>
                    <div class="api-endpoint">
                        <span class="method delete">DEL</span> /api/categorias/{id}
                    </div>
                </div>

                <div class="api-group">
                    <h3>🍕 Produtos</h3>
                    <div class="api-endpoint">
                        <span class="method get">GET</span> /api/produtos
                    </div>
                    <div class="api-endpoint">
                        <span class="method post">POST</span> /api/produtos
                    </div>
                    <div class="api-endpoint">
                        <span class="method put">PUT</span> /api/produtos/{id}
                    </div>
                    <div class="api-endpoint">
                        <span class="method delete">DEL</span> /api/produtos/{id}
                    </div>
                </div>

                <div class="api-group">
                    <h3>🪑 Mesas</h3>
                    <div class="api-endpoint">
                        <span class="method get">GET</span> /api/mesas
                    </div>
                    <div class="api-endpoint">
                        <span class="method post">POST</span> /api/mesas
                    </div>
                    <div class="api-endpoint">
                        <span class="method put">PUT</span> /api/mesas/{id}
                    </div>
                    <div class="api-endpoint">
                        <span class="method delete">DEL</span> /api/mesas/{id}
                    </div>
                </div>

                <div class="api-group">
                    <h3>👥 Usuários</h3>
                    <div class="api-endpoint">
                        <span class="method get">GET</span> /api/usuarios
                    </div>
                    <div class="api-endpoint">
                        <span class="method post">POST</span> /api/usuarios
                    </div>
                    <div class="api-endpoint">
                        <span class="method put">PUT</span> /api/usuarios/{id}
                    </div>
                    <div class="api-endpoint">
                        <span class="method delete">DEL</span> /api/usuarios/{id}
                    </div>
                </div>

                <div class="api-group">
                    <h3>📋 Pedidos</h3>
                    <div class="api-endpoint">
                        <span class="method get">GET</span> /api/pedidos
                    </div>
                    <div class="api-endpoint">
                        <span class="method post">POST</span> /api/pedidos
                    </div>
                    <div class="api-endpoint">
                        <span class="method put">PUT</span> /api/pedidos/{id}
                    </div>
                    <div class="api-endpoint">
                        <span class="method delete">DEL</span> /api/pedidos/{id}
                    </div>
                </div>

                <div class="api-group">
                    <h3>📊 Dashboard</h3>
                    <div class="api-endpoint">
                        <span class="method get">GET</span> /api/dashboard/stats
                    </div>
                    <div class="api-endpoint">
                        <span class="method get">GET</span> /api/dashboard/pedidos-status
                    </div>
                    <div class="api-endpoint">
                        <span class="method get">GET</span> /api/dashboard/produtos-vendidos
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function carregarEstatisticas() {
            const statsGrid = document.getElementById('statsGrid');
            statsGrid.classList.add('loading', 'pulse');
            
            fetch('/api/dashboard/stats')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('totalCategorias').textContent = data.total_categorias || 0;
                    document.getElementById('totalProdutos').textContent = data.total_produtos || 0;
                    document.getElementById('totalMesas').textContent = data.total_mesas || 0;
                    document.getElementById('totalUsuarios').textContent = data.total_usuarios || 0;
                    document.getElementById('totalPedidos').textContent = data.total_pedidos || 0;
                    document.getElementById('pedidosPendentes').textContent = data.pedidos_pendentes || 0;
                    document.getElementById('pedidosEmPreparo').textContent = data.pedidos_em_preparo || 0;
                    document.getElementById('pedidosProntos').textContent = data.pedidos_prontos || 0;
                })
                .catch(error => {
                    console.error('Erro ao carregar estatísticas:', error);
                })
                .finally(() => {
                    statsGrid.classList.remove('loading', 'pulse');
                });
        }

        // Carregar estatísticas ao carregar a página
        document.addEventListener('DOMContentLoaded', carregarEstatisticas);
        
        // Atualizar estatísticas a cada 30 segundos
        setInterval(carregarEstatisticas, 30000);
    </script>
</body>
</html>
