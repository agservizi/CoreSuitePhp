<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}
require_once __DIR__ . '/src/middleware/auth.php';
require_role('admin');
require_once __DIR__ . '/src/controllers/ProviderController.php';
use CoreSuite\Controllers\ProviderController;

$controller = new ProviderController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->store();
} else {
    $controller->create();
}
