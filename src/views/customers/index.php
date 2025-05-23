<?php
// $customers fornito dal controller
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clienti - CoreSuite</title>
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
            <span class="brand-text font-weight-light ml-3">CoreSuite</span>
            
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
                        <h1 class="m-0">Elenco Clienti</h1>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <!-- Toast notification container -->
                <div class="position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
                    <div id="toast-container"></div>
                </div>
                <div class="mb-3 d-flex flex-wrap align-items-center justify-content-between">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <a href="/customers_create.php" class="btn btn-primary mb-2 mr-2"><i class="fas fa-plus"></i> Nuovo Cliente</a>
                            <a href="/export_customers.php" class="btn btn-success mb-2"><i class="fas fa-file-csv"></i> Esporta CSV</a>
                        <?php endif; ?>
                    </div>
                    <form class="form-inline mb-2" id="searchForm" onsubmit="return false;">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" id="searchInput" placeholder="Cerca nome, cognome, email...">
                            <div class="input-group-append">
                                <button class="btn btn-info" type="button" id="resetSearch"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tutti i clienti</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped table-hover" id="customersTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Cognome</th>
                                    <th>Codice Fiscale</th>
                                    <th>Email</th>
                                    <th>Telefono</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($customers)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        <div class="my-4">
                                            <i class="fas fa-user-friends fa-3x mb-3 text-secondary"></i><br>
                                            <strong>Nessun cliente presente</strong>
                                            <p class="mt-2">Clicca su <b>Nuovo Cliente</b> per inserire il primo cliente.<br>Potrai gestire, modificare ed esportare i tuoi clienti da questa pagina.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                            <?php foreach ($customers as $c): ?>                                <tr>
                                    <td><?= htmlspecialchars($c['id']) ?></td>
                                    <td><?= htmlspecialchars($c['first_name']) ?></td>
                                    <td><?= htmlspecialchars($c['last_name']) ?></td>
                                    <td><?= htmlspecialchars($c['tax_code']) ?></td>
                                    <td><?= htmlspecialchars($c['email']) ?></td>
                                    <td><?= htmlspecialchars($c['phone']) ?></td>
                                    <td>
                                        <a href="/customers_show.php?id=<?= $c['id'] ?>" class="btn btn-info btn-sm" title="Dettagli"><i class="fas fa-eye"></i></a>
                                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                            <a href="/customers_edit.php?id=<?= $c['id'] ?>" class="btn btn-warning btn-sm" title="Modifica"><i class="fas fa-edit"></i></a>
                                            <a href="/customers_delete.php?id=<?= $c['id'] ?>" class="btn btn-danger btn-sm" title="Elimina" onclick="return confirm('Sei sicuro di voler eliminare questo cliente?');"><i class="fas fa-trash"></i></a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
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
$(function() {
    // Ricerca live
    $('#searchInput').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#customersTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    $('#resetSearch').on('click', function() {
        $('#searchInput').val('');
        $('#customersTable tbody tr').show();
    });

    // Toast feedback da parametri GET
    function getUrlParam(name) {
        let results = new RegExp('[?&]' + name + '=([^&#]*)').exec(window.location.href);
        return results ? decodeURIComponent(results[1].replace(/\+/g, ' ')) : null;
    }
    function showToast(type, message) {
        let icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
        let bg = type === 'success' ? 'bg-success' : 'bg-danger';
        let toast = `<div class="toast ${bg} text-white" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3500">
            <div class="toast-header ${bg} text-white">
                <i class="fas ${icon} mr-2"></i>
                <strong class="mr-auto">${type === 'success' ? 'Successo' : 'Errore'}</strong>
                <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Chiudi"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="toast-body">${message}</div>
        </div>`;
        $('#toast-container').append(toast);
        $('#toast-container .toast').toast('show');
    }
    // Esempio: ?success=Cliente+creato+con+successo
    let successMsg = getUrlParam('success');
    let errorMsg = getUrlParam('error');
    if (successMsg) showToast('success', successMsg);
    if (errorMsg) showToast('error', errorMsg);

    // Log per debug
    try {
        const customers = <?php echo json_encode($customers ?? null); ?>;
        if (!customers || customers.length === 0) {
            console.warn('DEBUG: Nessun cliente trovato o variabile $customers non valorizzata.', customers);
        } else {
            console.info('DEBUG: Clienti caricati:', customers);
        }
    } catch (e) {
        console.error('DEBUG: Errore nel parsing dei clienti:', e);
    }
});
</script>
</body>
</html>
