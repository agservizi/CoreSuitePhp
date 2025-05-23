<?php
namespace App\Model;
class AttachmentModel extends BaseModel {
    public function getByContract($contract_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM attachments WHERE contract_id = ?');
        $stmt->execute([$contract_id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function create($contract_id, $file_name, $file_path) {
        $stmt = $this->pdo->prepare('INSERT INTO attachments (contract_id, file_name, file_path) VALUES (?, ?, ?)');
        $stmt->execute([$contract_id, $file_name, $file_path]);
        // Log azione
        if (isset($_SESSION['user_id'])) {
            (new \App\Model\LogModel())->log($_SESSION['user_id'], 'upload_attachment', $file_name);
        }
        return $this->pdo->lastInsertId();
    }
    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM attachments WHERE id = ?');
        $stmt->execute([$id]);
        // Log azione
        if (isset($_SESSION['user_id'])) {
            (new \App\Model\LogModel())->log($_SESSION['user_id'], 'delete_attachment', $id);
        }
    }
}
