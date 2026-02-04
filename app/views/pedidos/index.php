<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<h1>Mis Pedidos</h1>

<?php if (empty($pedidos)): ?>
    <p>No tienes pedidos aún</p>
<?php else: ?>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $ped): ?>
                    <tr>
                        <td>#<?= $ped['id'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($ped['fecha_creacion'])) ?></td>
                        <td>$<?= number_format($ped['total'], 2) ?></td>
                        <td><span class="badge-envio badge-<?= strtolower($ped['estado_envio'] ?? 'pendiente') ?>"><?= $ped['estado_envio'] ?? 'PENDIENTE' ?></span></td>
                        <td>
                            <a href="<?= url('/pedidos/' . $ped['id']) ?>" class="btn btn-small">Ver Detalle</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<style>
/* Estilos para badges de estado de envío */
.badge-envio {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-pendiente {
    background-color: #fef3c7;
    color: #92400e;
}

.badge-en_preparacion {
    background-color: #dbeafe;
    color: #1e40af;
}

.badge-enviado {
    background-color: #d1fae5;
    color: #065f46;
}

.badge-entregado {
    background-color: #d1fae5;
    color: #065f46;
    border: 2px solid #10b981;
}

/* Estilo para la tabla */
.table-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-top: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

table thead {
    background: #f9fafb;
    border-bottom: 2px solid #e5e7eb;
}

table th {
    padding: 14px;
    text-align: left;
    font-weight: 600;
    color: #374151;
    font-size: 0.9rem;
}

table td {
    padding: 14px;
    border-bottom: 1px solid #e5e7eb;
    color: #1f2937;
}

table tbody tr:hover {
    background: #f9fafb;
}

.btn-small {
    padding: 8px 16px;
    background: #E10600;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-block;
    transition: background 0.2s;
}

.btn-small:hover {
    background: #B40500;
}

h1 {
    color: #1f2937;
    margin-bottom: 10px;
}
</style>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
