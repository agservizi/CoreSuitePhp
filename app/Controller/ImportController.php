<?php
namespace App\Controller;
use App\Model\CustomerModel;

class ImportController extends BaseController {
    public function importForm($error = '', $success = '') {
        include __DIR__ . '/../../View/import/form.php';
    }
    public function import() {
        $error = '';
        $success = '';
        // CSRF check
        if (!csrf_check($_POST['csrf_token'] ?? '')) {
            $this->importForm('Token CSRF non valido');
            return;
        }
        if (!isset($_FILES['csv']) || $_FILES['csv']['error'] !== UPLOAD_ERR_OK) {
            $error = 'Errore upload file.';
            $this->importForm($error);
            return;
        }
        $file = $_FILES['csv']['tmp_name'];
        $handle = fopen($file, 'r');
        if (!$handle) {
            $error = 'Impossibile leggere il file.';
            $this->importForm($error);
            return;
        }
        $header = fgetcsv($handle);
        $model = new CustomerModel();
        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);
            if (!$data) continue;
            // Validazione base
            if (empty($data['nome']) || empty($data['cognome'])) continue;
            $model->create($data);
            $count++;
        }
        fclose($handle);
        $success = "Importazione completata: $count clienti.";
        $this->importForm('', $success);
    }
}
