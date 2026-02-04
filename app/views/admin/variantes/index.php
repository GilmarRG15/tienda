<?php require_once __DIR__ . '/../../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../../layouts/navbar.php'; ?>

<div class="container" style="margin-top: 80px; padding: 20px;">
    <h1>Gestión de Variantes de Inventario</h1>
    
    <?php if (hasFlash('success')): ?>
        <div class="alert alert-success"><?= getFlash('success') ?></div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-body">
            <?php if (empty($variantes)): ?>
                <div class="empty-state">
                    <p>No hay variantes registradas aún</p>
                    <p>Las variantes permiten gestionar diferentes tamaños, colores o modelos del mismo producto.</p>
                </div>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Producto</th>
                            <th>Talla</th>
                            <th>Color</th>
                            <th>Stock</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($variantes as $variante): ?>
                            <tr>
                                <td>#<?= $variante['id'] ?></td>
                                <td><?= e($variante['producto_nombre'] ?? 'N/A') ?></td>
                                <td><?= e($variante['talla'] ?? '-') ?></td>
                                <td><?= e($variante['color'] ?? '-') ?></td>
                                <td><span class="badge badge-info"><?= $variante['stock'] ?></span></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" 
                                            onclick="editarVariante(<?= $variante['id'] ?>, '<?= e($variante['talla'] ?? '') ?>', '<?= e($variante['color'] ?? '') ?>', <?= $variante['stock'] ?>)" 
                                            title="Editar">
                                        Editar
                                    </button>
                                    <button class="btn btn-sm btn-danger" 
                                            onclick="eliminarVariante(<?= $variante['id'] ?>, '<?= e($variante['producto_nombre'] ?? 'N/A') ?>')" 
                                            title="Eliminar">
                                        Eliminar
                                    </button>
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

<!-- Modal para editar variante -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarModal()">&times;</span>
        <h2>Editar Variante</h2>
        <form id="formEditarVariante" method="POST">
            <input type="hidden" id="edit_variante_id" name="id">
            
            <div class="form-group">
                <label for="edit_talla">Talla:</label>
                <input type="text" id="edit_talla" name="talla" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="edit_color">Detalle:</label>
                <input type="text" id="edit_color" name="color" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="edit_stock">Stock:</label>
                <input type="number" id="edit_stock" name="stock" class="form-control" min="0" required>
            </div>
            
            <div class="modal-buttons">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                <button type="button" class="btn btn-secondary" onclick="cerrarModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function editarVariante(id, talla, color, stock) {
    document.getElementById('edit_variante_id').value = id;
    document.getElementById('edit_talla').value = talla;
    document.getElementById('edit_color').value = color;
    document.getElementById('edit_stock').value = stock;
    
    document.getElementById('editModal').style.display = 'block';
}

function cerrarModal() {
    document.getElementById('editModal').style.display = 'none';
}

function eliminarVariante(id, nombreProducto) {
    if (confirm('¿Estás seguro de eliminar esta variante del producto "' + nombreProducto + '"?')) {
        // Crear formulario para eliminar
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= url('/admin/variantes/delete') ?>';
        
        const inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.name = 'id';
        inputId.value = id;
        
        form.appendChild(inputId);
        document.body.appendChild(form);
        form.submit();
    }
}

// Manejar el submit del formulario de edición
document.getElementById('formEditarVariante').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('<?= url('/admin/variantes/update') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        // Verificar si la respuesta es JSON
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            // Si no es JSON, leer como texto para ver qué respondió el servidor
            return response.text().then(text => {
                console.error('Respuesta no JSON del servidor:', text);
                throw new Error('El servidor no respondió con JSON. Verifica la consola.');
            });
        }
    })
    .then(data => {
        if (data.success) {
            alert('Variante actualizada correctamente');
            location.reload();
        } else {
            alert('Error al actualizar: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        alert('Error al actualizar la variante: ' + error.message);
        console.error('Error completo:', error);
    });
});

// Cerrar modal al hacer clic fuera de él
window.onclick = function(event) {
    const modal = document.getElementById('editModal');
    if (event.target == modal) {
        cerrarModal();
    }
}
</script>

<style>
.card { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-top: 20px; }
.card-body { padding: 20px; }
.table { width: 100%; border-collapse: collapse; }
.table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
.table th { background: #f9fafb; font-weight: 600; }
.badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; }
.badge-info { background: #06b6d4; color: white; }
.empty-state { text-align: center; padding: 40px; color: #6b7280; }
.alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; }
.alert-success { background: #d1fae5; border: 1px solid #10b981; color: #065f46; }

/* Estilos para botones */
.btn-sm { padding: 6px 12px; font-size: 14px; margin-right: 5px; }
.btn-danger { background: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer; }
.btn-danger:hover { background: #dc2626; }

/* Estilos para el modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 30px;
    border: 1px solid #888;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    line-height: 20px;
}

.close:hover,
.close:focus {
    color: #000;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #374151;
}

.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.modal-buttons {
    display: flex;
    gap: 10px;
    margin-top: 25px;
}

.modal-buttons .btn {
    flex: 1;
    padding: 10px 20px;
}
</style>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
