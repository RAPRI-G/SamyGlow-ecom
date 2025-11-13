<?php
// app/controllers/ClienteController.php

require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Pedido.php';

class ClienteController
{
    private $clienteModel;
    private $pedidoModel;

    public function __construct()
    {
        global $pdo;
        $this->clienteModel = new Cliente($pdo);
        $this->pedidoModel = new Pedido($pdo);
    }

    // 🔹 VISTA PRINCIPAL DE GESTIÓN DE CLIENTES
    // En el método index() del ClienteController, modifica esta parte:
    // En el método index() del ClienteController, modifica esta parte:
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Evitar caché
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");

        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?view=login");
            exit;
        }

        $title = "Gestión de Clientes - SamyGlow";
        $pageTitle = "Gestión de Clientes";

        try {
            // Obtener datos para la vista
            $clientes = $this->clienteModel->listar();

            // Usar el método simple para evitar errores
            $estadisticas = $this->clienteModel->obtenerEstadisticasBasicas();

            $clientesFrecuentes = $this->clienteModel->obtenerFrecuentes();

            // Debug: Verificar datos
            error_log("Clientes count: " . count($clientes));
            error_log("Estadísticas: " . print_r($estadisticas, true));
            error_log("Frecuentes count: " . count($clientesFrecuentes));
        } catch (Exception $e) {
            // En caso de error, usar valores por defecto
            error_log("Error en ClienteController::index(): " . $e->getMessage());

            $clientes = [];
            $estadisticas = [
                'total_clientes' => 0,
                'clientes_con_pedidos' => 0,
                'promedio_pedidos' => 0,
                'max_pedidos' => 0,
                'max_gastado' => 0
            ];
            $clientesFrecuentes = [];
        }

        require_once __DIR__ . '/../views/templates/header.php';
        require_once __DIR__ . '/../views/admin/gestion-clientes.php';
        require_once __DIR__ . '/../views/templates/footer.php';
    }

    // 🔹 API: LISTAR CLIENTES (para AJAX)
    public function listarClientes()
    {
        header('Content-Type: application/json');

        try {
            $clientes = $this->clienteModel->listar();
            echo json_encode([
                'success' => true,
                'data' => $clientes,
                'total' => count($clientes)
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener clientes: ' . $e->getMessage()
            ]);
        }
    }

    // 🔹 API: OBTENER CLIENTE POR ID
    public function obtenerCliente()
    {
        header('Content-Type: application/json');

        try {
            $id = intval($_GET['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception('ID de cliente inválido');
            }

            $cliente = $this->clienteModel->obtener($id);

            if ($cliente) {
                // Obtener estadísticas del cliente
                $estadisticasCliente = $this->pedidoModel->obtenerEstadisticasCliente($id);
                $cliente['estadisticas'] = $estadisticasCliente;

                echo json_encode([
                    'success' => true,
                    'data' => $cliente
                ]);
            } else {
                throw new Exception('Cliente no encontrado');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // 🔹 API: REGISTRAR NUEVO CLIENTE
    public function registrarCliente()
    {
        header('Content-Type: application/json');

        try {
            // Validar que se hayan enviado datos
            if (empty($_POST['nombres']) || empty($_POST['apellidos']) || empty($_POST['dni'])) {
                throw new Exception('Nombres, apellidos y DNI son obligatorios');
            }

            $datos = [
                'nombres' => $_POST['nombres'] ?? '',
                'apellidos' => $_POST['apellidos'] ?? '',
                'dni' => $_POST['dni'] ?? '',
                'correo' => $_POST['correo'] ?? '',
                'telefono' => $_POST['telefono'] ?? ''
            ];

            // Validaciones básicas
            if (strlen($datos['dni']) !== 8 || !is_numeric($datos['dni'])) {
                throw new Exception('El DNI debe tener exactamente 8 dígitos');
            }

            if (!empty($datos['telefono']) && (strlen($datos['telefono']) !== 9 || !is_numeric($datos['telefono']))) {
                throw new Exception('El teléfono debe tener exactamente 9 dígitos');
            }

            if (!empty($datos['correo']) && !filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('El correo electrónico no es válido');
            }

            $resultado = $this->clienteModel->registrar($datos);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Cliente registrado exitosamente',
                    'id' => $resultado
                ]);
            } else {
                throw new Exception('Error al registrar el cliente');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // 🔹 API: ACTUALIZAR CLIENTE
    public function actualizarCliente()
    {
        header('Content-Type: application/json');

        try {
            $id = intval($_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception('ID de cliente inválido');
            }

            $datos = [
                'nombres' => $_POST['nombres'] ?? '',
                'apellidos' => $_POST['apellidos'] ?? '',
                'correo' => $_POST['correo'] ?? '',
                'telefono' => $_POST['telefono'] ?? ''
            ];

            // Validaciones
            if (empty($datos['nombres']) || empty($datos['apellidos'])) {
                throw new Exception('Nombres y apellidos son obligatorios');
            }

            if (!empty($datos['telefono']) && (strlen($datos['telefono']) !== 9 || !is_numeric($datos['telefono']))) {
                throw new Exception('El teléfono debe tener exactamente 9 dígitos');
            }

            if (!empty($datos['correo']) && !filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('El correo electrónico no es válido');
            }

            $resultado = $this->clienteModel->actualizar($id, $datos);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Cliente actualizado exitosamente'
                ]);
            } else {
                throw new Exception('Error al actualizar el cliente');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // 🔹 API: ELIMINAR CLIENTE
    public function eliminarCliente()
    {
        header('Content-Type: application/json');

        try {
            $id = intval($_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception('ID de cliente inválido');
            }

            // Verificar si el cliente tiene pedidos
            $tienePedidos = $this->pedidoModel->clienteTienePedidos($id);
            if ($tienePedidos) {
                throw new Exception('No se puede eliminar un cliente que tiene pedidos registrados');
            }

            $resultado = $this->clienteModel->eliminar($id);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Cliente eliminado exitosamente'
                ]);
            } else {
                throw new Exception('Error al eliminar el cliente');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // 🔹 API: CLIENTES FRECUENTES
    public function clientesFrecuentes()
    {
        header('Content-Type: application/json');

        try {
            $clientes = $this->clienteModel->obtenerFrecuentes();
            echo json_encode([
                'success' => true,
                'data' => $clientes,
                'total' => count($clientes)
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener clientes frecuentes: ' . $e->getMessage()
            ]);
        }
    }

    // 🔹 API: ESTADÍSTICAS DE CLIENTES
    public function estadisticasClientes()
    {
        header('Content-Type: application/json');

        try {
            $estadisticas = $this->clienteModel->obtenerEstadisticas();
            echo json_encode([
                'success' => true,
                'data' => $estadisticas
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ]);
        }
    }

    // 🔹 API: BUSCAR CLIENTES
    public function buscarClientes()
    {
        header('Content-Type: application/json');

        try {
            $termino = $_GET['q'] ?? '';

            if (empty($termino)) {
                $clientes = $this->clienteModel->listar();
            } else {
                $clientes = $this->clienteModel->buscar($termino);
            }

            echo json_encode([
                'success' => true,
                'data' => $clientes,
                'total' => count($clientes)
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al buscar clientes: ' . $e->getMessage()
            ]);
        }
    }

    // 🔹 API: CONTAR TOTAL DE CLIENTES
    public function contarClientes()
    {
        header('Content-Type: application/json');

        try {
            $total = $this->clienteModel->contarTotal();
            echo json_encode([
                'success' => true,
                'total' => $total
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al contar clientes: ' . $e->getMessage()
            ]);
        }
    }
}
