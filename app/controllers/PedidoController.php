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
    // 🔹 API: Obtener pedidos pendientes
    // 🔹 API: Obtener pedidos pendientes (ACTUALIZADO)
    public function pedidosPendientes()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario'])) die("Unauthorized");

        global $pdo;
        header("Content-Type: application/json");

        try {
            $sql = "SELECT p.*, 
                       CONCAT(c.nombres, ' ', c.apellidos) as cliente_nombre,
                       mp.nombre as metodo_pago_nombre
                FROM pedidos p
                LEFT JOIN clientes c ON p.cliente_id = c.id
                LEFT JOIN metodos_pago mp ON p.metodo_pago_id = mp.id
                WHERE p.estado = 'pendiente' 
                AND p.eliminado = FALSE
                ORDER BY p.fecha DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "success" => true,
                "data" => $pedidos
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
        }
        exit;
    }

    // 🔹 API: Contar pedidos pendientes
    public function contarPedidosPendientes()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario'])) die("Unauthorized");

        global $pdo;
        header("Content-Type: application/json");

        try {
            $sql = "SELECT COUNT(*) as total FROM pedidos 
                WHERE estado = 'pendiente' 
                AND eliminado = FALSE";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                "success" => true,
                "total" => (int)$result['total']
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
        }
        exit;
    }

    // 🔹 API: Obtener pedidos entregados (SI LO TIENES, ACTUALIZALO)
    public function pedidosEntregados()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario'])) die("Unauthorized");

        global $pdo;
        header("Content-Type: application/json");

        try {
            $sql = "SELECT p.*, 
                       CONCAT(c.nombres, ' ', c.apellidos) as cliente_nombre,
                       mp.nombre as metodo_pago_nombre
                FROM pedidos p
                LEFT JOIN clientes c ON p.cliente_id = c.id
                LEFT JOIN metodos_pago mp ON p.metodo_pago_id = mp.id
                WHERE p.estado = 'entregado' 
                AND p.eliminado = FALSE
                ORDER BY p.fecha DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "success" => true,
                "data" => $pedidos
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
        }
        exit;
    }

    // 🔹 API: Obtener todos los pedidos para historial (ACTUALIZADO)
    public function historialVentas()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario'])) die("Unauthorized");

        global $pdo;
        header("Content-Type: application/json");

        try {
            $sql = "SELECT p.*, 
                       CONCAT(c.nombres, ' ', c.apellidos) as cliente_nombre,
                       mp.nombre as metodo_pago_nombre
                FROM pedidos p
                LEFT JOIN clientes c ON p.cliente_id = c.id
                LEFT JOIN metodos_pago mp ON p.metodo_pago_id = mp.id
                WHERE p.eliminado = FALSE
                ORDER BY p.fecha DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "success" => true,
                "data" => $pedidos
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
        }
        exit;
    }

    // 🔹 API: Marcar pedido como entregado
    public function marcarEntregado()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario'])) die("Unauthorized");

        global $pdo;
        header("Content-Type: application/json");

        $pedidoId = $_POST['pedido_id'] ?? null;

        if (!$pedidoId) {
            echo json_encode(["success" => false, "error" => "ID de pedido requerido"]);
            exit;
        }

        try {
            $sql = "UPDATE pedidos SET estado = 'entregado' WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$pedidoId]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(["success" => true, "message" => "Pedido marcado como entregado"]);
            } else {
                echo json_encode(["success" => false, "error" => "Pedido no encontrado"]);
            }
        } catch (Exception $e) {
            echo json_encode(["success" => false, "error" => $e->getMessage()]);
        }
        exit;
    }

    // 🔹 API: Obtener pedidos eliminados (papelera)
    public function pedidosEliminados()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario'])) die("Unauthorized");

        global $pdo;
        header("Content-Type: application/json");

        try {
            $sql = "SELECT p.*, 
                       CONCAT(c.nombres, ' ', c.apellidos) as cliente_nombre,
                       mp.nombre as metodo_pago_nombre
                FROM pedidos p
                LEFT JOIN clientes c ON p.cliente_id = c.id
                LEFT JOIN metodos_pago mp ON p.metodo_pago_id = mp.id
                WHERE p.eliminado = TRUE
                ORDER BY p.fecha_eliminado DESC, p.fecha DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "success" => true,
                "data" => $pedidos
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
        }
        exit;
    }

    // 🔹 API: Mover pedido a papelera (eliminado suave)
    public function moverAPapelera()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario'])) die("Unauthorized");

        global $pdo;
        header("Content-Type: application/json");

        $pedidoId = $_POST['pedido_id'] ?? null;

        if (!$pedidoId) {
            echo json_encode(["success" => false, "error" => "ID de pedido requerido"]);
            exit;
        }

        try {
            $sql = "UPDATE pedidos SET eliminado = TRUE, fecha_eliminado = NOW() WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$pedidoId]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(["success" => true, "message" => "Pedido movido a la papelera"]);
            } else {
                echo json_encode(["success" => false, "error" => "Pedido no encontrado"]);
            }
        } catch (Exception $e) {
            echo json_encode(["success" => false, "error" => $e->getMessage()]);
        }
        exit;
    }

    // 🔹 API: Restaurar pedido desde papelera
    public function restaurarPedido()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario'])) die("Unauthorized");

        global $pdo;
        header("Content-Type: application/json");

        $pedidoId = $_POST['pedido_id'] ?? null;

        if (!$pedidoId) {
            echo json_encode(["success" => false, "error" => "ID de pedido requerido"]);
            exit;
        }

        try {
            $sql = "UPDATE pedidos SET eliminado = FALSE, fecha_eliminado = NULL WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$pedidoId]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(["success" => true, "message" => "Pedido restaurado correctamente"]);
            } else {
                echo json_encode(["success" => false, "error" => "Pedido no encontrado"]);
            }
        } catch (Exception $e) {
            echo json_encode(["success" => false, "error" => $e->getMessage()]);
        }
        exit;
    }

    // 🔹 API: Eliminar permanentemente
    public function eliminarPermanentemente()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario'])) die("Unauthorized");

        global $pdo;
        header("Content-Type: application/json");

        $pedidoId = $_POST['pedido_id'] ?? null;

        if (!$pedidoId) {
            echo json_encode(["success" => false, "error" => "ID de pedido requerido"]);
            exit;
        }

        try {
            // Iniciar transacción
            $pdo->beginTransaction();

            // 1. Eliminar detalles del pedido
            $sqlDetalles = "DELETE FROM detalle_pedido WHERE pedido_id = ?";
            $stmtDetalles = $pdo->prepare($sqlDetalles);
            $stmtDetalles->execute([$pedidoId]);

            // 2. Eliminar el pedido permanentemente
            $sqlPedido = "DELETE FROM pedidos WHERE id = ?";
            $stmtPedido = $pdo->prepare($sqlPedido);
            $stmtPedido->execute([$pedidoId]);

            $pdo->commit();

            if ($stmtPedido->rowCount() > 0) {
                echo json_encode(["success" => true, "message" => "Pedido eliminado permanentemente"]);
            } else {
                echo json_encode(["success" => false, "error" => "Pedido no encontrado"]);
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(["success" => false, "error" => $e->getMessage()]);
        }
        exit;
    }

    // 🔹 ACTUALIZA el método eliminarPedido existente para que sea eliminación suave:
    public function eliminarPedido()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario'])) die("Unauthorized");

        global $pdo;
        header("Content-Type: application/json");

        $pedidoId = $_POST['pedido_id'] ?? null;

        if (!$pedidoId) {
            echo json_encode(["success" => false, "error" => "ID de pedido requerido"]);
            exit;
        }

        try {
            $sql = "UPDATE pedidos SET eliminado = TRUE, fecha_eliminado = NOW() WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$pedidoId]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(["success" => true, "message" => "Pedido movido a la papelera"]);
            } else {
                echo json_encode(["success" => false, "error" => "Pedido no encontrado"]);
            }
        } catch (Exception $e) {
            echo json_encode(["success" => false, "error" => $e->getMessage()]);
        }
        exit;
    }

    // 🔹 API: Obtener detalles completos de un pedido
    public function obtenerDetallesPedido()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario'])) die("Unauthorized");

        global $pdo;
        header("Content-Type: application/json");

        $pedidoId = $_GET['id'] ?? null;

        if (!$pedidoId) {
            echo json_encode(["success" => false, "error" => "ID de pedido requerido"]);
            exit;
        }

        try {
            // Obtener información del pedido
            $sqlPedido = "SELECT p.*, 
                             CONCAT(c.nombres, ' ', c.apellidos) as cliente_nombre,
                             c.dni, c.correo, c.telefono,
                             mp.nombre as metodo_pago_nombre,
                             pr.nombre as promocion_nombre
                      FROM pedidos p
                      LEFT JOIN clientes c ON p.cliente_id = c.id
                      LEFT JOIN metodos_pago mp ON p.metodo_pago_id = mp.id
                      LEFT JOIN promociones pr ON p.promocion_id = pr.id
                      WHERE p.id = ?";

            $stmtPedido = $pdo->prepare($sqlPedido);
            $stmtPedido->execute([$pedidoId]);
            $pedido = $stmtPedido->fetch(PDO::FETCH_ASSOC);

            if (!$pedido) {
                echo json_encode(["success" => false, "error" => "Pedido no encontrado"]);
                exit;
            }

            // Obtener detalles del pedido (productos)
            $sqlDetalles = "SELECT dp.*, p.nombre as producto_nombre, p.imagen
                       FROM detalle_pedido dp
                       JOIN productos p ON dp.producto_id = p.id
                       WHERE dp.pedido_id = ?";

            $stmtDetalles = $pdo->prepare($sqlDetalles);
            $stmtDetalles->execute([$pedidoId]);
            $detalles = $stmtDetalles->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "success" => true,
                "pedido" => $pedido,
                "detalles" => $detalles
            ]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "error" => $e->getMessage()]);
        }
        exit;
    }
}
