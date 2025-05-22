<?php
// Endpoint: POST /api/v1/auth/login
require_once __DIR__ . '/../../../src/models/User.php';
use CoreSuite\Models\User;

header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Metodo non consentito']);
    exit;
}
$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Email e password obbligatorie']);
    exit;
}
$user = User::findByEmail($email);
if (!$user || !password_verify($password, $user['password_hash'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Credenziali non valide']);
    exit;
}
if (!$user['is_active']) {
    http_response_code(403);
    echo json_encode(['error' => 'Account disabilitato']);
    exit;
}
// Genera JWT
$config = include __DIR__ . '/../../../config/auth.php';
$payload = [
    'sub' => $user['id'],
    'role' => $user['role'],
    'exp' => time() + $config['jwt_expire']
];
$jwt = jwt_encode($payload, $config['jwt_secret']);
echo json_encode(['token' => $jwt]);

function jwt_encode($payload, $secret) {
    $header = base64url_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $body = base64url_encode(json_encode($payload));
    $sig = hash_hmac('sha256', "$header.$body", $secret, true);
    return "$header.$body." . base64url_encode($sig);
}
function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
