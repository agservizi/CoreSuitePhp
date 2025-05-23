<?php
namespace App\Model;
class UserModel extends BaseModel {
    public function getAll() {
        $stmt = $this->pdo->query('SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id');
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    public function create($data) {
        $stmt = $this->pdo->prepare('INSERT INTO users (email, password, role_id, mfa_secret) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role_id'],
            $data['mfa_secret'] ?? null
        ]);
        // Log azione
        if (isset($_SESSION['user_id'])) {
            (new \App\Model\LogModel())->log($_SESSION['user_id'], 'create_user', $data['email']);
        }
        return $this->pdo->lastInsertId();
    }
    public function update($id, $data) {
        $sql = 'UPDATE users SET email=?, role_id=?';
        $params = [$data['email'], $data['role_id']];
        if (!empty($data['password'])) {
            $sql .= ', password=?';
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        $sql .= ' WHERE id=?';
        $params[] = $id;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }
    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$id]);
        // Log azione
        if (isset($_SESSION['user_id'])) {
            (new \App\Model\LogModel())->log($_SESSION['user_id'], 'delete_user', $id);
        }
    }
}
