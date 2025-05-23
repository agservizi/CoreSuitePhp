<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuovo Cliente - CoreSuite</title>
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
                <a href="/dashboard.php" class="nav-link">Dashboard</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="/customers.php" class="nav-link">Clienti</a>
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
                    <a href="/logout.php" class="dropdown-item">
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
            <span class="brand-text font-weight-light ml-3">CoreSuite</span>
            
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
                        <h1>Nuovo Cliente</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/dashboard.php">Home</a></li>
                            <li class="breadcrumb-item"><a href="/customers.php">Clienti</a></li>
                            <li class="breadcrumb-item active">Nuovo</li>
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
                        <!-- general form elements -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Dati cliente</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form method="post">
                                <?php if (isset($error)): ?>
                                    <div class="alert alert-danger m-3"><?= htmlspecialchars($error) ?></div>
                                <?php endif; ?>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">                                            <div class="form-group">
                                                <label for="first_name">Nome</label>
                                                <input type="text" name="first_name" id="first_name" class="form-control" value="<?= $_POST['first_name'] ?? '' ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="last_name">Cognome</label>
                                                <input type="text" name="last_name" id="last_name" class="form-control" value="<?= $_POST['last_name'] ?? '' ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="tax_code">Codice Fiscale</label>
                                                <input type="text" name="tax_code" id="tax_code" class="form-control" value="<?= $_POST['tax_code'] ?? '' ?>" required maxlength="16">
                                            </div>
                                            <div class="form-group">
                                                <label for="date_of_birth">Data di nascita</label>
                                                <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="<?= $_POST['date_of_birth'] ?? '' ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="place_of_birth">Luogo di nascita</label>
                                                <input type="text" name="place_of_birth" id="place_of_birth" class="form-control" value="<?= $_POST['place_of_birth'] ?? '' ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="document_type">Tipo documento</label>
                                                <select name="document_type" id="document_type" class="form-control">
                                                    <option value="Carta d'identità" <?= isset($_POST['document_type']) && $_POST['document_type'] == "Carta d'identità" ? 'selected' : '' ?>>Carta d'identità</option>
                                                    <option value="Passaporto" <?= isset($_POST['document_type']) && $_POST['document_type'] == "Passaporto" ? 'selected' : '' ?>>Passaporto</option>
                                                    <option value="Patente" <?= isset($_POST['document_type']) && $_POST['document_type'] == "Patente" ? 'selected' : '' ?>>Patente</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="document_number">Numero documento</label>
                                                <input type="text" name="document_number" id="document_number" class="form-control" value="<?= $_POST['document_number'] ?? '' ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="document_expiry">Scadenza documento</label>
                                                <input type="date" name="document_expiry" id="document_expiry" class="form-control" value="<?= $_POST['document_expiry'] ?? '' ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                    </div>
                                                    <input type="email" name="email" id="email" class="form-control" value="<?= $_POST['email'] ?? '' ?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="phone">Telefono</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                    </div>
                                                    <input type="text" name="phone" id="phone" class="form-control" value="<?= $_POST['phone'] ?? '' ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mobile">Cellulare</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                                                    </div>
                                                    <input type="text" name="mobile" id="mobile" class="form-control" value="<?= $_POST['mobile'] ?? '' ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="notes">Note</label>
                                                <textarea name="notes" id="notes" class="form-control" rows="4"><?= $_POST['notes'] ?? '' ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salva</button>
                                    <a href="/customers.php" class="btn btn-secondary ml-2"><i class="fas fa-times"></i> Annulla</a>
                                </div>
                            </form>
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
