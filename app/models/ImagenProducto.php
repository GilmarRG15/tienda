<?php
/**
 * Modelo ImagenProducto
 */

class ImagenProducto extends Model {
    protected $table = 'imagenes_producto';
    
    /**
     * Obtener todas las imágenes con información del producto
     */
    public function getAllWithProducto() {
        return $this->executeWithRetry(function() {
            $db = $this->getDb();
            $stmt = $db->prepare("
                SELECT i.*, p.nombre as producto_nombre
                FROM imagenes_producto i
                INNER JOIN productos p ON i.producto_id = p.id
                ORDER BY p.nombre, i.es_principal DESC, i.orden ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        });
    }
    
    /**
     * Obtener imágenes por producto
     */
    public function getByProducto($productoId) {
        return $this->executeWithRetry(function() use ($productoId) {
            $db = $this->getDb();
            $stmt = $db->prepare("
                SELECT * FROM imagenes_producto
                WHERE producto_id = ?
                ORDER BY es_principal DESC, orden ASC
            ");
            $stmt->execute([$productoId]);
            return $stmt->fetchAll();
        });
    }
    
    /**
     * Obtener imagen principal
     */
    public function getPrincipal($productoId) {
        return $this->executeWithRetry(function() use ($productoId) {
            $db = $this->getDb();
            $stmt = $db->prepare("
                SELECT * FROM imagenes_producto
                WHERE producto_id = ? AND es_principal = 1
                LIMIT 1
            ");
            $stmt->execute([$productoId]);
            return $stmt->fetch();
        });
    }
    
    /**
     * Establecer como principal
     */
    public function setAsPrincipal($id, $productoId) {
        return $this->executeWithRetry(function() use ($id, $productoId) {
            $db = $this->getDb();
            // Primero, remover el flag de principal de todas las imágenes del producto
            $stmt1 = $db->prepare("
                UPDATE imagenes_producto 
                SET es_principal = 0 
                WHERE producto_id = ?
            ");
            $stmt1->execute([$productoId]);
            
            // Luego, establecer la imagen seleccionada como principal
            $stmt2 = $db->prepare("
                UPDATE imagenes_producto 
                SET es_principal = 1 
                WHERE id = ?
            ");
            return $stmt2->execute([$id]);
        });
    }
    
    /**
     * Crear imagen y establecer como principal si es la primera
     */
    public function createImage($data) {
        // Verificar si ya existe una imagen principal
        $existePrincipal = $this->getPrincipal($data['producto_id']);
        
        // Si no existe imagen principal, esta será la principal
        if (!$existePrincipal) {
            $data['es_principal'] = 1;
        }
        
        return $this->create($data);
    }
    
    /**
     * Obtener siguiente orden
     */
    public function getNextOrden($productoId) {
        return $this->executeWithRetry(function() use ($productoId) {
            $db = $this->getDb();
            $stmt = $db->prepare("
                SELECT MAX(orden) as max_orden 
                FROM imagenes_producto 
                WHERE producto_id = ?
            ");
            $stmt->execute([$productoId]);
            $result = $stmt->fetch();
            return ($result['max_orden'] ?? 0) + 1;
        });
    }
    
    /**
     * Eliminar imagen y reorganizar
     */
    public function deleteImage($id) {
        return $this->executeWithRetry(function() use ($id) {
            // Obtener información de la imagen
            $imagen = $this->find($id);
            if (!$imagen) {
                return false;
            }
            
            // Eliminar la imagen
            $this->delete($id);
            
            // Si era la principal, establecer otra como principal
            if ($imagen['es_principal']) {
                $db = $this->getDb();
                $otraImagen = $db->prepare("
                    SELECT id FROM imagenes_producto 
                    WHERE producto_id = ? 
                    ORDER BY orden 
                    LIMIT 1
                ");
                $otraImagen->execute([$imagen['producto_id']]);
                $nueva = $otraImagen->fetch();
                
                if ($nueva) {
                    $this->setAsPrincipal($nueva['id'], $imagen['producto_id']);
                }
            }
            
            return true;
        });
    }
}