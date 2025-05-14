<?php
/**
 * API per listare i clienti
 * @endpoint: /api/clients-list.php
 * @method: GET
 * @param: search (opzionale) - Termini di ricerca
 * @param: limit (opzionale) - Limite risultati
 * @param: offset (opzionale) - Offset per paginazione
 */

// Includi configurazioni e classi necessarie
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Database.php';
require_once '../controllers/ClientController.php';

// Verifica autenticazione
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

// Ottieni parametri di ricerca
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? intval($_GET['limit']) : 100;
$offset = isset($_GET['offset']) && is_numeric($_GET['offset']) ? intval($_GET['offset']) : 0;

try {
    // Inizializza database
    $database = new Database();
    $db = $database->getConnection();
    
    // Crea istanza controller
    $clientController = new ClientController($db);
    
    // Filtra clienti
    $filters = [
        'search' => $search,
        'limit' => $limit,
        'offset' => $offset
    ];
    
    $clients = $clientController->getClients($filters);
    
    // Formatta i risultati
    $formattedClients = [];
    foreach ($clients as $client) {
        $formattedClients[] = [
            'id' => $client['id'],
            'full_name' => $client['first_name'] . ' ' . $client['last_name'],
            'email' => $client['email'],
            'phone' => $client['phone'],
            'fiscal_code' => $client['fiscal_code'],
            'status' => $client['status']
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($formattedClients);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([]);
}
