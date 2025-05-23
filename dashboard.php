<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

?><!DOCTYPE html>
<html lang="it">
<head>    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CoreSuite</title>
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
                <a href="/dashboard.php" class="nav-link active">Dashboard</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-user"></i> <?= htmlspecialchars($_SESSION['role']) ?>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="/logout.php" class="dropdown-item">Logout</a>
                </div>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="/dashboard.php" class="brand-link">
            <span class="brand-text font-weight-light ml-3">CoreSuite</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="/dashboard.php" class="nav-link active">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/contracts.php" class="nav-link">
                            <i class="nav-icon fas fa-file-contract"></i>
                            <p>Contratti</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/customers.php" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Clienti</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/providers.php" class="nav-link">
                            <i class="nav-icon fas fa-building"></i>
                            <p>Provider</p>
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
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info text-center mt-2" id="welcome-empty-data" style="display:none;">
                            <i class="fas fa-info-circle mr-2"></i>
                            Benvenuto in CoreSuite!<br>
                            Inizia subito a inserire <b>contratti</b>, <b>clienti</b> e <b>provider</b> per vedere qui le statistiche e i grafici della tua attività.<br>
                            Tutte le funzionalità sono già pronte: usa il menu a sinistra per cominciare.
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer text-center">
        <strong>CoreSuite &copy; <?php echo date('Y'); ?></strong> - Tutti i diritti riservati.
    </footer>
</div>
<script>
    // Script per caricare dati dashboard
    $(function() {
        $.getJSON('/api/v1/stats.php', function(data) {
            $('#contracts-total').text(data.contracts_count || 0);
            $('#customers-total').text(data.customers_count || 0);
            $('#revenue-month').text('€' + (data.revenue_month || 0));
            $('#performance-kpi').text((data.performance || 0) + '%');
            // Mostra messaggio guida se tutto è vuoto
            if((!data.contracts_count || data.contracts_count==0) && (!data.customers_count || data.customers_count==0)) {
                $('#welcome-empty-data').show();
            }
            // Grafici
            if (data.contracts_by_month) {
                new Chart(document.getElementById('contractsChart'), {
                    type: 'bar',
                    data: {
                        labels: data.contracts_by_month.labels,
                        datasets: [{
                            label: 'Contratti',
                            data: data.contracts_by_month.data,
                            backgroundColor: '#3c8dbc'
                        }]
                    }
                });
            }
            
            if (data.providers_share) {
                new Chart(document.getElementById('providersChart'), {
                    type: 'doughnut',
                    data: {
                        labels: data.providers_share.labels,
                        datasets: [{
                            data: data.providers_share.data,
                            backgroundColor: ['#3c8dbc', '#00a65a', '#f39c12', '#dd4b39', '#605ca8']
                        }]
                    }
                });
            }
        });
    });
</script>
</body>
</html>
