<?php
// Template controller per le notifiche

class NotificationController {
    private $db;
    
    // Costruttore
    public function __construct($db = null) {
        if ($db) {
            $this->db = $db;
        } else {
            // Inizializza connessione database se non fornita
            require_once __DIR__ . '/../config/database.php';
            $this->db = Database::getInstance()->getConnection();
        }
    }
    
    /**
     * Registra una nuova notifica
     */
    public function createNotification($userId, $title, $message, $type = 'info', $resourceId = null, $resourceType = null) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO notifications (user_id, title, message, type, resource_id, resource_type, created_at)
                VALUES (:user_id, :title, :message, :type, :resource_id, :resource_type, NOW())
            ");
            
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':message', $message, PDO::PARAM_STR);
            $stmt->bindParam(':type', $type, PDO::PARAM_STR);
            $stmt->bindParam(':resource_id', $resourceId, PDO::PARAM_STR);
            $stmt->bindParam(':resource_type', $resourceType, PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Errore nella creazione della notifica: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Recupera le notifiche non lette di un utente
     */
    public function getUnreadNotifications($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT id, title, message, type, resource_id, resource_type, created_at
                FROM notifications
                WHERE user_id = :user_id AND is_read = 0
                ORDER BY created_at DESC
            ");
            
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Errore nel recupero delle notifiche: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Segna una notifica come letta
     */
    public function markAsRead($notificationId, $userId) {
        try {
            $stmt = $this->db->prepare("
                UPDATE notifications
                SET is_read = 1, read_at = NOW()
                WHERE id = :id AND user_id = :user_id
            ");
            
            $stmt->bindParam(':id', $notificationId, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Errore nel marcare la notifica come letta: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Elimina una notifica
     */
    public function deleteNotification($notificationId, $userId) {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM notifications
                WHERE id = :id AND user_id = :user_id
            ");
            
            $stmt->bindParam(':id', $notificationId, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Errore nell'eliminazione della notifica: " . $e->getMessage());
            return false;
        }
    }
      /**
     * Crea un'email di notifica
     */
    public function sendEmailNotification($userId, $title, $message) {
        try {
            // Ottieni l'email dell'utente
            $stmt = $this->db->prepare("SELECT email, first_name, last_name FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                error_log("Utente non trovato: $userId");
                return false;
            }
            
            // Carica il servizio email
            require_once __DIR__ . '/../services/EmailService.php';
            $emailService = new EmailService();
            
            // Invia la notifica
            $emailBody = "
                <p>Gentile {$user['first_name']} {$user['last_name']},</p>
                <p>$message</p>
                <p>Puoi accedere alla piattaforma per maggiori dettagli.</p>
                <p>Cordiali saluti,<br>Il team di CoreSuite</p>
            ";
            
            $result = $emailService->sendNotification($user['email'], $title, $emailBody);
            
            // Log dell'operazione
            if ($result) {
                $logStmt = $this->db->prepare("
                    INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $logStmt->execute([
                    $userId,
                    'email_notification',
                    'user',
                    $userId,
                    json_encode(['title' => $title])
                ]);
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Errore nell'invio dell'email: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Crea una notifica di contratto
     */
    public function createContractNotification($userId, $contractId, $contractType, $action) {
        $title = '';
        $message = '';
        $resourceType = 'contract';
        
        switch ($action) {
            case 'created':
                $title = 'Nuovo contratto';
                $message = "Un nuovo contratto $contractType è stato creato";
                $type = 'success';
                break;
            case 'updated':
                $title = 'Contratto aggiornato';
                $message = "Il contratto $contractType è stato aggiornato";
                $type = 'info';
                break;
            case 'expiring':
                $title = 'Contratto in scadenza';
                $message = "Il contratto $contractType sta per scadere";
                $type = 'warning';
                break;
            case 'expired':
                $title = 'Contratto scaduto';
                $message = "Il contratto $contractType è scaduto";
                $type = 'danger';
                break;
            default:
                $title = 'Aggiornamento contratto';
                $message = "C'è un aggiornamento per il contratto $contractType";
                $type = 'info';
        }
        
        return $this->createNotification($userId, $title, $message, $type, $contractId, $resourceType);
    }
}
