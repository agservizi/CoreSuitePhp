/**
 * Registrazione e gestione del service worker per CoreSuite
 */

// Variabili globali
let swRegistration = null;
let isSubscribed = false;

// Funzione per registrare il service worker
function registerServiceWorker() {
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/service-worker.js')
            .then(registration => {
                console.log('Service Worker registrato con successo:', registration);
                swRegistration = registration;
                
                // Dopo la registrazione, controlla lo stato della sottoscrizione
                checkPushSubscription();
            })
            .catch(error => {
                console.error('Errore nella registrazione del Service Worker:', error);
            });
    }
}

// Funzione per verificare lo stato della sottoscrizione alle notifiche push
function checkPushSubscription() {
    // Prima verifica se il browser supporta le push notifications
    if (!('PushManager' in window)) {
        console.log('Questo browser non supporta le notifiche push');
        return;
    }
    
    // Controlla lo stato attuale della sottoscrizione dal server
    fetch('/api/notifications/check-subscription.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                isSubscribed = data.subscribed;
                console.log('Stato sottoscrizione push:', isSubscribed ? 'Sottoscritto' : 'Non sottoscritto');
                
                // Se non è sottoscritto, tentare di sottoscrivere
                if (!isSubscribed && Notification.permission !== 'denied') {
                    subscribeToPushNotifications(swRegistration);
                }
            } else {
                console.error('Errore nel controllo dello stato di sottoscrizione:', data.message);
            }
        })
        .catch(error => {
            console.error('Errore nella verifica della sottoscrizione push:', error);
        });
}

// Funzione per sottoscriversi alle notifiche push
function subscribeToPushNotifications(registration) {
    // Se non è supportato, esci
    if (!('PushManager' in window)) {
        console.log('Le notifiche push non sono supportate da questo browser');
        return;
    }
    
    // Richiedi il permesso se non è già stato concesso
    if (Notification.permission !== 'granted') {
        // Richiedi il permesso
        Notification.requestPermission()
            .then(permission => {
                if (permission !== 'granted') {
                    console.log('Permesso notifiche non concesso');
                    return;
                }
                
                // Procedi con la sottoscrizione
                proceedWithSubscription(registration);
            });
    } else {
        // Permesso già concesso, verifica se esiste già una sottoscrizione
        registration.pushManager.getSubscription()
            .then(existingSubscription => {
                if (existingSubscription) {
                    console.log('Già sottoscritto alle notifiche push');
                    isSubscribed = true;
                    return;
                }
                
                // Procedi con la sottoscrizione
                proceedWithSubscription(registration);
            });
    }
}

// Funzione per procedere con la sottoscrizione dopo aver ottenuto i permessi
function proceedWithSubscription(registration) {
    // Ottieni la chiave pubblica VAPID dal server
    fetch('/api/notifications/get-public-key.php')
        .then(response => response.json())
        .then(data => {
            if (!data.success || !data.publicKey) {
                throw new Error('Impossibile ottenere la chiave pubblica VAPID');
            }
            
            // Converti la chiave pubblica in Uint8Array
            const convertedVapidKey = urlBase64ToUint8Array(data.publicKey);
            
            // Sottoscrivi
            return registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: convertedVapidKey
            });
        })
        .then(subscription => {
            // Salva la sottoscrizione sul server
            return fetch('/api/notifications/subscribe.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    subscription: subscription
                })
            });
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Sottoscrizione alle notifiche push completata');
                isSubscribed = true;
                
                // Mostra una notifica di successo all'utente
                if (typeof showNotification === 'function') {
                    showNotification(
                        'Notifiche attivate', 
                        'Riceverai notifiche push per gli aggiornamenti importanti', 
                        'success'
                    );
                }
            } else {
                console.error('Errore nella sottoscrizione:', data.message);
            }
        })
        .catch(error => {
            console.error('Errore nella sottoscrizione alle notifiche push:', error);
            
            // Mostra un messaggio all'utente se i permessi sono bloccati
            if (Notification.permission === 'denied') {
                if (typeof showNotification === 'function') {
                    showNotification(
                        'Notifiche bloccate', 
                        'Hai bloccato le notifiche. Abilita le notifiche nelle impostazioni del browser per ricevere aggiornamenti in tempo reale.', 
                        'warning'
                    );
                }
            }
        });
}

// Funzione per convertire una stringa base64 URL-safe in Uint8Array
// Necessaria per le chiavi VAPID
function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');
    
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    
    return outputArray;
}

// Funzione per inviare una notifica test al dispositivo attuale
function sendTestNotification() {
    if (!isSubscribed) {
        if (typeof showNotification === 'function') {
            showNotification(
                'Notifiche non attivate', 
                'Devi prima attivare le notifiche push', 
                'warning'
            );
        }
        return;
    }
    
    fetch('/api/notifications/send.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            userId: 'self', // Speciale valore per inviare solo a sé stessi
            title: 'Test notifica',
            body: 'Questa è una notifica di test da CoreSuite',
            icon: '/assets/images/logo.svg'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Notifica di test inviata con successo');
        } else {
            console.error('Errore nell\'invio della notifica di test:', data.message);
        }
    })
    .catch(error => {
        console.error('Errore nella richiesta di test notifica:', error);
    });
}

// Registra il service worker quando il documento è completamente caricato
document.addEventListener('DOMContentLoaded', function() {
    registerServiceWorker();
    
    // Gestisci eventuali pulsanti di test notifica
    const testNotificationBtn = document.getElementById('testNotificationBtn');
    if (testNotificationBtn) {
        testNotificationBtn.addEventListener('click', function() {
            sendTestNotification();
        });
    }
});
