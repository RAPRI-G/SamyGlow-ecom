<?php
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Categoria.php';

class ReporteController
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }


    public function index()
    {
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

    public function reporteVentas()
    {
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

    public function reporteProductos()
    {
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

    public function reporteInventario()
    {
        header('Content-Type: application/json');

        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario'])) {
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            exit;
        }

        try {
            $categoria_id = $_GET['categoria_id'] ?? 'todos';
            $estado_stock = $_GET['estado_stock'] ?? 'todos';

            // Obtener productos del inventario
            $productos = $this->obtenerProductosInventario($categoria_id, $estado_stock);

            // Calcular métricas
            $metrics = $this->calcularMetricasInventario($productos);

            // Inventario por categoría
            $inventario_por_categoria = $this->calcularInventarioPorCategoria($productos);

            // Estado del stock
            $estado_stock_data = $this->calcularEstadoStock($productos);

            // Productos que necesitan reposición
            $productos_reposicion = $this->obtenerProductosNecesitanReposicion(10);

            // Productos más valiosos
            $productos_mas_valiosos = $this->obtenerProductosMasValiosos(10);

            echo json_encode([
                'success' => true,
                'data' => [
                    'productos' => $productos,
                    'metrics' => $metrics,
                    'inventario_por_categoria' => $inventario_por_categoria,
                    'estado_stock_data' => $estado_stock_data,
                    'productos_reposicion' => $productos_reposicion,
                    'productos_mas_valiosos' => $productos_mas_valiosos
                ]
            ]);
        } catch (Exception $e) {
            error_log("Error en reporteInventario: " . $e->getMessage());

            echo json_encode([
                'success' => false,
                'error' => 'Error interno del servidor: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    public function reporteClientes()
    {
        header('Content-Type: application/json');

        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario'])) {
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            exit;
        }

        try {
            $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01', strtotime('-1 year'));
            $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-t');

            // Validar fechas
            if (!strtotime($fecha_inicio) || !strtotime($fecha_fin)) {
                throw new Exception('Fechas inválidas');
            }

            // Obtener clientes frecuentes en el período
            $clientes_frecuentes = $this->obtenerClientesFrecuentes($fecha_inicio, $fecha_fin);

            // Calcular métricas
            $metrics = $this->calcularMetricasClientes($clientes_frecuentes);

            // Obtener top 10 clientes por gasto
            $top_clientes_gasto = $this->obtenerTopClientesPorGasto($clientes_frecuentes, 10);

            // Distribución completa de clientes
            $distribucion_clientes = $this->calcularDistribucionClientes($clientes_frecuentes);

            // Estadísticas avanzadas
            $estadisticas_avanzadas = $this->obtenerEstadisticasAvanzadasClientes($clientes_frecuentes);

            // Combinar métricas
            $metrics_combinadas = array_merge($metrics, $estadisticas_avanzadas);

            echo json_encode([
                'success' => true,
                'data' => [
                    'clientes' => $clientes_frecuentes,
                    'metrics' => $metrics_combinadas,
                    'top_clientes_gasto' => $top_clientes_gasto,
                    'distribucion_clientes' => $distribucion_clientes
                ]
            ]);
        } catch (Exception $e) {
            error_log("Error en reporteClientes: " . $e->getMessage());

            echo json_encode([
                'success' => false,
                'error' => 'Error interno del servidor: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    // =========================================================================
    // MÉTODOS PRIVADOS PARA REPORTE DE VENTAS
    // =========================================================================

    private function obtenerPedidosFiltrados($fecha_inicio, $fecha_fin, $categoria_id, $metodo_pago_id)
    {
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

    private function calcularMetricasVentas($pedidos)
    {
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

    private function obtenerVentasPorDia($fecha_inicio, $fecha_fin, $categoria_id, $metodo_pago_id)
    {
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

    private function obtenerTopClientesPorGasto($clientes, $limite = 10)
    {
        usort($clientes, function ($a, $b) {
            return floatval($b['total_gastado']) - floatval($a['total_gastado']);
        });

        return array_slice($clientes, 0, $limite);
    }

    private function obtenerVentasPorCategoria($fecha_inicio, $fecha_fin, $metodo_pago_id)
    {
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

    private function obtenerProductosVendidos($fecha_inicio, $fecha_fin, $categoria_id)
    {
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

    private function calcularMetricasProductos($productos)
    {
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

    private function calcularDistribucionCategoria($productos_vendidos)
    {
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

    // =========================================================================
    // MÉTODOS PARA REPORTE DE CLIENTES FRECUENTES
    // =========================================================================

    private function obtenerClientesFrecuentes($fecha_inicio, $fecha_fin)
    {
        $sql = "SELECT 
        c.id,
        c.nombres,
        c.apellidos,
        c.correo,
        c.telefono,
        c.dni,
        COUNT(p.id) as total_pedidos,
        COALESCE(SUM(p.total), 0) as total_gastado,
        COALESCE(AVG(p.total), 0) as promedio_pedido,
        MIN(p.fecha) as primer_pedido,
        MAX(p.fecha) as ultimo_pedido,
        DATEDIFF(MAX(p.fecha), MIN(p.fecha)) as dias_actividad,
        CASE 
            WHEN COUNT(p.id) >= 5 THEN 'VIP'
            WHEN COUNT(p.id) >= 3 THEN 'Frecuente' 
            ELSE 'Ocasional'
        END as segmento
    FROM clientes c
    LEFT JOIN pedidos p ON c.id = p.cliente_id
    WHERE p.fecha IS NOT NULL 
    AND DATE(p.fecha) BETWEEN :fecha_inicio AND :fecha_fin
    AND p.eliminado = 0
    AND c.eliminado = 0
    GROUP BY c.id, c.nombres, c.apellidos, c.correo, c.telefono, c.dni
    HAVING total_pedidos > 0
    ORDER BY total_gastado DESC, total_pedidos DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function obtenerTodosLosClientes()
    {
        $sql = "SELECT 
        c.id,
        c.nombres,
        c.apellidos,
        c.correo,
        c.telefono,
        c.dni,
        COUNT(p.id) as total_pedidos,
        COALESCE(SUM(p.total), 0) as total_gastado,
        MAX(p.fecha) as ultimo_pedido,
        MIN(p.fecha) as primer_pedido
    FROM clientes c
    LEFT JOIN pedidos p ON c.id = p.cliente_id AND p.eliminado = 0
    WHERE c.eliminado = 0
    GROUP BY c.id, c.nombres, c.apellidos, c.correo, c.telefono, c.dni
    ORDER BY total_gastado DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    private function calcularMetricasClientes($clientes)
    {
        $total_clientes = count($clientes);
        $total_gastado = 0;
        $total_pedidos = 0;
        $max_gastado = 0;
        $max_pedidos = 0;

        foreach ($clientes as $cliente) {
            $gastado = floatval($cliente['total_gastado']);
            $pedidos = intval($cliente['total_pedidos']);

            $total_gastado += $gastado;
            $total_pedidos += $pedidos;

            if ($gastado > $max_gastado) {
                $max_gastado = $gastado;
            }

            if ($pedidos > $max_pedidos) {
                $max_pedidos = $pedidos;
            }
        }

        $ticket_promedio = $total_clientes > 0 ? $total_gastado / $total_clientes : 0;
        $frecuencia_compra = $total_clientes > 0 ? $total_pedidos / $total_clientes : 0;

        return [
            'total_clientes' => $total_clientes,
            'total_gastado' => $total_gastado,
            'total_pedidos' => $total_pedidos,
            'ticket_promedio' => $ticket_promedio,
            'frecuencia_compra' => $frecuencia_compra,
            'max_gastado' => $max_gastado,
            'max_pedidos' => $max_pedidos
        ];
    }

    private function calcularDistribucionClientes($clientes_frecuentes)
    {
        // Obtener todos los clientes para la distribución completa
        $todos_clientes = $this->obtenerTodosLosClientes();

        $distribucion = [
            'VIP' => 0,
            'Frecuente' => 0,
            'Ocasional' => 0,
            'Recurrentes' => 0,
            'Inactivos' => 0,
            'Nuevos' => 0
        ];

        $hace_30_dias = date('Y-m-d', strtotime('-30 days'));
        $hace_90_dias = date('Y-m-d', strtotime('-90 days'));
        $hace_180_dias = date('Y-m-d', strtotime('-180 days'));

        foreach ($todos_clientes as $cliente) {
            $total_pedidos = intval($cliente['total_pedidos']);
            $ultimo_pedido = $cliente['ultimo_pedido'];
            $primer_pedido = $cliente['primer_pedido'];

            // Clientes sin pedidos
            if ($total_pedidos === 0) {
                $distribucion['Inactivos']++;
                continue;
            }

            // Clientes nuevos (primer pedido en los últimos 30 días)
            if ($primer_pedido && $primer_pedido >= $hace_30_dias) {
                $distribucion['Nuevos']++;
            }
            // Clientes inactivos (último pedido hace más de 180 días)
            elseif ($ultimo_pedido && $ultimo_pedido < $hace_180_dias) {
                $distribucion['Inactivos']++;
            }
            // Clientes recurrentes (más de 1 pedido y activos recientemente)
            elseif ($total_pedidos > 1 && $ultimo_pedido && $ultimo_pedido >= $hace_90_dias) {
                $distribucion['Recurrentes']++;
            }
            // Segmentación por cantidad de pedidos (solo para clientes activos)
            else {
                if ($total_pedidos >= 5) {
                    $distribucion['VIP']++;
                } elseif ($total_pedidos >= 3) {
                    $distribucion['Frecuente']++;
                } else {
                    $distribucion['Ocasional']++;
                }
            }
        }

        // Convertir a formato para gráfico
        $resultado = [];
        foreach ($distribucion as $tipo => $cantidad) {
            if ($cantidad > 0) {
                $resultado[] = [
                    'tipo' => $tipo,
                    'cantidad' => $cantidad
                ];
            }
        }

        return $resultado;
    }

    private function obtenerEstadisticasAvanzadasClientes($clientes_frecuentes)
    {
        $todos_clientes = $this->obtenerTodosLosClientes();

        $estadisticas = [
            'total_clientes_registrados' => count($todos_clientes),
            'clientes_con_pedidos' => 0,
            'clientes_sin_pedidos' => 0,
            'tasa_retencion' => 0,
            'valor_vida_cliente' => 0
        ];

        $clientes_con_pedidos = 0;
        $total_gastado_vida = 0;

        foreach ($todos_clientes as $cliente) {
            if (intval($cliente['total_pedidos']) > 0) {
                $clientes_con_pedidos++;
                $total_gastado_vida += floatval($cliente['total_gastado']);
            }
        }

        $estadisticas['clientes_con_pedidos'] = $clientes_con_pedidos;
        $estadisticas['clientes_sin_pedidos'] = count($todos_clientes) - $clientes_con_pedidos;
        $estadisticas['tasa_retencion'] = count($todos_clientes) > 0 ?
            ($clientes_con_pedidos / count($todos_clientes)) * 100 : 0;
        $estadisticas['valor_vida_cliente'] = $clientes_con_pedidos > 0 ?
            $total_gastado_vida / $clientes_con_pedidos : 0;

        return $estadisticas;
    }

    private function obtenerClientesRecientes($limite = 5)
    {
        $sql = "SELECT 
        c.id,
        c.nombres,
        c.apellidos,
        c.correo,
        MAX(p.fecha) as ultimo_pedido,
        COUNT(p.id) as total_pedidos
    FROM clientes c
    LEFT JOIN pedidos p ON c.id = p.cliente_id
    WHERE p.fecha IS NOT NULL
    AND c.eliminado = 0
    GROUP BY c.id, c.nombres, c.apellidos, c.correo
    ORDER BY ultimo_pedido DESC
    LIMIT :limite";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================================
    // MÉTODOS PARA REPORTE DE ESTADO DE INVENTARIO
    // =========================================================================

    private function obtenerProductosInventario($categoria_id, $estado_stock)
    {
        $sql = "SELECT 
        p.id,
        p.nombre,
        p.descripcion,
        p.precio,
        p.stock,
        p.imagen,
        p.activo,
        c.nombre as categoria_nombre,
        c.id as categoria_id,
        (p.precio * p.stock) as valor_inventario,
        CASE 
            WHEN p.stock = 0 THEN 'agotado'
            WHEN p.stock <= 5 THEN 'bajo'
            ELSE 'disponible'
        END as estado_stock
    FROM productos p
    LEFT JOIN categorias c ON p.categoria_id = c.id
    WHERE p.eliminado = 0
    AND p.activo = 1"; // Solo productos activos

        $params = [];

        // Aplicar filtro de categoría
        if ($categoria_id !== 'todos') {
            $sql .= " AND p.categoria_id = :categoria_id";
            $params['categoria_id'] = $categoria_id;
        }

        // Aplicar filtro de estado de stock
        if ($estado_stock !== 'todos') {
            switch ($estado_stock) {
                case 'agotado':
                    $sql .= " AND p.stock = 0";
                    break;
                case 'bajo':
                    $sql .= " AND p.stock > 0 AND p.stock <= 5";
                    break;
                case 'disponible':
                    $sql .= " AND p.stock > 5";
                    break;
            }
        }

        $sql .= " ORDER BY p.stock ASC, p.nombre ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // DEBUG: Verificar qué datos se están obteniendo
        error_log("=== PRODUCTOS OBTENIDOS PARA INVENTARIO ===");
        error_log("Total productos: " . count($resultados));
        if (count($resultados) > 0) {
            error_log("Primer producto: " . print_r($resultados[0], true));
            error_log("Categorías encontradas:");
            $categorias_unicas = [];
            foreach ($resultados as $producto) {
                $cat_id = $producto['categoria_id'];
                $cat_nombre = $producto['categoria_nombre'];
                if (!isset($categorias_unicas[$cat_id])) {
                    $categorias_unicas[$cat_id] = $cat_nombre;
                }
            }
            error_log("Categorías únicas: " . print_r($categorias_unicas, true));
        }

        return $resultados;
    }

    private function calcularMetricasInventario($productos)
    {
        $total_productos = count($productos);
        $productos_stock_bajo = 0;
        $productos_agotados = 0;
        $productos_disponibles = 0;
        $valor_total_inventario = 0;
        $stock_total = 0;
        $productos_activos = 0;

        foreach ($productos as $producto) {
            $stock = intval($producto['stock']);
            $precio = floatval($producto['precio']);
            $activo = intval($producto['activo']);

            $stock_total += $stock;
            $valor_total_inventario += $stock * $precio;

            if ($activo === 1) {
                $productos_activos++;
            }

            if ($stock === 0) {
                $productos_agotados++;
            } elseif ($stock <= 5) {
                $productos_stock_bajo++;
            } else {
                $productos_disponibles++;
            }
        }

        $valor_promedio_producto = $total_productos > 0 ? $valor_total_inventario / $total_productos : 0;
        $stock_promedio = $total_productos > 0 ? $stock_total / $total_productos : 0;

        return [
            'total_productos' => $total_productos,
            'productos_activos' => $productos_activos,
            'productos_stock_bajo' => $productos_stock_bajo,
            'productos_agotados' => $productos_agotados,
            'productos_disponibles' => $productos_disponibles,
            'valor_total_inventario' => $valor_total_inventario,
            'stock_total' => $stock_total,
            'valor_promedio_producto' => $valor_promedio_producto,
            'stock_promedio' => $stock_promedio,
            'tasa_disponibilidad' => $total_productos > 0 ?
                (($productos_disponibles + $productos_stock_bajo) / $total_productos) * 100 : 0
        ];
    }

    private function calcularInventarioPorCategoria($productos)
    {
        $inventario_por_categoria = [];

        foreach ($productos as $producto) {
            $categoria_id = $producto['categoria_id'];
            $categoria_nombre = $producto['categoria_nombre'];

            // Si no hay categoría asignada, usar "Sin Categoría"
            if (empty($categoria_id) || empty($categoria_nombre)) {
                $categoria_id = 'sin_categoria';
                $categoria_nombre = 'Sin Categoría';
            }

            if (!isset($inventario_por_categoria[$categoria_id])) {
                $inventario_por_categoria[$categoria_id] = [
                    'categoria_id' => $categoria_id,
                    'categoria_nombre' => $categoria_nombre,
                    'total_productos' => 0,
                    'stock_total' => 0,
                    'valor_total' => 0,
                    'productos_agotados' => 0,
                    'productos_stock_bajo' => 0,
                    'productos_disponibles' => 0
                ];
            }

            $stock = intval($producto['stock'] ?? 0);
            $precio = floatval($producto['precio'] ?? 0);
            $valor_inventario = $stock * $precio;

            $inventario_por_categoria[$categoria_id]['total_productos']++;
            $inventario_por_categoria[$categoria_id]['stock_total'] += $stock;
            $inventario_por_categoria[$categoria_id]['valor_total'] += $valor_inventario;

            if ($stock === 0) {
                $inventario_por_categoria[$categoria_id]['productos_agotados']++;
            } elseif ($stock <= 5) {
                $inventario_por_categoria[$categoria_id]['productos_stock_bajo']++;
            } else {
                $inventario_por_categoria[$categoria_id]['productos_disponibles']++;
            }
        }

        $resultado = array_values($inventario_por_categoria);

        // DEBUG: Registrar los datos calculados
        error_log("=== INVENTARIO POR CATEGORÍA CALCULADO ===");
        error_log("Total categorías: " . count($resultado));
        foreach ($resultado as $index => $categoria) {
            error_log("Categoría {$index}: " . print_r($categoria, true));
        }

        return $resultado;
    }

    private function calcularEstadoStock($productos)
    {
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
            ['estado' => 'Disponible', 'cantidad' => $disponible, 'color' => '#10b981'],
            ['estado' => 'Stock Bajo', 'cantidad' => $bajo, 'color' => '#f59e0b'],
            ['estado' => 'Agotado', 'cantidad' => $agotado, 'color' => '#ef4444']
        ];
    }

    private function obtenerProductosNecesitanReposicion($limite = 10)
    {
        $sql = "SELECT 
        p.id,
        p.nombre,
        p.stock,
        p.precio,
        c.nombre as categoria_nombre,
        (p.precio * p.stock) as valor_actual,
        CASE 
            WHEN p.stock = 0 THEN 'URGENTE'
            WHEN p.stock <= 2 THEN 'ALTA'
            WHEN p.stock <= 5 THEN 'MEDIA'
            ELSE 'BAJA'
        END as prioridad_reposicion
    FROM productos p
    LEFT JOIN categorias c ON p.categoria_id = c.id
    WHERE p.stock <= 5 
    AND p.eliminado = 0
    AND p.activo = 1
    ORDER BY 
        CASE 
            WHEN p.stock = 0 THEN 1
            WHEN p.stock <= 2 THEN 2
            WHEN p.stock <= 5 THEN 3
            ELSE 4
        END ASC,
        p.stock ASC
    LIMIT :limite";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function obtenerProductosMasValiosos($limite = 10)
    {
        $sql = "SELECT 
        p.id,
        p.nombre,
        p.stock,
        p.precio,
        c.nombre as categoria_nombre,
        (p.precio * p.stock) as valor_inventario
    FROM productos p
    LEFT JOIN categorias c ON p.categoria_id = c.id
    WHERE p.eliminado = 0
    AND p.activo = 1
    ORDER BY valor_inventario DESC
    LIMIT :limite";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================================
    // MÉTODOS PARA REPORTE DE PRODUCTOS MÁS VENDIDOS
    // =========================================================================


    private function obtenerTopProductos($productos_vendidos, $limite = 10)
    {
        return array_slice($productos_vendidos, 0, $limite);
    }

    private function obtenerProductosConStockBajo()
    {
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
