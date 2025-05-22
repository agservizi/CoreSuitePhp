<?php
namespace CoreSuite\Controllers;

use CoreSuite\Models\Attachment;

class AttachmentController
{
    public function upload()
    {
        $contractId = $_POST['contract_id'] ?? null;
        if (!$contractId || !isset($_FILES['files'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Dati mancanti']);
            exit;
        }
        $results = [];
        foreach ($_FILES['files']['tmp_name'] as $i => $tmpName) {
            $original = $_FILES['files']['name'][$i];
            $size = $_FILES['files']['size'][$i];
            $type = strtolower(pathinfo($original, PATHINFO_EXTENSION));
            if ($size > 5*1024*1024) {
                $results[] = ['file' => $original, 'error' => 'File troppo grande'];
                continue;
            }
            if (!in_array($type, ['pdf','jpg','jpeg','png','doc'])) {
                $results[] = ['file' => $original, 'error' => 'Tipo file non consentito'];
                continue;
            }
            $safeName = uniqid('att_', true) . '.' . $type;
            $dir = __DIR__ . '/../../../uploads/' . intval($contractId);
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $dest = $dir . '/' . $safeName;
            if (move_uploaded_file($tmpName, $dest)) {
                Attachment::create([
                    'contract_id' => $contractId,
                    'filename' => $safeName,
                    'original_name' => $original,
                    'file_size' => $size
                ]);
                $results[] = ['file' => $original, 'success' => true];
            } else {
                $results[] = ['file' => $original, 'error' => 'Errore upload'];
            }
        }
        header('Content-Type: application/json');
        echo json_encode($results);
    }

    public function list($contractId)
    {
        session_start();
        $userId = $_SESSION['user_id'] ?? null;
        $role = $_SESSION['role'] ?? null;
        $attachments = \CoreSuite\Models\Attachment::allByContractForUser($contractId, $userId, $role);
        if ($role !== 'admin') {
            // Verifica che l'user sia proprietario del contratto
            $db = \CoreSuite\Models\Attachment::getDb();
            $cstmt = $db->prepare('SELECT user_id FROM contracts WHERE id = ?');
            $cstmt->execute([$contractId]);
            $owner = $cstmt->fetchColumn();
            if ($owner != $userId) {
                require __DIR__ . '/../views/errors/403.php';
                exit;
            }
        }
        require __DIR__ . '/../views/attachments/list.php';
    }
}
