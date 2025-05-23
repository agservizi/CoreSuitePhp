<?php
require_once __DIR__ . '/../BaseView.php';
// Form modifica contratto (AdminLTE)
?><!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica Contratto - CoreSuite</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Modifica Contratto</h2>
    <form method="post" action="/index.php?route=contract_edit&id=<?= $contract['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
        <div class="form-group">
            <label>Cliente</label>
            <select name="customer_id" class="form-control" required>
                <?php foreach ($customers as $cu): ?>
                    <option value="<?= $cu['id'] ?>" <?= $contract['customer_id']==$cu['id']?'selected':'' ?>><?= htmlspecialchars($cu['nome']) ?> <?= htmlspecialchars($cu['cognome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Gestore</label>
            <select name="provider_id" class="form-control" required>
                <?php foreach ($providers as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $contract['provider_id']==$p['id']?'selected':'' ?>><?= htmlspecialchars($p['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Tipo</label>
            <select name="type" class="form-control">
                <option value="telefonia" <?= $contract['type']=='telefonia'?'selected':'' ?>>Telefonia</option>
                <option value="luce" <?= $contract['type']=='luce'?'selected':'' ?>>Luce</option>
                <option value="gas" <?= $contract['type']=='gas'?'selected':'' ?>>Gas</option>
            </select>
        </div>
        <div class="form-group">
            <label>Stato</label>
            <input type="text" name="status" class="form-control" value="<?= htmlspecialchars($contract['status']) ?>">
        </div>
        <div class="form-group">
            <label>Data Inizio</label>
            <input type="date" name="data_inizio" class="form-control" value="<?= htmlspecialchars($contract['data_inizio']) ?>">
        </div>
        <div class="form-group">
            <label>Data Fine</label>
            <input type="date" name="data_fine" class="form-control" value="<?= htmlspecialchars($contract['data_fine']) ?>">
        </div>
        <div class="form-group">
            <label>Dati Contrattuali (JSON)</label>
            <textarea name="dati_json" class="form-control"><?= htmlspecialchars($contract['dati_json']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Salva</button>
        <a href="/index.php?route=contracts" class="btn btn-secondary">Annulla</a>
    </form>
    <a href="<?= $attachmentLink ?>" class="btn btn-info mb-2">Gestisci allegati</a>
</div>
</body>
</html>
