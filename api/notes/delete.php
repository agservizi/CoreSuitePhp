<?php
/**
 * API per eliminare una nota cliente
 * @endpoint: /api/notes/delete.php
 * @method: DELETE
 * @param: id - L'ID della nota da eliminare
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
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Metodo non supportato']);
    exit;
}

// Ottieni l'ID della nota
$noteId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($noteId <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'ID nota non valido']);
    exit;
}

try {
    // Inizializza la connessione al database
    $database = new Database();
    $db = $database->getConnection();
    
    // Verifica che la nota esista e che l'utente corrente possa eliminarla
    $checkStmt = $db->prepare("
        SELECT n.id, n.client_id, n.user_id, c.user_id as client_owner_id
        FROM client_notes n
        JOIN clients c ON n.client_id = c.id
        WHERE n.id = ?
    ");
    $checkStmt->execute([$noteId]);
    $note = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$note) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Nota non trovata']);
        exit;
    }
    
    // Controlla permessi
    // L'utente può eliminare la nota se:
    // - È un admin
    // - È l'autore della nota
    // - È il proprietario del cliente a cui la nota è associata
    $canDelete = (
        $_SESSION['user_role'] === 'admin' || 
        $note['user_id'] === $_SESSION['user_id'] || 
        $note['client_owner_id'] === $_SESSION['user_id']
    );
    
    if (!$canDelete) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Non hai i permessi per eliminare questa nota']);
        exit;
    }
    
    // Elimina la nota
    $deleteStmt = $db->prepare("DELETE FROM client_notes WHERE id = ?");
    $result = $deleteStmt->execute([$noteId]);
    
    if ($result) {
        // Log dell'operazione
        $logStmt = $db->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details) VALUES (?, ?, ?, ?, ?)");
        $logStmt->execute([
            $_SESSION['user_id'],
            'delete',
            'client_note',
            $noteId,
            json_encode(['message' => 'Nota cliente eliminata', 'note_id' => $noteId, 'client_id' => $note['client_id']])
        ]);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Nota eliminata con successo']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Errore durante l\'eliminazione della nota']);
    }
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Errore interno: ' . $e->getMessage()]);
}
