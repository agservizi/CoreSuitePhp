<?php
// Wizard installazione CoreSuite completo
session_start();

// Mostra errori PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Carica config DB
$config = require __DIR__ . '/../config/database.php';

// Step installazione
$step = isset($_GET['step']) ? intval($_GET['step']) : 1;
$error = '';
$success = '';

function check_requirements() {
    $errors = [];
    if (version_compare(PHP_VERSION, '8.0.0', '<')) $errors[] = 'PHP >= 8.0.0 richiesto.';
    $exts = ['pdo', 'pdo_mysql', 'openssl', 'mbstring', 'json', 'session'];
    foreach ($exts as $ext) if (!extension_loaded($ext)) $errors[] = "Estensione PHP mancante: $ext";
    if (!is_writable(__DIR__ . '/../uploads')) $errors[] = 'Cartella uploads non scrivibile.';
    if (!is_writable(__DIR__ . '/../logs')) $errors[] = 'Cartella logs non scrivibile.';
    return $errors;
}

function test_db($config) {
    try {
        $pdo = new PDO('mysql:host=' . $config['db_host'] . ';dbname=' . $config['db_name'], $config['db_user'], $config['db_password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return true;
    } catch (Exception $e) {
        return $e->getMessage();
    }
}

function import_sql($config, $file) {
    try {
        $pdo = new PDO('mysql:host=' . $config['db_host'] . ';dbname=' . $config['db_name'], $config['db_user'], $config['db_password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = file_get_contents($file);
        $pdo->exec($sql);
        return true;
    } catch (Exception $e) {
        return $e->getMessage();
    }
}

function create_admin($config, $email, $password) {
    try {
        $pdo = new PDO('mysql:host=' . $config['db_host'] . ';dbname=' . $config['db_name'], $config['db_user'], $config['db_password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Crea ruolo admin se non esiste
        $pdo->exec("INSERT IGNORE INTO roles (id, name, permissions) VALUES (1, 'admin', 'all')");
        // Crea utente admin
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (email, password, role_id) VALUES (?, ?, 1)");
        $stmt->execute([$email, $hash]);
        return true;
    } catch (Exception $e) {
        return $e->getMessage();
    }
}

// Gestione step
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 1) {
        $req = check_requirements();
        if (count($req) === 0) {
            header('Location: ?step=2'); exit;
        } else {
            $error = implode('<br>', $req);
        }
    }
    if ($step === 2) {
        $test = test_db($config);
        if ($test === true) {
            $imp = import_sql($config, __DIR__ . '/coresuite_schema.sql');
            if ($imp === true) {
                header('Location: ?step=3'); exit;
            } else {
                $error = 'Errore importazione SQL: ' . $imp;
            }
        } else {
            $error = 'Errore connessione DB: ' . $test;
        }
    }
    if ($step === 3) {
        $email = trim($_POST['admin_email'] ?? '');
        $password = $_POST['admin_password'] ?? '';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
            $error = 'Email non valida o password troppo corta.';
        } else {
            $res = create_admin($config, $email, $password);
            if ($res === true) {
                $success = 'Installazione completata! Admin creato.';
                header('Location: ?step=4'); exit;
            } else {
                $error = 'Errore creazione admin: ' . $res;
            }
        }
    }
}
?><!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Installazione CoreSuite</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>.step {font-weight:bold; color:#007bff;}</style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="#" class="h1"><b>Core</b>Suite</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Installazione guidata - Step <?php echo $step; ?>/4</p>
      <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
      <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
      <?php if ($step === 1): ?>
        <form method="post">
          <h5 class="step">1. Verifica requisiti</h5>
          <ul>
            <?php foreach (check_requirements() as $req): ?><li style="color:red"><?php echo $req; ?></li><?php endforeach; ?>
          </ul>
          <?php if (count(check_requirements()) === 0): ?>
            <button type="submit" class="btn btn-primary btn-block">Prosegui</button>
          <?php else: ?>
            <button type="button" class="btn btn-secondary btn-block" disabled>Correggi i requisiti</button>
          <?php endif; ?>
        </form>
      <?php elseif ($step === 2): ?>
        <form method="post">
          <h5 class="step">2. Connessione database e creazione tabelle</h5>
          <button type="submit" class="btn btn-primary btn-block">Testa e crea tabelle</button>
        </form>
      <?php elseif ($step === 3): ?>
        <form method="post">
          <h5 class="step">3. Crea utente amministratore</h5>
          <div class="form-group">
            <label>Email admin</label>
            <input type="email" name="admin_email" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Password (min 8 caratteri)</label>
            <input type="password" name="admin_password" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Crea Admin</button>
        </form>
      <?php elseif ($step === 4): ?>
        <div class="alert alert-success">Installazione completata! <br> <a href="../public/index.php" class="btn btn-success mt-2">Vai alla Dashboard</a></div>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>
