// Firebase Cloud Messaging - Push Notifications
class NotificationManager {
    constructor() {
        this.messaging = null;
        this.currentToken = null;
        this.initialized = false;
    }

    async init() {
        if (this.initialized) return;

        try {
            // Check if Firebase is loaded
            if (typeof firebase === 'undefined') {
                console.warn('Firebase SDK not loaded');
                return false;
            }

            // Initialize Firebase
            if (!firebase.apps.length) {
                firebase.initializeApp({
                    apiKey: "AIzaSyCrVdWT_inNqc-MYtw3l1krKdwnKoS71A4",
                    authDomain: "speedfood-b4495.firebaseapp.com",
                    projectId: "speedfood-b4495",
                    storageBucket: "speedfood-b4495.firebasestorage.app",
                    messagingSenderId: "1030985661644",
                    appId: "1:1030985661644:web:21270e407199369f085669"
                });
            }

            this.messaging = firebase.messaging();
            this.initialized = true;

            // Request permission and get token
            await this.requestPermission();

            // Handle foreground messages
            this.setupForegroundHandler();

            console.log('✅ Firebase Cloud Messaging initialized');
            return true;
        } catch (error) {
            console.error('Error initializing FCM:', error);
            return false;
        }
    }

    async requestPermission() {
        try {
            // Check if notifications are supported
            if (!('Notification' in window)) {
                console.warn('This browser does not support notifications');
                return false;
            }

            // Check current permission status
            if (Notification.permission === 'granted') {
                console.log('Notification permission already granted');
                await this.getToken();
                return true;
            }

            if (Notification.permission === 'denied') {
                console.warn('Notification permission denied');
                return false;
            }

            // Request permission
            const permission = await Notification.requestPermission();
            
            if (permission === 'granted') {
                console.log('Notification permission granted');
                await this.getToken();
                return true;
            } else {
                console.warn('Notification permission denied by user');
                return false;
            }
        } catch (error) {
            console.error('Error requesting notification permission:', error);
            return false;
        }
    }

    async getToken() {
        try {
            if (!this.messaging) {
                console.warn('Messaging not initialized');
                return null;
            }

            // Get registration token
            const currentToken = await this.messaging.getToken({
                vapidKey: 'BGCflfuqhmo-LWoIfVwN--UMNg1jAbWUWz0SYNonpPZYTNmK6cns5BhEILZVtMGsOndYk7OX067JuOJuecUuBbI'
            });

            if (currentToken) {
                console.log('FCM Token:', currentToken);
                this.currentToken = currentToken;
                
                // Save token to backend
                await this.saveTokenToBackend(currentToken);
                
                return currentToken;
            } else {
                console.warn('No registration token available');
                return null;
            }
        } catch (error) {
            console.error('Error getting FCM token:', error);
            return null;
        }
    }

    async saveTokenToBackend(token) {
        try {
            console.log('💾 Salvando FCM token no backend...');
            const response = await authFetch('/api/app/auth/fcm-token', {
                method: 'POST',
                body: JSON.stringify({
                    token: token,
                    device_type: 'web',
                    device_id: this.getDeviceId()
                })
            });

            console.log('📡 Resposta do servidor:', response);

            if (response.success) {
                console.log('✅ FCM token saved to backend');
                localStorage.setItem('fcm_token', token);
            } else {
                console.error('❌ Failed to save FCM token:', response.message);
            }
        } catch (error) {
            console.error('❌ Error saving FCM token to backend:', error);
        }
    }

    setupForegroundHandler() {
        if (!this.messaging) return;

        this.messaging.onMessage((payload) => {
            console.log('📩 Foreground message received:', payload);

            const notificationTitle = payload.notification?.title || 'Nova Notificação';
            const notificationOptions = {
                body: payload.notification?.body || 'Você tem uma nova atualização',
                icon: '/app-cliente/icons/icon-192x192.png',
                badge: '/app-cliente/icons/icon-192x192.png',
                vibrate: [200, 100, 200],
                data: payload.data || {}
            };

            // Show notification if browser supports it
            if ('Notification' in window && Notification.permission === 'granted') {
                const notification = new Notification(notificationTitle, notificationOptions);
                
                notification.onclick = () => {
                    window.focus();
                    notification.close();
                    
                    // Navigate to orders if pedido_id is present
                    if (payload.data?.pedido_id) {
                        if (typeof showOrders === 'function') {
                            showOrders();
                        }
                    }
                };
            }

            // Play sound
            this.playNotificationSound();

            // Show in-app notification
            this.showInAppNotification(payload);
        });
    }

    showInAppNotification(payload) {
        const title = payload.notification?.title || 'Nova Notificação';
        const body = payload.notification?.body || '';

        // Create toast notification
        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        toast.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10000;
            max-width: 300px;
            animation: slideInRight 0.3s ease-out;
        `;

        toast.innerHTML = `
            <div class="d-flex align-items-start">
                <i class="fas fa-bell text-primary fa-lg me-3"></i>
                <div class="flex-grow-1">
                    <h6 class="mb-1 fw-bold">${title}</h6>
                    <p class="mb-0 small text-muted">${body}</p>
                </div>
                <button class="btn-close btn-close-sm ms-2" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;

        document.body.appendChild(toast);

        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.style.animation = 'slideOutRight 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    playNotificationSound() {
        try {
            const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBTGH0fPTgjMGHm7A7+OZSA0PVqzn77BZFwxKouL0wXAiBjaO1vLHdycFKHzJ8NuSPwoTYLbq7qVRFApEn+DyvmwhBTGH0fPTgjMGHm7A7+OZSA0PVqzn77BZFwxKouL0wXAiBjaO1vLHdycFKHzJ8NuSPwoTYLbq7qVRFApEn+DyvmwhBTGH0fPTgjMGHm7A7+OZSA0PVqzn77BZFwxKouL0wXAiBjaO1vLHdycFKHzJ8NuSPwoTYLbq7qVRFApEn+DyvmwhBTGH0fPTgjMGHm7A7+OZSA0PVqzn77BZFwxKouL0wXAiBjaO1vLHdycFKHzJ8NuSPwoTYLbq7qVRFApEn+DyvmwhBTGH0fPTgjMGHm7A7+OZSA0PVqzn77BZFwxKouL0wXAiBjaO1vLHdycFKHzJ8NuSPwoTYLbq7qVRFApEn+DyvmwhBTGH0fPTgjMGHm7A7+OZSA0PVqzn77BZFwxKouL0wXAiBjaO1vLHdycFKHzJ8NuSPwoTYLbq7qVRFApEn+DyvmwhBTGH0fPTgjMGHm7A7+OZSA0PVqzn77BZFwxKouL0wXAiBjaO1vLHdycFKHzJ8NuSPwoTYLbq7qVRFApEn+DyvmwhBTGH0fPTgjMGHm7A7+OZSA0PVqzn77BZFwxKouL0wXAiBjaO1vLHdycFKHzJ8NuSPwoTYLbq7qVRFApEn+DyvmwhBTGH0fPTgjMGHm7A7+OZSA0PVqzn77BZFwxKouL0wXAiBjaO1vLHdycFKHzJ8NuSPwoTYLbq7qVRFApEn+DyvmwhBTGH0fPTgjMGHm7A7+OZSA0PVqzn77BZFwxKouL0wXAiBjaO1vLHdycFKHzJ8NuSPwoTYLbq7qVRFApEn+DyvmwhBTGH0fPTgjMGHm7A7+OZSA0PVqzn77BZFwxKouL0wXAiBjaO1vLHdycFKHzJ8NuSPwoTYLbq7qVRFApEn+DyvmwhBTGH0fPTgjMGHm7A7+OZSA0PVqzn77BZFwxKouL0wXAiBjaO1vLHdycFKHzJ8NuSPw==');
            audio.volume = 0.3;
            audio.play().catch(e => console.log('Could not play sound:', e));
        } catch (error) {
            console.log('Error playing sound:', error);
        }
    }

    getDeviceId() {
        let deviceId = localStorage.getItem('device_id');
        if (!deviceId) {
            deviceId = 'web_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            localStorage.setItem('device_id', deviceId);
        }
        return deviceId;
    }

    async refreshToken() {
        await this.getToken();
    }

    getCurrentToken() {
        return this.currentToken;
    }

    isInitialized() {
        return this.initialized;
    }
}

// Global instance
window.notificationManager = new NotificationManager();

// Add CSS animation for toast
const notificationStyle = document.createElement('style');
notificationStyle.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(notificationStyle);
