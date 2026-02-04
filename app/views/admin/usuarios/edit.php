<?php require_once __DIR__ . '/../../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../../layouts/navbar.php'; ?>

<div class="container" style="margin-top: 80px; padding: 20px;">
    <h1> Editar Usuario</h1>
    
    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?= url('/admin/usuarios/' . $usuario['id']) ?>">
                <?= csrf_field() ?>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nombre">Nombre *</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" value="<?= e($usuario['nombre']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="apellido">Apellido *</label>
                        <input type="text" id="apellido" name="apellido" class="form-control" value="<?= e($usuario['apellido']) ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= e($usuario['email']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Roles *</label>
                    <?php foreach ($roles as $rol): ?>
                        <div>
                            <label>
                                <input type="checkbox" name="roles[]" value="<?= e($rol['nombre']) ?>" 
                                    <?= in_array($rol['nombre'], $rolesUsuario) ? 'checked' : '' ?>>
                                <?= e($rol['nombre']) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <a href="<?= url('/admin/usuarios') ?>" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.card { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-top: 20px; }
.card-body { padding: 30px; }
.form-group { margin-bottom: 20px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
.form-control { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; }
.form-actions { display: flex; gap: 10px; margin-top: 30px; }
</style>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
