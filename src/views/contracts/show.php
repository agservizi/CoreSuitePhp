<?php
// $contract, $customer, $provider, $attachments forniti dal controller
$extraData = isset($contract['extra_data']) && !empty($contract['extra_data']) ? json_decode($contract['extra_data'], true) : [];
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dettaglio Contratto - CoreSuite</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/coresuite-theme.css">
    <link rel="icon" href="/assets/images/coresuite-favicon.svg">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <footer class="main-footer">
        <strong>CoreSuite &copy; <?= date('Y') ?></strong> - Tutti i diritti riservati.
    </footer>
</div>
</body>
</html>
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="/dashboard.php" class="nav-link">Dashboard</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-user"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="/logout.php" class="dropdown-item">Logout</a>
                </div>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="/dashboard.php" class="brand-link">
            <span class="brand-text font-weight-light ml-3">CoreSuite</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="/dashboard.php" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/contracts.php" class="nav-link active">
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
                    <li class="nav-item">
                        <a href="/notifications.php" class="nav-link">
                            <i class="nav-icon fas fa-bell"></i>
                            <p>Notifiche</p>
                        </a>
                    </li>
                </ul>
            </nav>
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="contracts.php" class="nav-link active">
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
        </div>    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Dettaglio Contratto #<?= htmlspecialchars($contract['id']) ?></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/dashboard.php">Home</a></li>
                            <li class="breadcrumb-item"><a href="/contracts.php">Contratti</a></li>
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
                    <div class="col-md-6">
                        <!-- Informazioni Contratto -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Informazioni Contratto</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th style="width:30%">ID</th>
                                                <td><?= htmlspecialchars($contract['id']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Tipo</th>
                                                <td><?= htmlspecialchars($contract['type']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Stato</th>
                                                <td>
                                                    <span class="badge 
                                                        <?= $contract['status'] == 'completato' ? 'badge-success' : 
                                                            ($contract['status'] == 'in_corso' ? 'badge-primary' : 
                                                            ($contract['status'] == 'annullato' ? 'badge-danger' : 'badge-warning')) ?>">
                                                        <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $contract['status']))) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Data Creazione</th>
                                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($contract['created_at']))) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Provider</th>
                                                <td>
                                                    <?php if ($provider): ?>
                                                        <a href="/providers_show.php?id=<?= $provider['id'] ?>">
                                                            <?= htmlspecialchars($provider['name']) ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">Non specificato</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="btn-group">
                                    <a href="/contracts.php" class="btn btn-default"><i class="fas fa-arrow-left"></i> Indietro</a>
                                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                        <a href="/contracts_edit.php?id=<?= $contract['id'] ?>" class="btn btn-warning"><i class="fas fa-edit"></i> Modifica</a>
                                        <a href="/contracts_delete.php?id=<?= $contract['id'] ?>" class="btn btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questo contratto?');"><i class="fas fa-trash"></i> Elimina</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <!-- Informazioni Cliente -->
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">Informazioni Cliente</h3>
                            </div>
                            <div class="card-body">
                                <?php if ($customer): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th style="width:30%">Nome</th>
                                                <td><?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Codice Fiscale</th>
                                                <td><?= htmlspecialchars($customer['tax_code']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Email</th>
                                                <td><?= htmlspecialchars($customer['email']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Telefono</th>
                                                <td><?= htmlspecialchars($customer['phone']) ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <a href="/customers_show.php?id=<?= $customer['id'] ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-user"></i> Visualizza Profilo Cliente
                                </a>
                                <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> Nessun cliente associato a questo contratto.
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dati Aggiuntivi e Allegati -->
                <div class="row">
                    <div class="col-md-6">
                        <!-- Dati Aggiuntivi -->
                        <div class="card card-secondary">
                            <div class="card-header">
                                <h3 class="card-title">Dati Aggiuntivi</h3>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($extraData)): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <?php foreach($extraData as $key => $value): ?>
                                            <?php if (!is_array($value) && !is_object($value)): ?>
                                            <tr>
                                                <th style="width:30%"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $key))) ?></th>
                                                <td><?= htmlspecialchars($value) ?></td>
                                            </tr>
                                            <?php endif; ?>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Nessun dato aggiuntivo disponibile.
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <!-- Allegati -->
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title">Allegati</h3>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($attachments)): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Nome File</th>
                                                <th>Dimensione</th>
                                                <th>Data Upload</th>
                                                <th>Azioni</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($attachments as $attachment): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($attachment['original_name']) ?></td>
                                                <td><?= htmlspecialchars(number_format($attachment['file_size'] / 1024, 2) . ' KB') ?></td>
                                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($attachment['upload_date']))) ?></td>
                                                <td>
                                                    <a href="/download_attachment.php?id=<?= $attachment['id'] ?>" class="btn btn-sm btn-info">
                                                        <i class="fas fa-download"></i> Scarica
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Nessun allegato disponibile.
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dettaglio Contratto #<?= htmlspecialchars($contract['id']) ?></h1>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">Dati contratto</h3>
                            </div>
                            <div class="card-body">
                                <dl class="row">
                                    <dt class="col-sm-4">Cliente</dt>
                                    <dd class="col-sm-8"><?= htmlspecialchars($contract['customer_id']) ?></dd>
                                    <dt class="col-sm-4">Gestore</dt>
                                    <dd class="col-sm-8"><?= htmlspecialchars($contract['provider']) ?></dd>
                                    <dt class="col-sm-4">Tipo</dt>
                                    <dd class="col-sm-8"><?= htmlspecialchars($contract['type']) ?></dd>
                                    <dt class="col-sm-4">Stato</dt>
                                    <dd class="col-sm-8"><?= htmlspecialchars($contract['status']) ?></dd>
                                    <dt class="col-sm-4">Creato il</dt>
                                    <dd class="col-sm-8"><?= htmlspecialchars($contract['created_at']) ?></dd>
                                    <dt class="col-sm-4">Aggiornato il</dt>
                                    <dd class="col-sm-8"><?= htmlspecialchars($contract['updated_at']) ?></dd>
                                </dl>
                            </div>
                            <div class="card-footer">
                                <a href="/contracts.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Torna all'elenco</a>
                            </div>
                        </div>
                        <?php if (!empty($contract['extra_data'])): ?>
                        <div class="card card-secondary mt-4">
                            <div class="card-header"><h3 class="card-title">Dati aggiuntivi provider</h3></div>
                            <div class="card-body">
                                <?php $extra = json_decode($contract['extra_data'], true); if ($extra): ?>
                                    <ul class="list-group list-group-flush">
                                    <?php foreach ($extra as $k=>$v): ?>
                                        <li class="list-group-item"><strong><?= htmlspecialchars($k) ?>:</strong> <?= htmlspecialchars($v) ?></li>
                                    <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin','user'])): ?>
                        <div class="card card-light mt-4">
                            <?php include __DIR__ . '/attachments.php'; ?>
                        </div>
                        <?php endif; ?>
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
