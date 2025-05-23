<?php
// Lista notifiche utente (AdminLTE)
?><!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Notifiche - CoreSuite</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Notifiche</h2>
    <table class="table table-bordered table-striped">
        <thead><tr><th>Data</th><th>Messaggio</th><th>Tipo</th><th>Stato</th><th>Azioni</th></tr></thead>
        <tbody>
        <?php foreach ($notifications as $n): ?>
            <tr>
                <td><?= htmlspecialchars($n['created_at']) ?></td>
                <td><?= htmlspecialchars($n['message']) ?></td>
                <td><?= htmlspecialchars($n['type']) ?></td>
                <td><?= $n['is_read'] ? 'Letta' : 'Non letta' ?></td>
                <td>
                    <?php if (!$n['is_read']): ?>
                        <a href="/index.php?route=notification_read&id=<?= $n['id'] ?>" class="btn btn-sm btn-success">Segna come letta</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <a href="/index.php?route=dashboard" class="btn btn-secondary mt-2">Torna alla dashboard</a>
</div>
</body>
</html>
