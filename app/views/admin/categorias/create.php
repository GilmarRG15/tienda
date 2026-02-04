<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<h1>Nueva Categoría</h1>

<form method="POST" action="<?= url('/admin/categorias') ?>" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    
    <div class="form-group">
        <label for="nombre">Nombre *</label>
        <input type="text" id="nombre" name="nombre" required>
    </div>
    
    <div class="form-group">
        <label for="slug">Slug</label>
        <input type="text" id="slug" name="slug" placeholder="Se genera automáticamente si está vacío">
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Crear Categoría</button>
        <a href="<?= url('/admin/categorias') ?>" class="btn">Cancelar</a>
    </div>
</form>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
