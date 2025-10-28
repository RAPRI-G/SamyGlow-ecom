<?php
require_once __DIR__ . "/../models/Usuario.php";
require_once __DIR__ . "/../../config/database.php";

class AuthController {

    // 🔹 Vista de login
    public function loginView() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // 🔒 Evitar que el navegador use caché
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");

        // ✅ Si ya hay sesión activa, redirigir al dashboard
        if (isset($_SESSION['usuario'])) {
            echo "<script>window.location.replace('index.php?view=dashboard');</script>";
            exit;
        }

        require_once __DIR__ . "/../views/login.php";
    }

    // 🔹 Proceso de login
    public function login() {
        if (!isset($_POST['username'], $_POST['password'])) {
            echo "⚠️ No llegaron datos del controlador";
            exit;
        }

        $username = trim($_POST['username']);
        $password = $_POST['password'];

        $usuario = new Usuario($GLOBALS['pdo']);
        $user = $usuario->login($username, $password);

        if (!$user) {
            session_start();
            $_SESSION['error'] = "Usuario o contraseña incorrectos.";
            header("Location: index.php?view=login");
            exit;
        }

        session_start();
        $_SESSION['usuario'] = $user;

        // 🔥 Evita que el navegador guarde el login en el historial
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Pragma: no-cache");
        header("Expires: 0");

        // 🔄 Reemplaza el historial del login por el del dashboard
        echo "<script>
            window.location.replace('index.php?view=dashboard');
        </script>";
        exit;
    }

    // 🔹 Cerrar sesión
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // 🔐 Limpiar todas las variables de sesión
        $_SESSION = [];

        // ❌ Eliminar cookie de sesión si existe
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // 🧹 Destruir sesión completamente
        session_destroy();

        // 🔒 Evitar volver atrás con caché
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");

        // 🚀 Redirigir al login reemplazando historial
        echo "<script>
            window.location.replace('index.php?view=login');
        </script>";
        exit;
    }
}
