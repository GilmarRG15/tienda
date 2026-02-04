<?php require_once __DIR__ . '/../../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../../layouts/navbar.php'; ?>

<div class="container" style="margin-top: 80px; padding: 20px;">
    <div class="page-header">
        <h1>Gestión de Productos</h1>
        <a href="<?= url('/admin/productos/create') ?>" class="btn btn-primary">
            Nuevo Producto
        </a>
    </div>
    
    <?php if (hasFlash('success')): ?>
        <div class="alert alert-success"><?= getFlash('success') ?></div>
    <?php endif; ?>
    
    <?php if (hasFlash('error')): ?>
        <div class="alert alert-danger"><?= getFlash('error') ?></div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-body">
            <?php if (empty($productos)): ?>
                <div class="empty-state">
                    <p>No hay productos registrados aún</p>
                    <a href="<?= url('/admin/productos/create') ?>" class="btn btn-primary">
                        Crear Primer Producto
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos as $producto): ?>
                                <tr>
                                    <td>#<?= $producto['id'] ?></td>
                                    <td>
                                        <strong><?= e($producto['nombre']) ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?= e($producto['categoria_nombre'] ?? 'Sin categoría') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($producto['precio'])): ?>
                                            <strong class="producto-precio">$<?= number_format($producto['precio'], 2) ?></strong>
                                        <?php else: ?>
                                            <span class="text-muted">Sin precio</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($producto['total_variantes'] > 0): ?>
                                            <span class="badge badge-info">
                                                <?= $producto['stock_total'] ?? 0 ?> unidades
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Sin variantes</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($producto['activo']): ?>
                                            <span class="badge badge-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?= url('/producto/' . $producto['id']) ?>" 
                                               class="btn btn-sm btn-info" 
                                               title="Ver"
                                               target="_blank">
                                                Ver
                                            </a>
                                            <a href="<?= url('/admin/productos/' . $producto['id'] . '/edit') ?>" 
                                               class="btn btn-sm btn-warning" 
                                               title="Editar">
                                                Editar
                                            </a>
                                            <form method="POST" 
                                                  action="<?= url('/admin/productos/' . $producto['id'] . '/delete') ?>" 
                                                  style="display: inline;"
                                                  onsubmit="return confirm('¿Estás seguro de eliminar este producto?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div style="margin-top: 20px;">
        <a href="<?= url('/admin') ?>" class="btn btn-secondary">
            ← Volver al Panel
        </a>
    </div>
</div>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.page-header h1 {
    margin: 0;
    font-size: 1.75rem;
}

.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.card-body {
    padding: 20px;
}

.table-responsive {
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th,
.table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.table th {
    background: #f9fafb;
    font-weight: 600;
    color: #374151;
}

.table tr:hover {
    background: #f9fafb;
}

.btn-group {
    display: flex;
    gap: 5px;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 14px;
}

.btn-info {
    background: #3b82f6;
    color: white;
}

.btn-warning {
    background: #f59e0b;
    color: white;
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.badge-success {
    background: #10b981;
    color: white;
}

.badge-secondary {
    background: #6b7280;
    color: white;
}

.badge-info {
    background: #06b6d4;
    color: white;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
}

.empty-state p {
    font-size: 1.25rem;
    color: #6b7280;
    margin-bottom: 20px;
}

.alert {
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.alert-success {
    background: #d1fae5;
    border: 1px solid #10b981;
    color: #065f46;
}

.alert-danger {
    background: #fee2e2;
    border: 1px solid #ef4444;
    color: #991b1b;
}
</style>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>