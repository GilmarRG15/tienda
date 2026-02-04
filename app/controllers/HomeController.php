<?php
/**
 * HomeController - Página principal
 * Refactorizado para claridad
 */

class HomeController extends Controller {
    
    /**
     * Muestra la página principal
     */
    public function index() {
        $productoModel = new Producto();
        $categoriaModel = new Categoria();
        
        // Obtener productos destacados (máximo 8)
        $productos = $productoModel->getActivosConImagen(8);
        
        // Obtener todas las categorías
        $categorias = $categoriaModel->all();
        
        $this->render('home/index', [
            'productos' => $productos,
            'categorias' => $categorias,
            'titulo' => 'Inicio'
        ]);
    }
}
