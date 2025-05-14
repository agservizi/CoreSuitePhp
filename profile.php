<?php
session_start();
require_once 'includes/header.php';
require_once 'auth.php';

$auth = new Auth();
$auth->requireLogin();

$db = Database::getInstance();
$userId = $_SESSION['user_id'];

// Ottieni i dati dell'utente corrente
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Controlla se l'utente ha già configurato il 2FA
$hasMfa = !empty($user['mfa_secret']);

// Gestione setup 2FA
$mfaQrCode = null;
$mfaSecret = null;
$mfaMessage = '';
$mfaSuccess = false;

// Richiesta di setup 2FA
if (isset($_POST['setup_mfa'])) {
    $setupResult = $auth->setupMfa($userId);
    
    if ($setupResult['success']) {
        $mfaSecret = $setupResult['secret'];
        $mfaQrCode = $setupResult['qrcode'];
    } else {
        $mfaMessage = $setupResult['message'];
    }
}

// Verifica codice 2FA per completare l'attivazione
if (isset($_POST['verify_mfa'])) {
    $code = $_POST['mfa_code'] ?? '';
    
    if (empty($code)) {
        $mfaMessage = 'Il codice è obbligatorio';
    } else {
        // Verifica che il codice sia valido
        $verifyResult = $auth->verifyMfa($code);
        
        if ($verifyResult['success']) {
            $mfaSuccess = true;
            $hasMfa = true;
            $mfaMessage = 'Autenticazione a due fattori attivata con successo!';
        } else {
            $mfaMessage = 'Codice non valido. Riprova.';
        }
    }
}

// Disabilita 2FA
if (isset($_POST['disable_mfa'])) {
    $code = $_POST['disable_code'] ?? '';
    
    if (empty($code)) {
        $mfaMessage = 'Il codice è obbligatorio per disabilitare il 2FA';
    } else {
        $disableResult = $auth->disableMfa($userId, $code);
        
        if ($disableResult['success']) {
            $hasMfa = false;
            $mfaSuccess = true;
            $mfaMessage = 'Autenticazione a due fattori disabilitata con successo!';
        } else {
            $mfaMessage = $disableResult['message'];
        }
    }
}

// Aggiornamento profilo
$profileMessage = '';
$profileSuccess = false;

if (isset($_POST['update_profile'])) {
    $firstName = $_POST['first_name'] ?? '';
    $lastName = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Controlla se l'email è già in uso da un altro utente
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $userId]);
    $emailExists = $stmt->fetchColumn() > 0;
    
    if ($emailExists) {
        $profileMessage = 'Email già in uso da un altro utente.';
    } else {
        $updateFields = [];
        $updateParams = [];
        
        if (!empty($firstName)) {
            $updateFields[] = "first_name = ?";
            $updateParams[] = $firstName;
        }
        
        if (!empty($lastName)) {
            $updateFields[] = "last_name = ?";
            $updateParams[] = $lastName;
        }
        
        if (!empty($email)) {
            $updateFields[] = "email = ?";
            $updateParams[] = $email;
        }
        
        // Aggiornamento password se richiesto
        if (!empty($newPassword)) {
            $config = require_once 'config/config.php';
            $minLength = $config['password_min_length'];
            
            if (strlen($newPassword) < $minLength) {
                $profileMessage = "La nuova password deve essere di almeno $minLength caratteri.";
            } elseif ($newPassword !== $confirmPassword) {
                $profileMessage = "Le password non coincidono.";
            } elseif (empty($currentPassword) || !password_verify($currentPassword, $user['password'])) {
                $profileMessage = "La password attuale non è corretta.";
            } else {
                $updateFields[] = "password = ?";
                $updateParams[] = password_hash($newPassword, PASSWORD_DEFAULT);
            }
        }
        
        // Se non ci sono errori, procedi con l'aggiornamento
        if (empty($profileMessage) && !empty($updateFields)) {
            $updateParams[] = $userId;
            
            $query = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE id = ?";
            $stmt = $db->prepare($query);
            
            if ($stmt->execute($updateParams)) {
                $profileSuccess = true;
                $profileMessage = "Profilo aggiornato con successo!";
                
                // Aggiorna la sessione se l'email è stata modificata
                if (!empty($email)) {
                    $_SESSION['user_email'] = $email;
                }
                
                // Aggiorna i dati dell'utente per la visualizzazione
                $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $profileMessage = "Si è verificato un errore durante l'aggiornamento del profilo.";
            }
        }
    }
}
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Gestione Profilo</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Profilo</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
        
            <!-- Container per le notifiche toast -->
            <div id="notifications-container" class="position-fixed bottom-0 end-0 p-3"></div>
            
            <div class="row">
                <div class="col-md-6">
                    <!-- Informazioni profilo -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Informazioni personali</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($profileMessage)): ?>
                                <div class="alert alert-<?php echo $profileSuccess ? 'success' : 'danger'; ?>">
                                    <?php echo $profileMessage; ?>
                                </div>
                            <?php endif; ?>
                            
                            <form action="profile.php" method="post">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="first_name">Nome</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="last_name">Cognome</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>">
                                </div>
                                
                                <hr>
                                <h5>Cambio password</h5>
                                <p class="text-muted small">Compila questi campi solo se desideri cambiare la password.</p>
                                
                                <div class="form-group">
                                    <label for="current_password">Password attuale</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password">
                                </div>
                                <div class="form-group">
                                    <label for="new_password">Nuova password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password">
                                </div>
                                <div class="form-group">
                                    <label for="confirm_password">Conferma nuova password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                </div>
                                
                                <button type="submit" name="update_profile" class="btn btn-primary">Aggiorna profilo</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <!-- Sicurezza e autenticazione a due fattori -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Sicurezza</h3>
                        </div>
                        <div class="card-body">
                            <h5>Autenticazione a due fattori (2FA)</h5>
                            
                            <?php if (!empty($mfaMessage)): ?>
                                <div class="alert alert-<?php echo $mfaSuccess ? 'success' : 'danger'; ?>">
                                    <?php echo $mfaMessage; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($hasMfa): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-check-circle"></i> L'autenticazione a due fattori è attualmente <strong>attiva</strong> sul tuo account.
                                </div>
                                
                                <p>L'autenticazione a due fattori aggiunge un livello di sicurezza aggiuntivo al tuo account richiedendo un codice generato dal tuo dispositivo oltre alla password.</p>
                                
                                <form action="profile.php" method="post" class="mt-4">
                                    <h6>Disattiva 2FA</h6>
                                    <div class="form-group">
                                        <label for="disable_code">Codice di verifica</label>
                                        <input type="text" class="form-control" id="disable_code" name="disable_code" placeholder="Inserisci il codice dalla tua app di autenticazione" required>
                                    </div>
                                    <button type="submit" name="disable_mfa" class="btn btn-danger">Disattiva 2FA</button>
                                </form>
                            <?php elseif (isset($mfaQrCode)): ?>
                                <p>Scansiona il codice QR con la tua app di autenticazione (Google Authenticator, Microsoft Authenticator, ecc.) o inserisci manualmente il codice:</p>
                                
                                <div class="text-center mb-3">
                                    <img src="<?php echo $mfaQrCode; ?>" alt="QR Code per 2FA" class="img-fluid border p-2">
                                </div>
                                
                                <div class="form-group">
                                    <label>Codice segreto (se non puoi scansionare il QR):</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="<?php echo $mfaSecret; ?>" readonly>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary copy-btn" type="button" data-clipboard-text="<?php echo $mfaSecret; ?>">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> <strong>Importante:</strong> Conserva questo codice in un luogo sicuro. Se perdi l'accesso alla tua app di autenticazione, avrai bisogno di questo codice per recuperare l'accesso.
                                </div>
                                
                                <form action="profile.php" method="post" class="mt-4">
                                    <div class="form-group">
                                        <label for="mfa_code">Codice di verifica</label>
                                        <input type="text" class="form-control" id="mfa_code" name="mfa_code" placeholder="Inserisci il codice dalla tua app di autenticazione" required>
                                    </div>
                                    <button type="submit" name="verify_mfa" class="btn btn-primary">Verifica e attiva 2FA</button>
                                </form>
                            <?php else: ?>
                                <p>L'autenticazione a due fattori aggiunge un livello di sicurezza aggiuntivo al tuo account richiedendo un codice generato dal tuo dispositivo oltre alla password.</p>
                                
                                <form action="profile.php" method="post" class="mt-3">
                                    <button type="submit" name="setup_mfa" class="btn btn-primary">Configura autenticazione a due fattori</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Accessi recenti -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Accessi recenti</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>IP</th>
                                        <th>Dispositivo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $db->prepare("
                                        SELECT * FROM login_logs 
                                        WHERE user_id = ? 
                                        ORDER BY created_at DESC 
                                        LIMIT 5
                                    ");
                                    $stmt->execute([$userId]);
                                    $loginLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    if (count($loginLogs) > 0):
                                        foreach ($loginLogs as $log):
                                            // Crea un parser per l'user agent
                                            $userAgent = $log['user_agent'];
                                            $device = "Sconosciuto";
                                            
                                            if (strpos($userAgent, 'iPhone') !== false) {
                                                $device = "iPhone";
                                            } elseif (strpos($userAgent, 'iPad') !== false) {
                                                $device = "iPad";
                                            } elseif (strpos($userAgent, 'Android') !== false) {
                                                $device = "Android";
                                            } elseif (strpos($userAgent, 'Windows') !== false) {
                                                $device = "Windows";
                                            } elseif (strpos($userAgent, 'Mac') !== false) {
                                                $device = "Mac";
                                            } elseif (strpos($userAgent, 'Linux') !== false) {
                                                $device = "Linux";
                                            }
                                    ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y H:i', strtotime($log['created_at'])); ?></td>
                                        <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                                        <td><?php echo htmlspecialchars($device); ?></td>
                                    </tr>
                                    <?php
                                        endforeach;
                                    else:
                                    ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Nessun accesso registrato</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>
<script>
    // Inizializza Clipboard.js
    var clipboard = new ClipboardJS('.copy-btn');
    
    clipboard.on('success', function(e) {
        // Cambia temporaneamente l'icona per dare feedback
        var button = e.trigger;
        var originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i>';
        
        setTimeout(function() {
            button.innerHTML = originalHTML;
        }, 1500);
        
        e.clearSelection();
    });
    
    // Validazione password
    document.getElementById('new_password')?.addEventListener('input', function() {
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
        if (!feedbackEl && password.length > 0) {
            feedbackEl = document.createElement('div');
            feedbackEl.id = 'password-strength';
            feedbackEl.className = 'mt-2 mb-3';
            this.parentNode.insertAdjacentElement('beforeend', feedbackEl);
        }
        
        if (feedbackEl && password.length > 0) {
            feedbackEl.innerHTML = `
                <small class="text-${strengthClass}">Sicurezza: ${strengthText}</small>
                <div class="progress" style="height: 5px;">
                    <div class="progress-bar bg-${strengthClass}" style="width: ${strength * 25}%"></div>
                </div>
            `;
        } else if (feedbackEl) {
            feedbackEl.innerHTML = '';
        }
    });
</script>
