// Firebase Cloud Messaging Service Worker
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js');

// Firebase configuration
firebase.initializeApp({
    apiKey: "AIzaSyCrVdWT_inNqc-MYtw3l1krKdwnKoS71A4",
    authDomain: "speedfood-b4495.firebaseapp.com",
    projectId: "speedfood-b4495",
    storageBucket: "speedfood-b4495.firebasestorage.app",
    messagingSenderId: "1030985661644",
    appId: "1:1030985661644:web:21270e407199369f085669"
});

const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage((payload) => {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    
    const notificationTitle = payload.notification?.title || 'Nova Notificação';
    const notificationOptions = {
        body: payload.notification?.body || 'Você tem uma nova atualização',
        icon: '/app-cliente/icons/icon-192x192.png',
        badge: '/app-cliente/icons/icon-192x192.png',
        vibrate: [200, 100, 200],
        data: payload.data || {},
        actions: [
            {
                action: 'open',
                title: 'Ver Pedido'
            }
        ]
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});

// Handle notification click
self.addEventListener('notificationclick', (event) => {
    console.log('[firebase-messaging-sw.js] Notification click received.');
    
    event.notification.close();
    
    // Open app and navigate to orders
    event.waitUntil(
        clients.openWindow('/app-cliente/#orders')
    );
});
