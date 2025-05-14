<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreSuite - Installazione</title>
    <link rel="icon" href="assets/images/favicon.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .setup-step {
            display: none;
        }
        .setup-step.active {
            display: block;
        }
        .setup-header {
            text-align: center;
            padding: 2rem 0;
        }
        .progress-indicator {
            margin: 2rem 0;
        }
    </style>
</head>
<body class="hold-transition">
    <div class="wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card mt-5">
                        <div class="card-body">
                            <div class="setup-header">
                                <img src="assets/images/logo.png" alt="CoreSuite Logo" style="max-width: 150px; margin-bottom: 1rem;">
                                <h2><span class="text-primary font-weight-bold">Core</span><span class="font-weight-light">Suite</span></h2>
                                <h3>Installazione</h3>
                            </div>

                            <div class="progress progress-indicator">
                                <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>

                            <!-- Step 1: Verifica Requisiti -->
                            <div class="setup-step active" id="step1">
                                <h4>Step 1: Verifica dei Requisiti</h4>
                                <div class="requirements-check">
                                    <?php
                                    $requirements = [
                                        'PHP Version' => ['required' => '7.4', 'current' => PHP_VERSION],
                                        'PDO Extension' => ['required' => true, 'current' => extension_loaded('pdo')],
                                        'MySQL Extension' => ['required' => true, 'current' => extension_loaded('pdo_mysql')],
                                        'GD Extension' => ['required' => true, 'current' => extension_loaded('gd')],
                                        'Writeable Directory' => ['required' => true, 'current' => is_writable(__DIR__)]
                                    ];

                                    foreach ($requirements as $name => $requirement): ?>
                                    <div class="requirement-item d-flex justify-content-between align-items-center mb-3">
                                        <span><?php echo $name; ?></span>
                                        <span class="badge <?php echo $requirement['current'] ? 'badge-success' : 'badge-danger'; ?>">
                                            <?php echo $requirement['current'] ? 'OK' : 'Non soddisfatto'; ?>
                                        </span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <button class="btn btn-primary float-right next-step">Continua</button>
                            </div>

                            <!-- Step 2: Configurazione Database -->
                            <div class="setup-step" id="step2">
                                <h4>Step 2: Configurazione Database</h4>
                                <form id="dbConfigForm" class="mt-4">
                                    <div class="form-group">
                                        <label>Host Database</label>
                                        <input type="text" class="form-control" name="db_host" value="127.0.0.1:3306" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Nome Database</label>
                                        <input type="text" class="form-control" name="db_name" value="u427445037_coresuite" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Utente Database</label>
                                        <input type="text" class="form-control" name="db_user" value="u427445037_coresuite" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Password Database</label>
                                        <input type="password" class="form-control" name="db_password" value="Giogiu2123@" required>
                                    </div>
                                    <button type="button" class="btn btn-info test-connection">Testa Connessione</button>
                                    <button type="button" class="btn btn-primary float-right next-step">Continua</button>
                                </form>
                            </div>

                            <!-- Step 3: Creazione Tabelle -->
                            <div class="setup-step" id="step3">
                                <h4>Step 3: Creazione Struttura Database</h4>
                                <div class="progress mt-4">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"></div>
                                </div>
                                <div class="mt-4" id="installationLog"></div>
                                <button type="button" class="btn btn-primary float-right next-step" disabled>Continua</button>
                            </div>

                            <!-- Step 4: Completamento -->
                            <div class="setup-step" id="step4">
                                <div class="text-center">
                                    <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                                    <h4 class="mt-4">Installazione Completata!</h4>
                                    <p>CoreSuite è stato installato con successo.</p>
                                    <a href="index.php" class="btn btn-success">Vai all'Applicazione</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            let currentStep = 1;
            const totalSteps = 4;

            function updateProgress() {
                const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
                $('.progress-indicator .progress-bar').css('width', progress + '%');
            }

            function showStep(step) {
                $('.setup-step').removeClass('active');
                $('#step' + step).addClass('active');
                currentStep = step;
                updateProgress();
            }

            // Verifica dei requisiti
            $('.next-step').click(function() {
                if (currentStep < totalSteps) {
                    // Se stiamo passando dallo step 2 allo step 3, inizializza il database
                    if (currentStep === 2) {
                        initDatabase();
                    } else {
                        showStep(currentStep + 1);
                    }
                }
            });

            // Test connessione database
            $('.test-connection').click(function() {
                const formData = $('#dbConfigForm').serialize();
                $.post('ajax/test_connection.php', formData, function(response) {
                    if (response.success) {
                        alert('Connessione al database riuscita!');
                    } else {
                        alert('Errore di connessione: ' + response.message);
                    }
                });
            });

            // Miglioramento della gestione degli errori e aggiunta di log dettagliati
            function initDatabase() {
                const dbConfig = $('#dbConfigForm').serialize();

                // Aggiorna UI per mostrare progresso
                $('#installationLog').html('<p>Inizializzazione database in corso...</p>');
                $('.progress-bar').css('width', '25%');

                $.post('ajax/init_database.php', { action: 'init_database' }, function(response) {
                    console.log('Risposta ricevuta da init_database.php:', response);
                    if (response.success) {
                        $('#installationLog').append('<p class="text-success">Database inizializzato con successo</p>');
                        $('.progress-bar').css('width', '100%');

                        // Attiva il pulsante per continuare
                        $('.next-step').prop('disabled', false);

                        // Procedi allo step successivo
                        setTimeout(function() {
                            showStep(currentStep + 1);
                        }, 1000);
                    } else {
                        $('#installationLog').append('<p class="text-danger">Errore: ' + response.message + '</p>');
                        console.error('Errore durante l\'inizializzazione del database:', response.message);
                    }
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    $('#installationLog').append('<p class="text-danger">Errore AJAX: ' + textStatus + '</p>');
                    console.error('Errore AJAX:', textStatus, errorThrown);
                });
            }

            // Creazione utente amministratore
            $('.create-admin').click(function() {
                // Verifica che le password corrispondano
                const password = $('#admin_password').val();
                const confirmPassword = $('#admin_password_confirm').val();
                
                if (password !== confirmPassword) {
                    alert('Le password non corrispondono');
                    return;
                }
                
                // Prepara i dati per la richiesta
                const adminData = {
                    action: 'create_admin',
                    email: $('#admin_email').val(),
                    password: password,
                    first_name: $('#admin_first_name').val(),
                    last_name: $('#admin_last_name').val()
                };
                
                $.post('ajax/init_database.php', adminData, function(response) {
                    if (response.success) {
                        alert('Utente amministratore creato con successo');
                        
                        // Se è stata selezionata l'opzione per creare dati di esempio
                        if ($('#create_sample_data').is(':checked')) {
                            createSampleData();
                        } else {
                            // Nascondi il pulsante di creazione e mostra il pulsante avanti
                            $('.create-admin').hide();
                            $('.next-step').show();
                        }
                    } else {
                        alert('Errore: ' + response.message);
                    }
                });
            });

            // Creazione dati di esempio
            function createSampleData() {
                $.post('ajax/init_database.php', { action: 'init_sample_data' }, function(response) {
                    if (response.success) {
                        // Mostra la riga dei dati di esempio
                        $('#sample_data_status').show();
                        
                        // Nascondi il pulsante di creazione e mostra il pulsante avanti
                        $('.create-admin').hide();
                        $('.next-step').show();
                        
                        alert('Dati di esempio creati con successo');
                    } else {
                        alert('Errore nella creazione dei dati di esempio: ' + response.message);
                    }
                });
            }

            // Inizializza il primo step
            updateProgress();
        });
    </script>
</body>
</html>
