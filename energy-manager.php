<?php
$pageTitle = 'Gestione Contratti Energia';
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
                        <li class="breadcrumb-item active">Energia</li>
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
                    <button class="btn btn-primary" id="create-new-energy-contract">
                        <i class="fas fa-plus"></i> Crea Nuovo Contratto
                    </button>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" id="filter-energy-contracts" class="form-control" placeholder="Filtra contratti energia...">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                    </div>                </div>
            </div>
            
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Lista Contratti Energia</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Tipo Energia</th>
                                <th>Data Inizio</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody id="energy-contracts-table-body">
                            <!-- Dati caricati dinamicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Container per le notifiche toast -->
<div id="notifications-container" class="position-fixed bottom-0 end-0 p-3"></div>

<?php
$extraScripts = '
<script src="assets/js/energy-contract.js"></script>
<script>
$(function() {
    // Filtraggio tabella contratti energia
    $("#filter-energy-contracts").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#energy-contracts-table-body tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
</script>
';
include "includes/footer.php";
?>
