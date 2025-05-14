<?php
$pageTitle = 'Gestione Clienti';
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
                        <li class="breadcrumb-item active">Clienti</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content-header -->
    
    <!-- Container per le notifiche toast -->
    <div id="notifications-container" class="position-fixed bottom-0 end-0 p-3"></div>    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Elenco Clienti</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" id="searchInput" class="form-control float-right" placeholder="Cerca cliente...">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default" id="searchButton">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary float-right mr-2" data-toggle="modal" data-target="#addClientModal"><i class="fas fa-user-plus"></i> Nuovo Cliente</button>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered table-hover" id="clientsTable">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Telefono</th>
                                <th>Codice Fiscale</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dati dinamici -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Modal Nuovo Cliente -->
        <div class="modal fade" id="addClientModal" tabindex="-1" role="dialog" aria-labelledby="addClientModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="addClientForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addClientModalLabel">Aggiungi Cliente</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Nome</label>
                                <input type="text" class="form-control" name="first_name" required>
                            </div>
                            <div class="form-group">
                                <label>Cognome</label>
                                <input type="text" class="form-control" name="last_name" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email">
                            </div>
                            <div class="form-group">
                                <label>Telefono</label>
                                <input type="text" class="form-control" name="phone">
                            </div>
                            <div class="form-group">
                                <label>Codice Fiscale</label>
                                <input type="text" class="form-control" name="fiscal_code">
                            </div>
                            <div class="form-group">
                                <label>Indirizzo</label>
                                <input type="text" class="form-control" name="address">
                            </div>
                            <div class="form-group">
                                <label>Città</label>
                                <input type="text" class="form-control" name="city">
                            </div>
                            <div class="form-group">
                                <label>CAP</label>
                                <input type="text" class="form-control" name="postal_code">
                            </div>                            <div class="form-group">
                                <label>Provincia</label>
                                <input type="text" class="form-control" name="province">
                            </div>
                            <div class="form-group">
                                <label>P.IVA</label>
                                <input type="text" class="form-control" name="vat_number">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                            <button type="submit" class="btn btn-primary">Salva</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Modal Elimina Cliente -->
        <div class="modal fade" id="deleteClientModal" tabindex="-1" role="dialog" aria-labelledby="deleteClientModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title" id="deleteClientModalLabel">Elimina Cliente</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>                    </div>
                    <div class="modal-body">
                        <p>Sei sicuro di voler eliminare questo cliente? Questa azione eliminerà anche tutti i contratti e gli allegati associati e non può essere annullata.</p>
                        <input type="hidden" id="deleteClientId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteClient">Elimina</button>
                    </div>
                </div>
            </div>
        </div>
        
<?php
$extraScripts = '
<script src="assets/js/clients.js"></script>
<script src="assets/js/notifications.js"></script>
';
include "includes/footer.php";
?>
