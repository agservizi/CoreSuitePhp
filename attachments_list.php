<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}
require_once __DIR__ . '/src/models/Attachment.php';
use CoreSuite\Models\Attachment;
$contractId = isset($_GET['contract_id']) ? intval($_GET['contract_id']) : 0;
$attachments = Attachment::allByContract($contractId);
include __DIR__ . '/src/views/attachments/list.php';
