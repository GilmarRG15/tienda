<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<div class="producto-detalle">
    <div class="producto-imagenes">
        <?php if (!empty($imagenes)): ?>
            <img src="<?= url($imagenes[0]['url']) ?>" alt="<?= e($producto['nombre']) ?>" class="imagen-principal">
            <?php if (count($imagenes) > 1): ?>
                <div class="imagenes-thumbnails">
                    <?php foreach ($imagenes as $img): ?>
                        <img src="<?= url($img['url']) ?>" alt="" class="thumbnail">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="no-image-large">Sin imagen</div>
        <?php endif; ?>
    </div>
    
    <div class="producto-info">
        <h1><?= e($producto['nombre']) ?></h1>
        <p class="marca"><strong>Marca:</strong> <?= e($producto['marca']) ?></p>
        <p class="categoria"><strong>Categoría:</strong> <?= e($producto['categoria_nombre']) ?></p>
        
        <?php if ($producto['descripcion']): ?>
            <p class="descripcion"><?= nl2br(e($producto['descripcion'])) ?></p>
        <?php endif; ?>
        
        <?php if (!empty($variantes)): ?>
            <form method="POST" action="<?= url('/carrito/add') ?>" class="variantes-form">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                
                <div class="form-group">
                    <label>Selecciona una variante:</label>
                    <select name="variante_id" required>
                        <option value="">-- Seleccione --</option>
                        <?php foreach ($variantes as $var): ?>
                            <option value="<?= $var['id'] ?>">
                                <?= e($var['talla']) ?> - <?= e($var['color']) ?> 
                                - $<?= number_format($var['precio'], 2) ?>
                                (Stock: <?= $var['stock'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Cantidad:</label>
                    <input type="number" name="cantidad" value="1" min="1" required>
                </div>
                
                <?php if (Auth::check()): ?>
                    <button type="submit" class="btn btn-primary btn-block">Agregar al Carrito</button>
                <?php else: ?>
                    <a href="<?= url('/login') ?>" class="btn btn-primary btn-block">Inicia sesión para comprar</a>
                <?php endif; ?>
            </form>
        <?php else: ?>
            <p class="text-warning">No hay variantes disponibles para este producto</p>
        <?php endif; ?>
    </div>
</div>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
