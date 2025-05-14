<?php
$pageTitle = 'Gestione Contratti';
include 'includes/header.php';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?php echo $pageTitle; ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Contratti</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="mb-3">
                <button class="btn btn-primary" id="create-new-contract">
                    <i class="fas fa-plus"></i> Crea Nuovo Contratto
                </button>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lista Contratti</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" id="filter-contracts" class="form-control float-right" placeholder="Cerca contratto...">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Data Inizio</th>
                                <th>Data Fine</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody id="contracts-table-body">
                            <!-- Dati caricati dinamicamente -->                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Container per le notifiche toast -->
<div id="notifications-container" class="position-fixed bottom-0 end-0 p-3"></div>

<!-- Modale per creare/modificare un contratto -->
<div class="modal fade" id="contractModal" tabindex="-1" aria-labelledby="contractModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contractModalLabel">Nuovo Contratto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="contract-form">
                    <div class="form-group">
                        <label for="client-name">Cliente</label>
                        <input type="text" class="form-control" id="client-name" name="client-name" required>
                    </div>
                    <div class="form-group">
                        <label for="start-date">Data Inizio</label>
                        <input type="date" class="form-control" id="start-date" name="start-date" required>
                    </div>
                    <div class="form-group">
                        <label for="end-date">Data Fine</label>
                        <input type="date" class="form-control" id="end-date" name="end-date">
                    </div>
                    <button type="submit" class="btn btn-primary">Salva</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$extraScripts = '
<script src="assets/js/contracts-list.js"></script>
<script>
$(function() {
    // Filtraggio tabella contratti
    $("#filter-contracts").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#contracts-table-body tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
</script>
';
include "includes/footer.php";
?>
