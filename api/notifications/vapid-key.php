<?php
/**
 * API per fornire la chiave VAPID pubblica
 * @endpoint: /api/notifications/vapid-key.php
 * @method: GET
 */

// Includi configurazioni necessarie
require_once '../../config/config.php';

// Verifica autenticazione
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Utente non autenticato']);
    exit;
}

// Fornisci la chiave pubblica VAPID
header('Content-Type: application/json');
if (isset(CONFIG['push_public_key'])) {
    echo json_encode([
        'success' => true,
        'key' => CONFIG['push_public_key']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Chiave VAPID non configurata'
    ]);
}
?>
