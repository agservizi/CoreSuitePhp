<?php
/**
 * API per contare le notifiche non lette
 * @endpoint: /api/notifications/count.php
 * @method: GET
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

try {
    // Conta le notifiche non lette
    $stmt = $db->prepare("
        SELECT COUNT(*) as count
        FROM notifications
        WHERE user_id = ? AND is_read = 0
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'count' => (int)$result['count']
    ]);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Errore durante il conteggio delle notifiche: ' . $e->getMessage()
    ]);
}
?>
