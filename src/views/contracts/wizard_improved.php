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
    if (empty($errors)) {
        $nextStep = null;
        foreach ($steps as $idx => $s) {
            if ($s === $step && isset($steps[$idx+1])) {
                $nextStep = $steps[$idx+1];
                break;
            }
        }
        if ($nextStep) {
            header('Location: /contracts_wizard.php?type=' . urlencode($type) . '&step=' . urlencode($nextStep));
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creazione Contratto - CoreSuite</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/coresuite-theme.css">
    <link rel="icon" href="/assets/images/coresuite-favicon.svg">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="/dashboard.php" class="nav-link">Dashboard</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="/contracts.php" class="nav-link">Contratti</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-user"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="/profile.php" class="dropdown-item">
                        <i class="fas fa-user-cog mr-2"></i> Profilo
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="/logout.php" class="dropdown-item">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                </div>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="/dashboard.php" class="brand-link">
            <img src="/assets/images/coresuite-logo.svg" alt="CoreSuite Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">CoreSuite</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="/dashboard.php" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/contracts.php" class="nav-link active">
                            <i class="nav-icon fas fa-file-contract"></i>
                            <p>Contratti</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/customers.php" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Clienti</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/providers.php" class="nav-link">
                            <i class="nav-icon fas fa-building"></i>
                            <p>Provider</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/notifications.php" class="nav-link">
                            <i class="nav-icon fas fa-bell"></i>
                            <p>Notifiche</p>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Creazione Contratto</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/dashboard.php">Home</a></li>
                            <li class="breadcrumb-item"><a href="/contracts.php">Contratti</a></li>
                            <li class="breadcrumb-item active">Creazione Wizard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <?php echo $formConfig[$type]['title'] ?? 'Nuovo Contratto'; ?> - 
                                    <?php echo $formConfig[$type]['steps'][$step]['title'] ?? $step; ?>
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <div class="bs-stepper">
                                            <div class="bs-stepper-header" role="tablist">
                                                <?php 
                                                $idx = 0;
                                                foreach($steps as $s): 
                                                    $idx++;
                                                    $isActive = ($s === $step);
                                                    $isDone = array_search($s, $steps) < array_search($step, $steps);
                                                ?>
                                                <!-- Step -->
                                                <?php if ($idx > 1): ?>
                                                <div class="line"></div>
                                                <?php endif; ?>
                                                <div class="step <?= $isActive ? 'active' : ''; ?> <?= $isDone ? 'done' : ''; ?>">
                                                    <button type="button" class="step-trigger">
                                                        <span class="bs-stepper-circle">
                                                            <?php if ($isDone): ?>
                                                                <i class="fas fa-check"></i>
                                                            <?php else: ?>
                                                                <?= $idx ?>
                                                            <?php endif; ?>
                                                        </span>
                                                        <span class="bs-stepper-label"><?= htmlspecialchars($formConfig[$type]['steps'][$s]['title'] ?? $s); ?></span>
                                                    </button>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="progress">
                                        <?php $currIdx = array_search($step, $steps); $perc = round(($currIdx+1)/count($steps)*100); ?>
                                        <div class="progress-bar bg-primary progress-step" role="progressbar" style="width: <?= $perc ?>%" aria-valuenow="<?= $perc ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="text-right small">Step <?= $currIdx+1 ?> di <?= count($steps) ?></div>
                                </div>

                                <?php if ($step !== 'riepilogo'): ?>
                                <form method="post" enctype="multipart/form-data">
                                    <div class="row">
                                        <?php foreach ($fields as $f): ?>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="<?= $f['name'] ?>"><?= $f['label'] ?><?= !empty($f['required']) ? ' <span class="text-danger">*</span>' : '' ?></label>
                                                
                                                <?php if ($f['type'] === 'select'): ?>
                                                    <select name="<?= $f['name'] ?>" id="<?= $f['name'] ?>" class="form-control <?= !empty($errors[$f['name']]) ? 'is-invalid' : '' ?>" <?= !empty($f['required']) ? 'required' : '' ?>>
                                                        <option value="">Seleziona...</option>
                                                        <?php foreach ($f['options'] as $opt): ?>
                                                            <option value="<?= $opt ?>" <?= ($draft[$f['name']] ?? '') == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                
                                                <?php elseif ($f['type'] === 'checkbox'): ?>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="<?= $f['name'] ?>" name="<?= $f['name'] ?>" value="1" <?= !empty($draft[$f['name']]) ? 'checked' : '' ?>>
                                                        <label class="custom-control-label" for="<?= $f['name'] ?>"><?= $f['label'] ?></label>
                                                    </div>
                                                
                                                <?php elseif ($f['type'] === 'checkbox_group'): ?>
                                                    <div>
                                                    <?php foreach ($f['options'] as $opt): ?>
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox" id="<?= $f['name'] . '_' . $opt ?>" name="<?= $f['name'] ?>[]" value="<?= $opt ?>" <?= (isset($draft[$f['name']]) && in_array($opt, (array)$draft[$f['name']])) ? 'checked' : '' ?>>
                                                            <label class="custom-control-label" for="<?= $f['name'] . '_' . $opt ?>"><?= $opt ?></label>
                                                        </div>
                                                    <?php endforeach; ?>
                                                    </div>
                                                
                                                <?php elseif ($f['type'] === 'textarea'): ?>
                                                    <textarea name="<?= $f['name'] ?>" id="<?= $f['name'] ?>" class="form-control <?= !empty($errors[$f['name']]) ? 'is-invalid' : '' ?>" rows="4" <?= !empty($f['required']) ? 'required' : '' ?>><?= htmlspecialchars($draft[$f['name']] ?? '') ?></textarea>
                                                
                                                <?php elseif ($f['type'] === 'file'): ?>
                                                    <div class="input-group">
                                                        <div class="custom-file">
                                                            <input type="file" name="<?= $f['name'] ?>" id="<?= $f['name'] ?>" class="custom-file-input <?= !empty($errors[$f['name']]) ? 'is-invalid' : '' ?>" <?= !empty($f['required']) && empty($draft[$f['name']]) ? 'required' : '' ?>>
                                                            <label class="custom-file-label" for="<?= $f['name'] ?>">Scegli file...</label>
                                                        </div>
                                                    </div>
                                                    <?php if (!empty($draft[$f['name']])): ?>
                                                        <div class="small text-success mt-1">
                                                            <i class="fas fa-check-circle"></i> File caricato: <?= htmlspecialchars($draft[$f['name']]) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                
                                                <?php else: ?>
                                                    <?php 
                                                    // Aggiungiamo icone significative in base al tipo di campo
                                                    $icon = '';
                                                    $inputGroupClass = '';
                                                    switch($f['type']) {
                                                        case 'email': $icon = 'fas fa-envelope'; $inputGroupClass = 'input-group'; break;
                                                        case 'tel': $icon = 'fas fa-phone'; $inputGroupClass = 'input-group'; break;
                                                        case 'date': $icon = 'fas fa-calendar'; $inputGroupClass = 'input-group'; break;
                                                        case 'number': $icon = 'fas fa-hashtag'; $inputGroupClass = 'input-group'; break;
                                                    }
                                                    if (!empty($f['validate'])) {
                                                        switch($f['validate']) {
                                                            case 'cf': $icon = 'fas fa-id-card'; $inputGroupClass = 'input-group'; break;
                                                            case 'piva': $icon = 'fas fa-building'; $inputGroupClass = 'input-group'; break;
                                                            case 'iban': $icon = 'fas fa-university'; $inputGroupClass = 'input-group'; break;
                                                        }
                                                    }
                                                    ?>
                                                    
                                                    <div class="<?= $inputGroupClass ?>">
                                                        <?php if ($icon): ?>
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="<?= $icon ?>"></i></span>
                                                            </div>
                                                        <?php endif; ?>
                                                        <input type="<?= $f['type'] ?>" name="<?= $f['name'] ?>" id="<?= $f['name'] ?>" class="form-control <?= !empty($errors[$f['name']]) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($draft[$f['name']] ?? '') ?>" <?= !empty($f['required']) ? 'required' : '' ?> placeholder="<?= htmlspecialchars($f['placeholder'] ?? '') ?>">
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($errors[$f['name']])): ?>
                                                    <div class="invalid-feedback d-block"><?= $errors[$f['name']] ?></div>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($f['help'])): ?>
                                                    <small class="form-text text-muted"><?= $f['help'] ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="card-footer">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <?php if ($currIdx > 0): ?>
                                                    <a href="/contracts_wizard.php?type=<?= urlencode($type) ?>&step=<?= urlencode($steps[$currIdx-1]) ?>" class="btn btn-secondary">
                                                        <i class="fas fa-arrow-left"></i> Indietro
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <button type="submit" class="btn btn-primary">
                                                    <?= ($step === end($steps)) ? 'Completa' : 'Avanti' ?> <i class="fas fa-arrow-right"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                
                                <?php else: ?>
                                <h4 class="mb-4">Riepilogo dati inseriti</h4>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <?php foreach ($steps as $s): if ($s === 'riepilogo') continue; ?>
                                        <div class="card card-outline card-info mb-3">
                                            <div class="card-header">
                                                <h3 class="card-title"><?= htmlspecialchars($formConfig[$type]['steps'][$s]['title'] ?? $s) ?></h3>
                                                <div class="card-tools">
                                                    <a href="/contracts_wizard.php?type=<?= urlencode($type) ?>&step=<?= urlencode($s) ?>" class="btn btn-tool">
                                                        <i class="fas fa-edit"></i> Modifica
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="card-body p-0">
                                                <table class="table table-striped">
                                                    <tbody>
                                                        <?php 
                                                        $stepFields = $formConfig[$type]['steps'][$s]['fields'];
                                                        $stepDraft = $drafts[$s] ?? [];
                                                        foreach ($stepFields as $f): 
                                                        ?>
                                                        <tr>
                                                            <td style="width:35%"><strong><?= htmlspecialchars($f['label']) ?></strong></td>
                                                            <td>
                                                                <?php if ($f['type'] === 'file' && !empty($stepDraft[$f['name']])): ?>
                                                                    <a href="/uploads/tmp/<?= $stepDraft[$f['name']] ?>" target="_blank">
                                                                        <i class="fas fa-file"></i> <?= htmlspecialchars($stepDraft[$f['name']]) ?>
                                                                    </a>
                                                                <?php elseif ($f['type'] === 'checkbox' || $f['type'] === 'checkbox_group'): ?>
                                                                    <?php 
                                                                    if (isset($stepDraft[$f['name']])) {
                                                                        if (is_array($stepDraft[$f['name']])) {
                                                                            echo implode(', ', $stepDraft[$f['name']]);
                                                                        } else {
                                                                            echo !empty($stepDraft[$f['name']]) ? 'Sì' : 'No';
                                                                        }
                                                                    } else {
                                                                        echo 'No';
                                                                    }
                                                                    ?>
                                                                <?php else: ?>
                                                                    <?= htmlspecialchars($stepDraft[$f['name']] ?? '') ?>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <a href="/contracts_wizard.php?type=<?= urlencode($type) ?>&step=<?= urlencode($steps[count($steps)-2]) ?>" class="btn btn-secondary">
                                                <i class="fas fa-arrow-left"></i> Indietro
                                            </a>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <form method="post" action="/api/v1/contracts.php">
                                                <input type="hidden" name="action" value="create_from_draft">
                                                <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-check"></i> Salva Contratto
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer">
        <div class="float-right d-none d-sm-block">
            <b>Version</b> 1.0.0
        </div>
        <strong>Copyright &copy; <?= date('Y') ?> <a href="/">CoreSuite</a>.</strong> Tutti i diritti riservati.
    </footer>
</div>

<script>
// Inizializzazione file input bootstrap
$(document).ready(function() {
    bsCustomFileInput.init();
    
    // Mostra nome file selezionato
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
</body>
</html>
