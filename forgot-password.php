<?php
require_once 'auth.php';
require_once 'services/EmailService.php';

$auth = new Auth();
$emailService = new EmailService();
$message = '';
$showSuccessMessage = false;

// Gestione invio richiesta recupero password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Verifica se l'email esiste nel sistema
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $userExists = $stmt->fetchColumn() > 0;
        
        if ($userExists) {
            // Crea token e salva nel database
            $resetResult = $auth->resetPassword($email);
            
            // Se il token è stato creato, ottienilo dal DB per inviare email
            if ($resetResult) {
                $stmt = $db->prepare("SELECT token FROM password_resets WHERE email = ? AND used = 0 ORDER BY created_at DESC LIMIT 1");
                $stmt->execute([$email]);
                $resetData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($resetData && $resetData['token']) {
                    // Invia email
                    $emailSent = $emailService->sendPasswordResetEmail($email, $resetData['token']);
                    
                    if ($emailSent) {
                        $showSuccessMessage = true;
                    } else {
                        $message = "Errore durante l'invio dell'email. Riprova più tardi.";
                    }
                }
            } else {
                $message = "Si è verificato un errore. Riprova più tardi.";
            }
        } else {
            // Non rivelare se l'email esiste per sicurezza
            $showSuccessMessage = true;
        }
    } else {
        $message = "Formato email non valido.";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreSuite - Recupera password</title>
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
                <?php if ($showSuccessMessage): ?>
                    <div class="alert alert-success">
                        <h5><i class="icon fas fa-check"></i> Email inviata!</h5>
                        Se l'indirizzo email fornito è associato a un account, riceverai a breve le istruzioni per reimpostare la password.
                    </div>
                    <p class="mt-3 text-center">
                        <a href="login.php" class="btn btn-primary">Torna al login</a>
                    </p>
                <?php else: ?>
                    <p class="login-box-msg">Hai dimenticato la password? Inserisci il tuo indirizzo email e ti invieremo le istruzioni per reimpostarla.</p>
                    
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-danger">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <form action="forgot-password.php" method="post">
                        <div class="input-group mb-3">
                            <input type="email" class="form-control" name="email" placeholder="Email" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-block">Invia link di recupero</button>
                            </div>
                        </div>
                    </form>
                    
                    <p class="mt-3 mb-1">
                        <a href="login.php">Torna al login</a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
