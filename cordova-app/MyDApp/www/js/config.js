// Configurações para ambiente mobile
const APP_CONFIG = {
    // URL do servidor (altere para produção)
    baseURL: 'http://myd.local', // ou seu domínio de produção
    
    // Configurações específicas para mobile
    isMobile: true,
    enableVibration: true,
    enablePushNotifications: false,
    
    // Configurações de API
    apiTimeout: 30000,
    retryAttempts: 3
};

// Configurar base URL para requisições
if (typeof window !== 'undefined') {
    window.APP_CONFIG = APP_CONFIG;
}