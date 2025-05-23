<?php
// Redirect automatico alla dashboard se loggato, altrimenti a login
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: /dashboard.php');
    exit;
}
?><!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benvenuto - CoreSuite</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/coresuite-theme.css">
    <link rel="icon" href="/assets/images/coresuite-favicon.svg">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</head>
<body class="hold-transition login-page">
<div class="login-box">    <div class="login-logo">
        <b>CoreSuite</b>
    </div>
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <span class="h4">Benvenuto in CoreSuite</span>
        </div>
        <div class="card-body">
            <p class="login-box-msg">Accedi per iniziare la tua sessione</p>
            <a href="/login.php" class="btn btn-primary btn-block">Accedi</a>
        </div>
    </div>
    <footer class="main-footer text-center mt-3" style="background:transparent;border:none;">
        <small>CoreSuite &copy; <?php echo date('Y'); ?> - Tutti i diritti riservati.</small>
    </footer>
</div>
</body>
</html>
