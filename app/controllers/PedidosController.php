<?php
/**
 * PedidosController - Gestión de pedidos del usuario
 * Refactorizado con mejores validaciones
 */

class PedidosController extends Controller {
    
    /**
     * Lista todos los pedidos del usuario
     */
    public function index() {
        Middleware::requireLogin();
        
        $pedidoModel = new Pedido();
        $pedidos = $pedidoModel->getByUsuario(Auth::id());
        
        $this->render('pedidos/index', [
            'pedidos' => $pedidos,
            'titulo' => 'Mis Pedidos'
        ]);
    }
    
    /**
     * Muestra el detalle de un pedido
     */
    public function show($id) {
        Middleware::requireLogin();
        
        $pedidoModel = new Pedido();
        $detallePedidoModel = new DetallePedido();
        $despachoModel = new DespachoPedido();
        
        $pedidoId = (int)$id;
        $pedido = $pedidoModel->find($pedidoId);
        
        // Verificar que el pedido existe y pertenece al usuario
        if (!$pedido) {
            flash('error', MSG_ERROR_NO_ENCONTRADO);
            redirect('/pedidos');
        }
        
        if ($pedido['usuario_id'] != Auth::id()) {
            flash('error', MSG_ERROR_PERMISOS);
            redirect('/pedidos');
        }
        
        $detalles = $detallePedidoModel->getByPedido($pedidoId);
        $despacho = $despachoModel->getByPedido($pedidoId);
        
        $this->render('pedidos/show', [
            'pedido' => $pedido,
            'detalles' => $detalles,
            'despacho' => $despacho,
            'titulo' => 'Pedido #' . $pedidoId
        ]);
    }
}
