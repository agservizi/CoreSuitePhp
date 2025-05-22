<?php
// install.php semplificato per CoreSuite
// Mostra solo una pagina di benvenuto e istruzioni base
?><!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installazione CoreSuite</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body text-center">
                        <h2 class="mb-4">Benvenuto nell'installazione di <span class="text-primary">CoreSuite</span></h2>
                        <p class="lead">Per proseguire, assicurati che il database sia già stato creato e che i parametri di connessione siano corretti nel file <code>config/database.php</code>.</p>
                        <p>Se il database è pronto, puoi eliminare questo file <code>install.php</code> per motivi di sicurezza e accedere direttamente al <a href="login.php">login</a>.</p>
                        <hr>
                        <p class="text-muted">Per installazioni avanzate, consulta la documentazione ufficiale.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
