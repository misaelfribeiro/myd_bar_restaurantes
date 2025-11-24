// Registro do Service Worker e PWA
(function() {
    'use strict';

    console.log('🔧 Inicializando PWA...');

    // Registrar Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            const swPath = './service-worker.js';
            console.log('📝 Registrando Service Worker:', swPath);
            
            navigator.serviceWorker.register(swPath)
                .then(registration => {
                    console.log('✅ Service Worker registrado com sucesso!');
                    console.log('Scope:', registration.scope);
                    
                    // Verificar atualizações
                    registration.addEventListener('updatefound', () => {
                        console.log('🔄 Nova versão encontrada!');
                    });
                })
                .catch(error => {
                    console.error('❌ Erro ao registrar Service Worker:', error);
                });
        });
    } else {
        console.warn('⚠️ Service Worker não suportado neste navegador');
    }

    // Detectar quando app está instalado
    window.addEventListener('appinstalled', () => {
        console.log('🎉 App instalado com sucesso!');
        localStorage.setItem('app_installed', 'true');
        showInstallSuccessMessage();
    });

    // Mostrar banner de instalação
    let deferredPrompt;
    
    window.addEventListener('beforeinstallprompt', (e) => {
        console.log('💡 Prompt de instalação disponível');
        e.preventDefault();
        deferredPrompt = e;
        
        // Verificar se já foi instalado ou se usuário já recusou
        const installDismissed = localStorage.getItem('install_dismissed');
        const appInstalled = localStorage.getItem('app_installed');
        
        if (!installDismissed && !appInstalled) {
            showInstallBanner();
        }
    });

    // Mostrar banner de instalação
    function showInstallBanner() {
        const banner = document.createElement('div');
        banner.id = 'installBanner';
        banner.className = 'install-banner';
        banner.innerHTML = `
            <div class="install-banner-content">
                <div class="d-flex align-items-center">
                    <i class="fas fa-mobile-alt fa-2x text-primary me-3"></i>
                    <div class="flex-grow-1">
                        <strong>Instalar App</strong>
                        <p class="mb-0 small">Adicione à tela inicial para acesso rápido</p>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary btn-sm flex-grow-1" id="btnInstall">
                        <i class="fas fa-download me-1"></i> Instalar
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" id="btnDismiss">
                        Agora não
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(banner);
        
        // Animar entrada
        setTimeout(() => banner.classList.add('show'), 100);
        
        // Botão instalar
        document.getElementById('btnInstall').addEventListener('click', async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                console.log(`Escolha do usuário: ${outcome}`);
                
                if (outcome === 'accepted') {
                    console.log('✅ Usuário aceitou instalação');
                } else {
                    console.log('❌ Usuário recusou instalação');
                    localStorage.setItem('install_dismissed', 'true');
                }
                
                deferredPrompt = null;
                hideInstallBanner();
            }
        });
        
        // Botão dispensar
        document.getElementById('btnDismiss').addEventListener('click', () => {
            localStorage.setItem('install_dismissed', 'true');
            hideInstallBanner();
        });
    }

    function hideInstallBanner() {
        const banner = document.getElementById('installBanner');
        if (banner) {
            banner.classList.remove('show');
            setTimeout(() => banner.remove(), 300);
        }
    }

    function showInstallSuccessMessage() {
        const toast = document.createElement('div');
        toast.className = 'alert alert-success position-fixed top-0 start-50 translate-middle-x shadow-lg';
        toast.style.cssText = 'z-index: 9999; margin-top: 80px; min-width: 300px;';
        toast.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle fa-2x text-success me-3"></i>
                <div>
                    <strong>App Instalado!</strong>
                    <p class="mb-0 small">Agora você pode acessar rapidamente pela tela inicial</p>
                </div>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => toast.remove(), 5000);
    }

    // Detectar modo standalone (app instalado)
    function isStandalone() {
        return (window.matchMedia('(display-mode: standalone)').matches) ||
               (window.navigator.standalone) ||
               document.referrer.includes('android-app://');
    }

    if (isStandalone()) {
        console.log('📱 App rodando em modo standalone');
        document.body.classList.add('standalone-mode');
    }

    // Atualização do Service Worker
    navigator.serviceWorker?.addEventListener('controllerchange', () => {
        console.log('🔄 Nova versão disponível');
        showUpdateNotification();
    });

    function showUpdateNotification() {
        const toast = document.createElement('div');
        toast.className = 'alert alert-info position-fixed bottom-0 start-50 translate-middle-x shadow-lg';
        toast.style.cssText = 'z-index: 9999; margin-bottom: 80px; min-width: 300px;';
        toast.innerHTML = `
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <strong>Nova versão disponível!</strong>
                    <p class="mb-0 small">Clique para atualizar</p>
                </div>
                <button class="btn btn-sm btn-info" onclick="window.location.reload()">
                    Atualizar
                </button>
            </div>
        `;
        
        document.body.appendChild(toast);
    }

})();
