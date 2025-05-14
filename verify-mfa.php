<?php
require_once 'auth.php';

// Controlla se è una richiesta AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $auth = new Auth();
    $code = $_POST['mfa_code'] ?? '';
    
    if (empty($code)) {
        echo json_encode([
            'success' => false,
            'message' => 'Codice mancante'
        ]);
        exit;
    }
    
    $result = $auth->verifyMfa($code);
    echo json_encode($result);
    exit;
}

// Reindirizza al login se non c'è un utente in attesa di verifica MFA
if (!isset($_SESSION['pending_user_id'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreSuite - Verifica 2FA</title>
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
                <p class="login-box-msg">Verifica autenticazione a due fattori</p>
                
                <div id="error-message" class="alert alert-danger" style="display: none;"></div>
                
                <form action="verify-mfa.php" method="post" id="mfaForm">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="mfa_code" id="mfa_code" placeholder="Codice 2FA" required autofocus>
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
                
                <p class="mt-3 mb-1">
                    <a href="logout.php">Annulla e torna al login</a>
                </p>
                
                <div class="mt-4">
                    <div class="callout callout-info">
                        <p>Apri la tua app di autenticazione (Google Authenticator, Microsoft Authenticator, ecc.) e inserisci il codice a 6 cifre mostrato per CoreSuite.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <script>
        // Gestione timer per countdown
        function startCountdown() {
            var seconds = 30;
            var timerEl = document.createElement('div');
            timerEl.className = 'text-center mt-3';
            timerEl.innerHTML = `<small class="text-muted">Il codice verrà aggiornato tra <span id="countdown">${seconds}</span> secondi</small>`;
            document.querySelector('#mfaForm').insertAdjacentElement('afterend', timerEl);
            
            var interval = setInterval(function() {
                seconds--;
                document.getElementById('countdown').innerText = seconds;
                
                if (seconds <= 0) {
                    clearInterval(interval);
                    timerEl.innerHTML = `<small class="text-success">Nuovo codice disponibile nell'app</small>`;
                    setTimeout(function() {
                        startCountdown();
                    }, 2000);
                }
            }, 1000);
        }
        
        // Avvia il countdown
        startCountdown();
        
        // Gestione invio form
        document.getElementById('mfaForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Disabilita il pulsante durante la verifica
            var submitBtn = this.querySelector('button[type="submit"]');
            var originalText = submitBtn.innerText;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Verifica in corso...';
            
            // Nascondi eventuali messaggi di errore precedenti
            document.getElementById('error-message').style.display = 'none';
            
            // Invia la richiesta
            var formData = new FormData(this);
            
            fetch('verify-mfa.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reindirizza alla dashboard
                    window.location.href = 'index.php';
                } else {
                    // Mostra errore
                    var errorEl = document.getElementById('error-message');
                    errorEl.innerText = data.message || 'Codice non valido. Riprova.';
                    errorEl.style.display = 'block';
                    
                    // Resetta il form
                    document.getElementById('mfa_code').value = '';
                    document.getElementById('mfa_code').focus();
                    
                    // Riattiva il pulsante
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                // Mostra errore generico
                var errorEl = document.getElementById('error-message');
                errorEl.innerText = 'Si è verificato un errore. Riprova più tardi.';
                errorEl.style.display = 'block';
                
                // Riattiva il pulsante
                submitBtn.disabled = false;
                submitBtn.innerText = originalText;
            });
        });
    </script>
</body>
</html>
