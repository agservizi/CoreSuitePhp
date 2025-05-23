<?php
require_once __DIR__ . '/../BaseView.php';
// Form modifica utente (AdminLTE)
?><!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica Utente - CoreSuite</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Modifica Utente</h2>
    <form method="post" action="/index.php?route=user_edit&id=<?= $user['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <div class="form-group">
            <label>Nuova Password (lascia vuoto per non cambiare)</label>
            <input type="password" name="password" class="form-control">
        </div>
        <div class="form-group">
            <label>Ruolo</label>
            <select name="role_id" class="form-control">
                <option value="1" <?= $user['role_id']==1?'selected':'' ?>>Admin</option>
                <option value="2" <?= $user['role_id']==2?'selected':'' ?>>User</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Salva</button>
        <a href="/index.php?route=users" class="btn btn-secondary">Annulla</a>
    </form>
</div>
</body>
</html>
