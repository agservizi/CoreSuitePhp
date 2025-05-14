<?php
// API per dettagli cliente
require_once '../classes/Database.php';
$db = Database::getInstance();
$id = intval($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT id, CONCAT(first_name, ' ', last_name) as full_name, fiscal_code, phone FROM clients WHERE id = ?");
$stmt->execute([$id]);
echo json_encode($stmt->fetch() ?: []);
