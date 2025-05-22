<?php
// Form wizard dinamico per creazione/modifica contratto
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}
require_once __DIR__ . '/../../../config/contract_forms.php';
require_once __DIR__ . '/../../utils/Validation.php';
use CoreSuite\Utils\Validation;
$formConfig = include __DIR__ . '/../../../config/contract_forms.php';
$type = $_GET['type'] ?? 'telefonia';
$step = $_GET['step'] ?? 'anagrafica';
$steps = array_keys($formConfig[$type]['steps']);
if (!in_array($step, $steps)) $step = $steps[0];
$fields = $formConfig[$type]['steps'][$step]['fields'];
$draft = $_SESSION['contract_draft'][$type][$step] ?? [];
$errors = [];
$drafts = $_SESSION['contract_draft'][$type] ?? [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($fields as $f) {
        if ($f['type'] === 'file') {
            if (isset($_FILES[$f['name']]) && $_FILES[$f['name']]['error'] === UPLOAD_ERR_OK) {
                $tmp = $_FILES[$f['name']]['tmp_name'];
                $name = uniqid('doc_', true) . '_' . basename($_FILES[$f['name']]['name']);
                $dest = __DIR__ . '/../../../uploads/tmp/' . $name;
                if (!is_dir(dirname($dest))) mkdir(dirname($dest), 0755, true);
                move_uploaded_file($tmp, $dest);
                $draft[$f['name']] = $name;
            } elseif (!empty($f['required']) && empty($draft[$f['name']])) {
                $errors[$f['name']] = 'Carica il file richiesto';
            }
        } else {
            $draft[$f['name']] = $_POST[$f['name']] ?? '';
        }
    }
    $_SESSION['contract_draft'][$type][$step] = $draft;
    foreach ($fields as $f) {
        if (!empty($f['required']) && empty($draft[$f['name']])) {
            $errors[$f['name']] = 'Campo obbligatorio';
        }
        if (!empty($f['validate']) && !empty($draft[$f['name']])) {
            $val = $draft[$f['name']];
            switch ($f['validate']) {
                case 'cf': if (!Validation::cf($val)) $errors[$f['name']] = 'Codice fiscale non valido'; break;
                case 'piva': if (!Validation::piva($val)) $errors[$f['name']] = 'Partita IVA non valida'; break;
                case 'iban': if (!Validation::iban($val)) $errors[$f['name']] = 'IBAN non valido'; break;
                case 'cap': if (!Validation::cap($val)) $errors[$f['name']] = 'CAP non valido'; break;
                case 'pod': if (!Validation::pod($val)) $errors[$f['name']] = 'POD non valido'; break;
                case 'pdr': if (!Validation::pdr($val)) $errors[$f['name']] = 'PDR non valido'; break;
                case 'migrazione': if (!Validation::migrazione($val)) $errors[$f['name']] = 'Codice migrazione non valido'; break;
            }
        }
    }
    if (!$errors) {
        $nextIdx = array_search($step, $steps) + 1;
        if ($nextIdx < count($steps)) {
            header('Location: ?type=' . $type . '&step=' . $steps[$nextIdx]);
            exit;
        } else {
            header('Location: ?type=' . $type . '&step=riepilogo');
            exit;
        }
    }
}
if (isset($_GET['autosave']) && $_GET['autosave'] == 1 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($fields as $f) {
        $draft[$f['name']] = $_POST[$f['name']] ?? '';
    }
    $_SESSION['contract_draft'][$type][$step] = $draft;
    exit;
}
if ($step === 'conferma' && !empty($drafts)) {
    require_once __DIR__ . '/../../controllers/ContractController.php';
    $controller = new \CoreSuite\Controllers\ContractController();
    $contractId = $controller->saveFromDraft($type, $drafts);
    unset($_SESSION['contract_draft'][$type]);
    echo '<div class="alert alert-success mt-4">Contratto salvato con successo!</div>';
    echo '<a href="/contracts_show.php?id=' . $contractId . '" class="btn btn-primary mt-2">Vai al dettaglio contratto</a>';
    echo '<script>showToast("Contratto salvato con successo!","success");</script>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Nuovo Contratto - Wizard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jquery-ui-dist/jquery-ui.min.css">
    <style>
    .progress-step { height: 8px; border-radius: 4px; }
    .toast { position: fixed; top: 20px; right: 20px; z-index: 9999; }
    .skeleton { background: #eee; min-height: 40px; border-radius: 4px; animation: skeleton 1.2s infinite linear alternate; }
    @keyframes skeleton { 0% { opacity: 0.6; } 100% { opacity: 1; } }
    </style>
</head>
<body>
<div class="container mt-4">
    <div class="mb-3">
        <div class="progress">
            <?php $currIdx = array_search($step, $steps); $perc = round(($currIdx+1)/count($steps)*100); ?>
            <div class="progress-bar bg-primary progress-step" role="progressbar" style="width: <?= $perc ?>%" aria-valuenow="<?= $perc ?>" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <div class="text-right small">Step <?= $currIdx+1 ?> di <?= count($steps) ?></div>
    </div>
    <div id="toast-container"></div>
    <div id="form-content">
    <?php if ($step !== 'riepilogo'): ?>
    <form method="post" enctype="multipart/form-data">
        <?php foreach ($fields as $f): ?>
            <div class="form-group">
                <label><?= $f['label'] ?><?= !empty($f['required']) ? '*' : '' ?></label>
                <?php if ($f['type'] === 'select'): ?>
                    <select name="<?= $f['name'] ?>" class="form-control" <?= !empty($f['required']) ? 'required' : '' ?>>
                        <option value="">Seleziona...</option>
                        <?php foreach ($f['options'] as $opt): ?>
                            <option value="<?= $opt ?>" <?= ($draft[$f['name']] ?? '') == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php elseif ($f['type'] === 'checkbox'): ?>
                    <input type="checkbox" name="<?= $f['name'] ?>" value="1" <?= !empty($draft[$f['name']]) ? 'checked' : '' ?>>
                <?php elseif ($f['type'] === 'checkbox_group'): ?>
                    <?php foreach ($f['options'] as $opt): ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="<?= $f['name'] ?>[]" value="<?= $opt ?>" <?= (isset($draft[$f['name']]) && in_array($opt, (array)$draft[$f['name']])) ? 'checked' : '' ?>>
                            <label class="form-check-label"><?= $opt ?></label>
                        </div>
                    <?php endforeach; ?>
                <?php elseif ($f['type'] === 'textarea'): ?>
                    <textarea name="<?= $f['name'] ?>" class="form-control" <?= !empty($f['required']) ? 'required' : '' ?>><?= htmlspecialchars($draft[$f['name']] ?? '') ?></textarea>
                <?php elseif ($f['type'] === 'file'): ?>
                    <input type="file" name="<?= $f['name'] ?>" class="form-control-file" <?= !empty($f['required']) ? 'required' : '' ?> />
                    <?php if (!empty($draft[$f['name']])): ?>
                        <div class="small text-success">File caricato: <?= htmlspecialchars($draft[$f['name']]) ?></div>
                    <?php endif; ?>
                <?php else: ?>
                    <input type="<?= $f['type'] ?>" name="<?= $f['name'] ?>" class="form-control" value="<?= htmlspecialchars($draft[$f['name']] ?? '') ?>" <?= !empty($f['required']) ? 'required' : '' ?> />
                <?php endif; ?>
                <?php if (!empty($errors[$f['name']])): ?>
                    <div class="text-danger small"><?= $errors[$f['name']] ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <button type="submit" class="btn btn-primary">Avanti</button>
    </form>
    <?php else: ?>
        <h4>Riepilogo dati inseriti</h4>
        <?php foreach ($steps as $s): if ($s === 'riepilogo') continue; ?>
            <div class="card mb-2">
                <div class="card-header"><b><?= $formConfig[$type]['steps'][$s]['label'] ?></b></div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                    <?php foreach ($formConfig[$type]['steps'][$s]['fields'] as $f): ?>
                        <li class="list-group-item">
                            <b><?= $f['label'] ?>:</b> <?= htmlspecialchars($drafts[$s][$f['name']] ?? '-') ?>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
        <form method="post" action="?type=<?= $type ?>&step=conferma">
            <button type="submit" class="btn btn-success">Conferma e Salva Contratto</button>
        </form>
    <?php endif; ?>
    </div>
    <?php if ($step === 'anagrafica'): ?>
    <div class="mb-3">
        <label>Cerca cliente esistente</label>
        <input type="text" id="autocomplete-cf" class="form-control" placeholder="Nome, Cognome o Codice Fiscale...">
    </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-ui-dist/jquery-ui.min.js"></script>
<script>
// Toast feedback
function showToast(msg, type='success') {
    var html = `<div class="toast bg-${type==='success'?'success':'danger'} text-white" data-delay="3000"><div class="toast-body">${msg}</div></div>`;
    $('#toast-container').append(html);
    $('.toast').toast('show').on('hidden.bs.toast', function(){ $(this).remove(); });
}
// Autosave draft ogni 30s
setInterval(function(){
    var form = $('form').first();
    if (form.length) {
        $.post(window.location.href+'&autosave=1', form.serialize());
        showToast('Draft salvato automaticamente','success');
    }
}, 30000);
// Skeleton loading su cambio step
$('a.btn-outline-secondary').on('click', function(e){
    $('#form-content').html('<div class="skeleton mb-3"></div><div class="skeleton mb-3"></div><div class="skeleton mb-3"></div>');
});
<?php if ($step === 'anagrafica'): ?>
$('#autocomplete-cf').autocomplete({
    minLength: 2,
    source: function(request, response) {
        $.getJSON('/api/v1/customers.php', {q: request.term}, function(data) {
            response(data.map(function(c) {
                return {
                    label: c.name + ' ' + c.surname + ' (' + c.fiscal_code + ')',
                    value: c.fiscal_code,
                    data: c
                };
            }));
        });
    },
    select: function(event, ui) {
        var c = ui.item.data;
        $('[name=nome]').val(c.name);
        $('[name=cognome]').val(c.surname);
        $('[name=codice_fiscale]').val(c.fiscal_code);
        $('[name=data_nascita]').val(c.date_of_birth);
        $('[name=luogo_nascita]').val(c.place_of_birth);
        $('[name=provincia_nascita]').val(c.province_of_birth);
        $('[name=sesso]').val(c.gender);
        $('[name=cittadinanza]').val(c.citizenship);
        $('[name=email]').val(c.email);
        $('[name=telefono]').val(c.phone);
        $('[name=cellulare]').val(c.mobile);
    }
});
<?php endif; ?>
</script>
</body>
</html>
