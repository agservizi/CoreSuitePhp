<?php
// Lista utenti (AdminLTE)
?><!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Utenti - CoreSuite</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Gestione Utenti</h2>
    <a href="/index.php?route=user_create" class="btn btn-success mb-2">Nuovo Utente</a>
    <table class="table table-bordered table-striped">
        <thead><tr><th>Email</th><th>Ruolo</th><th>Azioni</th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['role_name']) ?></td>
                <td>
                    <a href="/index.php?route=user_edit&id=<?= $u['id'] ?>" class="btn btn-sm btn-primary">Modifica</a>
                    <a href="/index.php?route=user_delete&id=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Eliminare utente?')">Elimina</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
