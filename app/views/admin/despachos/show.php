<?php require_once __DIR__ . '/../../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../../layouts/navbar.php'; ?>

<div class="container" style="margin-top: 80px; padding: 20px;">
    <h1>Detalle del Despacho #<?= $despacho['id'] ?></h1>
    
    <?php if (hasFlash('success')): ?>
        <div class="alert alert-success"><?= getFlash('success') ?></div>
    <?php endif; ?>
    
    <?php if (hasFlash('error')): ?>
        <div class="alert alert-danger"><?= getFlash('error') ?></div>
    <?php endif; ?>
    
    <div class="row">
        <!-- Información del Pedido -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Información del Pedido</h3>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="label">Número de Pedido:</span>
                        <span class="value">#<?= $despacho['pedido_id'] ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Cliente:</span>
                        <span class="value"><?= e($despacho['nombre'] . ' ' . $despacho['apellido']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Email:</span>
                        <span class="value"><?= e($despacho['email']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Nombre Recibe:</span>
                        <span class="value"><?= e($despacho['nombre_recibe'] ?? 'No especificado') ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Teléfono:</span>
                        <span class="value"><?= e($despacho['telefono'] ?? 'No especificado') ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Ciudad:</span>
                        <span class="value"><?= e($despacho['ciudad'] ?? 'No especificada') ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Total:</span>
                        <span class="value">$<?= number_format($despacho['total'], 2) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Método de Pago:</span>
                        <span class="value"><?= e($despacho['metodo_pago']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Fecha del Pedido:</span>
                        <span class="value">
                            <?php 
                            if (!empty($despacho['fecha_pedido'])) {
                                echo date('d/m/Y H:i', strtotime($despacho['fecha_pedido']));
                            } else {
                                echo '<span class="text-muted">Sin fecha</span>';
                            }
                            ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Información del Despacho -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Información del Despacho</h3>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="label">Estado del Envío:</span>
                        <span class="value">
                            <?php
                            $badge_class = '';
                            switch($despacho['estado_envio']) {
                                case 'PENDIENTE':
                                    $badge_class = 'badge-warning';
                                    break;
                                case 'EN_PREPARACION':
                                    $badge_class = 'badge-info';
                                    break;
                                case 'ENVIADO':
                                    $badge_class = 'badge-primary';
                                    break;
                                case 'ENTREGADO':
                                    $badge_class = 'badge-success';
                                    break;
                                default:
                                    $badge_class = 'badge-secondary';
                            }
                            ?>
                            <span class="badge <?= $badge_class ?>">
                                <?= e($despacho['estado_envio']) ?>
                            </span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="label">Dirección de Envío:</span>
                        <span class="value"><?= e($despacho['direccion'] ?? 'No especificada') ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Referencia:</span>
                        <span class="value"><?= e($despacho['referencia'] ?? 'Sin referencia') ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Fecha de Despacho:</span>
                        <span class="value">
                            <?php 
                            if (!empty($despacho['fecha_despacho'])) {
                                echo date('d/m/Y H:i', strtotime($despacho['fecha_despacho']));
                            } else {
                                echo '<span class="text-muted">Pendiente</span>';
                            }
                            ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="label">Fecha de Entrega:</span>
                        <span class="value">
                            <?php 
                            if (!empty($despacho['fecha_entrega'])) {
                                echo date('d/m/Y H:i', strtotime($despacho['fecha_entrega']));
                            } else {
                                echo '<span class="text-muted">Pendiente</span>';
                            }
                            ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="label">Notas:</span>
                        <span class="value"><?= e($despacho['notas'] ?? 'Sin notas') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Productos del Pedido -->
    <div class="card" style="margin-top: 20px;">
        <div class="card-header">
            <h3>Productos del Pedido</h3>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Variante</th>
                        <th>Cantidad</th>
                        <th>Precio Unit.</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalles as $detalle): ?>
                        <tr>
                            <td><?= e($detalle['producto_nombre']) ?></td>
                            <td>
                                <?php
                                $variante_info = [];
                                if (!empty($detalle['talla'])) $variante_info[] = $detalle['talla'];
                                if (!empty($detalle['color'])) $variante_info[] = $detalle['color'];
                                echo !empty($variante_info) ? e(implode(' - ', $variante_info)) : '-';
                                ?>
                            </td>
                            <td><?= $detalle['cantidad'] ?></td>
                            <td>$<?= number_format($detalle['precio_unitario'], 2) ?></td>
                            <td>$<?= number_format($detalle['subtotal'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align: right; font-weight: bold;">TOTAL:</td>
                        <td style="font-weight: bold;">$<?= number_format($despacho['total'], 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
    <!-- Actualizar Estado -->
    <div class="card" style="margin-top: 20px;">
        <div class="card-header">
            <h3>Actualizar Estado del Despacho</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= url('/admin/despachos/' . $despacho['id'] . '/actualizar') ?>">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="estado">Estado del Envío:</label>
                    <select name="estado" id="estado" class="form-control" required>
                        <option value="PENDIENTE" <?= $despacho['estado_envio'] === 'PENDIENTE' ? 'selected' : '' ?>>
                            Pendiente
                        </option>
                        <option value="EN_PREPARACION" <?= $despacho['estado_envio'] === 'EN_PREPARACION' ? 'selected' : '' ?>>
                            En Preparación
                        </option>
                        <option value="ENVIADO" <?= $despacho['estado_envio'] === 'ENVIADO' ? 'selected' : '' ?>>
                            Enviado
                        </option>
                        <option value="ENTREGADO" <?= $despacho['estado_envio'] === 'ENTREGADO' ? 'selected' : '' ?>>
                            Entregado
                        </option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="notas">Notas:</label>
                    <textarea name="notas" id="notas" class="form-control" rows="3"><?= e($despacho['notas'] ?? '') ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Actualizar Estado</button>
            </form>
        </div>
    </div>
    
    <div style="margin-top: 20px;">
        <a href="<?= url('/admin/despachos') ?>" class="btn btn-secondary">← Volver a Despachos</a>
    </div>
</div>

<style>
.row {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.col-md-6 {
    flex: 1;
    min-width: 300px;
}

.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.card-header {
    padding: 15px 20px;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
}

.card-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.card-body {
    padding: 20px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #f3f4f6;
}

.info-row:last-child {
    border-bottom: none;
}

.info-row .label {
    font-weight: 600;
    color: #374151;
}

.info-row .value {
    color: #6b7280;
    text-align: right;
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
}

.table tfoot td {
    border-top: 2px solid #374151;
}

.badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.badge-warning {
    background: #fbbf24;
    color: #78350f;
}

.badge-info {
    background: #06b6d4;
    color: white;
}

.badge-primary {
    background: #3b82f6;
    color: white;
}

.badge-success {
    background: #10b981;
    color: white;
}

.badge-secondary {
    background: #6b7280;
    color: white;
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
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
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

.text-muted {
    color: #9ca3af;
    font-style: italic;
}
</style>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>