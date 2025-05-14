<?php
/**
 * API per verificare lo stato della sottoscrizione alle notifiche push
 * @endpoint: /api/notifications/check-subscription.php
 * @method: GET
 */

// Includi configurazioni e classi necessarie
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/ApiController.php';

// Verifica autenticazione
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Utente non autenticato']);
    exit;
}

// Verifica il metodo della richiesta
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Metodo non supportato']);
    exit;
}

try {
    // Inizializza la connessione al database
    $database = new Database();
    $db = $database->getConnection();
    
    // Verifica se l'utente è già sottoscritto alle notifiche push
    $stmt = $db->prepare("SELECT id, subscription_data, created_at FROM push_subscriptions WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        // L'utente è già sottoscritto
        $responseData = [
            'success' => true,
            'subscribed' => true,
            'subscription' => [
                'id' => $result['id'],
                'created_at' => $result['created_at'],
                'data' => json_decode($result['subscription_data'], true)
            ]
        ];
    } else {
        // L'utente non è sottoscritto
        $responseData = [
            'success' => true,
            'subscribed' => false
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($responseData);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Errore interno: ' . $e->getMessage()]);
}
