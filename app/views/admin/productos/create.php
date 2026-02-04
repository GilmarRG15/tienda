<?php require_once __DIR__ . '/../../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../../layouts/navbar.php'; ?>

<div class="container" style="margin-top: 80px; padding: 20px;">
    <div class="page-header">
        <h1>Crear Producto</h1>
        <p class="subtitle">Completa la información del producto y agrega sus variantes (talla, color, precio, stock)</p>
    </div>
    
    <?php if (hasFlash('error')): ?>
        <div class="alert alert-danger"><?= getFlash('error') ?></div>
    <?php endif; ?>
    
    <form method="POST" action="<?= url('/admin/productos/store-with-variants') ?>" enctype="multipart/form-data" id="form-producto">
        <?= csrf_field() ?>
        
        <!-- INFORMACIÓN BÁSICA DEL PRODUCTO -->
        <div class="card">
            <div class="card-header">
                <h2> Información del Producto</h2>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-8">
                        <label for="nombre">Nombre del Producto *</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" 
                               value="<?= old('nombre') ?>" required>
                        <small class="form-text">Ejemplo: Zapatillas Nike Air Max, Camiseta Adidas Running</small>
                    </div>
                    
                    <div class="form-group col-md-4">
                        <label for="marca">Marca</label>
                        <input type="text" id="marca" name="marca" class="form-control" 
                               value="<?= old('marca') ?>">
                        <small class="form-text">Ejemplo: Nike, Adidas, Puma</small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" class="form-control" 
                              rows="4"><?= old('descripcion') ?></textarea>
                    <small class="form-text">Describe las características principales del producto</small>
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="categoria_id">Categoría *</label>
                        <select id="categoria_id" name="categoria_id" class="form-control" required>
                            <option value="">-- Seleccionar categoría --</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= $categoria['id'] ?>" 
                                        <?= old('categoria_id') == $categoria['id'] ? 'selected' : '' ?>>
                                    <?= e($categoria['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="imagen">Imagen del Producto</label>
                        <input type="file" id="imagen" name="imagen" class="form-control" 
                               accept="image/jpeg,image/jpg,image/png,image/webp">
                        <small class="form-text">JPG, PNG, WEBP. Máximo 5MB</small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="activo" value="1" checked>
                        <span>Producto activo (visible en catálogo)</span>
                    </label>
                </div>
            </div>
        </div>
        
        <!-- VARIANTES DEL PRODUCTO -->
        <div class="card">
            <div class="card-header">
                <h2> Variantes del Producto</h2>
                <p>Las variantes permiten gestionar diferentes tamaños, colores o modelos del mismo producto</p>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong> Importante:</strong> Debes agregar al menos una variante para que el producto esté disponible para la venta.
                    Cada variante debe tener un precio y stock definido.
                </div>
                
                <!-- Tabla de variantes -->
                <div class="table-responsive">
                    <table class="table" id="tabla-variantes">
                        <thead>
                            <tr>
                                <th style="width: 20%;">Talla/Tamaño</th>
                                <th style="width: 20%;">Color</th>
                                <th style="width: 20%;">Precio ($) *</th>
                                <th style="width: 20%;">Stock *</th>
                                <th style="width: 15%;">SKU (Auto)</th>
                                <th style="width: 5%;"></th>
                            </tr>
                        </thead>
                        <tbody id="variantes-list">
                            <!-- Las variantes se agregarán aquí dinámicamente -->
                        </tbody>
                    </table>
                </div>
                
                <button type="button" class="btn btn-success" onclick="agregarVariante()">
                     Agregar Variante
                </button>
                
                <div id="variantes-empty" class="empty-state">
                    <p> No hay variantes agregadas aún</p>
                    <p class="text-muted">Haz clic en "Agregar Variante" para comenzar</p>
                </div>
            </div>
        </div>
        
        <!-- ACCIONES -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary" id="btn-guardar">
                 Crear Producto con Variantes
            </button>
            <a href="<?= url('/admin/productos') ?>" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<style>
.container {
    max-width: 1200px;
    margin: 0 auto;
}

.page-header {
    margin-bottom: 30px;
}

.page-header h1 {
    margin: 0 0 10px 0;
    font-size: 2rem;
    color: #1f2937;
}

.subtitle {
    color: #6b7280;
    font-size: 0.95rem;
    margin: 0;
}

.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 24px;
}

.card-header {
    padding: 20px 30px;
    border-bottom: 1px solid #e5e7eb;
}

.card-header h2 {
    margin: 0 0 5px 0;
    font-size: 1.25rem;
    color: #1f2937;
}

.card-header p {
    margin: 0;
    font-size: 0.9rem;
    color: #6b7280;
}

.card-body {
    padding: 30px;
}

.form-row {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.form-row .form-group {
    flex: 1;
    min-width: 250px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #374151;
    font-size: 0.9rem;
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #E10600;
    box-shadow: 0 0 0 3px rgba(225, 6, 0, 0.1);
}

.form-text {
    display: block;
    margin-top: 5px;
    font-size: 12px;
    color: #6b7280;
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

/* Tabla de variantes */
.table-responsive {
    overflow-x: auto;
    margin-bottom: 20px;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table thead {
    background: #f9fafb;
}

.table th {
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: #374151;
    font-size: 0.875rem;
    border-bottom: 2px solid #e5e7eb;
}

.table td {
    padding: 12px;
    border-bottom: 1px solid #e5e7eb;
}

.table tbody tr:hover {
    background: #f9fafb;
}

.table input[type="text"],
.table input[type="number"] {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    font-size: 13px;
}

.table input[type="text"]:focus,
.table input[type="number"]:focus {
    outline: none;
    border-color: #E10600;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #6b7280;
    display: none;
}

.empty-state p {
    margin: 5px 0;
}

.empty-state.show {
    display: block;
}

/* Botones */
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

.btn-success {
    background: #10b981;
    color: white;
}

.btn-success:hover {
    background: #059669;
}

.btn-danger {
    background: #ef4444;
    color: white;
    padding: 6px 12px;
    font-size: 12px;
}

.btn-danger:hover {
    background: #dc2626;
}

.alert {
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
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
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 30px;
    margin-bottom: 30px;
}

@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        text-align: center;
    }
}
</style>

<script>
let contadorVariantes = 0;

// Agregar una nueva variante
function agregarVariante() {
    contadorVariantes++;
    
    const tbody = document.getElementById('variantes-list');
    const row = document.createElement('tr');
    row.id = `variante-${contadorVariantes}`;
    
    row.innerHTML = `
        <td>
            <input type="text" name="variantes[${contadorVariantes}][talla]" 
                   class="form-control" placeholder="Ej: M, L, 42">
        </td>
        <td>
            <input type="text" name="variantes[${contadorVariantes}][color]" 
                   class="form-control" placeholder="Ej: Rojo, Azul">
        </td>
        <td>
            <input type="number" name="variantes[${contadorVariantes}][precio]" 
                   class="form-control" placeholder="0.00" step="0.01" min="0" required>
        </td>
        <td>
            <input type="number" name="variantes[${contadorVariantes}][stock]" 
                   class="form-control" placeholder="0" min="0" required>
        </td>
        <td>
            <input type="text" name="variantes[${contadorVariantes}][sku]" 
                   class="form-control" placeholder="Auto-generado" readonly 
                   style="background: #f3f4f6;">
        </td>
        <td>
            <button type="button" class="btn btn-danger" onclick="eliminarVariante(${contadorVariantes})">
                
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    actualizarEstadoTabla();
}

// Eliminar una variante
function eliminarVariante(id) {
    const row = document.getElementById(`variante-${id}`);
    if (row) {
        row.remove();
        actualizarEstadoTabla();
    }
}

// Actualizar el estado de la tabla (mostrar/ocultar mensaje vacío)
function actualizarEstadoTabla() {
    const tbody = document.getElementById('variantes-list');
    const emptyState = document.getElementById('variantes-empty');
    
    if (tbody.children.length === 0) {
        emptyState.classList.add('show');
    } else {
        emptyState.classList.remove('show');
    }
}

// Validar formulario antes de enviar
document.getElementById('form-producto').addEventListener('submit', function(e) {
    const tbody = document.getElementById('variantes-list');
    
    if (tbody.children.length === 0) {
        e.preventDefault();
        alert(' Debes agregar al menos una variante para el producto');
        return false;
    }
    
    // Validar que todas las variantes tengan precio y stock
    let valid = true;
    const precios = document.querySelectorAll('input[name*="[precio]"]');
    const stocks = document.querySelectorAll('input[name*="[stock]"]');
    
    precios.forEach(input => {
        if (!input.value || parseFloat(input.value) <= 0) {
            valid = false;
            input.style.borderColor = '#ef4444';
        }
    });
    
    stocks.forEach(input => {
        if (!input.value || parseInt(input.value) < 0) {
            valid = false;
            input.style.borderColor = '#ef4444';
        }
    });
    
    if (!valid) {
        e.preventDefault();
        alert(' Todas las variantes deben tener un precio mayor a 0 y un stock válido');
        return false;
    }
});

// Inicializar: agregar una variante por defecto
window.addEventListener('DOMContentLoaded', function() {
    agregarVariante();
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
