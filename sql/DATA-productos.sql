-- =======================================================
-- DATOS INICIALES COMPLETOS
-- =======================================================

-- Usuario administrador
INSERT INTO usuarios (username, correo, password_hash) VALUES 
('admin', 'admin@samyglow.com', SHA2('admin123', 256)),
('samy', 'samy@samyglow.com', SHA2('samy2024', 256));

-- Categorías Victoria Secret
INSERT INTO categorias (nombre) VALUES 
('Fragancias'),
('Cremas Corporales'),
('Body Splash');

-- Métodos de pago
INSERT INTO metodos_pago (nombre) VALUES 
('Yape'),
('Plin'),
('Tarjeta de Crédito'),
('Transferencia Bancaria'),
('Efectivo');

-- =======================================================
-- PRODUCTOS VICTORIA SECRET - F R A G A N C I A S
-- =======================================================
INSERT INTO productos (nombre, descripcion, precio, stock, categoria_id) VALUES
('Bare Vanilla Fragrance Mist', 'Fragancia dulce y sensual con notas de vainilla y crema', 95.90, 18, 1),
('Velvet Petals Fragrance Mist', 'Aroma floral con notas de pétalos de seda y almizcle', 88.90, 12, 1),
('Pure Seduction Fragrance Mist', 'Fragancia frutal con ciruelas rojas y fresias', 82.90, 15, 1),
('Love Spell Fragrance Mist', 'Perfume con durazno, cereza y flor de melocotón', 85.90, 20, 1),
('Aqua Kiss Fragrance Mist', 'Notas acuáticas con lilas blancas y algodón de azúcar', 89.90, 14, 1),
('Midnight Bloom Fragrance Mist', 'Aroma nocturno con gardenias y ciruelas negras', 92.90, 10, 1),
('Coconut Passion Fragrance Mist', 'Fragancia tropical con coco cremoso y piña', 78.90, 16, 1),
('Strawberries & Champagne Mist', 'Combinación dulce de fresas y champagne', 86.90, 13, 1),
('Amber Romance Fragrance Mist', 'Aroma sensual con ámbar y vainilla', 94.90, 11, 1),
('Endless Love Fragrance Mist', 'Fragancia romántica con flores blancas', 81.90, 17, 1),
('Sheer Love Fragrance Mist', 'Perfume ligero con rosas y peonías', 79.90, 19, 1),
('Velvet Petals Gold Mist', 'Edición premium con oro y pétalos de seda', 102.90, 8, 1),
('Temptation Fragrance Mist', 'Fragancia cálida con notas florales y manzana', 87.90, 15, 1),
('Rush Fragrance Mist', 'Aroma floral intenso con ámbar y clementina', 91.90, 10, 1),
('Romantic Fragrance Mist', 'Perfume suave con pétalos rosados y freesia', 83.90, 17, 1),
('Secret Charm Fragrance Mist', 'Aroma fresco con madreselva y jazmín', 80.90, 19, 1),
('Electric Summer Fragrance Mist', 'Edición limitada vibrante con frutas exóticas', 99.90, 7, 1),
('Glam Shine Fragrance Mist', 'Fragancia con brillo y notas de frutos rojos', 96.90, 12, 1),
('Temptation Shimmer Mist', 'Splash con brillo y aroma a manzana y flor', 90.90, 11, 1),
('Rush Shimmer Mist', 'Splash con brillo intenso con ámbar y clementina', 93.90, 9, 1),
('Passion Struck Fragrance Mist', 'Aroma profundo con manzana fuji y orquídea', 84.90, 14, 1),
('Forbidden Fragrance Mist', 'Mezcla misteriosa de vainilla y ciruela', 98.90, 6, 1),
('Sun Kissed Fragrance Mist', 'Fragancia veraniega con notas cítricas y sal marina', 88.90, 13, 1),
('Gilded Amber Fragrance Mist', 'Edición de lujo con ámbar dorado y sándalo', 105.90, 5, 1);

-- =======================================================
-- PRODUCTOS VICTORIA SECRET - C R E M A S  C O R P O R A L E S
-- =======================================================
INSERT INTO productos (nombre, descripcion, precio, stock, categoria_id) VALUES
('Bare Vanilla Body Lotion', 'Crema hidratante con aroma a vainilla sensual', 65.90, 22, 2),
('Velvet Petals Body Cream', 'Crema corporal sedosa con fragancia floral', 68.90, 18, 2),
('Pure Seduction Body Lotion', 'Hidratante con aroma frutal de ciruelas rojas', 62.90, 25, 2),
('Love Spell Moisturizing Lotion', 'Crema con fragancia de durazno y cereza', 64.90, 20, 2),
('Aqua Kiss Hydrating Lotion', 'Hidratante ligero con notas acuáticas', 66.90, 16, 2),
('Coconut Passion Body Butter', 'Mantequilla corporal con coco tropical', 72.90, 14, 2),
('Sheer Love Body Lotion', 'Crema ligera con aroma a rosas blancas', 59.90, 19, 2),
('Amber Romance Body Cream', 'Crema rica con notas ámbar y vainilla', 75.90, 12, 2),
('Endless Love Body Lotion', 'Hidratante con fragancia floral romántica', 61.90, 17, 2),
('Velvet Petals Shea Body Butter', 'Mantequilla corporal nutritiva con shea', 79.90, 11, 2),
('Midnight Bloom Body Cream', 'Crema nocturna con gardenias y ciruelas', 69.90, 13, 2),
('Coconut Passion Hydrating Lotion', 'Hidratante tropical con aceite de coco', 63.90, 21, 2),
('Temptation Body Lotion', 'Crema hidratante con aroma a manzana y flores', 67.90, 20, 2),
('Rush Body Cream', 'Crema corporal rica con ámbar y clementina', 71.90, 15, 2),
('Romantic Body Lotion', 'Hidratante suave con pétalos rosados y freesia', 63.90, 23, 2),
('Secret Charm Body Lotion', 'Crema ligera con madreselva y jazmín', 60.90, 25, 2),
('Electric Summer Body Cream', 'Crema edición limitada con aceite nutritivo', 79.90, 10, 2),
('Glam Shine Body Lotion', 'Loción hidratante con destellos y frutos rojos', 76.90, 18, 2),
('Temptation Body Butter', 'Mantequilla corporal con manzana para hidratación profunda', 70.90, 16, 2),
('Rush Body Butter', 'Mantequilla rica con aroma a ámbar y clementina', 73.90, 14, 2),
('Passion Struck Body Lotion', 'Crema con manzana fuji y orquídea', 64.90, 19, 2),
('Forbidden Body Cream', 'Crema misteriosa de vainilla y ciruela negra', 78.90, 9, 2),
('Sun Kissed Body Lotion', 'Hidratante refrescante con notas cítricas', 68.90, 17, 2),
('Gilded Amber Body Cream', 'Crema de lujo con ámbar dorado y sándalo', 85.90, 8, 2);

-- =======================================================
-- PRODUCTOS VICTORIA SECRET - B O D Y  S P L A S H
-- =======================================================
INSERT INTO productos (nombre, descripcion, precio, stock, categoria_id) VALUES
('Bare Vanilla Hair Mist', 'Splash para cabello con aroma a vainilla', 55.90, 15, 3),
('Velvet Petals Hair Fragrance', 'Spray capilar con fragancia floral sedosa', 58.90, 12, 3),
('Pure Seduction Hair Mist', 'Splash con aroma frutal de ciruela y freesia', 52.90, 18, 3),
('Love Spell Hair & Body Mist', 'Spray versátil con fragancia de melocotón', 54.90, 16, 3),
('Aqua Kiss Refreshing Mist', 'Splash refrescante para cabello y cuerpo', 56.90, 14, 3),
('Coconut Passion Hair Shine', 'Spray para brillo y aroma tropical', 59.90, 13, 3),
('Midnight Bloom Hair Mist', 'Splash nocturno para cabello seductor', 62.90, 10, 3),
('Strawberries & Champagne Hair', 'Spray capilar con aroma dulce y festivo', 57.90, 11, 3),
('Amber Romance Hair Fragrance', 'Splash sensual con notas ámbar', 60.90, 9, 3),
('Endless Love Hair Mist', 'Spray romántico para cabello', 53.90, 17, 3),
('Sheer Love Refreshing Spray', 'Splash ligero con rosas y peonías', 51.90, 20, 3),
('Velvet Petals Luxury Hair Mist', 'Spray capilar premium con seda', 67.90, 8, 3),
('Temptation Refreshing Mist', 'Splash refrescante para el cuerpo y cabello', 57.90, 18, 3),
('Rush Conditioning Mist', 'Splash acondicionador con ámbar y clementina', 61.90, 13, 3),
('Romantic Hair & Body Mist', 'Spray versátil con pétalos rosados', 53.90, 21, 3),
('Secret Charm Refreshing Spray', 'Splash ligero con madreselva y jazmín', 50.90, 24, 3),
('Electric Summer Body Mist', 'Splash vibrante de verano, edición limitada', 65.90, 11, 3),
('Glam Shine Hair Mist', 'Spray capilar con brillo y aroma a frutos rojos', 64.90, 16, 3),
('Passion Struck Hair Mist', 'Splash para el cabello con manzana fuji y orquídea', 58.90, 15, 3),
('Forbidden Shimmer Mist', 'Splash con brillo de vainilla y ciruela', 68.90, 10, 3),
('Sun Kissed Body Mist', 'Spray corporal veraniego con sal marina', 62.90, 14, 3),
('Gilded Amber Hair Mist', 'Spray capilar de lujo con ámbar dorado', 71.90, 9, 3),
('Soft & Dreamy Hair Mist', 'Splash ligero con lavanda y coco', 56.90, 19, 3),
('Wicked Fragrance Body Mist', 'Fragancia intensa y seductora con notas oscuras', 69.90, 7, 3);

-- =======================================================
-- PROMOCIONES COMPLETAS
-- =======================================================
INSERT INTO promociones (nombre, descripcion, tipo, valor_descuento, fecha_inicio, fecha_fin, max_usos) VALUES
('Descuento 20% Fragancias', '20% de descuento en todas las fragancias', 'descuento_porcentaje', 20.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 100),
('Envío Gratis +50€', 'Envío gratis en compras mayores a 50€', 'envio_gratis', NULL, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 60 DAY), NULL),
('Combo Cremas', 'Combo especial de cremas corporales', 'combo', 15.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 15 DAY), 50),
('Lanzamiento Bare Vanilla', '15% descuento en toda la línea Bare Vanilla', 'descuento_porcentaje', 15.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 45 DAY), 80),
('Promo Cremas Nutritivas', '25% off en cremas y body butters', 'descuento_porcentaje', 25.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 20 DAY), 60),
('Combo Fragancias + Splash', 'Compra 2 fragancias y lleva splash 50% off', 'combo', 50.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 40),
('Pack Aroma Completo', '20% descuento al llevar 3 productos de misma fragancia', 'descuento_porcentaje', 20.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 60 DAY), NULL);

-- =======================================================
-- ASIGNACIÓN COMPLETA DE PRODUCTOS A PROMOCIONES
-- =======================================================

-- Promoción 1: Descuento 20% Fragancias
INSERT INTO productos_promocion (promocion_id, producto_id) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6), (1, 7), (1, 8), (1, 9), (1, 10), (1, 11), (1, 12);

-- Promoción 3: Combo Cremas
INSERT INTO productos_promocion (promocion_id, producto_id) VALUES
(3, 13), (3, 14), (3, 15), (3, 16), (3, 17), (3, 18), (3, 19), (3, 20), (3, 21), (3, 22), (3, 23), (3, 24);

-- Promoción 4: Lanzamiento Bare Vanilla
INSERT INTO productos_promocion (promocion_id, producto_id) VALUES
(4, 1), (4, 13), (4, 25);

-- Promoción 5: Cremas Nutritivas
INSERT INTO productos_promocion (promocion_id, producto_id) VALUES
(5, 14), (5, 18), (5, 20), (5, 21), (5, 22);

-- Promoción 6: Combo Fragancias + Splash
INSERT INTO productos_promocion (promocion_id, producto_id) VALUES
(6, 1), (6, 2), (6, 3), (6, 4), (6, 5), (6, 6), (6, 7), (6, 8), (6, 9), (6, 10), (6, 11), (6, 12),
(6, 25), (6, 26), (6, 27), (6, 28), (6, 29), (6, 30), (6, 31), (6, 32), (6, 33), (6, 34), (6, 35), (6, 36);

-- Promoción 7: Pack Aroma Completo
INSERT INTO productos_promocion (promocion_id, producto_id) VALUES
(7, 1), (7, 13), (7, 25),   -- Bare Vanilla
(7, 2), (7, 14), (7, 26),   -- Velvet Petals
(7, 4), (7, 16), (7, 28),   -- Love Spell
(7, 5), (7, 17), (7, 29),   -- Aqua Kiss
(7, 7), (7, 18), (7, 30);   -- Coconut Passion

-- =======================================================
-- CLIENTES DE EJEMPLO
-- =======================================================
INSERT INTO clientes (nombres, apellidos, dni, correo, telefono) VALUES
('María', 'Gonzales López', '23456789', 'maria.gonzales@email.com', '912345678'),
('Carlos', 'Rodríguez Paz', '34567890', 'carlos.rodriguez@email.com', '923456789'),
('Ana', 'Martínez Soto', '45678901', 'ana.martinez@email.com', '934567890'),
('Luis', 'Torres Mendoza', '56789012', 'luis.torres@email.com', '945678901'),
('Sofia', 'Ramírez Cruz', '67890123', 'sofia.ramirez@email.com', '956789012'),
('Jorge', 'Díaz Herrera', '78901234', 'jorge.diaz@email.com', '967890123'),
('Elena', 'Castillo Rojas', '89012345', 'elena.castillo@email.com', '978901234'),
('Miguel', 'Vargas Fuentes', '90123456', 'miguel.vargas@email.com', '989012345'),
('Andrea', 'Sánchez Pérez', '10111213', 'andrea.sanchez@email.com', '990112233'),
('Javier', 'Gómez Castro', '14151617', 'javier.gomez@email.com', '981223344'),
('Laura', 'Flores Silva', '18192021', 'laura.flores@email.com', '972334455'),
('Ricardo', 'Rojas Chávez', '22232425', 'ricardo.rojas@email.com', '963445566'),
('Valeria', 'Huamán Lino', '26272829', 'valeria.huaman@email.com', '954556677'),
('Daniel', 'Quiroz Ayala', '30313233', 'daniel.quiroz@email.com', '945667788'),
('Patricia', 'Céspedes Luna', '34353637', 'patricia.cespedes@email.com', '936778899'),
('Héctor', 'Vega Bravo', '38394041', 'hector.vega@email.com', '927889900'),
('Rosa', 'Molina Salas', '42434445', 'rosa.molina@email.com', '918990011'),
('Enrique', 'Paredes Vivas', '46474849', 'enrique.paredes@email.com', '999001122'),
('Jessica', 'Bustamante Rey', '50515253', 'jessica.bustamante@email.com', '980112200'),
('Fernando', 'Acosta Leal', '54555657', 'fernando.acosta@email.com', '971223311'),
('Gabriela', 'Núñez Ríos', '58596061', 'gabriela.nunez@email.com', '962334422'),
('Alejandro', 'Lozano Cano', '62636465', 'alejandro.lozano@email.com', '953445533'),
('Silvia', 'Herrera Vidal', '66676869', 'silvia.herrera@email.com', '944556644'),
('Roberto', 'Cárdenas Tello', '70717273', 'roberto.cardenas@email.com', '935667755'),
('Diana', 'Gutiérrez León', '74757677', 'diana.gutierrez@email.com', '926778866'),
('Felipe', 'Peña Sotelo', '78798081', 'felipe.pena@email.com', '917889977'),
('Karla', 'Arias Zúñiga', '82838485', 'karla.arias@email.com', '908990088'),
('Manuel', 'Cano Benites', '86878889', 'manuel.cano@email.com', '999001199'),
('Teresa', 'Aguilar Ramos', '90919293', 'teresa.aguilar@email.com', '980112210'),
('Óscar', 'Mendoza Poma', '94959697', 'oscar.mendoza@email.com', '971223321'),
('Isabel', 'Sosa Salazar', '98990001', 'isabel.sosa@email.com', '962334432'),
('Julio', 'Rivas Flores', '02030405', 'julio.rivas@email.com', '953445543'),
('Claudia', 'León Quispe', '06070809', 'claudia.leon@email.com', '944556654'),
('Sebastián', 'Vásquez Palma', '11121314', 'sebastian.vasquez@email.com', '935667765'),
('Mónica', 'Chávez Lira', '15161718', 'monica.chavez@email.com', '926778876'),
('Esteban', 'Pérez Merino', '19202122', 'esteban.perez@email.com', '917889987'),
('Marcela', 'Ríos Cueva', '23242526', 'marcela.rios@email.com', '908990098'),
('Aldo', 'Cruz Salazar', '27282930', 'aldo.cruz@email.com', '999001109'),
('Vanessa', 'Montoya Gil', '31323334', 'vanessa.montoya@email.com', '980112219'),
('Guillermo', 'Salas Rivera', '35363738', 'guillermo.salas@email.com', '971223320'),
('Lucía', 'Paz Herrera', '39404142', 'lucia.paz@email.com', '962334431'),
('Alonso', 'Díaz Soto', '43444546', 'alonso.diaz@email.com', '953445542'),
('Pilar', 'Mendoza Solís', '47484950', 'pilar.mendoza@email.com', '944556653'),
('Giovanni', 'Benites Castro', '51525354', 'giovanni.benites@email.com', '935667764'),
('Natalia', 'Vargas Torres', '55565758', 'natalia.vargas@email.com', '926778875'),
('Renzo', 'Herrera Vega', '59606162', 'renzo.herrera@email.com', '917889986'),
('Adriana', 'Castañeda Luna', '63646566', 'adriana.castaneda@email.com', '908990097'),
('Jairo', 'Soto Ríos', '67686970', 'jairo.soto@email.com', '999001108'),
('Elsa', 'Ramos Aguilar', '71727374', 'elsa.ramos@email.com', '980112218'),
('Mario', 'Silva Leal', '75767778', 'mario.silva@email.com', '971223329');

-- =======================================================
-- PEDIDOS DE EJEMPLO
-- =======================================================
INSERT INTO pedidos (cliente_id, subtotal, descuento_promocion, total, metodo_pago_id, promocion_id, estado) VALUES
(1, 185.70, 27.85, 157.85, 1, 4, 'entregado'),
(2, 240.50, 0, 240.50, 2, NULL, 'pendiente'),
(3, 156.80, 39.20, 117.60, 1, 5, 'entregado'),
(4, 320.90, 64.18, 256.72, 3, 7, 'pendiente'),
(5, 178.60, 35.72, 142.88, 4, 1, 'entregado'),
(6, 295.40, 0, 295.40, 2, NULL, 'pendiente'),
(7, 210.70, 52.67, 158.03, 1, 5, 'entregado'),
(8, 189.50, 0, 189.50, 5, NULL, 'pendiente');

-- =======================================================
-- DETALLES DE PEDIDOS
-- =======================================================
INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio_unitario, subtotal) VALUES
-- Pedido 1 (Bare Vanilla)
(1, 1, 1, 95.90, 95.90), (1, 13, 1, 65.90, 65.90), (1, 25, 1, 55.90, 55.90),

-- Pedido 2 (Sin promoción)
(2, 3, 2, 82.90, 165.80), (2, 17, 1, 59.90, 59.90), (2, 30, 1, 59.90, 59.90),

-- Pedido 3 (Cremas nutritivas)
(3, 18, 2, 75.90, 151.80), (3, 22, 1, 79.90, 79.90),

-- Pedido 4 (Pack aroma completo)
(4, 2, 1, 88.90, 88.90), (4, 14, 1, 68.90, 68.90), (4, 26, 1, 58.90, 58.90),
(4, 5, 1, 89.90, 89.90), (4, 17, 1, 66.90, 66.90),

-- Pedido 5 (Fragancias 20%)
(5, 6, 1, 92.90, 92.90), (5, 8, 1, 86.90, 86.90),

-- Pedido 6 (Sin promoción)
(6, 12, 1, 102.90, 102.90), (6, 20, 1, 79.90, 79.90), (6, 36, 1, 67.90, 67.90),

-- Pedido 7 (Cremas nutritivas)
(7, 14, 1, 68.90, 68.90), (7, 18, 1, 75.90, 75.90), (7, 22, 1, 79.90, 79.90),

-- Pedido 8 (Sin promoción)
(8, 4, 1, 85.90, 85.90), (8, 28, 1, 54.90, 54.90), (8, 16, 1, 64.90, 64.90);
