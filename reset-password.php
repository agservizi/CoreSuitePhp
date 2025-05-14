<?php
require_once 'auth.php';

$auth = new Auth();
$message = '';
$showForm = true;
$success = false;
$token = isset($_GET['token']) ? $_GET['token'] : '';

// Validazione del token
$resetData = $auth->validatePasswordResetToken($token);
if (!$resetData) {
    $message = "Il link di recupero non è valido o è scaduto. Richiedi un nuovo link.";
    $showForm = false;
}

// Gestione del form di reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $showForm) {
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    $config = require_once 'config/config.php';
    $minLength = $config['password_min_length'];
    
    // Validazione password
    if (strlen($password) < $minLength) {
        $message = "La password deve essere di almeno $minLength caratteri.";
    } elseif ($password !== $confirmPassword) {
        $message = "Le password non coincidono.";
    } else {
        // Aggiorna la password
        $updated = $auth->updatePassword($token, $password);
        
        if ($updated) {
            $success = true;
            $showForm = false;
        } else {
            $message = "Si è verificato un errore durante l'aggiornamento della password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreSuite - Reimposta password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="hold-transition login-page bg-coresuite">
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <h1 class="h1"><span class="text-primary font-weight-bold">Core</span><span class="font-weight-light">Suite</span></h1>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <h5><i class="icon fas fa-check"></i> Password aggiornata!</h5>
                        La tua password è stata reimpostata con successo. Ora puoi accedere con la nuova password.
                    </div>
                    <p class="mt-3 text-center">
                        <a href="login.php" class="btn btn-primary">Vai al login</a>
                    </p>
                <?php elseif ($showForm): ?>
                    <p class="login-box-msg">Imposta la tua nuova password</p>
                    
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-danger">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <form action="reset-password.php?token=<?php echo htmlspecialchars($token); ?>" method="post">
                        <div class="input-group mb-3">
                            <input type="password" class="form-control" name="password" id="password" placeholder="Nuova password" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" class="form-control" name="confirm_password" placeholder="Conferma password" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-block">Reimposta password</button>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-danger">
                        <?php echo $message; ?>
                    </div>
                    <p class="mt-3 text-center">
                        <a href="forgot-password.php" class="btn btn-outline-primary">Richiedi nuovo link</a>
                        <a href="login.php" class="btn btn-primary ml-2">Torna al login</a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <?php if ($showForm): ?>
    <script>
        // Gestione della sicurezza della password
        document.getElementById('password').addEventListener('input', function() {
            var password = this.value;
            var strength = 0;
            
            // Controlla la lunghezza
            if (password.length >= 8) strength += 1;
            
            // Controlla la presenza di numeri
            if (/\d/.test(password)) strength += 1;
            
            // Controlla la presenza di lettere minuscole e maiuscole
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 1;
            
            // Controlla la presenza di caratteri speciali
            if (/[^a-zA-Z0-9]/.test(password)) strength += 1;
            
            // Aggiorna l'indicatore di sicurezza
            var strengthText = '';
            var strengthClass = '';
            
            switch(strength) {
                case 0:
                case 1:
                    strengthText = 'Debole';
                    strengthClass = 'danger';
                    break;
                case 2:
                    strengthText = 'Media';
                    strengthClass = 'warning';
                    break;
                case 3:
                    strengthText = 'Buona';
                    strengthClass = 'info';
                    break;
                case 4:
                    strengthText = 'Forte';
                    strengthClass = 'success';
                    break;
            }
            
            // Crea o aggiorna l'indicatore
            var feedbackEl = document.getElementById('password-strength');
            if (!feedbackEl) {
                feedbackEl = document.createElement('div');
                feedbackEl.id = 'password-strength';
                feedbackEl.className = 'mt-2 mb-3';
                this.parentNode.parentNode.insertAdjacentElement('afterend', feedbackEl);
            }
            
            feedbackEl.innerHTML = `
                <small class="text-${strengthClass}">Sicurezza: ${strengthText}</small>
                <div class="progress" style="height: 5px;">
                    <div class="progress-bar bg-${strengthClass}" style="width: ${strength * 25}%"></div>
                </div>
            `;
        });
    </script>
    <?php endif; ?>
</body>
</html>
