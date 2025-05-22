<?php
// Middleware di autenticazione e autorizzazione per ruoli
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}
function require_role($role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        header('Location: /src/views/errors/403.php');
        exit;
    }
}
function require_any_role($roles) {
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles)) {
        header('Location: /src/views/errors/403.php');
        exit;
    }
}
