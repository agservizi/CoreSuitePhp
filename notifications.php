<?php
session_start();
require_once 'auth.php';
require_once 'controllers/NotificationController.php';

$auth = new Auth();
$auth->requireLogin();

$pageTitle = 'Notifiche';
include 'includes/header.php';

// Inizializza il controller
$notificationController = new NotificationController();

// Gestisci le richieste di marcatura come letta
if (isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
    $notificationController->markAsRead($_GET['mark_read'], $_SESSION['user_id']);
    header('Location: notifications.php');
    exit;
}

// Gestisci le richieste di eliminazione
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $notificationController->deleteNotification($_GET['delete'], $_SESSION['user_id']);
    header('Location: notifications.php');
    exit;
}

// Gestisci le richieste di marcatura di tutte come lette
if (isset($_GET['mark_all_read'])) {
    $stmt = Database::getInstance()->prepare("
        UPDATE notifications
        SET is_read = 1, read_at = NOW()
        WHERE user_id = ? AND is_read = 0
    ");
    $stmt->execute([$_SESSION['user_id']]);
    header('Location: notifications.php');
    exit;
}

// Ottieni tutte le notifiche dell'utente
$stmt = Database::getInstance()->prepare("
    SELECT id, title, message, type, is_read, resource_id, resource_type, created_at, read_at
    FROM notifications
    WHERE user_id = ?
    ORDER BY is_read ASC, created_at DESC
    LIMIT 100
");
$stmt->execute([$_SESSION['user_id']]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Conta notifiche non lette
$stmt = Database::getInstance()->prepare("
    SELECT COUNT(*) as count
    FROM notifications
    WHERE user_id = ? AND is_read = 0
");
$stmt->execute([$_SESSION['user_id']]);
$unreadCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
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
                        <li class="breadcrumb-item active">Notifiche</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Container per le notifiche toast -->
            <div id="notifications-container" class="position-fixed bottom-0 end-0 p-3"></div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Tutte le notifiche</h3>
                            
                            <div class="card-tools">
                                <?php if ($unreadCount > 0): ?>
                                <a href="notifications.php?mark_all_read=1" class="btn btn-sm btn-primary mr-2">
                                    <i class="fas fa-check-double"></i> Segna tutte come lette
                                </a>
                                <?php endif; ?>
                                
                                <a href="#" class="btn btn-sm btn-info" id="togglePreferences">
                                    <i class="fas fa-cog"></i> Preferenze
                                </a>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="row mb-4" id="preferencesPanel" style="display: none;">
                                <div class="col-12">
                                    <div class="card card-info">
                                        <div class="card-header">
                                            <h3 class="card-title">Preferenze di notifica</h3>
                                        </div>
                                        <div class="card-body">
                                            <form id="notificationPreferences" method="post" action="api/notifications/preferences.php">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" id="email_notifications" name="email_notifications" checked>
                                                                <label class="custom-control-label" for="email_notifications">Ricevi notifiche via email</label>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" id="browser_notifications" name="browser_notifications" checked>
                                                                <label class="custom-control-label" for="browser_notifications">Ricevi notifiche nel browser</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Notifiche scadenza contratti</label>
                                                            <select class="form-control" name="contract_expiry_days">
                                                                <option value="30,15,7,1">30, 15, 7 e 1 giorno prima</option>
                                                                <option value="15,7,1">15, 7 e 1 giorno prima</option>
                                                                <option value="7,1">7 e 1 giorno prima</option>
                                                                <option value="1">Solo 1 giorno prima</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <button type="submit" class="btn btn-primary">Salva preferenze</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if (count($notifications) === 0): ?>
                                <div class="alert alert-info">
                                    <i class="icon fas fa-info-circle"></i> Non hai notifiche.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%">#</th>
                                                <th style="width: 15%">Data</th>
                                                <th style="width: 20%">Titolo</th>
                                                <th style="width: 40%">Messaggio</th>
                                                <th style="width: 10%">Tipo</th>
                                                <th style="width: 10%">Azioni</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($notifications as $notification): ?>
                                                <?php
                                                // Determina lo stile della riga in base allo stato di lettura
                                                $rowClass = $notification['is_read'] ? '' : 'bg-light font-weight-bold';
                                                
                                                // Determina il colore del badge
                                                $badgeClass = 'badge-secondary';
                                                switch ($notification['type']) {
                                                    case 'success': $badgeClass = 'badge-success'; break;
                                                    case 'info': $badgeClass = 'badge-info'; break;
                                                    case 'warning': $badgeClass = 'badge-warning'; break;
                                                    case 'danger': $badgeClass = 'badge-danger'; break;
                                                }
                                                
                                                // Determina il testo del tipo
                                                $typeText = 'Informazione';
                                                switch ($notification['type']) {
                                                    case 'success': $typeText = 'Successo'; break;
                                                    case 'info': $typeText = 'Informazione'; break;
                                                    case 'warning': $typeText = 'Attenzione'; break;
                                                    case 'danger': $typeText = 'Errore'; break;
                                                }
                                                
                                                // Formatta la data
                                                $createdAt = new DateTime($notification['created_at']);
                                                $formattedDate = $createdAt->format('d/m/Y H:i');
                                                ?>
                                                <tr class="<?php echo $rowClass; ?>">
                                                    <td><?php echo $notification['id']; ?></td>
                                                    <td><?php echo $formattedDate; ?></td>
                                                    <td><?php echo htmlspecialchars($notification['title']); ?></td>
                                                    <td><?php echo htmlspecialchars($notification['message']); ?></td>
                                                    <td><span class="badge <?php echo $badgeClass; ?>"><?php echo $typeText; ?></span></td>
                                                    <td>
                                                        <?php if (!$notification['is_read']): ?>
                                                            <a href="notifications.php?mark_read=<?php echo $notification['id']; ?>" class="btn btn-sm btn-success" title="Segna come letta">
                                                                <i class="fas fa-check"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                        
                                                        <?php if ($notification['resource_id'] && $notification['resource_type'] === 'contract'): ?>
                                                            <a href="contract-details.php?id=<?php echo $notification['resource_id']; ?>" class="btn btn-sm btn-info" title="Visualizza dettagli">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                        
                                                        <a href="notifications.php?delete=<?php echo $notification['id']; ?>" class="btn btn-sm btn-danger" title="Elimina" onclick="return confirm('Sei sicuro di voler eliminare questa notifica?');">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
$extraScripts = '
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Toggle delle preferenze
        document.getElementById("togglePreferences").addEventListener("click", function(e) {
            e.preventDefault();
            const panel = document.getElementById("preferencesPanel");
            panel.style.display = panel.style.display === "none" ? "block" : "none";
        });
        
        // Gestione delle preferenze
        document.getElementById("notificationPreferences")?.addEventListener("submit", function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Preferenze salvate con successo!");
                } else {
                    alert("Errore: " + data.message);
                }
            })
            .catch(error => {
                console.error("Errore:", error);
                alert("Si è verificato un errore durante il salvataggio delle preferenze");
            });
        });
    });
</script>
';
include 'includes/footer.php';
?>
