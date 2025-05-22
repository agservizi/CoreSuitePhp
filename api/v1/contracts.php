<?php
// API RESTful: /api/v1/contracts
require_once __DIR__ . '/../../../src/models/Contract.php';
use CoreSuite\Models\Contract;

header('Content-Type: application/json');
require_once __DIR__ . '/jwt_middleware.php';
$user = api_require_auth();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            $contract = Contract::find(intval($_GET['id']));
            if (!$contract) {
                http_response_code(404);
                echo json_encode(['error' => 'Contratto non trovato']);
                exit;
            }
            echo json_encode($contract);
        } else {
            $contracts = Contract::allForUser($user['sub'], $user['role']);
            echo json_encode($contracts);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $data['user_id'] = $user['sub'];
        Contract::create($data);
        echo json_encode(['success' => true]);
        break;
    case 'PUT':
        parse_str(file_get_contents('php://input'), $putData);
        $id = $_GET['id'] ?? $putData['id'] ?? null;
        if (!$id) { http_response_code(400); echo json_encode(['error'=>'ID mancante']); exit; }
        $status = $putData['status'] ?? null;
        if ($status) Contract::updateStatus($id, $status);
        echo json_encode(['success' => true]);
        break;
    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if (!$id) { http_response_code(400); echo json_encode(['error'=>'ID mancante']); exit; }
        Contract::delete($id);
        echo json_encode(['success' => true]);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Metodo non consentito']);
}
