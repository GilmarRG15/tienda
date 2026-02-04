<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<h1>Checkout</h1>

<form method="POST" action="<?= url('/carrito/checkout') ?>" class="checkout-form">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    
    <div class="form-section">
        <h2>Datos de Envío</h2>
        
        <div class="form-group">
            <label for="nombre_recibe">Nombre de quien recibe *</label>
            <input type="text" id="nombre_recibe" name="nombre_recibe" required>
        </div>
        
        <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input type="tel" id="telefono" name="telefono">
        </div>
        
        <div class="form-group">
            <label for="ciudad">Ciudad *</label>
            <input type="text" id="ciudad" name="ciudad" required>
        </div>
        
        <div class="form-group">
            <label for="direccion">Dirección *</label>
            <textarea id="direccion" name="direccion" rows="3" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="referencia">Referencia</label>
            <textarea id="referencia" name="referencia" rows="2"></textarea>
        </div>
    </div>
    
    <div class="form-section">
        <h2>Método de Pago</h2>
        
        <div class="form-group">
            <select name="metodo_pago" required>
                <option value="Transferencia">Transferencia Bancaria</option>
                <option value="Efectivo">Efectivo contra entrega</option>
                <option value="Tarjeta">Tarjeta de Crédito</option>
            </select>
        </div>
    </div>
    
    <button type="submit" class="btn btn-primary btn-large btn-block">Confirmar Pedido</button>
</form>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
