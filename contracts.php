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
        </div>    </div>
    <!-- /.content-header -->
    
    <!-- Container per le notifiche toast -->
    <div id="notifications-container" class="position-fixed bottom-0 end-0 p-3"></div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="btn-group">
                        <a href="phone-contract.php" class="btn btn-primary">
                            <i class="fas fa-mobile-alt"></i> Nuovo Contratto Telefonico
                        </a>
                        <a href="energy-contract.php" class="btn btn-success">
                            <i class="fas fa-bolt"></i> Nuovo Contratto Energia
                        </a>
                        <button id="exportCsvBtn" class="btn btn-info">
                            <i class="fas fa-file-csv"></i> Esporta CSV
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Elenco Contratti</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" id="searchInput" class="form-control float-right" placeholder="Cerca contratto...">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default" id="searchButton">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                
                <div class="card-body">
                    <div class="btn-group mb-3">
                        <button type="button" class="btn btn-primary" data-filter="all">Tutti</button>
                        <button type="button" class="btn btn-default" data-filter="phone">Telefonia</button>
                        <button type="button" class="btn btn-default" data-filter="energy">Energia</button>
                        <button type="button" class="btn btn-default" data-filter="active">Attivi</button>
                        <button type="button" class="btn btn-default" data-filter="pending">In attesa</button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="contractsTable">
                            <thead>
                                <tr>
                                    <th style="width: 50px">ID</th>
                                    <th>Cliente</th>
                                    <th>Tipo</th>
                                    <th>Provider</th>
                                    <th>Data stipula</th>
                                    <th>Stato</th>
                                    <th style="width: 120px">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dati dinamici -->
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <nav aria-label="Navigazione pagine">
                            <ul class="pagination" id="pagination">
                                <!-- Paginazione dinamica -->
                            </ul>
                        </nav>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.container-fluid -->
        
        <!-- Modal Dettaglio Contratto -->
        <div class="modal fade" id="contractDetailsModal" tabindex="-1" role="dialog" aria-labelledby="contractDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="contractDetailsModalLabel">Dettaglio Contratto</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Informazioni Contratto</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>ID</th>
                                        <td id="contractId"></td>
                                    </tr>
                                    <tr>
                                        <th>Tipo</th>
                                        <td id="contractType"></td>
                                    </tr>
                                    <tr>
                                        <th>Provider</th>
                                        <td id="contractProvider"></td>
                                    </tr>
                                    <tr>
                                        <th>Data Stipula</th>
                                        <td id="contractDate"></td>
                                    </tr>
                                    <tr>
                                        <th>Scadenza</th>
                                        <td id="contractExpiration"></td>
                                    </tr>
                                    <tr>
                                        <th>Canone Mensile</th>
                                        <td id="contractMonthlyFee"></td>
                                    </tr>
                                    <tr>
                                        <th>Stato</th>
                                        <td id="contractStatus"></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>Informazioni Cliente</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Nome</th>
                                        <td id="clientName"></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td id="clientEmail"></td>
                                    </tr>
                                    <tr>
                                        <th>Telefono</th>
                                        <td id="clientPhone"></td>
                                    </tr>
                                    <tr>
                                        <th>Indirizzo</th>
                                        <td id="clientAddress"></td>
                                    </tr>
                                    <tr>
                                        <th>Città</th>
                                        <td id="clientCity"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-12">
                                <h5>Dettagli Specifici</h5>
                                <div id="specificDetailsPhone" style="display: none;">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Numero di Telefono</th>
                                            <td id="phoneNumber"></td>
                                        </tr>
                                        <tr>
                                            <th>Codice Migrazione</th>
                                            <td id="migrationCode"></td>
                                        </tr>
                                    </table>
                                </div>
                                <div id="specificDetailsEnergy" style="display: none;">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Indirizzo Attivazione</th>
                                            <td id="activationAddress"></td>
                                        </tr>
                                        <tr>
                                            <th>Indirizzo Installazione</th>
                                            <td id="installationAddress"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-12">
                                <h5>Allegati</h5>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nome File</th>
                                            <th>Dimensione</th>
                                            <th>Data Caricamento</th>
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
                    <div class="modal-footer">
                        <a href="#" class="btn btn-primary" id="editContractBtn">Modifica</a>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
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
                        <input type="hidden" id="deleteContractId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteContract">Elimina</button>
                    </div>
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
<script src="assets/js/notifications.js"></script>
';
include 'includes/footer.php';
?>
