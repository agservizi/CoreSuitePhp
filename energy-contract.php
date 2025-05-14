<?php
$pageTitle = 'Nuovo Contratto Luce/Gas';
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
                        <li class="breadcrumb-item"><a href="energy-manager.php">Energia</a></li>
                        <li class="breadcrumb-item active">Nuovo Contratto</li>
                    </ol>
                </div>
            </div>
        </div>    </div>
    <!-- /.content-header -->
    
    <!-- Container per le notifiche toast -->
    <div id="notifications-container" class="position-fixed bottom-0 end-0 p-3"></div>

    <!-- Main content -->    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-body">
                            <form id="energyContractForm" enctype="multipart/form-data">
                                <div class="row">
                                    <!-- Selezione Provider -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Provider</label>
                                            <select class="form-control" name="provider" required>
                                                <option value="">Seleziona provider</option>
                                                <option value="Enel Energia">Enel Energia</option>
                                                <option value="Fastweb Energia">Fastweb Energia</option>
                                                <option value="A2A Energia">A2A Energia</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Ricerca Cliente -->
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Cliente</label>
                                            <select class="form-control select2" name="client_id" required>
                                                <option value="">Cerca cliente...</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <!-- Dati Cliente (si popola automaticamente) -->
                                    <div class="col-md-12">
                                        <div class="card card-info">
                                            <div class="card-header">
                                                <h3 class="card-title">Dati Cliente</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Nome</label>
                                                            <input type="text" class="form-control" id="clientName" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Codice Fiscale</label>
                                                            <input type="text" class="form-control" id="clientFiscalCode" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Telefono</label>
                                                            <input type="text" class="form-control" id="clientPhone" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <!-- Dati Contratto -->
                                    <div class="col-md-12">
                                        <div class="card card-primary">
                                            <div class="card-header">
                                                <h3 class="card-title">Dati Contratto</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Indirizzo di Attivazione</label>
                                                            <input type="text" class="form-control" name="activation_address" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Indirizzo di Installazione</label>
                                                            <input type="text" class="form-control" name="installation_address">
                                                            <small class="text-muted">Compilare solo se diverso dall'indirizzo di attivazione</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Codice di Migrazione</label>
                                                            <input type="text" class="form-control" name="migration_code" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <!-- Upload Documenti -->
                                    <div class="col-md-12">
                                        <div class="card card-success">
                                            <div class="card-header">
                                                <h3 class="card-title">Documenti</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label>Carica Documenti</label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png">
                                                        <label class="custom-file-label">Scegli file...</label>
                                                    </div>
                                                    <small class="text-muted">Puoi caricare fino a 5 file (max 5MB ciascuno)</small>
                                                </div>
                                                <div id="fileList" class="mt-3"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary float-right">Salva Contratto</button>
                                        <a href="index.php" class="btn btn-secondary">Annulla</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php
$extraScripts = '
<script src="assets/js/energy-contract.js"></script>
<script src="assets/js/notifications.js"></script>
<script>
$(function() {
    // Inizializza Select2
    if ($.fn.select2) {
        $(".select2").select2();
    }
    
    // Visualizza il nome del file selezionato
    $(document).on("change", ".custom-file-input", function() {
        var fileName = $(this).val().split("\\\\").pop();
        $(this).next(".custom-file-label").addClass("selected").html(fileName);
    });
});
</script>
';
include "includes/footer.php";
?>
