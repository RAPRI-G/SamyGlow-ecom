<?php
session_start();

// 🧠 Evitar caché en todas las páginas controladas por este index
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// 🔗 Conexión y controladores base
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';

// Instancia base de autenticación
$auth = new AuthController();

// Determinar vista solicitada
$view = $_GET['view'] ?? 'login';

switch ($view) {

    // =====================================
    // 🔹 LOGIN Y AUTENTICACIÓN
    // =====================================
    case 'login':
        // Si ya hay sesión iniciada, redirigir al dashboard
        if (isset($_SESSION['usuario'])) {
            header("Location: index.php?view=dashboard");
            exit;
        }
        $auth->loginView();
        break;

    case 'loginAuth':
        $auth->login();
        break;

    case 'logout':
        $auth->logout();
        break;

    // =====================================
    // 🔹 DASHBOARD PRINCIPAL
    // =====================================
    case 'dashboard':
        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?view=login");
            exit;
        }

        // Evitar caché
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");

        require_once __DIR__ . '/../app/controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
        break;

    // =====================================
    // 🔹 GESTIÓN DE PRODUCTOS (VISTA PRINCIPAL)
    // =====================================
    case 'gestion-productos':
        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?view=login");
            exit;
        }

        require_once __DIR__ . '/../app/controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->index();
        break;

    // =====================================
    // 🔹 NUEVO PEDIDO (vista principal)
    // =====================================
    case 'nuevo-pedido':
        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?view=login");
            exit;
        }

        require_once __DIR__ . '/../app/controllers/PedidoController.php';
        $controller = new PedidoController();
        $controller->index();
        break;

    // =====================================
    // 🔹 ENDPOINTS API PARA JS (AJAX) - PEDIDOS
    // =====================================
    case 'api-productos':
        require_once __DIR__ . '/../app/controllers/PedidoController.php';
        $controller = new PedidoController();
        $controller->productos();
        break;

    case 'api-clientes':
        require_once __DIR__ . '/../app/controllers/PedidoController.php';
        $controller = new PedidoController();
        $controller->buscarCliente();
        break;

    case 'api-cliente-save':
        require_once __DIR__ . '/../app/controllers/PedidoController.php';
        $controller = new PedidoController();
        $controller->registrarCliente();
        break;

    case 'api-save-pedido':
        require_once __DIR__ . '/../app/controllers/PedidoController.php';
        $controller = new PedidoController();
        $controller->guardarPedido();
        break;

    // =====================================
    // 🔹 APIs DE GESTIÓN DE PRODUCTOS (AJAX)
    // =====================================
    case 'api-listar-productos':
        require_once __DIR__ . '/../app/controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->listarProductos();
        break;

    case 'api-guardar-producto':
        require_once __DIR__ . '/../app/controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->guardarProducto();
        break;

    case 'api-editar-producto':
        require_once __DIR__ . '/../app/controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->editarProducto();
        break;

    case 'api-eliminar-producto':
        require_once __DIR__ . '/../app/controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->eliminarProducto();
        break;

    case 'api-obtener-producto':
        require_once __DIR__ . '/../app/controllers/ProductoController.php';
        $controller = new ProductoController();
        // Método que necesitarás agregar al controlador
        $controller->obtenerProducto();
        break;

    case 'api-actualizar-stock':
        require_once __DIR__ . '/../app/controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->actualizarStock();
        break;

    case 'api-productos-stock-bajo':
        require_once __DIR__ . '/../app/controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->productosStockBajo();
        break;

    case 'api-buscar-productos':
        require_once __DIR__ . '/../app/controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->buscarProductos();
        break;

    case 'api-listar-categorias':
        require_once __DIR__ . '/../app/controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->listarCategorias();
        break;

    // =====================================
    // 🔹 PEDIDOS PENDIENTES (APIs)
    // =====================================
    case 'api-pedidos-pendientes':
        require_once __DIR__ . '/../app/controllers/PedidoController.php';
        $controller = new PedidoController();
        $controller->pedidosPendientes();
        break;

    case 'api-contar-pedidos-pendientes':
        require_once __DIR__ . '/../app/controllers/PedidoController.php';
        $controller = new PedidoController();
        $controller->contarPedidosPendientes();
        break;

    case 'api-marcar-entregado':
        require_once __DIR__ . '/../app/controllers/PedidoController.php';
        $controller = new PedidoController();
        $controller->marcarEntregado();
        break;

    // =====================================
    // 🔹 PAPELERA (APIs)
    // =====================================
    case 'api-pedidos-eliminados':
        require_once __DIR__ . '/../app/controllers/PedidoController.php';
        $controller = new PedidoController();
        $controller->pedidosEliminados();
        break;

    case 'api-mover-papelera':
        require_once __DIR__ . '/../app/controllers/PedidoController.php';
        $controller = new PedidoController();
        $controller->moverAPapelera();
        break;

    case 'api-restaurar-pedido':
        require_once __DIR__ . '/../app/controllers/PedidoController.php';
        $controller = new PedidoController();
        $controller->restaurarPedido();
        break;

    case 'api-eliminar-permanentemente':
        require_once __DIR__ . '/../app/controllers/PedidoController.php';
        $controller = new PedidoController();
        $controller->eliminarPermanentemente();
        break;

    case 'api-eliminar-pedido':
        require_once __DIR__ . '/../app/controllers/PedidoController.php';
        $controller = new PedidoController();
        $controller->eliminarPedido();
        break;

    case 'api-detalles-pedido':
        require_once __DIR__ . '/../app/controllers/PedidoController.php';
        $controller = new PedidoController();
        $controller->obtenerDetallesPedido();
        break;

    // =====================================
    // 🔹 PEDIDOS ENTREGADOS E HISTORIAL (APIs)
    // =====================================
    case 'api-pedidos-entregados':
        require_once __DIR__ . '/../app/controllers/PedidoController.php';
        $controller = new PedidoController();
        $controller->pedidosEntregados();
        break;

    case 'api-historial-ventas':
        require_once __DIR__ . '/../app/controllers/PedidoController.php';
        $controller = new PedidoController();
        $controller->historialVentas();
        break;

    // =====================================
    // ❌ 404 - Página no encontrada
    // =====================================
    default:
        http_response_code(404);
        echo "<h1 style='font-family:sans-serif;text-align:center;margin-top:20%;color:#555;'>
                404 - Página no encontrada
              </h1>";
        break;
}