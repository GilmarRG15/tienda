<?php
/**
 * Middleware - Protección de rutas y validaciones
 * Refactorizado para usar constantes y mejor organización
 */

class Middleware {
    
    /**
     * Requerir que el usuario esté autenticado
     */
    public static function requireLogin() {
        if (!Auth::check()) {
            flash('error', 'Debes iniciar sesión para acceder a esta página');
            redirect('/login');
        }
        
        // Verificar si está bloqueado
        if (Auth::isBlocked()) {
            Auth::logout();
            flash('error', 'Tu cuenta ha sido bloqueada. Contacta al administrador');
            redirect('/login');
        }
    }
    
    /**
     * Requerir que el usuario sea invitado (no autenticado)
     */
    public static function requireGuest() {
        if (Auth::check()) {
            redirect('/');
        }
    }
    
    /**
     * Requerir roles específicos
     */
    public static function requireRole($roles) {
        self::requireLogin();
        
        if (!Auth::hasRole($roles)) {
            flash('error', MSG_ERROR_PERMISOS);
            redirect('/');
        }
    }
    
    /**
     * Requerir rol de administrador
     */
    public static function requireAdmin() {
        self::requireRole(ROL_ADMIN);
    }
    
    /**
     * Requerir rol de gestor de productos
     */
    public static function requireGestorProductos() {
        self::requireRole([ROL_ADMIN, ROL_GESTOR_PRODUCTOS]);
    }
    
    /**
     * Requerir rol de gestor de inventario
     */
    public static function requireGestorInventario() {
        self::requireRole([ROL_ADMIN, ROL_GESTOR_INVENTARIO]);
    }
    
    /**
     * Requerir rol de despachador
     */
    public static function requireDespachador() {
        self::requireRole([ROL_ADMIN, ROL_DESPACHADOR]);
    }
    
    /**
     * Verificar CSRF token
     */
    public static function verifyCsrf() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_verify()) {
                flash('error', MSG_ERROR_CSRF);
                redirect($_SERVER['HTTP_REFERER'] ?? '/');
            }
        }
    }
}
