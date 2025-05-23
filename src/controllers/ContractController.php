<?php
namespace CoreSuite\Controllers;

use CoreSuite\Models\Contract;

class ContractController
{
    public function index()
    {
        $contracts = Contract::allForUser($_SESSION['user_id'], $_SESSION['role']);
        require __DIR__ . '/../views/contracts/index.php';
    }    public function show($id)
    {
        $contract = Contract::find($id);
        if (!$contract || ($_SESSION['role'] !== 'admin' && $contract['user_id'] != $_SESSION['user_id'])) {
            require __DIR__ . '/../views/errors/403.php';
            exit;
        }
        
        // Otteniamo i dati delle entità correlate
        $customer = null;
        $provider = null;
        
        if ($contract['customer_id']) {
            $customer = \CoreSuite\Models\Customer::find($contract['customer_id']);
        }
        
        if ($contract['provider']) {
            $provider = \CoreSuite\Models\Provider::find($contract['provider']);
        }
        
        // Otteniamo eventuali allegati
        $attachments = [];
        $db = Contract::getDb();
        $stmt = $db->prepare('SELECT * FROM attachments WHERE contract_id = ?');
        $stmt->execute([$id]);
        $attachments = $stmt->fetchAll();
        
        require __DIR__ . '/../views/contracts/show.php';
    }public function create()
    {
        // Prepariamo i dati per la vista
        $customers = \CoreSuite\Models\Customer::all();
        $providers = \CoreSuite\Models\Provider::all();
        require __DIR__ . '/../views/contracts/create.php';
    }public function store()
    {
        $data = [
            'customer_id' => $_POST['customer_id'] ?? null,
            'provider' => $_POST['provider'] ?? null,
            'type' => $_POST['type'] ?? null,
            'status' => $_POST['status'] ?? null,
            'user_id' => $_SESSION['user_id'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
            'extra_data' => isset($_POST['extra']) ? json_encode($_POST['extra']) : null
        ];
        if (!$data['customer_id'] || !$data['provider'] || !$data['type'] || !$data['status']) {
            $error = 'Tutti i campi sono obbligatori.';
            // Prepariamo i dati per la vista
            $customers = \CoreSuite\Models\Customer::all();
            $providers = \CoreSuite\Models\Provider::all();
            require __DIR__ . '/../views/contracts/create.php';
            return;
        }
        \CoreSuite\Models\Contract::create($data);
        header('Location: /contracts.php');
        exit;
    }

    public function edit($id)
    {
        $contract = Contract::find($id);
        if ($_SESSION['role'] !== 'admin' && $contract['user_id'] != $_SESSION['user_id']) {
            require __DIR__ . '/../views/errors/403.php';
            exit;
        }
        require __DIR__ . '/../views/contracts/edit.php';
    }

    public function update($id)
    {
        $contract = Contract::find($id);
        if ($_SESSION['role'] !== 'admin' && $contract['user_id'] != $_SESSION['user_id']) {
            require __DIR__ . '/../views/errors/403.php';
            exit;
        }
        $status = $_POST['status'] ?? null;
        if (!$status) {
            $error = 'Stato obbligatorio.';
            require __DIR__ . '/../views/contracts/edit.php';
            return;
        }
        \CoreSuite\Models\Contract::updateStatus($id, $status);
        header('Location: /contracts.php');
        exit;
    }

    public function delete($id)
    {
        $contract = Contract::find($id);
        if ($_SESSION['role'] !== 'admin' && $contract['user_id'] != $_SESSION['user_id']) {
            require __DIR__ . '/../views/errors/403.php';
            exit;
        }
        Contract::delete($id);
        header('Location: /contracts.php');
        exit;
    }

    public function saveFromDraft($type, $drafts)
    {
        // Esempio: salva dati principali contratto
        $db = \CoreSuite\Models\Contract::getDb();
        $main = $drafts['anagrafica'] ?? [];
        $userId = $_SESSION['user_id'];
        $stmt = $db->prepare('INSERT INTO contracts (user_id, type, status, created_at, customer_id, extra_data) VALUES (?, ?, ?, NOW(), NULL, ?)');
        $stmt->execute([$userId, $type, 'inserito', json_encode($drafts)]);
        $contractId = $db->lastInsertId();
        // Salva allegati (sposta da tmp a uploads definitivi)
        if (!empty($drafts['upload'])) {
            foreach ($drafts['upload'] as $k => $file) {
                if ($file && file_exists(__DIR__ . '/../../../uploads/tmp/' . $file)) {
                    $destDir = __DIR__ . '/../../../uploads/' . $contractId;
                    if (!is_dir($destDir)) mkdir($destDir, 0755, true);
                    rename(__DIR__ . '/../../../uploads/tmp/' . $file, $destDir . '/' . $file);
                    // Registra allegato in tabella attachments
                    $db->prepare('INSERT INTO attachments (contract_id, filename, original_name, file_size, upload_date) VALUES (?, ?, ?, ?, NOW())')
                        ->execute([$contractId, $file, $file, filesize($destDir . '/' . $file)]);
                }
            }
        }
        // Audit log, notifiche, ecc...
        return $contractId;
    }
}
