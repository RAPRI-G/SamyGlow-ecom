<?php
// app/controllers/MetodoPagoController.php
require_once __DIR__ . '/../models/MetodoPago.php';

class MetodoPagoController
{
    private $model;

    public function __construct()
    {
        global $pdo;
        $this->model = new MetodoPago($pdo);
    }

    // Vista principal de gestión de métodos de pago
    public function index()
    {
        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?view=login");
            exit;
        }

        require_once __DIR__ . '/../views/templates/header.php';
        require_once __DIR__ . '/../views/templates/sidebar.php';
        require_once __DIR__ . '/../views/admin/gestion-metodos-pago.php';
        require_once __DIR__ . '/../views/templates/footer.php';
    }

    // API: Obtener todos los métodos de pago
    public function listarMetodos()
    {
        header('Content-Type: application/json');
        try {
            $metodos = $this->model->obtenerTodos();
            echo json_encode(['success' => true, 'data' => $metodos]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // API: Obtener estadísticas
    public function obtenerEstadisticas()
    {
        header('Content-Type: application/json');
        try {
            $estadisticas = $this->model->obtenerEstadisticas();
            echo json_encode(['success' => true, 'data' => $estadisticas]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // API: Crear método de pago
    public function crearMetodo()
    {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!isset($input['nombre']) || empty(trim($input['nombre']))) {
                echo json_encode(['success' => false, 'error' => 'El nombre del método es requerido']);
                return;
            }

            $datos = [
                'nombre' => trim($input['nombre']),
                'tipo' => $input['tipo'] ?? 'digital',
                'descripcion' => $input['descripcion'] ?? '',
                'icono' => $input['icono'] ?? 'fas fa-credit-card',
                'activo' => isset($input['activo']) ? (int)$input['activo'] : 1
            ];

            $resultado = $this->model->crear($datos);

            if ($resultado) {
                echo json_encode(['success' => true, 'message' => 'Método creado correctamente']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al crear el método']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // API: Actualizar método de pago
    public function actualizarMetodo()
    {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!isset($input['id']) || !isset($input['nombre'])) {
                echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
                return;
            }

            $id = (int)$input['id'];
            $datos = [
                'nombre' => trim($input['nombre']),
                'tipo' => $input['tipo'] ?? 'digital',
                'descripcion' => $input['descripcion'] ?? '',
                'icono' => $input['icono'] ?? 'fas fa-credit-card',
                'activo' => isset($input['activo']) ? (int)$input['activo'] : 1
            ];

            $resultado = $this->model->actualizar($id, $datos);

            if ($resultado) {
                echo json_encode(['success' => true, 'message' => 'Método actualizado correctamente']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al actualizar el método']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // API: Obtener configuración
    public function obtenerConfiguracion()
    {
        header('Content-Type: application/json');
        try {
            $configuracion = $this->model->obtenerConfiguracion();
            echo json_encode(['success' => true, 'data' => $configuracion]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // API: Actualizar configuración
    public function actualizarConfiguracion()
    {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                echo json_encode(['success' => false, 'error' => 'Datos JSON inválidos']);
                return;
            }

            $datos = [
                'multiples_metodos' => isset($input['multiples_metodos']) ? (int)$input['multiples_metodos'] : 1,
                'notificaciones_pago' => isset($input['notificaciones_pago']) ? (int)$input['notificaciones_pago'] : 1,
                'confirmacion_automatica' => isset($input['confirmacion_automatica']) ? (int)$input['confirmacion_automatica'] : 0,
                'metodo_predeterminado_id' => isset($input['metodo_predeterminado_id']) ? (int)$input['metodo_predeterminado_id'] : 1,
                'orden_metodos' => $input['orden_metodos'] ?? [1, 2, 3, 4, 5]
            ];

            $resultado = $this->model->actualizarConfiguracion($datos);

            if ($resultado) {
                echo json_encode(['success' => true, 'message' => 'Configuración actualizada correctamente']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al actualizar la configuración']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // API: Eliminar método de pago (soft delete)
    public function eliminarMetodo()
    {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!isset($input['id'])) {
                echo json_encode(['success' => false, 'error' => 'ID del método no proporcionado']);
                return;
            }

            $id = (int)$input['id'];
            $resultado = $this->model->eliminar($id);

            if ($resultado) {
                echo json_encode(['success' => true, 'message' => 'Método eliminado correctamente']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al eliminar el método']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
