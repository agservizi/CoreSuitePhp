<?php
namespace CoreSuite\Controllers;

use CoreSuite\Models\Customer;

class CustomerController
{
    public function index()
    {
        $customers = \CoreSuite\Models\Customer::allForUser($_SESSION['user_id'], $_SESSION['role']);
        require __DIR__ . '/../views/customers/index.php';
    }

    public function show($id)
    {
        $customer = Customer::find($id);
        require __DIR__ . '/../views/customers/show.php';
    }

    public function create()
    {
        require __DIR__ . '/../views/customers/create.php';
    }    public function store()
    {
        $data = [
            'first_name' => $_POST['first_name'] ?? '',
            'last_name' => $_POST['last_name'] ?? '',
            'tax_code' => $_POST['tax_code'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'email' => $_POST['email'] ?? '',
            'date_of_birth' => $_POST['date_of_birth'] ?? null,
            'place_of_birth' => $_POST['place_of_birth'] ?? null,
            'document_type' => $_POST['document_type'] ?? null,
            'document_number' => $_POST['document_number'] ?? null,
            'document_expiry' => $_POST['document_expiry'] ?? null,
            'mobile' => $_POST['mobile'] ?? null,
            'notes' => $_POST['notes'] ?? null
        ];
        if (!$data['first_name'] || !$data['last_name'] || !$data['tax_code']) {
            $error = 'Nome, Cognome e Codice Fiscale sono obbligatori.';
            require __DIR__ . '/../views/customers/create.php';
            return;
        }
        Customer::create($data);
        header('Location: /customers.php');
        exit;
    }    public function edit($id)
    {
        $customer = Customer::find($id);
        require __DIR__ . '/../views/customers/edit.php';
    }

    public function update($id)
    {
        $data = [
            'first_name' => $_POST['first_name'] ?? '',
            'last_name' => $_POST['last_name'] ?? '',
            'tax_code' => $_POST['tax_code'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'email' => $_POST['email'] ?? '',
            'date_of_birth' => $_POST['date_of_birth'] ?? null,
            'place_of_birth' => $_POST['place_of_birth'] ?? null,
            'document_type' => $_POST['document_type'] ?? null,
            'document_number' => $_POST['document_number'] ?? null,
            'document_expiry' => $_POST['document_expiry'] ?? null,
            'mobile' => $_POST['mobile'] ?? null,
            'notes' => $_POST['notes'] ?? null
        ];
        Customer::update($id, $data);
        header('Location: /customers.php');
        exit;
    }

    public function delete($id)
    {
        Customer::delete($id);
        header('Location: /customers.php');
        exit;
    }
}
