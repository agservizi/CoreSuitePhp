<?php
// Dashboard dinamica con menu laterale in base al ruolo
?><!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - CoreSuite</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
            <li class="nav-item d-none d-sm-inline-block"><a href="/index.php?route=dashboard" class="nav-link">Dashboard</a></li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a href="/index.php?route=logout" class="nav-link">Logout</a></li>
        </ul>
    </nav>
    <?php \App\View\BaseView::renderSidebar($role_id); ?>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6"><h1>Dashboard</h1></div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner"><h3><?= $stats['users'] ?></h3><p>Utenti</p></div>
                            <div class="icon"><i class="fas fa-users"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner"><h3><?= $stats['customers'] ?></h3><p>Clienti</p></div>
                            <div class="icon"><i class="fas fa-address-book"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner"><h3><?= $stats['contracts'] ?></h3><p>Contratti</p></div>
                            <div class="icon"><i class="fas fa-file-contract"></i></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header"><h3 class="card-title">Contratti per mese</h3></div>
                            <div class="card-body">
                                <canvas id="chart1" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<script>
const ctx = document.getElementById('chart1').getContext('2d');
const chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($chart['labels']) ?>,
        datasets: [{
            label: 'Contratti',
            data: <?= json_encode($chart['data']) ?>,
            backgroundColor: 'rgba(60,141,188,0.9)'
        }]
    },
    options: {responsive:true, plugins:{legend:{display:false}}}
});
</script>
</body>
</html>
