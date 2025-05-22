<?php
// API: POST /api/v1/upload
require_once __DIR__ . '/../../../src/models/Attachment.php';
use CoreSuite\Models\Attachment;
header('Content-Type: application/json');
require_once __DIR__ . '/jwt_middleware.php';
api_require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Metodo non consentito']);
    exit;
}
$contractId = $_POST['contract_id'] ?? null;
if (!$contractId || !isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Dati mancanti']);
    exit;
}
$original = $_FILES['file']['name'];
$size = $_FILES['file']['size'];
$type = strtolower(pathinfo($original, PATHINFO_EXTENSION));
if ($size > 5*1024*1024) {
    echo json_encode(['error' => 'File troppo grande']);
    exit;
}
if (!in_array($type, ['pdf','jpg','jpeg','png','doc'])) {
    echo json_encode(['error' => 'Tipo file non consentito']);
    exit;
}
$safeName = uniqid('att_', true) . '.' . $type;
$dir = __DIR__ . '/../../../uploads/' . intval($contractId);
if (!is_dir($dir)) mkdir($dir, 0755, true);
$dest = $dir . '/' . $safeName;
if (move_uploaded_file($_FILES['file']['tmp_name'], $dest)) {
    Attachment::create([
        'contract_id' => $contractId,
        'filename' => $safeName,
        'original_name' => $original,
        'file_size' => $size
    ]);
    echo json_encode(['success' => true, 'filename' => $safeName]);
} else {
    echo json_encode(['error' => 'Errore upload']);
}
