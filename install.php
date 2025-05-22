<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbHost = $_POST['db_host'] ?? '';
    $dbName = $_POST['db_name'] ?? '';
    $dbUser = $_POST['db_user'] ?? '';
    $dbPass = $_POST['db_pass'] ?? '';
    $adminUser = '373798570';
    $adminPass = 'Giogiu2123@';
    $adminEmail = 'ag.servizi16@coresuite.it';
    $adminFirst = 'Carmine';
    $adminLast = 'Cavaliere';
    $error = '';
    $success = false;

    try {
        $dsn = "mysql:host=$dbHost;charset=utf8mb4";
        $pdo = new PDO($dsn, $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$dbName`");
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(255),
            first_name VARCHAR(255),
            last_name VARCHAR(255),
            role VARCHAR(50) DEFAULT 'admin',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        $hash = password_hash($adminPass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT IGNORE INTO users (username, password, email, first_name, last_name, role) VALUES (?, ?, ?, ?, ?, 'admin')");
        $stmt->execute([$adminUser, $hash, $adminEmail, $adminFirst, $adminLast]);
        $success = true;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
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
                    <div class="card-body">
                        <h2 class="mb-4 text-center">Installazione <span class="text-primary">CoreSuite</span></h2>
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success">Installazione completata!<br>Utente admin: <b>373798570</b><br>Password: <b>Giogiu2123@</b></div>
                            <a href="login.php" class="btn btn-success">Vai al login</a>
                        <?php elseif (!empty($error)): ?>
                            <div class="alert alert-danger">Errore: <?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <?php if (empty($success)): ?>
                        <form method="post">
                            <div class="form-group">
                                <label>Host Database</label>
                                <input type="text" name="db_host" class="form-control" value="127.0.0.1" required>
                            </div>
                            <div class="form-group">
                                <label>Nome Database</label>
                                <input type="text" name="db_name" class="form-control" value="coresuite" required>
                            </div>
                            <div class="form-group">
                                <label>Utente Database</label>
                                <input type="text" name="db_user" class="form-control" value="root" required>
                            </div>
                            <div class="form-group">
                                <label>Password Database</label>
                                <input type="password" name="db_pass" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary">Installa</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
