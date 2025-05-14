<?php
/**
 * Classe per la gestione della connessione al database
 * Implementa il pattern Singleton
 */
class Database {
    private static $instance = null;
    private $pdo;

    /**
     * Costruttore: inizializza la connessione al database
     */
    private function __construct() {
        $config = require_once __DIR__ . '/../config/database.php';
        
        try {
            $this->pdo = new PDO(
                "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4",
                $config['db_user'],
                $config['db_password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            throw new Exception("Errore di connessione al database: " . $e->getMessage());
        }
    }

    /**
     * Ritorna l'istanza Singleton del database
     * @return PDO
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }
    
    /**
     * Metodo alternativo per ottenere la connessione (compatibilità)
     * @return PDO
     */
    public function getConnection() {
        return $this->pdo;
    }

    // Previene la clonazione dell'oggetto
    private function __clone() {}
    
    // Previene la deserializzazione dell'oggetto
    private function __wakeup() {}
}
