<?php
/**
 * API per eliminare un cliente
 * @endpoint: /api/clients/delete.php
 * @method: DELETE
 * @param: id - L'ID del cliente da eliminare
 */

// Includi configurazioni e classi necessarie
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/ApiController.php';
require_once '../../controllers/ClientController.php';

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

// Ottieni l'ID del cliente
$clientId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($clientId <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'ID cliente non valido']);
    exit;
}

try {
    // Inizializza il database e il controller
    $database = new Database();
    $db = $database->getConnection();
    $clientController = new ClientController($db);
    
    // Verifica che il cliente esista e appartenga all'utente corrente
    $client = $clientController->getClient($clientId);
    
    if (!$client) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Cliente non trovato']);
        exit;
    }
    
    // Controlla permessi (se l'utente è admin o proprietario del cliente)
    if ($_SESSION['user_role'] !== 'admin' && $client['user_id'] !== $_SESSION['user_id']) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Non hai i permessi per eliminare questo cliente']);
        exit;
    }
    
    // Elimina il cliente
    $result = $clientController->deleteClient($clientId);
    
    if ($result) {
        // Log dell'operazione
        $logStmt = $db->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details) VALUES (?, ?, ?, ?, ?)");
        $logStmt->execute([
            $_SESSION['user_id'],
            'delete',
            'client',
            $clientId,
            json_encode(['message' => 'Cliente eliminato', 'client_id' => $clientId])
        ]);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Cliente eliminato con successo']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Errore durante l\'eliminazione del cliente']);
    }
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Errore interno: ' . $e->getMessage()]);
}
