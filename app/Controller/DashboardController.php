<?php
namespace App\Controller;
use App\Model\UserModel;
use App\Model\CustomerModel;
use App\Model\ContractModel;

class DashboardController extends BaseController {
    public function index() {
        $userModel = new \App\Model\UserModel();
        $customerModel = new \App\Model\CustomerModel();
        $contractModel = new \App\Model\ContractModel();
        $stats = [
            'users' => count($userModel->getAll()),
            'customers' => count($customerModel->getAll()),
            'contracts' => count($contractModel->getAll())
        ];
        $pdo = $contractModel->getPdo();
        $res = $pdo->query("SELECT DATE_FORMAT(data_inizio, '%Y-%m') as mese, COUNT(*) as tot FROM contracts GROUP BY mese ORDER BY mese DESC LIMIT 6");
        $labels = [];
        $data = [];
        foreach (array_reverse($res->fetchAll(\PDO::FETCH_ASSOC)) as $row) {
            $labels[] = $row['mese'];
            $data[] = (int)$row['tot'];
        }
        $chart = ['labels' => $labels, 'data' => $data];
        session_start();
        $role_id = $_SESSION['role_id'] ?? 2;
        include __DIR__ . '/../View/dashboard.php';
    }
}
