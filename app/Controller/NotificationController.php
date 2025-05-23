<?php
namespace App\Controller;
use App\Model\NotificationModel;

class NotificationController extends BaseController {
    public function list() {
        session_start();
        $user_id = $_SESSION['user_id'] ?? 0;
        $model = new NotificationModel();
        $notifications = $model->getForUser($user_id);
        include __DIR__ . '/../../View/notification/list.php';
    }
    public function markRead($id) {
        $model = new NotificationModel();
        $model->markRead($id);
        $this->redirect('/index.php?route=notifications');
    }
}
