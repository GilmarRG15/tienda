<?php
/**
 * Modelo Pedido
 */

class Pedido extends Model {
    protected $table = 'pedidos';
    
    /**
     * Crear pedido completo con detalles
     */
    public function createPedidoCompleto($usuarioId, $carrito, $datosEnvio, $metodoPago) {
        $db = $this->getDb();
        try {
            $db->beginTransaction();
            
            // Calcular total
            $total = 0;
            foreach ($carrito as $item) {
                $total += $item['precio'] * $item['cantidad'];
            }
            
            // Crear pedido
            $pedidoId = $this->create([
                'usuario_id' => $usuarioId,
                'estado' => 'PENDIENTE',
                'metodo_pago' => $metodoPago,
                'estado_pago' => 'PENDIENTE',
                'total' => $total
            ]);
            
            // Crear detalles del pedido
            $detallePedidoModel = new DetallePedido();
            $varianteModel = new VarianteProducto();
            
            foreach ($carrito as $item) {
                // Obtener información completa de la variante
                $variante = $varianteModel->findWithProducto($item['variante_id']);
                
                if (!$variante) {
                    throw new Exception("Variante no encontrada");
                }
                
                // Verificar stock
                if (!$varianteModel->verificarStock($item['variante_id'], $item['cantidad'])) {
                    throw new Exception("Stock insuficiente para: {$variante['producto_nombre']}");
                }
                
                // Crear detalle
                $detallePedidoModel->create([
                    'pedido_id' => $pedidoId,
                    'variante_id' => $item['variante_id'],
                    'nombre_snapshot' => $variante['producto_nombre'] . ($variante['producto_marca'] ? ' - ' . $variante['producto_marca'] : ''),
                    'talla_snapshot' => $variante['talla'],
                    'color_snapshot' => $variante['color'],
                    'precio_snapshot' => $variante['precio'],
                    'cantidad' => $item['cantidad'],
                    'total_linea' => $variante['precio'] * $item['cantidad']
                ]);
                
                // Decrementar stock
                $varianteModel->decrementarStock($item['variante_id'], $item['cantidad']);
            }
            
            // Crear registro de despacho
            $despachoModel = new DespachoPedido();
            $despachoModel->create([
                'pedido_id' => $pedidoId,
                'nombre_recibe' => $datosEnvio['nombre_recibe'],
                'telefono' => $datosEnvio['telefono'] ?? null,
                'ciudad' => $datosEnvio['ciudad'],
                'direccion' => $datosEnvio['direccion'],
                'referencia' => $datosEnvio['referencia'] ?? null,
                'estado_envio' => 'PENDIENTE'
            ]);
            
            $db->commit();
            return $pedidoId;
            
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Obtener pedidos por usuario
     */
    public function getByUsuario($usuarioId) {
        $db = $this->getDb();
        $stmt = $db->prepare("
            SELECT p.*, d.estado_envio
            FROM pedidos p
            LEFT JOIN despacho_pedido d ON p.id = d.pedido_id
            WHERE p.usuario_id = ?
            ORDER BY p.fecha_creacion DESC
        ");
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener pedido con detalles
     */
    public function getPedidoCompleto($id) {
        $db = $this->getDb();
        $stmt = $db->prepare("
            SELECT p.*, u.nombre as usuario_nombre, u.apellido as usuario_apellido, u.email as usuario_email
            FROM pedidos p
            INNER JOIN usuarios u ON p.usuario_id = u.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Actualizar estado del pedido
     */
    public function updateEstado($id, $estado) {
        $db = $this->getDb();
        $stmt = $db->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
        return $stmt->execute([$estado, $id]);
    }
    
    /**
     * Actualizar estado de pago
     */
    public function updateEstadoPago($id, $estadoPago) {
        $db = $this->getDb();
        $stmt = $db->prepare("UPDATE pedidos SET estado_pago = ? WHERE id = ?");
        return $stmt->execute([$estadoPago, $id]);
    }
    
    /**
     * Obtener todos los pedidos con información de usuario
     */
    public function getAllWithUsuario() {
        $db = $this->getDb();
        $stmt = $db->prepare("
            SELECT p.*, u.nombre as usuario_nombre, u.apellido as usuario_apellido,
                   d.estado_envio
            FROM pedidos p
            INNER JOIN usuarios u ON p.usuario_id = u.id
            LEFT JOIN despacho_pedido d ON p.id = d.pedido_id
            ORDER BY p.fecha_creacion DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener estadísticas de pedidos
     */
    public function getEstadisticas() {
        $db = $this->getDb();
        $stmt = $db->prepare("
            SELECT 
                COUNT(*) as total_pedidos,
                SUM(CASE WHEN estado = 'PENDIENTE' THEN 1 ELSE 0 END) as pendientes,
                SUM(CASE WHEN estado = 'PAGADO' THEN 1 ELSE 0 END) as pagados,
                SUM(CASE WHEN estado = 'ENVIADO' THEN 1 ELSE 0 END) as enviados,
                SUM(CASE WHEN estado = 'ENTREGADO' THEN 1 ELSE 0 END) as entregados,
                SUM(total) as total_ventas
            FROM pedidos
        ");
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Obtener detalles de un pedido
     */
    public function getDetalles($pedidoId) {
        $db = $this->getDb();
        $sql = "SELECT 
                    d.id,
                    d.pedido_id,
                    d.variante_id,
                    d.nombre_snapshot AS producto_nombre,
                    d.talla_snapshot AS talla,
                    d.color_snapshot AS color,
                    d.precio_snapshot AS precio_unitario,
                    d.cantidad,
                    d.total_linea AS subtotal
                FROM detalle_pedido d
                WHERE d.pedido_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$pedidoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}