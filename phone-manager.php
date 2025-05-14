<?php
$pageTitle = 'Gestione Contratti Telefonia';
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
                        <li class="breadcrumb-item active">Telefonia</li>
                    </ol>
                </div>
            </div>
        </div>    </div>
    <!-- /.content-header -->
    
    <!-- Container per le notifiche toast -->
    <div id="notifications-container" class="position-fixed bottom-0 end-0 p-3"></div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-6">
                    <button class="btn btn-primary" id="create-new-phone-contract">
                        <i class="fas fa-plus"></i> Crea Nuovo Contratto
                    </button>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" id="filter-phone-contracts" class="form-control" placeholder="Filtra contratti telefonia...">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Lista Contratti Telefonia</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Operatore</th>
                                <th>Data Inizio</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody id="phone-contracts-table-body">
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

<!-- Container per le notifiche toast -->
<div id="notifications-container" class="position-fixed bottom-0 end-0 p-3"></div>

<!-- Modale per creare/modificare un contratto telefonico -->
<div class="modal fade" id="phoneContractModal" tabindex="-1" aria-labelledby="phoneContractModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="phoneContractModalLabel">Nuovo Contratto Telefonico</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="phone-contract-form">
                    <input type="hidden" id="contract-id" name="contract-id">
                    
                    <!-- Dati Cliente -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Dati Cliente</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="client-select">Seleziona Cliente</label>
                                <select class="form-control select2" id="client-select" name="client-id" required>
                                    <option value="">Cerca cliente...</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="client-name">Nome Cliente</label>
                                <input type="text" class="form-control" id="client-name" name="client-name" readonly>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client-fiscal-code">Codice Fiscale</label>
                                        <input type="text" class="form-control" id="client-fiscal-code" name="client-fiscal-code" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client-phone">Telefono</label>
                                        <input type="text" class="form-control" id="client-phone" name="client-phone" readonly>
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
                                        <label for="provider">Operatore</label>
                                        <select class="form-control" id="provider" name="provider" required>
                                            <option value="">Seleziona operatore</option>
                                            <option value="Fastweb">Fastweb</option>
                                            <option value="Windtre">Windtre</option>
                                            <option value="Pianeta Fibra">Pianeta Fibra</option>
                                            <option value="Altro">Altro</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contract-number">Numero Contratto</label>
                                        <input type="text" class="form-control" id="contract-number" name="contract-number">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start-date">Data Inizio</label>
                                        <input type="date" class="form-control" id="start-date" name="start-date" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="end-date">Data Fine</label>
                                        <input type="date" class="form-control" id="end-date" name="end-date">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="note">Note</label>
                                <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Allegati -->
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Allegati</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="attachments">Carica Documenti</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="attachments" name="attachments[]" multiple>
                                    <label class="custom-file-label" for="attachments">Scegli file...</label>
                                </div>
                                <small class="form-text text-muted">Puoi caricare fino a 5 file (max 5MB ciascuno)</small>
                                <div id="fileList" class="mt-2 small"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-primary">Salva Contratto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$extraScripts = '
<script>
$(function() {
    // Inizializza Select2
    if ($.fn.select2) {
        $(".select2").select2({
            dropdownParent: $("#phoneContractModal")
        });
    }
    
    // Visualizza il nome del file selezionato
    $(document).on("change", ".custom-file-input", function() {
        var fileName = $(this).val().split("\\\\").pop();
        $(this).next(".custom-file-label").addClass("selected").html(fileName);
    });
});
</script>
<script src="assets/js/notifications.js"></script>
';
include "includes/footer.php";
?>
