<?php
/**
 * API per elencare le note di un cliente
 * @endpoint: /api/notes/list.php
 * @method: GET
 * @param: client_id - L'ID del cliente
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

// Ottieni l'ID del cliente
$clientId = isset($_GET['client_id']) ? intval($_GET['client_id']) : 0;

if ($clientId <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'ID cliente non valido']);
    exit;
}

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
        echo json_encode(['success' => false, 'message' => 'Non hai i permessi per visualizzare le note di questo cliente']);
        exit;
    }
    
    // Ottieni le note del cliente
    $query = "
        SELECT n.*, u.first_name || ' ' || u.last_name AS user_name
        FROM client_notes n
        JOIN users u ON n.user_id = u.id
        WHERE n.client_id = ?
        ORDER BY n.created_at DESC
    ";
    
    // Modifica la query per SQLite o MySQL se necessario
    if ($database->getDbType() === 'mysql') {
        $query = "
            SELECT n.*, CONCAT(u.first_name, ' ', u.last_name) AS user_name
            FROM client_notes n
            JOIN users u ON n.user_id = u.id
            WHERE n.client_id = ?
            ORDER BY n.created_at DESC
        ";
    }
    
    $stmt = $db->prepare($query);
    $stmt->execute([$clientId]);
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'notes' => $notes]);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Errore interno: ' . $e->getMessage()]);
}
