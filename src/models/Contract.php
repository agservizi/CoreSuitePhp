<?php
namespace CoreSuite\Models;

class Contract
{
    public static function allForUser($userId, $role)
    {
        $db = self::getDb();
        if ($role === 'admin') {
            $stmt = $db->query('SELECT * FROM contracts');
            return $stmt->fetchAll();
        } else {
            $stmt = $db->prepare('SELECT * FROM contracts WHERE user_id = ?');
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        }
    }

    public static function find($id)
    {
        $db = self::getDb();
        $stmt = $db->prepare('SELECT * FROM contracts WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function delete($id)
    {
        $db = self::getDb();
        $stmt = $db->prepare('DELETE FROM contracts WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function create($data)
    {
        $db = self::getDb();
        $stmt = $db->prepare('INSERT INTO contracts (user_id, provider, type, status, created_at, customer_id, extra_data) VALUES (?, ?, ?, ?, NOW(), ?, ?)');
        $stmt->execute([
            $data['user_id'],
            $data['provider'],
            $data['type'],
            $data['status'],
            $data['customer_id'],
            $data['extra_data']
        ]);
    }

    public static function updateStatus($id, $status)
    {
        $db = self::getDb();
        $stmt = $db->prepare('UPDATE contracts SET status = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$status, $id]);
    }

    public static function getDb()
    {
        $config = include __DIR__ . '/../../../config/database.php';
        $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4";
        return new \PDO($dsn, $config['db_user'], $config['db_pass'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ]);
    }
}
