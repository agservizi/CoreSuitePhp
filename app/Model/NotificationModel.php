<?php
namespace App\Model;
class NotificationModel extends BaseModel {
    public function create($user_id, $message, $type = 'info') {
        $stmt = $this->pdo->prepare('INSERT INTO notifications (user_id, message, type) VALUES (?, ?, ?)');
        $stmt->execute([$user_id, $message, $type]);
    }
    public function getForUser($user_id, $limit = 10) {
        $stmt = $this->pdo->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?');
        $stmt->execute([$user_id, $limit]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function markRead($id) {
        $stmt = $this->pdo->prepare('UPDATE notifications SET is_read = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }
}
