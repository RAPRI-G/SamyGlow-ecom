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

    // En el switch case, agrega estas rutas:

    case 'api-listar-eliminados':
        require_once __DIR__ . '/../app/controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->listarEliminados();
        break;

    case 'api-restaurar-producto':
        require_once __DIR__ . '/../app/controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->restaurarProducto();
        break;

    case 'api-eliminar-permanentemente-producto':
        require_once __DIR__ . '/../app/controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->eliminarPermanentemente();
        break;

    case 'api-vaciar-papelera':
        require_once __DIR__ . '/../app/controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->vaciarPapelera();
        break;

    case 'api-contar-papelera':
        require_once __DIR__ . '/../app/controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->contarPapelera();
        break;

    // En el switch case, agrega estas rutas:

    case 'api-obtener-categoria':
        require_once __DIR__ . '/../app/controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->obtenerCategoria();
        break;

    case 'api-actualizar-categoria':
        require_once __DIR__ . '/../app/controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->actualizarCategoria();
        break;

    case 'api-crear-categoria':
        require_once __DIR__ . '/../app/controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->crearCategoria();
        break;

    // =====================================
    // 🔹 GESTIÓN DE CLIENTES (VISTA PRINCIPAL)
    // =====================================
    case 'gestion-clientes':
        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?view=login");
            exit;
        }

        require_once __DIR__ . '/../app/controllers/ClienteController.php';
        $controller = new ClienteController();
        $controller->index();
        break;

    // =====================================
    // 🔹 APIs PARA GESTIÓN DE CLIENTES (AJAX)
    // =====================================
    case 'api-listar-clientes':
        require_once __DIR__ . '/../app/controllers/ClienteController.php';
        $controller = new ClienteController();
        $controller->listarClientes();
        break;

    case 'api-obtener-cliente':
        require_once __DIR__ . '/../app/controllers/ClienteController.php';
        $controller = new ClienteController();
        $controller->obtenerCliente();
        break;

    case 'api-actualizar-cliente':
        require_once __DIR__ . '/../app/controllers/ClienteController.php';
        $controller = new ClienteController();
        $controller->actualizarCliente();
        break;

    case 'api-eliminar-cliente':
        require_once __DIR__ . '/../app/controllers/ClienteController.php';
        $controller = new ClienteController();
        $controller->eliminarCliente();
        break;

    case 'api-clientes-frecuentes':
        require_once __DIR__ . '/../app/controllers/ClienteController.php';
        $controller = new ClienteController();
        $controller->clientesFrecuentes();
        break;

    case 'api-estadisticas-clientes':
        require_once __DIR__ . '/../app/controllers/ClienteController.php';
        $controller = new ClienteController();
        $controller->estadisticasClientes();
        break;

    case 'api-buscar-clientes':
        require_once __DIR__ . '/../app/controllers/ClienteController.php';
        $controller = new ClienteController();
        $controller->buscarClientes();
        break;

    case 'api-contar-clientes':
        require_once __DIR__ . '/../app/controllers/ClienteController.php';
        $controller = new ClienteController();
        $controller->contarClientes();
        break;
    // En el switch case de tu index.php, agrega esta línea:
    case 'api-registrar-cliente':
        require_once __DIR__ . '/../app/controllers/ClienteController.php';
        $controller = new ClienteController();
        $controller->apiRegistrarCliente();
        break;

    // En el switch case de tu index.php, agrega:

    // 🔹 PAPELERA DE CLIENTES
    case 'api-mover-papelera-cliente':
        require_once __DIR__ . '/../app/controllers/ClienteController.php';
        $controller = new ClienteController();
        $controller->moverPapelera();
        break;

    case 'api-restaurar-cliente':
        require_once __DIR__ . '/../app/controllers/ClienteController.php';
        $controller = new ClienteController();
        $controller->restaurarCliente();
        break;

    case 'api-eliminar-permanentemente-cliente':
        require_once __DIR__ . '/../app/controllers/ClienteController.php';
        $controller = new ClienteController();
        $controller->eliminarPermanentemente();
        break;

    case 'api-listar-papelera-clientes':
        require_once __DIR__ . '/../app/controllers/ClienteController.php';
        $controller = new ClienteController();
        $controller->listarPapelera();
        break;

    case 'api-contar-papelera-clientes':
        require_once __DIR__ . '/../app/controllers/ClienteController.php';
        $controller = new ClienteController();
        $controller->contarPapelera();
        break;

    case 'api-vaciar-papelera-clientes':
        require_once __DIR__ . '/../app/controllers/ClienteController.php';
        $controller = new ClienteController();
        $controller->vaciarPapelera();
        break;


    // En el switch case de tu index.php, agrega estas rutas:

    // 🔹 GESTIÓN DE PROMOCIONES
    case 'gestion-promociones':
        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?view=login");
            exit;
        }
        require_once __DIR__ . '/../app/controllers/PromocionController.php';
        $controller = new PromocionController();
        $controller->index();
        break;

    // 🔹 APIs DE PROMOCIONES
    case 'api-listar-promociones':
        require_once __DIR__ . '/../app/controllers/PromocionController.php';
        $controller = new PromocionController();
        $controller->listarPromociones();
        break;

    case 'api-obtener-promocion':
        require_once __DIR__ . '/../app/controllers/PromocionController.php';
        $controller = new PromocionController();
        $controller->obtenerPromocion();
        break;

    case 'api-registrar-promocion':
        require_once __DIR__ . '/../app/controllers/PromocionController.php';
        $controller = new PromocionController();
        $controller->registrarPromocion();
        break;

    case 'api-actualizar-promocion':
        require_once __DIR__ . '/../app/controllers/PromocionController.php';
        $controller = new PromocionController();
        $controller->actualizarPromocion();
        break;

    case 'api-eliminar-promocion':
        require_once __DIR__ . '/../app/controllers/PromocionController.php';
        $controller = new PromocionController();
        $controller->eliminarPromocion();
        break;

    case 'api-promociones-activas':
        require_once __DIR__ . '/../app/controllers/PromocionController.php';
        $controller = new PromocionController();
        $controller->obtenerPromocionesActivas();
        break;

    case 'api-productos-promocion':
        require_once __DIR__ . '/../app/controllers/PromocionController.php';
        $controller = new PromocionController();
        $controller->obtenerProductosPromocion();
        break;

    case 'api-estadisticas-promociones':
        require_once __DIR__ . '/../app/controllers/PromocionController.php';
        $controller = new PromocionController();
        $controller->obtenerEstadisticas();
        break;

    case 'api-buscar-promociones':
        require_once __DIR__ . '/../app/controllers/PromocionController.php';
        $controller = new PromocionController();
        $controller->buscarPromociones();
        break;

    case 'api-filtrar-promociones-tipo':
        require_once __DIR__ . '/../app/controllers/PromocionController.php';
        $controller = new PromocionController();
        $controller->filtrarPromocionesTipo();
        break;

    case 'api-listar-productos-promocion':
        require_once __DIR__ . '/../app/controllers/PromocionController.php';
        $controller = new PromocionController();
        $controller->listarProductosPromocion();
        break;

    // 🔹 PAPELERA DE PROMOCIONES
    case 'api-mover-papelera-promocion':
        require_once __DIR__ . '/../app/controllers/PromocionController.php';
        $controller = new PromocionController();
        $controller->moverPapelera();
        break;

    case 'api-restaurar-promocion':
        require_once __DIR__ . '/../app/controllers/PromocionController.php';
        $controller = new PromocionController();
        $controller->restaurarPromocion();
        break;

    case 'api-eliminar-permanentemente-promocion':
        require_once __DIR__ . '/../app/controllers/PromocionController.php';
        $controller = new PromocionController();
        $controller->eliminarPermanentemente();
        break;

    case 'api-listar-eliminadas-promociones':
        require_once __DIR__ . '/../app/controllers/PromocionController.php';
        $controller = new PromocionController();
        $controller->listarEliminadas();
        break;
        
    case 'api-productos-disponibles-promocion':
        require_once __DIR__ . '/../app/controllers/PromocionController.php';
        $controller = new PromocionController();
        $controller->productosDisponiblesPromocion();
        break;

    case 'api-contar-papelera-promociones':
        require_once __DIR__ . '/../app/controllers/PromocionController.php';
        $controller = new PromocionController();
        $controller->contarPapelera();
        break;

    case 'api-vaciar-papelera-promociones':
        require_once __DIR__ . '/../app/controllers/PromocionController.php';
        $controller = new PromocionController();
        $controller->vaciarPapelera();
        break;

    case 'api-obtener-productos-en-otras-promociones':
        require_once __DIR__ . '/../app/controllers/PromocionController.php';
        $controller = new PromocionController();
        $controller->obtenerProductosEnOtrasPromociones();
        break;

    // En el switch case, agrega estas rutas:

    // 🔹 GESTIÓN DE MÉTODOS DE PAGO
    case 'gestion-metodos-pago':
        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?view=login");
            exit;
        }
        require_once __DIR__ . '/../app/controllers/MetodoPagoController.php';
        $controller = new MetodoPagoController();
        $controller->index();
        break;

    // 🔹 APIs DE MÉTODOS DE PAGO
    case 'api-listar-metodos-pago':
        require_once __DIR__ . '/../app/controllers/MetodoPagoController.php';
        $controller = new MetodoPagoController();
        $controller->listarMetodos();
        break;

    case 'api-estadisticas-metodos-pago':
        require_once __DIR__ . '/../app/controllers/MetodoPagoController.php';
        $controller = new MetodoPagoController();
        $controller->obtenerEstadisticas();
        break;


    case 'api-crear-metodo-pago':
        require_once __DIR__ . '/../app/controllers/MetodoPagoController.php';
        $controller = new MetodoPagoController();
        $controller->crearMetodo();
        break;

    case 'api-actualizar-metodo-pago':
        require_once __DIR__ . '/../app/controllers/MetodoPagoController.php';
        $controller = new MetodoPagoController();
        $controller->actualizarMetodo();
        break;

    case 'api-configuracion-metodos-pago':
        require_once __DIR__ . '/../app/controllers/MetodoPagoController.php';
        $controller = new MetodoPagoController();
        $controller->obtenerConfiguracion();
        break;

    case 'api-actualizar-configuracion-metodos':
        require_once __DIR__ . '/../app/controllers/MetodoPagoController.php';
        $controller = new MetodoPagoController();
        $controller->actualizarConfiguracion();
        break;
    // En el switch case de tu index.php, agrega:

    case 'api-eliminar-metodo-pago':
        require_once __DIR__ . '/../app/controllers/MetodoPagoController.php';
        $controller = new MetodoPagoController();
        $controller->eliminarMetodo();
        break;
    // En tu public/index.php, agrega estas rutas si no las tienes:

    case 'reportes-analytics':
        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?view=login");
            exit;
        }

        require_once __DIR__ . '/../app/controllers/ReporteController.php';
        $controller = new ReporteController();
        $controller->index();
        break;

    case 'api-reporte-ventas':
        require_once __DIR__ . '/../app/controllers/ReporteController.php';
        $controller = new ReporteController();
        $controller->reporteVentas();
        break;

    case 'api-reporte-productos':
        require_once __DIR__ . '/../app/controllers/ReporteController.php';
        $controller = new ReporteController();
        $controller->reporteProductos();
        break;

    case 'api-reporte-clientes':
        require_once __DIR__ . '/../app/controllers/ReporteController.php';
        $controller = new ReporteController();
        $controller->reporteClientes();
        break;

    case 'api-reporte-inventario':
        require_once __DIR__ . '/../app/controllers/ReporteController.php';
        $controller = new ReporteController();
        $controller->reporteInventario();
        break;

    // 🔹 CONFIGURACIÓN DEL SISTEMA
    case 'configuracion':
        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?view=login");
            exit;
        }
        require_once __DIR__ . '/../app/controllers/ConfiguracionController.php';
        $controller = new ConfiguracionController();
        $controller->index();
        break;

    // 🔹 APIS PARA GESTIÓN DE USUARIOS ADMIN
    case 'api-crear-usuario':
        if (!isset($_SESSION['usuario'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            exit;
        }
        require_once __DIR__ . '/../app/controllers/ConfiguracionController.php';
        $controller = new ConfiguracionController();
        $controller->apiCrearUsuario();
        break;

    case 'api-listar-usuarios':
        if (!isset($_SESSION['usuario'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            exit;
        }
        require_once __DIR__ . '/../app/controllers/ConfiguracionController.php';
        $controller = new ConfiguracionController();
        $controller->apiListarUsuarios();
        break;

    case 'api-actualizar-usuario':
        if (!isset($_SESSION['usuario'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            exit;
        }
        require_once __DIR__ . '/../app/controllers/ConfiguracionController.php';
        $controller = new ConfiguracionController();
        $controller->apiActualizarUsuario();
        break;

    case 'api-eliminar-usuario':
        if (!isset($_SESSION['usuario'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            exit;
        }
        require_once __DIR__ . '/../app/controllers/ConfiguracionController.php';
        $controller = new ConfiguracionController();
        $controller->apiEliminarUsuario();
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
