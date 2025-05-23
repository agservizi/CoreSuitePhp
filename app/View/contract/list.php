<?php
// Lista contratti (AdminLTE)
?><!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Contratti - CoreSuite</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Gestione Contratti</h2>
    <a href="/index.php?route=contract_create" class="btn btn-success mb-2">Nuovo Contratto</a>
    <a href="/index.php?route=export_contracts" class="btn btn-warning mb-2">Esporta Contratti CSV</a>
    <table class="table table-bordered table-striped">
        <thead><tr><th>Cliente</th><th>Gestore</th><th>Tipo</th><th>Stato</th><th>Inizio</th><th>Fine</th><th>Azioni</th></tr></thead>
        <tbody>
        <?php foreach ($contracts as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['nome']) ?> <?= htmlspecialchars($c['cognome']) ?></td>
                <td><?= htmlspecialchars($c['provider_name']) ?></td>
                <td><?= htmlspecialchars($c['type']) ?></td>
                <td><?= htmlspecialchars($c['status']) ?></td>
                <td><?= htmlspecialchars($c['data_inizio']) ?></td>
                <td><?= htmlspecialchars($c['data_fine']) ?></td>
                <td>
                    <a href="/index.php?route=contract_edit&id=<?= $c['id'] ?>" class="btn btn-sm btn-primary">Modifica</a>
                    <a href="/index.php?route=contract_delete&id=<?= $c['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Eliminare contratto?')">Elimina</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
