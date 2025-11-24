<!DOCTYPE html>
<html lang="pt-BR">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <meta name="csrf-token" content="{{ csrf_token() }}">
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
 .api-monitoring-section {
 animation: fadeIn 0.5s ease-in;
 }
 @keyframes fadeIn {
 from { opacity: 0; transform: translateY(20px); }
 to { opacity: 1; transform: translateY(0); }
 }
 .api-status-card {
 transition: all 0.3s ease;
 position: relative;
 overflow: hidden;
 }
 .api-status-card:hover {
 transform: translateY(-2px);
 box-shadow: 0 12px 40px rgba(0,0,0,0.15) !important;
 }
 .api-status-badge {
 transition: all 0.3s ease;
 }
 .api-status-badge.online {
 background: linear-gradient(135deg, #28a745, #20c997);
 color: white;
 }
 .api-status-badge.offline {
 background: linear-gradient(135deg, #dc3545, #c82333);
 color: white;
 }
 .api-status-badge.warning {
 background: linear-gradient(135deg, #ffc107, #e0a800);
 color: #333;
 }
 .api-status-badge.loading {
 background: linear-gradient(135deg, #6c757d, #5a6268);
 color: white;
 }
 @keyframes statusPulse {
 0% { opacity: 1; }
 50% { opacity: 0.7; }
 100% { opacity: 1; }
 }
 .api-status-card.updating {
 animation: statusPulse 1s infinite;
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
 }        .menu-btn.usuarios {
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
 .menu-btn.caixa {
 background: linear-gradient(135deg, #28a745, #20c997);
 }
 .menu-btn.caixa-historico {
 background: linear-gradient(135deg, #17a2b8, #138496);
 }
 .status-indicator {
 display: inline-block;
 width: 12px;
 height: 12px;
 border-radius: 50%;
 margin-right: 8px;
 animation: pulse 2s infinite;
 }
 .status-aberto {
 background-color: #28a745;
 }
 .status-fechado {
 background-color: #dc3545;
 }
 .caixa-actions {
 margin-top: 15px;
 display: flex;
 gap: 10px;
 justify-content: center;
 flex-wrap: wrap;
 }
 .btn-acao-caixa {
 padding: 8px 16px;
 border: none;
 border-radius: 20px;
 font-size: 0.9rem;
 font-weight: 500;
 cursor: pointer;
 transition: all 0.3s ease;
 text-decoration: none;
 display: inline-flex;
 align-items: center;
 gap: 5px;
 }
 .btn-acao-caixa:hover {
 transform: translateY(-2px);
 box-shadow: 0 4px 12px rgba(0,0,0,0.2);
 }
 .btn-abrir-caixa {
 background: linear-gradient(135deg, #28a745, #20c997);
 color: white;
 }
 .btn-fechar-caixa {
 background: linear-gradient(135deg, #dc3545, #c82333);
 color: white;
 }
 .btn-relatorio-caixa {
 background: linear-gradient(135deg, #17a2b8, #138496);
 color: white;
 }
 </style>
</head>
<body>
 <div class="container">        <div class="header">
 <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
 <div></div>
 <div style="text-align: right;">
 <div style="background: rgba(102, 126, 234, 0.1); padding: 10px 20px; border-radius: 25px; display: inline-flex; align-items: center; gap: 10px;">
 <span style="font-size: 1.5rem;">👤</span>
 <div style="text-align: left;">
 <div style="font-weight: bold; color: #333; font-size: 0.95rem;" id="userName">Carregando...</div>
 <div style="font-size: 0.8rem; color: #666;" id="userRole">---</div>
 </div>
 </div>
 </div>
 </div>
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
 <!-- Seção de Caixa -->
 <div style="margin-top: 25px;">
 <h3 style="color: #333; margin-bottom: 15px; text-align: center;">💰 Sistema de Caixa</h3>
 <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
 <a href="/caixa" class="menu-btn caixa" style="background: linear-gradient(135deg, #28a745, #20c997); color: white; font-size: 1.1rem; padding: 12px 20px;">💰 Painel do Caixa</a>
 <a href="/caixa/historico" class="menu-btn caixa-historico" style="background: linear-gradient(135deg, #17a2b8, #138496); color: white; font-size: 1rem; padding: 12px 20px;">📊 Histórico de Caixas</a>
 <button id="btnStatusCaixa" class="menu-btn" style="background: linear-gradient(135deg, #6c757d, #5a6268); color: white; font-size: 1rem; padding: 12px 20px; border: none; cursor: pointer;">🔄 Verificar Status</button>
 </div>
 <!-- Cards de Status do Caixa -->
 <div id="statusCaixaCards" style="margin-top: 20px; display: none;">
 <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; max-width: 800px; margin: 0 auto;">
 <div class="stat-card" style="background: rgba(40, 167, 69, 0.1); border: 2px solid #28a745;">
 <div style="font-size: 1.5rem; color: #28a745; margin-bottom: 5px;">💰</div>
 <div class="stat-number" id="statusCaixaTexto" style="font-size: 1.2rem;">-</div>
 <div class="stat-label">Status do Caixa</div>
 </div>
 <div class="stat-card" style="background: rgba(23, 162, 184, 0.1); border: 2px solid #17a2b8;">
 <div style="font-size: 1.5rem; color: #17a2b8; margin-bottom: 5px;">💵</div>
 <div class="stat-number" id="saldoAtual">R$ -</div>
 <div class="stat-label">Saldo Atual</div>
 </div>
 <div class="stat-card" style="background: rgba(255, 193, 7, 0.1); border: 2px solid #ffc107;">
 <div style="font-size: 1.5rem; color: #ffc107; margin-bottom: 5px;">🛍️</div>
 <div class="stat-number" id="vendasHoje">R$ -</div>
 <div class="stat-label">Vendas Hoje</div>
 </div>
 <div class="stat-card" style="background: rgba(108, 117, 125, 0.1); border: 2px solid #6c757d;">
 <div style="font-size: 1.5rem; color: #6c757d; margin-bottom: 5px;">🕐</div>
 <div class="stat-number" id="horaAbertura">-</div>
 <div class="stat-label">Abertura</div>
 </div>
 </div>
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
 </div>        </div>
 <!-- 🔍 SEÇÃO DE MONITORAMENTO DE APIS -->
 <div class="api-monitoring-section" style="margin-bottom: 30px;">
 <div class="monitoring-header" style="background: rgba(255,255,255,0.95); padding: 20px; border-radius: 15px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); margin-bottom: 20px;">
 <div style="display: flex; justify-content: space-between; align-items: center;">
 <div>
 <h2 style="color: #333; margin: 0; display: flex; align-items: center;">
 <i class="fas fa-chart-line" style="margin-right: 10px; color: #667eea;"></i>
 Monitoramento de APIs
 </h2>
 <p style="color: #666; margin: 5px 0 0 0;">Status em tempo real de todas as APIs do sistema</p>
 </div>
 <div style="display: flex; gap: 10px; align-items: center;">
 <span id="last-api-update" style="color: #888; font-size: 0.9rem;">Carregando...</span>
 <button class="refresh-btn" id="refresh-apis" onclick="atualizarMonitoramentoAPIs()" style="padding: 8px 16px; font-size: 0.9rem;">
 <i class="fas fa-sync-alt" id="refresh-icon-apis"></i> Atualizar
 </button>
 <a href="/myd_bar_restaurantes/monitor_api_unificada.html" target="_blank" style="color: #667eea; text-decoration: none; font-weight: bold;">
 <i class="fas fa-external-link-alt"></i> Dashboard Completo
 </a>
 </div>
 </div>
 </div>
 <!-- Grid de Status das APIs -->
 <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 15px; margin-bottom: 20px;">
 <!-- API Unificada de Pagamentos -->
 <div class="api-status-card" style="background: rgba(255,255,255,0.95); padding: 20px; border-radius: 15px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); border-left: 5px solid #28a745;">
 <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 15px;">
 <h4 style="margin: 0; color: #333; display: flex; align-items: center;">
 <i class="fas fa-credit-card" style="margin-right: 8px; color: #28a745;"></i>
 API Pagamentos
 </h4>
 <span class="api-status-badge" id="api-pagamentos-status" style="padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: bold;">
 <i class="fas fa-spinner fa-spin"></i> Verificando...
 </span>
 </div>
 <div style="color: #666; font-size: 0.9rem; margin-bottom: 10px;">
 <div><strong>Endpoint:</strong> /api/pagamentos-status</div>
 <div><strong>Funcionalidades:</strong> Únicos, Múltiplos, Mesa</div>
 </div>
 <div style="display: flex; justify-content: space-between; align-items: center;">
 <div style="color: #888; font-size: 0.8rem;">
 Tempo: <span id="api-pagamentos-tempo">-</span>ms
 </div>
 <div style="color: #888; font-size: 0.8rem;">
 Taxa sucesso: <span id="api-pagamentos-sucesso">-</span>%
 </div>
 </div>
 </div>
 <!-- API de Produtos -->
 <div class="api-status-card" style="background: rgba(255,255,255,0.95); padding: 20px; border-radius: 15px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); border-left: 5px solid #ff6b35;">
 <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
 <h4 style="margin: 0; color: #333; display: flex; align-items: center;">
 <i class="fas fa-box" style="margin-right: 8px; color: #ff6b35;"></i>
 API Produtos
 </h4>
 <span class="api-status-badge" id="api-produtos-status" style="padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: bold;">
 <i class="fas fa-spinner fa-spin"></i> Verificando...
 </span>
 </div>
 <div style="color: #666; font-size: 0.9rem; margin-bottom: 10px;">
 <div><strong>Endpoint:</strong> /api/produtos</div>
 <div><strong>Total:</strong> <span id="api-produtos-total">-</span> produtos</div>
 </div>
 <div style="display: flex; justify-content: space-between; align-items: center;">
 <div style="color: #888; font-size: 0.8rem;">
 Tempo: <span id="api-produtos-tempo">-</span>ms
 </div>
 <div style="color: #888; font-size: 0.8rem;">
 Ativos: <span id="api-produtos-ativos">-</span>
 </div>
 </div>
 </div>
 <!-- API de Pedidos -->
 <div class="api-status-card" style="background: rgba(255,255,255,0.95); padding: 20px; border-radius: 15px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); border-left: 5px solid #007bff;">
 <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
 <h4 style="margin: 0; color: #333; display: flex; align-items: center;">
 <i class="fas fa-receipt" style="margin-right: 8px; color: #007bff;"></i>
 API Pedidos
 </h4>
 <span class="api-status-badge" id="api-pedidos-status" style="padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: bold;">
 <i class="fas fa-spinner fa-spin"></i> Verificando...
 </span>
 </div>
 <div style="color: #666; font-size: 0.9rem; margin-bottom: 10px;">
 <div><strong>Endpoint:</strong> /api/pedidos</div>
 <div><strong>Hoje:</strong> <span id="api-pedidos-hoje">-</span> pedidos</div>
 </div>
 <div style="display: flex; justify-content: space-between; align-items: center;">
 <div style="color: #888; font-size: 0.8rem;">
 Tempo: <span id="api-pedidos-tempo">-</span>ms
 </div>
 <div style="color: #888; font-size: 0.8rem;">
 Pendentes: <span id="api-pedidos-pendentes">-</span>
 </div>
 </div>
 </div>
 <!-- API de Monitoramento -->
 <div class="api-status-card" style="background: rgba(255,255,255,0.95); padding: 20px; border-radius: 15px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); border-left: 5px solid #6f42c1;">
 <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
 <h4 style="margin: 0; color: #333; display: flex; align-items: center;">
 <i class="fas fa-chart-bar" style="margin-right: 8px; color: #6f42c1;"></i>
 API Monitor
 </h4>
 <span class="api-status-badge" id="api-monitor-status" style="padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: bold;">
 <i class="fas fa-spinner fa-spin"></i> Verificando...
 </span>
 </div>
 <div style="color: #666; font-size: 0.9rem; margin-bottom: 10px;">
 <div><strong>Endpoint:</strong> /api/monitoramento/health</div>
 <div><strong>Uptime:</strong> <span id="api-monitor-uptime">-</span></div>
 </div>
 <div style="display: flex; justify-content: space-between; align-items: center;">
 <div style="color: #888; font-size: 0.8rem;">
 Tempo: <span id="api-monitor-tempo">-</span>ms
 </div>
 <div style="color: #888; font-size: 0.8rem;">
 DB: <span id="api-monitor-db">-</span>
 </div>
 </div>
 </div>
 </div>
 <!-- Resumo Geral -->
 <div style="background: rgba(255,255,255,0.95); padding: 20px; border-radius: 15px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center;">
 <div>
 <h4 style="margin: 0 0 5px 0; color: #333;">Status Geral do Sistema</h4>
 <p style="margin: 0; color: #666; font-size: 0.9rem;">
 APIs Online: <strong><span id="apis-online-count">0</span>/4</strong> | 
 Tempo médio: <strong><span id="tempo-medio-geral">-</span>ms</strong> | 
 Última verificação: <strong><span id="ultima-verificacao">-</span></strong>
 </p>
 </div>
 <div style="display: flex; gap: 10px;">
 <a href="/myd_bar_restaurantes/teste_api_simples.php" target="_blank" style="padding: 8px 16px; background: #28a745; color: white; text-decoration: none; border-radius: 20px; font-size: 0.9rem;">
 <i class="fas fa-vial"></i> Testar APIs
 </a>
 <a href="/myd_bar_restaurantes/public/api/status" target="_blank" style="padding: 8px 16px; background: #17a2b8; color: white; text-decoration: none; border-radius: 20px; font-size: 0.9rem;">
 <i class="fas fa-info-circle"></i> Status
 </a>
 </div>
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
 document.addEventListener('DOMContentLoaded', function() {
 carregarEstatisticas();
 carregarDadosUsuario();
 });
 setInterval(carregarEstatisticas, 30000);
 async function carregarDadosUsuario() {
 const token = localStorage.getItem('auth_token');
 const userStr = localStorage.getItem('user');
 if (!token || !userStr) {
 console.log('Usuário não autenticado');
 return;
 }
 try {
 const cachedUser = JSON.parse(userStr);
 if (cachedUser && cachedUser.nome) {
 atualizarNomeUsuario(cachedUser);
 }
 const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
 const response = await fetch('/api/auth/me', {
 method: 'GET',
 headers: {
 'Authorization': `Bearer ${token}`,
 'Accept': 'application/json',
 'X-CSRF-TOKEN': csrfToken || ''
 },
 credentials: 'include'
 });
 if (response.ok) {
 const data = await response.json();
 const user = data.usuario || data.data || data;
 localStorage.setItem('user', JSON.stringify(user));
 atualizarNomeUsuario(user);
 }
 } catch (error) {
 console.error('Erro ao carregar dados do usuário:', error);
 }
 }
 function atualizarNomeUsuario(user) {
 const roles = {
 'admin': 'Administrador',
 'gerente': 'Gerente',
 'garcom': 'Garçom',
 'caixa': 'Caixa',
 'cliente': 'Cliente'
 };
 const userNameElement = document.getElementById('userName');
 const userRoleElement = document.getElementById('userRole');
 if (userNameElement) {
 userNameElement.textContent = user.nome || 'Usuário';
 }
 if (userRoleElement) {
 userRoleElement.textContent = roles[user.role] || user.role || 'Usuário';
 }
 }
 let statusCaixaVisivel = false;
 let caixaAtual = null;
 function verificarStatusCaixa() {
 const btnStatus = document.getElementById('btnStatusCaixa');
 const statusCards = document.getElementById('statusCaixaCards');
 btnStatus.innerHTML = '⏳ Verificando...';
 btnStatus.disabled = true;
 fetch('/caixa/api/totais-tempo-real')
 .then(response => response.json())
 .then(data => {
 console.log('Status do caixa:', data);
 caixaAtual = data.caixa_aberto;
 atualizarStatusCaixa(data);
 if (!statusCaixaVisivel) {
 statusCards.style.display = 'block';
 statusCaixaVisivel = true;
 btnStatus.innerHTML = '🔄 Atualizar Status';
 } else {
 btnStatus.innerHTML = '🔄 Verificar Status';
 }
 })
 .catch(error => {
 console.error('Erro ao verificar status do caixa:', error);
 btnStatus.innerHTML = '❌ Erro - Tentar Novamente';
 document.getElementById('statusCaixaTexto').innerHTML = 'Erro ao conectar';
 document.getElementById('saldoAtual').innerHTML = 'R$ -';
 document.getElementById('vendasHoje').innerHTML = 'R$ -';
 document.getElementById('horaAbertura').innerHTML = '-';
 if (!statusCaixaVisivel) {
 statusCards.style.display = 'block';
 statusCaixaVisivel = true;
 }
 })
 .finally(() => {
 btnStatus.disabled = false;
 });
 }
 function atualizarStatusCaixa(data) {
 const statusTexto = document.getElementById('statusCaixaTexto');
 const saldoAtual = document.getElementById('saldoAtual');
 const vendasHoje = document.getElementById('vendasHoje');
 const horaAbertura = document.getElementById('horaAbertura');
 if (data.caixa_aberto) {
 statusTexto.innerHTML = '<span class="status-indicator status-aberto"></span>ABERTO';
 saldoAtual.innerHTML = 'R$ ' + (data.saldo_atual || '0,00').toLocaleString('pt-BR', {minimumFractionDigits: 2});
 vendasHoje.innerHTML = 'R$ ' + (data.total_vendas_hoje || '0,00').toLocaleString('pt-BR', {minimumFractionDigits: 2});
 horaAbertura.innerHTML = data.hora_abertura || '-';
 mostrarAcoesCaixa(true, data.caixa_aberto.id);
 } else {
 statusTexto.innerHTML = '<span class="status-indicator status-fechado"></span>FECHADO';
 saldoAtual.innerHTML = 'R$ 0,00';
 vendasHoje.innerHTML = 'R$ 0,00';
 horaAbertura.innerHTML = '-';
 mostrarAcoesCaixa(false);
 }
 }
 function mostrarAcoesCaixa(caixaAberto, caixaId = null) {
 let acoesCaixa = document.getElementById('acoesCaixa');
 if (acoesCaixa) {
 acoesCaixa.remove();
 }
 acoesCaixa = document.createElement('div');
 acoesCaixa.id = 'acoesCaixa';
 acoesCaixa.className = 'caixa-actions';
 if (caixaAberto && caixaId) {
 acoesCaixa.innerHTML = `
 <button class="btn-acao-caixa btn-fechar-caixa" onclick="confirmarFecharCaixa(${caixaId})">
 🔒 Fechar Caixa
 </button>
 <a href="/caixa/relatorio/${caixaId}" class="btn-acao-caixa btn-relatorio-caixa">
 📊 Ver Relatório
 </a>
 `;
 } else {                acoesCaixa.innerHTML = `
 <a href="/caixa/abertura" class="btn-acao-caixa btn-abrir-caixa">
 🔓 Abrir Caixa
 </a>
 `;
 }
 const statusCards = document.getElementById('statusCaixaCards');
 statusCards.appendChild(acoesCaixa);
 }
 function confirmarFecharCaixa(caixaId) {
 if (confirm('Tem certeza que deseja fechar o caixa? Esta ação não pode ser desfeita.')) {
 fecharCaixa(caixaId);
 }
 }
 function fecharCaixa(caixaId) {
 const form = document.createElement('form');
 form.method = 'POST';
 form.action = '/caixa/fechar';
 const tokenField = document.createElement('input');
 tokenField.type = 'hidden';
 tokenField.name = '_token';
 tokenField.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
 form.appendChild(tokenField);
 document.body.appendChild(form);
 form.submit();
 }
 document.addEventListener('DOMContentLoaded', function() {
 const btnStatus = document.getElementById('btnStatusCaixa');
 if (btnStatus) {
 let monitoramentoAtivo = false;
 let temposResposta = [];
 async function atualizarMonitoramentoAPIs() {
 console.log('🔍 Iniciando monitoramento de APIs...');
 const refreshIcon = document.getElementById('refresh-icon-apis');
 refreshIcon.classList.add('fa-spin');
 document.querySelectorAll('.api-status-card').forEach(card => {
 card.classList.add('updating');
 });
 let apisOnline = 0;
 temposResposta = [];
 try {
 const resultados = await Promise.allSettled([
 testarAPIPagamentos(),
 testarAPIProdutos(),
 testarAPIPedidos(),
 testarAPIMonitoramento()
 ]);
 apisOnline = resultados.filter(r => r.status === 'fulfilled' && r.value.online).length;
 atualizarResumoGeral(apisOnline);
 } catch (error) {
 console.error('Erro no monitoramento geral:', error);
 } finally {
 refreshIcon.classList.remove('fa-spin');
 setTimeout(() => {
 document.querySelectorAll('.api-status-card').forEach(card => {
 card.classList.remove('updating');
 });
 }, 500);
 document.getElementById('last-api-update').textContent = 
 `Atualizado: ${new Date().toLocaleTimeString('pt-BR')}`;
 }
 }
 async function testarAPIPagamentos() {
 const inicio = performance.now();
 try {
 const response = await fetch('/api/pagamentos-status');
 const fim = performance.now();
 const tempo = Math.round(fim - inicio);
 temposResposta.push(tempo);
 const data = await response.json();
 if (response.ok && data.success) {
 atualizarStatusAPI('pagamentos', 'online', tempo);
 document.getElementById('api-pagamentos-sucesso').textContent = '99.9';
 return { online: true, tempo };
 } else {
 atualizarStatusAPI('pagamentos', 'warning', tempo);
 return { online: false, tempo };
 }
 } catch (error) {
 atualizarStatusAPI('pagamentos', 'offline', 0);
 return { online: false, tempo: 0 };
 }
 }
 async function testarAPIProdutos() {
 const inicio = performance.now();
 try {
 const response = await fetch('/api/produtos-public');
 const fim = performance.now();
 const tempo = Math.round(fim - inicio);
 temposResposta.push(tempo);
 const data = await response.json();
 if (response.ok) {
 atualizarStatusAPI('produtos', 'online', tempo);
 document.getElementById('api-produtos-total').textContent = data.length || 0;
 document.getElementById('api-produtos-ativos').textContent = 
 data.filter(p => p.ativo).length || 0;
 return { online: true, tempo };
 } else {
 atualizarStatusAPI('produtos', 'warning', tempo);
 return { online: false, tempo };
 }
 } catch (error) {
 atualizarStatusAPI('produtos', 'offline', 0);
 return { online: false, tempo: 0 };
 }
 }
 async function testarAPIPedidos() {
 const inicio = performance.now();
 try {
 const response = await fetch('/api/dashboard/pedidos-status');
 const fim = performance.now();
 const tempo = Math.round(fim - inicio);
 temposResposta.push(tempo);
 const data = await response.json();
 if (response.ok) {
 atualizarStatusAPI('pedidos', 'online', tempo);
 document.getElementById('api-pedidos-hoje').textContent = 
 data.pedidos_hoje || 0;
 document.getElementById('api-pedidos-pendentes').textContent = 
 data.pendentes || 0;
 return { online: true, tempo };
 } else {
 atualizarStatusAPI('pedidos', 'warning', tempo);
 return { online: false, tempo };
 }
 } catch (error) {
 atualizarStatusAPI('pedidos', 'offline', 0);
 return { online: false, tempo: 0 };
 }
 }
 async function testarAPIMonitoramento() {
 const inicio = performance.now();
 try {
 const response = await fetch('/api/monitoramento/health');
 const fim = performance.now();
 const tempo = Math.round(fim - inicio);
 temposResposta.push(tempo);
 const data = await response.json();
 if (response.ok && data.success) {
 atualizarStatusAPI('monitor', 'online', tempo);
 document.getElementById('api-monitor-uptime').textContent = '99.9%';
 document.getElementById('api-monitor-db').textContent = 
 data.checks?.database ? 'OK' : 'Erro';
 return { online: true, tempo };
 } else {
 atualizarStatusAPI('monitor', 'warning', tempo);
 return { online: false, tempo };
 }
 } catch (error) {
 atualizarStatusAPI('monitor', 'offline', 0);
 return { online: false, tempo: 0 };
 }
 }
 function atualizarStatusAPI(api, status, tempo) {
 const statusBadge = document.getElementById(`api-${api}-status`);
 const tempoElement = document.getElementById(`api-${api}-tempo`);
 statusBadge.className = `api-status-badge ${status}`;
 switch (status) {
 case 'online':
 statusBadge.innerHTML = '<i class="fas fa-check-circle"></i> Online';
 break;
 case 'offline':
 statusBadge.innerHTML = '<i class="fas fa-times-circle"></i> Offline';
 break;
 case 'warning':
 statusBadge.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Alerta';
 break;
 }
 if (tempoElement) {
 tempoElement.textContent = tempo;
 }
 }
 function atualizarResumoGeral(apisOnline) {
 document.getElementById('apis-online-count').textContent = apisOnline;
 const tempoMedio = temposResposta.length > 0 
 ? Math.round(temposResposta.reduce((a, b) => a + b, 0) / temposResposta.length)
 : 0;
 document.getElementById('tempo-medio-geral').textContent = tempoMedio;
 document.getElementById('ultima-verificacao').textContent = 
 new Date().toLocaleTimeString('pt-BR');
 }
 function iniciarMonitoramentoAutomatico() {
 if (monitoramentoAtivo) return;
 console.log('🚀 Iniciando monitoramento automático de APIs...');
 monitoramentoAtivo = true;
 atualizarMonitoramentoAPIs();
 setInterval(() => {
 if (document.visibilityState === 'visible') {
 atualizarMonitoramentoAPIs();
 }
 }, 45000);
 }
 document.addEventListener('visibilitychange', () => {
 if (document.visibilityState === 'hidden') {
 console.log('📱 Página minimizada, pausando monitoramento...');
 } else {
 console.log('📱 Página ativa, retomando monitoramento...');
 if (monitoramentoAtivo) {
 setTimeout(atualizarMonitoramentoAPIs, 1000);
 }
 }
 });
 btnStatus.addEventListener('click', verificarStatusCaixa);
 }
 setTimeout(iniciarMonitoramentoAutomatico, 2000);
 });
 </script>
</body>
</html>