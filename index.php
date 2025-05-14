<?php
$pageTitle = 'Dashboard';
// Disabilita la cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
session_start();
require_once 'auth.php';

$auth = new Auth();
$auth->requireLogin();

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
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Container per le notifiche toast -->
            <div id="notifications-container" class="position-fixed bottom-0 end-0 p-3"></div>
            
            <!-- Pulsanti delle azioni -->
            <div class="row mb-3">
                <div class="col-12 text-right">
                    <button id="refreshDashboard" class="btn btn-primary mr-2" title="Aggiorna dashboard">
                        <i class="fas fa-sync"></i>
                    </button>
                    <button id="exportDashboardData" class="btn btn-secondary" title="Esporta report">
                        <i class="fas fa-download"></i> Esporta Report
                    </button>
                </div>
            </div>
            
            <!-- Info boxes -->
            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-info elevation-1"><i class="fas fa-file-contract"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Contratti Totali</span>
                            <span class="info-box-number contracts-total">0</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Contratti Attivi</span>
                            <span class="info-box-number contracts-active">0</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Clienti</span>
                            <span class="info-box-number clients-total">0</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">In Attesa</span>
                            <span class="info-box-number contracts-pending">0</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Seconda riga di info box -->
            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-calendar-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Contratti in Scadenza</span>
                            <span class="info-box-number contracts-expiring">0</span>
                            <small>Prossimi 30 giorni</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grafici -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="card card-primary card-outline">
                        <div class="card-header border-0">
                            <h3 class="card-title">
                                <i class="fas fa-chart-pie mr-1"></i>
                                Contratti per Tipo
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="position-relative mb-4">
                                <canvas id="contractsChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card card-info card-outline">
                        <div class="card-header border-0">
                            <h3 class="card-title">
                                <i class="fas fa-chart-bar mr-1"></i>
                                Contratti per Provider
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="position-relative mb-4">
                                <canvas id="providersChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Grafico mensile -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-success card-outline">
                        <div class="card-header border-0">
                            <h3 class="card-title">
                                <i class="fas fa-chart-line mr-1"></i>
                                Andamento Contratti
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="position-relative mb-4">
                                <canvas id="monthlyChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ultimi contratti -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list mr-1"></i>
                                Ultimi Contratti
                            </h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Tipo</th>
                                        <th>Provider</th>
                                        <th>Stato</th>
                                        <th>Data</th>
                                    </tr>
                                </thead>
                                <tbody id="latestContractsTable">
                                    <tr>
                                        <td colspan="6" class="text-center">Caricamento dati...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer clearfix">
                            <a href="contracts.php" class="btn btn-sm btn-secondary float-right">
                                <i class="fas fa-list"></i> Visualizza tutti i contratti
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Scadenze prossime -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                Scadenze nei prossimi 30 giorni
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <?php
                                $db = Database::getInstance();
                                $stmt = $db->query("
                                    SELECT 
                                        c.id, 
                                        CONCAT(cl.first_name, ' ', cl.last_name) as client_name,
                                        c.contract_type as type,
                                        c.provider,
                                        c.expiration_date,
                                        DATEDIFF(c.expiration_date, CURDATE()) as days_remaining
                                    FROM contracts c
                                    JOIN clients cl ON c.client_id = cl.id
                                    WHERE 
                                        c.expiration_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                                        AND c.status = 'active'
                                    ORDER BY c.expiration_date ASC
                                    LIMIT 10
                                ");
                                $expiringContracts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                ?>
                                
                                <?php if (count($expiringContracts) > 0): ?>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Cliente</th>
                                            <th>Tipo</th>
                                            <th>Provider</th>
                                            <th>Scadenza</th>
                                            <th>Giorni rimanenti</th>
                                            <th>Azioni</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($expiringContracts as $contract): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($contract['client_name']); ?></td>
                                            <td><?php echo $contract['type'] === 'phone' ? 'Telefonia' : 'Energia'; ?></td>
                                            <td><?php echo htmlspecialchars($contract['provider']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($contract['expiration_date'])); ?></td>
                                            <td>
                                                <?php
                                                $daysLeft = (int)$contract['days_remaining'];
                                                $badgeClass = $daysLeft <= 7 ? 'danger' : ($daysLeft <= 15 ? 'warning' : 'success');
                                                ?>
                                                <span class="badge bg-<?php echo $badgeClass; ?>"><?php echo $daysLeft; ?> giorni</span>
                                            </td>
                                            <td>
                                                <a href="contract-details.php?id=<?php echo $contract['id']; ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-success send-renewal-notification" data-contract-id="<?php echo $contract['id']; ?>">
                                                    <i class="fas fa-bell"></i> Notifica
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php else: ?>
                                <div class="alert alert-info m-3">
                                    <i class="icon fas fa-info-circle"></i> Nessun contratto in scadenza nei prossimi 30 giorni.
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/countup.js@2.0.8/dist/countUp.min.js"></script>
<script src="assets/js/dashboard.js"></script>
<script src="assets/js/notifications.js"></script>
<script src="assets/js/service-worker-registration.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Gestione notifiche per rinnovi
    document.querySelectorAll(".send-renewal-notification").forEach(button => {
        button.addEventListener("click", function() {
            const contractId = this.getAttribute("data-contract-id");
            const btn = this;
            
            // Disabilita il pulsante durante l\'invio
            btn.disabled = true;
            btn.innerHTML = \'<i class="fas fa-spinner fa-spin"></i>\';
            
            // Invia la notifica
            fetch("api/notifications/send.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    type: "contract_renewal",
                    contract_id: contractId,
                    title: "Promemoria rinnovo contratto",
                    message: "Un contratto sta per scadere. Contatta il cliente per il rinnovo."
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostra feedback positivo
                    btn.classList.remove("btn-success");
                    btn.classList.add("btn-secondary");
                    btn.innerHTML = \'<i class="fas fa-check"></i> Inviata\';
                    
                    // Mostra notifica toast
                    showNotification("Notifica inviata con successo!", "success");
                } else {
                    // Ripristina il pulsante
                    btn.disabled = false;
                    btn.innerHTML = \'<i class="fas fa-bell"></i> Notifica\';
                    
                    // Mostra errore
                    showNotification("Errore: " + (data.message || "Impossibile inviare la notifica"), "error");
                }
            })
            .catch(error => {
                console.error("Errore:", error);
                btn.disabled = false;
                btn.innerHTML = \'<i class="fas fa-bell"></i> Notifica\';
                showNotification("Errore di rete. Riprova più tardi.", "error");
            });
        });
    });
});
</script>
';
include "includes/footer.php";
?>
