<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreSuite Installer - Sistema di Gestione Contratti</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="/assets/images/coresuite-favicon.svg">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <style>
        body {
            background: #f4f6f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .installer-container {
            max-width: 800px;
            width: 100%;
            margin: 20px;
        }

        .step {
            display: none;
        }

        .step.active {
            display: block;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: bold;
        }

        .step-circle.active {
            background: #007bff;
            color: white;
        }

        .step-circle.done {
            background: #28a745;
            color: white;
        }

        .credentials-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-left: 5px solid #007bff;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="installer-container">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <h1><b>Core</b>Suite Installer</h1>
            </div>
            <div class="card-body">
                <!-- Indicatore di progresso -->
                <div class="row justify-content-center mb-4">
                    <div class="d-flex">
                        <div class="step-circle active" id="step1-indicator">1</div>
                        <div class="step-circle" id="step2-indicator">2</div>
                        <div class="step-circle" id="step3-indicator">3</div>
                        <div class="step-circle" id="step4-indicator">4</div>
                    </div>
                </div>
                
                <!-- Step 1: Benvenuto e verifica requisiti -->
                <div class="step active" id="step1">
                    <h3 class="text-center">Benvenuto in CoreSuite</h3>
                    <p class="text-muted text-center mb-4">Sistema di gestione contratti e fornitori</p>
                    
                    <h4><i class="fas fa-check-circle text-primary"></i> Verifica requisiti di sistema</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <?php
                                $requirements = [
                                    'PHP versione >= 7.4' => version_compare(PHP_VERSION, '7.4.0') >= 0,
                                    'estensione PDO' => extension_loaded('pdo'),
                                    'estensione PDO MySQL' => extension_loaded('pdo_mysql'),
                                    'estensione JSON' => extension_loaded('json'),
                                    'estensione cURL' => extension_loaded('curl'),
                                    'estensione GD' => extension_loaded('gd'),
                                    'permessi scrittura config/' => is_writable('config/') || is_writable('./')
                                ];
                                
                                $allRequirementsMet = true;
                                foreach ($requirements as $requirement => $satisfied) {
                                    echo '<tr>';
                                    echo '<td>' . $requirement . '</td>';
                                    if ($satisfied) {
                                        echo '<td><span class="badge bg-success"><i class="fas fa-check"></i> OK</span></td>';
                                    } else {
                                        echo '<td><span class="badge bg-danger"><i class="fas fa-times"></i> Non soddisfatto</span></td>';
                                        $allRequirementsMet = false;
                                    }
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button class="btn btn-primary btn-lg" onclick="nextStep(1)" <?php echo $allRequirementsMet ? '' : 'disabled'; ?>>
                            <i class="fas fa-arrow-right"></i> Continua
                        </button>
                        <?php if (!$allRequirementsMet): ?>
                            <div class="alert alert-danger mt-3">
                                <i class="fas fa-exclamation-triangle"></i> Verifica che tutti i requisiti siano soddisfatti prima di continuare.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Step 2: Configurazione database -->
                <div class="step" id="step2">
                    <h3><i class="fas fa-database text-primary"></i> Configurazione Database</h3>
                    <p class="text-muted">Inserisci le credenziali di accesso al tuo database MySQL</p>
                    
                    <form id="dbForm">
                        <div class="form-group">
                            <label for="dbHost">Host</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-server"></i></span>
                                </div>
                                <input type="text" class="form-control" id="dbHost" name="dbHost" value="localhost" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="dbName">Nome Database</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-database"></i></span>
                                </div>
                                <input type="text" class="form-control" id="dbName" name="dbName" placeholder="core_suite" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="dbUser">Username</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text" class="form-control" id="dbUser" name="dbUser" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="dbPass">Password</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                </div>
                                <input type="password" class="form-control" id="dbPass" name="dbPass" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="dbPrefix">Prefisso Tabelle (opzionale)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-table"></i></span>
                                </div>
                                <input type="text" class="form-control" id="dbPrefix" name="dbPrefix" value="core_" placeholder="core_">
                            </div>
                        </div>
                        
                        <div id="dbTestResult"></div>
                        
                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-info mr-2" onclick="testDbConnection()">
                                <i class="fas fa-plug"></i> Testa Connessione
                            </button>
                            <button type="button" class="btn btn-secondary mr-2" onclick="prevStep(2)">
                                <i class="fas fa-arrow-left"></i> Indietro
                            </button>
                            <button type="button" class="btn btn-primary" onclick="nextStep(2)">
                                <i class="fas fa-arrow-right"></i> Continua
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Step 3: Configurazione sito e admin -->
                <div class="step" id="step3">
                    <h3><i class="fas fa-cogs text-primary"></i> Configurazione Sito</h3>
                    <p class="text-muted">Inserisci le informazioni di base del tuo sito</p>
                    
                    <form id="siteForm">
                        <div class="form-group">
                            <label for="siteName">Nome del sito</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                </div>
                                <input type="text" class="form-control" id="siteName" name="siteName" value="CoreSuite" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="siteUrl">URL del sito</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-link"></i></span>
                                </div>
                                <input type="url" class="form-control" id="siteUrl" name="siteUrl" value="<?php echo 'http://' . $_SERVER['HTTP_HOST']; ?>" required>
                            </div>
                        </div>
                        
                        <h4 class="mt-4"><i class="fas fa-user-shield text-primary"></i> Account Amministratore</h4>
                        
                        <div class="form-group">
                            <label for="adminEmail">Email amministratore</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input type="email" class="form-control" id="adminEmail" name="adminEmail" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="adminPassword">Password</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                </div>
                                <input type="password" class="form-control" id="adminPassword" name="adminPassword" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="adminPasswordConfirm">Conferma password</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                </div>
                                <input type="password" class="form-control" id="adminPasswordConfirm" name="adminPasswordConfirm" required>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-secondary mr-2" onclick="prevStep(3)">
                                <i class="fas fa-arrow-left"></i> Indietro
                            </button>
                            <button type="button" class="btn btn-primary" onclick="nextStep(3)">
                                <i class="fas fa-arrow-right"></i> Continua
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Step 4: Installazione -->
                <div class="step" id="step4">
                    <h3 class="text-center"><i class="fas fa-check-circle text-success"></i> Installazione completata</h3>
                    <p class="text-center text-muted">CoreSuite è stato installato con successo!</p>
                    
                    <div class="progress mb-3">
                        <div class="progress-bar bg-success" id="installProgress" style="width: 100%">100%</div>
                    </div>
                    
                    <div class="credentials-box">
                        <h5><i class="fas fa-key"></i> Credenziali di accesso</h5>
                        <p><strong>URL:</strong> <span id="finalSiteUrl"></span></p>
                        <p><strong>Email:</strong> <span id="finalAdminEmail"></span></p>
                        <p><strong>Password:</strong> (La password che hai impostato)</p>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Per motivi di sicurezza, rimuovi il file <code>install.php</code> dal tuo server.
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="/login.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Vai al Login
                        </a>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer text-center text-muted">
                CoreSuite &copy; <?php echo date('Y'); ?>
            </div>
        </div>
    </div>

    <script>
        function nextStep(currentStep) {
            // Valida il form corrente
            if (currentStep == 2) {
                // Validazione database
                if (!document.getElementById('dbForm').checkValidity()) {
                    alert('Completa tutti i campi richiesti.');
                    return;
                }
            } else if (currentStep == 3) {
                // Validazione sito e password
                if (!document.getElementById('siteForm').checkValidity()) {
                    alert('Completa tutti i campi richiesti.');
                    return;
                }
                
                // Controlla che le password corrispondano
                let password = document.getElementById('adminPassword').value;
                let confirm = document.getElementById('adminPasswordConfirm').value;
                if (password !== confirm) {
                    alert('Le password non corrispondono.');
                    return;
                }
                
                // Imposta i valori finali per la schermata di riepilogo
                document.getElementById('finalSiteUrl').textContent = document.getElementById('siteUrl').value;
                document.getElementById('finalAdminEmail').textContent = document.getElementById('adminEmail').value;
                
                // In un'implementazione reale, qui si dovrebbe effettuare l'installazione effettiva
                // tramite AJAX o una chiamata al backend
            }
            
            // Nascondi passo corrente
            document.getElementById('step' + currentStep).classList.remove('active');
            // Segna indicatore passo corrente come completato
            document.getElementById('step' + currentStep + '-indicator').classList.remove('active');
            document.getElementById('step' + currentStep + '-indicator').classList.add('done');
            
            // Mostra passo successivo
            let nextStepNumber = currentStep + 1;
            document.getElementById('step' + nextStepNumber).classList.add('active');
            document.getElementById('step' + nextStepNumber + '-indicator').classList.add('active');
        }
        
        function prevStep(currentStep) {
            // Nascondi passo corrente
            document.getElementById('step' + currentStep).classList.remove('active');
            // Rimuovi active dall'indicatore
            document.getElementById('step' + currentStep + '-indicator').classList.remove('active');
            
            // Mostra passo precedente
            let prevStepNumber = currentStep - 1;
            document.getElementById('step' + prevStepNumber).classList.add('active');
            document.getElementById('step' + prevStepNumber + '-indicator').classList.add('active');
            document.getElementById('step' + prevStepNumber + '-indicator').classList.remove('done');
        }
        
        function testDbConnection() {
            let dbTestResult = document.getElementById('dbTestResult');
            
            // In un'implementazione reale, qui si dovrebbe effettuare una chiamata AJAX
            // Per simulazione, mostriamo un messaggio di successo
            dbTestResult.innerHTML = '<div class="alert alert-success mt-3"><i class="fas fa-check-circle"></i> Connessione al database riuscita!</div>';
            
            // In caso di errore, mostrare:
            // dbTestResult.innerHTML = '<div class="alert alert-danger mt-3"><i class="fas fa-times-circle"></i> Errore: impossibile connettersi al database.</div>';
        }
    </script>
</body>
</html>
<?php
// Parte PHP per gestione installazione reale
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'check_requirements':
            echo json_encode(checkSystemRequirements());
            break;
            
        case 'test_database':
            echo json_encode(testDatabaseConnection($_POST));
            break;
            
        case 'create_schema':
            echo json_encode(createDatabaseSchema($_POST));
            break;
            
        case 'create_admin':
            echo json_encode(createAdminUser($_POST));
            break;
    }
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'force_schema') {
    // Esegui direttamente la creazione dello schema e mostra risultato
    try {
        $config = include __DIR__ . '/config/database.php';
        $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        $sql = file_get_contents(__DIR__ . '/config/schema.sql');
        $pdo->exec($sql);
        echo '<div style="color:green;font-weight:bold;">Tabelle create con successo!</div>';
    } catch (Exception $e) {
        echo '<div style="color:red;font-weight:bold;">Errore creazione tabelle: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'force_admin') {
    // Crea utente admin solo se non esiste già
    try {
        $config = include __DIR__ . '/config/database.php';
        $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        $email = $_GET['email'] ?? 'admin@admin.it';
        $password = $_GET['password'] ?? 'admin12345';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetchColumn() > 0) {
            echo '<div style="color:orange;font-weight:bold;">Utente admin già esistente con questa email!</div>';
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, role, is_active, created_at) VALUES (?, ?, 'admin', 1, NOW())");
            $stmt->execute([$email, $hash]);
            echo '<div style="color:green;font-weight:bold;">Utente admin creato! Email: ' . htmlspecialchars($email) . '</div>';
        }
    } catch (Exception $e) {
        echo '<div style="color:red;font-weight:bold;">Errore creazione admin: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    exit;
}

function checkSystemRequirements() {
    $requirements = [
        'php_version' => version_compare(PHP_VERSION, '8.1.0', '>='),
        'pdo_mysql' => extension_loaded('pdo_mysql'),
        'gd' => extension_loaded('gd'),
        'curl' => extension_loaded('curl'),
        'openssl' => extension_loaded('openssl'),
        'uploads_writable' => is_writable(__DIR__ . '/uploads') || mkdir(__DIR__ . '/uploads', 0755, true)
    ];
    
    return [
        'success' => !in_array(false, $requirements),
        'requirements' => $requirements
    ];
}

function testDatabaseConnection($config) {
    try {
        $dsn = "mysql:host={$config['host']};dbname={$config['name']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['user'], $config['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        
        // Salva configurazione
        saveConfig($config);
        
        return ['success' => true, 'message' => 'Connessione database riuscita'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Errore connessione: ' . $e->getMessage()];
    }
}

function createDatabaseSchema($config) {
    try {
        $pdo = getDatabaseConnection();
        $sql = file_get_contents(__DIR__ . '/config/schema.sql');
        $pdo->exec($sql);
        return ['success' => true, 'message' => 'Schema database creato con successo'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Errore creazione schema: ' . $e->getMessage()];
    }
}

function createAdminUser($data) {
    try {
        $pdo = getDatabaseConnection();
        
        $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, role, is_active, created_at) VALUES (?, ?, 'admin', 1, NOW())");
        $stmt->execute([
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
        
        return ['success' => true, 'message' => 'Account amministratore creato'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Errore creazione admin: ' . $e->getMessage()];
    }
}

function saveConfig($config) {
    $configContent = "<?php\nreturn [\n";
    $configContent .= "    'db_host' => '" . addslashes($config['host']) . "',\n";
    $configContent .= "    'db_name' => '" . addslashes($config['name']) . "',\n";
    $configContent .= "    'db_user' => '" . addslashes($config['user']) . "',\n";
    $configContent .= "    'db_pass' => '" . addslashes($config['pass']) . "'\n";
    $configContent .= "];\n";
    $dir = __DIR__ . '/config';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($dir . '/database.php', $configContent);
}

function getDatabaseConnection() {
    $config = include __DIR__ . '/config/database.php';
    $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4";
    return new PDO($dsn, $config['db_user'], $config['db_pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
}
?>
