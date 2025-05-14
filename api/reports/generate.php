<?php
/**
 * API per generare report avanzati
 * @endpoint: /api/reports/generate.php
 * @method: POST
 */

// Includi configurazioni e classi necessarie
require_once '../../config/config.php';
require_once '../../classes/Database.php';
require_once '../../controllers/ContractController.php';
require_once '../../controllers/ClientController.php';

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

// Ottieni i dati dalla richiesta
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    $data = $_POST; // Supporto per form multipart/form-data
}

// Valida i dati
if (!isset($data['reportType'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Tipo di report non specificato']);
    exit;
}

try {
    // Inizializza il database
    $database = Database::getInstance();
    $db = $database;
    
    // Imposta i filtri di tempo
    $timeCondition = getTimeCondition($data['timePeriod'] ?? 'all', $data['startDate'] ?? null, $data['endDate'] ?? null);
    
    // Genera il report in base al tipo
    $reportData = [];
    $chartData = [];
    $tableHeaders = [];
    $tableData = [];
    
    switch ($data['reportType']) {
        case 'contracts':
            list($reportData, $chartData, $tableHeaders, $tableData) = generateContractsReport($db, $timeCondition, $data);
            break;
            
        case 'clients':
            list($reportData, $chartData, $tableHeaders, $tableData) = generateClientsReport($db, $timeCondition, $data);
            break;
            
        case 'expiring':
            list($reportData, $chartData, $tableHeaders, $tableData) = generateExpiringContractsReport($db, $data);
            break;
            
        case 'activity':
            list($reportData, $chartData, $tableHeaders, $tableData) = generateActivityReport($db, $timeCondition, $data);
            break;
            
        case 'providers':
            list($reportData, $chartData, $tableHeaders, $tableData) = generateProvidersReport($db, $timeCondition, $data);
            break;
            
        default:
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Tipo di report non valido']);
            exit;
    }
    
    // Log dell'operazione
    $stmt = $db->prepare("
        INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $_SESSION['user_id'],
        'generate_report',
        $data['reportType'],
        0,
        json_encode([
            'filters' => $data,
            'generated_at' => date('Y-m-d H:i:s')
        ])
    ]);
    
    // Ritorna i dati del report
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'reportType' => $data['reportType'],
        'filters' => $data,
        'reportData' => $reportData,
        'chartData' => $chartData,
        'tableHeaders' => $tableHeaders,
        'tableData' => $tableData
    ]);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Errore durante la generazione del report: ' . $e->getMessage()
    ]);
}

/**
 * Genera la condizione SQL per il filtro temporale
 */
function getTimeCondition($period, $startDate = null, $endDate = null) {
    $condition = "";
    
    switch ($period) {
        case 'today':
            $condition = "DATE(created_at) = CURDATE()";
            break;
            
        case 'week':
            $condition = "YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)";
            break;
            
        case 'month':
            $condition = "YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())";
            break;
            
        case 'year':
            $condition = "YEAR(created_at) = YEAR(CURDATE())";
            break;
            
        case 'custom':
            if ($startDate && $endDate) {
                $condition = "DATE(created_at) BETWEEN '$startDate' AND '$endDate'";
            }
            break;
            
        default:
            // Nessun filtro temporale
            $condition = "1=1";
    }
    
    return $condition;
}

/**
 * Genera report sui contratti
 */
function generateContractsReport($db, $timeCondition, $filters) {
    // Costruisci la query base
    $baseQuery = "FROM contracts WHERE $timeCondition";
    $params = [];
    
    // Aggiungi filtri aggiuntivi
    if (!empty($filters['contractType'])) {
        $baseQuery .= " AND contract_type = ?";
        $params[] = $filters['contractType'];
    }
    
    if (!empty($filters['provider'])) {
        $baseQuery .= " AND provider = ?";
        $params[] = $filters['provider'];
    }
    
    if (!empty($filters['status'])) {
        $baseQuery .= " AND status = ?";
        $params[] = $filters['status'];
    }
    
    // Ottieni il conteggio totale
    $countQuery = "SELECT COUNT(*) as total " . $baseQuery;
    $stmt = $db->prepare($countQuery);
    $stmt->execute($params);
    $totalCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Ottieni la distribuzione per tipo
    $typeQuery = "SELECT contract_type, COUNT(*) as count " . $baseQuery . " GROUP BY contract_type";
    $stmt = $db->prepare($typeQuery);
    $stmt->execute($params);
    $typeDistribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Ottieni la distribuzione per provider
    $providerQuery = "SELECT provider, COUNT(*) as count " . $baseQuery . " GROUP BY provider ORDER BY count DESC";
    $stmt = $db->prepare($providerQuery);
    $stmt->execute($params);
    $providerDistribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Ottieni la distribuzione per stato
    $statusQuery = "SELECT status, COUNT(*) as count " . $baseQuery . " GROUP BY status";
    $stmt = $db->prepare($statusQuery);
    $stmt->execute($params);
    $statusDistribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Ottieni la distribuzione per mese
    $monthQuery = "SELECT DATE_FORMAT(created_at, '%m-%Y') as month, COUNT(*) as count " . 
                 $baseQuery . " GROUP BY month ORDER BY MIN(created_at)";
    $stmt = $db->prepare($monthQuery);
    $stmt->execute($params);
    $monthlyDistribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Ottieni i dati della tabella
    $tableQuery = "SELECT c.id, c.contract_type, c.provider, c.status, 
                  c.monthly_fee, c.contract_date, c.expiration_date,
                  CONCAT(cl.first_name, ' ', cl.last_name) as client_name
                  FROM contracts c
                  JOIN clients cl ON c.client_id = cl.id
                  WHERE " . str_replace("FROM contracts WHERE ", "", $baseQuery) . "
                  ORDER BY c.created_at DESC
                  LIMIT 100";
    $stmt = $db->prepare($tableQuery);
    $stmt->execute($params);
    $tableData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatta i dati per la tabella
    $formattedTableData = [];
    foreach ($tableData as $contract) {
        // Traduci i valori per una migliore visualizzazione
        $contractType = $contract['contract_type'] == 'phone' ? 'Telefonia' : 'Energia';
        $status = '';
        switch ($contract['status']) {
            case 'active': $status = 'Attivo'; break;
            case 'pending': $status = 'In attesa'; break;
            case 'cancelled': $status = 'Annullato'; break;
        }
        
        $formattedContract = [
            'id' => $contract['id'],
            'type' => $contractType,
            'provider' => $contract['provider'],
            'client' => $contract['client_name'],
            'status' => $status,
            'monthly_fee' => number_format($contract['monthly_fee'], 2, ',', '.') . ' €',
            'contract_date' => date('d/m/Y', strtotime($contract['contract_date'])),
            'expiration_date' => $contract['expiration_date'] ? date('d/m/Y', strtotime($contract['expiration_date'])) : 'N/A'
        ];
        $formattedTableData[] = $formattedContract;
    }
    
    // Prepara i dati di riepilogo
    $reportData = [
        'totalContracts' => $totalCount,
        'byType' => $typeDistribution,
        'byProvider' => $providerDistribution,
        'byStatus' => $statusDistribution,
        'byMonth' => $monthlyDistribution
    ];
    
    // Prepara i dati per i grafici
    $pieChartData = [
        'labels' => ['Telefonia', 'Energia'],
        'datasets' => [[
            'data' => [
                // Trova il conteggio per ogni tipo di contratto
                array_reduce($typeDistribution, function($carry, $item) {
                    return $item['contract_type'] == 'phone' ? $item['count'] : $carry;
                }, 0),
                array_reduce($typeDistribution, function($carry, $item) {
                    return $item['contract_type'] == 'energy' ? $item['count'] : $carry;
                }, 0)
            ],
            'backgroundColor' => ['#3498db', '#e74c3c']
        ]]
    ];
    
    $barChartData = [
        'labels' => array_map(function($item) { return $item['provider']; }, $providerDistribution),
        'datasets' => [[
            'label' => 'Contratti per Provider',
            'data' => array_map(function($item) { return $item['count']; }, $providerDistribution),
            'backgroundColor' => ['#3498db', '#2ecc71', '#f1c40f', '#e74c3c', '#9b59b6', '#1abc9c', '#d35400', '#34495e']
        ]]
    ];
    
    $lineChartData = [
        'labels' => array_map(function($item) { 
            $month = explode('-', $item['month'])[0];
            $year = explode('-', $item['month'])[1];
            $monthNames = ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic'];
            return $monthNames[intval($month) - 1] . ' ' . $year;
        }, $monthlyDistribution),
        'datasets' => [[
            'label' => 'Contratti per Mese',
            'data' => array_map(function($item) { return $item['count']; }, $monthlyDistribution),
            'borderColor' => '#3498db',
            'backgroundColor' => 'rgba(52, 152, 219, 0.1)',
            'borderWidth' => 2,
            'fill' => true
        ]]
    ];
    
    $chartData = [
        'pieChart' => $pieChartData,
        'barChart' => $barChartData,
        'lineChart' => $lineChartData
    ];
    
    // Definisci le intestazioni della tabella
    $tableHeaders = [
        'ID', 'Tipo', 'Provider', 'Cliente', 'Stato', 'Canone Mensile', 'Data Contratto', 'Data Scadenza'
    ];
    
    return [$reportData, $chartData, $tableHeaders, $formattedTableData];
}

/**
 * Genera report sui clienti
 */
function generateClientsReport($db, $timeCondition, $filters) {
    // Costruisci la query base
    $baseQuery = "FROM clients WHERE $timeCondition";
    $params = [];
    
    // Ottieni il conteggio totale
    $countQuery = "SELECT COUNT(*) as total " . $baseQuery;
    $stmt = $db->prepare($countQuery);
    $stmt->execute($params);
    $totalCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Ottieni il conteggio dei clienti con contratti attivi
    $activeQuery = "SELECT COUNT(DISTINCT c.id) as active 
                   FROM clients c
                   JOIN contracts co ON c.id = co.client_id
                   WHERE co.status = 'active' AND $timeCondition";
    $stmt = $db->prepare($activeQuery);
    $stmt->execute($params);
    $activeClients = $stmt->fetch(PDO::FETCH_ASSOC)['active'];
    
    // Ottieni la distribuzione per città
    $cityQuery = "SELECT city, COUNT(*) as count " . $baseQuery . " AND city IS NOT NULL GROUP BY city ORDER BY count DESC LIMIT 10";
    $stmt = $db->prepare($cityQuery);
    $stmt->execute($params);
    $cityDistribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Ottieni la distribuzione per provincia
    $provinceQuery = "SELECT province, COUNT(*) as count " . $baseQuery . " AND province IS NOT NULL GROUP BY province ORDER BY count DESC";
    $stmt = $db->prepare($provinceQuery);
    $stmt->execute($params);
    $provinceDistribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Ottieni la distribuzione per mese di registrazione
    $monthQuery = "SELECT DATE_FORMAT(created_at, '%m-%Y') as month, COUNT(*) as count " . 
                 $baseQuery . " GROUP BY month ORDER BY MIN(created_at)";
    $stmt = $db->prepare($monthQuery);
    $stmt->execute($params);
    $monthlyDistribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Ottieni i dati della tabella
    $tableQuery = "SELECT c.id, c.first_name, c.last_name, c.email, c.phone, 
                  c.city, c.province, c.created_at,
                  (SELECT COUNT(*) FROM contracts WHERE client_id = c.id) as contracts_count
                  FROM clients c
                  WHERE " . str_replace("FROM clients WHERE ", "", $baseQuery) . "
                  ORDER BY c.created_at DESC
                  LIMIT 100";
    $stmt = $db->prepare($tableQuery);
    $stmt->execute($params);
    $tableData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatta i dati per la tabella
    $formattedTableData = [];
    foreach ($tableData as $client) {
        $formattedClient = [
            'id' => $client['id'],
            'name' => $client['first_name'] . ' ' . $client['last_name'],
            'email' => $client['email'] ?: 'N/A',
            'phone' => $client['phone'] ?: 'N/A',
            'location' => ($client['city'] ? $client['city'] : 'N/A') . ($client['province'] ? ' (' . $client['province'] . ')' : ''),
            'contracts' => $client['contracts_count'],
            'created_at' => date('d/m/Y', strtotime($client['created_at']))
        ];
        $formattedTableData[] = $formattedClient;
    }
    
    // Prepara i dati di riepilogo
    $reportData = [
        'totalClients' => $totalCount,
        'activeClients' => $activeClients,
        'byCity' => $cityDistribution,
        'byProvince' => $provinceDistribution,
        'byMonth' => $monthlyDistribution
    ];
    
    // Prepara i dati per i grafici
    $pieChartData = [
        'labels' => ['Con contratti', 'Senza contratti'],
        'datasets' => [[
            'data' => [$activeClients, $totalCount - $activeClients],
            'backgroundColor' => ['#2ecc71', '#95a5a6']
        ]]
    ];
    
    $barChartData = [
        'labels' => array_map(function($item) { return $item['province']; }, $provinceDistribution),
        'datasets' => [[
            'label' => 'Clienti per Provincia',
            'data' => array_map(function($item) { return $item['count']; }, $provinceDistribution),
            'backgroundColor' => ['#3498db', '#2ecc71', '#f1c40f', '#e74c3c', '#9b59b6', '#1abc9c', '#d35400', '#34495e']
        ]]
    ];
    
    $lineChartData = [
        'labels' => array_map(function($item) { 
            $month = explode('-', $item['month'])[0];
            $year = explode('-', $item['month'])[1];
            $monthNames = ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic'];
            return $monthNames[intval($month) - 1] . ' ' . $year;
        }, $monthlyDistribution),
        'datasets' => [[
            'label' => 'Clienti per Mese',
            'data' => array_map(function($item) { return $item['count']; }, $monthlyDistribution),
            'borderColor' => '#2ecc71',
            'backgroundColor' => 'rgba(46, 204, 113, 0.1)',
            'borderWidth' => 2,
            'fill' => true
        ]]
    ];
    
    $chartData = [
        'pieChart' => $pieChartData,
        'barChart' => $barChartData,
        'lineChart' => $lineChartData
    ];
    
    // Definisci le intestazioni della tabella
    $tableHeaders = [
        'ID', 'Nome', 'Email', 'Telefono', 'Località', 'N. Contratti', 'Registrato il'
    ];
    
    return [$reportData, $chartData, $tableHeaders, $formattedTableData];
}

/**
 * Genera report sui contratti in scadenza
 */
function generateExpiringContractsReport($db, $filters) {
    // Definisci l'intervallo di scadenza
    $daysRange = isset($filters['days']) ? intval($filters['days']) : 30;
    
    // Costruisci la query base
    $baseQuery = "FROM contracts WHERE status = 'active' AND expiration_date IS NOT NULL AND expiration_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL $daysRange DAY)";
    $params = [];
    
    // Aggiungi filtri aggiuntivi
    if (!empty($filters['contractType'])) {
        $baseQuery .= " AND contract_type = ?";
        $params[] = $filters['contractType'];
    }
    
    // Ottieni il conteggio totale
    $countQuery = "SELECT COUNT(*) as total " . $baseQuery;
    $stmt = $db->prepare($countQuery);
    $stmt->execute($params);
    $totalCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Ottieni la distribuzione per giorni alla scadenza
    $daysQuery = "SELECT 
                  CASE 
                    WHEN DATEDIFF(expiration_date, CURDATE()) <= 7 THEN '0-7 giorni'
                    WHEN DATEDIFF(expiration_date, CURDATE()) <= 15 THEN '8-15 giorni'
                    WHEN DATEDIFF(expiration_date, CURDATE()) <= 30 THEN '16-30 giorni'
                    ELSE 'Oltre 30 giorni'
                  END as range_group,
                  COUNT(*) as count
                  " . $baseQuery . "
                  GROUP BY range_group
                  ORDER BY MIN(DATEDIFF(expiration_date, CURDATE()))";
    $stmt = $db->prepare($daysQuery);
    $stmt->execute($params);
    $daysDistribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Ottieni la distribuzione per tipo
    $typeQuery = "SELECT contract_type, COUNT(*) as count " . $baseQuery . " GROUP BY contract_type";
    $stmt = $db->prepare($typeQuery);
    $stmt->execute($params);
    $typeDistribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Ottieni la distribuzione per provider
    $providerQuery = "SELECT provider, COUNT(*) as count " . $baseQuery . " GROUP BY provider ORDER BY count DESC";
    $stmt = $db->prepare($providerQuery);
    $stmt->execute($params);
    $providerDistribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Ottieni i dati della tabella
    $tableQuery = "SELECT c.id, c.contract_type, c.provider, c.monthly_fee, 
                  c.contract_date, c.expiration_date, DATEDIFF(c.expiration_date, CURDATE()) as days_remaining,
                  CONCAT(cl.first_name, ' ', cl.last_name) as client_name,
                  cl.phone, cl.email
                  FROM contracts c
                  JOIN clients cl ON c.client_id = cl.id
                  WHERE " . str_replace("FROM contracts WHERE ", "", $baseQuery) . "
                  ORDER BY c.expiration_date ASC
                  LIMIT 100";
    $stmt = $db->prepare($tableQuery);
    $stmt->execute($params);
    $tableData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatta i dati per la tabella
    $formattedTableData = [];
    foreach ($tableData as $contract) {
        // Traduci i valori per una migliore visualizzazione
        $contractType = $contract['contract_type'] == 'phone' ? 'Telefonia' : 'Energia';
        
        // Determina la classe di priorità in base ai giorni rimanenti
        $days = $contract['days_remaining'];
        $priority = '';
        if ($days <= 7) {
            $priority = 'Urgente';
        } elseif ($days <= 15) {
            $priority = 'Alta';
        } elseif ($days <= 30) {
            $priority = 'Media';
        } else {
            $priority = 'Bassa';
        }
        
        $formattedContract = [
            'id' => $contract['id'],
            'type' => $contractType,
            'provider' => $contract['provider'],
            'client' => $contract['client_name'],
            'contact' => $contract['phone'] ?: $contract['email'] ?: 'N/A',
            'monthly_fee' => number_format($contract['monthly_fee'], 2, ',', '.') . ' €',
            'contract_date' => date('d/m/Y', strtotime($contract['contract_date'])),
            'expiration_date' => date('d/m/Y', strtotime($contract['expiration_date'])),
            'days_remaining' => $days,
            'priority' => $priority
        ];
        $formattedTableData[] = $formattedContract;
    }
    
    // Prepara i dati di riepilogo
    $reportData = [
        'totalExpiring' => $totalCount,
        'byDays' => $daysDistribution,
        'byType' => $typeDistribution,
        'byProvider' => $providerDistribution
    ];
    
    // Prepara i dati per i grafici
    $pieChartData = [
        'labels' => array_map(function($item) { return $item['range_group']; }, $daysDistribution),
        'datasets' => [[
            'data' => array_map(function($item) { return $item['count']; }, $daysDistribution),
            'backgroundColor' => ['#e74c3c', '#f39c12', '#3498db', '#2ecc71']
        ]]
    ];
    
    $barChartData = [
        'labels' => array_map(function($item) { return $item['provider']; }, $providerDistribution),
        'datasets' => [[
            'label' => 'Contratti in Scadenza per Provider',
            'data' => array_map(function($item) { return $item['count']; }, $providerDistribution),
            'backgroundColor' => ['#3498db', '#2ecc71', '#f1c40f', '#e74c3c', '#9b59b6', '#1abc9c', '#d35400', '#34495e']
        ]]
    ];
    
    $chartData = [
        'pieChart' => $pieChartData,
        'barChart' => $barChartData
    ];
    
    // Definisci le intestazioni della tabella
    $tableHeaders = [
        'ID', 'Tipo', 'Provider', 'Cliente', 'Contatto', 'Canone Mensile', 'Data Contratto', 'Data Scadenza', 'Giorni Rimanenti', 'Priorità'
    ];
    
    return [$reportData, $chartData, $tableHeaders, $formattedTableData];
}

/**
 * Genera report sulle attività
 */
function generateActivityReport($db, $timeCondition, $filters) {
    // Costruisci la query base
    $baseQuery = "FROM activity_logs WHERE $timeCondition";
    $params = [];
    
    // Ottieni il conteggio totale
    $countQuery = "SELECT COUNT(*) as total " . $baseQuery;
    $stmt = $db->prepare($countQuery);
    $stmt->execute($params);
    $totalCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Ottieni la distribuzione per tipo di attività
    $actionQuery = "SELECT action, COUNT(*) as count " . $baseQuery . " GROUP BY action ORDER BY count DESC";
    $stmt = $db->prepare($actionQuery);
    $stmt->execute($params);
    $actionDistribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Ottieni la distribuzione per tipo di entità
    $entityQuery = "SELECT entity_type, COUNT(*) as count " . $baseQuery . " GROUP BY entity_type ORDER BY count DESC";
    $stmt = $db->prepare($entityQuery);
    $stmt->execute($params);
    $entityDistribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Ottieni la distribuzione per utente
    $userQuery = "SELECT u.id, CONCAT(u.first_name, ' ', u.last_name) as user_name, COUNT(*) as count 
                 FROM activity_logs al
                 JOIN users u ON al.user_id = u.id
                 WHERE " . str_replace("FROM activity_logs WHERE ", "", $baseQuery) . "
                 GROUP BY u.id, user_name
                 ORDER BY count DESC";
    $stmt = $db->prepare($userQuery);
    $stmt->execute($params);
    $userDistribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Ottieni la distribuzione per ora del giorno
    $hourQuery = "SELECT HOUR(created_at) as hour, COUNT(*) as count 
                 " . $baseQuery . "
                 GROUP BY hour
                 ORDER BY hour ASC";
    $stmt = $db->prepare($hourQuery);
    $stmt->execute($params);
    $hourDistribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Ottieni i dati della tabella
    $tableQuery = "SELECT al.id, al.action, al.entity_type, al.entity_id, al.created_at,
                  CONCAT(u.first_name, ' ', u.last_name) as user_name
                  FROM activity_logs al
                  JOIN users u ON al.user_id = u.id
                  WHERE " . str_replace("FROM activity_logs WHERE ", "", $baseQuery) . "
                  ORDER BY al.created_at DESC
                  LIMIT 100";
    $stmt = $db->prepare($tableQuery);
    $stmt->execute($params);
    $tableData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatta i dati per la tabella
    $formattedTableData = [];
    foreach ($tableData as $log) {
        // Traduci le azioni in italiano
        $action = '';
        switch ($log['action']) {
            case 'create': $action = 'Creazione'; break;
            case 'update': $action = 'Aggiornamento'; break;
            case 'delete': $action = 'Eliminazione'; break;
            case 'login': $action = 'Accesso'; break;
            case 'logout': $action = 'Disconnessione'; break;
            case 'send_email': $action = 'Invio Email'; break;
            case 'generate_report': $action = 'Generazione Report'; break;
            default: $action = ucfirst($log['action']);
        }
        
        // Traduci le entità in italiano
        $entityType = '';
        switch ($log['entity_type']) {
            case 'client': $entityType = 'Cliente'; break;
            case 'contract': $entityType = 'Contratto'; break;
            case 'user': $entityType = 'Utente'; break;
            case 'attachment': $entityType = 'Allegato'; break;
            case 'note': $entityType = 'Nota'; break;
            default: $entityType = ucfirst($log['entity_type']);
        }
        
        $formattedLog = [
            'id' => $log['id'],
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $log['entity_id'],
            'user' => $log['user_name'],
            'created_at' => date('d/m/Y H:i', strtotime($log['created_at']))
        ];
        $formattedTableData[] = $formattedLog;
    }
    
    // Prepara i dati di riepilogo
    $reportData = [
        'totalLogs' => $totalCount,
        'byAction' => $actionDistribution,
        'byEntity' => $entityDistribution,
        'byUser' => $userDistribution,
        'byHour' => $hourDistribution
    ];
    
    // Prepara i dati per i grafici
    $pieChartData = [
        'labels' => array_map(function($item) { 
            // Traduci le azioni in italiano
            $action = '';
            switch ($item['action']) {
                case 'create': $action = 'Creazione'; break;
                case 'update': $action = 'Aggiornamento'; break;
                case 'delete': $action = 'Eliminazione'; break;
                case 'login': $action = 'Accesso'; break;
                case 'logout': $action = 'Disconnessione'; break;
                case 'send_email': $action = 'Invio Email'; break;
                case 'generate_report': $action = 'Generazione Report'; break;
                default: $action = ucfirst($item['action']);
            }
            return $action;
        }, $actionDistribution),
        'datasets' => [[
            'data' => array_map(function($item) { return $item['count']; }, $actionDistribution),
            'backgroundColor' => ['#3498db', '#2ecc71', '#f1c40f', '#e74c3c', '#9b59b6', '#1abc9c', '#d35400', '#34495e']
        ]]
    ];
    
    $barChartData = [
        'labels' => array_map(function($item) { 
            // Traduci le entità in italiano
            $entityType = '';
            switch ($item['entity_type']) {
                case 'client': $entityType = 'Cliente'; break;
                case 'contract': $entityType = 'Contratto'; break;
                case 'user': $entityType = 'Utente'; break;
                case 'attachment': $entityType = 'Allegato'; break;
                case 'note': $entityType = 'Nota'; break;
                default: $entityType = ucfirst($item['entity_type']);
            }
            return $entityType;
        }, $entityDistribution),
        'datasets' => [[
            'label' => 'Attività per Tipo di Entità',
            'data' => array_map(function($item) { return $item['count']; }, $entityDistribution),
            'backgroundColor' => ['#3498db', '#2ecc71', '#f1c40f', '#e74c3c', '#9b59b6', '#1abc9c', '#d35400', '#34495e']
        ]]
    ];
    
    // Riempi l'array delle ore con tutti i valori da 0 a 23
    $hourData = array_fill(0, 24, 0);
    foreach ($hourDistribution as $hour) {
        $hourData[(int)$hour['hour']] = (int)$hour['count'];
    }
    
    $lineChartData = [
        'labels' => array_map(function($hour) { return $hour . ':00'; }, range(0, 23)),
        'datasets' => [[
            'label' => 'Attività per Ora del Giorno',
            'data' => $hourData,
            'borderColor' => '#3498db',
            'backgroundColor' => 'rgba(52, 152, 219, 0.1)',
            'borderWidth' => 2,
            'fill' => true
        ]]
    ];
    
    $chartData = [
        'pieChart' => $pieChartData,
        'barChart' => $barChartData,
        'lineChart' => $lineChartData
    ];
    
    // Definisci le intestazioni della tabella
    $tableHeaders = [
        'ID', 'Azione', 'Tipo Entità', 'ID Entità', 'Utente', 'Data e Ora'
    ];
    
    return [$reportData, $chartData, $tableHeaders, $formattedTableData];
}

/**
 * Genera report sui provider
 */
function generateProvidersReport($db, $timeCondition, $filters) {
    // Costruisci la query base
    $baseQuery = "FROM contracts WHERE $timeCondition";
    $params = [];
    
    // Aggiungi filtri aggiuntivi
    if (!empty($filters['contractType'])) {
        $baseQuery .= " AND contract_type = ?";
        $params[] = $filters['contractType'];
    }
    
    // Ottieni il conteggio totale per provider
    $providerQuery = "SELECT provider, COUNT(*) as total_contracts, 
                     SUM(monthly_fee) as total_revenue,
                     AVG(monthly_fee) as avg_revenue,
                     COUNT(DISTINCT client_id) as unique_clients
                     " . $baseQuery . "
                     GROUP BY provider
                     ORDER BY total_contracts DESC";
    $stmt = $db->prepare($providerQuery);
    $stmt->execute($params);
    $providers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Distribuzione per tipo di contratto per ogni provider
    $typeDistribution = [];
    foreach ($providers as $provider) {
        $typeQuery = "SELECT contract_type, COUNT(*) as count
                     FROM contracts
                     WHERE provider = ? AND " . str_replace("FROM contracts WHERE ", "", $baseQuery) . "
                     GROUP BY contract_type";
        $typeParams = array_merge([$provider['provider']], $params);
        $stmt = $db->prepare($typeQuery);
        $stmt->execute($typeParams);
        $types = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $typeDistribution[$provider['provider']] = $types;
    }
    
    // Ottieni i dati della tabella
    $tableQuery = "SELECT provider, contract_type, COUNT(*) as contracts_count, 
                  SUM(monthly_fee) as revenue, AVG(monthly_fee) as avg_fee,
                  COUNT(DISTINCT client_id) as clients_count
                  " . $baseQuery . "
                  GROUP BY provider, contract_type
                  ORDER BY provider ASC, contract_type ASC";
    $stmt = $db->prepare($tableQuery);
    $stmt->execute($params);
    $tableData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatta i dati per la tabella
    $formattedTableData = [];
    foreach ($tableData as $row) {
        $contractType = $row['contract_type'] == 'phone' ? 'Telefonia' : 'Energia';
        
        $formattedRow = [
            'provider' => $row['provider'],
            'type' => $contractType,
            'contracts' => $row['contracts_count'],
            'clients' => $row['clients_count'],
            'revenue' => number_format($row['revenue'], 2, ',', '.') . ' €',
            'avg_fee' => number_format($row['avg_fee'], 2, ',', '.') . ' €'
        ];
        $formattedTableData[] = $formattedRow;
    }
    
    // Prepara i dati di riepilogo
    $reportData = [
        'providers' => $providers,
        'typeDistribution' => $typeDistribution
    ];
    
    // Prepara i dati per i grafici
    $barChartData = [
        'labels' => array_map(function($item) { return $item['provider']; }, $providers),
        'datasets' => [[
            'label' => 'Numero di Contratti',
            'data' => array_map(function($item) { return $item['total_contracts']; }, $providers),
            'backgroundColor' => '#3498db'
        ]]
    ];
    
    $revenueChartData = [
        'labels' => array_map(function($item) { return $item['provider']; }, $providers),
        'datasets' => [[
            'label' => 'Fatturato Totale (€)',
            'data' => array_map(function($item) { return round($item['total_revenue'], 2); }, $providers),
            'backgroundColor' => '#2ecc71'
        ]]
    ];
    
    $clientsChartData = [
        'labels' => array_map(function($item) { return $item['provider']; }, $providers),
        'datasets' => [[
            'label' => 'Clienti Unici',
            'data' => array_map(function($item) { return $item['unique_clients']; }, $providers),
            'backgroundColor' => '#f1c40f'
        ]]
    ];
    
    $chartData = [
        'barChart' => $barChartData,
        'revenueChart' => $revenueChartData,
        'clientsChart' => $clientsChartData
    ];
    
    // Definisci le intestazioni della tabella
    $tableHeaders = [
        'Provider', 'Tipo', 'N. Contratti', 'N. Clienti', 'Fatturato Totale', 'Canone Medio'
    ];
    
    return [$reportData, $chartData, $tableHeaders, $formattedTableData];
}
?>
