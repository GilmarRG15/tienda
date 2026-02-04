<?php
/**
 * Modelo Rol
 */

class Rol extends Model {
    protected $table = 'roles';
    
    /**
     * Obtener rol por nombre
     */
    public function findByName($nombre) {
        return $this->executeWithRetry(function() use ($nombre) {
            $db = $this->getDb();
            $stmt = $db->prepare("SELECT * FROM roles WHERE nombre = ?");
            $stmt->execute([$nombre]);
            return $stmt->fetch();
        });
    }
    
    /**
     * Obtener todos los roles excepto CLIENTE
     */
    public function getAllExceptCliente() {
        return $this->executeWithRetry(function() {
            $db = $this->getDb();
            $stmt = $db->prepare("SELECT * FROM roles WHERE nombre != 'CLIENTE' ORDER BY nombre");
            $stmt->execute();
            return $stmt->fetchAll();
        });
    }
}