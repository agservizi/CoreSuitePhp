<?php
$pageTitle = 'Dettaglio Cliente';
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
                        <li class="breadcrumb-item"><a href="clients.php">Clienti</a></li>
                        <li class="breadcrumb-item active">Dettaglio Cliente</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content-header -->
    
    <!-- Container per le notifiche toast -->
    <div id="notifications-container" class="position-fixed bottom-0 end-0 p-3"></div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4">
                            <!-- Profile Image -->
                            <div class="card card-primary card-outline">
                                <div class="card-body box-profile">
                                    <div class="text-center">
                                        <img class="profile-user-img img-fluid img-circle" 
                                             src="assets/images/profile-placeholder.png" 
                                             alt="Cliente">
                                    </div>

                                    <h3 class="profile-username text-center" id="clientName">-</h3>
                                    <p class="text-muted text-center" id="clientEmail">-</p>

                                    <ul class="list-group list-group-unbordered mb-3">
                                        <li class="list-group-item">
                                            <b>Contratti totali</b> <a class="float-right" id="contractsCount">-</a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Data registrazione</b> <a class="float-right" id="registrationDate">-</a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Status</b> <a class="float-right" id="clientStatus">-</a>
                                        </li>
                                    </ul>

                                    <a href="#" class="btn btn-primary btn-block" id="editClientBtn">
                                        <i class="fas fa-edit"></i> Modifica Cliente
                                    </a>
                                </div>
                            </div>

                            <!-- About Box -->
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Dettagli</h3>
                                </div>
                                <div class="card-body">
                                    <strong><i class="fas fa-phone mr-1"></i> Telefono</strong>
                                    <p class="text-muted" id="clientPhone">-</p>
                                    <hr>

                                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Indirizzo</strong>
                                    <p class="text-muted" id="clientAddress">-</p>
                                    <hr>

                                    <strong><i class="fas fa-id-card mr-1"></i> Codice Fiscale</strong>
                                    <p class="text-muted" id="clientFiscalCode">-</p>
                                    <hr>

                                    <strong><i class="fas fa-file-alt mr-1"></i> P.IVA</strong>
                                    <p class="text-muted" id="clientVAT">-</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <!-- Contratti cliente -->
                            <div class="card">
                                <div class="card-header p-2">
                                    <ul class="nav nav-pills">
                                        <li class="nav-item"><a class="nav-link active" href="#contracts" data-toggle="tab">Contratti</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#notes" data-toggle="tab">Note</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#attachments" data-toggle="tab">Allegati</a></li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="contracts">
                                            <div class="d-flex justify-content-between mb-3">
                                                <h4>Contratti del cliente</h4>
                                                <div class="btn-group">
                                                    <a href="phone-contract.php?client_id=" class="btn btn-sm btn-primary" id="newPhoneContract">
                                                        <i class="fas fa-mobile-alt"></i> Nuovo contratto telefonico
                                                    </a>
                                                    <a href="energy-contract.php?client_id=" class="btn btn-sm btn-success" id="newEnergyContract">
                                                        <i class="fas fa-bolt"></i> Nuovo contratto energia
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Tipo</th>
                                                            <th>Provider</th>
                                                            <th>Data stipula</th>
                                                            <th>Stato</th>
                                                            <th>Azioni</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="contractsList">
                                                        <tr>
                                                            <td colspan="6" class="text-center">Caricamento contratti...</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="notes">
                                            <div class="post">
                                                <form id="addNoteForm">
                                                    <div class="form-group">
                                                        <label for="noteContent">Aggiungi nota</label>
                                                        <textarea class="form-control" id="noteContent" rows="3" placeholder="Inserisci una nuova nota per questo cliente..."></textarea>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Salva nota</button>
                                                </form>
                                            </div>
                                            <hr>
                                            <div id="clientNotes">
                                                <div class="alert alert-info">Nessuna nota presente per questo cliente.</div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="attachments">
                                            <div class="mb-3">
                                                <div class="form-group">
                                                    <label for="fileDescription">Descrizione allegato</label>
                                                    <input type="text" class="form-control" id="fileDescription" placeholder="Descrizione breve">
                                                </div>
                                                <div class="upload-area" id="uploadArea">
                                                    <i class="fas fa-cloud-upload-alt fa-3x mb-2"></i>
                                                    <p>Trascina un file qui o clicca per selezionare</p>
                                                    <input type="file" id="fileUpload" style="display: none">
                                                </div>
                                                <button id="uploadBtn" class="btn btn-primary mt-2">Carica allegato</button>
                                            </div>
                                            <hr>
                                            <h5>Allegati cliente</h5>
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Descrizione</th>
                                                            <th>Nome file</th>
                                                            <th>Data caricamento</th>
                                                            <th>Azioni</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="attachmentsList">
                                                        <tr>
                                                            <td colspan="4" class="text-center">Nessun allegato trovato</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal Modifica Cliente -->
                    <div class="modal fade" id="editClientModal" tabindex="-1" role="dialog" aria-labelledby="editClientModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h5 class="modal-title" id="editClientModalLabel">Modifica Cliente</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="editClientForm">
                                        <input type="hidden" id="editClientId">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="editFirstName">Nome</label>
                                                    <input type="text" class="form-control" id="editFirstName" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="editLastName">Cognome</label>
                                                    <input type="text" class="form-control" id="editLastName" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="editEmail">Email</label>
                                                    <input type="email" class="form-control" id="editEmail" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="editPhone">Telefono</label>
                                                    <input type="text" class="form-control" id="editPhone">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="editAddress">Indirizzo</label>
                                            <input type="text" class="form-control" id="editAddress">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="editFiscalCode">Codice Fiscale</label>
                                                    <input type="text" class="form-control" id="editFiscalCode">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="editVatNumber">P.IVA</label>
                                                    <input type="text" class="form-control" id="editVatNumber">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="editStatus">Stato</label>
                                            <select class="form-control" id="editStatus">
                                                <option value="1">Attivo</option>
                                                <option value="0">Inattivo</option>
                                            </select>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                    <button type="button" class="btn btn-primary" id="saveClientBtn">Salva modifiche</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal Elimina Contratto -->
                    <div class="modal fade" id="deleteContractModal" tabindex="-1" role="dialog" aria-labelledby="deleteContractModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <h5 class="modal-title" id="deleteContractModalLabel">Elimina Contratto</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p>Sei sicuro di voler eliminare questo contratto? Questa azione non può essere annullata.</p>
                                    <input type="hidden" id="deleteContractId">                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                    <button type="button" class="btn btn-danger" id="confirmDeleteContract">Elimina</button>
                                </div>
                            </div>
                        </div>
                    </div>                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php
$extraScripts = '
<script src="assets/js/client-details.js"></script>
<script src="assets/js/notifications.js"></script>
';
include "includes/footer.php";
?>
