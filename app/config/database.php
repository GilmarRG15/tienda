<?php
/**
 * Configuración de base de datos - PDO con reconexión automática
 */

class Database {
    private static $instance = null;
    private $connection;
    
    // Configuración de conexión
    private $host = 'localhost';
    private $port = '3306';
    private $dbname = 'bd_tienda';
    private $username = 'root';
    private $password = ''; // En XAMPP por defecto está vacío
    private $charset = 'utf8mb4';
    
    private function __construct() {
        $this->connect();
    }
    
    private function connect() {
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}",
                // Configuraciones para evitar "MySQL server has gone away"
                PDO::ATTR_TIMEOUT => 30, // Timeout de conexión
                PDO::ATTR_PERSISTENT => false // No usar conexiones persistentes
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        // Verificar si la conexión está activa, si no, reconectar
        try {
            // Intentar un query simple para verificar la conexión
            $this->connection->query('SELECT 1');
        } catch (PDOException $e) {
            // Si falla, reconectar
            $this->connect();
        }
        
        return $this->connection;
    }
    
    // Evitar clonación del objeto
    private function __clone() {}
    
    // Evitar deserialización del objeto
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}