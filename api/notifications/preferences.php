<?php
/**
 * API per gestire le preferenze di notifica
 * @endpoint: /api/notifications/preferences.php
 * @method: GET (per recuperare le preferenze) o POST (per salvarle)
 */

// Includi configurazioni e classi necessarie
require_once '../../config/config.php';
require_once '../../classes/Database.php';

// Verifica autenticazione
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Utente non autenticato']);
    exit;
}

// Inizializza il database
$database = Database::getInstance();
$db = $database;

// Gestisci le richieste
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Recupera le preferenze dell'utente
    $preferences = getUserPreferences($_SESSION['user_id']);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'preferences' => $preferences]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Salva le preferenze
    $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
    $browser_notifications = isset($_POST['browser_notifications']) ? 1 : 0;
    $contract_expiry_days = $_POST['contract_expiry_days'] ?? '30,15,7,1';
    
    // Verifica se l'utente ha già delle preferenze
    $stmt = $db->prepare("SELECT COUNT(*) FROM user_preferences WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $hasPreferences = $stmt->fetchColumn() > 0;
    
    if ($hasPreferences) {
        // Aggiorna le preferenze esistenti
        $stmt = $db->prepare("
            UPDATE user_preferences 
            SET 
                email_notifications = ?,
                browser_notifications = ?,
                contract_expiry_days = ?,
                updated_at = NOW()
            WHERE user_id = ?
        ");
        $result = $stmt->execute([
            $email_notifications,
            $browser_notifications,
            $contract_expiry_days,
            $_SESSION['user_id']
        ]);
    } else {
        // Crea nuove preferenze
        $stmt = $db->prepare("
            INSERT INTO user_preferences 
                (user_id, email_notifications, browser_notifications, contract_expiry_days, created_at, updated_at)
            VALUES 
                (?, ?, ?, ?, NOW(), NOW())
        ");
        $result = $stmt->execute([
            $_SESSION['user_id'],
            $email_notifications,
            $browser_notifications,
            $contract_expiry_days
        ]);
    }
    
    // Rispondi
    header('Content-Type: application/json');
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Preferenze salvate con successo']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Errore nel salvataggio delle preferenze']);
    }
}

/**
 * Recupera le preferenze dell'utente
 */
function getUserPreferences($userId) {
    global $db;
    
    // Verifica se esiste la tabella delle preferenze
    try {
        $stmt = $db->prepare("SHOW TABLES LIKE 'user_preferences'");
        $stmt->execute();
        $tableExists = $stmt->rowCount() > 0;
        
        if (!$tableExists) {
            // Se la tabella non esiste, creala
            $db->exec("
                CREATE TABLE IF NOT EXISTS user_preferences (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    email_notifications TINYINT(1) NOT NULL DEFAULT 1,
                    browser_notifications TINYINT(1) NOT NULL DEFAULT 1,
                    contract_expiry_days VARCHAR(50) NOT NULL DEFAULT '30,15,7,1',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                )
            ");
        }
        
        // Recupera le preferenze
        $stmt = $db->prepare("SELECT * FROM user_preferences WHERE user_id = ?");
        $stmt->execute([$userId]);
        $preferences = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($preferences) {
            return $preferences;
        } else {
            // Valori predefiniti
            return [
                'email_notifications' => 1,
                'browser_notifications' => 1,
                'contract_expiry_days' => '30,15,7,1'
            ];
        }
    } catch (PDOException $e) {
        error_log("Errore nel recupero delle preferenze: " . $e->getMessage());
        
        // Valori predefiniti in caso di errore
        return [
            'email_notifications' => 1,
            'browser_notifications' => 1,
            'contract_expiry_days' => '30,15,7,1'
        ];
    }
}
?>
