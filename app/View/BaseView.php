<?php
// Placeholder per View base
namespace App\View;
class BaseView {
    public static function renderSidebar($role_id) {
        echo '<aside class="main-sidebar sidebar-dark-primary elevation-4">';
        echo '<a href="/index.php?route=dashboard" class="brand-link"><span class="brand-text font-weight-light">CoreSuite</span></a>';
        echo '<div class="sidebar">';
        echo '<nav class="mt-2">';
        echo '<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">';
        echo '<li class="nav-item"><a href="/index.php?route=dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>';
        if ($role_id == 1) { // Admin
            echo '<li class="nav-item"><a href="/index.php?route=users" class="nav-link"><i class="nav-icon fas fa-users"></i><p>Utenti</p></a></li>';
        }
        echo '<li class="nav-item"><a href="/index.php?route=customers" class="nav-link"><i class="nav-icon fas fa-address-book"></i><p>Clienti</p></a></li>';
        echo '<li class="nav-item"><a href="/index.php?route=contracts" class="nav-link"><i class="nav-icon fas fa-file-contract"></i><p>Contratti</p></a></li>';
        echo '</ul>';
        echo '</nav>';
        echo '</div>';
        echo '</aside>';
    }
}

// Dashboard AdminLTE con statistiche base e grafico placeholder

// Funzioni CSRF globali
if (!function_exists('csrf_token')) {
    function csrf_token() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    function csrf_check($token) {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
