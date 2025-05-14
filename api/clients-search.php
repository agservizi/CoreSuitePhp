<?php
// API per ricerca clienti (autocomplete)
require_once '../classes/Database.php';
$db = Database::getInstance();
$q = $_GET['q'] ?? '';
if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}
$stmt = $db->prepare("SELECT id, CONCAT(first_name, ' ', last_name) as full_name FROM clients WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ? LIMIT 10");
$stmt->execute(["%$q%", "%$q%", "%$q%"]);
echo json_encode($stmt->fetchAll());
