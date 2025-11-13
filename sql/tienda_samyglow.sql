-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-11-2025 a las 06:10:01
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tienda_samyglow`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `CrearPedidoConPromocion` (IN `p_nombres` VARCHAR(100), IN `p_apellidos` VARCHAR(100), IN `p_dni` CHAR(8), IN `p_correo` VARCHAR(150), IN `p_telefono` CHAR(9), IN `p_metodo_pago_id` INT, IN `p_promocion_id` INT, IN `p_productos` JSON)   BEGIN
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
    
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activa` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `activa`, `created_at`) VALUES
(1, 'Fragancias', 'Fragancias y perfumes para el cuidado personal', 1, '2025-11-09 04:17:40'),
(2, 'Cremas Corporales', 'Cremas y lociones para hidratación corporal', 1, '2025-11-09 04:17:40'),
(3, 'Body Splash', 'Sprays corporales refrescantes y aromáticos', 1, '2025-11-09 04:17:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `dni` char(8) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `telefono` char(9) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `eliminado` tinyint(1) DEFAULT 0,
  `fecha_eliminado` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombres`, `apellidos`, `dni`, `correo`, `telefono`, `created_at`, `eliminado`, `fecha_eliminado`) VALUES
(1, 'María', 'Gonzales López', '23456789', 'maria.gonzales@email.com', '912345678', '2025-11-09 04:17:40', 0, NULL),
(2, 'Carlos', 'Rodríguez Paz', '34567890', 'carlos.rodriguez@email.com', '923456789', '2025-11-09 04:17:40', 0, NULL),
(3, 'Ana', 'Martínez Soto', '45678901', 'anamartinez@email.com', '934567890', '2025-11-09 04:17:40', 0, NULL),
(4, 'Luis', 'Torres Mendoza', '56789012', 'luis.torres@email.com', '945678901', '2025-11-09 04:17:40', 0, NULL),
(5, 'Sofia', 'Ramírez Cruz', '67890123', 'sofia.ramirez@email.com', '956789012', '2025-11-09 04:17:40', 0, NULL),
(6, 'Jorge', 'Díaz Herrera', '78901234', 'jorge.diaz@email.com', '967890123', '2025-11-09 04:17:40', 0, NULL),
(7, 'Elena', 'Castillo Rojas', '89012345', 'elena.castillo@email.com', '978901234', '2025-11-09 04:17:40', 0, NULL),
(8, 'Miguel', 'Vargas Fuentes', '90123456', 'miguel.vargas@email.com', '989012345', '2025-11-09 04:17:40', 0, NULL),
(9, 'gabriel francis', 'rapri capcha', '72571243', 'gabrielrapri14@gmail.com', '948537363', '2025-11-09 16:17:21', 0, NULL),
(11, 'samira tayli', 'rivera soller', '73027729', 'sollerriverasamira@gmail.com', '919462329', '2025-11-13 02:48:13', 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedido`
--

CREATE TABLE `detalle_pedido` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL CHECK (`cantidad` > 0),
  `precio_unitario` decimal(10,2) NOT NULL CHECK (`precio_unitario` >= 0),
  `subtotal` decimal(10,2) NOT NULL CHECK (`subtotal` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_pedido`
--

INSERT INTO `detalle_pedido` (`id`, `pedido_id`, `producto_id`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 1, 1, 1, 95.90, 95.90),
(2, 1, 13, 1, 65.90, 65.90),
(3, 1, 25, 1, 55.90, 55.90),
(4, 2, 3, 2, 82.90, 165.80),
(5, 2, 17, 1, 59.90, 59.90),
(6, 2, 30, 1, 59.90, 59.90),
(7, 3, 18, 2, 75.90, 151.80),
(8, 3, 22, 1, 79.90, 79.90),
(9, 4, 2, 1, 88.90, 88.90),
(10, 4, 14, 1, 68.90, 68.90),
(11, 4, 26, 1, 58.90, 58.90),
(12, 4, 5, 1, 89.90, 89.90),
(13, 4, 17, 1, 66.90, 66.90),
(14, 5, 6, 1, 92.90, 92.90),
(15, 5, 8, 1, 86.90, 86.90),
(16, 6, 12, 1, 102.90, 102.90),
(17, 6, 20, 1, 79.90, 79.90),
(18, 6, 36, 1, 67.90, 67.90),
(19, 7, 14, 1, 68.90, 68.90),
(20, 7, 18, 1, 75.90, 75.90),
(21, 7, 22, 1, 79.90, 79.90),
(22, 8, 4, 1, 85.90, 85.90),
(23, 8, 28, 1, 54.90, 54.90),
(24, 8, 16, 1, 64.90, 64.90),
(25, 9, 9, 1, 94.90, 94.90),
(26, 9, 1, 1, 95.90, 95.90),
(27, 9, 17, 1, 66.90, 66.90),
(28, 10, 9, 1, 65.00, 65.00),
(29, 11, 33, 1, 60.90, 60.90);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodos_pago`
--

CREATE TABLE `metodos_pago` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `metodos_pago`
--

INSERT INTO `metodos_pago` (`id`, `nombre`, `activo`) VALUES
(1, 'Yape', 1),
(2, 'Plin', 1),
(3, 'Tarjeta de Crédito', 1),
(4, 'Transferencia Bancaria', 1),
(5, 'Efectivo', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `subtotal` decimal(10,2) NOT NULL CHECK (`subtotal` >= 0),
  `descuento_promocion` decimal(10,2) DEFAULT 0.00 CHECK (`descuento_promocion` >= 0),
  `total` decimal(10,2) NOT NULL CHECK (`total` >= 0),
  `estado` enum('pendiente','entregado') DEFAULT 'pendiente',
  `metodo_pago_id` int(11) DEFAULT NULL,
  `promocion_id` int(11) DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `eliminado` tinyint(1) DEFAULT 0,
  `fecha_eliminado` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `cliente_id`, `fecha`, `subtotal`, `descuento_promocion`, `total`, `estado`, `metodo_pago_id`, `promocion_id`, `notas`, `eliminado`, `fecha_eliminado`) VALUES
(1, 1, '2025-11-09 04:17:40', 185.70, 27.85, 157.85, 'entregado', 1, 4, NULL, 0, NULL),
(2, 2, '2025-11-09 04:17:40', 240.50, 0.00, 240.50, 'pendiente', 2, NULL, NULL, 0, NULL),
(3, 3, '2025-11-09 04:17:40', 156.80, 39.20, 117.60, 'entregado', 1, 5, NULL, 0, NULL),
(4, 4, '2025-11-09 04:17:40', 320.90, 64.18, 256.72, 'pendiente', 3, 7, NULL, 0, NULL),
(5, 5, '2025-11-09 04:17:40', 178.60, 35.72, 142.88, 'entregado', 4, 1, NULL, 0, NULL),
(6, 6, '2025-11-09 04:17:40', 295.40, 0.00, 295.40, 'pendiente', 2, NULL, NULL, 0, NULL),
(7, 7, '2025-11-09 04:17:40', 210.70, 52.67, 158.03, 'entregado', 1, 5, NULL, 0, NULL),
(8, 8, '2025-11-09 04:17:40', 189.50, 0.00, 189.50, 'entregado', 5, NULL, NULL, 0, NULL),
(9, 9, '2025-11-09 16:17:21', 257.70, 0.00, 257.70, 'entregado', 1, NULL, 'llevara dos bolsas', 0, NULL),
(10, 11, '2025-11-13 05:00:15', 65.00, 0.00, 65.00, 'pendiente', 1, NULL, '', 0, NULL),
(11, 11, '2025-11-13 05:08:04', 60.90, 0.00, 60.90, 'entregado', 1, NULL, '', 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL CHECK (`precio` >= 0),
  `stock` int(11) DEFAULT 0 CHECK (`stock` >= 0),
  `imagen` varchar(255) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `eliminado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `stock`, `imagen`, `categoria_id`, `activo`, `created_at`, `eliminado`) VALUES
(1, 'Bare Vanilla Fragrance Mist', 'Fragancia dulce y sensual con notas de vainilla y crema', 95.90, 17, NULL, 1, 1, '2025-11-09 04:17:40', 0),
(2, 'Velvet Petals Fragrance Mist', 'Aroma floral con notas de pétalos de seda y almizcle', 88.90, 12, NULL, 1, 1, '2025-11-09 04:17:40', 0),
(3, 'Pure Seduction Fragrance Mist', 'Fragancia frutal con ciruelas rojas y fresias', 82.90, 15, NULL, 1, 1, '2025-11-09 04:17:40', 0),
(4, 'Love Spell Fragrance Mist', 'Perfume con durazno, cereza y flor de melocotón', 85.90, 20, NULL, 1, 1, '2025-11-09 04:17:40', 0),
(5, 'Aqua Kiss Fragrance Mist', 'Notas acuáticas con lilas blancas y algodón de azúcar', 65.00, 14, NULL, 1, 1, '2025-11-09 04:17:40', 0),
(6, 'Midnight Bloom Fragrance Mist', 'Aroma nocturno con gardenias y ciruelas negras', 92.90, 10, NULL, 1, 1, '2025-11-09 04:17:40', 0),
(7, 'Coconut Passion Fragrance Mist', 'Fragancia tropical con coco cremoso y piña', 78.90, 16, NULL, 1, 1, '2025-11-09 04:17:40', 0),
(8, 'Strawberries & Champagne Mist', 'Combinación dulce de fresas y champagne', 86.90, 13, NULL, 1, 1, '2025-11-09 04:17:40', 0),
(9, 'Amber Romance Fragrance Mist', 'Aroma sensual con ámbar y vainilla', 65.00, 9, NULL, 1, 1, '2025-11-09 04:17:40', 0),
(10, 'Endless Love Fragrance Mist', 'Fragancia romántica con flores blancas', 81.90, 17, NULL, 1, 1, '2025-11-09 04:17:40', 0),
(11, 'Sheer Love Fragrance Mist', 'Perfume ligero con rosas y peonías', 79.90, 19, NULL, 1, 1, '2025-11-09 04:17:40', 0),
(12, 'Velvet Petals Gold Mist', 'Edición premium con oro y pétalos de seda', 102.90, 8, NULL, 1, 1, '2025-11-09 04:17:40', 0),
(13, 'Bare Vanilla Body Lotion', 'Crema hidratante con aroma a vainilla sensual', 65.90, 22, NULL, 2, 1, '2025-11-09 04:17:40', 0),
(14, 'Velvet Petals Body Cream', 'Crema corporal sedosa con fragancia floral', 68.90, 18, NULL, 2, 1, '2025-11-09 04:17:40', 0),
(15, 'Pure Seduction Body Lotion', 'Hidratante con aroma frutal de ciruelas rojas', 62.90, 25, NULL, 2, 1, '2025-11-09 04:17:40', 0),
(16, 'Love Spell Moisturizing Lotion', 'Crema con fragancia de durazno y cereza', 64.90, 20, NULL, 2, 1, '2025-11-09 04:17:40', 0),
(17, 'Aqua Kiss Hydrating Lotion', 'Hidratante ligero con notas acuáticas', 66.90, 15, NULL, 2, 1, '2025-11-09 04:17:40', 0),
(18, 'Coconut Passion Body Butter', 'Mantequilla corporal con coco tropical', 72.90, 14, NULL, 2, 1, '2025-11-09 04:17:40', 0),
(19, 'Sheer Love Body Lotion', 'Crema ligera con aroma a rosas blancas', 59.90, 19, NULL, 2, 1, '2025-11-09 04:17:40', 0),
(20, 'Amber Romance Body Cream', 'Crema rica con notas ámbar y vainilla', 65.00, 14, '', 2, 1, '2025-11-09 04:17:40', 0),
(21, 'Endless Love Body Lotion', 'Hidratante con fragancia floral romántica', 61.90, 17, NULL, 2, 1, '2025-11-09 04:17:40', 0),
(22, 'Velvet Petals Shea Body Butter', 'Mantequilla corporal nutritiva con shea', 79.90, 11, NULL, 2, 1, '2025-11-09 04:17:40', 0),
(23, 'Midnight Bloom Body Cream', 'Crema nocturna con gardenias y ciruelas', 69.90, 13, NULL, 2, 1, '2025-11-09 04:17:40', 0),
(24, 'Coconut Passion Hydrating Lotion', 'Hidratante tropical con aceite de coco', 63.90, 21, NULL, 2, 1, '2025-11-09 04:17:40', 0),
(25, 'Bare Vanilla Hair Mist', 'Splash para cabello con aroma a vainilla', 55.90, 15, NULL, 3, 1, '2025-11-09 04:17:40', 0),
(26, 'Velvet Petals Hair Fragrance', 'Spray capilar con fragancia floral sedosa', 58.90, 12, NULL, 3, 1, '2025-11-09 04:17:40', 0),
(27, 'Pure Seduction Hair Mist', 'Splash con aroma frutal de ciruela y freesia', 52.90, 18, NULL, 3, 1, '2025-11-09 04:17:40', 0),
(28, 'Love Spell Hair & Body Mist', 'Spray versátil con fragancia de melocotón', 54.90, 16, NULL, 3, 1, '2025-11-09 04:17:40', 0),
(29, 'Aqua Kiss Refreshing Mist', 'Splash refrescante para cabello y cuerpo', 56.90, 14, NULL, 3, 1, '2025-11-09 04:17:40', 0),
(30, 'Coconut Passion Hair Shine', 'Spray para brillo y aroma tropical', 59.90, 13, NULL, 3, 1, '2025-11-09 04:17:40', 0),
(31, 'Midnight Bloom Hair Mist', 'Splash nocturno para cabello seductor', 62.90, 10, NULL, 3, 1, '2025-11-09 04:17:40', 0),
(32, 'Strawberries & Champagne Hair', 'Spray capilar con aroma dulce y festivo', 57.90, 11, NULL, 3, 1, '2025-11-09 04:17:40', 0),
(33, 'Amber Romance Hair Fragrance', 'Splash sensual con notas ámbar', 60.90, 8, NULL, 3, 1, '2025-11-09 04:17:40', 0),
(34, 'Endless Love Hair Mist', 'Spray romántico para cabello', 53.90, 17, NULL, 3, 1, '2025-11-09 04:17:40', 0),
(35, 'Sheer Love Refreshing Spray', 'Splash ligero con rosas y peonías', 51.90, 20, NULL, 3, 1, '2025-11-09 04:17:40', 0),
(36, 'Velvet Petals Luxury Hair Mist', 'Spray capilar premium con seda', 67.90, 8, NULL, 3, 1, '2025-11-09 04:17:40', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_promocion`
--

CREATE TABLE `productos_promocion` (
  `id` int(11) NOT NULL,
  `promocion_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos_promocion`
--

INSERT INTO `productos_promocion` (`id`, `promocion_id`, `producto_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 1, 6),
(7, 1, 7),
(8, 1, 8),
(9, 1, 9),
(10, 1, 10),
(11, 1, 11),
(12, 1, 12),
(13, 3, 13),
(14, 3, 14),
(15, 3, 15),
(16, 3, 16),
(17, 3, 17),
(18, 3, 18),
(19, 3, 19),
(20, 3, 20),
(21, 3, 21),
(22, 3, 22),
(23, 3, 23),
(24, 3, 24),
(25, 4, 1),
(26, 4, 13),
(27, 4, 25),
(28, 5, 14),
(29, 5, 18),
(30, 5, 20),
(31, 5, 21),
(32, 5, 22),
(33, 6, 1),
(34, 6, 2),
(35, 6, 3),
(36, 6, 4),
(37, 6, 5),
(38, 6, 6),
(39, 6, 7),
(40, 6, 8),
(41, 6, 9),
(42, 6, 10),
(43, 6, 11),
(44, 6, 12),
(45, 6, 25),
(46, 6, 26),
(47, 6, 27),
(48, 6, 28),
(49, 6, 29),
(50, 6, 30),
(51, 6, 31),
(52, 6, 32),
(53, 6, 33),
(54, 6, 34),
(55, 6, 35),
(56, 6, 36),
(57, 7, 1),
(60, 7, 2),
(63, 7, 4),
(66, 7, 5),
(69, 7, 7),
(58, 7, 13),
(61, 7, 14),
(64, 7, 16),
(67, 7, 17),
(70, 7, 18),
(59, 7, 25),
(62, 7, 26),
(65, 7, 28),
(68, 7, 29),
(71, 7, 30);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promociones`
--

CREATE TABLE `promociones` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('descuento_porcentaje','descuento_monto','combo','envio_gratis') NOT NULL,
  `valor_descuento` decimal(10,2) DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `activa` tinyint(1) DEFAULT 1,
  `max_usos` int(11) DEFAULT NULL,
  `usos_actual` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `promociones`
--

INSERT INTO `promociones` (`id`, `nombre`, `descripcion`, `tipo`, `valor_descuento`, `fecha_inicio`, `fecha_fin`, `activa`, `max_usos`, `usos_actual`, `created_at`) VALUES
(1, 'Descuento 20% Fragancias', '20% de descuento en todas las fragancias', 'descuento_porcentaje', 20.00, '2025-11-08', '2025-12-08', 1, 100, 0, '2025-11-09 04:17:40'),
(2, 'Envío Gratis +50€', 'Envío gratis en compras mayores a 50€', 'envio_gratis', NULL, '2025-11-08', '2026-01-07', 1, NULL, 0, '2025-11-09 04:17:40'),
(3, 'Combo Cremas', 'Combo especial de cremas corporales', 'combo', 15.00, '2025-11-08', '2025-11-23', 1, 50, 0, '2025-11-09 04:17:40'),
(4, 'Lanzamiento Bare Vanilla', '15% descuento en toda la línea Bare Vanilla', 'descuento_porcentaje', 15.00, '2025-11-08', '2025-12-23', 1, 80, 0, '2025-11-09 04:17:40'),
(5, 'Promo Cremas Nutritivas', '25% off en cremas y body butters', 'descuento_porcentaje', 25.00, '2025-11-08', '2025-11-28', 1, 60, 0, '2025-11-09 04:17:40'),
(6, 'Combo Fragancias + Splash', 'Compra 2 fragancias y lleva splash 50% off', 'combo', 50.00, '2025-11-08', '2025-12-08', 1, 40, 0, '2025-11-09 04:17:40'),
(7, 'Pack Aroma Completo', '20% descuento al llevar 3 productos de misma fragancia', 'descuento_porcentaje', 20.00, '2025-11-08', '2026-01-07', 1, NULL, 0, '2025-11-09 04:17:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('admin') DEFAULT 'admin',
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `correo`, `password_hash`, `rol`, `activo`, `created_at`) VALUES
(1, 'admin', 'admin@samyglow.com', '$2y$10$GzIkWAjAF3brrYORztWjyuLGEy2itHRzBGmcsdLhfV67D2LQeZtau', 'admin', 1, '2025-11-09 04:17:40'),
(2, 'samy', 'samy@samyglow.com', '0228c28ab7c9f1e777794499fbbef4316191568ce2b193b3f006ced9c389a21c', 'admin', 1, '2025-11-09 04:17:40');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD KEY `idx_dni` (`dni`),
  ADD KEY `idx_correo` (`correo`);

--
-- Indices de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `idx_pedido` (`pedido_id`);

--
-- Indices de la tabla `metodos_pago`
--
ALTER TABLE `metodos_pago`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `metodo_pago_id` (`metodo_pago_id`),
  ADD KEY `promocion_id` (`promocion_id`),
  ADD KEY `idx_cliente` (`cliente_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_fecha` (`fecha`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_categoria` (`categoria_id`),
  ADD KEY `idx_activo` (`activo`);

--
-- Indices de la tabla `productos_promocion`
--
ALTER TABLE `productos_promocion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_promocion_producto` (`promocion_id`,`producto_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `promociones`
--
ALTER TABLE `promociones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_fechas` (`fecha_inicio`,`fecha_fin`),
  ADD KEY `idx_activa` (`activa`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `metodos_pago`
--
ALTER TABLE `metodos_pago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `productos_promocion`
--
ALTER TABLE `productos_promocion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT de la tabla `promociones`
--
ALTER TABLE `promociones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD CONSTRAINT `detalle_pedido_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_pedido_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`metodo_pago_id`) REFERENCES `metodos_pago` (`id`),
  ADD CONSTRAINT `pedidos_ibfk_3` FOREIGN KEY (`promocion_id`) REFERENCES `promociones` (`id`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);

--
-- Filtros para la tabla `productos_promocion`
--
ALTER TABLE `productos_promocion`
  ADD CONSTRAINT `productos_promocion_ibfk_1` FOREIGN KEY (`promocion_id`) REFERENCES `promociones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `productos_promocion_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
