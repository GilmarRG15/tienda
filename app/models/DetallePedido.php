<?php
/**
 * Modelo DetallePedido
 */

class DetallePedido extends Model {
    protected $table = 'detalle_pedido';
    
    /**
     * Obtener detalles por pedido
     */
    public function getByPedido($pedidoId) {
        return $this->executeWithRetry(function() use ($pedidoId) {
            $db = $this->getDb();
            $stmt = $db->prepare("
                SELECT dp.*, vp.sku, p.nombre as producto_nombre,
                       i.url as imagen_url
                FROM detalle_pedido dp
                LEFT JOIN variantes_producto vp ON dp.variante_id = vp.id
                LEFT JOIN productos p ON vp.producto_id = p.id
                LEFT JOIN imagenes_producto i ON p.id = i.producto_id AND i.es_principal = 1
                WHERE dp.pedido_id = ?
                ORDER BY dp.id
            ");
            $stmt->execute([$pedidoId]);
            return $stmt->fetchAll();
        });
    }
    
    /**
     * Obtener resumen de productos más vendidos
     */
    public function getProductosMasVendidos($limit = 10) {
        return $this->executeWithRetry(function() use ($limit) {
            $db = $this->getDb();
            $stmt = $db->prepare("
                SELECT nombre_snapshot, SUM(cantidad) as total_vendido,
                       SUM(total_linea) as total_ingresos
                FROM detalle_pedido
                GROUP BY nombre_snapshot
                ORDER BY total_vendido DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        });
    }
}