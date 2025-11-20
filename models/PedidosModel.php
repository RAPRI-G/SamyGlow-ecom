<?php
// models/PedidosModel.php
class Pedido {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Guarda un nuevo pedido en la base de datos
     * @param array $data Datos del pedido
     * @return array Resultado de la operación
     */
    public function guardar($data) {
        try {
            $this->pdo->beginTransaction();

            // 1. Validar stock de productos
            $stockValido = $this->validarStock($data['items']);
            if (!$stockValido['ok']) {
                return $stockValido;
            }

            // 2. Calcular totales
            $totales = $this->calcularTotales($data['items']);

            // 3. Insertar pedido principal
            $pedidoId = $this->insertarPedido($data, $totales);
            if (!$pedidoId) {
                throw new Exception("Error al crear el pedido principal");
            }

            // 4. Insertar detalles del pedido
            $this->insertarDetalles($pedidoId, $data['items']);

            // 5. Actualizar stock de productos
            $this->actualizarStock($data['items']);

            $this->pdo->commit();

            return [
                'ok' => true,
                'pedido_id' => $pedidoId,
                'message' => 'Pedido guardado exitosamente'
            ];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error en Pedido::guardar: " . $e->getMessage());
            return [
                'ok' => false,
                'error' => 'Error al procesar el pedido: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Valida que haya stock suficiente para todos los productos
     */
    private function validarStock($items) {
        foreach ($items as $item) {
            $sql = "SELECT nombre, stock FROM productos WHERE id = ? AND activo = 1 AND eliminado = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$item['producto_id']]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$producto) {
                return [
                    'ok' => false,
                    'error' => "El producto con ID {$item['producto_id']} no está disponible"
                ];
            }

            if ($producto['stock'] < $item['cantidad']) {
                return [
                    'ok' => false,
                    'error' => "Stock insuficiente para {$producto['nombre']}. Stock disponible: {$producto['stock']}"
                ];
            }
        }

        return ['ok' => true];
    }

    /**
     * Calcula los totales del pedido
     */
    private function calcularTotales($items) {
        $subtotal = 0;

        foreach ($items as $item) {
            $sql = "SELECT precio FROM productos WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$item['producto_id']]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($producto) {
                $itemSubtotal = $producto['precio'] * $item['cantidad'];
                $subtotal += $itemSubtotal;
            }
        }

        // Por ahora no aplicamos descuentos de promoción
        $descuento = 0;
        $total = $subtotal - $descuento;

        return [
            'subtotal' => $subtotal,
            'descuento' => $descuento,
            'total' => $total
        ];
    }

    /**
     * Inserta el pedido principal en la tabla pedidos
     */
    private function insertarPedido($data, $totales) {
        $sql = "INSERT INTO pedidos (
            cliente_id, 
            subtotal, 
            descuento_promocion, 
            total, 
            estado, 
            metodo_pago_id, 
            notas,
            fecha
        ) VALUES (?, ?, ?, ?, 'pendiente', ?, ?, NOW())";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['cliente'],
            $totales['subtotal'],
            $totales['descuento'],
            $totales['total'],
            $data['payment'],
            $data['notes'] ?? null
        ]);

        return $this->pdo->lastInsertId();
    }

    /**
     * Inserta los detalles del pedido
     */
    private function insertarDetalles($pedidoId, $items) {
        $sql = "INSERT INTO detalle_pedido (
            pedido_id, 
            producto_id, 
            cantidad, 
            precio_unitario, 
            subtotal
        ) VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->pdo->prepare($sql);

        foreach ($items as $item) {
            // Obtener precio actual del producto
            $precioSql = "SELECT precio FROM productos WHERE id = ?";
            $precioStmt = $this->pdo->prepare($precioSql);
            $precioStmt->execute([$item['producto_id']]);
            $producto = $precioStmt->fetch(PDO::FETCH_ASSOC);

            if ($producto) {
                $precioUnitario = $producto['precio'];
                $subtotal = $precioUnitario * $item['cantidad'];

                $stmt->execute([
                    $pedidoId,
                    $item['producto_id'],
                    $item['cantidad'],
                    $precioUnitario,
                    $subtotal
                ]);
            }
        }
    }

    /**
     * Actualiza el stock de los productos
     */
    private function actualizarStock($items) {
        $sql = "UPDATE productos SET stock = stock - ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);

        foreach ($items as $item) {
            $stmt->execute([
                $item['cantidad'],
                $item['producto_id']
            ]);
        }
    }

    /**
     * Obtiene los detalles de un pedido por ID
     */
    public function obtenerPorId($pedidoId) {
        $sql = "SELECT p.*, 
                       c.nombres as cliente_nombre, 
                       c.apellidos as cliente_apellido,
                       c.correo as cliente_email,
                       c.telefono as cliente_telefono,
                       mp.nombre as metodo_pago
                FROM pedidos p
                LEFT JOIN clientes c ON p.cliente_id = c.id
                LEFT JOIN metodos_pago mp ON p.metodo_pago_id = mp.id
                WHERE p.id = ? AND p.eliminado = 0";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$pedidoId]);
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($pedido) {
            // Obtener detalles del pedido
            $detallesSql = "SELECT dp.*, 
                                   pr.nombre as producto_nombre,
                                   pr.descripcion as producto_descripcion
                            FROM detalle_pedido dp
                            LEFT JOIN productos pr ON dp.producto_id = pr.id
                            WHERE dp.pedido_id = ?";
            
            $detallesStmt = $this->pdo->prepare($detallesSql);
            $detallesStmt->execute([$pedidoId]);
            $pedido['detalles'] = $detallesStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $pedido;
    }

    /**
     * Obtiene todos los pedidos de un cliente
     */
    public function obtenerPorCliente($clienteId) {
        $sql = "SELECT p.*, mp.nombre as metodo_pago
                FROM pedidos p
                LEFT JOIN metodos_pago mp ON p.metodo_pago_id = mp.id
                WHERE p.cliente_id = ? AND p.eliminado = 0
                ORDER BY p.fecha DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$clienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza el estado de un pedido
     */
    public function actualizarEstado($pedidoId, $estado) {
        $estadosValidos = ['pendiente', 'entregado'];
        
        if (!in_array($estado, $estadosValidos)) {
            return [
                'ok' => false,
                'error' => 'Estado no válido'
            ];
        }

        $sql = "UPDATE pedidos SET estado = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([$estado, $pedidoId]);

        return [
            'ok' => $result,
            'message' => $result ? 'Estado actualizado correctamente' : 'Error al actualizar estado'
        ];
    }
}
?>