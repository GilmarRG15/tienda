<?php
/**
 * Modelo Categoria
 */

class Categoria extends Model {
    protected $table = 'categorias';
    
    /**
     * Generar slug a partir del nombre
     */
    public function generateSlug($nombre) {
        $slug = strtolower(trim($nombre));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }
    
    /**
     * Crear categoría con slug automático
     */
    public function createWithSlug($data) {
        if (!isset($data['slug']) || empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['nombre']);
        }
        return $this->create($data);
    }
    
    /**
     * Buscar por slug
     */
    public function findBySlug($slug) {
        return $this->executeWithRetry(function() use ($slug) {
            $db = $this->getDb();
            $stmt = $db->prepare("SELECT * FROM categorias WHERE slug = ?");
            $stmt->execute([$slug]);
            return $stmt->fetch();
        });
    }
    
    /**
     * Contar productos por categoría
     */
    public function countProductos($categoriaId) {
        return $this->executeWithRetry(function() use ($categoriaId) {
            $db = $this->getDb();
            $stmt = $db->prepare("SELECT COUNT(*) as total FROM productos WHERE categoria_id = ? AND activo = 1");
            $stmt->execute([$categoriaId]);
            $result = $stmt->fetch();
            return $result['total'];
        });
    }
    
    /**
     * Obtener categorías con cantidad de productos
     */
    public function getAllWithProductCount() {
        return $this->executeWithRetry(function() {
            $db = $this->getDb();
            $stmt = $db->prepare("
                SELECT c.*, COUNT(p.id) as total_productos
                FROM categorias c
                LEFT JOIN productos p ON c.id = p.categoria_id AND p.activo = 1
                GROUP BY c.id
                ORDER BY c.nombre
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        });
    }
}