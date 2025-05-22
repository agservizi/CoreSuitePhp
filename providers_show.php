<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}
require_once __DIR__ . '/src/controllers/ProviderController.php';
use CoreSuite\Controllers\ProviderController;

$controller = new ProviderController();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$controller->show($id);
