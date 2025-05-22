<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}
require_once __DIR__ . '/src/middleware/auth.php';
require_role('admin');
require_once __DIR__ . '/src/controllers/CustomerController.php';
use CoreSuite\Controllers\CustomerController;

$controller = new CustomerController();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->update($id);
} else {
    $controller->edit($id);
}
