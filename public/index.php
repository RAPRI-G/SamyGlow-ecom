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
    // 🔹 ENDPOINTS API PARA JS (AJAX)
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
    // ❌ 404 - Página no encontrada
    // =====================================
    default:
        http_response_code(404);
        echo "<h1 style='font-family:sans-serif;text-align:center;margin-top:20%;color:#555;'>
                404 - Página no encontrada
              </h1>";
        break;
}
?>