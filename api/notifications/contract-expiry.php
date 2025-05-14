<?php
/**
 * API per inviare notifiche di rinnovo contratti
 * @endpoint: /api/notifications/contract-expiry.php
 * @method: GET (per eseguire il controllo manuale) o POST (per inviare la notifica)
 */

// Includi configurazioni e classi necessarie
require_once '../../config/config.php';
require_once '../../classes/Database.php';
require_once '../../controllers/NotificationController.php';
require_once '../../controllers/ContractController.php';

// Verifica autenticazione
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Utente non autenticato']);
    exit;
}

// Inizializza il database
$database = Database::getInstance();
$db = $database;

// Inizializza i controller
$notificationController = new NotificationController($db);
$contractController = new ContractController($db);

// Controlla le richieste
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Controllo manuale delle scadenze
    checkExpiringContracts();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Invio di una notifica specifica per un contratto
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || !isset($data['contract_id']) || !isset($data['user_id'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Dati mancanti']);
        exit;
    }
    
    $contract = $contractController->getContract($data['contract_id']);
    
    if (!$contract) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Contratto non trovato']);
        exit;
    }
    
    // Calcola i giorni alla scadenza
    $today = new DateTime();
    $expiryDate = new DateTime($contract['expiration_date']);
    $daysRemaining = $today->diff($expiryDate)->days;
    
    // Crea la notifica
    $title = "Contratto in scadenza";
    $message = "Il contratto {$contract['contract_type']} con {$contract['provider']} scadrà tra $daysRemaining giorni.";
    $created = $notificationController->createNotification(
        $data['user_id'],
        $title,
        $message,
        'warning',
        $contract['id'],
        'contract'
    );
    
    // Invia anche email se richiesto
    $emailSent = false;
    if (isset($data['send_email']) && $data['send_email']) {
        $emailSent = $notificationController->sendEmailNotification(
            $data['user_id'],
            $title,
            $message
        );
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $created,
        'email_sent' => $emailSent,
        'message' => $created ? 'Notifica inviata con successo' : 'Errore nell\'invio della notifica'
    ]);
}

/**
 * Controlla tutti i contratti in scadenza nei prossimi 30 giorni
 * e invia notifiche appropriate
 */
function checkExpiringContracts() {
    global $db, $notificationController;
    
    // Trova i contratti attivi che scadono nei prossimi 30, 15 e 7 giorni
    $expiryIntervals = [30, 15, 7, 1];
    $results = [];
    
    foreach ($expiryIntervals as $days) {
        $stmt = $db->prepare("
            SELECT c.*, 
                   u.id as user_id,
                   CONCAT(cl.first_name, ' ', cl.last_name) as client_name
            FROM contracts c
            JOIN clients cl ON c.client_id = cl.id
            JOIN users u ON cl.user_id = u.id
            WHERE c.status = 'active'
            AND c.expiration_date = DATE_ADD(CURDATE(), INTERVAL ? DAY)
        ");
        
        $stmt->execute([$days]);
        $contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($contracts as $contract) {
            $type = $contract['contract_type'] === 'phone' ? 'Telefonia' : 'Energia';
            $title = "Contratto in scadenza tra $days giorni";
            $message = "Il contratto di $type con {$contract['provider']} per il cliente {$contract['client_name']} scadrà tra $days giorni.";
            
            // Crea notifica nel sistema
            $notificationController->createNotification(
                $contract['user_id'],
                $title,
                $message,
                'warning',
                $contract['id'],
                'contract'
            );
            
            // Invia email per urgenti (1 o 7 giorni)
            if ($days <= 7) {
                $notificationController->sendEmailNotification(
                    $contract['user_id'],
                    $title,
                    $message
                );
            }
            
            $results[] = [
                'contract_id' => $contract['id'],
                'client' => $contract['client_name'],
                'type' => $type,
                'days_remaining' => $days,
                'notified' => true
            ];
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'results' => $results,
        'count' => count($results)
    ]);
}
?>
