<?php
/**
 * Modelo de Despacho de Pedidos
 */

class DespachoPedido extends Model {
    protected $table = 'despacho_pedido';

    public function getAllWithPedido() {
        $db = $this->getDb();
        $query = "
            SELECT 
                d.*,
                p.id as pedido_id,
                p.estado as pedido_estado,
                p.fecha_creacion as fecha_pedido,
                p.total,
                u.nombre,
                u.apellido
            FROM {$this->table} d
            INNER JOIN pedidos p ON d.pedido_id = p.id
            INNER JOIN usuarios u ON p.usuario_id = u.id
            ORDER BY d.fecha_despacho DESC
        ";
        
        return $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function findWithDetails($id) {
        $db = $this->getDb();
        $query = "
            SELECT 
                d.*,
                p.id as pedido_id,
                p.estado as pedido_estado,
                p.total,
                p.metodo_pago,
                p.fecha_creacion as fecha_pedido,
                u.nombre,
                u.apellido,
                u.email
            FROM {$this->table} d
            INNER JOIN pedidos p ON d.pedido_id = p.id
            INNER JOIN usuarios u ON p.usuario_id = u.id
            WHERE d.id = :id
        ";
        
        $stmt = $db->prepare($query);
        $stmt->execute(['id' => $id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateEstadoEnvio($id, $estado) {
        $db = $this->getDb();
        $estados_validos = ['PENDIENTE', 'EN_PREPARACION', 'ENVIADO', 'ENTREGADO'];
        
        if (!in_array($estado, $estados_validos)) {
            throw new Exception('Estado de envío inválido');
        }
        
        // Actualizar fecha de entrega si el estado es ENTREGADO
        if ($estado === 'ENTREGADO') {
            $query = "UPDATE {$this->table} SET estado_envio = :estado, fecha_entrega = NOW() WHERE id = :id";
        } else {
            $query = "UPDATE {$this->table} SET estado_envio = :estado WHERE id = :id";
        }
        
        $stmt = $db->prepare($query);
        return $stmt->execute([
            'id' => $id,
            'estado' => $estado
        ]);
    }
    
    public function createForPedido($pedidoId, $direccion) {
        $db = $this->getDb();
        $query = "
            INSERT INTO {$this->table} (pedido_id, direccion_destino, estado_envio, fecha_despacho)
            VALUES (:pedido_id, :direccion, 'PENDIENTE', NOW())
        ";
        
        $stmt = $db->prepare($query);
        $stmt->execute([
            'pedido_id' => $pedidoId,
            'direccion' => $direccion
        ]);
        
        return $db->lastInsertId();
    }
    
    public function getByPedido($pedidoId) {
        $db = $this->getDb();
        $query = "
            SELECT *
            FROM {$this->table}
            WHERE pedido_id = :pedido_id
        ";
        
        $stmt = $db->prepare($query);
        $stmt->execute(['pedido_id' => $pedidoId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}