<?php
// app/controllers/PromocionController.php

require_once __DIR__ . '/../models/Promocion.php';
require_once __DIR__ . '/../models/Producto.php';

class PromocionController
{
    private $promocionModel;
    private $productoModel;

    public function __construct()
    {
        global $pdo;
        $this->promocionModel = new Promocion($pdo);
        $this->productoModel = new Producto($pdo);
    }

    // 🔹 VISTA PRINCIPAL DE GESTIÓN DE PROMOCIONES
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?view=login");
            exit;
        }

        $title = "Gestión de Promociones - SamyGlow";
        $pageTitle = "Gestión de Promociones";

        try {
            // Obtener datos para la vista
            $promociones = $this->promocionModel->listar();
            $productos = $this->productoModel->listar();
            $estadisticas = $this->promocionModel->obtenerEstadisticas();
        } catch (Exception $e) {
            error_log("Error en PromocionController::index(): " . $e->getMessage());
            $promociones = [];
            $productos = [];
            $estadisticas = [
                'total_promociones' => 0,
                'promociones_activas' => 0,
                'promociones_expiradas' => 0,
                'total_usos' => 0
            ];
        }

        require_once __DIR__ . '/../views/templates/header.php';
        require_once __DIR__ . '/../views/admin/gestion-promociones.php';
        require_once __DIR__ . '/../views/templates/footer.php';
    }

    // 🔹 API: LISTAR PROMOCIONES
    public function listarPromociones()
    {
        header('Content-Type: application/json');

        try {
            $promociones = $this->promocionModel->listar();
            echo json_encode([
                'success' => true,
                'data' => $promociones,
                'total' => count($promociones)
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener promociones: ' . $e->getMessage()
            ]);
        }
    }

    // 🔹 API: OBTENER PROMOCIÓN POR ID
    public function obtenerPromocion()
    {
        header('Content-Type: application/json');

        try {
            $id = intval($_GET['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception('ID de promoción inválido');
            }

            $promocion = $this->promocionModel->obtener($id);

            if ($promocion) {
                echo json_encode([
                    'success' => true,
                    'data' => $promocion
                ]);
            } else {
                throw new Exception('Promoción no encontrada');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // 🔹 API: REGISTRAR NUEVA PROMOCIÓN
    public function registrarPromocion()
    {
        header('Content-Type: application/json');

        try {
            // Validar datos requeridos
            if (empty($_POST['nombre']) || empty($_POST['tipo']) || empty($_POST['fecha_inicio']) || empty($_POST['fecha_fin'])) {
                throw new Exception('Todos los campos obligatorios deben ser completados');
            }

            $datos = [
                'nombre' => trim($_POST['nombre']),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'tipo' => $_POST['tipo'],
                'valor_descuento' => $_POST['valor_descuento'] ?? null,
                'fecha_inicio' => $_POST['fecha_inicio'],
                'fecha_fin' => $_POST['fecha_fin'],
                'activa' => isset($_POST['activa']) ? 1 : 0,
                'max_usos' => $_POST['max_usos'] ?? null
            ];

            // Validaciones específicas
            if ($datos['fecha_inicio'] > $datos['fecha_fin']) {
                throw new Exception('La fecha de inicio no puede ser posterior a la fecha de fin');
            }

            if ($datos['tipo'] !== 'envio_gratis' && (empty($datos['valor_descuento']) || $datos['valor_descuento'] <= 0)) {
                throw new Exception('El valor de descuento debe ser mayor a 0');
            }

            // Obtener productos seleccionados
            $productos = [];
            if ($datos['tipo'] !== 'envio_gratis' && isset($_POST['productos']) && is_array($_POST['productos'])) {
                $productos = array_map('intval', $_POST['productos']);

                if (empty($productos)) {
                    throw new Exception('Debes seleccionar al menos un producto para la promoción');
                }
            }

            $promocionId = $this->promocionModel->registrar($datos, $productos);

            echo json_encode([
                'success' => true,
                'message' => 'Promoción registrada exitosamente',
                'promocion_id' => $promocionId
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // 🔹 API: ACTUALIZAR PROMOCIÓN
    public function actualizarPromocion()
    {
        header('Content-Type: application/json');

        try {
            $id = intval($_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception('ID de promoción inválido');
            }

            // Validar datos requeridos
            if (empty($_POST['nombre']) || empty($_POST['tipo']) || empty($_POST['fecha_inicio']) || empty($_POST['fecha_fin'])) {
                throw new Exception('Todos los campos obligatorios deben ser completados');
            }

            $datos = [
                'nombre' => trim($_POST['nombre']),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'tipo' => $_POST['tipo'],
                'valor_descuento' => $_POST['valor_descuento'] ?? null,
                'fecha_inicio' => $_POST['fecha_inicio'],
                'fecha_fin' => $_POST['fecha_fin'],
                'activa' => isset($_POST['activa']) ? 1 : 0,
                'max_usos' => $_POST['max_usos'] ?? null
            ];

            // Validaciones
            if ($datos['fecha_inicio'] > $datos['fecha_fin']) {
                throw new Exception('La fecha de inicio no puede ser posterior a la fecha de fin');
            }

            if ($datos['tipo'] !== 'envio_gratis' && (empty($datos['valor_descuento']) || $datos['valor_descuento'] <= 0)) {
                throw new Exception('El valor de descuento debe ser mayor a 0');
            }

            // Obtener productos seleccionados
            $productos = [];
            if ($datos['tipo'] !== 'envio_gratis' && isset($_POST['productos']) && is_array($_POST['productos'])) {
                $productos = array_map('intval', $_POST['productos']);

                if (empty($productos)) {
                    throw new Exception('Debes seleccionar al menos un producto para la promoción');
                }
            }

            $resultado = $this->promocionModel->actualizar($id, $datos, $productos);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Promoción actualizada exitosamente'
                ]);
            } else {
                throw new Exception('Error al actualizar la promoción');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // 🔹 API: ELIMINAR PROMOCIÓN
    public function eliminarPromocion()
    {
        header('Content-Type: application/json');

        try {
            $id = intval($_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception('ID de promoción inválido');
            }

            $resultado = $this->promocionModel->eliminar($id);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Promoción eliminada exitosamente'
                ]);
            } else {
                throw new Exception('Error al eliminar la promoción');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // 🔹 API: OBTENER PROMOCIONES ACTIVAS
    public function obtenerPromocionesActivas()
    {
        header('Content-Type: application/json');

        try {
            $promociones = $this->promocionModel->obtenerActivas();
            echo json_encode([
                'success' => true,
                'data' => $promociones,
                'total' => count($promociones)
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener promociones activas: ' . $e->getMessage()
            ]);
        }
    }

    // 🔹 API: OBTENER PRODUCTOS EN PROMOCIÓN
    public function obtenerProductosPromocion()
    {
        header('Content-Type: application/json');

        try {
            $productos = $this->promocionModel->obtenerProductosEnPromocion();
            echo json_encode([
                'success' => true,
                'data' => $productos,
                'total' => count($productos)
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener productos en promoción: ' . $e->getMessage()
            ]);
        }
    }

    // 🔹 API: OBTENER ESTADÍSTICAS
    public function obtenerEstadisticas()
    {
        header('Content-Type: application/json');

        try {
            $estadisticas = $this->promocionModel->obtenerEstadisticas();
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

    // 🔹 API: BUSCAR PROMOCIONES
    public function buscarPromociones()
    {
        header('Content-Type: application/json');

        try {
            $termino = $_GET['q'] ?? '';

            if (empty($termino)) {
                $promociones = $this->promocionModel->listar();
            } else {
                $promociones = $this->promocionModel->buscar($termino);
            }

            echo json_encode([
                'success' => true,
                'data' => $promociones,
                'total' => count($promociones)
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al buscar promociones: ' . $e->getMessage()
            ]);
        }
    }

    // 🔹 API: FILTRAR PROMOCIONES POR TIPO
    public function filtrarPromocionesTipo()
    {
        header('Content-Type: application/json');

        try {
            $tipo = $_GET['tipo'] ?? '';

            if (empty($tipo)) {
                $promociones = $this->promocionModel->listar();
            } else {
                $promociones = $this->promocionModel->filtrarPorTipo($tipo);
            }

            echo json_encode([
                'success' => true,
                'data' => $promociones,
                'total' => count($promociones)
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al filtrar promociones: ' . $e->getMessage()
            ]);
        }
    }

    // 🔹 API: LISTAR PRODUCTOS PARA PROMOCIÓN
    public function listarProductosPromocion()
    {
        header('Content-Type: application/json');

        try {
            $productos = $this->productoModel->listar();
            echo json_encode([
                'success' => true,
                'data' => $productos
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener productos: ' . $e->getMessage()
            ]);
        }
    }
    // 🔹 API: MOVER PROMOCIÓN A PAPELERA
    public function moverPapelera()
    {
        header('Content-Type: application/json');

        try {
            $id = intval($_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception('ID de promoción inválido');
            }

            $resultado = $this->promocionModel->moverPapelera($id);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Promoción movida a la papelera exitosamente'
                ]);
            } else {
                throw new Exception('Error al mover la promoción a la papelera');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // 🔹 API: RESTAURAR PROMOCIÓN
    public function restaurarPromocion()
    {
        header('Content-Type: application/json');

        try {
            $id = intval($_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception('ID de promoción inválido');
            }

            $resultado = $this->promocionModel->restaurar($id);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Promoción restaurada exitosamente'
                ]);
            } else {
                throw new Exception('Error al restaurar la promoción');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // 🔹 API: ELIMINAR PERMANENTEMENTE
    public function eliminarPermanentemente()
    {
        header('Content-Type: application/json');

        try {
            $id = intval($_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception('ID de promoción inválido');
            }

            $resultado = $this->promocionModel->eliminarPermanentemente($id);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Promoción eliminada permanentemente'
                ]);
            } else {
                throw new Exception('Error al eliminar permanentemente la promoción');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // 🔹 API: LISTAR PROMOCIONES ELIMINADAS
    public function listarEliminadas()
    {
        header('Content-Type: application/json');

        try {
            $promociones = $this->promocionModel->listarEliminadas();
            echo json_encode([
                'success' => true,
                'data' => $promociones,
                'total' => count($promociones)
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener promociones eliminadas: ' . $e->getMessage()
            ]);
        }
    }

    // 🔹 API: CONTAR PAPELERA
    public function contarPapelera()
    {
        header('Content-Type: application/json');

        try {
            $total = $this->promocionModel->contarPapelera();
            echo json_encode([
                'success' => true,
                'total' => $total
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al contar papelera: ' . $e->getMessage()
            ]);
        }
    }

    // 🔹 API: VACIAR PAPELERA
    public function vaciarPapelera()
    {
        header('Content-Type: application/json');

        try {
            $eliminadas = $this->promocionModel->vaciarPapelera();

            echo json_encode([
                'success' => true,
                'message' => "Papelera vaciada exitosamente. Se eliminaron {$eliminadas} promociones.",
                'eliminadas' => $eliminadas
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
