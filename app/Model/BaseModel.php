<?php
// Placeholder per Model base
namespace App\Model;
class BaseModel {
    protected $pdo;
    public function __construct() {
        $config = require __DIR__ . '/../../../config/database.php';
        $this->pdo = new \PDO('mysql:host=' . $config['db_host'] . ';dbname=' . $config['db_name'], $config['db_user'], $config['db_password']);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
    public function getPdo() {
        return $this->pdo;
    }
}
