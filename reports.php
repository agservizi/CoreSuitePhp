<?php
/**
 * Pagina per la gestione dei report avanzati
 */
session_start();
require_once 'auth.php';
require_once 'controllers/ContractController.php';
require_once 'controllers/ClientController.php';

$auth = new Auth();
$auth->requireLogin();

$pageTitle = 'Reportistica Avanzata';
include 'includes/header.php';
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?php echo $pageTitle; ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Reportistica</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Sistema di filtri -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Filtri Report</h3>
                </div>
                <div class="card-body">
                    <form id="reportFilters" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tipo di report</label>
                                    <select class="form-control" id="reportType" name="reportType">
                                        <option value="contracts">Contratti</option>
                                        <option value="clients">Clienti</option>
                                        <option value="expiring">Contratti in scadenza</option>
                                        <option value="activity">Attività recenti</option>
                                        <option value="providers">Provider</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Periodo</label>
                                    <select class="form-control" id="timePeriod" name="timePeriod">
                                        <option value="all">Tutti</option>
                                        <option value="today">Oggi</option>
                                        <option value="week">Questa settimana</option>
                                        <option value="month">Questo mese</option>
                                        <option value="year">Quest'anno</option>
                                        <option value="custom">Personalizzato</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Formato di esportazione</label>
                                    <select class="form-control" id="exportFormat" name="exportFormat">
                                        <option value="csv">CSV</option>
                                        <option value="excel">Excel</option>
                                        <option value="pdf">PDF</option>
                                        <option value="json">JSON</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row date-range" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Data inizio</label>
                                    <input type="date" class="form-control" id="startDate" name="startDate">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Data fine</label>
                                    <input type="date" class="form-control" id="endDate" name="endDate">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Filtri aggiuntivi per contratti -->
                        <div class="row contract-filters" style="display: none;">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tipo contratto</label>
                                    <select class="form-control" id="contractType" name="contractType">
                                        <option value="">Tutti</option>
                                        <option value="phone">Telefonia</option>
                                        <option value="energy">Energia</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Provider</label>
                                    <select class="form-control" id="provider" name="provider">
                                        <option value="">Tutti</option>
                                        <?php 
                                        // Popola i provider disponibili dalla configurazione
                                        $providers = [];
                                        foreach(CONFIG['providers'] as $category => $categoryProviders) {
                                            foreach($categoryProviders as $provider) {
                                                if (!in_array($provider, $providers)) {
                                                    $providers[] = $provider;
                                                    echo "<option value=\"".htmlspecialchars($provider)."\">".htmlspecialchars($provider)."</option>";
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Stato</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">Tutti</option>
                                        <option value="active">Attivi</option>
                                        <option value="pending">In attesa</option>
                                        <option value="cancelled">Annullati</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" id="generateReport" class="btn btn-primary">
                                    <i class="fas fa-chart-bar"></i> Genera Report
                                </button>
                                <button type="button" id="exportReport" class="btn btn-success" disabled>
                                    <i class="fas fa-file-export"></i> Esporta
                                </button>
                                <button type="reset" class="btn btn-secondary">
                                    <i class="fas fa-sync"></i> Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Risultati del report -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Risultati Report</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="reportResults">
                        <p class="text-center text-muted">Genera un report per visualizzare i risultati</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
$extraScripts = '
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
<script src="assets/js/reports.js"></script>
';
include 'includes/footer.php';
?>
