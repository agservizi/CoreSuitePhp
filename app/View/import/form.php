<?php
require_once __DIR__ . '/../BaseView.php';

// Form importazione clienti da CSV (AdminLTE)
$error = $error ?? '';
$success = $success ?? '';
?><!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Importa Clienti - CoreSuite</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Importa Clienti da CSV</h2>
    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data" action="/index.php?route=import_customers">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
        <div class="form-group">
            <label>File CSV (intestazione: nome,cognome,cf,documento,telefono,email,piva,ragione_sociale,rappresentante,sdi_pec)</label>
            <input type="file" name="csv" class="form-control" required accept=".csv">
        </div>
        <button type="submit" class="btn btn-success">Importa</button>
        <a href="/index.php?route=customers" class="btn btn-secondary">Annulla</a>
    </form>
</div>
</body>
</html>
