<?php
/**
 * API per aggiornare un cliente
 * @endpoint: /api/clients/update.php
 * @method: POST
 * @payload: JSON con i dati del cliente
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

// Ottieni i dati del cliente dalla richiesta
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['id']) || empty($data['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Dati mancanti o non validi']);
    exit;
}

// Validazione dei dati
$requiredFields = ['first_name', 'last_name', 'email'];
foreach ($requiredFields as $field) {
    if (!isset($data[$field]) || empty(trim($data[$field]))) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => "Il campo $field è obbligatorio"]);
        exit;
    }
}

// Valida l'email
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Email non valida']);
    exit;
}

try {
    // Inizializza la connessione al database
    $database = new Database();
    $db = $database->getConnection();
    
    // Verifica che il cliente esista
    $clientId = intval($data['id']);
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
        echo json_encode(['success' => false, 'message' => 'Non hai i permessi per modificare questo cliente']);
        exit;
    }
    
    // Verifica se l'email è già utilizzata da un altro cliente
    $emailStmt = $db->prepare("SELECT id FROM clients WHERE email = ? AND id != ?");
    $emailStmt->execute([$data['email'], $clientId]);
    if ($emailStmt->rowCount() > 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Email già utilizzata da un altro cliente']);
        exit;
    }
    
    // Aggiorna il cliente
    $updateStmt = $db->prepare("
        UPDATE clients 
        SET first_name = ?, 
            last_name = ?, 
            email = ?, 
            phone = ?, 
            address = ?, 
            fiscal_code = ?, 
            vat_number = ?, 
            status = ?, 
            updated_at = NOW() 
        WHERE id = ?
    ");
    
    $result = $updateStmt->execute([
        $data['first_name'],
        $data['last_name'],
        $data['email'],
        $data['phone'] ?? null,
        $data['address'] ?? null,
        $data['fiscal_code'] ?? null,
        $data['vat_number'] ?? null,
        $data['status'] ?? 1,
        $clientId
    ]);
    
    if ($result) {
        // Log dell'operazione
        $logStmt = $db->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details) VALUES (?, ?, ?, ?, ?)");
        $logStmt->execute([
            $_SESSION['user_id'],
            'update',
            'client',
            $clientId,
            json_encode(['message' => 'Cliente aggiornato', 'client_id' => $clientId])
        ]);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Cliente aggiornato con successo']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Errore durante l\'aggiornamento del cliente']);
    }
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Errore interno: ' . $e->getMessage()]);
}
