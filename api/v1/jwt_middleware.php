<?php
// Middleware per autenticazione Bearer JWT
function api_require_auth() {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Token mancante']);
        exit;
    }
    $auth = $headers['Authorization'];
    if (strpos($auth, 'Bearer ') !== 0) {
        http_response_code(401);
        echo json_encode(['error' => 'Formato token non valido']);
        exit;
    }
    $jwt = substr($auth, 7);
    $config = include __DIR__ . '/../../../config/auth.php';
    $payload = jwt_decode($jwt, $config['jwt_secret']);
    if (!$payload || ($payload['exp'] ?? 0) < time()) {
        http_response_code(401);
        echo json_encode(['error' => 'Token scaduto o non valido']);
        exit;
    }
    return $payload;
}
function jwt_decode($jwt, $secret) {
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) return false;
    list($header, $body, $sig) = $parts;
    $valid = hash_hmac('sha256', "$header.$body", $secret, true);
    if (base64url_encode($valid) !== $sig) return false;
    return json_decode(base64_decode(strtr($body, '-_', '+/')), true);
}
function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
