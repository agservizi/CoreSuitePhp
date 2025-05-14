<?php
/**
 * Test della connessione al database
 * Utilizzato durante il processo di installazione
 */

// Verifica che lo script sia chiamato da install.php
$referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$installPath = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . "/install.php";

if (strpos($referrer, "install.php") === false) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Accesso non autorizzato']);
    exit;
}

// Ottieni i parametri di connessione
$dbHost = isset($_POST['db_host']) ? $_POST['db_host'] : '';
$dbName = isset($_POST['db_name']) ? $_POST['db_name'] : '';
$dbUser = isset($_POST['db_user']) ? $_POST['db_user'] : '';
$dbPass = isset($_POST['db_password']) ? $_POST['db_password'] : '';

// Verifica parametri obbligatori
if (empty($dbHost) || empty($dbName) || empty($dbUser)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Parametri mancanti']);
    exit;
}

try {
    // Tenta la connessione
    $dsn = "mysql:host=$dbHost;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ];
    
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    
    // Verifica se il database esiste
    $stmt = $pdo->query("SHOW DATABASES LIKE '$dbName'");
    $dbExists = $stmt->rowCount() > 0;
    
    if (!$dbExists) {
        // Tenta di creare il database
        try {
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $dbExists = true;
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Errore nella creazione del database: ' . $e->getMessage()]);
            exit;
        }
    }
    
    // Salva i parametri di connessione in un file di configurazione
    $configContent = "<?php\nreturn [\n";
    $configContent .= "    'db_host' => '" . addslashes($dbHost) . "',\n";
    $configContent .= "    'db_name' => '" . addslashes($dbName) . "',\n";
    $configContent .= "    'db_user' => '" . addslashes($dbUser) . "',\n";
    $configContent .= "    'db_password' => '" . addslashes($dbPass) . "'\n";
    $configContent .= "];\n?>";
    
    // Crea la directory config se non esiste
    if (!file_exists('../config')) {
        mkdir('../config', 0755, true);
    }
    
    // Salva il file di configurazione
    if (!file_put_contents('../config/database.php', $configContent)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Impossibile salvare il file di configurazione']);
        exit;
    }
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Connessione al database riuscita']);
    
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Errore di connessione: ' . $e->getMessage()]);
}
