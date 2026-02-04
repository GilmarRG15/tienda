<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<div class="auth-container">
    <div class="auth-card">
        <h1>Registro</h1>
        
        <form method="POST" action="<?= url('/register') ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" value="<?= old('nombre') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="apellido">Apellido</label>
                <input type="text" id="apellido" name="apellido" value="<?= old('apellido') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= old('email') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="password_confirmation">Confirmar Contraseña</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Registrarse</button>
        </form>
        
        <p class="auth-link">¿Ya tienes cuenta? <a href="<?= url('/login') ?>">Inicia sesión aquí</a></p>
    </div>
</div>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
