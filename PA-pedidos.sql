-- =======================================================
-- PROCEDIMIENTO ALMACENADO PARA CREAR PEDIDOS
-- =======================================================
DELIMITER //
CREATE PROCEDURE CrearPedidoConPromocion(
    IN p_nombres VARCHAR(100),
    IN p_apellidos VARCHAR(100),
    IN p_dni CHAR(8),
    IN p_correo VARCHAR(150),
    IN p_telefono CHAR(9),
    IN p_metodo_pago_id INT,
    IN p_promocion_id INT,
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
    SET v_promocion_id = NULL;
    IF p_promocion_id IS NOT NULL THEN
        SELECT id INTO v_promocion_id 
        FROM promociones 
        WHERE id = p_promocion_id
          AND activa = TRUE 
          AND CURDATE() BETWEEN fecha_inicio AND fecha_fin
          AND (max_usos IS NULL OR usos_actual < max_usos);
    END IF;
    
    -- 3. Calcular subtotal
    SET i = 0;
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
        SET v_descuento = (v_subtotal * v_descuento / 100);
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