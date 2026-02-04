<?php
/**
 * Auth - Sistema de autenticación
 */

class Auth {
    
    /**
     * Verificar si el usuario está autenticado
     */
    public static function check() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Obtener el usuario autenticado
     */
    public static function user() {
        if (!self::check()) {
            return null;
        }
        
        if (!isset($_SESSION['user_data'])) {
            $usuarioModel = new Usuario();
            $user = $usuarioModel->find($_SESSION['user_id']);
            $_SESSION['user_data'] = $user;
        }
        
        return $_SESSION['user_data'];
    }
    
    /**
     * Obtener ID del usuario autenticado
     */
    public static function id() {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Iniciar sesión
     */
    public static function login($user) {
        // Regenerar ID de sesión ANTES de guardar datos (por seguridad)
        session_regenerate_id(true);
        
        // Ahora guardar los datos en la nueva sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_data'] = $user;
        
        // Cargar roles del usuario
        $usuarioModel = new Usuario();
        $_SESSION['user_roles'] = $usuarioModel->getRoles($user['id']);
        
        // Actualizar último login
        $usuarioModel->updateLastLogin($user['id']);
    }
    
    /**
     * Cerrar sesión
     */
    public static function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_data']);
        unset($_SESSION['user_roles']);
        unset($_SESSION['carrito']);
        session_destroy();
    }
    
    /**
     * Verificar si el usuario tiene un rol específico
     */
    public static function hasRole($roles) {
        if (!self::check()) {
            return false;
        }
        
        // Asegurar que $roles sea un array
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        
        // Obtener roles del usuario si no están en sesión
        if (!isset($_SESSION['user_roles'])) {
            $usuarioModel = new Usuario();
            $userRoles = $usuarioModel->getRoles(self::id());
            $_SESSION['user_roles'] = $userRoles;
        }
        
        $userRoles = $_SESSION['user_roles'];
        
        // Verificar si tiene alguno de los roles requeridos
        foreach ($roles as $role) {
            if (in_array($role, $userRoles)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Verificar si es admin
     */
    public static function isAdmin() {
        return self::hasRole('ADMIN');
    }
    
    /**
     * Verificar si es cliente
     */
    public static function isCliente() {
        return self::hasRole('CLIENTE');
    }
    
    /**
     * Verificar si el usuario está bloqueado
     */
    public static function isBlocked() {
        $user = self::user();
        return $user && $user['estado'] === 'BLOQUEADO';
    }
    
    /**
     * Refrescar datos del usuario en sesión
     */
    public static function refresh() {
        if (self::check()) {
            $usuarioModel = new Usuario();
            $user = $usuarioModel->find(self::id());
            $_SESSION['user_data'] = $user;
            
            // Refrescar roles
            $userRoles = $usuarioModel->getRoles(self::id());
            $_SESSION['user_roles'] = $userRoles;
        }
    }
}