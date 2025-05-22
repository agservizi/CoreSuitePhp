<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}
require_once __DIR__ . '/src/controllers/CustomerController.php';
use CoreSuite\Controllers\CustomerController;

$controller = new CustomerController();
$controller->index();
