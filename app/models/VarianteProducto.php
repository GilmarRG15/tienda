<?php
/**
 * Modelo para gestionar variantes de productos
 */

class VarianteProducto extends Model {
    protected $table = 'variantes_producto';
    
    /**
     * Obtiene todas las variantes con información del producto
     */
    public function getAllWithProducto() {
        $db = $this->getDb();
        $stmt = $db->prepare("
            SELECT 
                v.*,
                p.nombre as producto_nombre,
                p.marca as producto_marca,
                c.nombre as categoria_nombre
            FROM variantes_producto v
            INNER JOIN productos p ON v.producto_id = p.id
            INNER JOIN categorias c ON p.categoria_id = c.id
            ORDER BY p.nombre, v.talla, v.color
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obtiene todas las variantes de un producto específico
     */
    public function getByProducto($productoId) {
        $db = $this->getDb();
        $stmt = $db->prepare("
            SELECT * FROM variantes_producto
            WHERE producto_id = ?
            ORDER BY talla, color
        ");
        $stmt->execute([$productoId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtiene variantes activas de un producto
     */
    public function getActivasByProducto($productoId) {
        $db = $this->getDb();
        $stmt = $db->prepare("
            SELECT * FROM variantes_producto
            WHERE producto_id = ? AND activo = 1 AND stock > 0
            ORDER BY precio
        ");
        $stmt->execute([$productoId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Busca una variante específica por sus atributos
     */
    public function findByAttributes($productoId, $talla = null, $color = null) {
        $db = $this->getDb();
        
        $sql = "SELECT * FROM variantes_producto WHERE producto_id = ?";
        $params = [$productoId];
        
        if ($talla !== null) {
            $sql .= " AND talla = ?";
            $params[] = $talla;
        }
        
        if ($color !== null) {
            $sql .= " AND color = ?";
            $params[] = $color;
        }
        
        $sql .= " LIMIT 1";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    /**
     * Actualiza el stock de una variante
     */
    public function updateStock($id, $cantidad) {
        $db = $this->getDb();
        $stmt = $db->prepare("
            UPDATE variantes_producto 
            SET stock = stock + ? 
            WHERE id = ?
        ");
        return $stmt->execute([$cantidad, $id]);
    }
    
    /**
     * Reduce el stock de una variante (para ventas)
     */
    public function reduceStock($id, $cantidad) {
        $db = $this->getDb();
        $stmt = $db->prepare("
            UPDATE variantes_producto 
            SET stock = stock - ? 
            WHERE id = ? AND stock >= ?
        ");
        return $stmt->execute([$cantidad, $id, $cantidad]);
    }
    
    /**
     * Verifica si hay stock disponible
     */
    public function hasStock($id, $cantidad = 1) {
        $db = $this->getDb();
        $stmt = $db->prepare("
            SELECT stock FROM variantes_producto 
            WHERE id = ? AND activo = 1
        ");
        $stmt->execute([$id]);
        $variante = $stmt->fetch();
        
        return $variante && $variante['stock'] >= $cantidad;
    }
    
    /**
     * Obtiene el precio más bajo de las variantes de un producto
     */
    public function getMinPrecioByProducto($productoId) {
        $db = $this->getDb();
        $stmt = $db->prepare("
            SELECT MIN(precio) as precio_minimo
            FROM variantes_producto
            WHERE producto_id = ? AND activo = 1
        ");
        $stmt->execute([$productoId]);
        $result = $stmt->fetch();
        return $result['precio_minimo'] ?? 0;
    }
    
    /**
     * Obtiene el rango de precios de las variantes de un producto
     */
    public function getPrecioRangeByProducto($productoId) {
        $db = $this->getDb();
        $stmt = $db->prepare("
            SELECT 
                MIN(precio) as precio_minimo,
                MAX(precio) as precio_maximo
            FROM variantes_producto
            WHERE producto_id = ? AND activo = 1
        ");
        $stmt->execute([$productoId]);
        return $stmt->fetch();
    }
    
    /**
     * Cuenta las variantes de un producto
     */
    public function countByProducto($productoId) {
        $db = $this->getDb();
        $stmt = $db->prepare("
            SELECT COUNT(*) as total
            FROM variantes_producto
            WHERE producto_id = ?
        ");
        $stmt->execute([$productoId]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
    
    /**
     * Genera un SKU único automáticamente
     */
    public function generateSKU($productoId, $talla = '', $color = '') {
        // Formato: PROD-{ID}-{TALLA}-{COLOR}-{TIMESTAMP}
        $base = 'PROD-' . $productoId;
        
        if ($talla) {
            $base .= '-' . strtoupper(str_replace(' ', '', $talla));
        }
        
        if ($color) {
            $base .= '-' . strtoupper(substr(str_replace(' ', '', $color), 0, 2));
        }
        
        $base .= '-' . substr(uniqid(), -4);
        
        return $base;
    }
    
    /**
     * Crea una variante con SKU automático si no se proporciona
     */
    public function createWithSKU($data) {
        if (empty($data['sku'])) {
            $data['sku'] = $this->generateSKU(
                $data['producto_id'],
                $data['talla'] ?? '',
                $data['color'] ?? ''
            );
        }
        
        return $this->create($data);
    }
    
    /**
     * Obtiene variantes con stock bajo (para alertas)
     */
    public function getLowStock($limite = 10) {
        $db = $this->getDb();
        $stmt = $db->prepare("
            SELECT 
                v.*,
                p.nombre as producto_nombre,
                p.marca as producto_marca
            FROM variantes_producto v
            INNER JOIN productos p ON v.producto_id = p.id
            WHERE v.activo = 1 AND v.stock <= ?
            ORDER BY v.stock ASC
        ");
        $stmt->execute([$limite]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtiene todas las tallas únicas disponibles
     */
    public function getAllTallas() {
        $db = $this->getDb();
        $stmt = $db->query("
            SELECT DISTINCT talla 
            FROM variantes_producto 
            WHERE talla IS NOT NULL AND talla != ''
            ORDER BY talla
        ");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * Obtiene todos los colores únicos disponibles
     */
    public function getAllColores() {
        $db = $this->getDb();
        $stmt = $db->query("
            SELECT DISTINCT color 
            FROM variantes_producto 
            WHERE color IS NOT NULL AND color != ''
            ORDER BY color
        ");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * Obtiene una variante con información del producto
     */
    public function findWithProducto($varianteId) {
        $db = $this->getDb();
        $stmt = $db->prepare("
            SELECT 
                v.*,
                p.nombre as producto_nombre,
                p.marca as producto_marca,
                p.descripcion as producto_descripcion,
                c.nombre as categoria_nombre,
                i.url as imagen_url
            FROM variantes_producto v
            INNER JOIN productos p ON v.producto_id = p.id
            INNER JOIN categorias c ON p.categoria_id = c.id
            LEFT JOIN imagenes_producto i ON p.id = i.producto_id AND i.es_principal = 1
            WHERE v.id = ?
        ");
        $stmt->execute([$varianteId]);
        return $stmt->fetch();
    }
    
    /**
     * Verifica si hay stock disponible para una variante
     */
    public function verificarStock($varianteId, $cantidad = 1) {
        $db = $this->getDb();
        $stmt = $db->prepare("
            SELECT stock 
            FROM variantes_producto 
            WHERE id = ? AND activo = 1
        ");
        $stmt->execute([$varianteId]);
        $variante = $stmt->fetch();
        
        if (!$variante) {
            return false;
        }
        
        return $variante['stock'] >= $cantidad;
    }
    
    /**
     * Obtiene el precio de una variante específica
     */
    public function getPrecio($varianteId) {
        $db = $this->getDb();
        $stmt = $db->prepare("
            SELECT precio 
            FROM variantes_producto 
            WHERE id = ?
        ");
        $stmt->execute([$varianteId]);
        $variante = $stmt->fetch();
        
        return $variante ? $variante['precio'] : 0;
    }
    
    /**
     * Decrementa el stock de una variante (alias de reduceStock)
     * Se usa al procesar un pedido
     */
    public function decrementarStock($varianteId, $cantidad) {
        return $this->reduceStock($varianteId, $cantidad);
    }
}
