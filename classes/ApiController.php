<?php
abstract class ApiController {
    protected $db;
    protected $user;

    public function __construct() {
        session_start();
        $this->db = Database::getInstance();
        $this->checkAuth();
    }

    protected function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            $this->sendResponse(401, ['error' => 'Non autorizzato']);
            exit;
        }
        $this->user = $_SESSION['user_id'];
    }

    protected function sendResponse($statusCode, $data) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function getRequestData() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->sendResponse(400, ['error' => 'JSON non valido']);
        }
        return $data;
    }

    protected function validateRequired($data, $fields) {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $this->sendResponse(400, ['error' => "Campo obbligatorio mancante: $field"]);
            }
        }
    }

    protected function sanitizeInput($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->sanitizeInput($value);
            }
        } else {
            $data = trim(strip_tags($data));
        }
        return $data;
    }

    protected function beginTransaction() {
        $this->db->beginTransaction();
    }

    protected function commit() {
        $this->db->commit();
    }

    protected function rollback() {
        $this->db->rollBack();
    }

    protected function handleFileUpload($file, $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf']) {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new RuntimeException('Parametri non validi');
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('Nessun file caricato');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('File troppo grande');
            default:
                throw new RuntimeException('Errore sconosciuto');
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            throw new RuntimeException('File troppo grande (max 5MB)');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, $allowedTypes)) {
            throw new RuntimeException('Tipo di file non permesso');
        }

        $fileName = sprintf(
            '%s-%s.%s',
            uniqid('doc-', true),
            date('Y-m-d-H-i-s'),
            pathinfo($file['name'], PATHINFO_EXTENSION)
        );

        $uploadPath = __DIR__ . '/../uploads/' . date('Y/m/');
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $uploadPath . $fileName)) {
            throw new RuntimeException('Errore durante il salvataggio del file');
        }

        return [
            'name' => $fileName,
            'path' => date('Y/m/') . $fileName,
            'size' => $file['size'],
            'type' => $mimeType
        ];
    }
}
