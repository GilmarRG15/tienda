<?php require_once __DIR__ . '/../../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../../layouts/navbar.php'; ?>

<div class="container" style="margin-top: 80px; padding: 20px;">
    <div class="page-header">
        <h1>Editar Producto</h1>
    </div>
    
    <?php if (hasFlash('success')): ?>
        <div class="alert alert-success"><?= getFlash('success') ?></div>
    <?php endif; ?>
    
    <?php if (hasFlash('error')): ?>
        <div class="alert alert-danger"><?= getFlash('error') ?></div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?= url('/admin/productos/' . $producto['id']) ?>">
                <?= csrf_field() ?>
                
                <div class="form-group">
                    <label for="nombre">Nombre del Producto *</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" 
                           value="<?= e($producto['nombre']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="marca">Marca</label>
                    <input type="text" id="marca" name="marca" class="form-control" 
                           value="<?= e($producto['marca'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" class="form-control" 
                              rows="4"><?= e($producto['descripcion'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="categoria_id">Categoría *</label>
                    <select id="categoria_id" name="categoria_id" class="form-control" required>
                        <option value="">-- Seleccionar --</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= $categoria['id'] ?>" 
                                    <?= $producto['categoria_id'] == $categoria['id'] ? 'selected' : '' ?>>
                                <?= e($categoria['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="activo" value="1" 
                               <?= $producto['activo'] ? 'checked' : '' ?>>
                        <span>Producto activo (visible en catálogo)</span>
                    </label>
                </div>
                
                <div class="alert alert-info">
                    <strong>Nota:</strong> El precio y stock se manejan en las <strong>Variantes del Producto</strong>.
                    Después de actualizar, puedes gestionar las variantes de este producto.
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Actualizar Producto</button>
                    <a href="<?= url('/admin/productos') ?>" class="btn btn-secondary">Cancelar</a>
                    
                    <?php if (isset($producto['id'])): ?>
                        <a href="<?= url('/admin/productos/' . $producto['id'] . '/variantes') ?>" 
                           class="btn btn-info">
                            Gestionar Variantes
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
    
    <div style="margin-top: 20px;">
        <a href="<?= url('/admin/productos') ?>" class="btn btn-secondary">
            ← Volver a Productos
        </a>
    </div>
</div>

<style>
.page-header {
    margin-bottom: 20px;
}

.page-header h1 {
    margin: 0;
    font-size: 1.75rem;
}

.card { 
    background: white; 
    border-radius: 8px; 
    box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
    margin-top: 20px; 
}

.card-body { 
    padding: 30px; 
}

.form-group { 
    margin-bottom: 20px; 
}

.form-group label { 
    display: block; 
    margin-bottom: 8px; 
    font-weight: 600;
    color: #374151;
}

.form-control { 
    width: 100%; 
    padding: 10px 12px; 
    border: 1px solid #d1d5db; 
    border-radius: 6px;
    font-size: 14px;
}

.form-control:focus {
    outline: none;
    border-color: #E10600;
    box-shadow: 0 0 0 3px rgba(225, 6, 0, 0.1);
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    font-weight: normal;
}

.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.form-actions { 
    display: flex; 
    gap: 10px; 
    margin-top: 30px;
    flex-wrap: wrap;
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

.alert-info {
    background: #dbeafe;
    border: 1px solid #3b82f6;
    color: #1e40af;
    margin-top: 20px;
}

.btn {
    padding: 10px 20px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
    transition: all 0.2s;
}

.btn-primary {
    background: #E10600;
    color: white;
}

.btn-primary:hover {
    background: #B40500;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

.btn-info {
    background: #3b82f6;
    color: white;
}

.btn-info:hover {
    background: #2563eb;
}
</style>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>