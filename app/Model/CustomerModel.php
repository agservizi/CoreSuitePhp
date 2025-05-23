<?php
namespace App\Model;
class CustomerModel extends BaseModel {
    public function getAll() {
        $stmt = $this->pdo->query('SELECT * FROM customers');
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM customers WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    public function create($data) {
        $stmt = $this->pdo->prepare('INSERT INTO customers (nome, cognome, cf, documento, telefono, email, piva, ragione_sociale, rappresentante, sdi_pec) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['nome'], $data['cognome'], $data['cf'], $data['documento'], $data['telefono'], $data['email'], $data['piva'], $data['ragione_sociale'], $data['rappresentante'], $data['sdi_pec']
        ]);
        // Log azione
        if (isset($_SESSION['user_id'])) {
            (new \App\Model\LogModel())->log($_SESSION['user_id'], 'create_customer', $data['email']);
        }
        return $this->pdo->lastInsertId();
    }
    public function update($id, $data) {
        $stmt = $this->pdo->prepare('UPDATE customers SET nome=?, cognome=?, cf=?, documento=?, telefono=?, email=?, piva=?, ragione_sociale=?, rappresentante=?, sdi_pec=? WHERE id=?');
        $stmt->execute([
            $data['nome'], $data['cognome'], $data['cf'], $data['documento'], $data['telefono'], $data['email'], $data['piva'], $data['ragione_sociale'], $data['rappresentante'], $data['sdi_pec'], $id
        ]);
    }
    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM customers WHERE id = ?');
        $stmt->execute([$id]);
        // Log azione
        if (isset($_SESSION['user_id'])) {
            (new \App\Model\LogModel())->log($_SESSION['user_id'], 'delete_customer', $id);
        }
    }
}
