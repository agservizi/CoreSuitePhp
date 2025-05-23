<?php
// Funzioni CSRF globali
if (!function_exists('csrf_token')) {
    function csrf_token() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    function csrf_check($token) {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}

// Vista MFA TOTP AdminLTE
$error = $error ?? '';
?><!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>MFA CoreSuite</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="#" class="h1"><b>Core</b>Suite</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Verifica MFA (TOTP)</p>
      <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
      <form method="post" action="/index.php?route=mfa">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
        <div class="input-group mb-3">
          <input type="text" name="totp" class="form-control" placeholder="Codice MFA" required maxlength="6">
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-key"></span></div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Verifica</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
