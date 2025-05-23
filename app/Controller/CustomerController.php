<?php
namespace App\Controller;
use App\Model\CustomerModel;

class CustomerController extends BaseController {
    public function index() {
        $model = new CustomerModel();
        $customers = $model->getAll();
        include __DIR__ . '/../../View/customer/list.php';
    }
    public function createForm($error = '') {
        include __DIR__ . '/../../View/customer/create.php';
    }
    public function create() {
        // CSRF check
        if (!csrf_check($_POST['csrf_token'] ?? '')) {
            $this->createForm('Token CSRF non valido');
            return;
        }

        $data = [
            'nome' => $_POST['nome'],
            'cognome' => $_POST['cognome'],
            'cf' => $_POST['cf'],
            'documento' => $_POST['documento'],
            'telefono' => $_POST['telefono'],
            'email' => $_POST['email'],
            'piva' => $_POST['piva'],
            'ragione_sociale' => $_POST['ragione_sociale'],
            'rappresentante' => $_POST['rappresentante'],
            'sdi_pec' => $_POST['sdi_pec']
        ];
        $model = new CustomerModel();
        $model->create($data);
        $this->redirect('/index.php?route=customers');
    }
    public function editForm($id, $error = '') {
        $model = new CustomerModel();
        $customer = $model->getById($id);
        include __DIR__ . '/../../View/customer/edit.php';
    }
    public function update($id) {
        // CSRF check
        if (!csrf_check($_POST['csrf_token'] ?? '')) {
            $this->editForm($id, 'Token CSRF non valido');
            return;
        }

        $data = [
            'nome' => $_POST['nome'],
            'cognome' => $_POST['cognome'],
            'cf' => $_POST['cf'],
            'documento' => $_POST['documento'],
            'telefono' => $_POST['telefono'],
            'email' => $_POST['email'],
            'piva' => $_POST['piva'],
            'ragione_sociale' => $_POST['ragione_sociale'],
            'rappresentante' => $_POST['rappresentante'],
            'sdi_pec' => $_POST['sdi_pec']
        ];
        $model = new CustomerModel();
        $model->update($id, $data);
        $this->redirect('/index.php?route=customers');
    }
    public function delete($id) {
        $model = new CustomerModel();
        $model->delete($id);
        $this->redirect('/index.php?route=customers');
    }
}
