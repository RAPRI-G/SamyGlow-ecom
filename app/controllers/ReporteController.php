<?php
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Categoria.php';

class ReporteController {
  private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }
    

    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?view=login");
            exit;
        }

        $title = "Reportes & Analytics - SamyGlow";
        $pageTitle = "Reportes & Analytics";

         // Obtener datos iniciales para filtros
        global $pdo;
        $stmt = $pdo->query("SELECT id, nombre FROM categorias WHERE activa = 1");
        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/templates/header.php';
        require_once __DIR__ . '/../views/admin/reportes-analytics.php';
        require_once __DIR__ . '/../views/templates/footer.php';
    }

    public function reporteVentas() {
    // Establecer headers ANTES de cualquier output
    header('Content-Type: application/json');
    
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['usuario'])) {
        echo json_encode(['success' => false, 'error' => 'No autorizado']);
        exit;
    }

    try {
        // Obtener parámetros con valores por defecto
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-t');
        $categoria_id = $_GET['categoria_id'] ?? 'todos';
        $metodo_pago_id = $_GET['metodo_pago_id'] ?? 'todos';

        // Validar fechas
        if (!strtotime($fecha_inicio) || !strtotime($fecha_fin)) {
            throw new Exception('Fechas inválidas');
        }

        // Obtener pedidos filtrados
        $pedidos = $this->obtenerPedidosFiltrados($fecha_inicio, $fecha_fin, $categoria_id, $metodo_pago_id);
        
        // Calcular métricas
        $metrics = $this->calcularMetricasVentas($pedidos);
        
        // Ventas por día
        $ventas_por_dia = $this->obtenerVentasPorDia($fecha_inicio, $fecha_fin, $categoria_id, $metodo_pago_id);
        
        // Ventas por categoría
        $ventas_por_categoria = $this->obtenerVentasPorCategoria($fecha_inicio, $fecha_fin, $metodo_pago_id);

        // Enviar respuesta JSON
        echo json_encode([
            'success' => true,
            'data' => [
                'pedidos' => $pedidos,
                'metrics' => $metrics,
                'ventas_por_dia' => $ventas_por_dia,
                'ventas_por_categoria' => $ventas_por_categoria
            ]
        ]);

    } catch (Exception $e) {
        // Log del error para debugging
        error_log("Error en reporteVentas: " . $e->getMessage());
        
        echo json_encode([
            'success' => false,
            'error' => 'Error interno del servidor: ' . $e->getMessage()
        ]);
    }
    exit;
}

    public function reporteProductos() {
    header('Content-Type: application/json');
    
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['usuario'])) {
        echo json_encode(['success' => false, 'error' => 'No autorizado']);
        exit;
    }

    try {
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-t');
        $categoria_id = $_GET['categoria_id'] ?? 'todos';

        // Validar fechas
        if (!strtotime($fecha_inicio) || !strtotime($fecha_fin)) {
            throw new Exception('Fechas inválidas');
        }

        // Obtener productos vendidos
        $productos_vendidos = $this->obtenerProductosVendidos($fecha_inicio, $fecha_fin, $categoria_id);
        
        // Calcular métricas
        $metrics = $this->calcularMetricasProductos($productos_vendidos);
        
        // Obtener top 10 productos
        $top_productos = $this->obtenerTopProductos($productos_vendidos, 10);
        
        // Distribución por categoría
        $distribucion_categoria = $this->calcularDistribucionCategoria($productos_vendidos);
        
        // Productos con stock bajo (para alertas)
        $productos_stock_bajo = $this->obtenerProductosConStockBajo();

        echo json_encode([
            'success' => true,
            'data' => [
                'productos' => $productos_vendidos,
                'metrics' => $metrics,
                'top_productos' => $top_productos,
                'distribucion_categoria' => $distribucion_categoria,
                'productos_stock_bajo' => $productos_stock_bajo
            ]
        ]);

    } catch (Exception $e) {
        error_log("Error en reporteProductos: " . $e->getMessage());
        
        echo json_encode([
            'success' => false,
            'error' => 'Error interno del servidor: ' . $e->getMessage()
        ]);
    }
    exit;
}

     public function reporteInventario() {
        header('Content-Type: application/json');
        
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario'])) {
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            exit;
        }

        try {
            $categoria_id = $_GET['categoria_id'] ?? 'todos';
            $estado_stock = $_GET['estado_stock'] ?? 'todos';

            $productos = $this->obtenerProductosInventario($categoria_id, $estado_stock);
            $metrics = $this->calcularMetricasInventario($productos);
            $inventario_por_categoria = $this->calcularInventarioPorCategoria($productos);
            $estado_stock_data = $this->calcularEstadoStock($productos);

            echo json_encode([
                'success' => true,
                'data' => [
                    'productos' => $productos,
                    'metrics' => $metrics,
                    'inventario_por_categoria' => $inventario_por_categoria,
                    'estado_stock_data' => $estado_stock_data
                ]
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    // =========================================================================
    // MÉTODOS PRIVADOS PARA REPORTE DE VENTAS
    // =========================================================================

    private function obtenerPedidosFiltrados($fecha_inicio, $fecha_fin, $categoria_id, $metodo_pago_id) {
        $sql = "SELECT p.*, 
                       CONCAT(c.nombres, ' ', c.apellidos) as cliente_nombre,
                       mp.nombre as metodo_pago_nombre
                FROM pedidos p
                LEFT JOIN clientes c ON p.cliente_id = c.id
                LEFT JOIN metodos_pago mp ON p.metodo_pago_id = mp.id
                WHERE DATE(p.fecha) BETWEEN :fecha_inicio AND :fecha_fin
                AND p.eliminado = 0";

        $params = [
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin
        ];

        // Aplicar filtro de categoría
        if ($categoria_id !== 'todos') {
            $sql .= " AND EXISTS (
                SELECT 1 FROM detalle_pedido dp 
                JOIN productos prod ON dp.producto_id = prod.id 
                WHERE dp.pedido_id = p.id AND prod.categoria_id = :categoria_id
            )";
            $params['categoria_id'] = $categoria_id;
        }

        // Aplicar filtro de método de pago
        if ($metodo_pago_id !== 'todos') {
            $sql .= " AND p.metodo_pago_id = :metodo_pago_id";
            $params['metodo_pago_id'] = $metodo_pago_id;
        }

        $sql .= " ORDER BY p.fecha DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function calcularMetricasVentas($pedidos) {
        $total_ventas = 0;
        $total_pedidos = count($pedidos);
        $pedidos_entregados = 0;

        foreach ($pedidos as $pedido) {
            $total_ventas += floatval($pedido['total']);
            if ($pedido['estado'] === 'entregado') {
                $pedidos_entregados++;
            }
        }

        $ticket_promedio = $total_pedidos > 0 ? $total_ventas / $total_pedidos : 0;

        return [
            'ventas_totales' => $total_ventas,
            'total_pedidos' => $total_pedidos,
            'ticket_promedio' => $ticket_promedio,
            'pedidos_entregados' => $pedidos_entregados
        ];
    }

    private function obtenerVentasPorDia($fecha_inicio, $fecha_fin, $categoria_id, $metodo_pago_id) {
        $sql = "SELECT 
            DATE(p.fecha) as fecha,
            SUM(p.total) as total_ventas,
            COUNT(*) as cantidad_pedidos
        FROM pedidos p
        WHERE DATE(p.fecha) BETWEEN :fecha_inicio AND :fecha_fin
        AND p.eliminado = 0";

        $params = [
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin
        ];

        // Aplicar filtros
        if ($categoria_id !== 'todos') {
            $sql .= " AND EXISTS (
                SELECT 1 FROM detalle_pedido dp 
                JOIN productos prod ON dp.producto_id = prod.id 
                WHERE dp.pedido_id = p.id AND prod.categoria_id = :categoria_id
            )";
            $params['categoria_id'] = $categoria_id;
        }

        if ($metodo_pago_id !== 'todos') {
            $sql .= " AND p.metodo_pago_id = :metodo_pago_id";
            $params['metodo_pago_id'] = $metodo_pago_id;
        }

        $sql .= " GROUP BY DATE(p.fecha) ORDER BY fecha";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function obtenerVentasPorCategoria($fecha_inicio, $fecha_fin, $metodo_pago_id) {
        $sql = "SELECT 
            c.nombre as categoria,
            SUM(dp.subtotal) as total_ventas,
            COUNT(DISTINCT p.id) as pedidos,
            SUM(dp.cantidad) as unidades_vendidas
        FROM detalle_pedido dp
        INNER JOIN pedidos p ON dp.pedido_id = p.id
        INNER JOIN productos prod ON dp.producto_id = prod.id
        INNER JOIN categorias c ON prod.categoria_id = c.id
        WHERE DATE(p.fecha) BETWEEN :fecha_inicio AND :fecha_fin
        AND p.eliminado = 0";

        $params = [
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin
        ];

        if ($metodo_pago_id !== 'todos') {
            $sql .= " AND p.metodo_pago_id = :metodo_pago_id";
            $params['metodo_pago_id'] = $metodo_pago_id;
        }

        $sql .= " GROUP BY c.id, c.nombre ORDER BY total_ventas DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================================
    // MÉTODOS PARA LOS OTROS REPORTES (SIMPLIFICADOS POR AHORA)
    // =========================================================================

    private function obtenerProductosVendidos($fecha_inicio, $fecha_fin, $categoria_id) {
    $sql = "SELECT 
        prod.id,
        prod.nombre,
        c.nombre as categoria_nombre,
        SUM(dp.cantidad) as cantidad_vendida,
        SUM(dp.subtotal) as ingresos_totales,
        prod.stock as stock_actual,
        prod.precio,
        prod.imagen
    FROM detalle_pedido dp
    INNER JOIN pedidos p ON dp.pedido_id = p.id
    INNER JOIN productos prod ON dp.producto_id = prod.id
    INNER JOIN categorias c ON prod.categoria_id = c.id
    WHERE DATE(p.fecha) BETWEEN :fecha_inicio AND :fecha_fin
    AND p.eliminado = 0
    AND prod.eliminado = 0";

    $params = [
        'fecha_inicio' => $fecha_inicio,
        'fecha_fin' => $fecha_fin
    ];

    if ($categoria_id !== 'todos') {
        $sql .= " AND prod.categoria_id = :categoria_id";
        $params['categoria_id'] = $categoria_id;
    }

    $sql .= " GROUP BY prod.id, prod.nombre, c.nombre, prod.stock, prod.precio, prod.imagen
             ORDER BY cantidad_vendida DESC";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    private function calcularMetricasProductos($productos) {
    $total_unidades = 0;
    $total_productos = count($productos);
    $producto_mas_caro = 0;
    $nombre_producto_caro = '';
    $ingresos_totales = 0;

    foreach ($productos as $producto) {
        $total_unidades += intval($producto['cantidad_vendida']);
        $ingresos_totales += floatval($producto['ingresos_totales']);
        
        if ($producto['precio'] > $producto_mas_caro) {
            $producto_mas_caro = $producto['precio'];
            $nombre_producto_caro = $producto['nombre'];
        }
    }

    $producto_mas_vendido = $productos[0] ?? null;
    $nombre_producto_mas_vendido = $producto_mas_vendido ? $producto_mas_vendido['nombre'] : 'N/A';
    $cantidad_mas_vendido = $producto_mas_vendido ? $producto_mas_vendido['cantidad_vendida'] : 0;

    return [
        'total_unidades' => $total_unidades,
        'total_productos' => $total_productos,
        'producto_mas_caro' => $producto_mas_caro,
        'nombre_producto_caro' => $nombre_producto_caro,
        'producto_mas_vendido' => $nombre_producto_mas_vendido,
        'cantidad_mas_vendido' => $cantidad_mas_vendido,
        'ingresos_totales' => $ingresos_totales
    ];
}

private function calcularDistribucionCategoria($productos_vendidos) {
    $distribucion = [];
    
    foreach ($productos_vendidos as $producto) {
        $categoria = $producto['categoria_nombre'];
        if (!isset($distribucion[$categoria])) {
            $distribucion[$categoria] = [
                'categoria' => $categoria,
                'cantidad' => 0,
                'ingresos' => 0,
                'productos' => 0
            ];
        }
        $distribucion[$categoria]['cantidad'] += intval($producto['cantidad_vendida']);
        $distribucion[$categoria]['ingresos'] += floatval($producto['ingresos_totales']);
        $distribucion[$categoria]['productos'] += 1;
    }

    return array_values($distribucion);
}

    private function obtenerClientesFrecuentes($fecha_inicio, $fecha_fin) {
        $sql = "SELECT 
            c.id,
            c.nombres,
            c.apellidos,
            c.correo,
            COUNT(p.id) as total_pedidos,
            COALESCE(SUM(p.total), 0) as total_gastado,
            MAX(p.fecha) as ultimo_pedido
        FROM clientes c
        LEFT JOIN pedidos p ON c.id = p.cliente_id
        WHERE (p.fecha IS NULL OR DATE(p.fecha) BETWEEN :fecha_inicio AND :fecha_fin)
        AND c.eliminado = 0
        GROUP BY c.id, c.nombres, c.apellidos, c.correo
        HAVING total_pedidos > 0
        ORDER BY total_gastado DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function calcularMetricasClientes($clientes) {
        $total_clientes = count($clientes);
        $total_gastado = 0;
        $total_pedidos = 0;

        foreach ($clientes as $cliente) {
            $total_gastado += floatval($cliente['total_gastado']);
            $total_pedidos += intval($cliente['total_pedidos']);
        }

        $ticket_promedio = $total_clientes > 0 ? $total_gastado / $total_clientes : 0;
        $frecuencia_compra = $total_clientes > 0 ? $total_pedidos / $total_clientes : 0;

        return [
            'total_clientes' => $total_clientes,
            'ticket_promedio' => $ticket_promedio,
            'frecuencia_compra' => $frecuencia_compra
        ];
    }

    private function calcularDistribucionClientes($clientes) {
        $nuevos = 0;
        $recurrentes = 0;
        $inactivos = 0;

        $hace_30_dias = date('Y-m-d', strtotime('-30 days'));

        foreach ($clientes as $cliente) {
            $ultimo_pedido = $cliente['ultimo_pedido'] ?? null;
            $total_pedidos = intval($cliente['total_pedidos']);

            if ($total_pedidos === 1) {
                $nuevos++;
            } elseif ($total_pedidos > 1 && $ultimo_pedido >= $hace_30_dias) {
                $recurrentes++;
            } else {
                $inactivos++;
            }
        }

        return [
            ['tipo' => 'Clientes Nuevos', 'cantidad' => $nuevos],
            ['tipo' => 'Clientes Recurrentes', 'cantidad' => $recurrentes],
            ['tipo' => 'Clientes Inactivos', 'cantidad' => $inactivos]
        ];
    }

    private function obtenerProductosInventario($categoria_id, $estado_stock) {
        $sql = "SELECT 
            p.id,
            p.nombre,
            p.descripcion,
            p.precio,
            p.stock,
            p.imagen,
            c.nombre as categoria_nombre
        FROM productos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
        WHERE p.eliminado = 0";

        $params = [];

        if ($categoria_id !== 'todos') {
            $sql .= " AND p.categoria_id = :categoria_id";
            $params['categoria_id'] = $categoria_id;
        }

        if ($estado_stock !== 'todos') {
            if ($estado_stock === 'agotado') {
                $sql .= " AND p.stock = 0";
            } elseif ($estado_stock === 'bajo') {
                $sql .= " AND p.stock > 0 AND p.stock <= 5";
            } elseif ($estado_stock === 'disponible') {
                $sql .= " AND p.stock > 5";
            }
        }

        $sql .= " ORDER BY p.stock ASC, p.nombre ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function calcularMetricasInventario($productos) {
        $total_productos = count($productos);
        $productos_stock_bajo = 0;
        $productos_agotados = 0;
        $valor_total_inventario = 0;

        foreach ($productos as $producto) {
            $stock = intval($producto['stock']);
            $precio = floatval($producto['precio']);
            
            if ($stock === 0) {
                $productos_agotados++;
            } elseif ($stock <= 5) {
                $productos_stock_bajo++;
            }
            
            $valor_total_inventario += $stock * $precio;
        }

        return [
            'total_productos' => $total_productos,
            'productos_stock_bajo' => $productos_stock_bajo,
            'productos_agotados' => $productos_agotados,
            'valor_total_inventario' => $valor_total_inventario
        ];
    }

    private function calcularInventarioPorCategoria($productos) {
        $inventario_por_categoria = [];
        
        foreach ($productos as $producto) {
            $categoria = $producto['categoria_nombre'];
            if (!isset($inventario_por_categoria[$categoria])) {
                $inventario_por_categoria[$categoria] = 0;
            }
            $inventario_por_categoria[$categoria] += intval($producto['stock']);
        }

        $result = [];
        foreach ($inventario_por_categoria as $categoria => $stock) {
            $result[] = [
                'categoria' => $categoria,
                'stock' => $stock
            ];
        }

        return $result;
    }

    private function calcularEstadoStock($productos) {
        $agotado = 0;
        $bajo = 0;
        $disponible = 0;

        foreach ($productos as $producto) {
            $stock = intval($producto['stock']);
            if ($stock === 0) {
                $agotado++;
            } elseif ($stock <= 5) {
                $bajo++;
            } else {
                $disponible++;
            }
        }

        return [
            ['estado' => 'Disponible', 'cantidad' => $disponible],
            ['estado' => 'Stock Bajo', 'cantidad' => $bajo],
            ['estado' => 'Agotado', 'cantidad' => $agotado]
        ];
    }
    // =========================================================================
// MÉTODOS PARA REPORTE DE PRODUCTOS MÁS VENDIDOS
// =========================================================================


private function obtenerTopProductos($productos_vendidos, $limite = 10) {
    return array_slice($productos_vendidos, 0, $limite);
}

private function obtenerProductosConStockBajo() {
    $sql = "SELECT 
        p.id,
        p.nombre,
        p.stock,
        c.nombre as categoria_nombre,
        p.precio
    FROM productos p
    LEFT JOIN categorias c ON p.categoria_id = c.id
    WHERE p.stock > 0 AND p.stock <= 5
    AND p.eliminado = 0
    ORDER BY p.stock ASC
    LIMIT 10";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
?>