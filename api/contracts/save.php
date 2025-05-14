<?php
/**
 * API per salvare (creare o aggiornare) un contratto
 * @endpoint: /api/contracts/save.php
 * @method: POST
 * @payload: JSON o FormData con i dati del contratto
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/ApiController.php';
require_once '../../controllers/ContractController.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Utente non autenticato']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Metodo non supportato']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $db->beginTransaction();
    
    // Controlla se ci sono dati in POST (FormData) o dati JSON
    if (!empty($_POST)) {
        // Dati FormData dal form modale
        
        // Controlla che i dati necessari siano presenti
        if (!isset($_POST['client-id']) || !isset($_POST['provider']) || !isset($_POST['start-date'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Dati mancanti o non validi']);
            exit;
        }
        
        // Dati del contratto
        $contractData = [
            'id' => isset($_POST['contract-id']) && $_POST['contract-id'] > 0 ? intval($_POST['contract-id']) : null,
            'client_id' => intval($_POST['client-id']),
            'type' => 'phone',
            'provider' => $_POST['provider'],
            'start_date' => $_POST['start-date'],
            'end_date' => !empty($_POST['end-date']) ? $_POST['end-date'] : null,
            'reference_number' => isset($_POST['contract-number']) ? $_POST['contract-number'] : null,
            'note' => isset($_POST['note']) ? $_POST['note'] : null,
            'created_by' => $_SESSION['user_id']
        ];
        
        // Salvataggio del contratto
        if ($contractData['id']) {
            // Aggiornamento
            $stmt = $db->prepare("
                UPDATE contracts SET 
                    client_id = ?, 
                    provider = ?, 
                    start_date = ?, 
                    end_date = ?, 
                    reference_number = ?, 
                    note = ?, 
                    updated_at = NOW() 
                WHERE id = ? AND type = 'phone'
            ");
            $result = $stmt->execute([
                $contractData['client_id'],
                $contractData['provider'],
                $contractData['start_date'],
                $contractData['end_date'],
                $contractData['reference_number'],
                $contractData['note'],
                $contractData['id']
            ]);
            
            if (!$result) {
                throw new Exception("Errore durante l'aggiornamento del contratto");
            }
            $contractId = $contractData['id'];
        } else {
            // Nuova creazione
            $stmt = $db->prepare("
                INSERT INTO contracts 
                    (client_id, type, provider, start_date, end_date, reference_number, note, created_by, created_at) 
                VALUES 
                    (?, 'phone', ?, ?, ?, ?, ?, ?, NOW())
            ");
            $result = $stmt->execute([
                $contractData['client_id'],
                $contractData['provider'],
                $contractData['start_date'],
                $contractData['end_date'],
                $contractData['reference_number'],
                $contractData['note'],
                $contractData['created_by']
            ]);
            
            if (!$result) {
                throw new Exception("Errore durante la creazione del contratto");
            }
            $contractId = $db->lastInsertId();
        }
        
        // Gestione degli allegati
        if (!empty($_FILES['attachments']['name'][0])) {
            $uploadDir = '../../uploads/contracts/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            for ($i = 0; $i < count($_FILES['attachments']['name']); $i++) {
                $fileName = $_FILES['attachments']['name'][$i];
                $tmpName = $_FILES['attachments']['tmp_name'][$i];
                $fileSize = $_FILES['attachments']['size'][$i];
                $fileType = $_FILES['attachments']['type'][$i];
                
                // Genera un nome di file univoco
                $newFileName = uniqid() . '_' . $fileName;
                $targetFilePath = $uploadDir . $newFileName;
                
                // Sposta il file
                if (move_uploaded_file($tmpName, $targetFilePath)) {
                    // Inserisci nel database
                    $attachStmt = $db->prepare("
                        INSERT INTO attachments 
                            (contract_id, client_id, filename, filepath, filesize, filetype, uploaded_by, uploaded_at) 
                        VALUES 
                            (?, ?, ?, ?, ?, ?, ?, NOW())
                    ");
                    $attachStmt->execute([
                        $contractId,
                        $contractData['client_id'],
                        $fileName,
                        'uploads/contracts/' . $newFileName,
                        $fileSize,
                        $fileType,
                        $_SESSION['user_id']
                    ]);
                }
            }
        }
    } else {
        // Dati JSON dal form precedente
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['client-name']) || !isset($data['start-date'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Dati mancanti o non validi']);
            exit;
        }
        
        $contractController = new ContractController($db);
        
        // Salva il contratto (implementazione base, da adattare secondo la struttura del DB)
        $result = $contractController->saveContract([
            'client_name' => $data['client-name'],
            'start_date' => $data['start-date'],
            'end_date' => $data['end-date'] ?? null,
            'created_by' => $_SESSION['user_id']
        ]);
        
        if (!$result['success']) {
            throw new Exception($result['message'] ?? 'Errore nel salvataggio');
        }
        
        $contractId = $result['contract_id'] ?? null;
    }
    
    // Log dell'operazione
    $logStmt = $db->prepare("
        INSERT INTO activity_logs 
            (user_id, action, entity_type, entity_id, details) 
        VALUES 
            (?, ?, 'contract', ?, ?)
    ");
    $logStmt->execute([
        $_SESSION['user_id'],
        isset($contractData['id']) ? 'update' : 'create',
        $contractId,
        json_encode([
            'client_id' => $contractData['client_id'] ?? null,
            'type' => 'phone',
            'provider' => $contractData['provider'] ?? null
        ])
    ]);
    
    $db->commit();
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true, 
        'message' => isset($contractData['id']) ? 'Contratto aggiornato con successo' : 'Contratto creato con successo',
        'contract_id' => $contractId
    ]);
    
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Errore: ' . $e->getMessage()]);
}
