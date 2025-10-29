<?php
session_start();

// Evitar caché en todas las páginas controladas por este index
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';

$auth = new AuthController();
$view = $_GET['view'] ?? 'login';

switch ($view) {
    case 'login':
        // Si ya hay sesión iniciada, no permitir volver al login
        if (isset($_SESSION['usuario'])) {
            header("Location: index.php?view=dashboard");
            exit;
        }
        $auth->loginView();
        break;

    case 'loginAuth':
        $auth->login();
        break;

    case 'dashboard':
        // Solo usuarios logueados pueden entrar
        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?view=login");
            exit;
        }

        // Evitar que se cachee el dashboard
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");

        require_once __DIR__ . '/../app/controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
        break;

    case 'logout':
        $auth->logout();
        break;

    default:
        http_response_code(404);
        echo "<h1 style='font-family:sans-serif;text-align:center;margin-top:20%;color:#555;'>404 - Página no encontrada</h1>";
        break;
}


