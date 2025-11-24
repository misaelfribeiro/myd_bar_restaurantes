// Service Worker para PWA - MyD App
const CACHE_NAME = 'myd-app-v1.0.1';
const urlsToCache = [
  './',
  './index.html',
  './css/app.css',
  './js/app.js',
  './js/auth.js',
  './js/menu.js',
  './js/cart.js',
  './js/orders.js',
  './js/profile.js',
  './js/dark-mode.js',
  './js/pwa-install.js'
];

// Instalar Service Worker
self.addEventListener('install', event => {
  console.log('[SW] Instalando Service Worker v1.0.1...');
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('[SW] Abrindo cache');
        return cache.addAll(urlsToCache);
      })
      .then(() => {
        console.log('[SW] ✅ Todos os arquivos cacheados com sucesso!');
      })
      .catch(err => {
        console.error('[SW] ❌ Erro ao cachear arquivos:', err);
      })
  );
  self.skipWaiting();
});

// Ativar Service Worker
self.addEventListener('activate', event => {
  console.log('[SW] Ativando Service Worker...');
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            console.log('[SW] Removendo cache antigo:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  return self.clients.claim();
});

// Interceptar requisições
self.addEventListener('fetch', event => {
  const url = new URL(event.request.url);
  
  // Não cachear requisições da API
  if (url.pathname.startsWith('/api/')) {
    return;
  }
  
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // Retornar do cache se existir
        if (response) {
          // Atualizar cache em background
          fetch(event.request).then(networkResponse => {
            caches.open(CACHE_NAME).then(cache => {
              cache.put(event.request, networkResponse);
            });
          }).catch(() => {});
          
          return response;
        }
        
        // Buscar da rede
        return fetch(event.request).then(networkResponse => {
          // Cachear a resposta para próximas requisições
          if (event.request.method === 'GET') {
            const responseToCache = networkResponse.clone();
            caches.open(CACHE_NAME).then(cache => {
              cache.put(event.request, responseToCache);
            });
          }
          return networkResponse;
        });
      })
      .catch(() => {
        // Se offline e não tem no cache, retornar página offline
        if (event.request.destination === 'document') {
          return caches.match('/app-cliente/index.html');
        }
      })
  );
});

// Notificações Push (preparado para futuro)
self.addEventListener('push', event => {
  const data = event.data ? event.data.json() : {};
  const title = data.title || 'MyD Bar & Restaurantes';
  const options = {
    body: data.body || 'Você tem uma nova atualização!',
    icon: '/app-cliente/icons/icon-192x192.png',
    badge: '/app-cliente/icons/icon-96x96.png',
    vibrate: [200, 100, 200],
    data: data.url || '/app-cliente/',
    actions: [
      { action: 'open', title: 'Abrir' },
      { action: 'close', title: 'Fechar' }
    ]
  };
  
  event.waitUntil(
    self.registration.showNotification(title, options)
  );
});

// Ação ao clicar na notificação
self.addEventListener('notificationclick', event => {
  event.notification.close();
  
  if (event.action === 'open' || !event.action) {
    event.waitUntil(
      clients.openWindow(event.notification.data || '/app-cliente/')
    );
  }
});

// Sincronização em background
self.addEventListener('sync', event => {
  console.log('[SW] Sincronização em background:', event.tag);
  
  if (event.tag === 'sync-orders') {
    event.waitUntil(syncOrders());
  }
});

async function syncOrders() {
  try {
    // Implementar lógica de sincronização de pedidos offline
    console.log('[SW] Sincronizando pedidos...');
  } catch (error) {
    console.error('[SW] Erro ao sincronizar:', error);
  }
}
