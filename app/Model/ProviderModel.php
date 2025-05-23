<?php
namespace App\Model;
class ProviderModel extends BaseModel {
    public function getAll() {
        $stmt = $this->pdo->query('SELECT * FROM providers');
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM providers WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
