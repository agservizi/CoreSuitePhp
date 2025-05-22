<?php
namespace CoreSuite\Models;

class Provider
{
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
        $stmt = $db->prepare('INSERT INTO providers (name, type, logo, form_config) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            $data['name'], $data['type'], $data['logo'], json_encode($data['form_config'])
        ]);
    }

    public static function update($id, $data)
    {
        $db = self::getDb();
        $stmt = $db->prepare('UPDATE providers SET name=?, type=?, logo=?, form_config=? WHERE id=?');
        $stmt->execute([
            $data['name'], $data['type'], $data['logo'], json_encode($data['form_config']), $id
        ]);
    }

    public static function delete($id)
    {
        $db = self::getDb();
        $stmt = $db->prepare('DELETE FROM providers WHERE id = ?');
        $stmt->execute([$id]);
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
