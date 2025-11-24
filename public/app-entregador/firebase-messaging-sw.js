// Firebase Cloud Messaging Service Worker
try {
    importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js');
    importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js');
} catch (error) {
    console.error('Error importing Firebase scripts:', error);
}

// Firebase configuration
try {
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
        
        const notificationTitle = payload.notification?.title || 'Nova Entrega Disponível!';
        const notificationOptions = {
            body: payload.notification?.body || 'Uma nova entrega está disponível',
            icon: '/app-entregador/img/icon-192.png',
            badge: '/app-entregador/img/icon-192.png',
            vibrate: [200, 100, 200, 100, 200],
            tag: 'nova-entrega',
            data: payload.data || {},
            actions: [
                {
                    action: 'view',
                    title: 'Ver Entrega'
                },
                {
                    action: 'dismiss',
                    title: 'Fechar'
                }
            ]
        };

        self.registration.showNotification(notificationTitle, notificationOptions);
    });

    // Handle notification click
    self.addEventListener('notificationclick', (event) => {
        console.log('[firebase-messaging-sw.js] Notification click received.');
        
        event.notification.close();
        
        if (event.action === 'view') {
            // Open app and navigate to entregas
            event.waitUntil(
                clients.openWindow('/app-entregador/#entregas')
            );
        }
    });
} catch (error) {
    console.error('Error initializing Firebase in SW:', error);
}
