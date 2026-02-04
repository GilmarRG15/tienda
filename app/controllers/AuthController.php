<?php
/**
 * AuthController - Gestión de autenticación
 * Refactorizado con constantes y validaciones mejoradas
 */

class AuthController extends Controller {
    
    public function showLogin() {
        Middleware::requireGuest();
        $this->render('auth/login', ['titulo' => 'Iniciar Sesión']);
    }
    
    public function login() {
        Middleware::requireGuest();
        
        if (!csrf_verify()) {
            $this->redirectWith('/login', 'error', MSG_ERROR_CSRF);
        }
        
        $email = $this->getPostData('email', '');
        $password = $this->getPostData('password', '');
        
        // Validar que los campos estén completos
        $validation = $this->validate($_POST, [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        if ($validation !== true) {
            saveOldInput();
            flash('errors', $validation);
            redirect('/login');
        }
        
        // Verificar credenciales
        $usuarioModel = new Usuario();
        $user = $usuarioModel->verificarCredenciales($email, $password);
        
        if (!$user) {
            flash('error', 'Credenciales incorrectas');
            redirect('/login');
        }
        
        // Verificar si la cuenta está bloqueada
        if ($user['estado'] === USUARIO_ESTADO_BLOQUEADO) {
            flash('error', 'Tu cuenta ha sido bloqueada. Contacta al administrador');
            redirect('/login');
        }
        
        // Iniciar sesión
        Auth::login($user);
        
        flash('success', '¡Bienvenido ' . e($user['nombre']) . '!');
        redirect('/');
    }
    
    public function showRegister() {
        Middleware::requireGuest();
        $this->render('auth/register', ['titulo' => 'Registro']);
    }
    
    public function register() {
        Middleware::requireGuest();
        
        if (!csrf_verify()) {
            $this->redirectWith('/register', 'error', MSG_ERROR_CSRF);
        }
        
        // Validar datos del formulario
        $validation = $this->validate($_POST, [
            'nombre' => 'required|min:2|max:100',
            'apellido' => 'required|min:2|max:100',
            'email' => 'required|email|max:150',
            'password' => 'required|min:' . MIN_PASSWORD_LENGTH . '|confirmed'
        ]);
        
        if ($validation !== true) {
            saveOldInput();
            flash('errors', $validation);
            redirect('/register');
        }
        
        $usuarioModel = new Usuario();
        
        // Verificar que el email no esté registrado
        if ($usuarioModel->findByEmail($this->getPostData('email'))) {
            saveOldInput();
            flash('error', 'El email ya está registrado');
            redirect('/register');
        }
        
        try {
            // Crear nuevo usuario
            $userId = $usuarioModel->createUser([
                'nombre' => $this->getPostData('nombre'),
                'apellido' => $this->getPostData('apellido'),
                'email' => $this->getPostData('email'),
                'password' => $this->getPostData('password'),
                'password_confirmation' => $this->getPostData('password_confirmation')
            ]);
            
            // Asignar rol de cliente
            $usuarioModel->assignRole($userId, ROL_CLIENTE);
            
            // Iniciar sesión automáticamente
            $user = $usuarioModel->find($userId);
            Auth::login($user);
            
            flash('success', '¡Registro exitoso! Bienvenido ' . e($user['nombre']));
            redirect('/');
            
        } catch (Exception $e) {
            flash('error', 'Error al crear el usuario. Intenta nuevamente');
            redirect('/register');
        }
    }
    
    public function logout() {
        Auth::logout();
        flash('success', 'Sesión cerrada correctamente');
        redirect('/');
    }
}
