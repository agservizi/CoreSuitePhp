<?php
// API RESTful: /api/v1/customers
require_once __DIR__ . '/../../../src/models/Customer.php';
use CoreSuite\Models\Customer;

header('Content-Type: application/json');
require_once __DIR__ . '/jwt_middleware.php';
api_require_auth();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            $customer = Customer::find(intval($_GET['id']));
            if (!$customer) {
                http_response_code(404);
                echo json_encode(['error' => 'Cliente non trovato']);
                exit;
            }
            echo json_encode($customer);
        } else {
            $customers = Customer::all();
            echo json_encode($customers);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        Customer::create($data);
        echo json_encode(['success' => true]);
        break;
    case 'PUT':
        parse_str(file_get_contents('php://input'), $putData);
        $id = $_GET['id'] ?? $putData['id'] ?? null;
        if (!$id) { http_response_code(400); echo json_encode(['error'=>'ID mancante']); exit; }
        Customer::update($id, $putData);
        echo json_encode(['success' => true]);
        break;
    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if (!$id) { http_response_code(400); echo json_encode(['error'=>'ID mancante']); exit; }
        Customer::delete($id);
        echo json_encode(['success' => true]);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Metodo non consentito']);
}

// Endpoint per ricerca clienti autocomplete
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if (strlen($q) >= 2) {
    $db = Customer::getDb();
    $stmt = $db->prepare('SELECT id, name, surname, fiscal_code, date_of_birth, place_of_birth, province_of_birth, gender, citizenship, email, phone, mobile FROM customers WHERE name LIKE ? OR surname LIKE ? OR fiscal_code LIKE ? LIMIT 10');
    $stmt->execute(["%$q%","%$q%","%$q%"]);
    echo json_encode($stmt->fetchAll());
}
