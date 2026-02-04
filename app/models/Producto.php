<?php
// Modelo para gestionar productos

class Producto extends Model {
    protected $table = 'productos';
    
    // Obtiene todos los productos con su categoría y datos de variantes
    public function getAllWithCategoria() {
        $db = $this->getDb();
        $stmt = $db->prepare("
            SELECT 
                p.*, 
                c.nombre as categoria_nombre,
                COUNT(DISTINCT v.id) as total_variantes,
                COALESCE(SUM(v.stock), 0) as stock_total,
                MIN(v.precio) as precio
            FROM productos p
            INNER JOIN categorias c ON p.categoria_id = c.id
            LEFT JOIN variantes_producto v ON p.id = v.producto_id AND v.activo = 1
            GROUP BY p.id, c.nombre
            ORDER BY p.fecha_creacion DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Obtiene un producto específico con su categoría
    public function findWithCategoria($id) {
        $db = $this->getDb();
        $stmt = $db->prepare("
            SELECT p.*, c.nombre as categoria_nombre, c.slug as categoria_slug
            FROM productos p
            INNER JOIN categorias c ON p.categoria_id = c.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    // Busca productos por nombre, marca o descripción
    public function search($query, $categoriaId = null) {
        $sql = "
            SELECT p.*, c.nombre as categoria_nombre, c.slug as categoria_slug,
                   i.url as imagen_url
            FROM productos p
            INNER JOIN categorias c ON p.categoria_id = c.id
            LEFT JOIN imagenes_producto i ON p.id = i.producto_id AND i.es_principal = 1
            WHERE p.activo = 1
            AND (p.nombre LIKE ? OR p.marca LIKE ? OR p.descripcion LIKE ?)
        ";
        
        $params = ["%{$query}%", "%{$query}%", "%{$query}%"];
        
        if ($categoriaId) {
            $sql .= " AND p.categoria_id = ?";
            $params[] = $categoriaId;
        }
        
        $sql .= " ORDER BY p.nombre";
        
        $db = $this->getDb();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    // Obtiene productos de una categoría específica
    public function getByCategoria($categoriaId) {
        $db = $this->getDb();
        $stmt = $db->prepare("
            SELECT p.*, c.nombre as categoria_nombre, c.slug as categoria_slug,
                   i.url as imagen_url
            FROM productos p
            INNER JOIN categorias c ON p.categoria_id = c.id
            LEFT JOIN imagenes_producto i ON p.id = i.producto_id AND i.es_principal = 1
            WHERE p.categoria_id = ? AND p.activo = 1
            ORDER BY p.nombre
        ");
        $stmt->execute([$categoriaId]);
        return $stmt->fetchAll();
    }
    
    // Obtiene productos activos con su imagen principal
    public function getActivosConImagen($limit = null) {
        $sql = "
            SELECT p.*, c.nombre as categoria_nombre, c.slug as categoria_slug,
                   i.url as imagen_url
            FROM productos p
            INNER JOIN categorias c ON p.categoria_id = c.id
            LEFT JOIN imagenes_producto i ON p.id = i.producto_id AND i.es_principal = 1
            WHERE p.activo = 1
            ORDER BY p.fecha_creacion DESC
        ";
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        $db = $this->getDb();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Activa o desactiva un producto
    public function toggleActivo($id) {
        $db = $this->getDb();
        $stmt = $db->prepare("UPDATE productos SET activo = NOT activo WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Eliminar producto con todas sus relaciones
     * Este método elimina en cascada:
     * 1. Detalles de pedidos relacionados con variantes del producto
     * 2. Variantes del producto
     * 3. Imágenes del producto
     * 4. El producto mismo
     */
    public function deleteWithRelations($id) {
        $db = $this->getDb();
        
        try {
            // Iniciar transacción
            $db->beginTransaction();
            
            // 1. Eliminar detalles de pedidos relacionados con las variantes de este producto
            $stmt = $db->prepare("
                DELETE dp FROM detalle_pedido dp
                INNER JOIN variantes_producto v ON dp.variante_id = v.id
                WHERE v.producto_id = ?
            ");
            $stmt->execute([$id]);
            
            // 2. Eliminar variantes del producto
            $stmt = $db->prepare("DELETE FROM variantes_producto WHERE producto_id = ?");
            $stmt->execute([$id]);
            
            // 3. Eliminar imágenes del producto
            $stmt = $db->prepare("DELETE FROM imagenes_producto WHERE producto_id = ?");
            $stmt->execute([$id]);
            
            // 4. Eliminar el producto
            $stmt = $db->prepare("DELETE FROM productos WHERE id = ?");
            $stmt->execute([$id]);
            
            // Confirmar transacción
            $db->commit();
            
            return true;
        } catch (Exception $e) {
            // Revertir cambios si hay error
            $db->rollBack();
            throw $e;
        }
    }
}