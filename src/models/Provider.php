<?php
namespace CoreSuite\Models;

class Provider
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
    
    public static function all()
    {
        $db = self::getDb();
        $stmt = $db->query('SELECT * FROM providers');
        return $stmt->fetchAll();
    }

    public static function find($id)
    {
        $db = self::getDb();
        $stmt = $db->prepare('SELECT * FROM providers WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($data)
    {
        $db = self::getDb();
        $stmt = $db->prepare('INSERT INTO providers (name, code, type, logo, form_config) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['name'], 
            $data['code'], 
            $data['type'], 
            $data['logo'], 
            json_encode($data['form_config'])
        ]);
    }

    public static function update($id, $data)
    {
        $db = self::getDb();
        $stmt = $db->prepare('UPDATE providers SET name=?, code=?, type=?, logo=?, form_config=? WHERE id=?');
        $stmt->execute([
            $data['name'], 
            $data['code'], 
            $data['type'], 
            $data['logo'], 
            json_encode($data['form_config']), 
            $id
        ]);
    }

    public static function delete($id)
    {
        $db = self::getDb();
        $stmt = $db->prepare('DELETE FROM providers WHERE id = ?');
        $stmt->execute([$id]);
    }
}
