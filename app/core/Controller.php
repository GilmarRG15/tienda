<?php
/**
 * Controller - Clase base para todos los controladores
 */

class Controller {
    
    /**
     * Renderizar una vista
     */
    protected function render($view, $data = []) {
        extract($data);
        
        $viewPath = VIEWS_PATH . '/' . str_replace('.', '/', $view) . '.php';
        
        if (!file_exists($viewPath)) {
            die("Vista {$view} no encontrada en {$viewPath}");
        }
        
        require_once $viewPath;
    }
    
    /**
     * Renderizar JSON
     */
    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Validar datos
     */
    protected function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $ruleList = explode('|', $rule);
            
            foreach ($ruleList as $r) {
                $ruleParts = explode(':', $r);
                $ruleName = $ruleParts[0];
                $ruleValue = $ruleParts[1] ?? null;
                
                switch ($ruleName) {
                    case 'required':
                        if (!isset($data[$field]) || trim($data[$field]) === '') {
                            $errors[$field][] = "El campo {$field} es requerido";
                        }
                        break;
                    
                    case 'email':
                        if (isset($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = "El campo {$field} debe ser un email válido";
                        }
                        break;
                    
                    case 'min':
                        if (isset($data[$field]) && strlen(trim($data[$field])) < $ruleValue) {
                            $errors[$field][] = "El campo {$field} debe tener al menos {$ruleValue} caracteres";
                        }
                        break;
                    
                    case 'max':
                        if (isset($data[$field]) && strlen(trim($data[$field])) > $ruleValue) {
                            $errors[$field][] = "El campo {$field} no debe exceder {$ruleValue} caracteres";
                        }
                        break;
                    
                    case 'numeric':
                        if (isset($data[$field]) && !is_numeric($data[$field])) {
                            $errors[$field][] = "El campo {$field} debe ser numérico";
                        }
                        break;
                    
                    case 'confirmed':
                        $confirmField = $field . '_confirmation';
                        if (isset($data[$field]) && (!isset($data[$confirmField]) || $data[$field] !== $data[$confirmField])) {
                            $errors[$field][] = "El campo {$field} no coincide con su confirmación";
                        }
                        break;
                }
            }
        }
        
        return empty($errors) ? true : $errors;
    }
    
    /**
     * Sanitizar datos de entrada
     */
    protected function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInput'], $data);
        }
        return trim(htmlspecialchars($data, ENT_QUOTES, 'UTF-8'));
    }
    
    /**
     * Obtener y sanitizar datos POST
     */
    protected function getPostData($key, $default = null) {
        if (!isset($_POST[$key])) {
            return $default;
        }
        return is_array($_POST[$key]) ? 
            $this->sanitizeInput($_POST[$key]) : 
            trim($_POST[$key]);
    }
    
    /**
     * Redireccionar
     */
    protected function redirect($path) {
        redirect($path);
    }
    
    /**
     * Redireccionar con mensaje
     */
    protected function redirectWith($path, $type, $message) {
        flash($type, $message);
        redirect($path);
    }
}
