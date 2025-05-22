<?php
session_start();
require_once 'vendor/autoload.php';
require_once 'config/config.php';
require_once 'models/User.php';

use RobThree\Auth\TwoFactorAuth;

class Auth {
    private $db;
    private $tfa;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->tfa = new TwoFactorAuth('CoreSuite');
    }

    public function login($email, $password, $remember = false) {
        try {
            $email = trim($email);
            $password = trim($password);
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Controllo hash valido
            if ($user && (!isset($user['password']) || strlen($user['password']) < 50 || strpos($user['password'], '$2y$') !== 0)) {
                return [
                    'requiresMfa' => false,
                    'success' => false,
                    'message' => 'Hash password non valido. Reimposta la password.'
                ];
            }

            if ($user && password_verify($password, $user['password'])) {
                if ($user['mfa_secret']) {
                    // Richiedi verifica MFA
                    $_SESSION['pending_user_id'] = $user['id'];
                    return [
                        'requiresMfa' => true,
                        'success' => false,
                        'message' => 'Inserisci il codice 2FA'
                    ];
                } else {
                    // Login diretto senza MFA
                    $this->createSession($user, $remember);
                    return [
                        'requiresMfa' => false,
                        'success' => true,
                        'message' => 'Login effettuato con successo'
                    ];
                }
            }

            return [
                'requiresMfa' => false,
                'success' => false,
                'message' => 'Credenziali non valide'
            ];
        } catch (Exception $e) {
            error_log('Errore login: ' . $e->getMessage());
            return [
                'requiresMfa' => false,
                'success' => false,
                'message' => 'Errore durante il login'
            ];
        }
    }

    public function verifyMfa($code) {
        if (!isset($_SESSION['pending_user_id'])) {
            return [
                'success' => false,
                'message' => 'Sessione non valida'
            ];
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['pending_user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && $this->tfa->verifyCode($user['mfa_secret'], $code)) {
                $this->createSession($user);
                unset($_SESSION['pending_user_id']);
                return [
                    'success' => true,
                    'message' => 'Verifica completata con successo'
                ];
            }

            return [
                'success' => false,
                'message' => 'Codice non valido'
            ];
        } catch (Exception $e) {
            error_log($e->getMessage());
            return [
                'success' => false,
                'message' => 'Errore durante la verifica'
            ];
        }
    }

    private function createSession($user, $remember = false) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['logged_in'] = true;

        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));

            $stmt = $this->db->prepare("INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $token, $expiry]);

            setcookie('remember_token', $token, strtotime('+30 days'), '/', '', true, true);
        }

        // Registra il login
        $stmt = $this->db->prepare("INSERT INTO login_logs (user_id, ip_address, user_agent) VALUES (?, ?, ?)");
        $stmt->execute([
            $user['id'],
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT']
        ]);
    }

    public function logout() {
        // Rimuovi il token remember me se presente
        if (isset($_COOKIE['remember_token'])) {
            $stmt = $this->db->prepare("DELETE FROM remember_tokens WHERE token = ?");
            $stmt->execute([$_COOKIE['remember_token']]);
            setcookie('remember_token', '', time() - 3600, '/', '', true, true);
        }

        // Distruggi la sessione
        session_destroy();
        return true;
    }

    public function isLoggedIn() {
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
            return true;
        }

        // Controlla il remember token
        if (isset($_COOKIE['remember_token'])) {
            $stmt = $this->db->prepare("
                SELECT u.* FROM users u 
                JOIN remember_tokens rt ON u.id = rt.user_id 
                WHERE rt.token = ? AND rt.expires_at > NOW()
            ");
            $stmt->execute([$_COOKIE['remember_token']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $this->createSession($user);
                return true;
            }
        }

        return false;
    }

    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }

    public function requireAdmin() {
        $this->requireLogin();
        if ($_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?error=unauthorized');
            exit;
        }
    }

    public function setupMfa($userId) {
        $secret = $this->tfa->createSecret();
        
        $stmt = $this->db->prepare("UPDATE users SET mfa_secret = ? WHERE id = ?");
        if ($stmt->execute([$secret, $userId])) {
            return [
                'success' => true,
                'secret' => $secret,
                'qrcode' => $this->tfa->getQRCodeImageAsDataUri(
                    'CoreSuite: ' . $_SESSION['user_email'],
                    $secret
                )
            ];
        }

        return [
            'success' => false,
            'message' => 'Errore durante la configurazione 2FA'
        ];
    }

    public function disableMfa($userId, $code) {
        $stmt = $this->db->prepare("SELECT mfa_secret FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $this->tfa->verifyCode($user['mfa_secret'], $code)) {
            $stmt = $this->db->prepare("UPDATE users SET mfa_secret = NULL WHERE id = ?");
            if ($stmt->execute([$userId])) {
                return [
                    'success' => true,
                    'message' => '2FA disabilitato con successo'
                ];
            }
        }

        return [
            'success' => false,
            'message' => 'Codice non valido'
        ];
    }

    public function resetPassword($email) {
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $this->db->prepare("
            INSERT INTO password_resets (email, token, expires_at) 
            VALUES (?, ?, ?)
        ");

        if ($stmt->execute([$email, $token, $expiry])) {
            // Invia email con il link di reset
            $resetLink = "https://app.coresuite.it/reset-password.php?token=" . $token;
            // TODO: Implementa l'invio dell'email
            return true;
        }

        return false;
    }

    public function validatePasswordResetToken($token) {
        $stmt = $this->db->prepare("
            SELECT * FROM password_resets 
            WHERE token = ? AND expires_at > NOW() 
            AND used = 0
        ");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePassword($token, $newPassword) {
        $reset = $this->validatePasswordResetToken($token);
        if (!$reset) {
            return false;
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $this->db->beginTransaction();
        try {
            // Aggiorna la password
            $stmt = $this->db->prepare("
                UPDATE users SET password = ? 
                WHERE email = ?
            ");
            $stmt->execute([$hashedPassword, $reset['email']]);

            // Marca il token come utilizzato
            $stmt = $this->db->prepare("
                UPDATE password_resets SET used = 1 
                WHERE token = ?
            ");
            $stmt->execute([$token]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
}

// Gestione della richiesta AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $auth = new Auth();

    if (isset($_POST['email']) && isset($_POST['password'])) {
        echo json_encode($auth->login(
            $_POST['email'],
            $_POST['password'],
            isset($_POST['remember'])
        ));
    } elseif (isset($_POST['mfa_code'])) {
        echo json_encode($auth->verifyMfa($_POST['mfa_code']));
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Richiesta non valida'
        ]);
    }
    exit;
}
