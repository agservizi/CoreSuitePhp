<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}
require_once __DIR__ . '/src/controllers/ProviderController.php';
use CoreSuite\Controllers\ProviderController;

$controller = new ProviderController();
$controller->index();
