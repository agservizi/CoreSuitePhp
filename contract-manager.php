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
            <div class="row mb-3">
                <div class="col-md-6">
                    <button class="btn btn-primary" id="create-new-contract">
                        <i class="fas fa-plus"></i> Crea Nuovo Contratto
                    </button>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" id="filter-contracts" class="form-control" placeholder="Filtra contratti...">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lista Contratti</h3>
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
                            </tr>                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Data Inizio</th>
                                <th>Data Fine</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody id="contracts-table-body">
                            <!-- Dati caricati dinamicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php
$extraScripts = '
<script src="assets/js/contracts.js"></script>
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
