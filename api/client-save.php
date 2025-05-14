<?php
/**
 * API per salvare un nuovo cliente
 * @endpoint: /api/client-save.php
 * @method: POST
 */

// Includi configurazioni e classi necessarie
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Database.php';
require_once '../classes/ApiController.php';
require_once '../controllers/ClientController.php';

// Verifica autenticazione
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Utente non autenticato']);
    exit;
}

header('Content-Type: application/json');

try {
    // Validazione campi obbligatori
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    
    if (!$first_name || !$last_name) {
        echo json_encode(['success' => false, 'error' => 'Nome e cognome obbligatori']);
        exit;
    }
    
    // Prepara dati cliente
    $clientData = [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $_POST['email'] ?? null,
        'phone' => $_POST['phone'] ?? null,
        'fiscal_code' => $_POST['fiscal_code'] ?? null,
        'vat_number' => $_POST['vat_number'] ?? null,
        'address' => $_POST['address'] ?? null,
        'city' => $_POST['city'] ?? null,
        'postal_code' => $_POST['postal_code'] ?? null,
        'province' => $_POST['province'] ?? null,
        'status' => 1
    ];
    
    // Inizializza database e controller
    $database = new Database();
    $db = $database->getConnection();
    $clientController = new ClientController($db);
    
    // Crea cliente
    $clientId = $clientController->create($clientData);
    
    if ($clientId) {
        // Log dell'operazione
        $logStmt = $db->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details) VALUES (?, ?, ?, ?, ?)");
        $logStmt->execute([
            $_SESSION['user_id'],
            'create',
            'client',
            $clientId,
            json_encode(['message' => 'Cliente creato', 'client_id' => $clientId])
        ]);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Cliente creato con successo',
            'client_id' => $clientId
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Errore nella creazione del cliente']);
    }
} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
