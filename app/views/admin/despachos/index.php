<?php require_once __DIR__ . '/../../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../../layouts/navbar.php'; ?>

<div class="container" style="margin-top: 80px; padding: 20px;">
    <h1>Gestión de Despachos</h1>
    
    <?php if (hasFlash('success')): ?>
        <div class="alert alert-success"><?= getFlash('success') ?></div>
    <?php endif; ?>
    
    <?php if (hasFlash('error')): ?>
        <div class="alert alert-danger"><?= getFlash('error') ?></div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-body">
            <?php if (empty($despachos)): ?>
                <div class="empty-state">
                    <p>No hay despachos pendientes</p>
                </div>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pedido</th>
                            <th>Cliente</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($despachos as $despacho): ?>
                            <tr>
                                <td>#<?= $despacho['id'] ?></td>
                                <td>
                                    <a href="<?= url('/admin/despachos/' . $despacho['id']) ?>">
                                        #<?= $despacho['pedido_id'] ?>
                                    </a>
                                </td>
                                <td><?= e($despacho['nombre'] . ' ' . $despacho['apellido']) ?></td>
                                <td>
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
                                </td>
                                <td>
                                    <?php 
                                    // Manejo seguro de fechas que pueden ser null
                                    if (!empty($despacho['fecha_despacho'])) {
                                        echo date('d/m/Y', strtotime($despacho['fecha_despacho']));
                                    } else {
                                        echo '<span class="text-muted">Sin fecha</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="<?= url('/admin/despachos/' . $despacho['id']) ?>" 
                                       class="btn btn-sm btn-primary">
                                        Ver
                                    </a>
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
.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-top: 20px;
}

.card-body {
    padding: 20px;
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

.table tbody tr:hover {
    background: #f9fafb;
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

.empty-state {
    text-align: center;
    padding: 40px;
    color: #6b7280;
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