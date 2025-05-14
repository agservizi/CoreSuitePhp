<?php
/**
 * API per eliminare un allegato
 * @endpoint: /api/attachments/delete.php
 * @method: POST, DELETE
 * @param: id - L'ID dell'allegato da eliminare (per il metodo DELETE)
 * @payload: JSON con id dell'allegato (per il metodo POST)
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

// Ottieni l'ID dell'allegato in base al metodo
$attachmentId = 0;

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $attachmentId = isset($_GET['id']) ? intval($_GET['id']) : 0;
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $attachmentId = isset($data['id']) ? intval($data['id']) : 0;
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Metodo non supportato']);
    exit;
}

if ($attachmentId <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'ID allegato non valido o mancante']);
    exit;
}

try {
    // Inizializza la connessione al database
    $database = new Database();
    $db = $database->getConnection();
    
    // Verifica che l'allegato esista e ottieni le informazioni necessarie
    $checkStmt = $db->prepare("
        SELECT a.*, c.user_id as client_owner_id
        FROM attachments a
        LEFT JOIN clients c ON a.client_id = c.id
        LEFT JOIN contracts co ON a.contract_id = co.id
        LEFT JOIN clients c2 ON co.client_id = c2.id
        WHERE a.id = ?
    ");
    $checkStmt->execute([$attachmentId]);
    $attachment = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$attachment) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Allegato non trovato']);
        exit;
    }
    
    // Controlla permessi (se l'utente è admin o proprietario del cliente/contratto)
    $clientOwnerId = $attachment['client_owner_id'] ?? null;
    
    if ($_SESSION['user_role'] !== 'admin' && $clientOwnerId !== $_SESSION['user_id']) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Non hai i permessi per eliminare questo allegato']);
        exit;
    }
    
    // Ottieni il percorso del file da eliminare
    $filepath = $attachment['filepath'];
    $fullPath = __DIR__ . '/../../' . $filepath;
    
    // Inizia una transazione
    $db->beginTransaction();
    
    // Elimina il record del database
    $deleteStmt = $db->prepare("DELETE FROM attachments WHERE id = ?");
    $result = $deleteStmt->execute([$attachmentId]);
    
    if ($result) {
        // Tenta di eliminare il file fisico se esiste
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
        
        // Log dell'operazione
        $logStmt = $db->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details) VALUES (?, ?, ?, ?, ?)");
        $logStmt->execute([
            $_SESSION['user_id'],
            'delete',
            'attachment',
            $attachmentId,
            json_encode([
                'filename' => $attachment['filename'],
                'client_id' => $attachment['client_id'],
                'contract_id' => $attachment['contract_id']
            ])
        ]);
        
        // Commit della transazione
        $db->commit();
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Allegato eliminato con successo']);
    } else {
        // Rollback in caso di errore
        $db->rollBack();
        
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Errore nell\'eliminazione dell\'allegato']);
    }
    
} catch (Exception $e) {
    // Rollback in caso di eccezione
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Errore interno: ' . $e->getMessage()]);
}
