<?php
/**
 * PerfilController - Gestión del perfil del usuario
 * Refactorizado con constantes y validaciones mejoradas
 */

class PerfilController extends Controller {
    
    /**
     * Muestra el perfil del usuario
     */
    public function index() {
        Middleware::requireLogin();
        
        $user = Auth::user();
        
        $this->render('perfil/index', [
            'user' => $user,
            'titulo' => 'Mi Perfil'
        ]);
    }
    
    /**
     * Actualiza los datos del perfil
     */
    public function update() {
        Middleware::requireLogin();
        
        if (!csrf_verify()) {
            $this->redirectWith('/perfil', 'error', MSG_ERROR_CSRF);
        }
        
        $usuarioModel = new Usuario();
        $userId = Auth::id();
        
        // Validar datos del formulario
        $validation = $this->validate($_POST, [
            'nombre' => 'required|min:2|max:100',
            'apellido' => 'required|min:2|max:100',
            'email' => 'required|email|max:150'
        ]);
        
        if ($validation !== true) {
            flash('errors', $validation);
            redirect('/perfil');
        }
        
        $email = $this->getPostData('email');
        
        // Verificar que el email no esté siendo usado por otro usuario
        $existingUser = $usuarioModel->findByEmail($email);
        if ($existingUser && $existingUser['id'] != $userId) {
            flash('error', 'El email ya está registrado por otro usuario');
            redirect('/perfil');
        }
        
        try {
            // Actualizar datos del usuario
            $usuarioModel->update($userId, [
                'nombre' => $this->getPostData('nombre'),
                'apellido' => $this->getPostData('apellido'),
                'email' => $email
            ]);
            
            // Actualizar datos en sesión
            Auth::refresh();
            
            flash('success', MSG_SUCCESS_ACTUALIZADO);
            redirect('/perfil');
            
        } catch (Exception $e) {
            flash('error', 'Error al actualizar el perfil');
            redirect('/perfil');
        }
    }
    
    /**
     * Cambia la contraseña del usuario
     */
    public function changePassword() {
        Middleware::requireLogin();
        
        if (!csrf_verify()) {
            $this->redirectWith('/perfil', 'error', MSG_ERROR_CSRF);
        }
        
        // Validar campos del formulario
        $validation = $this->validate($_POST, [
            'current_password' => 'required',
            'new_password' => 'required|min:' . MIN_PASSWORD_LENGTH . '|confirmed'
        ]);
        
        if ($validation !== true) {
            flash('errors', $validation);
            redirect('/perfil');
        }
        
        $usuarioModel = new Usuario();
        $userId = Auth::id();
        $user = $usuarioModel->find($userId);
        
        $currentPassword = $this->getPostData('current_password');
        $newPassword = $this->getPostData('new_password');
        
        // Verificar que la contraseña actual sea correcta
        if (!password_verify($currentPassword, $user['password_hash'])) {
            flash('error', 'La contraseña actual es incorrecta');
            redirect('/perfil');
        }
        
        // Verificar que la nueva contraseña sea diferente
        if (password_verify($newPassword, $user['password_hash'])) {
            flash('error', 'La nueva contraseña debe ser diferente a la actual');
            redirect('/perfil');
        }
        
        try {
            // Actualizar contraseña
            $usuarioModel->updatePassword($userId, $newPassword);
            
            flash('success', 'Contraseña actualizada correctamente');
            redirect('/perfil');
            
        } catch (Exception $e) {
            flash('error', 'Error al actualizar la contraseña');
            redirect('/perfil');
        }
    }
}
