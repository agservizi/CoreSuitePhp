<?php
/**
 * API per ottenere la chiave pubblica VAPID per le notifiche push
 * @endpoint: /api/notifications/get-public-key.php
 * @method: GET
 */

// Includi configurazioni e classi necessarie
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/ApiController.php';

// Verifica autenticazione
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Utente non autenticato']);
    exit;
}

// Carica la configurazione
$config = require '../../config/config.php';

// Verifica che la chiave pubblica VAPID sia impostata
if (!isset($config['push_public_key']) || empty($config['push_public_key'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Chiave pubblica VAPID non configurata']);
    exit;
}

// Restituisci la chiave pubblica
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'publicKey' => $config['push_public_key']
]);
