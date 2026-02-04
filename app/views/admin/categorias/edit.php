<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<h1>Editar Categoría</h1>

<form method="POST" action="<?= url('/admin/categorias/' . $categoria['id']) ?>" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    
    <div class="form-group">
        <label for="nombre">Nombre *</label>
        <input type="text" id="nombre" name="nombre" value="<?= e($categoria['nombre']) ?>" required>
    </div>
    
    <div class="form-group">
        <label for="slug">Slug *</label>
        <input type="text" id="slug" name="slug" value="<?= e($categoria['slug']) ?>" required>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="<?= url('/admin/categorias') ?>" class="btn">Cancelar</a>
    </div>
</form>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
