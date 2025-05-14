/**
 * Service Worker per la gestione delle notifiche push
 */

self.addEventListener('push', function(event) {
    if (event.data) {
        // Estrai dati dalla notifica
        try {
            const data = event.data.json();
            const title = data.title || 'CoreSuite Notifica';
            const options = {
                body: data.body || 'Hai una nuova notifica',
                icon: data.icon || '/assets/images/logo.svg',
                badge: '/assets/images/badge.png',
                data: {
                    url: data.url || '/'
                },
                vibrate: [100, 50, 100],
                requireInteraction: data.requireInteraction || false
            };
            
            // Mostra la notifica
            event.waitUntil(
                self.registration.showNotification(title, options)
            );
        } catch (e) {
            console.error('Errore nell\'elaborazione della notifica push:', e);
        }
    }
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    
    // Naviga alla URL specificata
    if (event.notification.data && event.notification.data.url) {
        event.waitUntil(
            clients.openWindow(event.notification.data.url)
        );
    }
});

self.addEventListener('install', function(event) {
    self.skipWaiting();
});

self.addEventListener('activate', function(event) {
    return self.clients.claim();
});
