<?php
require_once __DIR__ . '/../BaseView.php';
// Form creazione utente (AdminLTE)
?><!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Nuovo Utente - CoreSuite</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Nuovo Utente</h2>
    <form method="post" action="/index.php?route=user_create">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Ruolo</label>
            <select name="role_id" class="form-control">
                <option value="1">Admin</option>
                <option value="2">User</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Crea</button>
        <a href="/index.php?route=users" class="btn btn-secondary">Annulla</a>
    </form>
</div>
</body>
</html>
