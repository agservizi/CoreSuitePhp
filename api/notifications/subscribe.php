<?php
/**
 * API per la gestione delle notifiche in tempo reale
 * @endpoint: /api/notifications/subscribe.php
 * @method: POST
 * @payload: JSON con token di sottoscrizione push
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
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Metodo non supportato']);
    exit;
}

// Ottieni i dati dalla richiesta
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['subscription']) || empty($data['subscription'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Dati sottoscrizione mancanti']);
    exit;
}

try {
    // Inizializza la connessione al database
    $database = new Database();
    $db = $database->getConnection();
    
    // Prima controlla se esiste già una sottoscrizione per questo utente
    $checkStmt = $db->prepare("SELECT id FROM push_subscriptions WHERE user_id = ?");
    $checkStmt->execute([$_SESSION['user_id']]);
    
    if ($checkStmt->rowCount() > 0) {
        // Aggiorna la sottoscrizione esistente
        $stmt = $db->prepare("
            UPDATE push_subscriptions 
            SET subscription_data = ?, updated_at = NOW() 
            WHERE user_id = ?
        ");
        
        $result = $stmt->execute([
            json_encode($data['subscription']),
            $_SESSION['user_id']
        ]);
    } else {
        // Crea una nuova sottoscrizione
        $stmt = $db->prepare("
            INSERT INTO push_subscriptions (user_id, subscription_data, created_at)
            VALUES (?, ?, NOW())
        ");
        
        $result = $stmt->execute([
            $_SESSION['user_id'],
            json_encode($data['subscription'])
        ]);
    }
    
    if ($result) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Sottoscrizione salvata con successo']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Errore nel salvataggio della sottoscrizione']);
    }
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Errore interno: ' . $e->getMessage()]);
}
