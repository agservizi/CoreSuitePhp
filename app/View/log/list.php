<?php
// Lista log di sistema (AdminLTE)
?><!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Audit Log - CoreSuite</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Audit Log</h2>
    <table class="table table-bordered table-striped">
        <thead><tr><th>Data</th><th>Utente</th><th>Azione</th><th>Dettagli</th></tr></thead>
        <tbody>
        <?php foreach ($logs as $log): ?>
            <tr>
                <td><?= htmlspecialchars($log['created_at']) ?></td>
                <td><?= htmlspecialchars($log['email']) ?></td>
                <td><?= htmlspecialchars($log['action']) ?></td>
                <td><?= htmlspecialchars($log['details']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <a href="/index.php?route=dashboard" class="btn btn-secondary mt-2">Torna alla dashboard</a>
    <a href="/index.php?route=export_logs" class="btn btn-warning mb-2">Esporta Log CSV</a>
</div>
</body>
</html>
