<?php
/**
 * Model - Clase base para todos los modelos con manejo de reconexión
 */

class Model {
    protected $table;
    protected $primaryKey = 'id';
    
    /**
     * Obtener conexión de base de datos
     * Siempre obtiene una conexión fresca y verificada
     */
    protected function getDb() {
        return Database::getInstance()->getConnection();
    }
    
    /**
     * Ejecutar una consulta con manejo de reconexión automática
     */
    protected function executeWithRetry($callback, $maxRetries = 2) {
        $attempt = 0;
        
        while ($attempt < $maxRetries) {
            try {
                return $callback();
            } catch (PDOException $e) {
                $attempt++;
                
                // Si es un error de "server has gone away" y no es el último intento, reintentar
                if (strpos($e->getMessage(), 'gone away') !== false && $attempt < $maxRetries) {
                    // Forzar nueva conexión
                    Database::getInstance()->getConnection();
                    usleep(100000); // Esperar 0.1 segundos
                    continue;
                }
                
                // Si no es reconectable o ya se agotaron los intentos, lanzar el error
                throw $e;
            }
        }
    }
    
    /**
     * Obtener todos los registros
     */
    public function all() {
        return $this->executeWithRetry(function() {
            $db = $this->getDb();
            $stmt = $db->prepare("SELECT * FROM {$this->table}");
            $stmt->execute();
            return $stmt->fetchAll();
        });
    }
    
    /**
     * Alias para all() - retrocompatibilidad
     */
    public function getAll() {
        return $this->all();
    }
    
    /**
     * Encontrar por ID
     */
    public function find($id) {
        return $this->executeWithRetry(function() use ($id) {
            $db = $this->getDb();
            $stmt = $db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        });
    }
    
    /**
     * Crear registro
     */
    public function create($data) {
        return $this->executeWithRetry(function() use ($data) {
            $db = $this->getDb();
            $fields = array_keys($data);
            $values = array_values($data);
            $placeholders = str_repeat('?,', count($fields) - 1) . '?';
            
            $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") VALUES ({$placeholders})";
            $stmt = $db->prepare($sql);
            
            if ($stmt->execute($values)) {
                return $db->lastInsertId();
            }
            return false;
        });
    }
    
    /**
     * Actualizar registro
     */
    public function update($id, $data) {
        return $this->executeWithRetry(function() use ($id, $data) {
            $db = $this->getDb();
            $fields = [];
            foreach (array_keys($data) as $field) {
                $fields[] = "{$field} = ?";
            }
            
            $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE {$this->primaryKey} = ?";
            $stmt = $db->prepare($sql);
            
            $values = array_values($data);
            $values[] = $id;
            
            return $stmt->execute($values);
        });
    }
    
    /**
     * Eliminar registro
     */
    public function delete($id) {
        return $this->executeWithRetry(function() use ($id) {
            $db = $this->getDb();
            $stmt = $db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
            return $stmt->execute([$id]);
        });
    }
    
    /**
     * Ejecutar query personalizado
     */
    protected function query($sql, $params = []) {
        return $this->executeWithRetry(function() use ($sql, $params) {
            $db = $this->getDb();
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        });
    }
    
    /**
     * Contar registros
     */
    public function count($where = '', $params = []) {
        return $this->executeWithRetry(function() use ($where, $params) {
            $db = $this->getDb();
            $sql = "SELECT COUNT(*) as total FROM {$this->table}";
            if ($where) {
                $sql .= " WHERE {$where}";
            }
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return $result['total'];
        });
    }
    
    /**
     * Verificar si existe
     */
    public function exists($field, $value, $excludeId = null) {
        return $this->executeWithRetry(function() use ($field, $value, $excludeId) {
            $db = $this->getDb();
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$field} = ?";
            $params = [$value];
            
            if ($excludeId) {
                $sql .= " AND {$this->primaryKey} != ?";
                $params[] = $excludeId;
            }
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return $result['total'] > 0;
        });
    }
}