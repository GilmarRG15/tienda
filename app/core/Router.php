<?php
/**
 * Router - Sistema de enrutamiento simple
 */

class Router {
    private $routes = [];
    private $notFoundCallback;
    
    public function get($path, $callback) {
        $this->addRoute('GET', $path, $callback);
    }
    
    public function post($path, $callback) {
        $this->addRoute('POST', $path, $callback);
    }
    
    private function addRoute($method, $path, $callback) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback
        ];
    }
    
    public function notFound($callback) {
        $this->notFoundCallback = $callback;
    }
    
    public function run() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $_SERVER['REQUEST_URI'];
        
        // Remover query string
        $requestUri = strtok($requestUri, '?');
        
        // Remover base path si existe - CORREGIDO
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        if ($basePath === '/' || $basePath === '\\') {
            $basePath = '';
        }
        $requestUri = substr($requestUri, strlen($basePath));
        
        // Asegurar que inicia con /
        if ($requestUri === '' || $requestUri === false || $requestUri[0] !== '/') {
            $requestUri = '/' . ltrim($requestUri, '/');
        }
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }
            
            $pattern = $this->convertToRegex($route['path']);
            
            if (preg_match($pattern, $requestUri, $matches)) {
                array_shift($matches); // Remover match completo
                
                $callback = $route['callback'];
                
                if (is_callable($callback)) {
                    call_user_func_array($callback, $matches);
                } elseif (is_string($callback)) {
                    $this->callControllerAction($callback, $matches);
                }
                
                return;
            }
        }
        
        // No se encontró ruta
        if ($this->notFoundCallback) {
            call_user_func($this->notFoundCallback);
        } else {
            http_response_code(404);
            echo "404 - Página no encontrada";
        }
    }
    
    private function convertToRegex($path) {
        // Convertir :param a expresión regular
        $pattern = preg_replace('/\/:([^\/]+)/', '/(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
    
    private function callControllerAction($callback, $params) {
        list($controller, $action) = explode('@', $callback);
        
        $controllerClass = $controller;
        
        if (!class_exists($controllerClass)) {
            die("Controller {$controllerClass} no encontrado");
        }
        
        $controllerInstance = new $controllerClass();
        
        if (!method_exists($controllerInstance, $action)) {
            die("Método {$action} no existe en {$controllerClass}");
        }
        
        // Filtrar solo los parámetros numéricos (eliminar los nombres)
        $filteredParams = array_filter($params, function($key) {
            return is_numeric($key);
        }, ARRAY_FILTER_USE_KEY);
        
        call_user_func_array([$controllerInstance, $action], $filteredParams);
    }
}