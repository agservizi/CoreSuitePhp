<?php
namespace App\Controller;
use App\Model\LogModel;

class LogController extends BaseController {
    public function index() {
        $model = new LogModel();
        $logs = $model->getAll();
        include __DIR__ . '/../../View/log/list.php';
    }
}
