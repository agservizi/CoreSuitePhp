<?php
// $customer fornito dal controller
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dettaglio Cliente - CoreSuite</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/coresuite-theme.css">
    <link rel="icon" href="/assets/images/coresuite-favicon.svg">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="dashboard.php" class="nav-link">Dashboard</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="customers.php" class="nav-link">Clienti</a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-user"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="/profile.php" class="dropdown-item">
                        <i class="fas fa-user-cog mr-2"></i> Profilo
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="logout.php" class="dropdown-item">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                </div>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="/dashboard.php" class="brand-link">
            <img src="/assets/images/coresuite-logo.svg" alt="CoreSuite Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">CoreSuite</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="/dashboard.php" class="nav-link">
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
                        <a href="/customers.php" class="nav-link active">
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
                    <li class="nav-item">
                        <a href="/notifications.php" class="nav-link">
                            <i class="nav-icon fas fa-bell"></i>
                            <p>Notifiche</p>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Dettaglio Cliente #<?= htmlspecialchars($customer['id']) ?></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/dashboard.php">Home</a></li>
                            <li class="breadcrumb-item"><a href="/customers.php">Clienti</a></li>
                            <li class="breadcrumb-item active">Dettaglio</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Dati cliente</h3>
                            </div>                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-md-12 col-lg-8 order-2 order-md-1">
                                        <div class="row">
                                            <div class="col-12">
                                                <h4>Informazioni Cliente</h4>
                                                <div class="post">
                                                    <dl class="row">
                                                        <dt class="col-sm-3">Nome</dt>
                                                        <dd class="col-sm-9"><?= htmlspecialchars($customer['name']) ?></dd>
                                                        <dt class="col-sm-3">Cognome</dt>
                                                        <dd class="col-sm-9"><?= htmlspecialchars($customer['surname']) ?></dd>
                                                        <dt class="col-sm-3">Codice Fiscale</dt>
                                                        <dd class="col-sm-9"><?= htmlspecialchars($customer['fiscal_code']) ?></dd>
                                                        <dt class="col-sm-3">Data di nascita</dt>
                                                        <dd class="col-sm-9"><?= htmlspecialchars($customer['date_of_birth']) ?></dd>
                                                        <dt class="col-sm-3">Luogo di nascita</dt>
                                                        <dd class="col-sm-9"><?= htmlspecialchars($customer['place_of_birth']) ?></dd>
                                                    </dl>
                                                </div>

                                                <h4>Documenti</h4>
                                                <div class="post">
                                                    <dl class="row">
                                                        <dt class="col-sm-3">Tipo documento</dt>
                                                        <dd class="col-sm-9"><?= htmlspecialchars($customer['document_type']) ?></dd>
                                                        <dt class="col-sm-3">Numero documento</dt>
                                                        <dd class="col-sm-9"><?= htmlspecialchars($customer['document_number']) ?></dd>
                                                        <dt class="col-sm-3">Scadenza documento</dt>
                                                        <dd class="col-sm-9"><?= htmlspecialchars($customer['document_expiry']) ?></dd>
                                                    </dl>
                                                </div>

                                                <h4>Contatti</h4>
                                                <div class="post">
                                                    <dl class="row">
                                                        <dt class="col-sm-3">Email</dt>
                                                        <dd class="col-sm-9"><a href="mailto:<?= htmlspecialchars($customer['email']) ?>"><?= htmlspecialchars($customer['email']) ?></a></dd>
                                                        <dt class="col-sm-3">Telefono</dt>
                                                        <dd class="col-sm-9"><?= htmlspecialchars($customer['phone']) ?></dd>
                                                        <dt class="col-sm-3">Cellulare</dt>
                                                        <dd class="col-sm-9"><?= htmlspecialchars($customer['mobile']) ?></dd>
                                                    </dl>
                                                </div>

                                                <h4>Note</h4>
                                                <div class="post">
                                                    <p><?= htmlspecialchars($customer['notes'] ?: 'Nessuna nota disponibile.') ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-12 col-lg-4 order-1 order-md-2">
                                        <h3 class="text-primary"><i class="fas fa-user"></i> <?= htmlspecialchars($customer['name']) ?> <?= htmlspecialchars($customer['surname']) ?></h3>
                                        <p class="text-muted">Cliente #<?= htmlspecialchars($customer['id']) ?></p>
                                        <br>
                                        <div class="text-muted">
                                            <p class="text-sm">Codice Fiscale
                                                <b class="d-block"><?= htmlspecialchars($customer['fiscal_code']) ?></b>
                                            </p>
                                            <p class="text-sm">Email
                                                <b class="d-block"><?= htmlspecialchars($customer['email']) ?></b>
                                            </p>
                                        </div>

                                        <div class="btn-group mt-5">
                                            <a href="/customers_edit.php?id=<?= $customer['id'] ?>" class="btn btn-primary">
                                                <i class="fas fa-edit"></i> Modifica
                                            </a>
                                            <a href="/customers.php" class="btn btn-secondary">
                                                <i class="fas fa-arrow-left"></i> Torna all'elenco
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer">
        <div class="float-right d-none d-sm-block">
            <b>Version</b> 1.0.0
        </div>
        <strong>Copyright &copy; <?= date('Y') ?> <a href="/">CoreSuite</a>.</strong> Tutti i diritti riservati.
    </footer>
</div>
</body>
</html>
