<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CoreSuite</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/coresuite-theme.css">
    <link rel="icon" href="/assets/images/coresuite-favicon.svg">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <style>
        body { background: #f4f8fb; }
        .login-logo img { height: 48px; margin-bottom: 8px; }
    </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <img src="/assets/images/coresuite-logo.svg" alt="CoreSuite Logo"><br>
        <b style="color:#0066CC;">CoreSuite</b>
    </div>
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <span class="h4">Accedi a CoreSuite</span>
        </div>
        <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger text-center mb-3"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" action="/login.php">
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                </div>
                <div class="row align-items-center mb-3">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Ricordami</label>
                        </div>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Accedi</button>
                    </div>
                </div>
            </form>
            <p class="mb-1 text-center">
                <a href="/password-reset.php">Password dimenticata?</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>
