<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}
require_once __DIR__ . '/src/middleware/auth.php';
require_any_role(['admin','user']);
require_once __DIR__ . '/src/controllers/ContractController.php';
use CoreSuite\Controllers\ContractController;

$controller = new ContractController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->store();
} else {
    $controller->create();
}
