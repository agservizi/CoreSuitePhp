<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

?><!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CoreSuite</title>
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/coresuite-theme.css">
    <link rel="icon" href="/assets/images/coresuite-favicon.svg">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="dashboard.php" class="nav-link">Dashboard</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-user"></i> <?= htmlspecialchars($_SESSION['role']) ?>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="logout.php" class="dropdown-item">Logout</a>
                </div>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="dashboard.php" class="brand-link">
            <img src="/assets/images/coresuite-logo.svg" alt="CoreSuite Logo" class="brand-image" style="opacity: .8">
            <span class="brand-text font-weight-light">CoreSuite</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link active">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="contracts.php" class="nav-link">
                            <i class="nav-icon fas fa-file-contract"></i>
                            <p>Contratti</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="customers.php" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Clienti</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="providers.php" class="nav-link">
                            <i class="nav-icon fas fa-building"></i>
                            <p>Provider</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="notifications.php" class="nav-link">
                            <i class="nav-icon fas fa-bell"></i>
                            <p>Notifiche</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard</h1>
                    </div>
                </div>
                <?php include __DIR__ . '/src/views/dashboard/stats_box.php'; ?>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3 id="contracts-total">0</h3>
                                <p>Contratti totali</p>
                            </div>
                            <div class="icon"><i class="fas fa-file-contract"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3 id="customers-total">0</h3>
                                <p>Clienti attivi</p>
                            </div>
                            <div class="icon"><i class="fas fa-users"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3 id="revenue-month">€0</h3>
                                <p>Revenue mese</p>
                            </div>
                            <div class="icon"><i class="fas fa-euro-sign"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3 id="performance-kpi">0%</h3>
                                <p>Performance</p>
                            </div>
                            <div class="icon"><i class="fas fa-chart-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Contratti per mese</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="contractsChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Provider per quota</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="providersChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer text-center">
        <strong>CoreSuite &copy; <?= date('Y') ?></strong> - Tutti i diritti riservati.
    </footer>
</div>
<script>
    // Animazione contatori (demo, da collegare a API reali)
    function animateValue(id, start, end, duration, prefix = '', suffix = '') {
        let range = end - start;
        let current = start;
        let increment = range / (duration / 20);
        let obj = document.getElementById(id);
        let step = 0;
        let timer = setInterval(function() {
            current += increment;
            step++;
            if ((increment > 0 && current >= end) || (increment < 0 && current <= end) || step > 100) {
                current = end;
                clearInterval(timer);
            }
            obj.textContent = prefix + Math.floor(current) + suffix;
        }, 20);
    }
    animateValue('contracts-total', 0, 120, 1000);
    animateValue('customers-total', 0, 80, 1000);
    animateValue('revenue-month', 0, 32000, 1200, '€');
    animateValue('performance-kpi', 0, 87, 900, '', '%');
    // Chart.js demo (da collegare a API reali)
    const ctx1 = document.getElementById('contractsChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug'],
            datasets: [{
                label: 'Contratti per mese',
                data: [12, 19, 15, 22, 30, 28, 35],
                borderColor: '#0066CC',
                backgroundColor: 'rgba(0,102,204,0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });
    const ctx2 = document.getElementById('providersChart').getContext('2d');
    new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: ['Fastweb', 'Windtre', 'Pianeta Fibra', 'Enel Energia', 'A2A Energia'],
            datasets: [{
                data: [30, 20, 15, 25, 10],
                backgroundColor: ['#0066CC', '#00AA44', '#FFB300', '#FF5252', '#7C4DFF']
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });
</script>
</body>
</html>
