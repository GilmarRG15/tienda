<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<div class="admin-header">
    <h1>Gestión de Categorías</h1>
    <a href="<?= url('/admin/categorias/create') ?>" class="btn btn-primary">+ Nueva Categoría</a>
</div>

<div class="table-container mt-3">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Slug</th>
                <th>Productos</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categorias as $cat): ?>
                <tr>
                    <td><?= $cat['id'] ?></td>
                    <td><?= e($cat['nombre']) ?></td>
                    <td><?= e($cat['slug']) ?></td>
                    <td><?= $cat['total_productos'] ?? 0 ?></td>
                    <td>
                        <a href="<?= url('/admin/categorias/' . $cat['id'] . '/edit') ?>" 
                           class="btn btn-small btn-warning">Editar</a>
                        <form method="POST" action="<?= url('/admin/categorias/' . $cat['id'] . '/delete') ?>" 
                              style="display:inline" 
                              onsubmit="return confirm('¿Eliminar esta categoría?')">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <button type="submit" class="btn btn-small btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
