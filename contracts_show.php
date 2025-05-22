<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}
require_once __DIR__ . '/src/controllers/ContractController.php';
use CoreSuite\Controllers\ContractController;

$controller = new ContractController();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$controller->show($id);
