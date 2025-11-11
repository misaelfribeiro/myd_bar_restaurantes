<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📊 Logs de Acesso - Sistema Bar & Restaurante</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1600px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #e74c3c 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .nav-button {
            position: absolute;
            top: 30px;
            left: 30px;
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .nav-button:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }

        .main-content {
            padding: 30px;
        }

        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .stat-card.success { border-color: #28a745; }
        .stat-card.danger { border-color: #dc3545; }
        .stat-card.warning { border-color: #ffc107; }
        .stat-card.info { border-color: #17a2b8; }

        .stat-number {
            font-size: 2.2em;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .stat-label {
            color: #666;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .filters {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .filter-group label {
            font-size: 0.85em;
            color: #666;
            font-weight: 600;
        }

        .form-control {
            padding: 8px 12px;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            font-size: 0.9em;
            transition: all 0.3s ease;
            min-width: 120px;
        }

        .form-control:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        }

        .btn {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 0.9em;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,123,255,0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }

        .btn-danger:hover {
            box-shadow: 0 4px 12px rgba(220,53,69,0.3);
        }

        .logs-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9em;
        }

        .table th {
            background: #f8f9fa;
            padding: 15px 10px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            white-space: nowrap;
        }

        .table td {
            padding: 12px 10px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }

        .table tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.success { background: #d4edda; color: #155724; }
        .status-badge.failed { background: #f8d7da; color: #721c24; }
        .status-badge.denied { background: #fff3cd; color: #856404; }

        .action-badge {
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 0.75em;
            font-weight: 600;
            background: #e9ecef;
            color: #495057;
        }

        .action-badge.login { background: #d1ecf1; color: #0c5460; }
        .action-badge.logout { background: #d4edda; color: #155724; }
        .action-badge.api_access { background: #fff3cd; color: #856404; }

        .loading {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .page-btn {
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .page-btn:hover {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        .page-btn.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        .charts-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .chart-title {
            font-size: 1.2em;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .security-events {
            background: #fff5f5;
            border: 1px solid #fed7d7;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .event-item {
            padding: 10px;
            border-bottom: 1px solid #fed7d7;
            font-size: 0.9em;
        }

        .event-item:last-child {
            border-bottom: none;
        }

        .event-time {
            color: #666;
            font-size: 0.8em;
        }

        @media (max-width: 768px) {
            .filters {
                flex-direction: column;
                align-items: stretch;
            }

            .charts-section {
                grid-template-columns: 1fr;
            }

            .table {
                font-size: 0.8em;
            }

            .table th,
            .table td {
                padding: 8px 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="/" class="nav-button">
                <span>←</span> Voltar ao Dashboard
            </a>
            <h1>📊 Logs de Acesso & Segurança</h1>
            <p>Monitoramento detalhado de atividades do sistema</p>
        </div>

        <div class="main-content">
            <!-- Estatísticas Gerais -->
            <div class="stats-overview" id="statsOverview">
                <!-- Preenchido via JavaScript -->
            </div>

            <!-- Eventos de Segurança -->
            <div class="security-events" id="securityEvents" style="display: none;">
                <h3>⚠️ Eventos de Segurança Recentes</h3>
                <div id="securityEventsList">
                    <!-- Preenchido via JavaScript -->
                </div>
            </div>

            <!-- Gráficos -->
            <div class="charts-section">
                <div class="chart-container">
                    <div class="chart-title">📈 Atividade por Hora (Últimas 24h)</div>
                    <canvas id="hourlyChart" width="400" height="200"></canvas>
                </div>
                <div class="chart-container">
                    <div class="chart-title">👥 Usuários Mais Ativos</div>
                    <div id="activeUsersList">
                        <!-- Preenchido via JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Controles e Filtros -->
            <div class="controls">
                <div class="filters">
                    <div class="filter-group">
                        <label>Ação</label>
                        <select id="actionFilter" class="form-control">
                            <option value="">Todas as ações</option>
                            <option value="login">Login</option>
                            <option value="logout">Logout</option>
                            <option value="api_access">Acesso API</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>Status</label>
                        <select id="statusFilter" class="form-control">
                            <option value="">Todos os status</option>
                            <option value="success">Sucesso</option>
                            <option value="failed">Falhou</option>
                            <option value="denied">Negado</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>Data de</label>
                        <input type="date" id="dateFromFilter" class="form-control">
                    </div>
                    
                    <div class="filter-group">
                        <label>Data até</label>
                        <input type="date" id="dateToFilter" class="form-control">
                    </div>
                    
                    <div class="filter-group">
                        <label>&nbsp;</label>
                        <button class="btn" id="applyFilters">
                            🔍 Filtrar
                        </button>
                    </div>
                </div>

                <button class="btn btn-danger" id="clearOldLogs">
                    🗑️ Limpar Logs Antigos
                </button>
            </div>

            <!-- Tabela de Logs -->
            <div class="logs-table">
                <div class="loading" id="loading">
                    ⏳ Carregando logs...
                </div>
                <table class="table" id="logsTable" style="display: none;">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Usuário</th>
                            <th>Ação</th>
                            <th>Endpoint</th>
                            <th>IP</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="logsTableBody">
                        <!-- Preenchido via JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="pagination" id="pagination">
                <!-- Preenchido via JavaScript -->
            </div>
        </div>
    </div>

    <script>
        // Configurações
        const API_BASE = '/logs';
        let currentPage = 1;

        // Elementos DOM
        const elements = {
            loading: document.getElementById('loading'),
            logsTable: document.getElementById('logsTable'),
            logsTableBody: document.getElementById('logsTableBody'),
            statsOverview: document.getElementById('statsOverview'),
            securityEvents: document.getElementById('securityEvents'),
            securityEventsList: document.getElementById('securityEventsList'),
            activeUsersList: document.getElementById('activeUsersList'),
            pagination: document.getElementById('pagination'),
            actionFilter: document.getElementById('actionFilter'),
            statusFilter: document.getElementById('statusFilter'),
            dateFromFilter: document.getElementById('dateFromFilter'),
            dateToFilter: document.getElementById('dateToFilter'),
            applyFilters: document.getElementById('applyFilters'),
            clearOldLogs: document.getElementById('clearOldLogs')
        };

        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            loadStats();
            loadLogs();
            loadSecurityEvents();
            setupEventListeners();
            
            // Definir data padrão (últimos 7 dias)
            const today = new Date();
            const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
            
            elements.dateToFilter.valueAsDate = today;
            elements.dateFromFilter.valueAsDate = weekAgo;
        });

        function setupEventListeners() {
            elements.applyFilters.addEventListener('click', () => {
                currentPage = 1;
                loadLogs();
            });

            elements.clearOldLogs.addEventListener('click', clearOldLogs);
        }

        async function loadStats() {
            try {
                const response = await fetch(`${API_BASE}/stats`);
                const data = await response.json();
                
                if (data.success) {
                    renderStats(data.stats);
                    renderHourlyChart(data.hourly_activity, data.hourly_labels);
                    renderActiveUsers(data.active_users);
                }
            } catch (error) {
                console.error('Erro ao carregar estatísticas:', error);
            }
        }

        function renderStats(stats) {
            elements.statsOverview.innerHTML = `
                <div class="stat-card success">
                    <div class="stat-number">${stats.successful_logins}</div>
                    <div class="stat-label">Logins com Sucesso</div>
                </div>
                <div class="stat-card danger">
                    <div class="stat-number">${stats.failed_logins}</div>
                    <div class="stat-label">Falhas de Login</div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-number">${stats.denied_access}</div>
                    <div class="stat-label">Acessos Negados</div>
                </div>
                <div class="stat-card info">
                    <div class="stat-number">${stats.total_api_access}</div>
                    <div class="stat-label">Acessos à API</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">${stats.today_logins}</div>
                    <div class="stat-label">Logins Hoje</div>
                </div>
            `;
        }

        function renderHourlyChart(data, labels) {
            // Simplified chart representation
            const canvas = document.getElementById('hourlyChart');
            const ctx = canvas.getContext('2d');
            
            // Clear canvas
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            // Simple bar chart
            const maxValue = Math.max(...data) || 1;
            const barWidth = canvas.width / data.length;
            const barMaxHeight = canvas.height - 40;
            
            ctx.fillStyle = '#007bff';
            
            data.forEach((value, index) => {
                const barHeight = (value / maxValue) * barMaxHeight;
                const x = index * barWidth;
                const y = canvas.height - barHeight - 20;
                
                ctx.fillRect(x + 2, y, barWidth - 4, barHeight);
                
                // Labels
                ctx.fillStyle = '#666';
                ctx.font = '10px Arial';
                ctx.textAlign = 'center';
                ctx.fillText(labels[index], x + barWidth/2, canvas.height - 5);
                
                ctx.fillStyle = '#007bff';
            });
        }

        function renderActiveUsers(users) {
            elements.activeUsersList.innerHTML = users.map(user => `
                <div style="padding: 10px; border-bottom: 1px solid #eee;">
                    <div style="font-weight: 600;">${user.email || 'Email não informado'}</div>
                    <div style="color: #666; font-size: 0.9em;">${user.total_actions} ações</div>
                </div>
            `).join('') || '<div style="padding: 20px; text-align: center; color: #666;">Nenhum dado disponível</div>';
        }

        async function loadLogs() {
            showLoading(true);
            
            try {
                const params = new URLSearchParams({
                    page: currentPage,
                    action: elements.actionFilter.value,
                    status: elements.statusFilter.value,
                    date_from: elements.dateFromFilter.value,
                    date_to: elements.dateToFilter.value
                });

                const response = await fetch(`${API_BASE}/data?${params}`);
                const data = await response.json();
                
                if (data.success) {
                    renderLogs(data.logs.data);
                    renderPagination(data.logs);
                }
            } catch (error) {
                console.error('Erro ao carregar logs:', error);
            } finally {
                showLoading(false);
            }
        }

        function renderLogs(logs) {
            if (logs.length === 0) {
                elements.logsTableBody.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #666;">
                            Nenhum log encontrado para os filtros aplicados
                        </td>
                    </tr>
                `;
                return;
            }

            elements.logsTableBody.innerHTML = logs.map(log => `
                <tr>
                    <td>${formatDate(log.created_at)}</td>
                    <td>${log.email || log.usuario?.nome || 'N/A'}</td>
                    <td><span class="action-badge ${log.action}">${formatAction(log.action)}</span></td>
                    <td><code>${log.endpoint || 'N/A'}</code></td>
                    <td>${log.ip_address}</td>
                    <td><span class="status-badge ${log.status}">${formatStatus(log.status)}</span></td>
                </tr>
            `).join('');
        }

        function renderPagination(paginationData) {
            if (paginationData.last_page <= 1) {
                elements.pagination.innerHTML = '';
                return;
            }

            let pages = '';
            
            // Botão anterior
            if (paginationData.current_page > 1) {
                pages += `<button class="page-btn" onclick="goToPage(${paginationData.current_page - 1})">←</button>`;
            }
            
            // Páginas
            for (let i = 1; i <= paginationData.last_page; i++) {
                if (i === paginationData.current_page) {
                    pages += `<button class="page-btn active">${i}</button>`;
                } else {
                    pages += `<button class="page-btn" onclick="goToPage(${i})">${i}</button>`;
                }
            }
            
            // Botão próximo
            if (paginationData.current_page < paginationData.last_page) {
                pages += `<button class="page-btn" onclick="goToPage(${paginationData.current_page + 1})">→</button>`;
            }
            
            elements.pagination.innerHTML = pages;
        }

        async function loadSecurityEvents() {
            try {
                const response = await fetch(`${API_BASE}/security-events`);
                const data = await response.json();
                
                if (data.success && data.events.length > 0) {
                    elements.securityEvents.style.display = 'block';
                    elements.securityEventsList.innerHTML = data.events.map(event => `
                        <div class="event-item">
                            <div><strong>${formatAction(event.action)}</strong> - ${event.email || 'Email não informado'}</div>
                            <div class="event-time">${formatDate(event.created_at)} - IP: ${event.ip_address}</div>
                        </div>
                    `).join('');
                }
            } catch (error) {
                console.error('Erro ao carregar eventos de segurança:', error);
            }
        }

        async function clearOldLogs() {
            if (!confirm('Tem certeza que deseja remover logs antigos (mais de 30 dias)?')) {
                return;
            }

            try {
                const response = await fetch(`${API_BASE}/clear-old`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    alert(`${data.deleted_count} logs foram removidos com sucesso!`);
                    loadStats();
                    loadLogs();
                } else {
                    alert('Erro ao remover logs antigos');
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro de conexão');
            }
        }

        function goToPage(page) {
            currentPage = page;
            loadLogs();
        }

        function showLoading(show) {
            elements.loading.style.display = show ? 'block' : 'none';
            elements.logsTable.style.display = show ? 'none' : 'table';
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('pt-BR') + ' ' + 
                   date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        }

        function formatAction(action) {
            const actions = {
                'login': 'Login',
                'logout': 'Logout', 
                'api_access': 'Acesso API'
            };
            return actions[action] || action;
        }

        function formatStatus(status) {
            const statuses = {
                'success': 'Sucesso',
                'failed': 'Falhou',
                'denied': 'Negado'
            };
            return statuses[status] || status;
        }
    </script>
</body>
</html>
