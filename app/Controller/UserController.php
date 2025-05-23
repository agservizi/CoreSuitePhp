<?php
namespace App\Controller;
use App\Model\UserModel;

class UserController extends BaseController {
    public function index() {
        $model = new UserModel();
        $users = $model->getAll();
        include __DIR__ . '/../../View/user/list.php';
    }
    public function createForm($error = '') {
        include __DIR__ . '/../../View/user/create.php';
    }
    public function create() {
        // CSRF check
        if (!csrf_check($_POST['csrf_token'] ?? '')) {
            $this->createForm('Token CSRF non valido');
            return;
        }

        $data = [
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'role_id' => $_POST['role_id'],
            'mfa_secret' => $_POST['mfa_secret'] ?? null
        ];
        $model = new UserModel();
        $model->create($data);
        $this->redirect('/index.php?route=users');
    }
    public function editForm($id, $error = '') {
        $model = new UserModel();
        $user = $model->getById($id);
        include __DIR__ . '/../../View/user/edit.php';
    }
    public function update($id) {
        // CSRF check
        if (!csrf_check($_POST['csrf_token'] ?? '')) {
            $this->editForm($id, 'Token CSRF non valido');
            return;
        }

        $data = [
            'email' => $_POST['email'],
            'password' => $_POST['password'] ?? '',
            'role_id' => $_POST['role_id']
        ];
        $model = new UserModel();
        $model->update($id, $data);
        $this->redirect('/index.php?route=users');
    }
    public function delete($id) {
        $model = new UserModel();
        $model->delete($id);
        $this->redirect('/index.php?route=users');
    }
}
