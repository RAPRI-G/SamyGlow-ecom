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

-- =======================================================
-- DATOS INICIALES
-- =======================================================

-- Usuario administrador
INSERT INTO usuarios (username, correo, password_hash) 
VALUES ('admin', 'admin@tienda.com', SHA2('admin123', 256));

-- Categorías básicas
INSERT INTO categorias (nombre) VALUES 
('Fragancias'),
('Cremas Corporales'),
('Splash Cabello');

-- Métodos de pago
INSERT INTO metodos_pago (nombre) VALUES 
('Yape'),
('Plin'),
('Tarjeta'),
('Efectivo');

-- Productos de ejemplo
INSERT INTO productos (nombre, descripcion, precio, stock, categoria_id) VALUES
('Aqua Kiss Body Mist', 'Fragancia refrescante con notas florales', 89.90, 15, 1),
('Pure Seduction Lotion', 'Crema corporal hidratante', 49.90, 20, 2),
('Love Spell Hair Mist', 'Splash para cabello con aroma frutal', 65.90, 10, 3),
('Velvet Petals Mist', 'Fragancia dulce y sensual', 92.90, 8, 1),
('Coco Shea Body Butter', 'Crema corporal nutritiva', 55.90, 12, 2);

-- Promociones de ejemplo (sin códigos)
INSERT INTO promociones (nombre, descripcion, tipo, valor_descuento, fecha_inicio, fecha_fin, max_usos) VALUES
('Descuento 20% Fragancias', '20% de descuento en todas las fragancias', 'descuento_porcentaje', 20.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 100),
('Envío Gratis +50€', 'Envío gratis en compras mayores a 50€', 'envio_gratis', NULL, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 60 DAY), NULL),
('Combo Cremas', 'Combo especial de cremas corporales', 'combo', 15.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 15 DAY), 50);

-- Asignar productos a promociones
INSERT INTO productos_promocion (promocion_id, producto_id) VALUES
(1, 1), -- Aqua Kiss en promoción 1
(1, 4), -- Velvet Petals en promoción 1
(3, 2), -- Pure Seduction en promoción 3
(3, 5); -- Coco Shea en promoción 3

-- PROCEDIMIENTOS

DELIMITER //
CREATE PROCEDURE CrearPedidoConPromocion(
    IN p_nombres VARCHAR(100),
    IN p_apellidos VARCHAR(100),
    IN p_dni CHAR(8),
    IN p_correo VARCHAR(150),
    IN p_telefono CHAR(9),
    IN p_metodo_pago_id INT,
    IN p_promocion_id INT, -- ID de la promoción directamente
    IN p_productos JSON
)
BEGIN
    DECLARE v_cliente_id INT;
    DECLARE v_pedido_id INT;
    DECLARE v_promocion_id INT;
    DECLARE v_subtotal DECIMAL(10,2) DEFAULT 0;
    DECLARE v_descuento DECIMAL(10,2) DEFAULT 0;
    DECLARE v_total DECIMAL(10,2) DEFAULT 0;
    DECLARE i INT DEFAULT 0;
    DECLARE v_producto_id INT;
    DECLARE v_cantidad INT;
    DECLARE v_precio DECIMAL(10,2);
    DECLARE v_item_subtotal DECIMAL(10,2);
    
    -- 1. Verificar/Insertar Cliente
    SELECT id INTO v_cliente_id FROM clientes WHERE dni = p_dni;
    IF v_cliente_id IS NULL THEN
        INSERT INTO clientes (nombres, apellidos, dni, correo, telefono) 
        VALUES (p_nombres, p_apellidos, p_dni, p_correo, p_telefono);
        SET v_cliente_id = LAST_INSERT_ID();
    END IF;
    
    -- 2. Verificar promoción si se proporcionó ID
    IF p_promocion_id IS NOT NULL THEN
        SELECT id INTO v_promocion_id 
        FROM promociones 
        WHERE id = p_promocion_id
          AND activa = TRUE 
          AND CURDATE() BETWEEN fecha_inicio AND fecha_fin
          AND (max_usos IS NULL OR usos_actual < max_usos);
    END IF;
    
    -- 3. Calcular subtotal
    WHILE i < JSON_LENGTH(p_productos) DO
        SET v_producto_id = JSON_EXTRACT(p_productos, CONCAT('$[', i, '].producto_id'));
        SET v_cantidad = JSON_EXTRACT(p_productos, CONCAT('$[', i, '].cantidad'));
        
        SELECT precio INTO v_precio FROM productos WHERE id = v_producto_id;
        SET v_item_subtotal = v_precio * v_cantidad;
        SET v_subtotal = v_subtotal + v_item_subtotal;
        
        SET i = i + 1;
    END WHILE;
    
    -- 4. Aplicar descuento si hay promoción válida
    IF v_promocion_id IS NOT NULL THEN
        SELECT valor_descuento INTO v_descuento FROM promociones WHERE id = v_promocion_id;
        SET v_descuento = (v_subtotal * v_descuento / 100); -- Para porcentaje
    END IF;
    
    SET v_total = v_subtotal - v_descuento;
    
    -- 5. Crear pedido
    INSERT INTO pedidos (cliente_id, subtotal, descuento_promocion, total, metodo_pago_id, promocion_id) 
    VALUES (v_cliente_id, v_subtotal, v_descuento, v_total, p_metodo_pago_id, v_promocion_id);
    SET v_pedido_id = LAST_INSERT_ID();
    
    -- 6. Actualizar usos de promoción
    IF v_promocion_id IS NOT NULL THEN
        UPDATE promociones SET usos_actual = usos_actual + 1 WHERE id = v_promocion_id;
    END IF;
    
    -- 7. Insertar detalles y actualizar stock
    SET i = 0;
    WHILE i < JSON_LENGTH(p_productos) DO
        SET v_producto_id = JSON_EXTRACT(p_productos, CONCAT('$[', i, '].producto_id'));
        SET v_cantidad = JSON_EXTRACT(p_productos, CONCAT('$[', i, '].cantidad'));
        
        SELECT precio INTO v_precio FROM productos WHERE id = v_producto_id;
        SET v_item_subtotal = v_precio * v_cantidad;
        
        INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio_unitario, subtotal)
        VALUES (v_pedido_id, v_producto_id, v_cantidad, v_precio, v_item_subtotal);
        
        UPDATE productos SET stock = stock - v_cantidad WHERE id = v_producto_id;
        
        SET i = i + 1;
    END WHILE;
    
    -- 8. Retornar resultado
    SELECT 
        v_pedido_id as pedido_id, 
        v_subtotal as subtotal,
        v_descuento as descuento,
        v_total as total,
        IF(v_promocion_id IS NOT NULL, 'Promoción aplicada', 'Sin promoción') as estado_promocion;
    
END//
DELIMITER ;

-- CONSULTAS SIMPLIFICADAS PARA PROMOCIONES
-- Gestión de Promociones (Admin)

-- Ver todas las promociones activas
SELECT 
    id,
    nombre,
    tipo,
    valor_descuento,
    fecha_inicio,
    fecha_fin,
    usos_actual,
    max_usos,
    activa
FROM promociones 
ORDER BY fecha_inicio DESC;

-- Ver productos en promoción
SELECT 
    pr.nombre as promocion,
    p.nombre as producto,
    p.precio,
    c.nombre as categoria
FROM productos_promocion pp
JOIN promociones pr ON pp.promocion_id = pr.id
JOIN productos p ON pp.producto_id = p.id
JOIN categorias c ON p.categoria_id = c.id
WHERE pr.activa = TRUE;

-- Promociones activas para mostrar en frontend
SELECT 
    pr.id,
    pr.nombre,
    pr.descripcion,
    pr.tipo,
    pr.valor_descuento
FROM promociones pr
WHERE pr.activa = TRUE
AND CURDATE() BETWEEN pr.fecha_inicio AND pr.fecha_fin
AND (pr.max_usos IS NULL OR pr.usos_actual < pr.max_usos);


-- Uso en el Frontend

-- Obtener promociones disponibles
SELECT 
    id,
    nombre,
    descripcion,
    tipo,
    valor_descuento
FROM promociones 
WHERE activa = TRUE
  AND CURDATE() BETWEEN fecha_inicio AND fecha_fin
  AND (max_usos IS NULL OR usos_actual < max_usos);

-- Productos con promociones activas
SELECT 
    p.id,
    p.nombre,
    p.precio,
    pr.nombre as promocion_nombre,
    pr.valor_descuento,
    ROUND(p.precio * (1 - pr.valor_descuento/100), 2) as precio_promocional
FROM productos p
JOIN productos_promocion pp ON p.id = pp.producto_id
JOIN promociones pr ON pp.promocion_id = pr.id
WHERE pr.activa = TRUE
AND CURDATE() BETWEEN pr.fecha_inicio AND pr.fecha_fin;