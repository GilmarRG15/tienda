<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<h1>Pedido #<?= $pedido['id'] ?></h1>

<div class="pedido-info">
    <div class="info-section">
        <h3>Información del Pedido</h3>
        <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($pedido['fecha_creacion'])) ?></p>
        <p><strong>Estado:</strong> <?= $pedido['estado'] ?></p>
        <p><strong>Estado Pago:</strong> <?= $pedido['estado_pago'] ?></p>
        <p><strong>Método Pago:</strong> <?= $pedido['metodo_pago'] ?></p>
        <p><strong>Total:</strong> $<?= number_format($pedido['total'], 2) ?></p>
    </div>
    
    <?php if ($despacho): ?>
        <div class="info-section">
            <h3>Información de Envío</h3>
            <p><strong>Recibe:</strong> <?= e($despacho['nombre_recibe']) ?></p>
            <p><strong>Teléfono:</strong> <?= e($despacho['telefono']) ?></p>
            <p><strong>Ciudad:</strong> <?= e($despacho['ciudad']) ?></p>
            <p><strong>Dirección:</strong> <?= e($despacho['direccion']) ?></p>
            <p><strong>Estado Envío:</strong> <?= $despacho['estado_envio'] ?></p>
        </div>
    <?php endif; ?>
</div>

<h3>Productos</h3>
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Talla</th>
                <th>Color</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detalles as $det): ?>
                <tr>
                    <td><?= e($det['nombre_snapshot']) ?></td>
                    <td><?= e($det['talla_snapshot']) ?></td>
                    <td><?= e($det['color_snapshot']) ?></td>
                    <td>$<?= number_format($det['precio_snapshot'], 2) ?></td>
                    <td><?= $det['cantidad'] ?></td>
                    <td>$<?= number_format($det['total_linea'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
