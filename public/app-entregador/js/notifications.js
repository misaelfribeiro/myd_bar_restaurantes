// Firebase Cloud Messaging - Push Notifications para Entregadores
class NotificationManager {
    constructor() {
        this.messaging = null;
        this.currentToken = null;
        this.initialized = false;
    }

    async init() {
        if (this.initialized) return;

        try {
            // Verificar se está rodando no Android nativo
            if (typeof Android !== 'undefined' && Android.getFCMToken) {
                console.log('📱 Rodando no Android - usando token nativo');
                const androidToken = Android.getFCMToken();
                if (androidToken) {
                    this.currentToken = androidToken;
                    await this.saveTokenToServer(androidToken);
                    this.initialized = true;
                    console.log('✅ Token Android registrado');
                    return true;
                }
            }

            // Check if Firebase is loaded (para web/PWA)
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
            console.error('❌ Error initializing FCM:', error);
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
                console.log('✅ Notification permission already granted');
                await this.getToken();
                return true;
            }

            if (Notification.permission === 'denied') {
                console.warn('❌ Notification permission denied');
                return false;
            }

            // Request permission
            const permission = await Notification.requestPermission();
            
            if (permission === 'granted') {
                console.log('✅ Notification permission granted');
                await this.getToken();
                return true;
            } else {
                console.warn('❌ Notification permission denied by user');
                return false;
            }
        } catch (error) {
            console.error('❌ Error requesting notification permission:', error);
            return false;
        }
    }

    async getToken() {
        try {
            if (!this.messaging) {
                console.warn('Messaging not initialized');
                return null;
            }

            // Wait for Service Worker to be ready
            const registration = await navigator.serviceWorker.ready;
            console.log('📡 Service Worker ready:', registration.scope);

            // Get registration token
            const currentToken = await this.messaging.getToken({
                vapidKey: 'BGCflfuqhmo-LWoIfVwN--UMNg1jAbWUWz0SYNonpPZYTNmK6cns5BhEILZVtMGsOndYk7OX067JuOJuecUuBbI',
                serviceWorkerRegistration: registration
            });

            if (currentToken) {
                console.log('📱 FCM Token:', currentToken);
                this.currentToken = currentToken;
                
                // Save token to server
                await this.saveTokenToServer(currentToken);
                
                return currentToken;
            } else {
                console.warn('No registration token available. Request permission to generate one.');
                return null;
            }
        } catch (error) {
            console.error('An error occurred while retrieving token:', error);
            return null;
        }
    }

    async saveTokenToServer(token) {
        try {
            const response = await fetchAPI(ENDPOINTS.SALVAR_TOKEN_FCM, {
                method: 'POST',
                body: JSON.stringify({ 
                    device_token: token,
                    device_type: 'web'
                })
            });

            if (response.success) {
                console.log('✅ Token FCM salvo no servidor');
                return true;
            } else {
                console.error('❌ Erro ao salvar token:', response.message);
                return false;
            }
        } catch (error) {
            console.error('❌ Error saving token to server:', error);
            return false;
        }
    }

    setupForegroundHandler() {
        if (!this.messaging) return;

        // Handle foreground messages
        this.messaging.onMessage((payload) => {
            console.log('📩 Message received in foreground:', payload);
            
            const notificationTitle = payload.notification?.title || 'Nova Entrega Disponível!';
            const notificationOptions = {
                body: payload.notification?.body || 'Uma nova entrega está disponível',
                icon: '/app-entregador/img/icon-192.png',
                badge: '/app-entregador/img/icon-192.png',
                vibrate: [200, 100, 200, 100, 200],
                tag: 'nova-entrega',
                data: payload.data || {}
            };

            // Show notification even in foreground
            if (Notification.permission === 'granted') {
                new Notification(notificationTitle, notificationOptions);
                
                // Play sound
                this.playNotificationSound();
            }
            
            // SEMPRE recarrega a lista de entregas quando receber notificação
            console.log('🔄 Recarregando lista de entregas...');
            setTimeout(() => {
                if (typeof carregarEntregasDisponiveis === 'function') {
                    carregarEntregasDisponiveis();
                    console.log('✅ Lista de entregas recarregada');
                }
                if (typeof carregarEntregasAceitas === 'function') {
                    carregarEntregasAceitas();
                }
            }, 500);
        });
    }

    playNotificationSound() {
        try {
            const audio = new Audio('/app-entregador/sounds/notification.mp3');
            audio.volume = 0.5;
            audio.play().catch(e => console.warn('Could not play sound:', e));
        } catch (error) {
            console.warn('Error playing notification sound:', error);
        }
    }

    async deleteToken() {
        try {
            if (!this.messaging || !this.currentToken) {
                return true;
            }

            await this.messaging.deleteToken();
            console.log('Token deleted.');
            this.currentToken = null;
            return true;
        } catch (error) {
            console.error('Unable to delete token:', error);
            return false;
        }
    }
}

// Initialize notification manager globally
const notificationManager = new NotificationManager();
