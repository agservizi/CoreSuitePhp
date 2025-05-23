<?php
require_once __DIR__ . '/../BaseView.php';

// Lista e upload allegati contratto (AdminLTE)
$error = $error ?? '';
?><!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Allegati Contratto - CoreSuite</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Allegati Contratto</h2>
    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data" action="/index.php?route=attachment_upload&contract_id=<?= $_GET['contract_id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
        <div class="form-group">
            <label>Carica file (max 5MB, pdf/jpg/png/doc/docx)</label>
            <input type="file" name="file" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Upload</button>
    </form>
    <h4 class="mt-4">Allegati già caricati</h4>
    <table class="table table-bordered table-striped">
        <thead><tr><th>Nome file</th><th>Azioni</th></tr></thead>
        <tbody>
        <?php foreach ($attachments as $a): ?>
            <tr>
                <td><a href="/uploads/<?= htmlspecialchars($a['file_path']) ?>" target="_blank"><?= htmlspecialchars($a['file_name']) ?></a></td>
                <td>
                    <a href="/index.php?route=attachment_delete&id=<?= $a['id'] ?>&contract_id=<?= $_GET['contract_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Eliminare allegato?')">Elimina</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <a href="/index.php?route=contracts" class="btn btn-secondary mt-2">Torna ai contratti</a>
</div>
</body>
</html>
