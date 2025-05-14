<?php
require_once 'vendor/autoload.php';
require_once 'config/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $mailer;
    private $from;
    private $fromName;
    
    public function __construct() {
        $config = require_once 'config/config.php';
        
        $this->mailer = new PHPMailer(true);
        $this->from = $config['email']['from'];
        $this->fromName = $config['email']['from_name'];
        
        // Configurazione server
        $this->mailer->isSMTP();
        $this->mailer->Host = $config['email']['host'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $config['email']['username'];
        $this->mailer->Password = $config['email']['password'];
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = $config['email']['port'];
        $this->mailer->CharSet = 'UTF-8';
        
        // Mittente predefinito
        $this->mailer->setFrom($this->from, $this->fromName);
    }
    
    public function sendPasswordResetEmail($email, $token) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email);
            
            $resetLink = "https://app.coresuite.it/reset-password.php?token=" . $token;
            
            $this->mailer->isHTML(true);
            $this->mailer->Subject = "CoreSuite - Ripristino password";
            $this->mailer->Body = $this->getPasswordResetTemplate($resetLink);
            $this->mailer->AltBody = "Clicca sul seguente link per reimpostare la tua password: " . $resetLink;
            
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Errore invio email: " . $e->getMessage());
            return false;
        }
    }
    
    public function sendNewAccountEmail($email, $password) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email);
            
            $loginLink = "https://app.coresuite.it/login.php";
            
            $this->mailer->isHTML(true);
            $this->mailer->Subject = "CoreSuite - Nuovo account";
            $this->mailer->Body = $this->getNewAccountTemplate($email, $password, $loginLink);
            $this->mailer->AltBody = "Benvenuto su CoreSuite! Email: " . $email . " Password: " . $password;
            
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Errore invio email: " . $e->getMessage());
            return false;
        }
    }
    
    public function sendNotification($email, $subject, $message) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email);
            
            $this->mailer->isHTML(true);
            $this->mailer->Subject = "CoreSuite - " . $subject;
            $this->mailer->Body = $this->getNotificationTemplate($subject, $message);
            $this->mailer->AltBody = $message;
            
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Errore invio email: " . $e->getMessage());
            return false;
        }
    }
    
    private function getPasswordResetTemplate($resetLink) {
        return '
        <div style="max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
            <div style="text-align: center; margin-bottom: 20px;">
                <h1 style="color: #3c8dbc; margin: 0;"><span style="font-weight: bold;">Core</span><span style="font-weight: normal;">Suite</span></h1>
            </div>
            <div style="background-color: #f8f9fa; border-radius: 5px; padding: 20px; border-top: 3px solid #3c8dbc;">
                <h2 style="margin-top: 0; color: #333;">Ripristino password</h2>
                <p style="color: #555;">Hai richiesto di reimpostare la tua password. Clicca sul pulsante qui sotto per procedere:</p>
                <div style="text-align: center; margin: 30px 0;">
                    <a href="' . $resetLink . '" style="background-color: #3c8dbc; color: #ffffff; text-decoration: none; padding: 10px 20px; border-radius: 3px; display: inline-block;">Reimposta password</a>
                </div>
                <p style="color: #555; font-size: 0.9em;">Se non hai richiesto il ripristino della password, puoi ignorare questa email.</p>
                <p style="color: #555; font-size: 0.9em;">Il link scadrà tra 1 ora per motivi di sicurezza.</p>
            </div>
            <div style="text-align: center; margin-top: 20px; color: #888; font-size: 0.8em;">
                <p>&copy; ' . date('Y') . ' CoreSuite. Tutti i diritti riservati.</p>
            </div>
        </div>';
    }
    
    private function getNewAccountTemplate($email, $password, $loginLink) {
        return '
        <div style="max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
            <div style="text-align: center; margin-bottom: 20px;">
                <h1 style="color: #3c8dbc; margin: 0;"><span style="font-weight: bold;">Core</span><span style="font-weight: normal;">Suite</span></h1>
            </div>
            <div style="background-color: #f8f9fa; border-radius: 5px; padding: 20px; border-top: 3px solid #3c8dbc;">
                <h2 style="margin-top: 0; color: #333;">Benvenuto su CoreSuite!</h2>
                <p style="color: #555;">È stato creato un nuovo account per te. Ecco le tue credenziali di accesso:</p>
                <div style="background-color: #e9ecef; padding: 15px; border-radius: 3px; margin: 20px 0;">
                    <p style="margin: 5px 0;"><strong>Email:</strong> ' . $email . '</p>
                    <p style="margin: 5px 0;"><strong>Password:</strong> ' . $password . '</p>
                </div>
                <p style="color: #555;">Per accedere al sistema, clicca sul pulsante qui sotto:</p>
                <div style="text-align: center; margin: 30px 0;">
                    <a href="' . $loginLink . '" style="background-color: #3c8dbc; color: #ffffff; text-decoration: none; padding: 10px 20px; border-radius: 3px; display: inline-block;">Accedi a CoreSuite</a>
                </div>
                <p style="color: #555; font-size: 0.9em;">Ti consigliamo di cambiare la password al primo accesso.</p>
            </div>
            <div style="text-align: center; margin-top: 20px; color: #888; font-size: 0.8em;">
                <p>&copy; ' . date('Y') . ' CoreSuite. Tutti i diritti riservati.</p>
            </div>
        </div>';
    }
    
    private function getNotificationTemplate($subject, $message) {
        return '
        <div style="max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
            <div style="text-align: center; margin-bottom: 20px;">
                <h1 style="color: #3c8dbc; margin: 0;"><span style="font-weight: bold;">Core</span><span style="font-weight: normal;">Suite</span></h1>
            </div>
            <div style="background-color: #f8f9fa; border-radius: 5px; padding: 20px; border-top: 3px solid #3c8dbc;">
                <h2 style="margin-top: 0; color: #333;">' . $subject . '</h2>
                <div style="color: #555; margin: 20px 0;">
                    ' . $message . '
                </div>
            </div>
            <div style="text-align: center; margin-top: 20px; color: #888; font-size: 0.8em;">
                <p>&copy; ' . date('Y') . ' CoreSuite. Tutti i diritti riservati.</p>
            </div>
        </div>';
    }
}
?>
