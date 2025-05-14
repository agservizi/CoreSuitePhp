<?php
/**
 * API per esportare i contratti in formato CSV
 * @endpoint: /api/contracts/export.php
 * @method: GET
 * @param: filter - Filtro per tipo o stato (opzionale)
 * @param: search - Termine di ricerca (opzionale)
 * @param: client_id - ID del cliente per filtro specifico (opzionale)
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
    
    // Ottieni parametri dalla richiesta
    $clientId = isset($_GET['client_id']) ? intval($_GET['client_id']) : 0;
    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    
    // Costruisci la query di base
    $query = "
        SELECT 
            c.id, c.contract_type, c.provider, c.contract_date, c.expiration_date, 
            c.monthly_fee, c.status, c.phone_number, c.migration_code,
            c.activation_address, c.installation_address, c.created_at,
            cl.id as client_id, cl.first_name, cl.last_name, cl.email, cl.phone,
            cl.address, cl.city, cl.postal_code, cl.province
        FROM contracts c
        JOIN clients cl ON c.client_id = cl.id
        WHERE 1=1
    ";
    
    $params = [];
    
    // Aggiungi filtri alla query
    if ($filter !== 'all') {
        if ($filter === 'phone' || $filter === 'energy') {
            $query .= " AND c.contract_type = ?";
            $params[] = $filter;
        } else if ($filter === 'active' || $filter === 'pending' || $filter === 'cancelled') {
            $query .= " AND c.status = ?";
            $params[] = $filter;
        }
    }
    
    // Aggiungi filtro cliente
    if ($clientId > 0) {
        $query .= " AND c.client_id = ?";
        $params[] = $clientId;
    }
    
    // Aggiungi ricerca
    if (!empty($search)) {
        $query .= " AND (c.id LIKE ? OR cl.first_name LIKE ? OR cl.last_name LIKE ? OR c.provider LIKE ?)";
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    // Se non è admin, limita ai clienti dell'utente
    if ($_SESSION['user_role'] !== 'admin') {
        $query .= " AND cl.user_id = ?";
        $params[] = $_SESSION['user_id'];
    }
    
    $query .= " ORDER BY c.id DESC";
    
    // Esegui la query
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Prepara l'intestazione del CSV
    $headers = [
        'ID', 'Tipo Contratto', 'Provider', 'Data Contratto', 'Data Scadenza', 
        'Canone Mensile', 'Stato', 'Cliente ID', 'Nome Cliente', 'Cognome Cliente', 
        'Email Cliente', 'Telefono Cliente', 'Indirizzo Cliente', 'Città Cliente',
        'Numero Telefono', 'Codice Migrazione', 'Indirizzo Attivazione', 'Indirizzo Installazione',
        'Data Creazione'
    ];
    
    // Crea il file CSV in memoria
    $output = fopen('php://temp', 'w');
    
    // Scrivi intestazione UTF-8 BOM per Excel
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Scrivi intestazione
    fputcsv($output, $headers);
    
    // Scrivi righe dati
    foreach ($contracts as $contract) {
        $row = [
            $contract['id'],
            $contract['contract_type'] === 'phone' ? 'Telefonia' : 'Energia',
            $contract['provider'],
            $contract['contract_date'],
            $contract['expiration_date'],
            $contract['monthly_fee'],
            $contract['status'] === 'active' ? 'Attivo' : ($contract['status'] === 'pending' ? 'In attesa' : 'Annullato'),
            $contract['client_id'],
            $contract['first_name'],
            $contract['last_name'],
            $contract['email'],
            $contract['phone'],
            $contract['address'],
            $contract['city'],
            $contract['phone_number'],
            $contract['migration_code'],
            $contract['activation_address'],
            $contract['installation_address'],
            $contract['created_at']
        ];
        fputcsv($output, $row);
    }
    
    // Resetta il puntatore del file
    rewind($output);
    
    // Leggi il contenuto
    $csv = stream_get_contents($output);
    fclose($output);
    
    // Log dell'operazione
    $logStmt = $db->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details) VALUES (?, ?, ?, ?, ?)");
    $logStmt->execute([
        $_SESSION['user_id'],
        'export',
        'contracts',
        0,
        json_encode([
            'filter' => $filter,
            'search' => $search,
            'client_id' => $clientId,
            'count' => count($contracts)
        ])
    ]);
    
    // Imposta gli header per il download
    $filename = "contratti_export_" . date('Ymd_His') . ".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Invia il CSV
    echo $csv;
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Errore interno: ' . $e->getMessage()]);
}
