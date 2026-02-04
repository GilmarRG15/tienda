<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container" style="margin-top: 80px; padding: 20px;">
    <h1> Mi Perfil</h1>
    
    <?php if (hasFlash('success')): ?>
        <div class="alert alert-success"><?= getFlash('success') ?></div>
    <?php endif; ?>
    
    <?php if (hasFlash('error')): ?>
        <div class="alert alert-danger"><?= getFlash('error') ?></div>
    <?php endif; ?>
    
    <?php if (hasFlash('errors')): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach (getFlash('errors') as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
        
        <!-- Información Personal -->
        <div class="card">
            <div class="card-header">
                <h2>Información Personal</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= url('/perfil') ?>">
                    <?= csrf_field() ?>
                    
                    <div class="form-group">
                        <label for="nombre">Nombre *</label>
                        <input 
                            type="text" 
                            id="nombre" 
                            name="nombre" 
                            class="form-control" 
                            value="<?= e($user['nombre']) ?>" 
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="apellido">Apellido *</label>
                        <input 
                            type="text" 
                            id="apellido" 
                            name="apellido" 
                            class="form-control" 
                            value="<?= e($user['apellido']) ?>" 
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-control" 
                            value="<?= e($user['email']) ?>" 
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label>Roles Asignados</label>
                        <div class="badge-container" style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <?php 
                            $userRoles = $_SESSION['user_roles'] ?? [];
                            if (empty($userRoles)): 
                            ?>
                                <span class="badge badge-secondary">Sin roles</span>
                            <?php else: ?>
                                <?php foreach ($userRoles as $rol): ?>
                                    <span class="badge badge-primary"><?= e($rol) ?></span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Estado de la Cuenta</label>
                        <div>
                            <?php if ($user['estado'] === 'ACTIVO'): ?>
                                <span class="badge badge-success"> Activa</span>
                            <?php else: ?>
                                <span class="badge badge-danger"> Bloqueada</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        Guardar Cambios
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Cambiar Contraseña -->
        <div class="card">
            <div class="card-header">
                <h2>Cambiar Contraseña</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= url('/perfil/password') ?>">
                    <?= csrf_field() ?>
                    
                    <div class="form-group">
                        <label for="current_password">Contraseña Actual *</label>
                        <input 
                            type="password" 
                            id="current_password" 
                            name="current_password" 
                            class="form-control" 
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">Nueva Contraseña *</label>
                        <input 
                            type="password" 
                            id="new_password" 
                            name="new_password" 
                            class="form-control" 
                            required
                            minlength="6"
                        >
                        <small class="form-text">Mínimo 6 caracteres</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password_confirmation">Confirmar Nueva Contraseña *</label>
                        <input 
                            type="password" 
                            id="new_password_confirmation" 
                            name="new_password_confirmation" 
                            class="form-control" 
                            required
                            minlength="6"
                        >
                    </div>
                    
                    <button type="submit" class="btn btn-warning">
                         Cambiar Contraseña
                    </button>
                </form>
            </div>
        </div>
        
    </div>
    
    <!-- Información adicional -->
    <div class="card" style="margin-top: 20px;">
        <div class="card-header">
            <h2>Información de la Cuenta</h2>
        </div>
        <div class="card-body">
            <div class="info-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div>
                    <strong> Fecha de Registro:</strong><br>
                    <?= date('d/m/Y H:i', strtotime($user['fecha_creacion'])) ?>
                </div>
                <div>
                    <strong> Último Acceso:</strong><br>
                    <?= $user['ultimo_login'] ? date('d/m/Y H:i', strtotime($user['ultimo_login'])) : 'Nunca' ?>
                </div>
                <div>
                    <strong> ID de Usuario:</strong><br>
                    #<?= $user['id'] ?>
                </div>
            </div>
        </div>
    </div>
    
</div>

<style>
.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.card-header {
    background: #f8f9fa;
    padding: 15px 20px;
    border-bottom: 1px solid #dee2e6;
}

.card-header h2 {
    margin: 0;
    font-size: 1.25rem;
    color: #333;
}

.card-body {
    padding: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
}

.form-text {
    display: block;
    margin-top: 5px;
    font-size: 12px;
    color: #666;
}

.badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.badge-primary {
    background: #007bff;
    color: white;
}

.badge-success {
    background: #28a745;
    color: white;
}

.badge-danger {
    background: #dc3545;
    color: white;
}

.badge-secondary {
    background: #6c757d;
    color: white;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
}

.btn-warning {
    background: #ffc107;
    color: #212529;
}

.btn-warning:hover {
    background: #e0a800;
}

.alert {
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.alert-success {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.alert-danger {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.alert ul {
    margin: 0;
    padding-left: 20px;
}

@media (max-width: 768px) {
    .row {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
