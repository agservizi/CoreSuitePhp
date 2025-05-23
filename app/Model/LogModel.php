<?php
namespace App\Model;
class LogModel extends BaseModel {
    public function log($user_id, $action, $details = null) {
        $stmt = $this->pdo->prepare('INSERT INTO logs (user_id, action, details) VALUES (?, ?, ?)');
        $stmt->execute([$user_id, $action, $details]);
    }
    public function getAll($limit = 100) {
        $stmt = $this->pdo->prepare('SELECT l.*, u.email FROM logs l LEFT JOIN users u ON l.user_id = u.id ORDER BY l.created_at DESC LIMIT ?');
        $stmt->execute([$limit]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
