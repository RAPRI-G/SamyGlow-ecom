-- =======================================================
-- BASE DE DATOS INTERMEDIA CON PROMOCIONES
-- =======================================================
CREATE DATABASE IF NOT EXISTS tienda_samyglow;
USE tienda_samyglow;

-- =======================================================
-- TABLA: USUARIOS (SOLO ADMIN)
-- =======================================================
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    correo VARCHAR(150) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('admin') DEFAULT 'admin',
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =======================================================
-- TABLA: CLIENTES
-- =======================================================
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    dni CHAR(8) UNIQUE NOT NULL,
    correo VARCHAR(150) NOT NULL,
    telefono CHAR(9),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_dni (dni),
    INDEX idx_correo (correo)
);

-- =======================================================
-- TABLA: CATEGORÍAS
-- =======================================================
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    activa BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =======================================================
-- TABLA: PRODUCTOS
-- =======================================================
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL CHECK (precio >= 0),
    stock INT DEFAULT 0 CHECK (stock >= 0),
    imagen VARCHAR(255),
    categoria_id INT,
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id),
    INDEX idx_categoria (categoria_id),
    INDEX idx_activo (activo)
);

-- =======================================================
-- TABLA: PROMOCIONES (SIMPLIFICADA)
-- =======================================================
CREATE TABLE promociones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    tipo ENUM('descuento_porcentaje', 'descuento_monto', 'combo', 'envio_gratis') NOT NULL,
    valor_descuento DECIMAL(10,2), -- % o monto fijo
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    activa BOOLEAN DEFAULT TRUE,
    max_usos INT DEFAULT NULL, -- Límite de usos (NULL = ilimitado)
    usos_actual INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_fechas (fecha_inicio, fecha_fin),
    INDEX idx_activa (activa)
);

-- =======================================================
-- TABLA: PRODUCTOS_EN_PROMOCION
-- =======================================================
CREATE TABLE productos_promocion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    promocion_id INT NOT NULL,
    producto_id INT NOT NULL,
    FOREIGN KEY (promocion_id) REFERENCES promociones(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id),
    UNIQUE KEY unique_promocion_producto (promocion_id, producto_id)
);

-- =======================================================
-- TABLA: MÉTODOS DE PAGO
-- =======================================================
CREATE TABLE metodos_pago (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    activo BOOLEAN DEFAULT TRUE
);

-- =======================================================
-- TABLA: PEDIDOS (ACTUALIZADA CON PROMOCIÓN)
-- =======================================================
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    subtotal DECIMAL(10,2) NOT NULL CHECK (subtotal >= 0),
    descuento_promocion DECIMAL(10,2) DEFAULT 0 CHECK (descuento_promocion >= 0),
    total DECIMAL(10,2) NOT NULL CHECK (total >= 0),
    estado ENUM('pendiente','entregado') DEFAULT 'pendiente',
    metodo_pago_id INT,
    promocion_id INT NULL, -- Promoción aplicada
    notas TEXT,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (metodo_pago_id) REFERENCES metodos_pago(id),
    FOREIGN KEY (promocion_id) REFERENCES promociones(id),
    INDEX idx_cliente (cliente_id),
    INDEX idx_estado (estado),
    INDEX idx_fecha (fecha)
);

-- =======================================================
-- TABLA: DETALLE DE PEDIDO
-- =======================================================
CREATE TABLE detalle_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL CHECK (cantidad > 0),
    precio_unitario DECIMAL(10,2) NOT NULL CHECK (precio_unitario >= 0),
    subtotal DECIMAL(10,2) NOT NULL CHECK (subtotal >= 0),
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id),
    INDEX idx_pedido (pedido_id)
);