<?php
/**
 * API per elencare gli allegati di un cliente o contratto
 * @endpoint: /api/attachments/list.php
 * @method: GET
 * @param: client_id - L'ID del cliente (opzionale)
 * @param: contract_id - L'ID del contratto (opzionale)
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

// Ottieni i parametri dalla richiesta
$clientId = isset($_GET['client_id']) ? intval($_GET['client_id']) : 0;
$contractId = isset($_GET['contract_id']) ? intval($_GET['contract_id']) : 0;

// Almeno uno dei due parametri deve essere specificato
if ($clientId <= 0 && $contractId <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'È necessario specificare client_id o contract_id']);
    exit;
}

try {
    // Inizializza la connessione al database
    $database = new Database();
    $db = $database->getConnection();
    
    // Se specificato client_id, verifica che il cliente esista
    if ($clientId > 0) {
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
            echo json_encode(['success' => false, 'message' => 'Non hai i permessi per visualizzare gli allegati di questo cliente']);
            exit;
        }
    }
    
    // Se specificato contract_id, verifica che il contratto esista
    if ($contractId > 0) {
        $checkStmt = $db->prepare("
            SELECT c.id, cl.user_id 
            FROM contracts c
            JOIN clients cl ON c.client_id = cl.id
            WHERE c.id = ?
        ");
        $checkStmt->execute([$contractId]);
        $contract = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$contract) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Contratto non trovato']);
            exit;
        }
        
        // Controlla permessi (se l'utente è admin o proprietario del cliente associato al contratto)
        if ($_SESSION['user_role'] !== 'admin' && $contract['user_id'] !== $_SESSION['user_id']) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Non hai i permessi per visualizzare gli allegati di questo contratto']);
            exit;
        }
    }
    
    // Costruisci la query in base ai parametri
    $query = "
        SELECT a.*, u.first_name, u.last_name
        FROM attachments a
        LEFT JOIN users u ON a.user_id = u.id
        WHERE 1=1
    ";
    $params = [];
    
    if ($clientId > 0) {
        $query .= " AND a.client_id = ?";
        $params[] = $clientId;
    }
    
    if ($contractId > 0) {
        $query .= " AND a.contract_id = ?";
        $params[] = $contractId;
    }
    
    $query .= " ORDER BY a.created_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $attachments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Log dell'operazione
    $logStmt = $db->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details) VALUES (?, ?, ?, ?, ?)");
    $logStmt->execute([
        $_SESSION['user_id'],
        'view',
        $contractId > 0 ? 'contract_attachments' : 'client_attachments',
        $contractId > 0 ? $contractId : $clientId,
        json_encode(['count' => count($attachments)])
    ]);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'data' => $attachments]);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Errore interno: ' . $e->getMessage()]);
}
