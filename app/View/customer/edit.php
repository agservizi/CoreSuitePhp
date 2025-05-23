<?php
require_once __DIR__ . '/../BaseView.php';
// Form modifica cliente (AdminLTE)
?><!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica Cliente - CoreSuite</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Modifica Cliente</h2>
    <form method="post" action="/index.php?route=customer_edit&id=<?= $customer['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
        <div class="form-group"><label>Nome</label><input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($customer['nome']) ?>" required></div>
        <div class="form-group"><label>Cognome</label><input type="text" name="cognome" class="form-control" value="<?= htmlspecialchars($customer['cognome']) ?>" required></div>
        <div class="form-group"><label>Codice Fiscale</label><input type="text" name="cf" class="form-control" value="<?= htmlspecialchars($customer['cf']) ?>"></div>
        <div class="form-group"><label>Documento</label><input type="text" name="documento" class="form-control" value="<?= htmlspecialchars($customer['documento']) ?>"></div>
        <div class="form-group"><label>Telefono</label><input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($customer['telefono']) ?>"></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($customer['email']) ?>"></div>
        <div class="form-group"><label>P.IVA</label><input type="text" name="piva" class="form-control" value="<?= htmlspecialchars($customer['piva']) ?>"></div>
        <div class="form-group"><label>Ragione Sociale</label><input type="text" name="ragione_sociale" class="form-control" value="<?= htmlspecialchars($customer['ragione_sociale']) ?>"></div>
        <div class="form-group"><label>Rappresentante</label><input type="text" name="rappresentante" class="form-control" value="<?= htmlspecialchars($customer['rappresentante']) ?>"></div>
        <div class="form-group"><label>SDI/PEC</label><input type="text" name="sdi_pec" class="form-control" value="<?= htmlspecialchars($customer['sdi_pec']) ?>"></div>
        <button type="submit" class="btn btn-primary">Salva</button>
        <a href="/index.php?route=customers" class="btn btn-secondary">Annulla</a>
    </form>
</div>
</body>
</html>
