<?php
/**
 * API per elencare i contratti 
 * @endpoint: /api/contracts/list.php
 * @method: GET
 * @param: client_id - L'ID del cliente (opzionale)
 * @param: page - Numero di pagina (opzionale)
 * @param: items_per_page - Elementi per pagina (opzionale)
 * @param: filter - Filtro per tipo o stato (opzionale)
 * @param: search - Termine di ricerca (opzionale)
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

try {
    // Inizializza il database e il controller
    $database = new Database();
    $db = $database->getConnection();
    $contractController = new ContractController($db);
    
    // Ottieni parametri dalla richiesta
    $clientId = isset($_GET['client_id']) ? intval($_GET['client_id']) : 0;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $itemsPerPage = isset($_GET['items_per_page']) ? intval($_GET['items_per_page']) : 10;
    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    
    // Se c'è un ID cliente, verifica che l'utente abbia i permessi
    if ($clientId > 0) {
        $clientStmt = $db->prepare("SELECT user_id FROM clients WHERE id = ?");
        $clientStmt->execute([$clientId]);
        $client = $clientStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$client) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Cliente non trovato']);
            exit;
        }
        
        // Controlla permessi (se l'utente è admin o proprietario del cliente)
        if ($_SESSION['user_role'] !== 'admin' && $client['user_id'] !== $_SESSION['user_id']) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Non hai i permessi per visualizzare i contratti di questo cliente']);
            exit;
        }
        
        // Ottieni i contratti del cliente
        $result = $contractController->getClientContracts($clientId);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => [
            'contracts' => $result,
            'total' => count($result),
            'page' => 1,
            'items_per_page' => count($result),
            'total_pages' => 1
        ]]);
        exit;
    }
    
    // Costruisci la query di base per tutti i contratti con paginazione e filtri
    $query = "
        SELECT 
            c.id, c.contract_type, c.provider, c.contract_date, c.expiration_date, 
            c.monthly_fee, c.status, c.client_id, c.phone_number, c.migration_code,
            c.activation_address, c.installation_address,
            CONCAT(cl.first_name, ' ', cl.last_name) AS client_name
        FROM contracts c
        JOIN clients cl ON c.client_id = cl.id
        WHERE 1=1
    ";
    
    $countQuery = "SELECT COUNT(*) as total FROM contracts c JOIN clients cl ON c.client_id = cl.id WHERE 1=1";
    $params = [];
    
    // Aggiungi filtri alla query
    if ($filter !== 'all') {
        if ($filter === 'phone' || $filter === 'energy') {
            $query .= " AND c.contract_type = ?";
            $countQuery .= " AND c.contract_type = ?";
            $params[] = $filter;
        } else if ($filter === 'active' || $filter === 'pending' || $filter === 'cancelled') {
            $query .= " AND c.status = ?";
            $countQuery .= " AND c.status = ?";
            $params[] = $filter;
        }
    }
    
    // Aggiungi ricerca
    if (!empty($search)) {
        $query .= " AND (c.id LIKE ? OR cl.first_name LIKE ? OR cl.last_name LIKE ? OR c.provider LIKE ?)";
        $countQuery .= " AND (c.id LIKE ? OR cl.first_name LIKE ? OR cl.last_name LIKE ? OR c.provider LIKE ?)";
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    // Se non è admin, limita ai clienti dell'utente
    if ($_SESSION['user_role'] !== 'admin') {
        $query .= " AND cl.user_id = ?";
        $countQuery .= " AND cl.user_id = ?";
        $params[] = $_SESSION['user_id'];
    }
    
    // Esegui la query di conteggio
    $countStmt = $db->prepare($countQuery);
    $countStmt->execute($params);
    $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Aggiungi paginazione
    $offset = ($page - 1) * $itemsPerPage;
    $query .= " ORDER BY c.id DESC LIMIT ?, ?";
    $paramsFull = $params;
    $paramsFull[] = $offset;
    $paramsFull[] = $itemsPerPage;
    
    // Esegui la query principale
    $stmt = $db->prepare($query);
    $stmt->execute($paramsFull);
    $contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Log dell'operazione
    $logStmt = $db->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details) VALUES (?, ?, ?, ?, ?)");
    $logStmt->execute([
        $_SESSION['user_id'],
        'list',
        'contracts',
        0,
        json_encode([
            'filter' => $filter,
            'search' => $search,
            'page' => $page,
            'client_id' => $clientId
        ])
    ]);
    
    // Prepara la risposta
    $response = [
        'success' => true,
        'data' => [
            'contracts' => $contracts,
            'total' => intval($totalCount),
            'page' => $page,
            'items_per_page' => $itemsPerPage,
            'total_pages' => ceil($totalCount / $itemsPerPage)
        ]
    ];
    
    header('Content-Type: application/json');
    echo json_encode($response);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Errore interno: ' . $e->getMessage()]);
}
