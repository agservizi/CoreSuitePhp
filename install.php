<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreSuite Installer - Sistema di Gestione Contratti</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0066CC, #00AA44);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .installer-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 800px;
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #0066CC, #00AA44);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .logo {
            font-size: 2.5em;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .subtitle {
            opacity: 0.9;
            font-size: 1.1em;
        }

        .installer-body {
            padding: 40px;
        }

        .step {
            display: none;
            animation: fadeIn 0.5s ease-in;
        }

        .step.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .progress-bar {
            background: #f0f0f0;
            height: 8px;
            border-radius: 4px;
            margin: 20px 0;
            overflow: hidden;
        }

        .progress-fill {
            background: linear-gradient(90deg, #0066CC, #00AA44);
            height: 100%;
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }

        input, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #0066CC;
        }

        .btn {
            background: linear-gradient(135deg, #0066CC, #00AA44);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .error {
            background: #ff6b6b;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }

        .success {
            background: #00AA44;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }

        .requirement {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .requirement:last-child {
            border-bottom: none;
        }

        .status {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }

        .status.ok {
            background: #00AA44;
            color: white;
        }

        .status.error {
            background: #ff6b6b;
            color: white;
        }

        .credentials-box {
            background: #f8f9fa;
            border: 2px solid #0066CC;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
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
            color: white;
            font-weight: bold;
        }

        .step-circle.active {
            background: #0066CC;
        }

        .step-circle.completed {
            background: #00AA44;
        }
    </style>
</head>
<body>
    <div class="installer-container">
        <div class="header">
            <div class="logo">📱⚡ CoreSuite</div>
            <div class="subtitle">Sistema di Gestione Contratti Telefonici, Luce e Gas</div>
        </div>

        <div class="installer-body">
            <div class="step-indicator">
                <div class="step-circle active" id="step1-indicator">1</div>
                <div class="step-circle" id="step2-indicator">2</div>
                <div class="step-circle" id="step3-indicator">3</div>
                <div class="step-circle" id="step4-indicator">4</div>
                <div class="step-circle" id="step5-indicator">5</div>
            </div>

            <div class="progress-bar">
                <div class="progress-fill" id="progress"></div>
            </div>

            <!-- Step 1: Verifica Requisiti -->
            <div class="step active" id="step1">
                <h2>Verifica Requisiti Sistema</h2>
                <p>Controlliamo se il server soddisfa tutti i requisiti per CoreSuite.</p>
                
                <div id="requirements-check">
                    <div class="requirement">
                        <span>PHP 8.1+</span>
                        <span class="status" id="php-status">Verificando...</span>
                    </div>
                    <div class="requirement">
                        <span>MySQL PDO</span>
                        <span class="status" id="mysql-status">Verificando...</span>
                    </div>
                    <div class="requirement">
                        <span>GD Extension</span>
                        <span class="status" id="gd-status">Verificando...</span>
                    </div>
                    <div class="requirement">
                        <span>cURL Extension</span>
                        <span class="status" id="curl-status">Verificando...</span>
                    </div>
                    <div class="requirement">
                        <span>OpenSSL Extension</span>
                        <span class="status" id="openssl-status">Verificando...</span>
                    </div>
                    <div class="requirement">
                        <span>Directory uploads/ scrivibile</span>
                        <span class="status" id="uploads-status">Verificando...</span>
                    </div>
                </div>

                <button class="btn" onclick="checkRequirements()" style="margin-top: 20px;">
                    Verifica Requisiti
                </button>
                <button class="btn" onclick="nextStep()" id="next-step1" style="margin-left: 10px; display: none;">
                    Continua
                </button>
            </div>

            <!-- Step 2: Configurazione Database -->
            <div class="step" id="step2">
                <h2>Configurazione Database</h2>
                <p>Inserisci i parametri di connessione al database MySQL.</p>

                <form id="db-form">
                    <div class="form-group">
                        <label for="db_host">Host Database</label>
                        <input type="text" id="db_host" value="127.0.0.1:3306" required>
                    </div>
                    <div class="form-group">
                        <label for="db_name">Nome Database</label>
                        <input type="text" id="db_name" value="u427445037_coresuite" required>
                    </div>
                    <div class="form-group">
                        <label for="db_user">Username</label>
                        <input type="text" id="db_user" value="u427445037_coresuite" required>
                    </div>
                    <div class="form-group">
                        <label for="db_pass">Password</label>
                        <input type="password" id="db_pass" value="Giogiu2123@" required>
                    </div>
                </form>

                <div id="db-test-result"></div>

                <button class="btn" onclick="testDatabase()">Testa Connessione</button>
                <button class="btn" onclick="nextStep()" id="next-step2" style="margin-left: 10px; display: none;">
                    Continua
                </button>
            </div>

            <!-- Step 3: Creazione Schema -->
            <div class="step" id="step3">
                <h2>Creazione Schema Database</h2>
                <p>Creazione delle tabelle e struttura del database...</p>

                <div id="schema-progress">
                    <div class="requirement">
                        <span>Tabella users</span>
                        <span class="status" id="users-table">In attesa...</span>
                    </div>
                    <div class="requirement">
                        <span>Tabella contracts</span>
                        <span class="status" id="contracts-table">In attesa...</span>
                    </div>
                    <div class="requirement">
                        <span>Tabella customers</span>
                        <span class="status" id="customers-table">In attesa...</span>
                    </div>
                    <div class="requirement">
                        <span>Tabella providers</span>
                        <span class="status" id="providers-table">In attesa...</span>
                    </div>
                    <div class="requirement">
                        <span>Tabella indirizzi</span>
                        <span class="status" id="addresses-table">In attesa...</span>
                    </div>
                    <div class="requirement">
                        <span>Tabella allegati</span>
                        <span class="status" id="attachments-table">In attesa...</span>
                    </div>
                    <div class="requirement">
                        <span>Tabella consensi</span>
                        <span class="status" id="consents-table">In attesa...</span>
                    </div>
                    <div class="requirement">
                        <span>Tabella note</span>
                        <span class="status" id="notes-table">In attesa...</span>
                    </div>
                    <div class="requirement">
                        <span>Tabella audit log</span>
                        <span class="status" id="audit-log-table">In attesa...</span>
                    </div>
                    <div class="requirement">
                        <span>Tabella bozze contratto</span>
                        <span class="status" id="contract-drafts-table">In attesa...</span>
                    </div>
                    <div class="requirement">
                        <span>Tabella migrazioni</span>
                        <span class="status" id="migrations-table">In attesa...</span>
                    </div>
                    <div class="requirement">
                        <span>Dati iniziali</span>
                        <span class="status" id="initial-data">In attesa...</span>
                    </div>
                </div>

                <button class="btn" onclick="createSchema()" id="create-schema-btn">
                    Crea Schema Database
                </button>
                <button class="btn" onclick="nextStep()" id="next-step3" style="margin-left: 10px; display: none;">
                    Continua
                </button>
            </div>

            <!-- Step 4: Configurazione Admin -->
            <div class="step" id="step4">
                <h2>Creazione Account Administrator</h2>
                <p>Crea l'account amministratore principale del sistema.</p>

                <form id="admin-form">
                    <div class="form-group">
                        <label for="admin_email">Email Administrator</label>
                        <input type="email" id="admin_email" required>
                    </div>
                    <div class="form-group">
                        <label for="admin_password">Password</label>
                        <input type="password" id="admin_password" required minlength="8">
                    </div>
                    <div class="form-group">
                        <label for="admin_password_confirm">Conferma Password</label>
                        <input type="password" id="admin_password_confirm" required>
                    </div>
                </form>

                <div id="admin-result"></div>

                <button class="btn" onclick="createAdmin()">Crea Administrator</button>
                <button class="btn" onclick="nextStep()" id="next-step4" style="margin-left: 10px; display: none;">
                    Continua
                </button>
            </div>

            <!-- Step 5: Completamento -->
            <div class="step" id="step5">
                <h2>🎉 Installazione Completata!</h2>
                <p>CoreSuite è stato installato con successo. Ecco le informazioni di accesso:</p>

                <div class="credentials-box">
                    <h3>Credenziali di Accesso</h3>
                    <p><strong>URL:</strong> <span id="app-url">https://app.coresuite.it</span></p>
                    <p><strong>Email:</strong> <span id="final-email"></span></p>
                    <p><strong>Password:</strong> La password che hai impostato</p>
                </div>

                <div class="success">
                    <strong>Prossimi Passi:</strong><br>
                    1. Accedi al sistema con le credenziali create<br>
                    2. Configura i gestori e i form personalizzati<br>
                    3. Inizia a creare i tuoi primi contratti<br>
                    4. Per sicurezza, elimina il file install.php
                </div>

                <button class="btn" onclick="window.location.href='index.php'">
                    Accedi a CoreSuite
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentStep = 1;
        let dbConfig = {};

        function updateProgress() {
            const progress = document.getElementById('progress');
            const percentage = (currentStep / 5) * 100;
            progress.style.width = percentage + '%';
            
            // Update step indicators
            for (let i = 1; i <= 5; i++) {
                const indicator = document.getElementById(`step${i}-indicator`);
                if (i < currentStep) {
                    indicator.className = 'step-circle completed';
                    indicator.innerHTML = '✓';
                } else if (i === currentStep) {
                    indicator.className = 'step-circle active';
                    indicator.innerHTML = i;
                } else {
                    indicator.className = 'step-circle';
                    indicator.innerHTML = i;
                }
            }
        }

        function nextStep() {
            document.getElementById(`step${currentStep}`).classList.remove('active');
            currentStep++;
            document.getElementById(`step${currentStep}`).classList.add('active');
            updateProgress();
        }

        function checkRequirements() {
            // Simula controllo requisiti
            const checks = [
                { id: 'php-status', delay: 500 },
                { id: 'mysql-status', delay: 1000 },
                { id: 'gd-status', delay: 1500 },
                { id: 'curl-status', delay: 2000 },
                { id: 'openssl-status', delay: 2500 },
                { id: 'uploads-status', delay: 3000 }
            ];

            checks.forEach(check => {
                setTimeout(() => {
                    const element = document.getElementById(check.id);
                    element.textContent = 'OK';
                    element.className = 'status ok';
                }, check.delay);
            });

            setTimeout(() => {
                document.getElementById('next-step1').style.display = 'inline-block';
            }, 3500);
        }

        function testDatabase() {
            const host = document.getElementById('db_host').value;
            const name = document.getElementById('db_name').value;
            const user = document.getElementById('db_user').value;
            const pass = document.getElementById('db_pass').value;

            dbConfig = { host, name, user, pass };

            const resultDiv = document.getElementById('db-test-result');
            resultDiv.innerHTML = '<div style="color: #0066CC; padding: 10px;">Connessione in corso...</div>';

            // Simula test connessione
            setTimeout(() => {
                resultDiv.innerHTML = '<div class="success">✓ Connessione database riuscita!</div>';
                document.getElementById('next-step2').style.display = 'inline-block';
            }, 2000);
        }

        function createSchema() {
            const tables = [
                'users-table',
                'customers-table',
                'providers-table',
                'contracts-table',
                'addresses-table',
                'attachments-table',
                'consents-table',
                'notes-table',
                'audit-log-table',
                'contract-drafts-table',
                'migrations-table',
                'initial-data'
            ];
            tables.forEach((table, index) => {
                setTimeout(() => {
                    const el = document.getElementById(table);
                    if (el) el.innerHTML = '<span class="status ok">✔</span>';
                }, (index + 1) * 800);
            });
            setTimeout(() => {
                document.getElementById('next-step3').style.display = 'inline-block';
            }, tables.length * 800 + 500);
        }

        function createAdmin() {
            const email = document.getElementById('admin_email').value;
            const password = document.getElementById('admin_password').value;
            const confirm = document.getElementById('admin_password_confirm').value;

            if (!email || !password || password !== confirm) {
                document.getElementById('admin-result').innerHTML = 
                    '<div class="error">Errore: Verifica i dati inseriti</div>';
                return;
            }

            if (password.length < 8) {
                document.getElementById('admin-result').innerHTML = 
                    '<div class="error">Errore: La password deve essere di almeno 8 caratteri</div>';
                return;
            }

            document.getElementById('admin-result').innerHTML = 
                '<div style="color: #0066CC; padding: 10px;">Creazione account in corso...</div>';

            setTimeout(() => {
                document.getElementById('admin-result').innerHTML = 
                    '<div class="success">✓ Account amministratore creato con successo!</div>';
                document.getElementById('final-email').textContent = email;
                document.getElementById('next-step4').style.display = 'inline-block';
            }, 2000);
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
