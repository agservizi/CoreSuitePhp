/**
 * notifications.js - Sistema di notifiche browser
 * CoreSuite
 */

// Inizializzazione al caricamento del documento
document.addEventListener('DOMContentLoaded', function() {
    // Inizializza il sistema di notifiche
    initNotificationSystem();
    
    // Richiedi permesso all'avvio dell'applicazione (se non già concesso/negato)
    if ('Notification' in window && Notification.permission !== 'granted' && Notification.permission !== 'denied') {
        showPermissionBanner();
    }
});

/**
 * Inizializza il sistema di notifiche
 */
function initNotificationSystem() {
    // Verifica se il contenitore delle notifiche esiste, altrimenti crealo
    if (!document.getElementById('notifications-container')) {
        const container = document.createElement('div');
        container.id = 'notifications-container';
        container.className = 'position-fixed';
        container.style.zIndex = '1050';
        container.style.right = '20px';
        container.style.bottom = '20px';
        document.body.appendChild(container);
    }
    
    // Controlla notifiche non lette ogni 5 minuti
    setInterval(checkPendingNotifications, 300000);
    
    // Prima chiamata all'avvio (dopo 3 secondi per non sovraccaricare il caricamento della pagina)
    setTimeout(checkPendingNotifications, 3000);
}

/**
 * Mostra banner per richiedere permesso notifiche
 */
function showPermissionBanner() {
    // Crea banner solo se non è già presente
    if (document.getElementById('notification-permission-banner')) {
        return;
    }
    
    const permissionBanner = document.createElement('div');
    permissionBanner.id = 'notification-permission-banner';
    permissionBanner.className = 'alert alert-info alert-dismissible fade show';
    permissionBanner.setAttribute('role', 'alert');
    permissionBanner.innerHTML = `
        <strong>Attiva le notifiche!</strong> Ricevi aggiornamenti su nuovi contratti e scadenze.
        <button type="button" id="enable-notifications" class="btn btn-sm btn-outline-info ml-2">Attiva</button>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
    
    const contentWrapper = document.querySelector('.content-wrapper');
    if (contentWrapper) {
        contentWrapper.insertAdjacentElement('afterbegin', permissionBanner);
        
        // Aggiungi event listener al pulsante
        document.getElementById('enable-notifications').addEventListener('click', requestNotificationPermission);
    }
}

/**
 * Richiedi permesso per le notifiche
 */
function requestNotificationPermission() {
    Notification.requestPermission().then(function(permission) {
        // Rimuovi il banner in ogni caso
        const banner = document.getElementById('notification-permission-banner');
        if (banner) banner.remove();
        
        if (permission === 'granted') {
            showNotification('Notifiche', 'Notifiche attivate con successo!', 'success');
            
            // Registra per notifiche push se service worker è supportato
            if ('serviceWorker' in navigator) {
                console.log('Il browser supporta i service worker');
            }
        } else {
            showNotification('Notifiche', 'Le notifiche sono state rifiutate', 'warning');
        }
    });
}

/**
 * Controlla notifiche non lette dal server
 */
function checkPendingNotifications() {
    fetch('api/notifications.php')
        .then(response => response.json())
        .then(data => {
            if (data.notifications && data.notifications.length > 0) {
                data.notifications.forEach(notification => {
                    showNotification(
                        notification.title || 'CoreSuite',
                        notification.message,
                        notification.type || 'info'
                    );
                    
                    // Se abilitato, mostra anche notifiche browser
                    if (Notification.permission === 'granted') {
                        showBrowserNotification(
                            notification.title || 'CoreSuite',
                            notification.message
                        );
                    }
                });
            }
        })
        .catch(error => console.error('Errore nel recupero notifiche:', error));
}

/**
 * Mostra una notifica nell'interfaccia utente
 * 
 * @param {string} title Titolo della notifica
 * @param {string} message Messaggio della notifica
 * @param {string} type Tipo di notifica (success, info, warning, error)
 * @param {number} timeout Tempo in ms prima che la notifica scompaia (default: 5000)
 */
function showNotification(title, message, type = 'info', timeout = 5000) {
    // Definisci le classi CSS in base al tipo
    let bgClass, iconClass;
    
    switch (type) {
        case 'success':
            bgClass = 'bg-success';
            iconClass = 'fas fa-check-circle';
            break;
        case 'warning':
            bgClass = 'bg-warning';
            iconClass = 'fas fa-exclamation-triangle';
            break;
        case 'error':
            bgClass = 'bg-danger';
            iconClass = 'fas fa-times-circle';
            break;
        case 'info':
        default:
            bgClass = 'bg-info';
            iconClass = 'fas fa-info-circle';
            break;
    }
    
    // Crea l'elemento di notifica
    const notificationId = 'notification-' + Date.now();
    const notificationHtml = `
        <div id="${notificationId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="${timeout}">
            <div class="toast-header ${bgClass} text-white">
                <i class="${iconClass} mr-2"></i>
                <strong class="mr-auto">${title}</strong>
                <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    // Verifica se il contenitore delle notifiche esiste, altrimenti crealo
    let notificationsContainer = document.getElementById('notifications-container');
    
    if (!notificationsContainer) {
        notificationsContainer = document.createElement('div');
        notificationsContainer.id = 'notifications-container';
        notificationsContainer.style.zIndex = '1050';
        notificationsContainer.style.right = '20px';
        notificationsContainer.style.bottom = '20px';
        document.body.appendChild(notificationsContainer);
    }
    
    // Aggiungi la notifica al contenitore
    notificationsContainer.innerHTML += notificationHtml;
    
    // Mostra la notifica
    const notification = $(`#${notificationId}`);
    notification.toast('show');
    
    // Rimuovi la notifica dal DOM dopo che è stata nascosta
    notification.on('hidden.bs.toast', function() {
        notification.remove();
    });
    
    // Ritorna l'ID della notifica per eventuali riferimenti futuri
    return notificationId;
}

/**
 * Mostra una notifica push del browser
 * 
 * @param {string} title Titolo della notifica
 * @param {string} body Messaggio della notifica
 * @param {string} icon URL dell'icona (opzionale)
 */
function showBrowserNotification(title, body, icon = 'assets/images/logo.svg') {
    if (!('Notification' in window)) {
        console.log('Questo browser non supporta le notifiche desktop');
        return;
    }
    
    if (Notification.permission === 'granted') {
        const notification = new Notification(title, {
            body: body,
            icon: icon
        });
        
        notification.onclick = function() {
            window.focus();
            notification.close();
        };
    } else if (Notification.permission !== 'denied') {
        Notification.requestPermission().then(function(permission) {
            if (permission === 'granted') {
                showBrowserNotification(title, body, icon);
            }
        });
    }
}

/**
 * Notifica dopo il salvataggio di un contratto
 * 
 * @param {string} contractType Tipo di contratto
 * @param {string} clientName Nome del cliente
 */
function notifyContractSaved(contractType, clientName) {
    const title = 'Contratto salvato';
    const body = `Il contratto ${contractType} per ${clientName} è stato registrato con successo!`;
    
    // Mostra sia una notifica UI che una notifica browser
    showNotification(title, body, 'success');
    showBrowserNotification(title, body);
}

// Esporta funzioni per uso globale
window.CoreSuiteNotifications = {
    showNotification: showNotification,
    showBrowserNotification: showBrowserNotification,
    notifyContractSaved: notifyContractSaved
};
