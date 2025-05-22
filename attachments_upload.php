<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Non autorizzato']);
    exit;
}
require_once __DIR__ . '/src/controllers/AttachmentController.php';
use CoreSuite\Controllers\AttachmentController;

$controller = new AttachmentController();
$controller->upload();
