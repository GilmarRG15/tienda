<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<div class="auth-container">
    <div class="auth-card">
        <h1>Iniciar Sesión</h1>
        
        <form method="POST" action="<?= url('/login') ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= old('email') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
        </form>
        
        <p class="auth-link">¿No tienes cuenta? <a href="<?= url('/register') ?>">Regístrate aquí</a></p>
    </div>
</div>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
