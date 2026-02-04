<?php
/**
 * CarritoController - Gestión del carrito de compras
 * Refactorizado con constantes y validaciones mejoradas
 */

class CarritoController extends Controller {
    
    /**
     * Muestra el carrito de compras
     */
    public function index() {
        Middleware::requireLogin();
        
        $carrito = $_SESSION['carrito'] ?? [];
        $total = 0;
        $items = [];
        
        $varianteModel = new VarianteProducto();
        
        // Cargar información de cada producto en el carrito
        foreach ($carrito as $key => $item) {
            $variante = $varianteModel->findWithProducto($item['variante_id']);
            if ($variante) {
                $subtotal = $variante['precio'] * $item['cantidad'];
                $items[] = [
                    'key' => $key,
                    'variante' => $variante,
                    'cantidad' => $item['cantidad'],
                    'subtotal' => $subtotal
                ];
                $total += $subtotal;
            }
        }
        
        $this->render('carrito/index', [
            'items' => $items,
            'total' => $total,
            'titulo' => 'Mi Carrito'
        ]);
    }
    
    /**
     * Agrega un producto al carrito
     */
    public function add() {
        Middleware::requireLogin();
        
        if (!csrf_verify()) {
            $this->json(['success' => false, 'message' => MSG_ERROR_CSRF], 400);
        }
        
        $varianteId = (int)$this->getPostData('variante_id', 0);
        $cantidad = (int)$this->getPostData('cantidad', 1);
        
        // Validar cantidad positiva
        if ($cantidad < 1) {
            flash('error', 'La cantidad debe ser mayor a 0');
            redirect($_SERVER['HTTP_REFERER'] ?? '/catalogo');
        }
        
        $varianteModel = new VarianteProducto();
        
        // Verificar stock disponible
        if (!$varianteModel->verificarStock($varianteId, $cantidad)) {
            flash('error', 'Stock insuficiente');
            redirect($_SERVER['HTTP_REFERER'] ?? '/catalogo');
        }
        
        // Inicializar carrito si no existe
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
        
        $key = 'v_' . $varianteId;
        
        // Si ya existe en el carrito, sumar cantidad
        if (isset($_SESSION['carrito'][$key])) {
            $nuevaCantidad = $_SESSION['carrito'][$key]['cantidad'] + $cantidad;
            
            // Verificar stock para la nueva cantidad
            if (!$varianteModel->verificarStock($varianteId, $nuevaCantidad)) {
                flash('error', 'No hay suficiente stock para agregar más unidades');
                redirect($_SERVER['HTTP_REFERER'] ?? '/catalogo');
            }
            
            $_SESSION['carrito'][$key]['cantidad'] = $nuevaCantidad;
        } else {
            $_SESSION['carrito'][$key] = [
                'variante_id' => $varianteId,
                'cantidad' => $cantidad,
                'precio' => $varianteModel->getPrecio($varianteId)
            ];
        }
        
        flash('success', 'Producto agregado al carrito');
        redirect('/carrito');
    }
    
    /**
     * Actualiza la cantidad de un producto en el carrito
     */
    public function update() {
        Middleware::requireLogin();
        
        $key = $this->getPostData('key', '');
        $cantidad = (int)$this->getPostData('cantidad', 1);
        
        if (!isset($_SESSION['carrito'][$key])) {
            flash('error', 'Producto no encontrado en el carrito');
            redirect('/carrito');
        }
        
        if ($cantidad > 0) {
            // Verificar stock antes de actualizar
            $varianteId = $_SESSION['carrito'][$key]['variante_id'];
            $varianteModel = new VarianteProducto();
            
            if (!$varianteModel->verificarStock($varianteId, $cantidad)) {
                flash('error', 'Stock insuficiente para la cantidad solicitada');
                redirect('/carrito');
            }
            
            $_SESSION['carrito'][$key]['cantidad'] = $cantidad;
            flash('success', 'Carrito actualizado');
        } else {
            unset($_SESSION['carrito'][$key]);
            flash('success', 'Producto eliminado del carrito');
        }
        
        redirect('/carrito');
    }
    
    /**
     * Elimina un producto del carrito
     */
    public function remove() {
        Middleware::requireLogin();
        
        $key = $this->getPostData('key', '');
        
        if (isset($_SESSION['carrito'][$key])) {
            unset($_SESSION['carrito'][$key]);
            flash('success', 'Producto eliminado del carrito');
        } else {
            flash('error', 'Producto no encontrado');
        }
        
        redirect('/carrito');
    }
    
    /**
     * Muestra la página de checkout
     */
    public function checkout() {
        Middleware::requireLogin();
        
        $carrito = $_SESSION['carrito'] ?? [];
        
        if (empty($carrito)) {
            flash('error', 'Tu carrito está vacío');
            redirect('/carrito');
        }
        
        $this->render('carrito/checkout', ['titulo' => 'Finalizar Compra']);
    }
    
    /**
     * Procesa la compra del carrito
     */
    public function processCheckout() {
        Middleware::requireLogin();
        
        if (!csrf_verify()) {
            $this->redirectWith('/carrito/checkout', 'error', MSG_ERROR_CSRF);
        }
        
        $carrito = $_SESSION['carrito'] ?? [];
        
        if (empty($carrito)) {
            flash('error', 'Tu carrito está vacío');
            redirect('/carrito');
        }
        
        // Validar datos de envío
        $validation = $this->validate($_POST, [
            'nombre_recibe' => 'required|min:3|max:100',
            'telefono' => 'required|min:7|max:20',
            'ciudad' => 'required|min:3|max:100',
            'direccion' => 'required|min:10|max:255'
        ]);
        
        if ($validation !== true) {
            saveOldInput();
            flash('errors', $validation);
            redirect('/carrito/checkout');
        }
        
        // Datos de envío del formulario
        $datosEnvio = [
            'nombre_recibe' => $this->getPostData('nombre_recibe'),
            'telefono' => $this->getPostData('telefono'),
            'ciudad' => $this->getPostData('ciudad'),
            'direccion' => $this->getPostData('direccion'),
            'referencia' => $this->getPostData('referencia', '')
        ];
        
        $metodoPago = $this->getPostData('metodo_pago', 'Transferencia');
        
        try {
            // Crear el pedido completo
            $pedidoModel = new Pedido();
            $pedidoId = $pedidoModel->createPedidoCompleto(
                Auth::id(),
                $carrito,
                $datosEnvio,
                $metodoPago
            );
            
            // Limpiar el carrito
            unset($_SESSION['carrito']);
            clearOldInput();
            
            flash('success', '¡Pedido realizado exitosamente! Número de pedido: ' . $pedidoId);
            redirect('/pedidos/' . $pedidoId);
            
        } catch (Exception $e) {
            flash('error', 'Error al procesar el pedido: ' . e($e->getMessage()));
            redirect('/carrito/checkout');
        }
    }
}
