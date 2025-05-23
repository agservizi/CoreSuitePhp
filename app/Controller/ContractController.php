<?php
namespace App\Controller;
use App\Model\ContractModel;
use App\Model\CustomerModel;
use App\Model\ProviderModel;

class ContractController extends BaseController {
    public function index() {
        $model = new ContractModel();
        $contracts = $model->getAll();
        include __DIR__ . '/../../View/contract/list.php';
    }
    public function createForm($error = '') {
        $customerModel = new CustomerModel();
        $providerModel = new ProviderModel();
        $customers = $customerModel->getAll();
        $providers = $providerModel->getAll();
        include __DIR__ . '/../../View/contract/create.php';
    }
    public function create() {
        // CSRF check
        if (!csrf_check($_POST['csrf_token'] ?? '')) {
            $this->createForm('Token CSRF non valido');
            return;
        }

        $data = [
            'customer_id' => $_POST['customer_id'],
            'provider_id' => $_POST['provider_id'],
            'type' => $_POST['type'],
            'status' => $_POST['status'],
            'data_inizio' => $_POST['data_inizio'],
            'data_fine' => $_POST['data_fine'],
            'dati_json' => $_POST['dati_json'] ?? ''
        ];
        $model = new ContractModel();
        $model->create($data);
        $this->redirect('/index.php?route=contracts');
    }
    public function editForm($id, $error = '') {
        $model = new ContractModel();
        $customerModel = new CustomerModel();
        $providerModel = new ProviderModel();
        $contract = $model->getById($id);
        $customers = $customerModel->getAll();
        $providers = $providerModel->getAll();
        // Link allegati
        $attachmentLink = '/index.php?route=attachment_list&contract_id=' . $id;
        include __DIR__ . '/../../View/contract/edit.php';
    }
    public function update($id) {
        // CSRF check
        if (!csrf_check($_POST['csrf_token'] ?? '')) {
            $this->editForm($id, 'Token CSRF non valido');
            return;
        }

        $data = [
            'customer_id' => $_POST['customer_id'],
            'provider_id' => $_POST['provider_id'],
            'type' => $_POST['type'],
            'status' => $_POST['status'],
            'data_inizio' => $_POST['data_inizio'],
            'data_fine' => $_POST['data_fine'],
            'dati_json' => $_POST['dati_json'] ?? ''
        ];
        $model = new ContractModel();
        $model->update($id, $data);
        $this->redirect('/index.php?route=contracts');
    }
    public function delete($id) {
        $model = new ContractModel();
        $model->delete($id);
        $this->redirect('/index.php?route=contracts');
    }
}
