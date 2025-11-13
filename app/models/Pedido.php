<?php
class Pedido
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * $data expected:
     * [
     *   "cliente" => int,   // cliente_id
     *   "items" => [ { producto_id: int, cantidad: int }, ... ],
     *   "payment" => int,   // metodo_pago_id
     *   "notes" => string
     * ]
     */
    public function guardar($data)
    {
        try {
            // Validaciones básicas
            if (!isset($data['cliente']) || !isset($data['items']) || !is_array($data['items']) || count($data['items']) === 0) {
                return ["ok" => false, "error" => "Datos incompletos"];
            }

            $this->pdo->beginTransaction();

            $subtotal = 0.0;
            $detalle_items = [];

            // Calcular precios desde DB y preparar detalle
            $stmtProd = $this->pdo->prepare("SELECT id, precio, stock, nombre FROM productos WHERE id = ? AND activo = 1 LIMIT 1");

            foreach ($data['items'] as $it) {
                $pid = intval($it['producto_id']);
                $qty = intval($it['cantidad']);
                if ($qty <= 0) {
                    $this->pdo->rollBack();
                    return ["ok" => false, "error" => "Cantidad inválida para producto $pid"];
                }

                $stmtProd->execute([$pid]);
                $prod = $stmtProd->fetch(PDO::FETCH_ASSOC);
                if (!$prod) {
                    $this->pdo->rollBack();
                    return ["ok" => false, "error" => "Producto no encontrado ($pid)"];
                }

                if ($prod['stock'] < $qty) {
                    $this->pdo->rollBack();
                    return ["ok" => false, "error" => "Stock insuficiente para {$prod['nombre']} (ID {$pid})"];
                }

                $precio_unit = floatval($prod['precio']);
                $subtotal_item = $precio_unit * $qty;
                $subtotal += $subtotal_item;

                $detalle_items[] = [
                    'producto_id' => $pid,
                    'cantidad' => $qty,
                    'precio_unitario' => $precio_unit,
                    'subtotal' => $subtotal_item
                ];
            }

            // Por simplicidad: no aplicamos promociones en backend (la UI lo hizo / o solo existe 1 promo)
            // Si necesitas aplicar la promo combo en backend, se puede añadir aquí.
            $descuento = 0.0;
            $total = $subtotal - $descuento;

            // Insertar pedido
            $sql = "INSERT INTO pedidos (cliente_id, subtotal, descuento_promocion, total, metodo_pago_id, notas)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                intval($data['cliente']),
                number_format($subtotal, 2, '.', ''),
                number_format($descuento, 2, '.', ''),
                number_format($total, 2, '.', ''),
                intval($data['payment']),
                isset($data['notes']) ? $data['notes'] : null
            ]);

            $pedidoId = $this->pdo->lastInsertId();

            // Insertar detalle y descontar stock
            $insDet = $this->pdo->prepare("INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
            $updStock = $this->pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");

            foreach ($detalle_items as $d) {
                $insDet->execute([
                    $pedidoId,
                    intval($d['producto_id']),
                    intval($d['cantidad']),
                    number_format($d['precio_unitario'], 2, '.', ''),
                    number_format($d['subtotal'], 2, '.', '')
                ]);
                $updStock->execute([intval($d['cantidad']), intval($d['producto_id'])]);
            }

            $this->pdo->commit();
            return ["ok" => true, "pedido_id" => $pedidoId];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ["ok" => false, "error" => $e->getMessage()];
        }
    }

    // 🔹 VERIFICAR SI CLIENTE TIENE PEDIDOS
    public function clienteTienePedidos($clienteId)
    {
        $sql = "SELECT COUNT(*) as total FROM pedidos WHERE cliente_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$clienteId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }

    // 🔹 OBTENER ESTADÍSTICAS DE CLIENTE
    public function obtenerEstadisticasCliente($clienteId)
    {
        $sql = "SELECT 
            COUNT(*) as total_pedidos,
            COALESCE(SUM(total), 0) as total_gastado,
            COALESCE(AVG(total), 0) as promedio_pedido,
            MIN(fecha) as primer_pedido,
            MAX(fecha) as ultimo_pedido
            FROM pedidos 
            WHERE cliente_id = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$clienteId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔹 OBTENER PEDIDOS POR CLIENTE
    public function obtenerPorCliente($clienteId)
    {
        $sql = "SELECT p.*, 
                   mp.nombre as metodo_pago,
                   COUNT(dp.id) as total_items,
                   GROUP_CONCAT(pr.nombre SEPARATOR ', ') as productos
            FROM pedidos p
            LEFT JOIN metodos_pago mp ON p.metodo_pago_id = mp.id
            LEFT JOIN detalle_pedido dp ON p.id = dp.pedido_id
            LEFT JOIN productos pr ON dp.producto_id = pr.id
            WHERE p.cliente_id = ?
            GROUP BY p.id
            ORDER BY p.fecha DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$clienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
