<?php
session_start();
require_once __DIR__ . '/src/controllers/AuthController.php';
use CoreSuite\Controllers\AuthController;

$controller = new AuthController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->login();
} else {
    $controller->showLogin();
}
