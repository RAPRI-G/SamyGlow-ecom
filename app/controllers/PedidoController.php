<?php
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Pedido.php';

class PedidoController
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?view=login");
            exit;
        }

        // 🧠 Evitar caché de esta pantalla
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");

        $title = "Nuevo Pedido - SamyGlow";
        $pageTitle = "Nuevo Pedido";

        require __DIR__ . "/../views/admin/nuevo-pedido.php";
    }

    // 🔹 API: Obtener lista de productos
    public function productos()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario'])) die("Unauthorized");

        global $pdo;
        header("Content-Type: application/json");

        $producto = new Producto($pdo);
        echo json_encode($producto->listar());
        exit;
    }

    // 🔎 API: Buscar clientes
    public function buscarCliente()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario'])) die("Unauthorized");

        global $pdo;
        header("Content-Type: application/json");

        $q = trim($_GET['q'] ?? '');
        $cliente = new Cliente($pdo);

        echo json_encode($cliente->buscar($q));
        exit;
    }

    // 🧾 API: Registrar nuevo cliente
    public function registrarCliente()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario'])) die("Unauthorized");

        global $pdo;
        header("Content-Type: application/json");

        $post = array_map('trim', $_POST);

        // Validaciones mínimas
        if (empty($post['dni']) || empty($post['nombres']) || empty($post['correo'])) {
            echo json_encode(["ok" => false, "msg" => "Datos incompletos"]);
            exit;
        }

        $cliente = new Cliente($pdo);
        $id = $cliente->registrar($post); // 🔥 devuelve ID o false

        if ($id) {
            echo json_encode([
                "ok" => true,
                "id" => $id,
                "cliente" => [
                    "id" => $id,
                    "nombres" => $post['nombres'],
                    "apellidos" => $post['apellidos'],
                    "dni" => $post['dni'],
                    "correo" => $post['correo'],
                    "telefono" => $post['telefono'] ?? ''
                ]
            ]);
        } else {
            echo json_encode(["ok" => false, "msg" => "Error al guardar cliente"]);
        }

        exit;
    }

    // 🛍️ API: Guardar pedido
    public function guardarPedido()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario'])) die("Unauthorized");

        global $pdo;
        header("Content-Type: application/json");

        $json = file_get_contents("php://input");
        $data = json_decode($json, true);

        if (!$data) {
            echo json_encode(["ok" => false, "error" => "Sin datos"]);
            exit;
        }

        // ✅ Validación mínima
        if (empty($data['cliente']) || empty($data['items'])) {
            echo json_encode(["ok" => false, "error" => "Pedido incompleto"]);
            exit;
        }

        $pedido = new Pedido($pdo);
        $resp = $pedido->guardar($data);

        echo json_encode($resp);
        exit;
    }
}
