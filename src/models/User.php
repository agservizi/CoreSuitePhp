<?php
namespace CoreSuite\Models;

class User
{
    public static function findByEmail($email)
    {
        $db = self::getDb();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public static function getDb()
    {
        require_once __DIR__ . '/../../config/database.php';
        $dsn = 'mysql:host=' . $GLOBALS['DB_HOST'] . ';dbname=' . $GLOBALS['DB_NAME'] . ';charset=utf8mb4';
        return new \PDO($dsn, $GLOBALS['DB_USER'], $GLOBALS['DB_PASS'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ]);
    }
}
