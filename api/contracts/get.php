<?php
/**
 * API per ottenere i dettagli di un contratto
 * @endpoint: /api/contracts/get.php
 * @method: GET
 * @param: id - L'ID del contratto
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

// Verifica il metodo della richiesta
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Metodo non supportato']);
    exit;
}

// Ottieni l'ID del contratto
$contractId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($contractId <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'ID contratto non valido']);
    exit;
}

try {
    // Inizializza il database e il controller
    $database = new Database();
    $db = $database->getConnection();
    $contractController = new ContractController($db);
    
    // Ottieni i dettagli del contratto
    $contract = $contractController->getContract($contractId);
    
    if (!$contract) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Contratto non trovato']);
        exit;
    }
    
    // Ottieni i dettagli del cliente
    $clientStmt = $db->prepare("
        SELECT id, first_name, last_name, email, phone, address, city, postal_code, province, user_id 
        FROM clients 
        WHERE id = ?
    ");
    $clientStmt->execute([$contract['client_id']]);
    $client = $clientStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$client) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Dati cliente non trovati']);
        exit;
    }
    
    // Controlla permessi (se l'utente è admin o proprietario del cliente)
    if ($_SESSION['user_role'] !== 'admin' && $client['user_id'] !== $_SESSION['user_id']) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Non hai i permessi per visualizzare questo contratto']);
        exit;
    }
    
    // Aggiungi i dati del cliente alla risposta
    $contract['client'] = $client;
    
    // Log dell'operazione
    $logStmt = $db->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details) VALUES (?, ?, ?, ?, ?)");
    $logStmt->execute([
        $_SESSION['user_id'],
        'view',
        'contract',
        $contractId,
        json_encode(['contract_type' => $contract['contract_type']])
    ]);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'data' => $contract]);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Errore interno: ' . $e->getMessage()]);
}
