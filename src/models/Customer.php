<?php
namespace CoreSuite\Models;

class Customer
{    public static function getDb()
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
    
    public static function all()
    {
        $db = self::getDb();
        $stmt = $db->query('SELECT * FROM customers');
        return $stmt->fetchAll();
    }

    public static function find($id)
    {
        $db = self::getDb();
        $stmt = $db->prepare('SELECT * FROM customers WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }    public static function create($data)
    {
        $db = self::getDb();
        $stmt = $db->prepare('INSERT INTO customers (first_name, last_name, tax_code, phone, email, date_of_birth, place_of_birth, document_type, document_number, document_expiry, mobile, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['first_name'], 
            $data['last_name'], 
            $data['tax_code'], 
            $data['phone'], 
            $data['email'], 
            $data['date_of_birth'] ?? null,
            $data['place_of_birth'] ?? null,
            $data['document_type'] ?? null,
            $data['document_number'] ?? null,
            $data['document_expiry'] ?? null,
            $data['mobile'] ?? null,
            $data['notes']
        ]);
    }

    public static function update($id, $data)
    {
        $db = self::getDb();
        $stmt = $db->prepare('UPDATE customers SET first_name=?, last_name=?, tax_code=?, phone=?, email=?, date_of_birth=?, place_of_birth=?, document_type=?, document_number=?, document_expiry=?, mobile=?, notes=? WHERE id=?');
        $stmt->execute([
            $data['first_name'], 
            $data['last_name'], 
            $data['tax_code'], 
            $data['phone'], 
            $data['email'], 
            $data['date_of_birth'] ?? null,
            $data['place_of_birth'] ?? null,
            $data['document_type'] ?? null,
            $data['document_number'] ?? null,
            $data['document_expiry'] ?? null,
            $data['mobile'] ?? null,
            $data['notes'],
            $id
        ]);
    }

    public static function delete($id)
    {
        $db = self::getDb();
        $stmt = $db->prepare('DELETE FROM customers WHERE id = ?');
        $stmt->execute([$id]);
    }    public static function allForUser($userId, $role)
    {
        $db = self::getDb();
        if ($role === 'admin') {
            $stmt = $db->query('SELECT * FROM customers');
            return $stmt->fetchAll();
        } else {
            $stmt = $db->prepare('SELECT DISTINCT c.* FROM customers c JOIN contracts k ON c.id = k.customer_id WHERE k.user_id = ?');
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        }
    }
}
