<?php
/**
 * Script per l'inizializzazione del database
 * Utilizzato durante il processo di installazione
 */

require_once 'config/database.php';
require_once 'classes/Database.php';

// Verifica che lo script sia chiamato da install.php
$referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$installPath = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . "/install.php";

if (strpos($referrer, "install.php") === false) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Accesso non autorizzato']);
    exit;
}

// Verifica i parametri
if (!isset($_POST['action']) || empty($_POST['action'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Parametri mancanti']);
    exit;
}

$action = $_POST['action'];

// Gestisci le diverse azioni
switch ($action) {
    case 'init_database':
        initDatabase();
        break;
    case 'create_admin':
        createAdmin();
        break;
    case 'init_sample_data':
        initSampleData();
        break;
    default:
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Azione non valida']);
        exit;
}

/**
 * Inizializza il database creando tutte le tabelle necessarie
 */
function initDatabase() {
    try {
        // Carica lo schema
        $schema = require 'config/schema.php';
        
        // Inizializza il database
        $database = new Database();
        $db = $database->getConnection();
        
        // Esegui le query per creare le tabelle
        foreach ($schema as $tableName => $createQuery) {
            $db->exec($createQuery);
        }
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Database inizializzato con successo']);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Errore durante l\'inizializzazione del database: ' . $e->getMessage()]);
    }
}

/**
 * Crea l'utente amministratore iniziale
 */
function createAdmin() {
    try {
        // Verifica i parametri
        if (!isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['first_name']) || !isset($_POST['last_name'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Dati utente mancanti']);
            exit;
        }
        
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $firstName = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
        $lastName = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
        
        if (empty($email) || empty($password) || empty($firstName) || empty($lastName)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Tutti i campi sono obbligatori']);
            exit;
        }
        
        // Inizializza il database
        $database = new Database();
        $db = $database->getConnection();
        
        // Controlla se l'utente esiste già
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Un utente con questa email esiste già']);
            exit;
        }
        
        // Genera l'hash della password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Inserisci l'utente admin
        $stmt = $db->prepare("
            INSERT INTO users (email, password, role, first_name, last_name, created_at)
            VALUES (?, ?, 'admin', ?, ?, NOW())
        ");
        
        $result = $stmt->execute([$email, $hashedPassword, $firstName, $lastName]);
        
        if ($result) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Utente amministratore creato con successo']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Errore durante la creazione dell\'utente amministratore']);
        }
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Errore durante la creazione dell\'utente amministratore: ' . $e->getMessage()]);
    }
}

/**
 * Inizializza dati di esempio (opzionale)
 */
function initSampleData() {
    try {
        // Inizializza il database
        $database = new Database();
        $db = $database->getConnection();
        
        // Carica i dati di esempio dal file di configurazione
        $sampleData = require_once __DIR__ . '/../config/sample_data.php';
        
        // Transazione per assicurarsi che tutto venga inserito correttamente
        $db->beginTransaction();
        
        // 1. Ottieni l'ID dell'utente admin
        $stmt = $db->prepare("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$userData || !isset($userData['id'])) {
            throw new Exception("Utente amministratore non trovato");
        }
        
        $userId = $userData['id'];
        
        // 2. Inserisci clienti di esempio
        $clientIds = [];
        
        foreach ($sampleData['clients'] as $client) {
            $stmt = $db->prepare("
                INSERT INTO clients (
                    user_id, first_name, last_name, email, phone, 
                    fiscal_code, vat_number, address, city, postal_code, 
                    province, created_at
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $userId,
                $client['first_name'],
                $client['last_name'],
                $client['email'],
                $client['phone'],
                $client['fiscal_code'],
                $client['vat_number'],
                $client['address'],
                $client['city'],
                $client['postal_code'],
                $client['province']
            ]);
            
            $clientIds[] = $db->lastInsertId();
        }
        
        // 2. Inserisci clienti di esempio
        $clients = [
            ['Mario', 'Rossi', 'mario.rossi@example.com', '3331234567', 'RSSMRA80A01H501U', null, 'Via Roma 123', 'Roma', '00100', 'RM'],
            ['Giuseppe', 'Verdi', 'giuseppe.verdi@example.com', '3337654321', 'VRDGPP75B02H501U', 'IT12345678901', 'Via Milano 45', 'Milano', '20100', 'MI'],
            ['Anna', 'Bianchi', 'anna.bianchi@example.com', '3339876543', 'BNCNNA85C03H501U', null, 'Via Napoli 67', 'Napoli', '80100', 'NA']
        ];
        
        $clientIds = [];
        
        foreach ($clients as $client) {
            $stmt = $db->prepare("
                INSERT INTO clients (user_id, first_name, last_name, email, phone, fiscal_code, vat_number, address, city, postal_code, province, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $userId,
                $client[0], // first_name
                $client[1], // last_name
                $client[2], // email
                $client[3], // phone
                $client[4], // fiscal_code
                $client[5], // vat_number
                $client[6], // address
                $client[7], // city
                $client[8], // postal_code
                $client[9]  // province
            ]);
            
            $clientIds[] = $db->lastInsertId();
        }
        
        // 3. Inserisci contratti di esempio
        $contracts = [
            // Contratti telefonici
            ['phone', 'Fastweb', 'Via Roma 123', null, 'CM12345', '3331234567', '2023-01-15', '2025-01-15', 29.90, 'active', $clientIds[0]],
            ['phone', 'Windtre', 'Via Milano 45', null, 'CM67890', '3337654321', '2023-03-10', '2025-03-10', 25.99, 'active', $clientIds[1]],
            
            // Contratti energia
            ['energy', 'Enel Energia', 'Via Roma 123', 'Via Roma 123', null, null, '2023-02-20', '2025-02-20', 85.50, 'active', $clientIds[0]],
            ['energy', 'A2A Energia', 'Via Napoli 67', 'Via Napoli 67', null, null, '2023-04-05', '2025-04-05', 75.30, 'pending', $clientIds[2]]
        ];
        
        foreach ($contracts as $contract) {
            $stmt = $db->prepare("
                INSERT INTO contracts (
                    contract_type, provider, activation_address, installation_address, 
                    migration_code, phone_number, contract_date, expiration_date, 
                    monthly_fee, status, client_id, created_by, created_at
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $contract[0],  // contract_type
                $contract[1],  // provider
                $contract[2],  // activation_address
                $contract[3],  // installation_address
                $contract[4],  // migration_code
                $contract[5],  // phone_number
                $contract[6],  // contract_date
                $contract[7],  // expiration_date
                $contract[8],  // monthly_fee
                $contract[9],  // status
                $contract[10], // client_id
                $userId        // created_by
            ]);
        }
        
        // 4. Commit della transazione
        $db->commit();
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Dati di esempio inseriti con successo']);
    } catch (Exception $e) {
        // Rollback in caso di errore
        if (isset($db) && $db->inTransaction()) {
            $db->rollBack();
        }
        
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Errore durante l\'inserimento dei dati di esempio: ' . $e->getMessage()]);
    }
}
