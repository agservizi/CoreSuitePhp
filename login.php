<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreSuite - Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="hold-transition login-page bg-coresuite">
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">                <h1 class="h1"><span class="text-primary font-weight-bold">Core</span><span class="font-weight-light">Suite</span></h1>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Accedi per iniziare la tua sessione</p>

                <form action="auth.php" method="post" id="loginForm">
                    <div class="input-group mb-3">
                        <input type="tel" class="form-control" name="phone" placeholder="Numero di cellulare" required pattern="[0-9]{10,15}" maxlength="15">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-phone"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember" name="remember">
                                <label for="remember">
                                    Ricordami
                                </label>
                            </div>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                        </div>
                    </div>
                </form>

                <!-- Form MFA (inizialmente nascosto) -->
                <form action="verify-mfa.php" method="post" id="mfaForm" style="display: none;">
                    <div class="input-group mb-3 mt-3">
                        <input type="text" class="form-control" name="mfa_code" placeholder="Codice 2FA" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-key"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">Verifica</button>
                        </div>
                    </div>
                </form>

                <p class="mb-1 mt-3">
                    <a href="forgot-password.php">Ho dimenticato la password</a>
                </p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/adminlte@3.2/dist/js/adminlte.min.js"></script>
    <script>
        // Gestione del form di login
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('auth.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.requiresMfa) {
                    // Nascondi il form di login e mostra il form MFA
                    document.getElementById('loginForm').style.display = 'none';
                    document.getElementById('mfaForm').style.display = 'block';
                } else if (data.success) {
                    // Reindirizza alla dashboard
                    window.location.href = 'index.php';
                } else {
                    // Mostra errore
                    alert(data.message || 'Errore durante il login');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Errore durante il login');
            });
        });

        // Gestione del form MFA
        document.getElementById('mfaForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('verify-mfa.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'index.php';
                } else {
                    alert(data.message || 'Codice MFA non valido');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Errore durante la verifica MFA');
            });
        });
    </script>
</body>
</html>
