<?php
/**
 * API per caricare un allegato per un cliente o un contratto
 * @endpoint: /api/attachments/upload.php
 * @method: POST
 * @param: client_id - L'ID del cliente (richiesto)
 * @param: description - Descrizione dell'allegato
 * @param: file - File da caricare
 * @param: contract_id (opzionale) - ID del contratto associato
 */

// Includi configurazioni e classi necessarie
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/ApiController.php';

// Verifica autenticazione
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Utente non autenticato']);
    exit;
}

// Verifica il metodo della richiesta
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Metodo non supportato']);
    exit;
}

// Ottieni i dati dalla richiesta
$clientId = isset($_POST['client_id']) ? intval($_POST['client_id']) : 0;
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$contractId = isset($_POST['contract_id']) ? intval($_POST['contract_id']) : null;

if ($clientId <= 0 || empty($description)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Parametri mancanti o non validi']);
    exit;
}

// Verifica che sia stato caricato un file
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Nessun file caricato o errore durante il caricamento']);
    exit;
}

try {
    // Inizializza la connessione al database
    $database = new Database();
    $db = $database->getConnection();
    
    // Verifica che il cliente esista
    $checkStmt = $db->prepare("SELECT id, user_id FROM clients WHERE id = ?");
    $checkStmt->execute([$clientId]);
    $client = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$client) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Cliente non trovato']);
        exit;
    }
    
    // Controlla permessi (se l'utente è admin o proprietario del cliente)
    if ($_SESSION['user_role'] !== 'admin' && $client['user_id'] !== $_SESSION['user_id']) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Non hai i permessi per aggiungere allegati a questo cliente']);
        exit;
    }
    
    // Se è specificato un contratto, verifica che esista
    if ($contractId) {
        $contractStmt = $db->prepare("SELECT id FROM contracts WHERE id = ? AND client_id = ?");
        $contractStmt->execute([$contractId, $clientId]);
        if ($contractStmt->rowCount() === 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Contratto non trovato o non associato al cliente specificato']);
            exit;
        }
    }
    
    // Prepara la directory di upload
    $uploadDir = '../../uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Genera un nome di file univoco
    $filename = $_FILES['file']['name'];
    $fileExt = pathinfo($filename, PATHINFO_EXTENSION);
    $uniqueName = uniqid('file_') . '_' . date('Ymd') . '.' . $fileExt;
    $filePath = $uploadDir . $uniqueName;
    
    // Sposta il file nella directory di upload
    if (!move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Errore durante il salvataggio del file']);
        exit;
    }
    
    // Salva le informazioni sull'allegato nel database
    $stmt = $db->prepare("
        INSERT INTO attachments (client_id, contract_id, description, filename, filepath, filesize, filetype, created_at, user_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)
    ");
    
    $result = $stmt->execute([
        $clientId,
        $contractId,
        $description,
        $filename,
        $uniqueName,
        $_FILES['file']['size'],
        $_FILES['file']['type'],
        $_SESSION['user_id']    ]);
    
    if ($result) {
        $attachmentId = $db->lastInsertId();
        
        // Log dell'operazione
        $logStmt = $db->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details) VALUES (?, ?, ?, ?, ?)");
        $logStmt->execute([
            $_SESSION['user_id'],
            'create',
            'attachment',
            $attachmentId,
            json_encode([
                'message' => 'Allegato caricato',
                'client_id' => $clientId,
                'contract_id' => $contractId,
                'attachment_id' => $attachmentId,
                'filename' => $filename
            ])
        ]);
        
        // Notifica all'utente
        try {
            $entityType = $contractId ? 'contratto' : 'cliente';
            $entityId = $contractId ? $contractId : $clientId;
            
            // Invia una notifica in tempo reale se è un contratto
            if ($contractId) {
                // Potrebbe essere implementato nel futuro con API di notifica
                // Il codice qui verrebbe eseguito quando le notifiche push saranno completamente implementate
            }
        } catch (Exception $notifyEx) {
            // Ignora errori di notifica
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'File caricato con successo',
            'attachment' => [
                'id' => $attachmentId,
                'filename' => $filename,
                'filepath' => $uniqueName,
                'filesize' => $_FILES['file']['size'],
                'created_at' => date('Y-m-d H:i:s'),
                'description' => $description
            ]
        ]);
    } else {
        // Se l'inserimento fallisce, elimina il file caricato
        unlink($filePath);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Errore durante il salvataggio delle informazioni sul file']);
    }
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Errore interno: ' . $e->getMessage()]);
}
