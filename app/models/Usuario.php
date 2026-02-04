<?php
// Modelo para gestionar usuarios

class Usuario extends Model {
    protected $table = 'usuarios';
    
    // Crea un nuevo usuario con contraseña encriptada
    public function createUser($data) {
        // Encriptar la contraseña
        $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        unset($data['password']);
        unset($data['password_confirmation']);
        
        return $this->create($data);
    }
    
    // Busca un usuario por su email
    public function findByEmail($email) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    // Verifica si el email y contraseña son correctos
    public function verificarCredenciales($email, $password) {
        $user = $this->findByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }
        
        return $user;
    }
    
// Actualiza la fecha del último login con manejo de reconexión
public function updateLastLogin($userId) {
    $maxRetries = 3;
    $attempt = 0;
    
    while ($attempt < $maxRetries) {
        try {
            // Obtener una conexión fresca
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?");
            return $stmt->execute([$userId]);
        } catch (PDOException $e) {
            $attempt++;
            
            // Si es un error de "server has gone away" y no es el último intento
            if (strpos($e->getMessage(), 'gone away') !== false && $attempt < $maxRetries) {
                // Esperar un momento antes de reintentar
                usleep(100000); // 0.1 segundos
                continue;
            }
            
            // Si no es reconectable o se agotaron los intentos, lanzar el error
            throw $e;
        }
    }
    
    return false;
}
    
    // Asigna un rol al usuario
    public function assignRole($userId, $roleName) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            INSERT INTO usuario_roles (usuario_id, rol_id)
            SELECT ?, id FROM roles WHERE nombre = ?
        ");
        return $stmt->execute([$userId, $roleName]);
    }
    
    // Obtiene todos los roles de un usuario
    public function getRoles($userId) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT r.nombre
            FROM roles r
            INNER JOIN usuario_roles ur ON r.id = ur.rol_id
            WHERE ur.usuario_id = ?
        ");
        $stmt->execute([$userId]);
        $roles = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $roles;
    }
    
    // Obtiene todos los usuarios con sus roles
    public function getAllWithRoles() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT u.*, GROUP_CONCAT(r.nombre SEPARATOR ', ') as roles
            FROM usuarios u
            LEFT JOIN usuario_roles ur ON u.id = ur.usuario_id
            LEFT JOIN roles r ON ur.rol_id = r.id
            GROUP BY u.id
            ORDER BY u.fecha_creacion DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Cambia el estado del usuario (activo/bloqueado)
    public function cambiarEstado($userId, $estado) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE usuarios SET estado = ? WHERE id = ?");
        return $stmt->execute([$estado, $userId]);
    }
    
    // Elimina todos los roles de un usuario
    public function removeRoles($userId) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM usuario_roles WHERE usuario_id = ?");
        return $stmt->execute([$userId]);
    }
    
    // Actualiza la contraseña del usuario
    public function updatePassword($userId, $newPassword) {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE usuarios SET password_hash = ? WHERE id = ?");
        return $stmt->execute([$passwordHash, $userId]);
    }
}
