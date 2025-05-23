<?php
require_once __DIR__ . '/../BaseView.php';
// Form creazione contratto (AdminLTE)
?><!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Nuovo Contratto - CoreSuite</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Nuovo Contratto</h2>
    <form method="post" action="/index.php?route=contract_create">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
        <div class="form-group">
            <label>Cliente</label>
            <select name="customer_id" class="form-control" required>
                <?php foreach ($customers as $cu): ?>
                    <option value="<?= $cu['id'] ?>"><?= htmlspecialchars($cu['nome']) ?> <?= htmlspecialchars($cu['cognome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Gestore</label>
            <select name="provider_id" class="form-control" required>
                <?php foreach ($providers as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Tipo</label>
            <select name="type" class="form-control">
                <option value="telefonia">Telefonia</option>
                <option value="luce">Luce</option>
                <option value="gas">Gas</option>
            </select>
        </div>
        <div class="form-group">
            <label>Stato</label>
            <input type="text" name="status" class="form-control">
        </div>
        <div class="form-group">
            <label>Data Inizio</label>
            <input type="date" name="data_inizio" class="form-control">
        </div>
        <div class="form-group">
            <label>Data Fine</label>
            <input type="date" name="data_fine" class="form-control">
        </div>
        <div class="form-group">
            <label>Dati Contrattuali (JSON)</label>
            <textarea name="dati_json" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-success">Crea</button>
        <a href="/index.php?route=contracts" class="btn btn-secondary">Annulla</a>
    </form>
</div>
</body>
</html>
