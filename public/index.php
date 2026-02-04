<?php
/**
 * Front Controller - Punto de entrada de la aplicación
 */

// Cargar configuración PRIMERO
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';

// Cargar Router
require_once __DIR__ . '/../app/core/Router.php';

// LUEGO iniciar sesión
session_start();

// Crear instancia del router
$router = new Router();

// ===== RUTAS PÚBLICAS =====
$router->get('/', 'HomeController@index');

// Auth
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');

// Catálogo
$router->get('/catalogo', 'CatalogoController@index');
$router->get('/producto/:id', 'CatalogoController@show');

// ===== RUTAS PROTEGIDAS =====
// Carrito
$router->get('/carrito', 'CarritoController@index');
$router->post('/carrito/add', 'CarritoController@add');
$router->post('/carrito/update', 'CarritoController@update');
$router->post('/carrito/remove', 'CarritoController@remove');
$router->get('/carrito/checkout', 'CarritoController@checkout');
$router->post('/carrito/checkout', 'CarritoController@processCheckout');

// Pedidos
$router->get('/pedidos', 'PedidosController@index');
$router->get('/pedidos/:id', 'PedidosController@show');

// Perfil
$router->get('/perfil', 'PerfilController@index');
$router->post('/perfil', 'PerfilController@update');
$router->post('/perfil/password', 'PerfilController@changePassword');

// ===== RUTAS ADMIN =====
$router->get('/admin', 'AdminController@dashboard');

// Categorías
$router->get('/admin/categorias', 'AdminController@categorias');
$router->get('/admin/categorias/create', 'AdminController@categoriasCreate');
$router->post('/admin/categorias', 'AdminController@categoriasStore');
$router->get('/admin/categorias/:id/edit', 'AdminController@categoriasEdit');
$router->post('/admin/categorias/:id', 'AdminController@categoriasUpdate');
$router->post('/admin/categorias/:id/delete', 'AdminController@categoriasDelete');

// Productos
$router->get('/admin/productos', 'AdminController@productos');
$router->get('/admin/productos/create', 'AdminController@productosCreate');
$router->post('/admin/productos', 'AdminController@productosStore');
$router->post('/admin/productos/store-with-variants', 'AdminController@productosStoreWithVariants');
$router->get('/admin/productos/:id/edit', 'AdminController@productosEdit');
$router->post('/admin/productos/:id', 'AdminController@productosUpdate');
$router->post('/admin/productos/:id/delete', 'AdminController@productosDelete');

// Variantes
$router->post('/admin/variantes/update', 'AdminController@variantesUpdate');
$router->post('/admin/variantes/delete', 'AdminController@variantesDelete');
$router->get('/admin/variantes', 'AdminController@variantes');
$router->get('/admin/productos/:id/variantes', 'AdminController@productVariantes');
$router->post('/admin/variantes', 'AdminController@variantesStore');
$router->post('/admin/variantes/:id', 'AdminController@variantesUpdate');
$router->post('/admin/variantes/:id/delete', 'AdminController@variantesDelete');

// Imágenes
$router->get('/admin/imagenes', 'AdminController@imagenes');
$router->post('/admin/imagenes', 'AdminController@imagenesStore');
$router->post('/admin/imagenes/:id/delete', 'AdminController@imagenesDelete');

// Usuarios
$router->get('/admin/usuarios', 'AdminController@usuarios');
$router->get('/admin/usuarios/:id/edit', 'AdminController@usuariosEdit');
$router->post('/admin/usuarios/:id', 'AdminController@usuariosUpdate');
$router->post('/admin/usuarios/:id/toggle-estado', 'AdminController@usuariosToggleEstado');

// Despachos
$router->get('/admin/despachos', 'AdminController@despachos');
$router->get('/admin/despachos/:id', 'AdminController@despachoShow');
$router->post('/admin/despachos/:id/actualizar', 'AdminController@despachoActualizar');

// 404
$router->notFound(function() {
    http_response_code(404);
    echo '<h1>404 - Página no encontrada</h1>';
    echo '<p><a href="' . url('/') . '">Volver al inicio</a></p>';
});

// Ejecutar router
$router->run();