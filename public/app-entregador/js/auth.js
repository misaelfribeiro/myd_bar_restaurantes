// Autenticação
let currentUser = null;

// Verificar se já está logado ao carregar
document.addEventListener('DOMContentLoaded', function() {
    checkAuth();
});

// Verificar autenticação
function checkAuth() {
    const token = localStorage.getItem(STORAGE_KEYS.TOKEN);
    const user = localStorage.getItem(STORAGE_KEYS.USER);
    
    if (token && user) {
        currentUser = JSON.parse(user);
        showApp();
        carregarDadosIniciais();
    } else {
        showLogin();
    }
}

// Mostrar tela de login
function showLogin() {
    document.getElementById('loginScreen').classList.add('active');
    document.getElementById('appScreen').classList.remove('active');
}

// Mostrar app
function showApp() {
    document.getElementById('loginScreen').classList.remove('active');
    document.getElementById('appScreen').classList.add('active');
    
    // Atualizar informações do usuário
    if (currentUser) {
        document.getElementById('menuNome').textContent = currentUser.nome;
        document.getElementById('menuEmail').textContent = currentUser.email;
        document.getElementById('perfilNome').textContent = currentUser.nome;
        document.getElementById('perfilEmail').textContent = currentUser.email;
        document.getElementById('perfilTelefone').textContent = currentUser.telefone || '-';
        document.getElementById('perfilCpf').textContent = currentUser.cpf || '-';
        document.getElementById('perfilVeiculo').textContent = currentUser.veiculo || '-';
        
        // Atualizar disponibilidade
        const disponivel = localStorage.getItem(STORAGE_KEYS.DISPONIVEL) === 'true';
        atualizarStatusUI(disponivel);
    }
}

// Login
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const email = document.getElementById('loginEmail').value;
    const senha = document.getElementById('loginSenha').value;
    
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Entrando...';
    
    try {
        const response = await fetch(ENDPOINTS.LOGIN, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, senha })
        });
        
        const data = await response.json();
        
        if (data.success) {
            localStorage.setItem(STORAGE_KEYS.TOKEN, data.token);
            localStorage.setItem(STORAGE_KEYS.USER, JSON.stringify(data.entregador));
            localStorage.setItem(STORAGE_KEYS.DISPONIVEL, 'true');
            currentUser = data.entregador;
            
            showApp();
            carregarDadosIniciais();
        } else {
            alert(data.message || 'Erro ao fazer login');
        }
    } catch (error) {
        console.error('Erro no login:', error);
        alert('Erro ao conectar com o servidor');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Entrar';
    }
});

// Logout
function logout() {
    if (confirm('Deseja realmente sair?')) {
        localStorage.removeItem(STORAGE_KEYS.TOKEN);
        localStorage.removeItem(STORAGE_KEYS.USER);
        localStorage.removeItem(STORAGE_KEYS.DISPONIVEL);
        currentUser = null;
        showLogin();
    }
}

// Toggle disponibilidade
async function toggleDisponibilidade() {
    const disponivel = localStorage.getItem(STORAGE_KEYS.DISPONIVEL) !== 'true';
    
    try {
        const response = await fetchAPI(ENDPOINTS.TOGGLE_DISPONIBILIDADE, {
            method: 'POST',
            body: JSON.stringify({ disponivel })
        });
        
        if (response.success) {
            localStorage.setItem(STORAGE_KEYS.DISPONIVEL, disponivel.toString());
            atualizarStatusUI(disponivel);
            
            if (disponivel) {
                carregarEntregasDisponiveis();
            }
        }
    } catch (error) {
        console.error('Erro ao atualizar disponibilidade:', error);
    }
}

// Atualizar UI do status
function atualizarStatusUI(disponivel) {
    const icon = document.getElementById('statusIcon');
    const badge = document.getElementById('statusBadge');
    
    if (disponivel) {
        icon.className = 'fas fa-toggle-on';
        icon.style.color = 'var(--success-color)';
        badge.textContent = 'Online';
        badge.className = 'status-badge';
    } else {
        icon.className = 'fas fa-toggle-off';
        icon.style.color = 'var(--danger-color)';
        badge.textContent = 'Offline';
        badge.className = 'status-badge offline';
    }
}

// Helper para fetch com autenticação
async function fetchAPI(url, options = {}) {
    const token = localStorage.getItem(STORAGE_KEYS.TOKEN);
    
    const defaultOptions = {
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        }
    };
    
    const response = await fetch(url, { ...defaultOptions, ...options });
    
    if (response.status === 401) {
        logout();
        throw new Error('Não autenticado');
    }
    
    return await response.json();
}

// Recuperar senha
function recuperarSenha() {
    const email = prompt('Digite seu e-mail:');
    if (email) {
        alert('Instruções enviadas para: ' + email);
    }
}
