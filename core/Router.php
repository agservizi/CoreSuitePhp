<?php
namespace Core;
class Router {
    public function run() {
        $route = $_GET['route'] ?? 'dashboard';
        switch ($route) {
            case 'login':
                $ctrl = new \App\Controller\AuthController();
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $ctrl->login();
                } else {
                    $ctrl->loginForm();
                }
                break;
            case 'mfa':
                $ctrl = new \App\Controller\AuthController();
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $ctrl->mfaVerify();
                } else {
                    $ctrl->mfaForm();
                }
                break;
            case 'logout':
                $ctrl = new \App\Controller\AuthController();
                $ctrl->logout();
                break;
            case 'dashboard':
                session_start();
                if (!isset($_SESSION['user_id'])) {
                    header('Location: /index.php?route=login'); exit;
                }
                $ctrl = new \App\Controller\DashboardController();
                $ctrl->index();
                break;
            case 'users':
                session_start();
                if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
                    header('Location: /index.php?route=login'); exit;
                }
                $ctrl = new \App\Controller\UserController();
                $ctrl->index();
                break;
            case 'user_create':
                session_start();
                if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
                    header('Location: /index.php?route=login'); exit;
                }
                $ctrl = new \App\Controller\UserController();
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $ctrl->create();
                } else {
                    $ctrl->createForm();
                }
                break;
            case 'user_edit':
                session_start();
                if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
                    header('Location: /index.php?route=login'); exit;
                }
                $ctrl = new \App\Controller\UserController();
                $id = intval($_GET['id'] ?? 0);
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $ctrl->update($id);
                } else {
                    $ctrl->editForm($id);
                }
                break;
            case 'user_delete':
                session_start();
                if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
                    header('Location: /index.php?route=login'); exit;
                }
                $ctrl = new \App\Controller\UserController();
                $id = intval($_GET['id'] ?? 0);
                $ctrl->delete($id);
                break;
            case 'customers':
                session_start();
                if (!isset($_SESSION['user_id'])) {
                    header('Location: /index.php?route=login'); exit;
                }
                $ctrl = new \App\Controller\CustomerController();
                $ctrl->index();
                break;
            case 'customer_create':
                session_start();
                if (!isset($_SESSION['user_id'])) {
                    header('Location: /index.php?route=login'); exit;
                }
                $ctrl = new \App\Controller\CustomerController();
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $ctrl->create();
                } else {
                    $ctrl->createForm();
                }
                break;
            case 'customer_edit':
                session_start();
                if (!isset($_SESSION['user_id'])) {
                    header('Location: /index.php?route=login'); exit;
                }
                $ctrl = new \App\Controller\CustomerController();
                $id = intval($_GET['id'] ?? 0);
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $ctrl->update($id);
                } else {
                    $ctrl->editForm($id);
                }
                break;
            case 'customer_delete':
                session_start();
                if (!isset($_SESSION['user_id'])) {
                    header('Location: /index.php?route=login'); exit;
                }
                $ctrl = new \App\Controller\CustomerController();
                $id = intval($_GET['id'] ?? 0);
                $ctrl->delete($id);
                break;
            case 'contracts':
                session_start();
                if (!isset($_SESSION['user_id'])) {
                    header('Location: /index.php?route=login'); exit;
                }
                $ctrl = new \App\Controller\ContractController();
                $ctrl->index();
                break;
            case 'contract_create':
                session_start();
                if (!isset($_SESSION['user_id'])) {
                    header('Location: /index.php?route=login'); exit;
                }
                $ctrl = new \App\Controller\ContractController();
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $ctrl->create();
                } else {
                    $ctrl->createForm();
                }
                break;
            case 'contract_edit':
                session_start();
                if (!isset($_SESSION['user_id'])) {
                    header('Location: /index.php?route=login'); exit;
                }
                $ctrl = new \App\Controller\ContractController();
                $id = intval($_GET['id'] ?? 0);
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $ctrl->update($id);
                } else {
                    $ctrl->editForm($id);
                }
                break;
            case 'contract_delete':
                session_start();
                if (!isset($_SESSION['user_id'])) {
                    header('Location: /index.php?route=login'); exit;
                }
                $ctrl = new \App\Controller\ContractController();
                $id = intval($_GET['id'] ?? 0);
                $ctrl->delete($id);
                break;
            case 'attachment_list':
                session_start();
                if (!isset($_SESSION['user_id'])) {
                    header('Location: /index.php?route=login'); exit;
                }
                $ctrl = new \App\Controller\AttachmentController();
                $contract_id = intval($_GET['contract_id'] ?? 0);
                $ctrl->uploadForm($contract_id);
                break;
            case 'attachment_upload':
                session_start();
                if (!isset($_SESSION['user_id'])) {
                    header('Location: /index.php?route=login'); exit;
                }
                $ctrl = new \App\Controller\AttachmentController();
                $contract_id = intval($_GET['contract_id'] ?? 0);
                $ctrl->upload($contract_id);
                break;
            case 'attachment_delete':
                session_start();
                if (!isset($_SESSION['user_id'])) {
                    header('Location: /index.php?route=login'); exit;
                }
                $ctrl = new \App\Controller\AttachmentController();
                $id = intval($_GET['id'] ?? 0);
                $contract_id = intval($_GET['contract_id'] ?? 0);
                $ctrl->delete($id, $contract_id);
                break;
            case 'logs':
                session_start();
                if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
                    header('Location: /index.php?route=login'); exit;
                }
                $ctrl = new \App\Controller\LogController();
                $ctrl->index();
                break;
            case 'notifications':
                session_start();
                if (!isset($_SESSION['user_id'])) {
                    header('Location: /index.php?route=login'); exit;
                }
                $ctrl = new \App\Controller\NotificationController();
                $ctrl->list();
                break;
            case 'notification_read':
                session_start();
                if (!isset($_SESSION['user_id'])) {
                    header('Location: /index.php?route=login'); exit;
                }
                $ctrl = new \App\Controller\NotificationController();
                $id = intval($_GET['id'] ?? 0);
                $ctrl->markRead($id);
                break;
            case 'import_customers':
                session_start();
                if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
                    header('Location: /index.php?route=login'); exit;
                }
                $ctrl = new \App\Controller\ImportController();
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $ctrl->import();
                } else {
                    $ctrl->importForm();
                }
                break;
            case 'export_customers':
                session_start();
                if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
                    header('Location: /index.php?route=login'); exit;
                }
                $ctrl = new \App\Controller\ExportController();
                $ctrl->exportCustomers();
                break;
            case 'export_contracts':
                session_start();
                if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
                    header('Location: /index.php?route=login'); exit;
                }
                $ctrl = new \App\Controller\ExportContractController();
                $ctrl->exportContracts();
                break;
            case 'export_logs':
                session_start();
                if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
                    header('Location: /index.php?route=login'); exit;
                }
                $ctrl = new \App\Controller\ExportLogController();
                $ctrl->exportLogs();
                break;
            default:
                echo '<h1>404 - Pagina non trovata</h1>';
        }
    }
}
