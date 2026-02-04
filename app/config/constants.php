<?php
/**
 * Constantes de la aplicación
 * Centraliza valores constantes para evitar "magic strings"
 */

// Estados de pedidos
define('PEDIDO_ESTADO_PENDIENTE', 'PENDIENTE');
define('PEDIDO_ESTADO_PROCESANDO', 'PROCESANDO');
define('PEDIDO_ESTADO_ENVIADO', 'ENVIADO');
define('PEDIDO_ESTADO_ENTREGADO', 'ENTREGADO');
define('PEDIDO_ESTADO_CANCELADO', 'CANCELADO');

// Roles de usuario
define('ROL_ADMIN', 'ADMIN');
define('ROL_GESTOR_PRODUCTOS', 'GESTOR_PRODUCTOS');
define('ROL_GESTOR_INVENTARIO', 'GESTOR_INVENTARIO');
define('ROL_DESPACHADOR', 'DESPACHADOR');
define('ROL_CLIENTE', 'CLIENTE');

// Estados de usuario
define('USUARIO_ESTADO_ACTIVO', 'ACTIVO');
define('USUARIO_ESTADO_BLOQUEADO', 'BLOQUEADO');

// Estados de despacho
define('DESPACHO_ESTADO_PENDIENTE', 'PENDIENTE');
define('DESPACHO_ESTADO_EN_CAMINO', 'EN_CAMINO');
define('DESPACHO_ESTADO_ENTREGADO', 'ENTREGADO');

// Mensajes de error comunes
define('MSG_ERROR_CSRF', 'Token de seguridad inválido');
define('MSG_ERROR_NO_ENCONTRADO', 'Recurso no encontrado');
define('MSG_ERROR_PERMISOS', 'No tienes permisos para realizar esta acción');
define('MSG_ERROR_GENERAL', 'Ha ocurrido un error. Intenta nuevamente');

// Mensajes de éxito comunes
define('MSG_SUCCESS_GUARDADO', 'Guardado exitosamente');
define('MSG_SUCCESS_ACTUALIZADO', 'Actualizado exitosamente');
define('MSG_SUCCESS_ELIMINADO', 'Eliminado exitosamente');
define('MSG_SUCCESS_CREADO', 'Creado exitosamente');

// Validaciones
define('MAX_UPLOAD_SIZE', 5242880); // 5MB en bytes
define('MIN_PASSWORD_LENGTH', 6);
define('MAX_PASSWORD_LENGTH', 255);

// Límites de paginación
define('ITEMS_PER_PAGE', 10);
define('PRODUCTOS_PER_PAGE', 12);
