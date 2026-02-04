<?php require_once __DIR__ . '/../../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../../layouts/navbar.php'; ?>

<div class="container" style="margin-top: 80px; padding: 20px;">
    <h1>Gestión de Usuarios</h1>
    
    <?php if (hasFlash('success')): ?>
        <div class="alert alert-success"><?= getFlash('success') ?></div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-body">
            <?php if (empty($usuarios)): ?>
                <div class="empty-state"><p>No hay usuarios registrados</p></div>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Roles</th>
                            <th>Estado</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td>#<?= $usuario['id'] ?></td>
                                <td><?= e($usuario['nombre'] . ' ' . $usuario['apellido']) ?></td>
                                <td><?= e($usuario['email']) ?></td>
                                <td>
                                    <?php 
                                    $roles = explode(', ', $usuario['roles'] ?? '');
                                    foreach ($roles as $rol): 
                                        if ($rol):
                                    ?>
                                        <span class="badge badge-primary"><?= e($rol) ?></span>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </td>
                                <td>
                                    <?php if ($usuario['estado'] === 'ACTIVO'): ?>
                                        <span class="badge badge-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Bloqueado</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y', strtotime($usuario['fecha_creacion'])) ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= url('/admin/usuarios/' . $usuario['id'] . '/edit') ?>" class="btn btn-sm btn-warning" title="Editar">Editar</a>
                                        <form method="POST" action="<?= url('/admin/usuarios/' . $usuario['id'] . '/toggle-estado') ?>" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-secondary" title="Cambiar Estado">
                                                <?= $usuario['estado'] === 'ACTIVO' ? 'Bloquear' : 'Desbloquear' ?>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <div style="margin-top: 20px;">
        <a href="<?= url('/admin') ?>" class="btn btn-secondary">← Volver al Panel</a>
    </div>
</div>

<style>
.card { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-top: 20px; }
.card-body { padding: 20px; }
.table { width: 100%; border-collapse: collapse; }
.table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
.table th { background: #f9fafb; font-weight: 600; }
.btn-group { display: flex; gap: 5px; }
.badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; margin: 2px; }
.badge-primary { background: #2563eb; color: white; }
.badge-success { background: #10b981; color: white; }
.badge-danger { background: #ef4444; color: white; }
.empty-state { text-align: center; padding: 40px; color: #6b7280; }
.alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; }
.alert-success { background: #d1fae5; border: 1px solid #10b981; color: #065f46; }
</style>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
