<?php
namespace CoreSuite\Models;

class Customer
{
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
    }

    public static function create($data)
    {
        $db = self::getDb();
        $stmt = $db->prepare('INSERT INTO customers (name, surname, fiscal_code, phone, email, created_at, date_of_birth, place_of_birth, document_type, document_number, document_expiry, mobile, notes) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['name'], $data['surname'], $data['fiscal_code'], $data['phone'], $data['email'],
            $data['date_of_birth'], $data['place_of_birth'], $data['document_type'], $data['document_number'], $data['document_expiry'], $data['mobile'], $data['notes']
        ]);
    }

    public static function update($id, $data)
    {
        $db = self::getDb();
        $stmt = $db->prepare('UPDATE customers SET name=?, surname=?, fiscal_code=?, phone=?, email=?, date_of_birth=?, place_of_birth=?, document_type=?, document_number=?, document_expiry=?, mobile=?, notes=? WHERE id=?');
        $stmt->execute([
            $data['name'], $data['surname'], $data['fiscal_code'], $data['phone'], $data['email'],
            $data['date_of_birth'], $data['place_of_birth'], $data['document_type'], $data['document_number'], $data['document_expiry'], $data['mobile'], $data['notes'], $id
        ]);
    }

    public static function delete($id)
    {
        $db = self::getDb();
        $stmt = $db->prepare('DELETE FROM customers WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function allForUser($userId, $role)
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
