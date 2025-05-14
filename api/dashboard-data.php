<?php
// API per recuperare i dati della dashboard
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Utente non autenticato']);
    exit;
}

// Connessione al database
require_once '../config/config.php';
require_once '../classes/Database.php';

try {
    $db = Database::getInstance();
    
    // Recupera il conteggio totale dei contratti
    $totalContractsQuery = $db->query("SELECT COUNT(*) as total FROM contracts");
    $totalContracts = $totalContractsQuery->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Recupera il conteggio dei contratti attivi
    $activeContractsQuery = $db->query("SELECT COUNT(*) as active FROM contracts WHERE status = 'active'");
    $activeContracts = $activeContractsQuery->fetch(PDO::FETCH_ASSOC)['active'] ?? 0;
    
    // Recupera il conteggio dei contratti in attesa
    $pendingContractsQuery = $db->query("SELECT COUNT(*) as pending FROM contracts WHERE status = 'pending'");
    $pendingContracts = $pendingContractsQuery->fetch(PDO::FETCH_ASSOC)['pending'] ?? 0;
    
    // Recupera il conteggio totale dei clienti
    $totalClientsQuery = $db->query("SELECT COUNT(*) as total FROM clients");
    $totalClients = $totalClientsQuery->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Recupera il conteggio dei contratti per tipo
    $phoneContractsQuery = $db->query("SELECT COUNT(*) as phone FROM contracts WHERE contract_type = 'phone'");
    $phoneContracts = $phoneContractsQuery->fetch(PDO::FETCH_ASSOC)['phone'] ?? 0;
    
    $energyContractsQuery = $db->query("SELECT COUNT(*) as energy FROM contracts WHERE contract_type = 'energy'");
    $energyContracts = $energyContractsQuery->fetch(PDO::FETCH_ASSOC)['energy'] ?? 0;
    
    // Recupera statistiche provider
    $providerStatsQuery = $db->query("
        SELECT provider, COUNT(*) as count 
        FROM contracts 
        GROUP BY provider 
        ORDER BY count DESC 
        LIMIT 5
    ");
    $providerStats = [];
    $providerLabels = [];
    
    while ($row = $providerStatsQuery->fetch(PDO::FETCH_ASSOC)) {
        $providerLabels[] = $row['provider'];
        $providerStats[] = (int)$row['count'];
    }
    
    // Recupera gli ultimi contratti
    $latestContractsQuery = $db->query("
        SELECT c.id, CONCAT(cl.first_name, ' ', cl.last_name) as client_name, 
               c.contract_type as type, c.provider, c.status, 
               DATE_FORMAT(c.created_at, '%d-%m-%Y') as date
        FROM contracts c
        JOIN clients cl ON c.client_id = cl.id
        ORDER BY c.created_at DESC
        LIMIT 10
    ");
    
    $latestContracts = [];
    while ($contract = $latestContractsQuery->fetch(PDO::FETCH_ASSOC)) {
        // Formatta il tipo di contratto per visualizzazione
        $contract['type'] = ucfirst($contract['type']);
        $latestContracts[] = $contract;
    }
    
    // Contratti in scadenza nei prossimi 30 giorni
    $expiringContractsQuery = $db->query("
        SELECT COUNT(*) as count
        FROM contracts
        WHERE expiration_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
        AND status = 'active'
    ");
    $expiringContracts = $expiringContractsQuery->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    
    // Contratti per mese (ultimi 6 mesi)
    $contractsByMonthQuery = $db->query("
        SELECT 
            DATE_FORMAT(created_at, '%m-%Y') as month,
            DATE_FORMAT(created_at, '%b %Y') as month_label,
            COUNT(*) as count
        FROM contracts
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY month
        ORDER BY created_at ASC
    ");
    
    $monthLabels = [];
    $monthData = [];
    
    while ($row = $contractsByMonthQuery->fetch(PDO::FETCH_ASSOC)) {
        $monthLabels[] = $row['month_label'];
        $monthData[] = (int)$row['count'];
    }
    
    // Prepara la risposta JSON
    $response = [
        'totalContracts' => (int)$totalContracts,
        'activeContracts' => (int)$activeContracts,
        'pendingContracts' => (int)$pendingContracts,
        'totalClients' => (int)$totalClients,
        'phoneContracts' => (int)$phoneContracts,
        'energyContracts' => (int)$energyContracts,
        'expiringContracts' => (int)$expiringContracts,
        'providerLabels' => $providerLabels,
        'providerStats' => $providerStats,
        'monthLabels' => $monthLabels,
        'monthData' => $monthData,
        'latestContracts' => $latestContracts
    ];
    
    header('Content-Type: application/json');
    echo json_encode($response);
    
} catch (PDOException $e) {
    // Gestione degli errori
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Errore nel recupero dei dati: ' . $e->getMessage(),
        'totalContracts' => 0,
        'activeContracts' => 0,
        'pendingContracts' => 0,
        'totalClients' => 0,
        'phoneContracts' => 0,
        'energyContracts' => 0,
        'expiringContracts' => 0,
        'providerLabels' => [],
        'providerStats' => [],
        'monthLabels' => [],
        'monthData' => [],
        'latestContracts' => []
    ]);
}
?>
?>
