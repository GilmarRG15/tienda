<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<h1>Mi Carrito</h1>

<?php if (empty($items)): ?>
    <div class="carrito-vacio">
        <p>Tu carrito está vacío</p>
        <a href="<?= url('/catalogo') ?>" class="btn btn-primary">Ir al Catálogo</a>
    </div>
<?php else: ?>
    <div class="carrito-container">
        <table class="table-container">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Variante</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= e($item['variante']['producto_nombre']) ?></td>
                        <td>
                            <?= e($item['variante']['talla']) ?> - <?= e($item['variante']['color']) ?>
                        </td>
                        <td>$<?= number_format($item['variante']['precio'], 2) ?></td>
                        <td>
                            <form method="POST" action="<?= url('/carrito/update') ?>" style="display:inline">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="key" value="<?= $item['key'] ?>">
                                <input type="number" name="cantidad" value="<?= $item['cantidad'] ?>" 
                                       min="1" max="<?= $item['variante']['stock'] ?>" style="width:60px">
                                <button type="submit" class="btn btn-small">Actualizar</button>
                            </form>
                        </td>
                        <td>$<?= number_format($item['subtotal'], 2) ?></td>
                        <td>
                            <form method="POST" action="<?= url('/carrito/remove') ?>" style="display:inline">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="key" value="<?= $item['key'] ?>">
                                <button type="submit" class="btn btn-danger btn-small">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="carrito-total">
            <h2>Total: $<?= number_format($total, 2) ?></h2>
            <a href="<?= url('/carrito/checkout') ?>" class="btn btn-primary btn-large">Proceder al Checkout</a>
        </div>
    </div>
<?php endif; ?>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
