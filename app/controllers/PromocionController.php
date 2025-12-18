<?php
// app/controllers/PromocionController.php

require_once __DIR__ . '/../models/Promocion.php';
require_once __DIR__ . '/../models/Producto.php';

class PromocionController
{
    private $promocionModel;
    private $productoModel;

    // En PromocionController.php, modifica el constructor:

    public function __construct()
    {
        global $pdo;

        // LOG para verificar la conexión
        error_log("🔌 Inicializando PromocionController");
        error_log("📊 PDO disponible: " . ($pdo ? 'SÍ' : 'NO'));

        if (!$pdo) {
            error_log("❌ ERROR: No hay conexión PDO");
            throw new Exception("No hay conexión a la base de datos");
        }

        $this->promocionModel = new Promocion($pdo);
        $this->productoModel = new Producto($pdo);

        error_log("✅ Modelos inicializados correctamente");
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

        // LOG para depuración
        error_log("📡 API listarPromociones llamada");
        error_log("📊 Método: " . $_SERVER['REQUEST_METHOD']);
        error_log("🔗 URL: " . $_SERVER['REQUEST_URI']);

        try {
            // LOG de conexión PDO
            error_log("📦 Conectando con PDO...");

            $promociones = $this->promocionModel->listar();

            // LOG de resultados
            error_log("✅ Promociones obtenidas: " . count($promociones));

            echo json_encode([
                'success' => true,
                'data' => $promociones,
                'total' => count($promociones)
            ]);
        } catch (Exception $e) {
            // LOG del error detallado
            error_log("❌ ERROR en listarPromociones: " . $e->getMessage());
            error_log("📝 Trace: " . $e->getTraceAsString());

            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener promociones: ' . $e->getMessage(),
                'error_details' => $e->getMessage() // Solo para depuración
            ]);
        }
    }

    // 🔹 API: OBTENER PRODUCTOS DISPONIBLES PARA NUEVA PROMOCIÓN
    public function productosDisponiblesPromocion()
    {
        header('Content-Type: application/json');

        try {
            // Obtener productos que NO están en ninguna promoción activa
            $productosDisponibles = $this->promocionModel->obtenerProductosConEstadoPromocion();

            // Filtrar solo los que tienen 0 promociones activas
            $productosFiltrados = array_filter($productosDisponibles, function ($producto) {
                return $producto['total_promociones_activas'] == 0;
            });

            // Re-indexar el array
            $productosFiltrados = array_values($productosFiltrados);

            echo json_encode([
                'success' => true,
                'data' => $productosFiltrados,
                'total' => count($productosFiltrados),
                'message' => 'Productos disponibles para nueva promoción'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener productos: ' . $e->getMessage()
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

            // Obtener datos de la promoción
            $promocion = $this->promocionModel->obtener($id);

            if (!$promocion) {
                throw new Exception('Promoción no encontrada');
            }

            // Obtener TODOS los productos con su estado
            $todosProductos = $this->promocionModel->obtenerTodosProductosConEstado($id);

            // Separar en categorías para facilitar el manejo en el frontend
            $productosEnEstaPromocion = [];
            $productosEnOtrasPromociones = [];
            $productosDisponibles = [];

            foreach ($todosProductos as $producto) {
                switch ($producto['estado_promocion']) {
                    case 'en_esta_promocion':
                        $productosEnEstaPromocion[] = $producto;
                        break;
                    case 'en_otra_promocion':
                        $productosEnOtrasPromociones[] = $producto;
                        break;
                    default:
                        $productosDisponibles[] = $producto;
                        break;
                }
            }

            echo json_encode([
                'success' => true,
                'data' => $promocion,
                'productos' => [
                    'todos' => $todosProductos,
                    'en_esta_promocion' => $productosEnEstaPromocion,
                    'en_otras_promociones' => $productosEnOtrasPromociones,
                    'disponibles' => $productosDisponibles
                ],
                'contadores' => [
                    'total' => count($todosProductos),
                    'en_esta' => count($productosEnEstaPromocion),
                    'en_otras' => count($productosEnOtrasPromociones),
                    'disponibles' => count($productosDisponibles)
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // 🔹 API: REGISTRAR NUEVA PROMOCIÓN
    // 🔹 API: REGISTRAR NUEVA PROMOCIÓN - CORREGIR VALIDACIÓN
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
                'valor_descuento' => $_POST['valor_descuento'] ?? 0,  // Default a 0
                'fecha_inicio' => $_POST['fecha_inicio'],
                'fecha_fin' => $_POST['fecha_fin'],
                'activa' => isset($_POST['activa']) ? 1 : 0,
                'max_usos' => $_POST['max_usos'] ?? null
            ];

            // Validaciones específicas
            if ($datos['fecha_inicio'] > $datos['fecha_fin']) {
                throw new Exception('La fecha de inicio no puede ser posterior a la fecha de fin');
            }

            // VALIDACIÓN CORREGIDA PARA ENVÍO GRATIS
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

                // VERIFICAR SI LOS PRODUCTOS YA ESTÁN EN OTRAS PROMOCIONES
                if (!empty($productos)) {
                    $productosEnOtrasPromociones = $this->promocionModel->obtenerProductosEnPromocionesActivas();

                    // Filtrar productos que están en otras promociones
                    $productosConflicto = array_intersect($productos, $productosEnOtrasPromociones);

                    if (!empty($productosConflicto)) {
                        // Obtener nombres de productos en conflicto
                        $nombresProductos = $this->productoModel->obtenerNombresPorIds($productosConflicto);
                        $listaNombres = implode(', ', $nombresProductos);

                        throw new Exception("Los siguientes productos ya están en otras promociones activas: " . $listaNombres);
                    }
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
    // app/controllers/PromocionController.php - ACTUALIZA actualizarPromocion()

    public function actualizarPromocion()
    {
        header('Content-Type: application/json');

        error_log("📥 Iniciando actualizarPromocion");
        error_log("📦 POST data: " . print_r($_POST, true));
        error_log("📦 INPUT data: " . file_get_contents('php://input'));

        try {
            // Obtener datos JSON
            $input = json_decode(file_get_contents('php://input'), true);

            // Si no hay JSON, usar POST normal
            if (!$input || json_last_error() !== JSON_ERROR_NONE) {
                error_log("⚠️ No se pudo decodificar JSON, usando POST");
                $input = $_POST;
            }

            error_log("📊 Datos recibidos: " . print_r($input, true));

            $id = intval($input['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception('ID de promoción inválido');
            }

            // Validar datos requeridos
            $camposRequeridos = ['nombre', 'tipo', 'fecha_inicio', 'fecha_fin'];
            foreach ($camposRequeridos as $campo) {
                if (empty($input[$campo])) {
                    throw new Exception("El campo '$campo' es obligatorio");
                }
            }

            $datos = [
                'nombre' => trim($input['nombre']),
                'descripcion' => trim($input['descripcion'] ?? ''),
                'tipo' => $input['tipo'],
                'valor_descuento' => isset($input['valor_descuento']) ? floatval($input['valor_descuento']) : 0,
                'fecha_inicio' => $input['fecha_inicio'],
                'fecha_fin' => $input['fecha_fin'],
                'activa' => isset($input['activa']) ? intval($input['activa']) : 0,
                'max_usos' => !empty($input['max_usos']) ? intval($input['max_usos']) : null
            ];

            error_log("📝 Datos procesados: " . print_r($datos, true));

            // Validaciones
            if ($datos['fecha_inicio'] > $datos['fecha_fin']) {
                throw new Exception('La fecha de inicio no puede ser posterior a la fecha de fin');
            }

            if ($datos['tipo'] !== 'envio_gratis' && $datos['valor_descuento'] <= 0) {
                throw new Exception('El valor de descuento debe ser mayor a 0');
            }

            // Obtener productos seleccionados
            $productos = [];
            if ($datos['tipo'] !== 'envio_gratis') {
                if (isset($input['productos']) && is_array($input['productos'])) {
                    $productos = array_map('intval', $input['productos']);
                    error_log("📦 Productos recibidos: " . print_r($productos, true));

                    // Validar que los productos existan
                    if (!empty($productos)) {
                        $productosExistentes = $this->productoModel->verificarProductosExisten($productos);
                        if (count($productosExistentes) !== count($productos)) {
                            throw new Exception('Algunos productos no existen en el sistema');
                        }

                        // Verificar que los productos no estén en otras promociones activas
                        $productosEnOtrasPromociones = $this->promocionModel->obtenerProductosEnPromocionesActivas($id);
                        $productosConflicto = array_intersect($productos, array_column($productosEnOtrasPromociones, 'producto_id'));

                        if (!empty($productosConflicto)) {
                            $nombresProductos = $this->productoModel->obtenerNombresPorIds($productosConflicto);
                            throw new Exception("Los siguientes productos ya están en otras promociones: " . implode(', ', $nombresProductos));
                        }
                    }
                } else {
                    error_log("⚠️ No se recibieron productos para promoción no-envío gratis");
                    // Permitir promociones sin productos (el usuario podría quitar todos)
                }
            }

            // Actualizar la promoción
            error_log("🔄 Ejecutando actualización en modelo...");
            $resultado = $this->promocionModel->actualizar($id, $datos, $productos);

            error_log("✅ Resultado de actualización: " . ($resultado ? 'true' : 'false'));

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Promoción actualizada exitosamente',
                    'promocion_id' => $id
                ]);
            } else {
                throw new Exception('No se pudo actualizar la promoción');
            }
        } catch (Exception $e) {
            error_log("❌ ERROR en actualizarPromocion: " . $e->getMessage());
            error_log("📝 Trace: " . $e->getTraceAsString());

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'error_details' => $e->getMessage() // Solo para depuración
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

    // 🔹 API: LISTAR PRODUCTOS PARA NUEVA PROMOCIÓN
    public function listarProductosPromocion()
    {
        header('Content-Type: application/json');

        try {
            // Obtener productos DISPONIBLES (no usados en ninguna promoción activa)
            $productosDisponibles = $this->promocionModel->obtenerProductosDisponibles();

            echo json_encode([
                'success' => true,
                'data' => $productosDisponibles,
                'message' => 'Productos disponibles para promoción'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener productos: ' . $e->getMessage()
            ]);
        }
    }

    // 🔹 API: OBTENER PRODUCTOS EN OTRAS PROMOCIONES ACTIVAS
    public function obtenerProductosEnOtrasPromociones()
    {
        header('Content-Type: application/json');

        try {
            $productos = $this->promocionModel->obtenerProductosEnPromocionesActivas();

            echo json_encode([
                'success' => true,
                'data' => $productos,
                'total' => count($productos)
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
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
