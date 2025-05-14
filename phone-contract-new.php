<?php
$pageTitle = 'Nuovo Contratto Telefonia';
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
                        <li class="breadcrumb-item"><a href="phone-manager.php">Telefonia</a></li>
                        <li class="breadcrumb-item active">Nuovo Contratto</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card card-default">
                <div class="card-body">
                    <form id="phoneContractForm" enctype="multipart/form-data">
                        <div class="row">
                            <!-- Selezione Provider -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="provider">Provider</label>
                                    <select class="form-control select2" id="provider" name="provider" required>
                                        <option value="">Seleziona provider</option>
                                        <option value="Fastweb">Fastweb</option>
                                        <option value="Windtre">Windtre</option>
                                        <option value="Pianeta Fibra">Pianeta Fibra</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Ricerca Cliente -->
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="client_id">Cliente</label>
                                    <select class="form-control select2" id="client_id" name="client_id" required>
                                        <option value="">Cerca cliente...</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Dati Cliente -->
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Dati Cliente</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="clientName">Nome</label>
                                            <input type="text" class="form-control" id="clientName" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="clientFiscalCode">Codice Fiscale</label>
                                            <input type="text" class="form-control" id="clientFiscalCode" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="clientPhone">Telefono</label>
                                            <input type="text" class="form-control" id="clientPhone" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dati Contratto -->
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Dati Contratto</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="activation_address">Indirizzo di Attivazione</label>
                                            <input type="text" class="form-control" id="activation_address" name="activation_address" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="installation_address">Indirizzo di Installazione</label>
                                            <input type="text" class="form-control" id="installation_address" name="installation_address">
                                            <small class="form-text text-muted">Compilare solo se diverso dall'indirizzo di attivazione</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="migration_code">Codice di Migrazione</label>
                                            <input type="text" class="form-control" id="migration_code" name="migration_code" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone_number">Numero di Telefono</label>
                                            <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="start_date">Data Inizio</label>
                                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="monthly_fee">Canone Mensile (€)</label>
                                            <input type="number" step="0.01" class="form-control" id="monthly_fee" name="monthly_fee" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Upload Documenti -->
                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Documenti</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="attachments">Carica Documenti</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="attachments" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png">
                                        <label class="custom-file-label" for="attachments">Scegli file...</label>
                                    </div>
                                    <small class="form-text text-muted">Puoi caricare fino a 5 file (max 5MB ciascuno)</small>
                                </div>
                                <div id="fileList" class="mt-2"></div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <a href="phone-manager.php" class="btn btn-secondary mr-2">Annulla</a>
                            <button type="submit" class="btn btn-primary">Salva Contratto</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Container per le notifiche toast -->
<div id="notifications-container" class="position-fixed bottom-0 end-0 p-3"></div>

<?php
$extraScripts = '
<script src="assets/js/phone-contract.js"></script>
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
        
        // Mostra la lista dei file
        var fileList = $("#fileList");
        fileList.empty();
        
        if (this.files && this.files.length > 0) {
            var fileNames = $("<ul class=\"list-group\"></ul>");
            for (var i = 0; i < this.files.length; i++) {
                var file = this.files[i];
                var item = $("<li class=\"list-group-item py-1 px-2\"></li>")
                    .text(file.name + " (" + formatFileSize(file.size) + ")");
                fileNames.append(item);
            }
            fileList.append(fileNames);
        }
    });
    
    // Formatta la dimensione dei file
    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + " bytes";
        else if (bytes < 1048576) return (bytes / 1024).toFixed(1) + " KB";
        else return (bytes / 1048576).toFixed(1) + " MB";
    }
});
</script>
';
include "includes/footer.php";
?>
