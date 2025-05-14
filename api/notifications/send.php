<?php
/**
 * API per inviare notifiche push
 * @endpoint: /api/notifications/send.php
 * @method: POST
 * @payload: JSON con destinatario e messaggio
 */

// Includi configurazioni e classi necessarie
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/ApiController.php';

// Libreria per Web Push (installare con composer: composer require minishlink/web-push)
require_once '../../vendor/autoload.php';
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

// Verifica autenticazione
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Utente non autenticato']);
    exit;
}

// Verifica il metodo della richiesta
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Metodo non supportato']);
    exit;
}

// Ottieni i dati dalla richiesta
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['userId']) || !isset($data['title']) || !isset($data['body'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Dati non validi']);
    exit;
}

try {
    // Inizializza la connessione al database
    $database = new Database();
    
    // Inizializza la connessione al database
    $database = new Database();
    $db = $database->getConnection();
    
    // Recupera la sottoscrizione dell'utente
    $stmt = $db->prepare("SELECT subscription_data FROM push_subscriptions WHERE user_id = ?");
    $stmt->execute([$data['userId']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Nessuna sottoscrizione trovata per questo utente']);
        exit;
    }
    
    $subscriptionData = json_decode($result['subscription_data'], true);
    
    // Configurazione VAPID (da generare e memorizzare in un file di configurazione)
    $auth = [
        'VAPID' => [
            'subject' => 'mailto:admin@coresuite.it',
            'publicKey' => CONFIG['push_public_key'], // Da configurare in config.php
            'privateKey' => CONFIG['push_private_key'], // Da configurare in config.php
        ],
    ];
    
    // Prepara la notifica
    $notification = [
        'title' => $data['title'],
        'body' => $data['body'],
        'icon' => $data['icon'] ?? '/assets/images/logo.svg',
        'url' => $data['url'] ?? '/'
    ];
    
    // Crea sottoscrizione
    $subscription = Subscription::create($subscriptionData);
    
    // Inizializza WebPush
    $webPush = new WebPush($auth);
    
    // Invia la notifica
    $report = $webPush->sendOneNotification(
        $subscription,
        json_encode($notification)
    );
    
    // Gestisci il risultato
    if ($report->isSuccess()) {
        // Log dell'operazione
        $logStmt = $db->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details) VALUES (?, ?, ?, ?, ?)");
        $logStmt->execute([
            $_SESSION['user_id'],
            'send',
            'notification',
            $data['userId'],
            json_encode([
                'message' => 'Notifica inviata',
                'recipient_id' => $data['userId'],
                'title' => $data['title']
            ])
        ]);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Notifica inviata con successo']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Errore nell\'invio della notifica: ' . $report->getReason()]);
    }
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Errore interno: ' . $e->getMessage()]);
}
?>
