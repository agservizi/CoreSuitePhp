<?php
namespace CoreSuite\Models;

class Attachment
{
    public static function create($data)
    {
        $db = self::getDb();
        $stmt = $db->prepare('INSERT INTO attachments (contract_id, filename, original_name, file_size, upload_date) VALUES (?, ?, ?, ?, NOW())');
        $stmt->execute([
            $data['contract_id'],
            $data['filename'],
            $data['original_name'],
            $data['file_size']
        ]);
    }

    public static function allByContract($contractId)
    {
        $db = self::getDb();
        $stmt = $db->prepare('SELECT * FROM attachments WHERE contract_id = ?');
        $stmt->execute([$contractId]);
        return $stmt->fetchAll();
    }

    public static function allByContractForUser($contractId, $userId, $role)
    {
        $db = self::getDb();
        if ($role === 'admin') {
            $stmt = $db->prepare('SELECT * FROM attachments WHERE contract_id = ?');
            $stmt->execute([$contractId]);
            return $stmt->fetchAll();
        } else {
            // Verifica che il contratto sia dell'user
            $cstmt = $db->prepare('SELECT user_id FROM contracts WHERE id = ?');
            $cstmt->execute([$contractId]);
            $owner = $cstmt->fetchColumn();
            if ($owner == $userId) {
                $stmt = $db->prepare('SELECT * FROM attachments WHERE contract_id = ?');
                $stmt->execute([$contractId]);
                return $stmt->fetchAll();
            } else {
                return [];
            }
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
