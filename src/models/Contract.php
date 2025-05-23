<?php
namespace CoreSuite\Models;

class Contract
{    
    public static function getDb()
    {
        static $db = null;
        if ($db === null) {
            require_once __DIR__ . '/../../config/database.php';
            $db = new \PDO(
                'mysql:host=' . $GLOBALS['DB_HOST'] . ';dbname=' . $GLOBALS['DB_NAME'] . ';charset=utf8mb4',
                $GLOBALS['DB_USER'],
                $GLOBALS['DB_PASS'],
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
        }
        return $db;
    }
    
    public static function allForUser($userId, $role)
    {
        $db = self::getDb();
        if ($role === 'admin') {
            $stmt = $db->query('SELECT c.*, p.name as provider_name, CONCAT(cu.first_name, " ", cu.last_name) as customer_name FROM contracts c 
                               LEFT JOIN providers p ON c.provider = p.id 
                               LEFT JOIN customers cu ON c.customer_id = cu.id');
            return $stmt->fetchAll();
        } else {
            $stmt = $db->prepare('SELECT c.*, p.name as provider_name, CONCAT(cu.first_name, " ", cu.last_name) as customer_name FROM contracts c 
                               LEFT JOIN providers p ON c.provider = p.id 
                               LEFT JOIN customers cu ON c.customer_id = cu.id 
                               WHERE c.user_id = ?');
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        }
    }

    public static function find($id)
    {
        $db = self::getDb();
        $stmt = $db->prepare('SELECT c.*, p.name as provider_name, CONCAT(cu.first_name, " ", cu.last_name) as customer_name FROM contracts c 
                               LEFT JOIN providers p ON c.provider = p.id 
                               LEFT JOIN customers cu ON c.customer_id = cu.id 
                               WHERE c.id = ?');
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
        return $db->lastInsertId();
    }

    public static function updateStatus($id, $status)
    {
        $db = self::getDb();
        $stmt = $db->prepare('UPDATE contracts SET status = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$status, $id]);
    }
}
