<?php
namespace App\Controller;

use PDO;
use Core\TOTP;

class AuthController extends BaseController {
    public function loginForm($error = '') {
        include __DIR__ . '/../../View/auth/login.php';
    }

    public function login() {
        session_start();
        $config = require __DIR__ . '/../../../config/database.php';
        $pdo = new PDO('mysql:host=' . $config['db_host'] . ';dbname=' . $config['db_name'], $config['db_user'], $config['db_password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // CSRF check
        if (!csrf_check($_POST['csrf_token'] ?? '')) {
            $this->loginForm('Token CSRF non valido');
            return;
        }

        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            // MFA: se attivo, chiedi codice TOTP
            if (!empty($user['mfa_secret'])) {
                $_SESSION['tmp_user_id'] = $user['id'];
                header('Location: /index.php?route=mfa');
                exit;
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role_id'] = $user['role_id'];
                header('Location: /index.php?route=dashboard');
                exit;
            }
        } else {
            $this->loginForm('Credenziali non valide');
        }
    }

    public function mfaForm($error = '') {
        include __DIR__ . '/../../View/auth/mfa.php';
    }

    public function mfaVerify() {
        session_start();
        if (!isset($_SESSION['tmp_user_id'])) {
            header('Location: /index.php?route=login'); exit;
        }
        $userId = $_SESSION['tmp_user_id'];
        $config = require __DIR__ . '/../../../config/database.php';
        $pdo = new PDO('mysql:host=' . $config['db_host'] . ';dbname=' . $config['db_name'], $config['db_user'], $config['db_password']);
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $code = $_POST['totp'] ?? '';
        
        // CSRF check
        if (!csrf_check($_POST['csrf_token'] ?? '')) {
            $this->mfaForm('Token CSRF non valido');
            return;
        }

        if ($user && \Core\TOTP::verify($user['mfa_secret'], $code)) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role_id'] = $user['role_id'];
            unset($_SESSION['tmp_user_id']);
            header('Location: /index.php?route=dashboard');
            exit;
        } else {
            $this->mfaForm('Codice MFA non valido');
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: /index.php?route=login');
        exit;
    }
}