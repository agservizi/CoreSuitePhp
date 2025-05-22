<?php
// Script per aggiornare la password admin con hash bcrypt corretto
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/classes/Database.php';

// Parametri admin
$email = '373798570'; // Cambia se l'admin ha email diversa
$password = 'Giogiu2123@';

try {
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        echo "Utente admin non trovato.\n";
        exit(1);
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->execute([$hash, $email]);
    echo "Password admin aggiornata con successo!\n";
} catch (Exception $e) {
    echo "Errore: " . $e->getMessage() . "\n";
    exit(1);
}
