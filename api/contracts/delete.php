<?php
/**
 * API per eliminare un contratto
 * @endpoint: /api/contracts/delete.php
 * @method: POST, DELETE
 * @payload: JSON con id del contratto (per POST)
 * @param: id - L'ID del contratto da eliminare (per DELETE)
 */

// Includi configurazioni e classi necessarie
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/ApiController.php';
require_once '../../controllers/ContractController.php';

// Verifica autenticazione
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Utente non autenticato']);
    exit;
}

// Ottieni l'ID del contratto in base al metodo
$contractId = 0;

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $contractId = isset($_GET['id']) ? intval($_GET['id']) : 0;
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $contractId = isset($data['id']) ? intval($data['id']) : 0;
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Metodo non supportato']);
    exit;
}

if ($contractId <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'ID contratto non valido o mancante']);
    exit;
}

try {
    // Inizializza il database e il controller
    $database = new Database();
    $db = $database->getConnection();
    $contractController = new ContractController($db);
    
    // Ottieni i dettagli del contratto per verificare i permessi
    $contractStmt = $db->prepare("
        SELECT c.id, c.client_id, c.contract_type, cl.user_id 
        FROM contracts c
        JOIN clients cl ON c.client_id = cl.id
        WHERE c.id = ?
    ");
    $contractStmt->execute([$contractId]);
    $contract = $contractStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$contract) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Contratto non trovato']);
        exit;
    }
    
    // Controlla permessi (se l'utente è admin o proprietario del cliente)
    if ($_SESSION['user_role'] !== 'admin' && $contract['user_id'] !== $_SESSION['user_id']) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Non hai i permessi per eliminare questo contratto']);
        exit;
    }
    
    // Salva i dati per il log prima dell'eliminazione
    $contractDetails = [
        'contract_id' => $contract['id'],
        'client_id' => $contract['client_id'],
        'contract_type' => $contract['contract_type']
    ];
    
    // Inizia una transazione
    $db->beginTransaction();
    
    // Elimina eventuali allegati collegati
    $attachmentsStmt = $db->prepare("DELETE FROM attachments WHERE contract_id = ?");
    $attachmentsStmt->execute([$contractId]);
    
    // Elimina il contratto
    $result = $contractController->deleteContract($contractId);
    
    if ($result) {
        // Log dell'operazione
        $logStmt = $db->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details) VALUES (?, ?, ?, ?, ?)");
        $logStmt->execute([
            $_SESSION['user_id'],
            'delete',
            'contract',
            $contractId,
            json_encode($contractDetails)
        ]);
        
        // Invia notifica
        try {
            $notifyMessage = "Contratto #{$contractId} eliminato con successo";
            showNotification('Contratto eliminato', $notifyMessage, 'success');
        } catch (Exception $notifyEx) {
            // Ignora errori di notifica
        }
        
        // Commit della transazione
        $db->commit();
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Contratto eliminato con successo']);
    } else {
        // Rollback in caso di errore
        $db->rollBack();
        
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Errore nell\'eliminazione del contratto']);
    }
    
} catch (Exception $e) {
    // Rollback in caso di eccezione
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Errore interno: ' . $e->getMessage()]);
}
