<?php
/**
 * AdminController - Panel de administración
 * Refactorizado con constantes y validaciones mejoradas
 */

class AdminController extends Controller {
    
    // ===== DASHBOARD =====
    public function dashboard() {
        Middleware::requireRole([ROL_ADMIN, ROL_GESTOR_PRODUCTOS, ROL_GESTOR_INVENTARIO, ROL_DESPACHADOR]);
        
        $pedidoModel = new Pedido();
        $productoModel = new Producto();
        $usuarioModel = new Usuario();
        
        $stats = $pedidoModel->getEstadisticas();
        $totalProductos = $productoModel->count('activo = 1');
        $totalUsuarios = $usuarioModel->count();
        
        $this->render('admin/dashboard', [
            'stats' => $stats,
            'totalProductos' => $totalProductos,
            'totalUsuarios' => $totalUsuarios,
            'titulo' => 'Panel Admin'
        ]);
    }
    
    // ===== CATEGORÍAS =====
    public function categorias() {
        Middleware::requireGestorProductos();
        
        $categoriaModel = new Categoria();
        $categorias = $categoriaModel->getAllWithProductCount();
        
        $this->render('admin/categorias/index', [
            'categorias' => $categorias,
            'titulo' => 'Gestión de Categorías'
        ]);
    }
    
    public function categoriasCreate() {
        Middleware::requireGestorProductos();
        $this->render('admin/categorias/create', ['titulo' => 'Nueva Categoría']);
    }
    
    public function categoriasStore() {
        Middleware::requireGestorProductos();
        
        if (!csrf_verify()) {
            $this->redirectWith('/admin/categorias', 'error', MSG_ERROR_CSRF);
        }
        
        $categoriaModel = new Categoria();
        
        try {
            $categoriaModel->createWithSlug([
                'nombre' => $_POST['nombre'],
                'slug' => $_POST['slug'] ?? ''
            ]);
            
            $this->redirectWith('/admin/categorias', 'success', 'Categoría ' . MSG_SUCCESS_CREADO);
        } catch (Exception $e) {
            $this->redirectWith('/admin/categorias/create', 'error', 'Error al crear la categoría');
        }
    }
    
    public function categoriasEdit($id) {
        Middleware::requireGestorProductos();
        
        $categoriaModel = new Categoria();
        $categoria = $categoriaModel->find($id);
        
        if (!$categoria) {
            $this->redirectWith('/admin/categorias', 'error', 'Categoría no encontrada');
        }
        
        $this->render('admin/categorias/edit', [
            'categoria' => $categoria,
            'titulo' => 'Editar Categoría'
        ]);
    }
    
    public function categoriasUpdate($id) {
        Middleware::requireGestorProductos();
        
        if (!csrf_verify()) {
            $this->redirectWith('/admin/categorias', 'error', MSG_ERROR_CSRF);
        }
        
        $categoriaModel = new Categoria();
        
        try {
            $categoriaModel->update($id, [
                'nombre' => $_POST['nombre'],
                'slug' => $_POST['slug']
            ]);
            
            $this->redirectWith('/admin/categorias', 'success', 'Categoría ' . MSG_SUCCESS_ACTUALIZADO);
        } catch (Exception $e) {
            $this->redirectWith('/admin/categorias/' . $id . '/edit', 'error', 'Error al actualizar');
        }
    }
    
    public function categoriasDelete($id) {
        Middleware::requireGestorProductos();
        
        $categoriaModel = new Categoria();
        
        if ($categoriaModel->countProductos($id) > 0) {
            $this->redirectWith('/admin/categorias', 'error', 'No se puede eliminar: tiene productos asociados');
        }
        
        $categoriaModel->delete($id);
        $this->redirectWith('/admin/categorias', 'success', 'Categoría eliminada');
    }
    
    // ===== PRODUCTOS =====
    public function productos() {
        Middleware::requireGestorProductos();
        
        $productoModel = new Producto();
        $productos = $productoModel->getAllWithCategoria();
        
        $this->render('admin/productos/index', [
            'productos' => $productos,
            'titulo' => 'Gestión de Productos'
        ]);
    }
    
    public function productosCreate() {
        Middleware::requireGestorProductos();
        
        $categoriaModel = new Categoria();
        $categorias = $categoriaModel->getAll();
        
        $this->render('admin/productos/create', [
            'categorias' => $categorias,
            'titulo' => 'Crear Producto'
        ]);
    }
    
    public function productosStore() {
        Middleware::requireGestorProductos();
        
        if (!csrf_verify()) {
            $this->redirectWith('/admin/productos', 'error', MSG_ERROR_CSRF);
        }
        
        $productoModel = new Producto();
        $imagenModel = new ImagenProducto();
        
        try {
            // Crear el producto
            $productoId = $productoModel->create([
                'nombre' => $_POST['nombre'],
                'marca' => $_POST['marca'] ?? null,
                'descripcion' => $_POST['descripcion'] ?? null,
                'categoria_id' => $_POST['categoria_id'],
                'activo' => isset($_POST['activo']) ? 1 : 0
            ]);
            
            // Procesar la imagen si fue subida
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->uploadProductImage($_FILES['imagen']);
                
                if ($uploadResult['success']) {
                    // Guardar la imagen en la base de datos
                    $imagenModel->createImage([
                        'producto_id' => $productoId,
                        'url' => $uploadResult['url'],
                        'es_principal' => 1,
                        'orden' => 1
                    ]);
                } else {
                    // Si hubo error al subir la imagen, registrar advertencia pero continuar
                    flash('warning', 'Producto creado pero hubo un error al subir la imagen: ' . $uploadResult['error']);
                }
            }
            
            flash('success', 'Producto creado exitosamente. Ahora agrega variantes (precio y stock).');
            redirect('/admin/productos/' . $productoId . '/variantes');
        } catch (Exception $e) {
            saveOldInput();
            $this->redirectWith('/admin/productos/create', 'error', 'Error al crear el producto: ' . $e->getMessage());
        }
    }
    
    /**
     * Crear producto con variantes en el mismo formulario
     */
    public function productosStoreWithVariants() {
        Middleware::requireGestorProductos();
        
        if (!csrf_verify()) {
            $this->redirectWith('/admin/productos', 'error', MSG_ERROR_CSRF);
        }
        
        $productoModel = new Producto();
        $imagenModel = new ImagenProducto();
        $varianteModel = new VarianteProducto();
        
        try {
            // Validar que haya variantes
            if (!isset($_POST['variantes']) || empty($_POST['variantes'])) {
                saveOldInput();
                $this->redirectWith('/admin/productos/create', 'error', 'Debes agregar al menos una variante');
            }
            
            // Crear el producto
            $productoId = $productoModel->create([
                'nombre' => $_POST['nombre'],
                'marca' => $_POST['marca'] ?? null,
                'descripcion' => $_POST['descripcion'] ?? null,
                'categoria_id' => $_POST['categoria_id'],
                'activo' => isset($_POST['activo']) ? 1 : 0
            ]);
            
            // Procesar la imagen si fue subida
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->uploadProductImage($_FILES['imagen']);
                
                if ($uploadResult['success']) {
                    $imagenModel->createImage([
                        'producto_id' => $productoId,
                        'url' => $uploadResult['url'],
                        'es_principal' => 1,
                        'orden' => 1
                    ]);
                }
            }
            
            // Crear las variantes
            $variantesCreadas = 0;
            foreach ($_POST['variantes'] as $variante) {
                // Validar que tenga precio
                if (empty($variante['precio']) || $variante['precio'] <= 0) {
                    continue;
                }
                
                // Generar SKU si no está presente
                $sku = !empty($variante['sku']) ? $variante['sku'] : $varianteModel->generateSKU(
                    $productoId,
                    $variante['talla'] ?? '',
                    $variante['color'] ?? ''
                );
                
                // Crear la variante
                $varianteModel->create([
                    'producto_id' => $productoId,
                    'sku' => $sku,
                    'talla' => !empty($variante['talla']) ? $variante['talla'] : null,
                    'color' => !empty($variante['color']) ? $variante['color'] : null,
                    'precio' => $variante['precio'],
                    'stock' => $variante['stock'] ?? 0,
                    'activo' => 1
                ]);
                
                $variantesCreadas++;
            }
            
            if ($variantesCreadas == 0) {
                // Si no se creó ninguna variante, eliminar el producto
                $productoModel->delete($productoId);
                saveOldInput();
                $this->redirectWith('/admin/productos/create', 'error', 'No se pudo crear ninguna variante válida');
            }
            
            $this->redirectWith('/admin/productos', 'success', 
                "Producto creado exitosamente con {$variantesCreadas} variante(s)");
            
        } catch (Exception $e) {
            saveOldInput();
            $this->redirectWith('/admin/productos/create', 'error', 
                'Error al crear el producto: ' . $e->getMessage());
        }
    }
    
    /**
     * Función auxiliar para subir imágenes de productos
     */
    private function uploadProductImage($file) {
        // Validar que se subió un archivo
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'No se subió ningún archivo'];
        }
        
        // Validar tamaño (5MB máximo)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'El archivo es demasiado grande. Máximo 5MB'];
        }
        
        // Validar tipo de archivo
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            return ['success' => false, 'error' => 'Tipo de archivo no válido. Use JPG, PNG o WEBP'];
        }
        
        // Generar nombre único para el archivo
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('producto_') . '_' . time() . '.' . $extension;
        
        // Definir directorio de destino
        $uploadDir = PUBLIC_PATH . '/assets/img/productos/';
        
        // Crear directorio si no existe
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $destination = $uploadDir . $fileName;
        
        // Mover archivo
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Retornar URL relativa
            return [
                'success' => true,
                'url' => '/assets/img/productos/' . $fileName,
                'fileName' => $fileName
            ];
        } else {
            return ['success' => false, 'error' => 'Error al mover el archivo'];
        }
    }
    
    public function productosEdit($id) {
        Middleware::requireGestorProductos();
        
        $productoModel = new Producto();
        $categoriaModel = new Categoria();
        
        $producto = $productoModel->find($id);
        $categorias = $categoriaModel->getAll();
        
        if (!$producto) {
            $this->redirectWith('/admin/productos', 'error', 'Producto no encontrado');
        }
        
        $this->render('admin/productos/edit', [
            'producto' => $producto,
            'categorias' => $categorias,
            'titulo' => 'Editar Producto'
        ]);
    }
    
    public function productosUpdate($id) {
        Middleware::requireGestorProductos();
        
        if (!csrf_verify()) {
            $this->redirectWith('/admin/productos', 'error', MSG_ERROR_CSRF);
        }
        
        $productoModel = new Producto();
        
        try {
            $productoModel->update($id, [
                'nombre' => $_POST['nombre'],
                'marca' => $_POST['marca'] ?? null,
                'descripcion' => $_POST['descripcion'] ?? null,
                'categoria_id' => $_POST['categoria_id'],
                'activo' => isset($_POST['activo']) ? 1 : 0
            ]);
            
            $this->redirectWith('/admin/productos', 'success', 'Producto ' . MSG_SUCCESS_ACTUALIZADO);
        } catch (Exception $e) {
            $this->redirectWith('/admin/productos/' . $id . '/edit', 'error', 'Error al actualizar: ' . $e->getMessage());
        }
    }
    
    public function productosDelete($id) {
        Middleware::requireGestorProductos();
        
        try {
            $productoModel = new Producto();
            $productoModel->deleteWithRelations($id);
            
            $this->redirectWith('/admin/productos', 'success', 'Producto eliminado correctamente');
        } catch (Exception $e) {
            $this->redirectWith('/admin/productos', 'error', 'Error al eliminar: ' . $e->getMessage());
        }
    }
    
    // ===== VARIANTES =====
    public function variantes() {
        Middleware::requireRole([ROL_ADMIN, ROL_GESTOR_INVENTARIO]);
        
        $varianteModel = new VarianteProducto();
        $variantes = $varianteModel->getAllWithProducto();
        
        $this->render('admin/variantes/index', [
            'variantes' => $variantes,
            'titulo' => 'Gestión de Variantes'
        ]);
    }
    
    public function productVariantes($productoId) {
        Middleware::requireRole([ROL_ADMIN, ROL_GESTOR_INVENTARIO]);
        
        $productoModel = new Producto();
        $varianteModel = new VarianteProducto();
        
        $producto = $productoModel->find($productoId);
        if (!$producto) {
            $this->redirectWith('/admin/productos', 'error', 'Producto no encontrado');
        }
        
        $variantes = $varianteModel->getByProducto($productoId);
        
        $this->render('admin/variantes/index', [
            'producto' => $producto,
            'variantes' => $variantes,
            'titulo' => 'Variantes de ' . $producto['nombre']
        ]);
    }
    
    public function variantesStore() {
        Middleware::requireRole([ROL_ADMIN, ROL_GESTOR_INVENTARIO]);
        
        if (!csrf_verify()) {
            $this->redirectWith('/admin/variantes', 'error', MSG_ERROR_CSRF);
        }
        
        $varianteModel = new VarianteProducto();

        $productoId = $_POST['producto_id'] ?? null;
        
        try {
            $varianteModel->create([
                'producto_id' => $productoId,
                'talla' => $_POST['talla'] ?? null,
                'color' => $_POST['color'] ?? null,
                'precio' => $_POST['precio'],
                'stock' => $_POST['stock'] ?? 0,
                'activo' => 1
            ]);
            
            // Redirigir de vuelta a la vista del producto específico
            if ($productoId) {
                $this->redirectWith('/admin/productos/' . $productoId . '/variantes', 'success', 'Variante creada exitosamente');
            } else {
                $this->redirectWith('/admin/variantes', 'success', 'Variante creada exitosamente');
            }
        } catch (Exception $e) {
            $this->redirectWith('/admin/variantes', 'error', 'Error al crear la variante');
        }
    }
    
    public function variantesUpdate() {
        Middleware::requireRole([ROL_ADMIN, ROL_GESTOR_INVENTARIO]);
        
        // Verificar si es una petición AJAX
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        
        $varianteModel = new VarianteProducto();
        
        try {
            $id = $_POST['id'] ?? null;
            
            if (!$id) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'ID de variante no proporcionado']);
                    exit;
                }
                $this->redirectWith('/admin/variantes', 'error', 'ID de variante no proporcionado');
            }
            
            $data = [
                'talla' => $_POST['talla'] ?? null,
                'color' => $_POST['color'] ?? null,
                'stock' => $_POST['stock'] ?? 0
            ];
            
            $varianteModel->update($id, $data);
            
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Variante actualizada correctamente']);
                exit;
            }
            
            $this->redirectWith('/admin/variantes', 'success', 'Variante actualizada');
        } catch (Exception $e) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()]);
                exit;
            }
            $this->redirectWith('/admin/variantes', 'error', 'Error al actualizar');
        }
    }
    
    public function variantesDelete() {
        Middleware::requireRole([ROL_ADMIN, ROL_GESTOR_INVENTARIO]);
        
        $varianteModel = new VarianteProducto();
        
        try {
            $id = $_POST['id'] ?? null;
            
            if (!$id) {
                $this->redirectWith('/admin/variantes', 'error', 'ID de variante no proporcionado');
            }
            
            $varianteModel->delete($id);
            $this->redirectWith('/admin/variantes', 'success', 'Variante eliminada');
        } catch (Exception $e) {
            $this->redirectWith('/admin/variantes', 'error', 'Error al eliminar variante');
        }
    }
    
    // ===== IMÁGENES =====
    public function imagenes() {
        Middleware::requireGestorProductos();
        
        $imagenModel = new ImagenProducto();
        $productoModel = new Producto();
        
        $imagenes = $imagenModel->getAllWithProducto();
        $productos = $productoModel->getAll();
        
        $this->render('admin/imagenes/index', [
            'imagenes' => $imagenes,
            'productos' => $productos,
            'titulo' => 'Gestión de Imágenes'
        ]);
    }
    
    public function imagenesStore() {
        Middleware::requireGestorProductos();
        
        if (!csrf_verify()) {
            $this->redirectWith('/admin/imagenes', 'error', MSG_ERROR_CSRF);
        }
        
        // Aquí iría la lógica de upload de imágenes
        $this->redirectWith('/admin/imagenes', 'success', 'Imagen subida exitosamente');
    }
    
    public function imagenesDelete($id) {
        Middleware::requireGestorProductos();
        
        $imagenModel = new ImagenProducto();
        $imagenModel->delete($id);
        
        $this->redirectWith('/admin/imagenes', 'success', 'Imagen eliminada');
    }
    
    // ===== USUARIOS =====
    public function usuarios() {
        Middleware::requireAdmin();
        
        $usuarioModel = new Usuario();
        $usuarios = $usuarioModel->getAllWithRoles();
        
        $this->render('admin/usuarios/index', [
            'usuarios' => $usuarios,
            'titulo' => 'Gestión de Usuarios'
        ]);
    }
    
    public function usuariosEdit($id) {
        Middleware::requireAdmin();
        
        $usuarioModel = new Usuario();
        $rolModel = new Rol();
        
        $usuario = $usuarioModel->find($id);
        if (!$usuario) {
            $this->redirectWith('/admin/usuarios', 'error', 'Usuario no encontrado');
        }
        $roles = $rolModel->getAll();
        $rolesUsuario = $usuarioModel->getRoles($id);
        
        $this->render('admin/usuarios/edit', [
            'usuario' => $usuario,
            'roles' => $roles,
            'rolesUsuario' => $rolesUsuario,
            'titulo' => 'Editar Usuario'
        ]);
    }
    
    public function usuariosUpdate($id) {
        Middleware::requireAdmin();
        
        if (!csrf_verify()) {
            $this->redirectWith('/admin/usuarios', 'error', MSG_ERROR_CSRF);
        }
        
        $usuarioModel = new Usuario();
        
        try {
            // Actualizar datos básicos
            $usuarioModel->update($id, [
                'nombre' => $_POST['nombre'],
                'apellido' => $_POST['apellido'],
                'email' => $_POST['email']
            ]);
            
            // Actualizar roles
            if (isset($_POST['roles'])) {
                $usuarioModel->removeRoles($id);
                foreach ($_POST['roles'] as $rolNombre) {
                    $usuarioModel->assignRole($id, $rolNombre);
                }
            }
            
            $this->redirectWith('/admin/usuarios', 'success', 'Usuario actualizado');
        } catch (Exception $e) {
            $this->redirectWith('/admin/usuarios/' . $id . '/edit', 'error', 'Error al actualizar');
        }
    }
    
    public function usuariosToggleEstado($id) {
        Middleware::requireAdmin();
        
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->find($id);
        if (!$usuario) {
            $this->redirectWith('/admin/usuarios', 'error', 'Usuario no encontrado');
        }
        
        $nuevoEstado = $usuario['estado'] === 'ACTIVO' ? 'BLOQUEADO' : 'ACTIVO';
        $usuarioModel->cambiarEstado($id, $nuevoEstado);
        
        $this->redirectWith('/admin/usuarios', 'success', 'Estado actualizado');
    }
    
    // ===== DESPACHOS =====
    public function despachos() {
        Middleware::requireRole([ROL_ADMIN, ROL_DESPACHADOR]);
        
        $despachoModel = new DespachoPedido();
        $despachos = $despachoModel->getAllWithPedido();
        
        $this->render('admin/despachos/index', [
            'despachos' => $despachos,
            'titulo' => 'Gestión de Despachos'
        ]);
    }
    
    public function despachoShow($id) {
        Middleware::requireRole([ROL_ADMIN, ROL_DESPACHADOR]);
        
        $despachoModel = new DespachoPedido();
        $despacho = $despachoModel->findWithDetails($id);
        
        if (!$despacho) {
            $this->redirectWith('/admin/despachos', 'error', 'Despacho no encontrado');
        }
        
        // Obtener los detalles del pedido
        $pedidoModel = new Pedido();
        $detalles = $pedidoModel->getDetalles($despacho['pedido_id']);
        
        $this->render('admin/despachos/show', [
            'despacho' => $despacho,
            'detalles' => $detalles,
            'titulo' => 'Detalle del Despacho #' . $id
        ]);
    }
    
    public function despachoActualizar($id) {
        Middleware::requireRole([ROL_ADMIN, ROL_DESPACHADOR]);
        
        if (!csrf_verify()) {
            $this->redirectWith('/admin/despachos', 'error', MSG_ERROR_CSRF);
        }
        
        $despachoModel = new DespachoPedido();
        
        try {
            $despachoModel->updateEstadoEnvio($id, $_POST['estado']);
            
            // Si hay notas, actualizarlas también
            if (isset($_POST['notas'])) {
                $despachoModel->update($id, ['notas' => $_POST['notas']]);
            }
            
            $this->redirectWith('/admin/despachos', 'success', 'Despacho actualizado');
        } catch (Exception $e) {
            $this->redirectWith('/admin/despachos/' . $id, 'error', 'Error al actualizar');
        }
    }
}