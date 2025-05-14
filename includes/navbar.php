<?php
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

// Ottieni il conteggio delle notifiche non lette
$notification_count = 0;
$recent_notifications = [];

// Se l'utente è autenticato, ottieni le notifiche
if (isset($_SESSION['user_id'])) {
    try {
        require_once __DIR__ . '/../classes/Database.php';
        $db = Database::getInstance();
        
        // Conta le notifiche non lette
        $stmt = $db->prepare("
            SELECT COUNT(*) as count
            FROM notifications
            WHERE user_id = ? AND is_read = 0
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $notification_count = $result['count'];
        
        // Ottieni le notifiche recenti
        $stmt = $db->prepare("
            SELECT id, title, message, type, created_at, resource_id, resource_type
            FROM notifications
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT 5
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $recent_notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Errore nel recupero delle notifiche: " . $e->getMessage());
    }
}
?>
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="index.php" class="nav-link">Home</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                <?php if ($notification_count > 0): ?>
                <span class="badge badge-warning navbar-badge"><?php echo $notification_count; ?></span>
                <?php endif; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-header"><?php echo $notification_count; ?> Notifiche non lette</span>
                <div class="dropdown-divider"></div>
                
                <?php if (empty($recent_notifications)): ?>
                    <a href="#" class="dropdown-item text-center">
                        <p>Nessuna notifica disponibile</p>
                    </a>
                <?php else: ?>
                    <?php foreach ($recent_notifications as $notification): ?>
                        <?php
                        // Determina l'icona in base al tipo di notifica
                        $icon = 'fas fa-bell';
                        switch ($notification['type']) {
                            case 'success': $icon = 'fas fa-check-circle text-success'; break;
                            case 'warning': $icon = 'fas fa-exclamation-triangle text-warning'; break;
                            case 'danger': $icon = 'fas fa-times-circle text-danger'; break;
                            case 'info': $icon = 'fas fa-info-circle text-info'; break;
                        }
                        
                        // Determina il link in base al tipo di risorsa
                        $link = 'notifications.php';
                        if ($notification['resource_type'] === 'contract' && $notification['resource_id']) {
                            $link = 'contract-details.php?id=' . $notification['resource_id'];
                        }
                        
                        // Formatta la data
                        $created_at = new DateTime($notification['created_at']);
                        $now = new DateTime();
                        $diff = $now->diff($created_at);
                        
                        if ($diff->d > 0) {
                            $time_text = $diff->d . ' g';
                        } elseif ($diff->h > 0) {
                            $time_text = $diff->h . ' h';
                        } else {
                            $time_text = $diff->i . ' min';
                        }
                        ?>
                        <a href="<?php echo $link; ?>" class="dropdown-item">
                            <i class="<?php echo $icon; ?> mr-2"></i> <?php echo htmlspecialchars($notification['title']); ?>
                            <span class="float-right text-muted text-sm"><?php echo $time_text; ?></span>
                        </a>
                        <div class="dropdown-divider"></div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <a href="notifications.php" class="dropdown-item dropdown-footer">Vedi tutte le notifiche</a>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <!-- User Dropdown Menu -->
        <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <img src="https://ui-avatars.com/api/?name=Admin&background=random" class="user-image img-circle elevation-2" alt="User Image">
                <span class="d-none d-md-inline">Admin</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <!-- User image -->
                <li class="user-header bg-primary">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=random" class="img-circle elevation-2" alt="User Image">
                    <p>
                        Administrator
                        <small>Membro dal <?php echo date('M. Y'); ?></small>
                    </p>
                </li>
                <!-- Menu Footer-->                <li class="user-footer">
                    <a href="profile.php" class="btn btn-default btn-flat">Profilo</a>
                    <a href="logout.php" class="btn btn-default btn-flat float-right">Esci</a>
                </li>
            </ul>
        </li>
    </ul>
</nav>
<!-- /.navbar -->

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link">
        <span class="brand-text font-weight-bold">Core<span class="font-weight-light">Suite</span></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="https://ui-avatars.com/api/?name=Admin&background=random" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">Administrator</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="index.php" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="phone-manager.php" class="nav-link">
                        <i class="nav-icon fas fa-phone-alt"></i>
                        <p>Telefonia</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-bolt"></i>
                        <p>
                            Energia
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="energy-contract.php?provider=enel" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Enel Energia</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="energy-contract.php?provider=fastweb" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Fastweb Energia</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="energy-contract.php?provider=a2a" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>A2A Energia</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="clients.php" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Clienti</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="contracts.php" class="nav-link">
                        <i class="nav-icon fas fa-file-contract"></i>
                        <p>Contratti</p>
                    </a>
                </li>
                <li class="nav-header">ACCOUNT</li>                <li class="nav-item">
                    <a href="profile.php" class="nav-link">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Profilo</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
