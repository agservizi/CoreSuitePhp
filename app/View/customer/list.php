<?php
// Lista clienti (AdminLTE)
?><!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Clienti - CoreSuite</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Gestione Clienti</h2>
    <a href="/index.php?route=customer_create" class="btn btn-success mb-2">Nuovo Cliente</a>
    <a href="/index.php?route=import_customers" class="btn btn-info mb-2">Importa da CSV</a>
    <a href="/index.php?route=export_customers" class="btn btn-warning mb-2">Esporta CSV</a>
    <table class="table table-bordered table-striped">
        <thead><tr><th>Nome</th><th>Cognome</th><th>Email</th><th>Telefono</th><th>Azioni</th></tr></thead>
        <tbody>
        <?php foreach ($customers as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['nome']) ?></td>
                <td><?= htmlspecialchars($c['cognome']) ?></td>
                <td><?= htmlspecialchars($c['email']) ?></td>
                <td><?= htmlspecialchars($c['telefono']) ?></td>
                <td>
                    <a href="/index.php?route=customer_edit&id=<?= $c['id'] ?>" class="btn btn-sm btn-primary">Modifica</a>
                    <a href="/index.php?route=customer_delete&id=<?= $c['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Eliminare cliente?')">Elimina</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
