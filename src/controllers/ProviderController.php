<?php
namespace CoreSuite\Controllers;

use CoreSuite\Models\Provider;

class ProviderController
{
    public function index()
    {
        $providers = Provider::all();
        require __DIR__ . '/../views/providers/index.php';
    }

    public function show($id)
    {
        $provider = Provider::find($id);
        if (isset($_GET['json'])) {
            header('Content-Type: application/json');
            echo json_encode($provider);
            exit;
        }
        require __DIR__ . '/../views/providers/show.php';
    }

    public function create()
    {
        require __DIR__ . '/../views/providers/create.php';
    }

    public function store()
    {
        $data = [
            'name' => $_POST['name'] ?? '',
            'type' => $_POST['type'] ?? '',
            'logo' => $_POST['logo'] ?? '',
            'form_config' => json_decode($_POST['form_config'] ?? '[]', true)
        ];
        if (!$data['name'] || !$data['type']) {
            $error = 'Nome e tipo sono obbligatori.';
            require __DIR__ . '/../views/providers/create.php';
            return;
        }
        Provider::create($data);
        header('Location: /providers.php');
        exit;
    }

    public function edit($id)
    {
        $provider = Provider::find($id);
        require __DIR__ . '/../views/providers/edit.php';
    }

    public function update($id)
    {
        $data = [
            'name' => $_POST['name'] ?? '',
            'type' => $_POST['type'] ?? '',
            'logo' => $_POST['logo'] ?? '',
            'form_config' => json_decode($_POST['form_config'] ?? '[]', true)
        ];
        Provider::update($id, $data);
        header('Location: /providers.php');
        exit;
    }

    public function delete($id)
    {
        Provider::delete($id);
        header('Location: /providers.php');
        exit;
    }
}
