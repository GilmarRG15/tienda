<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<div class="admin-container">
    <h1>Panel de Administración</h1>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Pedidos</h3>
            <p class="stat-number"><?= $stats['total_pedidos'] ?? 0 ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Pedidos Pendientes</h3>
            <p class="stat-number"><?= $stats['pendientes'] ?? 0 ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Total Productos</h3>
            <p class="stat-number"><?= $totalProductos ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Total Ventas</h3>
            <p class="stat-number">$<?= number_format($stats['total_ventas'] ?? 0, 2) ?></p>
        </div>
    </div>
    
    <div class="admin-menu">
        <h2>Gestión</h2>
        <div class="admin-links">
            <?php if (Auth::hasRole(['ADMIN', 'GESTOR_PRODUCTOS'])): ?>
                <a href="<?= url('/admin/categorias') ?>" class="admin-link">Categorías</a>
                <a href="<?= url('/admin/productos') ?>" class="admin-link">Productos</a>
            <?php endif; ?>
            
            <?php if (Auth::hasRole(['ADMIN', 'GESTOR_INVENTARIO'])): ?>
                <a href="<?= url('/admin/variantes') ?>" class="admin-link">Variantes</a>
            <?php endif; ?>
            
            <?php if (Auth::hasRole('ADMIN')): ?>
                <a href="<?= url('/admin/usuarios') ?>" class="admin-link">Usuarios</a>
            <?php endif; ?>
            
            <?php if (Auth::hasRole(['ADMIN', 'DESPACHADOR'])): ?>
                <a href="<?= url('/admin/despachos') ?>" class="admin-link">Despachos</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
