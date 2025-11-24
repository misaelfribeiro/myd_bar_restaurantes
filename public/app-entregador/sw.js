const CACHE_NAME = 'myd-entregador-v1';
const urlsToCache = [
    '/app-entregador/',
    '/app-entregador/index.html',
    '/app-entregador/css/style.css',
    '/app-entregador/js/config.js',
    '/app-entregador/js/auth.js',
    '/app-entregador/js/entregas.js',
    '/app-entregador/js/location.js',
    '/app-entregador/js/app.js',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
    'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'
];

// Instalação do Service Worker
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(urlsToCache))
    );
});

// Ativação do Service Worker
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

// Fetch - estratégia Network First
self.addEventListener('fetch', event => {
    event.respondWith(
        fetch(event.request)
            .then(response => {
                // Clonar a resposta
                const responseClone = response.clone();
                
                // Salvar no cache
                caches.open(CACHE_NAME)
                    .then(cache => cache.put(event.request, responseClone));
                
                return response;
            })
            .catch(() => {
                // Se falhar, tentar do cache
                return caches.match(event.request);
            })
    );
});

// Push Notifications
self.addEventListener('push', event => {
    const data = event.data ? event.data.json() : {};
    
    const options = {
        body: data.body || 'Nova notificação',
        icon: '/app-entregador/img/icon-192.png',
        badge: '/app-entregador/img/icon-192.png',
        vibrate: [200, 100, 200],
        data: data
    };
    
    event.waitUntil(
        self.registration.showNotification(data.title || 'MyD Entregador', options)
    );
});

// Click na notificação
self.addEventListener('notificationclick', event => {
    event.notification.close();
    
    event.waitUntil(
        clients.openWindow('/app-entregador/')
    );
});
