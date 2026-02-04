<?php require_once __DIR__ . '/../../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../../layouts/navbar.php'; ?>

<div class="container" style="margin-top: 80px; padding: 20px;">
    <h1>Gestión de Imágenes</h1>
    
    <div class="card">
        <div class="card-body">
            <div class="empty-state">
                <p>Sección de gestión de imágenes de productos</p>
                <p>Aquí podrás subir y administrar las imágenes de tus productos.</p>
                <p style="color: #f59e0b; margin-top: 20px;">NOTA: Esta funcionalidad requiere configuración adicional de upload de archivos.</p>
            </div>
        </div>
    </div>
    
    <div style="margin-top: 20px;">
        <a href="<?= url('/admin') ?>" class="btn btn-secondary">← Volver al Panel</a>
    </div>
</div>

<style>
.card { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-top: 20px; }
.card-body { padding: 20px; }
.empty-state { text-align: center; padding: 40px; color: #6b7280; }
</style>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
