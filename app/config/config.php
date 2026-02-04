<?php
/**
 * Configuración principal de la aplicación
 * Tienda de Artículos Deportivos - MVC
 */

// Configuración general
define('APP_NAME', 'Olimpus Store');
define('APP_URL', 'http://localhost/tienda/public');
define('APP_ENV', 'development'); // development | production

// Rutas del sistema
define('ROOT_PATH', dirname(dirname(__DIR__)));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('VIEWS_PATH', APP_PATH . '/views');
define('CONTROLLERS_PATH', APP_PATH . '/controllers');
define('MODELS_PATH', APP_PATH . '/models');

// Zona horaria
date_default_timezone_set('America/Guayaquil');

// Configuración de uploads
ini_set('upload_max_filesize', '5M');
ini_set('post_max_size', '6M');
ini_set('memory_limit', '128M');

// Manejo de errores
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Cargar constantes de la aplicación
require_once APP_PATH . '/config/constants.php';

// Autoload de clases
spl_autoload_register(function ($class) {
    $paths = [
        APP_PATH . '/core/' . $class . '.php',
        APP_PATH . '/controllers/' . $class . '.php',
        APP_PATH . '/models/' . $class . '.php',
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Función helper para debug
function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

// Función helper para redirección
function redirect($path) {
    header('Location: ' . APP_URL . $path);
    exit;
}

// Función helper para obtener la URL base
function url($path = '') {
    return APP_URL . $path;
}

// Función helper para assets
function asset($path) {
    return APP_URL . '/assets/' . $path;
}

// Función helper para escapar HTML
function e($string) {
    if ($string === null) {
        return '';
    }
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Función helper para generar CSRF token
function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Función helper para verificar CSRF token
function csrf_verify() {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

// Función helper para mensajes flash
function flash($key, $message = null) {
    if ($message === null) {
        // Obtener mensaje
        if (isset($_SESSION['flash'][$key])) {
            $msg = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $msg;
        }
        return null;
    } else {
        // Establecer mensaje
        $_SESSION['flash'][$key] = $message;
    }
}

// Función helper para verificar si existe un mensaje flash
function hasFlash($key) {
    return isset($_SESSION['flash'][$key]);
}

// Función helper para obtener un mensaje flash sin eliminarlo
function getFlash($key) {
    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

// Función helper para generar campo CSRF
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

// Función helper para obtener input antiguo
function old($key, $default = '') {
    if (isset($_SESSION['old'][$key])) {
        $value = $_SESSION['old'][$key];
        return $value;
    }
    return $default;
}

// Guardar input antiguo
function saveOldInput() {
    $_SESSION['old'] = $_POST;
}

// Limpiar input antiguo
function clearOldInput() {
    unset($_SESSION['old']);
}