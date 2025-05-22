<?php
namespace CoreSuite\Controllers;

use CoreSuite\Models\User;

class AuthController
{
    public function showLogin()
    {
        require __DIR__ . '/../views/auth/login.php';
    }

    public function login()
    {
        // Validazione input
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        if (!$email || !$password) {
            $error = 'Email e password obbligatorie.';
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        $user = User::findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $error = 'Credenziali non valide.';
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        if (!$user['is_active']) {
            $error = 'Account disabilitato.';
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        // TODO: MFA check
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header('Location: /dashboard.php');
        exit;
    }

    public function logout()
    {
        session_destroy();
        header('Location: /login.php');
        exit;
    }
}
