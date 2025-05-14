<?php
/**
 * API per ottenere i dettagli di un cliente
 * @endpoint: /api/clients/get.php
 * @method: GET
 * @param: id - L'ID del cliente
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
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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
    
    // Ottieni il cliente
    $client = $clientController->getClient($clientId);
    
    if (!$client) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Cliente non trovato']);
        exit;
    }
    
    // Controlla permessi (se l'utente è admin o proprietario del cliente)
    if ($_SESSION['user_role'] !== 'admin' && $client['user_id'] !== $_SESSION['user_id']) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Non hai i permessi per visualizzare questo cliente']);
        exit;
    }
    
    // Ottieni il numero di contratti associati
    $contractStmt = $db->prepare("SELECT COUNT(*) as count FROM contracts WHERE client_id = ?");
    $contractStmt->execute([$clientId]);
    $contractCount = $contractStmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Aggiungi il conteggio dei contratti ai dati del cliente
    $client['contracts_count'] = $contractCount;
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'client' => $client]);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Errore interno: ' . $e->getMessage()]);
}
