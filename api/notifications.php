<?php
// Verifica se l'utente è autenticato
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Utente non autenticato']);
    exit;
}

// In un'applicazione reale, recuperare le notifiche dal database
// Per questo esempio, restituiremo notifiche simulate

$lastCheckTime = isset($_GET['lastCheck']) ? (int)$_GET['lastCheck'] : 0;
$currentTime = time();

// Simula notifiche solo se sono passati più di 10 minuti dall'ultimo controllo
// o se è la prima chiamata (lastCheck = 0)
if ($lastCheckTime === 0 || ($currentTime - $lastCheckTime) > 600) {
    $notifications = [
        [
            'id' => 1,
            'title' => 'Promemoria',
            'message' => 'Hai 3 contratti in scadenza questo mese',
            'type' => 'warning',
            'timestamp' => $currentTime
        ],
        [
            'id' => 2,
            'title' => 'Nuovo cliente',
            'message' => 'Un nuovo cliente è stato registrato nel sistema',
            'type' => 'info',
            'timestamp' => $currentTime
        ]
    ];
} else {
    // Nessuna nuova notifica
    $notifications = [];
}

// Prepara la risposta
$response = [
    'success' => true,
    'notifications' => $notifications,
    'lastCheck' => $currentTime
];

// Invia risposta
header('Content-Type: application/json');
echo json_encode($response);
?>
