<?php
namespace App\Model;
class ContractModel extends BaseModel {
    public function getAll() {
        $stmt = $this->pdo->query('SELECT c.*, cu.nome, cu.cognome, p.name as provider_name FROM contracts c JOIN customers cu ON c.customer_id = cu.id JOIN providers p ON c.provider_id = p.id');
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM contracts WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    public function create($data) {
        $stmt = $this->pdo->prepare('INSERT INTO contracts (customer_id, provider_id, type, status, data_inizio, data_fine, dati_json) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['customer_id'], $data['provider_id'], $data['type'], $data['status'], $data['data_inizio'], $data['data_fine'], $data['dati_json']
        ]);
        // Log azione
        if (isset($_SESSION['user_id'])) {
            (new \App\Model\LogModel())->log($_SESSION['user_id'], 'create_contract', json_encode($data));
            // Notifica admin
            (new \App\Model\NotificationModel())->create(1, 'Nuovo contratto creato', 'info');
        }
        return $this->pdo->lastInsertId();
    }
    public function update($id, $data) {
        $stmt = $this->pdo->prepare('UPDATE contracts SET customer_id=?, provider_id=?, type=?, status=?, data_inizio=?, data_fine=?, dati_json=? WHERE id=?');
        $stmt->execute([
            $data['customer_id'], $data['provider_id'], $data['type'], $data['status'], $data['data_inizio'], $data['data_fine'], $data['dati_json'], $id
        ]);
    }
    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM contracts WHERE id = ?');
        $stmt->execute([$id]);
        // Log azione
        if (isset($_SESSION['user_id'])) {
            (new \App\Model\LogModel())->log($_SESSION['user_id'], 'delete_contract', $id);
        }
    }
}
