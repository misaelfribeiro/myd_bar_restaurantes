/**
 * Auth Guard - Middleware JavaScript para verificação de autenticação
 * Inclua este script em todas as páginas que requerem autenticação
 */

(function() {
    'use strict';

    const API_BASE = '/api';
    
    // Páginas públicas que não precisam de autenticação
    const PUBLIC_PAGES = [
        '/login',
        '/login-niveis',
        '/login-simple',
        '/login-test',
        '/register'
    ];

    // Verificar se a página atual é pública
    function isPublicPage() {
        const currentPath = window.location.pathname;
        return PUBLIC_PAGES.some(page => currentPath.includes(page));
    }

    // Verificar autenticação
    async function checkAuth() {
        // Se for página pública, não fazer nada
        if (isPublicPage()) {
            return;
        }

        const token = localStorage.getItem('auth_token');
        const user = localStorage.getItem('user');

        // Se não tem token, redirecionar para login
        if (!token || !user) {
            console.log('🔒 Acesso negado: Usuário não autenticado');
            redirectToLogin();
            return;
        }

        try {
            // Pegar CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            // Validar token com o backend
            const response = await fetch(`${API_BASE}/auth/me`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                credentials: 'include'
            });

            if (!response.ok) {
                console.log('🔒 Token inválido ou expirado');
                clearAuthAndRedirect();
                return;
            }

            const userData = await response.json();
            const currentUser = userData.usuario || userData;
            
            // Atualizar dados do usuário no localStorage
            localStorage.setItem('user', JSON.stringify(currentUser));
            
            console.log(`✅ Autenticado como: ${currentUser.nome} (${currentUser.role})`);
            
            // Verificar se tem permissão para acessar a página atual
            checkPagePermission(currentUser.role);

        } catch (error) {
            console.error('❌ Erro ao verificar autenticação:', error);
            clearAuthAndRedirect();
        }
    }

    // Verificar permissão para página específica
    function checkPagePermission(role) {
        const currentPath = window.location.pathname;
        
        // Regras de permissão por role
        const permissions = {
            admin: ['*'], // Admin acessa tudo
            gerente: ['/dashboard', '/produtos', '/pedidos', '/mesas', '/relatorios'],
            garcom: ['/dashboard', '/pedidos', '/mesas', '/cardapio', '/garcom'],
            caixa: ['/dashboard', '/caixa', '/pedidos', '/relatorio'],
            cliente: ['/dashboard', '/cardapio', '/meu-pedido']
        };

        const allowedPaths = permissions[role] || [];
        
        // Se role tem acesso total (*), permitir
        if (allowedPaths.includes('*')) {
            return;
        }

        // Verificar se o caminho atual está permitido
        const hasPermission = allowedPaths.some(path => currentPath.includes(path));

        if (!hasPermission) {
            console.log(`🚫 Acesso negado: ${role} não tem permissão para ${currentPath}`);
            showAccessDenied(role);
        }
    }

    // Limpar autenticação e redirecionar
    function clearAuthAndRedirect() {
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        redirectToLogin();
    }

    // Redirecionar para login
    function redirectToLogin() {
        const currentUrl = encodeURIComponent(window.location.pathname);
        window.location.href = `/login-niveis?redirect=${currentUrl}`;
    }

    // Mostrar mensagem de acesso negado
    function showAccessDenied(role) {
        document.body.innerHTML = `
            <div style="display: flex; align-items: center; justify-content: center; min-height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); font-family: 'Segoe UI', sans-serif;">
                <div style="background: white; border-radius: 20px; padding: 40px; max-width: 500px; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                    <i class="fas fa-lock" style="font-size: 4rem; color: #dc3545; margin-bottom: 20px;"></i>
                    <h2 style="color: #333; margin-bottom: 15px;">Acesso Negado</h2>
                    <p style="color: #666; margin-bottom: 30px;">
                        Seu nível de acesso (<strong>${role}</strong>) não tem permissão para acessar esta página.
                    </p>
                    <button onclick="window.location.href='/'" 
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                                   color: white; 
                                   border: none; 
                                   padding: 12px 30px; 
                                   border-radius: 10px; 
                                   font-size: 1rem; 
                                   cursor: pointer; 
                                   margin-right: 10px;">
                        <i class="fas fa-home"></i> Ir para Dashboard
                    </button>
                    <button onclick="logout()" 
                            style="background: #dc3545; 
                                   color: white; 
                                   border: none; 
                                   padding: 12px 30px; 
                                   border-radius: 10px; 
                                   font-size: 1rem; 
                                   cursor: pointer;">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </button>
                </div>
            </div>
        `;
    }

    // Função global de logout
    window.logout = async function() {
        const token = localStorage.getItem('auth_token');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        try {
            await fetch(`${API_BASE}/auth/logout`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                credentials: 'include'
            });
        } catch (error) {
            console.error('Erro ao fazer logout:', error);
        }

        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        window.location.href = '/login-niveis';
    };

    // Interceptar requisições fetch para adicionar token automaticamente
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        let [resource, config] = args;
        
        // Se for requisição para API, adicionar token e CSRF
        if (resource.toString().includes('/api/')) {
            const token = localStorage.getItem('auth_token');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            config = config || {};
            config.headers = config.headers || {};
            config.credentials = 'include';
            
            if (token) {
                config.headers['Authorization'] = `Bearer ${token}`;
            }
            if (csrfToken) {
                config.headers['X-CSRF-TOKEN'] = csrfToken;
            }
        }

        return originalFetch(resource, config);
    };

    // Executar verificação quando a página carregar
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', checkAuth);
    } else {
        checkAuth();
    }

    // Verificar periodicamente se o token ainda é válido (a cada 5 minutos)
    setInterval(async () => {
        if (!isPublicPage()) {
            const token = localStorage.getItem('auth_token');
            if (token) {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const response = await fetch(`${API_BASE}/auth/me`, {
                        method: 'GET',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken || ''
                        },
                        credentials: 'include'
                    });

                    if (!response.ok) {
                        console.log('🔒 Sessão expirada, redirecionando para login...');
                        clearAuthAndRedirect();
                    }
                } catch (error) {
                    console.error('Erro ao verificar sessão:', error);
                }
            }
        }
    }, 5 * 60 * 1000); // 5 minutos

    console.log('🛡️ Auth Guard inicializado');

})();
