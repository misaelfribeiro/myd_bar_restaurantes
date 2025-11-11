<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>👥 Gestão de Usuários - Sistema Bar & Restaurante</title>
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
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
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

        .header p {
            opacity: 0.9;
            font-size: 1.1em;
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .stat-card.admin { border-color: #dc3545; }
        .stat-card.gerente { border-color: #fd7e14; }
        .stat-card.garcom { border-color: #ffc107; }
        .stat-card.cliente { border-color: #28a745; }
        .stat-card.total { border-color: #6f42c1; }

        .stat-number {
            font-size: 2.5em;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .stat-label {
            color: #666;
            font-size: 1.1em;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .search-box {
            flex: 1;
            max-width: 400px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 12px 45px 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1em;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        }

        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .btn {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            font-size: 1em;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,123,255,0.3);
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        }

        .btn-success:hover {
            box-shadow: 0 5px 15px rgba(40,167,69,0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }

        .btn-danger:hover {
            box-shadow: 0 5px 15px rgba(220,53,69,0.3);
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            color: #212529;
        }

        .users-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background: #f8f9fa;
            padding: 18px 15px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
        }

        .table td {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }

        .table tr:hover {
            background: #f8f9fa;
        }

        .role-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .role-badge.admin { background: #ff4757; color: white; }
        .role-badge.gerente { background: #ff9ff3; color: #721c24; }
        .role-badge.garcom { background: #ffeaa7; color: #856404; }
        .role-badge.cliente { background: #00b894; color: white; }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85em;
            border-radius: 6px;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 15px;
            padding: 30px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
        }

        .modal-header {
            margin-bottom: 25px;
        }

        .modal-header h2 {
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #495057;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1em;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        .loading.show {
            display: block;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }

        .alert.show {
            display: block;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .empty-state img {
            max-width: 200px;
            opacity: 0.5;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .controls {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                max-width: none;
            }

            .table {
                font-size: 0.9em;
            }

            .action-buttons {
                flex-direction: column;
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
            <h1>👥 Gestão de Usuários</h1>
            <p>Sistema de controle e administração de usuários por perfil</p>
        </div>

        <div class="main-content">
            <!-- Alertas -->
            <div id="alert" class="alert">
                <span id="alertMessage"></span>
            </div>

            <!-- Estatísticas -->
            <div class="stats-grid" id="statsGrid">
                <!-- Preenchido via JavaScript -->
            </div>

            <!-- Controles -->
            <div class="controls">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Buscar por nome ou email...">
                    <span class="search-icon">🔍</span>
                </div>
                <button class="btn btn-success" id="addUserBtn">
                    <span>👤</span> Adicionar Usuário
                </button>
            </div>

            <!-- Tabela de Usuários -->
            <div class="users-table">
                <div class="loading" id="loading">
                    <div>⏳ Carregando usuários...</div>
                </div>
                <table class="table" id="usersTable" style="display: none;">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Perfil</th>
                            <th>Criado em</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        <!-- Preenchido via JavaScript -->
                    </tbody>
                </table>
                <div id="emptyState" class="empty-state" style="display: none;">
                    <div style="font-size: 4em;">👥</div>
                    <h3>Nenhum usuário encontrado</h3>
                    <p>Clique em "Adicionar Usuário" para começar</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Usuário -->
    <div class="modal" id="userModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Adicionar Usuário</h2>
                <p id="modalSubtitle">Preencha os dados abaixo</p>
            </div>
            <form id="userForm">
                <div class="form-group">
                    <label for="nome">Nome Completo</label>
                    <input type="text" id="nome" name="nome" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="role">Perfil de Acesso</label>
                    <select id="role" name="role" class="form-control" required>
                        <option value="">Selecione um perfil</option>
                        <option value="admin">🔴 Administrador - Acesso Total</option>
                        <option value="gerente">🟠 Gerente - Gestão Operacional</option>
                        <option value="garcom">🟡 Garçom - Operações Básicas</option>
                        <option value="cliente">🟢 Cliente - Apenas Consultas</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="password">Senha</label>
                    <input type="password" id="password" name="password" class="form-control" minlength="6">
                    <small style="color: #6c757d; margin-top: 5px; display: block;">
                        <span id="passwordHelp">Mínimo de 6 caracteres</span>
                    </small>
                </div>
                
                <div class="form-group">
                    <label for="password_confirmation">Confirmar Senha</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" minlength="6">
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn" id="cancelBtn">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="saveBtn">
                        <span>💾</span> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Configurações globais
        const API_BASE = '/api';
        let users = [];
        let editingUserId = null;

        // Elementos DOM
        const elements = {
            statsGrid: document.getElementById('statsGrid'),
            loading: document.getElementById('loading'),
            usersTable: document.getElementById('usersTable'),
            usersTableBody: document.getElementById('usersTableBody'),
            emptyState: document.getElementById('emptyState'),
            searchInput: document.getElementById('searchInput'),
            addUserBtn: document.getElementById('addUserBtn'),
            userModal: document.getElementById('userModal'),
            userForm: document.getElementById('userForm'),
            modalTitle: document.getElementById('modalTitle'),
            modalSubtitle: document.getElementById('modalSubtitle'),
            cancelBtn: document.getElementById('cancelBtn'),
            alert: document.getElementById('alert'),
            alertMessage: document.getElementById('alertMessage'),
            passwordHelp: document.getElementById('passwordHelp')
        };

        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            setupCSRF();
            loadStats();
            loadUsers();
            setupEventListeners();
        });

        // Configurar CSRF token
        function setupCSRF() {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch.defaults = {
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            };
        }

        // Event Listeners
        function setupEventListeners() {
            elements.addUserBtn.addEventListener('click', () => openModal());
            elements.cancelBtn.addEventListener('click', () => closeModal());
            elements.userForm.addEventListener('submit', handleSubmit);
            elements.searchInput.addEventListener('input', handleSearch);
            elements.userModal.addEventListener('click', (e) => {
                if (e.target === elements.userModal) closeModal();
            });

            // Monitorar mudanças no campo de edição
            document.getElementById('password').addEventListener('input', function() {
                if (editingUserId) {
                    elements.passwordHelp.textContent = 'Deixe em branco para manter a senha atual';
                }
            });
        }

        // Carregar estatísticas
        async function loadStats() {
            try {
                const response = await fetch('/user-management/stats');
                const data = await response.json();
                
                if (data.success) {
                    renderStats(data.stats);
                }
            } catch (error) {
                console.error('Erro ao carregar estatísticas:', error);
            }
        }

        // Renderizar estatísticas
        function renderStats(stats) {
            elements.statsGrid.innerHTML = `
                <div class="stat-card admin">
                    <div class="stat-number">${stats.admin}</div>
                    <div class="stat-label">🔴 Admins</div>
                </div>
                <div class="stat-card gerente">
                    <div class="stat-number">${stats.gerente}</div>
                    <div class="stat-label">🟠 Gerentes</div>
                </div>
                <div class="stat-card garcom">
                    <div class="stat-number">${stats.garcom}</div>
                    <div class="stat-label">🟡 Garçons</div>
                </div>
                <div class="stat-card cliente">
                    <div class="stat-number">${stats.cliente}</div>
                    <div class="stat-label">🟢 Clientes</div>
                </div>
                <div class="stat-card total">
                    <div class="stat-number">${stats.total}</div>
                    <div class="stat-label">👥 Total</div>
                </div>
            `;
        }

        // Carregar usuários
        async function loadUsers() {
            showLoading(true);
            
            try {
                const response = await fetch('/user-management/users');
                const data = await response.json();
                
                if (data.success) {
                    users = data.users;
                    renderUsers(users);
                } else {
                    showAlert('Erro ao carregar usuários', 'error');
                }
            } catch (error) {
                console.error('Erro ao carregar usuários:', error);
                showAlert('Erro de conexão ao carregar usuários', 'error');
            } finally {
                showLoading(false);
            }
        }

        // Renderizar usuários
        function renderUsers(usersToRender) {
            if (usersToRender.length === 0) {
                elements.usersTable.style.display = 'none';
                elements.emptyState.style.display = 'block';
                return;
            }

            elements.usersTable.style.display = 'table';
            elements.emptyState.style.display = 'none';

            elements.usersTableBody.innerHTML = usersToRender.map(user => `
                <tr>
                    <td><strong>${user.nome}</strong></td>
                    <td>${user.email}</td>
                    <td><span class="role-badge ${user.role}">${user.role}</span></td>
                    <td>${formatDate(user.created_at)}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-warning btn-sm" onclick="editUser(${user.id})">
                                ✏️ Editar
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteUser(${user.id}, '${user.nome}')">
                                🗑️ Excluir
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // Buscar usuários
        function handleSearch(e) {
            const searchTerm = e.target.value.toLowerCase();
            const filteredUsers = users.filter(user => 
                user.nome.toLowerCase().includes(searchTerm) ||
                user.email.toLowerCase().includes(searchTerm)
            );
            renderUsers(filteredUsers);
        }

        // Abrir modal
        function openModal(userId = null) {
            editingUserId = userId;
            
            if (userId) {
                const user = users.find(u => u.id === userId);
                elements.modalTitle.textContent = 'Editar Usuário';
                elements.modalSubtitle.textContent = `Editando: ${user.nome}`;
                elements.passwordHelp.textContent = 'Deixe em branco para manter a senha atual';
                
                document.getElementById('nome').value = user.nome;
                document.getElementById('email').value = user.email;
                document.getElementById('role').value = user.role;
                document.getElementById('password').required = false;
                document.getElementById('password_confirmation').required = false;
            } else {
                elements.modalTitle.textContent = 'Adicionar Usuário';
                elements.modalSubtitle.textContent = 'Preencha os dados abaixo';
                elements.passwordHelp.textContent = 'Mínimo de 6 caracteres';
                elements.userForm.reset();
                document.getElementById('password').required = true;
                document.getElementById('password_confirmation').required = true;
            }
            
            elements.userModal.classList.add('show');
        }

        // Fechar modal
        function closeModal() {
            elements.userModal.classList.remove('show');
            editingUserId = null;
            elements.userForm.reset();
        }

        // Submit do formulário
        async function handleSubmit(e) {
            e.preventDefault();
            
            const formData = new FormData(elements.userForm);
            const data = Object.fromEntries(formData.entries());
            
            // Validar senhas
            if (data.password && data.password !== data.password_confirmation) {
                showAlert('As senhas não coincidem', 'error');
                return;
            }

            try {
                const url = editingUserId 
                    ? `/user-management/users/${editingUserId}`
                    : '/user-management/users';
                
                const method = editingUserId ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    closeModal();
                    loadUsers();
                    loadStats();
                } else {
                    showAlert(result.message || 'Erro ao salvar usuário', 'error');
                }
            } catch (error) {
                console.error('Erro:', error);
                showAlert('Erro de conexão ao salvar usuário', 'error');
            }
        }

        // Editar usuário
        function editUser(userId) {
            openModal(userId);
        }

        // Excluir usuário
        async function deleteUser(userId, userName) {
            if (!confirm(`Tem certeza que deseja excluir o usuário "${userName}"?\n\nEsta ação não pode ser desfeita.`)) {
                return;
            }

            try {
                const response = await fetch(`/user-management/users/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    loadUsers();
                    loadStats();
                } else {
                    showAlert(result.message || 'Erro ao excluir usuário', 'error');
                }
            } catch (error) {
                console.error('Erro:', error);
                showAlert('Erro de conexão ao excluir usuário', 'error');
            }
        }

        // Mostrar/ocultar loading
        function showLoading(show) {
            elements.loading.style.display = show ? 'block' : 'none';
            elements.usersTable.style.display = show ? 'none' : 'table';
        }

        // Mostrar alerta
        function showAlert(message, type = 'success') {
            elements.alertMessage.textContent = message;
            elements.alert.className = `alert alert-${type} show`;
            
            setTimeout(() => {
                elements.alert.classList.remove('show');
            }, 5000);
        }

        // Formatar data
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('pt-BR') + ' às ' + 
                   date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        }
    </script>
</body>
</html>
