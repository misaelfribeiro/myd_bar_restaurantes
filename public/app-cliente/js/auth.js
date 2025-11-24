// Autenticação do cliente com TOKEN

function showLogin() {
    // Esconder navegação inferior
    const bottomNav = document.querySelector('.bottom-nav');
    if (bottomNav) {
        bottomNav.style.display = 'none';
    }
    
    const content = `
        <div class="fade-in">
            <div class="text-center mb-4">
                <i class="fas fa-user-circle fa-4x text-primary mb-3"></i>
                <h4>Identificação</h4>
                <p class="text-muted">Digite seu telefone para continuar</p>
            </div>

            <div class="card">
                <div class="card-body">
                    <form onsubmit="loginWithPhone(event)">
                        <div class="mb-3">
                            <label class="form-label">Telefone</label>
                            <input type="tel" class="form-control form-control-lg" id="phoneInput" 
                                   placeholder="(11) 99999-9999" required>
                        </div>
                        <button type="submit" class="btn btn-primary-custom w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>Continuar
                        </button>
                    </form>
                </div>
            </div>

            <div class="text-center mt-3">
                <small class="text-muted">Novo por aqui?</small><br>
                <a href="#" onclick="showRegister(); return false;" class="text-primary fw-bold">
                    Criar conta
                </a>
            </div>
        </div>
    `;
    
    document.getElementById('mainContent').innerHTML = content;
}

async function loginWithPhone(event) {
    event.preventDefault();
    
    const phone = document.getElementById('phoneInput').value;
    const cleanPhone = phone.replace(/\D/g, '');
    
    try {
        const response = await fetch(`${API_BASE_URL}/app/auth/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                telefone: cleanPhone
            })
        });
        
        const data = await response.json();
        
        if (data.success && data.token) {
            // Salvar token e dados do cliente
            localStorage.setItem('app_token', data.token);
            localStorage.setItem('app_user', JSON.stringify(data.cliente));
            appState.user = data.cliente;
            appState.token = data.token;
            
            // Mostrar navegação
            const bottomNav = document.querySelector('.bottom-nav');
            if (bottomNav) {
                bottomNav.style.display = 'flex';
            }
            
            showAlert(data.is_new ? 'Bem-vindo!' : 'Bem-vindo de volta!', 'success');
            showHome();
        } else if (data.requires_registration) {
            // Cliente não encontrado - mostrar registro
            showRegister(cleanPhone);
        } else {
            showAlert(data.message || 'Erro ao fazer login', 'danger');
        }
    } catch (error) {
        console.error('Erro no login:', error);
        showAlert('Erro ao fazer login. Tente novamente.', 'danger');
    }
}

function showRegister(prefilledPhone = '') {
    const content = `
        <div class="fade-in">
            <div class="text-center mb-4">
                <i class="fas fa-user-plus fa-4x text-primary mb-3"></i>
                <h4>Criar Conta</h4>
                <p class="text-muted">Preencha seus dados para continuar</p>
            </div>

            <div class="card">
                <div class="card-body">
                    <form onsubmit="registerClient(event)">
                        <div class="mb-3">
                            <label class="form-label">Nome Completo *</label>
                            <input type="text" class="form-control" id="registerName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Telefone *</label>
                            <input type="tel" class="form-control" id="registerPhone" 
                                   value="${prefilledPhone}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">E-mail (opcional)</label>
                            <input type="email" class="form-control" id="registerEmail">
                        </div>
                        <button type="submit" class="btn btn-primary-custom w-100">
                            <i class="fas fa-check me-2"></i>Criar Conta
                        </button>
                    </form>
                </div>
            </div>

            <div class="text-center mt-3">
                <a href="#" onclick="showLogin(); return false;" class="text-primary">
                    <i class="fas fa-arrow-left me-1"></i>Voltar ao login
                </a>
            </div>
        </div>
    `;
    
    document.getElementById('mainContent').innerHTML = content;
}

async function registerClient(event) {
    event.preventDefault();
    
    const clientData = {
        telefone: document.getElementById('registerPhone').value.replace(/\D/g, ''),
        nome: document.getElementById('registerName').value,
        email: document.getElementById('registerEmail').value || null,
        tenant_cod: 'APP/EXTERNO' // Identificar que o cadastro veio do app externo
    };
    
    // Validação básica
    if (!clientData.telefone || clientData.telefone.length < 10) {
        showAlert('Informe um telefone válido', 'warning');
        return;
    }
    
    if (!clientData.nome || clientData.nome.trim().length < 3) {
        showAlert('Informe um nome válido', 'warning');
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE_URL}/app/auth/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(clientData)
        });
        
        const data = await response.json();
        
        if (data.success && data.token) {
            // Salvar token e dados do cliente
            localStorage.setItem('app_token', data.token);
            localStorage.setItem('app_user', JSON.stringify(data.cliente));
            appState.user = data.cliente;
            appState.token = data.token;
            
            const message = data.is_new ? 'Conta criada com sucesso!' : 'Login realizado com sucesso!';
            showAlert(message, 'success');
            showHome();
        } else {
            // Mostrar mensagem específica do servidor
            showAlert(data.message || 'Erro ao criar conta', 'danger');
        }
    } catch (error) {
        console.error('Erro ao cadastrar:', error);
        showAlert('Erro de conexão. Verifique sua internet e tente novamente.', 'danger');
    }
}

function logout() {
    console.log('🔓 Logout');
    
    // Guardar dados para requisições assíncronas
    const token = localStorage.getItem('app_token');
    const userId = appState.user?.id;
    
    // Limpar IMEDIATAMENTE (UI não espera nada)
    localStorage.removeItem('app_token');
    localStorage.removeItem('app_user');
    localStorage.removeItem('app_cart');
    appState.user = null;
    appState.token = null;
    appState.cart = [];
    
    // Esconder navegação
    const bottomNav = document.querySelector('.bottom-nav');
    if (bottomNav) bottomNav.style.display = 'none';
    
    // Mostrar login
    showLogin();
    
    // Requisições em background (não bloqueiam UI)
    if (userId && token) {
        setTimeout(() => {
            Promise.all([
                fetch(`${API_BASE_URL}/app/auth/logout`, {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                }),
                fetch(`${API_BASE_URL}/notificacao/desativar-token`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ user_id: userId })
                })
            ]).catch(() => {});
        }, 0);
    }
}

// Função auxiliar para fazer requisições autenticadas
async function authFetch(url, options = {}) {
    const token = localStorage.getItem('app_token');
    
    if (!token) {
        console.warn('Sem token - redirecionando para login');
        showLogin();
        throw new Error('No token');
    }
    
    const headers = {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        ...options.headers
    };
    
    const response = await fetch(url, {
        ...options,
        headers
    });
    
    // Se retornar 401, token inválido
    if (response.status === 401) {
        localStorage.removeItem('app_token');
        localStorage.removeItem('app_user');
        appState.user = null;
        appState.token = null;
        showAlert('Sessão expirada. Faça login novamente.', 'warning');
        showLogin();
        throw new Error('Unauthorized');
    }
    
    
    return response;
}
