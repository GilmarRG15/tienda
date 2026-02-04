-- ============================================
-- BASE DE DATOS TIENDA DEPORTIVA
-- Sistema MVC PHP Puro
-- ============================================

CREATE DATABASE IF NOT EXISTS bd_tienda;
USE bd_tienda;

-- ============================================
-- TABLA DE ROLES
-- ============================================
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT INTO roles (nombre) VALUES
('ADMIN'),
('GESTOR_PRODUCTOS'),
('GESTOR_INVENTARIO'),
('DESPACHADOR'),
('CLIENTE');

-- ============================================
-- TABLA DE USUARIOS
-- ============================================
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    estado ENUM('ACTIVO', 'BLOQUEADO') DEFAULT 'ACTIVO',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    ultimo_login DATETIME NULL
) ENGINE=InnoDB;

CREATE INDEX idx_usuarios_email ON usuarios(email);

ALTER TABLE usuarios
MODIFY estado ENUM('ACTIVO', 'BLOQUEADO') NOT NULL DEFAULT 'ACTIVO';

ALTER TABLE usuarios
ADD CONSTRAINT chk_password_hash_length
CHECK (CHAR_LENGTH(password_hash) >= 60);

SET time_zone = '+00:00';

DELIMITER $$
CREATE TRIGGER trg_usuarios_fecha_creacion
BEFORE INSERT ON usuarios
FOR EACH ROW
BEGIN
    IF NEW.fecha_creacion IS NULL THEN
        SET NEW.fecha_creacion = UTC_TIMESTAMP();
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER trg_usuarios_update_login
BEFORE UPDATE ON usuarios
FOR EACH ROW
BEGIN
    IF NEW.ultimo_login <> OLD.ultimo_login THEN
        SET NEW.ultimo_login = UTC_TIMESTAMP();
    END IF;
END$$
DELIMITER ;

-- ============================================
-- TABLA USUARIO_ROLES
-- ============================================
CREATE TABLE usuario_roles (
    usuario_id INT NOT NULL,
    rol_id INT NOT NULL,
    PRIMARY KEY (usuario_id, rol_id),
    CONSTRAINT fk_usuario_roles_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_usuario_roles_rol
        FOREIGN KEY (rol_id)
        REFERENCES roles(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================
-- TABLA CATEGORIAS
-- ============================================
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(120) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ============================================
-- TABLA PRODUCTOS
-- ============================================
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    marca VARCHAR(100),
    descripcion TEXT,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_productos_categoria
        FOREIGN KEY (categoria_id)
        REFERENCES categorias(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================
-- TABLA VARIANTES_PRODUCTO
-- ============================================
CREATE TABLE variantes_producto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    sku VARCHAR(50) NOT NULL UNIQUE,
    talla VARCHAR(20),
    color VARCHAR(50),
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    activo BOOLEAN DEFAULT TRUE,
    CONSTRAINT fk_variantes_producto
        FOREIGN KEY (producto_id)
        REFERENCES productos(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- TABLA IMAGENES_PRODUCTO
-- ============================================
CREATE TABLE imagenes_producto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    url VARCHAR(255) NOT NULL,
    es_principal BOOLEAN DEFAULT FALSE,
    orden INT DEFAULT 0,
    CONSTRAINT fk_imagenes_producto
        FOREIGN KEY (producto_id)
        REFERENCES productos(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- TABLA PEDIDOS
-- ============================================
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    estado ENUM(
        'PENDIENTE',
        'PAGADO',
        'EN_PREPARACION',
        'ENVIADO',
        'ENTREGADO',
        'CANCELADO'
    ) NOT NULL DEFAULT 'PENDIENTE',
    metodo_pago VARCHAR(50),
    estado_pago ENUM('PENDIENTE', 'APROBADO', 'RECHAZADO') DEFAULT 'PENDIENTE',
    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pedidos_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================
-- TABLA DETALLE_PEDIDO
-- ============================================
CREATE TABLE detalle_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    variante_id INT NOT NULL,
    nombre_snapshot VARCHAR(150) NOT NULL,
    talla_snapshot VARCHAR(20),
    color_snapshot VARCHAR(50),
    precio_snapshot DECIMAL(10,2) NOT NULL,
    cantidad INT NOT NULL,
    total_linea DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_detalle_pedido_pedido
        FOREIGN KEY (pedido_id)
        REFERENCES pedidos(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_detalle_pedido_variante
        FOREIGN KEY (variante_id)
        REFERENCES variantes_producto(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================
-- TABLA DESPACHO_PEDIDO
-- ============================================
CREATE TABLE despacho_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    asignado_a INT,
    nombre_recibe VARCHAR(150) NOT NULL,
    telefono VARCHAR(20),
    ciudad VARCHAR(100),
    direccion VARCHAR(255),
    referencia VARCHAR(255),
    estado_envio ENUM(
        'PENDIENTE',
        'EN_PREPARACION',
        'ENVIADO',
        'ENTREGADO'
    ) DEFAULT 'PENDIENTE',
    fecha_despacho DATETIME NULL,
    fecha_entrega DATETIME NULL,
    CONSTRAINT fk_despacho_pedido
        FOREIGN KEY (pedido_id)
        REFERENCES pedidos(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_despacho_asignado
        FOREIGN KEY (asignado_a)
        REFERENCES usuarios(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================
-- DATOS DE PRUEBA - USUARIOS
-- ============================================
-- Contraseñas: todas son "password123"
INSERT INTO usuarios (nombre, apellido, email, password_hash) VALUES
('Admin', 'Sistema', 'admin@tienda.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Juan', 'Pérez', 'gestor.productos@tienda.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('María', 'González', 'gestor.inventario@tienda.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Carlos', 'Ramírez', 'despachador@tienda.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Ana', 'Martínez', 'cliente1@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Pedro', 'López', 'cliente2@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Asignar roles
INSERT INTO usuario_roles (usuario_id, rol_id) VALUES
(1, 1), -- Admin
(2, 2), -- Gestor Productos
(3, 3), -- Gestor Inventario
(4, 4), -- Despachador
(5, 5), -- Cliente
(6, 5); -- Cliente

-- ============================================
-- DATOS DE PRUEBA - CATEGORÍAS
-- ============================================
INSERT INTO categorias (nombre, slug) VALUES
('Fútbol', 'futbol'),
('Running', 'running'),
('Gym', 'gym');

-- ============================================
-- DATOS DE PRUEBA - PRODUCTOS
-- ============================================
INSERT INTO productos (categoria_id, nombre, marca, descripcion, activo) VALUES
(1, 'Camiseta de Fútbol Pro', 'Nike', 'Camiseta deportiva de alta calidad para fútbol profesional', 1),
(1, 'Zapatos de Fútbol X-Speed', 'Adidas', 'Zapatos con tecnología de punta para máxima velocidad', 1),
(2, 'Zapatillas Running Air', 'Nike', 'Zapatillas con amortiguación de aire para running de larga distancia', 1),
(2, 'Shorts Running Pro', 'Puma', 'Shorts ligeros y transpirables para running', 1),
(3, 'Guantes de Gym PowerGrip', 'Reebok', 'Guantes con agarre antideslizante para levantamiento de pesas', 1),
(3, 'Mancuernas Ajustables 20kg', 'Bowflex', 'Set de mancuernas ajustables de 5 a 20 kg', 1),
(1, 'Balón de Fútbol Champions', 'Adidas', 'Balón oficial tamaño 5 para competiciones', 1),
(2, 'Reloj Deportivo GPS', 'Garmin', 'Reloj con GPS y monitor cardíaco para atletas', 1);

-- ============================================
-- DATOS DE PRUEBA - VARIANTES
-- ============================================
INSERT INTO variantes_producto (producto_id, sku, talla, color, precio, stock) VALUES
-- Camiseta Fútbol
(1, 'CAM-FUT-01-S-AZ', 'S', 'Azul', 45.00, 20),
(1, 'CAM-FUT-01-M-AZ', 'M', 'Azul', 45.00, 30),
(1, 'CAM-FUT-01-L-RO', 'L', 'Rojo', 45.00, 25),
-- Zapatos Fútbol
(2, 'ZAP-FUT-02-40-NE', '40', 'Negro', 120.00, 15),
(2, 'ZAP-FUT-02-42-BL', '42', 'Blanco', 120.00, 18),
-- Running Air
(3, 'ZAP-RUN-03-39-GR', '39', 'Gris', 95.00, 25),
(3, 'ZAP-RUN-03-41-NE', '41', 'Negro', 95.00, 30),
-- Shorts Running
(4, 'SHO-RUN-04-M-NE', 'M', 'Negro', 35.00, 40),
(4, 'SHO-RUN-04-L-AZ', 'L', 'Azul', 35.00, 35),
-- Guantes Gym
(5, 'GUA-GYM-05-M-NE', 'M', 'Negro', 25.00, 50),
(5, 'GUA-GYM-05-L-NE', 'L', 'Negro', 25.00, 45),
-- Mancuernas
(6, 'MAN-GYM-06-20K-NE', '20kg', 'Negro', 180.00, 10),
-- Balón
(7, 'BAL-FUT-07-5-BL', '5', 'Blanco', 60.00, 30),
-- Reloj GPS
(8, 'REL-RUN-08-U-NE', 'Único', 'Negro', 250.00, 12);

-- ============================================
-- DATOS DE PRUEBA - IMÁGENES
-- ============================================
INSERT INTO imagenes_producto (producto_id, url, es_principal, orden) VALUES
(1, '/assets/img/productos/camiseta-futbol.jpg', 1, 1),
(2, '/assets/img/productos/zapatos-futbol.jpg', 1, 1),
(3, '/assets/img/productos/zapatillas-running.jpg', 1, 1),
(4, '/assets/img/productos/shorts-running.jpg', 1, 1),
(5, '/assets/img/productos/guantes-gym.jpg', 1, 1),
(6, '/assets/img/productos/mancuernas.jpg', 1, 1),
(7, '/assets/img/productos/balon-futbol.jpg', 1, 1),
(8, '/assets/img/productos/reloj-gps.jpg', 1, 1);

-- ============================================
-- RESUMEN DE USUARIOS DEMO
-- ============================================
-- Email: admin@tienda.com | Password: password123 | Rol: ADMIN
-- Email: gestor.productos@tienda.com | Password: password123 | Rol: GESTOR_PRODUCTOS
-- Email: gestor.inventario@tienda.com | Password: password123 | Rol: GESTOR_INVENTARIO
-- Email: despachador@tienda.com | Password: password123 | Rol: DESPACHADOR
-- Email: cliente1@email.com | Password: password123 | Rol: CLIENTE
-- Email: cliente2@email.com | Password: password123 | Rol: CLIENTE
