<?php
// $customer fornito dal controller
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Cliente - CoreSuite</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/coresuite-theme.css">
    <link rel="icon" href="/assets/images/coresuite-favicon.svg">
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
                    <i class="far fa-user"></i>
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
                        <a href="dashboard.php" class="nav-link">
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
                        <a href="customers.php" class="nav-link active">
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
                        <h1 class="m-0">Modifica Cliente #<?= htmlspecialchars($customer['id']) ?></h1>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card card-warning">
                            <div class="card-header">
                                <h3 class="card-title">Modifica dati cliente</h3>
                            </div>
                            <form method="post">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="name">Nome</label>
                                        <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($customer['name']) ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="surname">Cognome</label>
                                        <input type="text" name="surname" id="surname" class="form-control" value="<?= htmlspecialchars($customer['surname']) ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="fiscal_code">Codice Fiscale</label>
                                        <input type="text" name="fiscal_code" id="fiscal_code" class="form-control" value="<?= htmlspecialchars($customer['fiscal_code']) ?>" required maxlength="16">
                                    </div>
                                    <div class="form-group">
                                        <label for="date_of_birth">Data di nascita</label>
                                        <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="<?= htmlspecialchars($customer['date_of_birth']) ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="place_of_birth">Luogo di nascita</label>
                                        <input type="text" name="place_of_birth" id="place_of_birth" class="form-control" value="<?= htmlspecialchars($customer['place_of_birth']) ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="document_type">Tipo documento</label>
                                        <input type="text" name="document_type" id="document_type" class="form-control" value="<?= htmlspecialchars($customer['document_type']) ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="document_number">Numero documento</label>
                                        <input type="text" name="document_number" id="document_number" class="form-control" value="<?= htmlspecialchars($customer['document_number']) ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="document_expiry">Scadenza documento</label>
                                        <input type="date" name="document_expiry" id="document_expiry" class="form-control" value="<?= htmlspecialchars($customer['document_expiry']) ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($customer['email']) ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="phone">Telefono</label>
                                        <input type="text" name="phone" id="phone" class="form-control" value="<?= htmlspecialchars($customer['phone']) ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="mobile">Cellulare</label>
                                        <input type="text" name="mobile" id="mobile" class="form-control" value="<?= htmlspecialchars($customer['mobile']) ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="notes">Note</label>
                                        <input type="text" name="notes" id="notes" class="form-control" value="<?= htmlspecialchars($customer['notes']) ?>">
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Salva Modifiche</button>
                                    <a href="/customers.php" class="btn btn-secondary ml-2">Annulla</a>
                                </div>
                            </form>
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
</body>
</html>
