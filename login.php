<?php
session_start();
require_once __DIR__ . '/src/controllers/AuthController.php';
use CoreSuite\Controllers\AuthController;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$controller = new AuthController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->login();
} else {
    $controller->showLogin();
}
