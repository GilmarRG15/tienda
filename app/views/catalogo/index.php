<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<div class="catalogo-header">
    <h1>Catálogo de Productos</h1>
    
    <form method="GET" action="<?= url('/catalogo') ?>" class="search-form">
        <input type="text" name="search" placeholder="Buscar productos..." value="<?= e($search) ?>">
        <button type="submit" class="btn">Buscar</button>
    </form>
    
    <div class="filtros">
        <a href="<?= url('/catalogo') ?>" class="filtro <?= !$categoriaId ? 'active' : '' ?>">Todas</a>
        <?php foreach ($categorias as $cat): ?>
            <a href="<?= url('/catalogo?categoria=' . $cat['id']) ?>" 
               class="filtro <?= $categoriaId == $cat['id'] ? 'active' : '' ?>">
                <?= e($cat['nombre']) ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<div class="productos-grid mt-3">
    <?php if (empty($productos)): ?>
        <p>No se encontraron productos</p>
    <?php else: ?>
        <?php foreach ($productos as $prod): ?>
            <div class="producto-card">
                <?php if (isset($prod['imagen_url']) && $prod['imagen_url']): ?>
                    <img src="<?= url($prod['imagen_url']) ?>" alt="<?= e($prod['nombre']) ?>">
                <?php else: ?>
                    <div class="no-image">Sin imagen</div>
                <?php endif; ?>
                <h3><?= e($prod['nombre']) ?></h3>
                <p class="marca"><?= e($prod['marca']) ?></p>
                <p class="categoria"><?= e($prod['categoria_nombre']) ?></p>
                <a href="<?= url('/producto/' . $prod['id']) ?>" class="btn">Ver Detalles</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
