<?php
/**
 * Database Connection Class
 * Singleton pattern ile veritabanı bağlantı yönetimi
 */

class Database {
    private static $instance = null;
    private $connection;
    
    /**
     * Private constructor - Singleton pattern
     */
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Veritabanı bağlantı hatası: " . $e->getMessage());
        }
    }
    
    /**
     * Singleton instance'ı döndürür
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * PDO bağlantısını döndürür
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Prepared statement ile sorgu çalıştırma
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Sorgu hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Tek satır sonuç döndürür
     */
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetch() : false;
    }
    
    /**
     * Tüm sonuçları döndürür
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetchAll() : false;
    }
    
    /**
     * INSERT sorgusu çalıştırır ve son eklenen ID'yi döndürür
     */
    public function insert($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $this->connection->lastInsertId() : false;
    }
    
    /**
     * UPDATE veya DELETE sorgusu çalıştırır
     */
    public function execute($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->rowCount() : false;
    }
    
    /**
     * Transaction başlatır
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Transaction'ı commit eder
     */
    public function commit() {
        return $this->connection->commit();
    }
    
    /**
     * Transaction'ı rollback eder
     */
    public function rollBack() {
        return $this->connection->rollBack();
    }
    
    /**
     * Clone ve wakeup methodları - Singleton pattern
     */
    private function __clone() {}
    
    public function __wakeup() {
        throw new Exception("Singleton sınıfı unserialize edilemez.");
    }
}
