<?php
/**
 * API per aggiungere una nota a un cliente
 * @endpoint: /api/notes/add.php
 * @method: POST
 * @payload: JSON con client_id e content
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

if (!$data || !isset($data['client_id']) || !isset($data['content']) || empty(trim($data['content']))) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Dati mancanti o non validi']);
    exit;
}

$clientId = intval($data['client_id']);
$content = trim($data['content']);

try {
    // Inizializza la connessione al database
    $database = new Database();
    $db = $database->getConnection();
    
    // Verifica che il cliente esista
    $checkStmt = $db->prepare("SELECT id, user_id FROM clients WHERE id = ?");
    $checkStmt->execute([$clientId]);
    $client = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$client) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Cliente non trovato']);
        exit;
    }
    
    // Controlla permessi (se l'utente è admin o proprietario del cliente)
    if ($_SESSION['user_role'] !== 'admin' && $client['user_id'] !== $_SESSION['user_id']) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Non hai i permessi per aggiungere note a questo cliente']);
        exit;
    }
    
    // Aggiungi la nota
    $stmt = $db->prepare("
        INSERT INTO client_notes (client_id, user_id, content, created_at)
        VALUES (?, ?, ?, NOW())
    ");
    
    $result = $stmt->execute([
        $clientId,
        $_SESSION['user_id'],
        $content
    ]);
    
    if ($result) {
        $noteId = $db->lastInsertId();
        
        // Log dell'operazione
        $logStmt = $db->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details) VALUES (?, ?, ?, ?, ?)");
        $logStmt->execute([
            $_SESSION['user_id'],
            'create',
            'client_note',
            $noteId,
            json_encode(['message' => 'Nota cliente creata', 'client_id' => $clientId, 'note_id' => $noteId])
        ]);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Nota aggiunta con successo', 'note_id' => $noteId]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Errore durante l\'aggiunta della nota']);
    }
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Errore interno: ' . $e->getMessage()]);
}
