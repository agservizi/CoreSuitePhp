<?php
// API per salvataggio contratto (telefonia/energia)
require_once '../classes/Database.php';
$db = Database::getInstance();

header('Content-Type: application/json');

try {
    $provider = $_POST['provider'] ?? '';
    $client_id = intval($_POST['client_id'] ?? 0);
    $activation_address = $_POST['activation_address'] ?? '';
    $installation_address = $_POST['installation_address'] ?? '';
    $migration_code = $_POST['migration_code'] ?? '';
    $phone_number = $_POST['phone_number'] ?? null;
    $user_id = $_SESSION['user_id'] ?? 1; // fallback demo
    $contract_type = in_array($provider, ['Fastweb', 'Windtre', 'Pianeta Fibra']) ? 'phone' : 'energy';

    // Validazione base
    if (!$provider || !$client_id || !$activation_address || !$migration_code) {
        echo json_encode(['success' => false, 'error' => 'Compila tutti i campi obbligatori']);
        exit;
    }

    $stmt = $db->prepare("INSERT INTO contracts (client_id, contract_type, provider, activation_address, installation_address, migration_code, phone_number, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$client_id, $contract_type, $provider, $activation_address, $installation_address, $migration_code, $phone_number, $user_id]);
    $contract_id = $db->lastInsertId();

    // Gestione upload file
    if (!empty($_FILES['attachments']['name'][0])) {
        foreach ($_FILES['attachments']['tmp_name'] as $i => $tmp_name) {
            if ($_FILES['attachments']['error'][$i] === UPLOAD_ERR_OK) {
                $name = $_FILES['attachments']['name'][$i];
                $size = $_FILES['attachments']['size'][$i];
                if ($size > 5*1024*1024) continue;
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                if (!in_array($ext, ['pdf','jpg','jpeg','png'])) continue;
                $destDir = '../uploads/' . date('Y/m/');
                if (!is_dir($destDir)) mkdir($destDir, 0777, true);
                $newName = uniqid('doc-', true) . '.' . $ext;
                $dest = $destDir . $newName;
                if (move_uploaded_file($tmp_name, $dest)) {
                    $stmt = $db->prepare("INSERT INTO attachments (contract_id, file_name, file_path, file_size, uploaded_by) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$contract_id, $name, date('Y/m/') . $newName, $size, $user_id]);
                }
            }
        }
    }

    echo json_encode(['success' => true, 'contract_id' => $contract_id]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
