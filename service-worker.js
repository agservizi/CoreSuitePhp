/**
 * Service Worker per CoreSuite
 * Gestisce le notifiche push e il caching offline
 */

const CACHE_NAME = 'coresuite-cache-v1';
const urlsToCache = [
  // Pagine principali
  '/',
  '/index.php',
  '/login.php',
  '/clients.php',
  '/contracts.php',
  '/phone-contract.php',
  '/energy-contract.php',
  '/client-details.php',
  
  // Asset CSS
  '/assets/css/custom.css',
  
  // Asset JS
  '/assets/js/notifications.js',
  '/assets/js/dashboard.js',
  '/assets/js/clients.js',
  '/assets/js/contracts.js',
  '/assets/js/service-worker-registration.js',
  
  // Immagini e icone
  '/assets/images/logo.svg',
  
  // Librerie esterne
  'https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css',
  'https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css',
  'https://code.jquery.com/jquery-3.6.0.min.js',
  'https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js',
  'https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js',
  'https://cdn.jsdelivr.net/npm/chart.js'
];

// Installazione e caching delle risorse
self.addEventListener('install', event => {
  // Forza l'attivazione immediata del nuovo service worker
  self.skipWaiting();
  
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Cache aperta con successo');
        return cache.addAll(urlsToCache);
      })
      .catch(error => {
        console.error('Errore durante il caching delle risorse:', error);
      })
  );
});

// Attivazione e pulizia delle vecchie cache
self.addEventListener('activate', event => {
  const cacheWhitelist = [CACHE_NAME];
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheWhitelist.indexOf(cacheName) === -1) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

// Gestione delle richieste offline
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // Cache hit - restituisce la risposta dalla cache
        if (response) {
          return response;
        }
        return fetch(event.request);
      }
    )
  );
});

// Gestione delle notifiche push
self.addEventListener('push', event => {
  let data = {};
  if (event.data) {
    data = event.data.json();
  }

  const options = {
    body: data.body || 'Nuova notifica da CoreSuite',
    icon: data.icon || '/assets/images/logo.svg',
    badge: '/assets/images/logo.svg',
    data: {
      url: data.url || '/'
    }
  };

  event.waitUntil(
    self.registration.showNotification(data.title || 'CoreSuite', options)
  );
});

// Gestione del click sulle notifiche
self.addEventListener('notificationclick', event => {
  event.notification.close();

  event.waitUntil(
    clients.matchAll({type: 'window'})
      .then(windowClients => {
        // Se c'è una finestra client aperta, focalizzala e naviga
        for (let i = 0; i < windowClients.length; i++) {
          const client = windowClients[i];
          if (client.url === event.notification.data.url && 'focus' in client) {
            return client.focus();
          }
        }
        // Altrimenti, apri una nuova finestra
        if (clients.openWindow) {
          return clients.openWindow(event.notification.data.url);
        }
      })
  );
});
