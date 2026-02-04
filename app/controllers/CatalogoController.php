<?php
/**
 * CatalogoController - Visualización de productos
 * Refactorizado con mejores validaciones
 */

class CatalogoController extends Controller {
    
    /**
     * Muestra el catálogo de productos con filtros
     */
    public function index() {
        $productoModel = new Producto();
        $categoriaModel = new Categoria();
        
        $categoriaId = isset($_GET['categoria']) ? (int)$_GET['categoria'] : null;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        
        // Obtener productos según filtros
        if ($search !== '') {
            $productos = $productoModel->search($search, $categoriaId);
        } elseif ($categoriaId) {
            $productos = $productoModel->getByCategoria($categoriaId);
        } else {
            $productos = $productoModel->getActivosConImagen();
        }
        
        $categorias = $categoriaModel->all();
        
        $this->render('catalogo/index', [
            'productos' => $productos,
            'categorias' => $categorias,
            'search' => $search,
            'categoriaId' => $categoriaId,
            'titulo' => 'Catálogo'
        ]);
    }
    
    /**
     * Muestra el detalle de un producto
     */
    public function show($id) {
        $productoModel = new Producto();
        $varianteModel = new VarianteProducto();
        $imagenModel = new ImagenProducto();
        
        $productoId = (int)$id;
        $producto = $productoModel->findWithCategoria($productoId);
        
        if (!$producto || !$producto['activo']) {
            flash('error', MSG_ERROR_NO_ENCONTRADO);
            redirect('/catalogo');
        }
        
        $variantes = $varianteModel->getActivasByProducto($productoId);
        $imagenes = $imagenModel->getByProducto($productoId);
        
        $this->render('catalogo/show', [
            'producto' => $producto,
            'variantes' => $variantes,
            'imagenes' => $imagenes,
            'titulo' => e($producto['nombre'])
        ]);
    }
}
