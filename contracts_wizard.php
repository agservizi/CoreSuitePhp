<?php
// Entry point per il wizard contratti dinamico
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}
require_once __DIR__ . '/src/middleware/auth.php';
require_any_role(['admin','user']);
include __DIR__ . '/src/views/contracts/wizard.php';
