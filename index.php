<?php
// Redirect automatico alla dashboard se loggato, altrimenti a login
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: /dashboard.php');
    exit;
} else {
    header('Location: /login.php');
    exit;
}
