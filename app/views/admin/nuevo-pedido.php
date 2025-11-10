<?php
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

$title = "Nuevo Pedido - SamyGlow";
$pageTitle = "Nuevo Pedido";

require __DIR__ . "/../templates/header.php";
?>

<style>
    /* Estilos para los tabs */
    .tab-button {
        transition: all 0.3s ease;
        position: relative;
        border: none;
        background: none;
        cursor: pointer;
    }

    .tab-button.active {
        background-color: #f472b6;
        color: white;
        font-weight: 600;
    }

    .tab-button.active:hover {
        background-color: #ec4899;
    }

    .tab-button:not(.active):hover {
        background-color: #f9fafb;
    }

    .tab-panel {
        display: none;
        animation: fadeIn 0.3s ease;
    }

    .tab-panel.active {
        display: block;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Estilos para la tabla de pedidos */
    .table-container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .table-header {
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem 1.5rem;
    }

    .table-content {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th {
        background-color: #f8fafc;
        padding: 0.75rem 1.5rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 600;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #e5e7eb;
    }

    .table td {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f3f4f6;
        white-space: nowrap;
    }

    .table tbody tr:hover {
        background-color: #f9fafb;
    }

    /* Badges y estados */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-pending {
        background-color: #fef3c7;
        color: #92400e;
    }

    .status-delivered {
        background-color: #d1fae5;
        color: #065f46;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .badge-success {
        background-color: #d1fae5;
        color: #065f46;
    }

    .badge-warning {
        background-color: #fef3c7;
        color: #92400e;
    }

    .badge-danger {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .badge-info {
        background-color: #dbeafe;
        color: #1e40af;
    }


    /* Estilos para el modal */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background-color: white;
        border-radius: 12px;
        width: 100%;
        max-width: 700px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        animation: modalSlideIn 0.3s ease;
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-20px) scale(0.95);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Estilos para submenús */
    .submenu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }

    .submenu.open {
        max-height: 500px;
    }

    .menu-item {
        transition: all 0.3s ease;
    }

    .menu-item:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .rotate-180 {
        transform: rotate(180deg);
    }

    .fade-in {
        animation: fadeIn 0.3s ease;
    }

    /* Estilos para el menú de exportación */
    .export-menu {
        transform-origin: top right;
        transition: all 0.2s ease;
    }

    .export-menu.show {
        display: block;
        animation: fadeInScale 0.2s ease;
    }

    @keyframes fadeInScale {
        from {
            opacity: 0;
            transform: scale(0.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }
</style>
<!-- Main Content -->


<main class="flex-1 overflow-y-auto p-6">
    <!-- Sección de Ventas y Pedidos -->
    <div class="content-section active" id="ventas-pedidos">
        <!-- Tabs de navegación -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="flex border-b">
                <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-600 hover:bg-gray-50 active" data-tab="nuevo-pedido">
                    <i class="fas fa-plus-circle mr-2"></i>Nuevo Pedido
                </button>
                <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-600 hover:bg-gray-50" data-tab="pedidos-pendientes">
                    <i class="fas fa-clock mr-2"></i>Pedidos Pendientes
                    <span class="ml-2 bg-red-500 text-white text-xs rounded-full px-2 py-1 transition-colors" id="pending-badge">
                        <i class="fas fa-spinner fa-spin mr-1"></i>
                        <span id="pending-count">0</span>
                    </span>
                </button>
                <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-600 hover:bg-gray-50" data-tab="pedidos-entregados">
                    <i class="fas fa-check-circle mr-2"></i>Pedidos Entregados
                </button>
                <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-600 hover:bg-gray-50" data-tab="historial-ventas">
                    <i class="fas fa-history mr-2"></i>Historial de Ventas
                </button>
                <!-- En la sección de tabs, agrega esto después del último tab -->
                <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-600 hover:bg-gray-50" data-tab="papelera">
                    <i class="fas fa-trash mr-2"></i>Papelera
                    <span class="ml-2 bg-gray-500 text-white text-xs rounded-full px-2 py-1 transition-colors" id="papelera-badge">
                        <i class="fas fa-spinner fa-spin mr-1"></i>
                        <span id="papelera-count">0</span>
                    </span>
                </button>
            </div>
        </div>

        <!-- Contenido de las pestañas -->
        <div id="tab-content">
            <!-- Nueva pestaña: Nuevo Pedido -->
            <div class="tab-panel active" id="nuevo-pedido-panel">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold mb-4">Crear Nuevo Pedido</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Información del Cliente -->
                        <div>
                            <h3 class="text-lg font-semibold mb-3 text-pink-600">Información del Cliente</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar Cliente</label>
                                    <select id="cliente-select" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                                        <option value="">Seleccionar cliente existente</option>
                                        <!-- Los clientes se cargarán dinámicamente -->
                                    </select>
                                </div>

                                <div class="text-center text-gray-500">O</div>

                                <div id="nuevo-cliente-form">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nuevo Cliente</label>
                                    <input type="text" id="cliente-nombres" placeholder="Nombres" class="w-full border border-gray-300 rounded-md px-3 py-2 mb-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                                    <input type="text" id="cliente-apellidos" placeholder="Apellidos" class="w-full border border-gray-300 rounded-md px-3 py-2 mb-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                                    <input type="text" id="cliente-dni" placeholder="DNI (8 dígitos)" class="w-full border border-gray-300 rounded-md px-3 py-2 mb-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                                    <input type="email" id="cliente-correo" placeholder="Correo electrónico" class="w-full border border-gray-300 rounded-md px-3 py-2 mb-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                                    <input type="text" id="cliente-telefono" placeholder="Teléfono (9 dígitos)" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                                </div>
                            </div>
                        </div>

                        <!-- Productos y Promociones -->
                        <div>
                            <h3 class="text-lg font-semibold mb-3 text-pink-600">Productos y Promociones</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Seleccionar Productos</label>
                                    <select id="producto-select" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                                        <option value="">Cargando productos...</option>
                                        <!-- Los productos se cargarán dinámicamente -->
                                    </select>
                                    <button id="agregar-producto" class="mt-2 bg-pink-500 hover:bg-pink-600 text-white text-sm font-medium py-1 px-3 rounded transition-colors">
                                        <i class="fas fa-plus mr-1"></i>Agregar Producto
                                    </button>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Método de Pago</label>
                                    <select id="metodo-pago-select" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                                        <option value="">Seleccionar método...</option>
                                        <option value="1">Yape</option>
                                        <option value="2">Plin</option>
                                        <option value="3">Tarjeta de Crédito</option>
                                        <option value="4">Transferencia Bancaria</option>
                                        <option value="5">Efectivo</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notas del Pedido</label>
                                    <textarea id="notas-pedido" placeholder="Notas adicionales sobre el pedido..." class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de Productos Agregados -->
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-3 text-pink-600">Productos en el Pedido</h3>
                        <div id="lista-productos-pedido" class="bg-gray-50 rounded-lg p-4 min-h-20">
                            <p class="text-gray-500 text-center py-4">No hay productos agregados al pedido</p>
                        </div>
                    </div>

                    <!-- Resumen del Pedido -->
                    <div class="mt-6 p-4 bg-pink-50 rounded-lg border border-pink-100">
                        <h3 class="text-lg font-semibold mb-3 text-pink-700">Resumen del Pedido</h3>
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-gray-700">Subtotal: <span class="font-semibold" id="subtotal-resumen">S/0.00</span></p>
                                <p class="text-gray-700">Descuento: <span class="font-semibold" id="descuento-resumen">S/0.00</span></p>
                                <p class="text-lg font-bold text-pink-600">Total: <span id="total-resumen">S/0.00</span></p>
                            </div>
                            <button id="guardar-pedido" class="bg-pink-500 hover:bg-pink-600 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                                <i class="fas fa-save mr-2"></i>Guardar Pedido
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Otras pestañas (simplificadas por ahora) -->
            <div class="tab-panel" id="pedidos-pendientes-panel">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-4 border-b flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Pedidos Pendientes</h2>
                            <p class="text-gray-600"><span id="pending-count-text">0</span> pedidos pendientes de entrega</p>
                        </div>
                        <button class="bg-pink-500 hover:bg-pink-600 text-white font-medium py-2 px-4 rounded-lg transition-colors"
                            onclick="cargarPedidosPendientes()">
                            <i class="fas fa-sync-alt mr-2"></i>Actualizar
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método Pago</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="tabla-pedidos-pendientes">
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        <i class="fas fa-spinner fa-spin text-2xl mb-3 text-pink-500"></i>
                                        <p>Cargando pedidos pendientes...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pestaña: Pedidos Entregados -->
            <div class="tab-panel" id="pedidos-entregados-panel">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-4 border-b flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">✅ Pedidos Entregados</h2>
                            <p class="text-gray-600">Historial de pedidos completados exitosamente</p>
                        </div>
                        <button class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg transition-colors"
                            onclick="cargarPedidosEntregados()">
                            <i class="fas fa-sync-alt mr-2"></i>Actualizar
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método Pago</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="tabla-pedidos-entregados">
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        <i class="fas fa-spinner fa-spin text-2xl mb-3 text-green-500"></i>
                                        <p>Cargando pedidos entregados...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pestaña: Historial de Ventas -->
            <div class="tab-panel" id="historial-ventas-panel">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-4 border-b flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">📊 Historial de Ventas</h2>
                            <p class="text-gray-600">Todos los pedidos registrados en el sistema</p>
                        </div>
                        <div class="flex space-x-3">
                            <button class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition-colors"
                                onclick="cargarHistorialVentas()">
                                <i class="fas fa-sync-alt mr-2"></i>Actualizar
                            </button>
                            <!-- Botón de exportación mejorado -->
                            <div class="relative inline-block">
                                <button class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center"
                                        onclick="toggleExportMenu()">
                                    <i class="fas fa-download mr-2"></i>Exportar
                                    <i class="fas fa-chevron-down ml-2 text-xs"></i>
                                </button>
                                
                                <!-- Menú desplegable -->
                                <div id="export-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50 hidden export-menu">
                                    <div class="py-1">
                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                                           onclick="exportarHistorialVentas('csv')">
                                           <i class="fas fa-file-csv mr-2 text-green-500"></i>Exportar a CSV
                                        </a>
                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                                           onclick="exportarHistorialVentas('excel')">
                                           <i class="fas fa-file-excel mr-2 text-green-600"></i>Exportar a Excel
                                        </a>
                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                                           onclick="exportarHistorialVentas('pdf')">
                                           <i class="fas fa-file-pdf mr-2 text-red-500"></i>Exportar a PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descuento</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="tabla-historial-ventas">
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                        <i class="fas fa-spinner fa-spin text-2xl mb-3 text-blue-500"></i>
                                        <p>Cargando historial de ventas...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Pestaña: Papelera -->
            <div class="tab-panel" id="papelera-panel">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-4 border-b flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">🗑️ Papelera</h2>
                            <p class="text-gray-600"><span id="papelera-count-text">0</span> pedidos eliminados</p>
                        </div>
                        <div class="flex space-x-3">
                            <button class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors"
                                onclick="cargarPapelera()">
                                <i class="fas fa-sync-alt mr-2"></i>Actualizar
                            </button>
                            <button class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg transition-colors"
                                onclick="vaciarPapelera()">
                                <i class="fas fa-broom mr-2"></i>Vaciar Papelera
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Eliminación</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado Original</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="tabla-papelera">
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        <i class="fas fa-spinner fa-spin text-2xl mb-3 text-gray-500"></i>
                                        <p>Cargando papelera...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
</div>

<!-- Modal para notificaciones -->
<div id="notification" class="fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 hidden">
    <div class="flex items-center">
        <i class="fas fa-info-circle mr-2"></i>
        <span id="notification-message"></span>
    </div>
</div>

<script>
    // Variables globales
    let productosEnPedido = [];
    let productos = [];
    let clientes = [];
    let pedidoActual = {
        cliente_id: null,
        metodo_pago_id: null,
        notas: "",
        es_nuevo_cliente: false
    };

    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 Inicializando aplicación...');
        inicializarApp();
        cargarProductos();
        cargarClientes();

        // ✅ CARGAR CONTADOR AUTOMÁTICAMENTE AL INICIAR
        cargarContadorPedidosPendientes();

        // ✅ CARGAR CONTADOR DE PAPELERA
        cargarContadorPapelera();

        // Si ya estamos en alguna pestaña, cargar los datos
        const tabActiva = document.querySelector('.tab-button.active');
        if (tabActiva) {
            const tabId = tabActiva.getAttribute('data-tab');
            if (tabId === 'pedidos-pendientes') {
                cargarPedidosPendientes();
            } else if (tabId === 'papelera') {
                cargarPapelera();
            }
        }
    });

    // Nueva función para cargar contador de papelera
    async function cargarContadorPapelera() {
        try {
            const response = await fetch('index.php?view=api-pedidos-eliminados');
            const result = await response.json();

            if (result.success && Array.isArray(result.data)) {
                actualizarContadoresPapelera(result.data.length);
            }
        } catch (error) {
            console.error('Error cargando contador de papelera:', error);
        }
    }

    function inicializarApp() {
        // Configurar navegación y menús desplegables
        document.querySelectorAll('.menu-group > .menu-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const submenu = this.nextElementSibling;
                const icon = this.querySelector('.fa-chevron-down');

                if (submenu.classList.contains('submenu')) {
                    submenu.classList.toggle('open');
                    icon.classList.toggle('rotate-180');
                }
            });
        });

        // Navegación desde el submenú de Ventas y Pedidos
        document.querySelectorAll('.submenu .menu-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();

                const target = this.getAttribute('data-target');

                // Activar el tab correspondiente
                document.querySelectorAll('.tab-button').forEach(btn => {
                    btn.classList.remove('active');
                    if (btn.getAttribute('data-tab') === target) {
                        btn.classList.add('active');
                    }
                });

                // Mostrar el panel correspondiente
                document.querySelectorAll('.tab-panel').forEach(panel => {
                    panel.classList.remove('active');
                });
                document.getElementById(target + '-panel').classList.add('active');

                // ✅ SI ES PEDIDOS PENDIENTES, CARGAR CONTADOR Y LISTA
                if (target === 'pedidos-pendientes') {
                    console.log('🔄 Navegando a pedidos pendientes...');
                    cargarContadorPedidosPendientes();
                    cargarPedidosPendientes();
                }
            });
        });

        // Tabs
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active'));
                const tabId = this.getAttribute('data-tab');
                document.getElementById(tabId + '-panel').classList.add('active');

                // ✅ CARGAR DATOS CUANDO SE CAMBIA DE PESTAÑA
                if (tabId === 'pedidos-pendientes') {
                    console.log('🔄 Cambiando a pestaña de pedidos pendientes...');
                    cargarContadorPedidosPendientes();
                    cargarPedidosPendientes();
                } else if (tabId === 'pedidos-entregados') {
                    console.log('✅ Cambiando a pestaña de pedidos entregados...');
                    cargarPedidosEntregados();
                } else if (tabId === 'historial-ventas') {
                    console.log('📊 Cambiando a pestaña de historial de ventas...');
                    cargarHistorialVentas();
                } else if (tabId === 'papelera') {
                    console.log('🗑️ Cambiando a pestaña de papelera...');
                    cargarPapelera();
                }
            });
        });

        configurarEventListeners();
    }

    function configurarEventListeners() {
        // Agregar producto al pedido
        document.getElementById('agregar-producto').addEventListener('click', agregarProductoAlPedido);

        // Guardar pedido
        document.getElementById('guardar-pedido').addEventListener('click', guardarPedido);

        // Cambio en selección de cliente
        document.getElementById('cliente-select').addEventListener('change', function() {
            if (this.value) {
                pedidoActual.cliente_id = this.value;
                pedidoActual.es_nuevo_cliente = false;
            }
        });

        // Cambio en selección de método de pago
        document.getElementById('metodo-pago-select').addEventListener('change', function() {
            if (this.value) {
                pedidoActual.metodo_pago_id = this.value;
            }
        });

        // Cambio en notas del pedido
        document.getElementById('notas-pedido').addEventListener('input', function() {
            pedidoActual.notas = this.value;
        });
    }

    // =====================================
    // 🔹 FUNCIONES PARA CARGAR DATOS
    // =====================================

    async function cargarProductos() {
        try {
            console.log('🔍 Cargando productos...');
            const response = await fetch('index.php?view=api-productos');
            console.log('📦 Respuesta recibida:', response);

            const data = await response.json();
            console.log('📊 Datos de productos:', data);

            if (data && Array.isArray(data)) {
                productos = data;
                console.log(`✅ ${productos.length} productos cargados`);
                actualizarSelectProductos();
            } else {
                console.error('❌ Error: data no es un array', data);
                mostrarNotificacion('Error al cargar productos', 'error');
            }
        } catch (error) {
            console.error('❌ Error de conexión:', error);
            mostrarNotificacion('Error de conexión al cargar productos', 'error');
        }
    }

    async function cargarClientes() {
        try {
            const response = await fetch('index.php?view=api-clientes&q=');
            const data = await response.json();

            if (data && Array.isArray(data)) {
                clientes = data;
                actualizarSelectClientes();
            }
        } catch (error) {
            console.error('Error al cargar clientes:', error);
        }
    }

    async function cargarContadorPedidosPendientes() {
        try {
            console.log('🔢 Cargando contador de pedidos pendientes...');

            const response = await fetch('index.php?view=api-contar-pedidos-pendientes');
            const result = await response.json();

            if (result.success) {
                actualizarContadores(result.total);
                console.log(`✅ Contador actualizado: ${result.total} pedidos pendientes`);
            } else {
                console.error('❌ Error cargando contador:', result.error);
            }
        } catch (error) {
            console.error('❌ Error de conexión al cargar contador:', error);
        }
    }

    async function cargarPedidosPendientes() {
        const tabla = document.getElementById('tabla-pedidos-pendientes');

        try {
            console.log('📋 Cargando pedidos pendientes...');

            // Mostrar loading
            tabla.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-8">
                    <div class="flex flex-col items-center justify-center text-gray-500">
                        <i class="fas fa-spinner fa-spin text-2xl mb-3 text-pink-500"></i>
                        <p>Cargando pedidos pendientes...</p>
                    </div>
                </td>
            </tr>
        `;

            const response = await fetch('index.php?view=api-pedidos-pendientes');
            const result = await response.json();

            console.log('📦 Respuesta de pedidos pendientes:', result);

            if (result.success && Array.isArray(result.data)) {
                actualizarTablaPedidosPendientes(result.data);
                actualizarContadores(result.data.length);
            } else {
                throw new Error(result.error || 'Error desconocido');
            }

        } catch (error) {
            console.error('❌ Error cargando pedidos pendientes:', error);

            tabla.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-8">
                    <div class="flex flex-col items-center justify-center text-red-500">
                        <i class="fas fa-exclamation-triangle text-2xl mb-3"></i>
                        <p>Error al cargar pedidos pendientes</p>
                        <p class="text-sm text-gray-500">${error.message}</p>
                    </div>
                </td>
            </tr>
        `;

            mostrarNotificacion('Error al cargar pedidos pendientes', 'error');
        }
    }

    // =====================================
    // 🔹 FUNCIONES DE ACTUALIZACIÓN UI
    // =====================================

    function actualizarSelectProductos() {
        const select = document.getElementById('producto-select');

        if (!select) {
            console.error('❌ No se encontró el select de productos');
            return;
        }

        console.log('🔄 Actualizando select con', productos.length, 'productos');

        // Limpiar select completamente
        select.innerHTML = '';

        // Agregar opción por defecto
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = 'Seleccionar producto...';
        defaultOption.disabled = true;
        defaultOption.selected = true;
        select.appendChild(defaultOption);

        if (productos.length === 0) {
            const noProductsOption = document.createElement('option');
            noProductsOption.value = '';
            noProductsOption.textContent = 'No hay productos disponibles';
            select.appendChild(noProductsOption);
            return;
        }

        // Crear grupos por categoría
        const categorias = {
            1: {
                nombre: '🌟 Fragancias',
                productos: []
            },
            2: {
                nombre: '🧴 Cremas Corporales',
                productos: []
            },
            3: {
                nombre: '💦 Body Splash',
                productos: []
            }
        };

        // Agrupar productos por categoría
        productos.forEach(producto => {
            const categoriaId = producto.categoria_id;

            if (categorias[categoriaId]) {
                categorias[categoriaId].productos.push(producto);
            } else {
                if (!categorias.otros) {
                    categorias.otros = {
                        nombre: '📦 Otros',
                        productos: []
                    };
                }
                categorias.otros.productos.push(producto);
            }
        });

        // Crear optgroups
        const ordenCategorias = [1, 2, 3, 'otros'];

        ordenCategorias.forEach(categoriaId => {
            if (categorias[categoriaId] && categorias[categoriaId].productos.length > 0) {
                const categoria = categorias[categoriaId];
                const optgroup = document.createElement('optgroup');
                optgroup.label = categoria.nombre;

                // Ordenar productos alfabéticamente
                categoria.productos.sort((a, b) => a.nombre.localeCompare(b.nombre));

                categoria.productos.forEach(producto => {
                    const option = document.createElement('option');
                    option.value = producto.id;
                    option.textContent = `${producto.nombre} - S/${parseFloat(producto.precio).toFixed(2)}`;
                    option.setAttribute('data-precio', producto.precio);
                    option.setAttribute('data-stock', producto.stock);
                    optgroup.appendChild(option);
                });

                select.appendChild(optgroup);
            }
        });

        console.log('✅ Select de productos actualizado correctamente');
    }

    function actualizarSelectClientes() {
        const select = document.getElementById('cliente-select');

        // Limpiar opciones existentes (excepto la primera)
        while (select.options.length > 1) {
            select.remove(1);
        }

        // Agregar clientes
        clientes.forEach(cliente => {
            const option = document.createElement('option');
            option.value = cliente.id;
            option.textContent = `${cliente.nombres} ${cliente.apellidos} (${cliente.dni})`;
            select.appendChild(option);
        });
    }

    function actualizarTablaPedidosPendientes(pedidos) {
        const tabla = document.getElementById('tabla-pedidos-pendientes');

        if (pedidos.length === 0) {
            tabla.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-12">
                    <div class="flex flex-col items-center justify-center text-gray-400">
                        <i class="fas fa-check-circle text-4xl mb-4 text-green-300"></i>
                        <p class="text-lg font-medium text-gray-600">¡No hay pedidos pendientes!</p>
                        <p class="text-sm">Todos los pedidos han sido entregados exitosamente</p>
                    </div>
                </td>
            </tr>
        `;
            return;
        }

        let html = '';
        pedidos.forEach(pedido => {
            const fecha = new Date(pedido.fecha).toLocaleDateString('es-PE', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            html += `
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="py-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-pink-100 text-pink-800">
                    #${pedido.id}
                </span>
            </td>
            <td class="py-4">
                <div>
                    <p class="font-semibold text-gray-900">${pedido.cliente_nombre || 'Cliente no encontrado'}</p>
                    ${pedido.notas ? `<p class="text-sm text-gray-500 mt-1">${pedido.notas}</p>` : ''}
                </div>
            </td>
            <td class="py-4 text-sm text-gray-600">
                ${fecha}
            </td>
            <td class="py-4">
                <span class="font-bold text-green-600">S/${parseFloat(pedido.total).toFixed(2)}</span>
            </td>
            <td class="py-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <i class="fas fa-credit-card mr-1"></i>
                    ${pedido.metodo_pago_nombre || 'No especificado'}
                </span>
            </td>
            <td class="py-4">
                <div class="flex space-x-2">
                    <button onclick="marcarComoEntregado(${pedido.id})" 
                            class="inline-flex items-center px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-lg transition-colors">
                        <i class="fas fa-check mr-1"></i>Entregado
                    </button>
                    <button onclick="verDetallesPedido(${pedido.id})" 
                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-eye mr-1"></i>Ver
                    </button>
                    <button onclick="eliminarPedido(${pedido.id})" 
                            class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg transition-colors">
                        <i class="fas fa-trash mr-1"></i>Eliminar
                    </button>
                </div>
            </td>
        </tr>
        `;
        });

        tabla.innerHTML = html;
    }

    function actualizarContadores(cantidad) {
        // Actualizar TODOS los contadores en la página
        const contadores = [
            'pending-badge', // En el tab
            'pending-count', // En el sidebar  
            'pending-count-text' // En el contenido
        ];

        contadores.forEach(id => {
            const elemento = document.getElementById(id);
            if (elemento) {
                // Solo actualizar el texto, no el HTML completo
                const span = elemento.querySelector('span') || elemento;
                span.textContent = cantidad;

                // Si es 0, cambiar el color a verde
                if (cantidad === 0) {
                    elemento.classList.remove('bg-red-500');
                    elemento.classList.add('bg-green-500');
                } else {
                    elemento.classList.remove('bg-green-500');
                    elemento.classList.add('bg-red-500');
                }
            }
        });

        console.log(`🔢 ${cantidad} pedidos pendientes - Contadores actualizados`);
    }

    // =====================================
    // 🔹 FUNCIONES DE NUEVO PEDIDO
    // =====================================

    function agregarProductoAlPedido() {
        console.log('🛒 Intentando agregar producto...');

        const productoSelect = document.getElementById('producto-select');
        const productoId = productoSelect.value;

        if (!productoId || productoId === '') {
            mostrarNotificacion('❌ Por favor selecciona un producto de la lista', 'error');
            return;
        }

        // Buscar el producto en la lista
        const producto = productos.find(p => p.id == productoId);

        if (!producto) {
            mostrarNotificacion('❌ Producto no encontrado en la base de datos', 'error');
            return;
        }

        // Verificar stock
        if (producto.stock <= 0) {
            mostrarNotificacion(`❌ ${producto.nombre} sin stock disponible`, 'error');
            return;
        }

        // Verificar si el producto ya está en el pedido
        const productoExistente = productosEnPedido.find(p => p.id == productoId);

        if (productoExistente) {
            // Verificar que no exceda el stock
            if (productoExistente.cantidad >= producto.stock) {
                mostrarNotificacion(`❌ Stock insuficiente de ${producto.nombre}. Máximo: ${producto.stock}`, 'error');
                return;
            }

            // Incrementar cantidad
            productoExistente.cantidad += 1;
            productoExistente.subtotal = productoExistente.precio * productoExistente.cantidad;
        } else {
            // Agregar nuevo producto
            const nuevoProducto = {
                id: parseInt(producto.id),
                nombre: producto.nombre,
                precio: parseFloat(producto.precio),
                cantidad: 1,
                subtotal: parseFloat(producto.precio),
                stock: producto.stock
            };

            productosEnPedido.push(nuevoProducto);
        }

        // Actualizar la lista visual
        actualizarListaProductosPedido();
        calcularTotales();
        productoSelect.selectedIndex = 0;

        mostrarNotificacion(`✅ ${producto.nombre} agregado al pedido`, 'success');
    }

    function actualizarListaProductosPedido() {
        const listaContainer = document.getElementById('lista-productos-pedido');

        if (productosEnPedido.length === 0) {
            listaContainer.innerHTML = '<p class="text-gray-500 text-center py-4">No hay productos agregados al pedido</p>';
            return;
        }

        let html = '';
        productosEnPedido.forEach(producto => {
            html += `
        <div class="product-item bg-white rounded-lg p-3 mb-2 shadow-sm flex justify-between items-center fade-in">
            <div>
                <h4 class="font-medium text-gray-800">${producto.nombre}</h4>
                <p class="text-sm text-gray-600">S/${producto.precio.toFixed(2)} x ${producto.cantidad} = S/${producto.subtotal.toFixed(2)}</p>
            </div>
            <div class="flex items-center space-x-2">
                <button class="text-pink-500 hover:text-pink-700" onclick="cambiarCantidadProducto(${producto.id}, -1)">
                    <i class="fas fa-minus"></i>
                </button>
                <span class="font-medium">${producto.cantidad}</span>
                <button class="text-pink-500 hover:text-pink-700" onclick="cambiarCantidadProducto(${producto.id}, 1)">
                    <i class="fas fa-plus"></i>
                </button>
                <button class="text-red-500 hover:text-red-700 ml-2" onclick="eliminarProductoDelPedido(${producto.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        `;
        });

        listaContainer.innerHTML = html;
    }

    function cambiarCantidadProducto(productoId, cambio) {
        const producto = productosEnPedido.find(p => p.id == productoId);

        if (producto) {
            producto.cantidad += cambio;

            if (producto.cantidad <= 0) {
                eliminarProductoDelPedido(productoId);
            } else {
                producto.subtotal = producto.precio * producto.cantidad;
                actualizarListaProductosPedido();
                calcularTotales();
            }
        }
    }

    function eliminarProductoDelPedido(productoId) {
        productosEnPedido = productosEnPedido.filter(p => p.id != productoId);
        actualizarListaProductosPedido();
        calcularTotales();
        mostrarNotificacion('Producto eliminado del pedido', 'info');
    }

    function calcularTotales() {
        let subtotal = 0;
        productosEnPedido.forEach(producto => {
            subtotal += producto.subtotal;
        });

        const total = subtotal;

        document.getElementById('subtotal-resumen').textContent = `S/${subtotal.toFixed(2)}`;
        document.getElementById('descuento-resumen').textContent = `S/0.00`;
        document.getElementById('total-resumen').textContent = `S/${total.toFixed(2)}`;
    }

    async function guardarPedido() {
        if (!validarCliente()) return;
        if (productosEnPedido.length === 0) {
            mostrarNotificacion('El pedido debe contener al menos un producto', 'error');
            return;
        }
        if (!pedidoActual.metodo_pago_id) {
            mostrarNotificacion('Por favor selecciona un método de pago', 'error');
            return;
        }

        // Crear nuevo cliente si es necesario
        if (pedidoActual.es_nuevo_cliente) {
            const clienteCreado = await crearNuevoCliente();
            if (!clienteCreado) return;
        }

        // Preparar datos para enviar
        const pedidoData = {
            cliente: pedidoActual.cliente_id,
            payment: pedidoActual.metodo_pago_id,
            notes: pedidoActual.notas,
            items: productosEnPedido.map(p => ({
                producto_id: p.id,
                cantidad: p.cantidad
            }))
        };

        try {
            const response = await fetch('index.php?view=api-save-pedido', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(pedidoData)
            });

            const result = await response.json();

            if (result.ok) {
                mostrarNotificacion(`Pedido #${result.pedido_id} creado exitosamente`, 'success');

                // ACTUALIZAR CONTADOR DESPUÉS DE CREAR PEDIDO
                setTimeout(() => {
                    cargarContadorPedidosPendientes();
                }, 500);

                limpiarFormularioPedido();
            } else {
                mostrarNotificacion(`Error: ${result.error}`, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarNotificacion('Error de conexión al guardar pedido', 'error');
        }
    }

    function validarCliente() {
        const clienteSelect = document.getElementById('cliente-select');

        // Si se seleccionó un cliente existente
        if (clienteSelect.value) {
            pedidoActual.cliente_id = clienteSelect.value;
            pedidoActual.es_nuevo_cliente = false;
            return true;
        }

        // Si es un nuevo cliente, validar campos
        const nombres = document.getElementById('cliente-nombres').value.trim();
        const apellidos = document.getElementById('cliente-apellidos').value.trim();
        const dni = document.getElementById('cliente-dni').value.trim();
        const correo = document.getElementById('cliente-correo').value.trim();
        const telefono = document.getElementById('cliente-telefono').value.trim();

        if (!nombres || !apellidos || !dni || !correo || !telefono) {
            mostrarNotificacion('Por favor completa todos los campos del cliente', 'error');
            return false;
        }

        if (dni.length !== 8) {
            mostrarNotificacion('El DNI debe tener 8 dígitos', 'error');
            return false;
        }

        if (telefono.length !== 9) {
            mostrarNotificacion('El teléfono debe tener 9 dígitos', 'error');
            return false;
        }

        // Verificar si el DNI ya existe en la lista local
        if (clientes.some(c => c.dni === dni)) {
            mostrarNotificacion('Ya existe un cliente con este DNI', 'error');
            return false;
        }

        pedidoActual.es_nuevo_cliente = true;
        return true;
    }

    async function crearNuevoCliente() {
        const nombres = document.getElementById('cliente-nombres').value.trim();
        const apellidos = document.getElementById('cliente-apellidos').value.trim();
        const dni = document.getElementById('cliente-dni').value.trim();
        const correo = document.getElementById('cliente-correo').value.trim();
        const telefono = document.getElementById('cliente-telefono').value.trim();

        const formData = new FormData();
        formData.append('nombres', nombres);
        formData.append('apellidos', apellidos);
        formData.append('dni', dni);
        formData.append('correo', correo);
        formData.append('telefono', telefono);

        try {
            const response = await fetch('index.php?view=api-cliente-save', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.ok) {
                // Agregar el nuevo cliente a la lista local
                clientes.push(result.cliente);
                actualizarSelectClientes();

                // Establecer como cliente seleccionado
                document.getElementById('cliente-select').value = result.id;
                pedidoActual.cliente_id = result.id;

                mostrarNotificacion('Cliente registrado exitosamente', 'success');
                return true;
            } else {
                mostrarNotificacion(`Error al registrar cliente: ${result.msg}`, 'error');
                return false;
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarNotificacion('Error de conexión al registrar cliente', 'error');
            return false;
        }
    }

    function limpiarFormularioPedido() {
        // Limpiar selecciones
        document.getElementById('cliente-select').value = '';
        document.getElementById('producto-select').value = '';
        document.getElementById('metodo-pago-select').value = '';
        document.getElementById('notas-pedido').value = '';

        // Limpiar campos de nuevo cliente
        document.getElementById('cliente-nombres').value = '';
        document.getElementById('cliente-apellidos').value = '';
        document.getElementById('cliente-dni').value = '';
        document.getElementById('cliente-correo').value = '';
        document.getElementById('cliente-telefono').value = '';

        // Limpiar datos
        productosEnPedido = [];
        pedidoActual = {
            cliente_id: null,
            metodo_pago_id: null,
            notas: "",
            es_nuevo_cliente: false
        };

        // Actualizar interfaz
        actualizarListaProductosPedido();
        calcularTotales();
    }

    // =====================================
    // 🔹 FUNCIONES DE ACCIÓN PARA PEDIDOS
    // =====================================

    async function marcarComoEntregado(pedidoId) {
        if (!confirm(`¿Estás seguro de marcar el pedido #${pedidoId} como entregado?`)) {
            return;
        }

        try {
            console.log(`🔄 Marcando pedido #${pedidoId} como entregado...`);

            const formData = new FormData();
            formData.append('pedido_id', pedidoId);

            const response = await fetch('index.php?view=api-marcar-entregado', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                mostrarNotificacion(`✅ Pedido #${pedidoId} marcado como entregado`, 'success');

                // Recargar la lista y contadores
                setTimeout(() => {
                    cargarPedidosPendientes();
                    cargarContadorPedidosPendientes();
                }, 1000);

            } else {
                throw new Error(result.error);
            }

        } catch (error) {
            console.error('❌ Error al marcar como entregado:', error);
            mostrarNotificacion(`❌ Error: ${error.message}`, 'error');
        }
    }

    async function eliminarPedido(pedidoId) {
        if (!confirm(`⚠️ ¿Estás seguro de ELIMINAR el pedido #${pedidoId}?\n\nEsta acción NO se puede deshacer y se perderán todos los datos del pedido.`)) {
            return;
        }

        try {
            console.log(`🗑️ Eliminando pedido #${pedidoId}...`);

            const formData = new FormData();
            formData.append('pedido_id', pedidoId);

            const response = await fetch('index.php?view=api-eliminar-pedido', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                mostrarNotificacion(`✅ Pedido #${pedidoId} eliminado correctamente`, 'success');

                // ✅ ACTUALIZAR CONTADOR DE PAPELERA INMEDIATAMENTE
                setTimeout(() => {
                    cargarPedidosPendientes();
                    cargarContadorPedidosPendientes();
                    cargarContadorPapelera(); // ← ESTA ES LA LÍNEA QUE FALTA
                }, 1000);

            } else {
                throw new Error(result.error);
            }

        } catch (error) {
            console.error('❌ Error al eliminar pedido:', error);
            mostrarNotificacion(`❌ Error: ${error.message}`, 'error');
        }
    }

    async function verDetallesPedido(pedidoId) {
        try {
            console.log(`👀 Cargando detalles del pedido #${pedidoId}...`);

            const response = await fetch(`index.php?view=api-detalles-pedido&id=${pedidoId}`);
            const result = await response.json();

            if (result.success) {
                mostrarModalDetalles(result.pedido, result.detalles);
            } else {
                throw new Error(result.error);
            }

        } catch (error) {
            console.error('❌ Error al cargar detalles:', error);
            mostrarNotificacion(`❌ Error: ${error.message}`, 'error');
        }
    }

    // =====================================
    // 🔹 MODAL DE DETALLES
    // =====================================

    function mostrarModalDetalles(pedido, detalles) {
        // Crear modal dinámicamente si no existe
        let modal = document.getElementById('modal-detalles');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'modal-detalles';
            modal.className = 'modal';
            modal.innerHTML = `
            <div class="modal-content">
                <div class="p-4 border-b flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">Detalles del Pedido</h3>
                    <button class="text-gray-500 hover:text-gray-700" onclick="cerrarModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-4" id="contenido-modal"></div>
            </div>
        `;
            document.body.appendChild(modal);
        }

        const contenido = document.getElementById('contenido-modal');

        const fecha = new Date(pedido.fecha).toLocaleDateString('es-PE', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        let html = `
        <div class="space-y-6">
            <!-- Encabezado -->
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Pedido #${pedido.id}</h3>
                    <p class="text-sm text-gray-500">${fecha}</p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${pedido.estado === 'pendiente' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'}">
                    ${pedido.estado === 'pendiente' ? '⏳ Pendiente' : '✅ Entregado'}
                </span>
            </div>
            
            <!-- Información del Cliente -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-2">👤 Información del Cliente</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                    <div><span class="font-medium">Nombre:</span> ${pedido.cliente_nombre}</div>
                    <div><span class="font-medium">DNI:</span> ${pedido.dni}</div>
                    <div><span class="font-medium">Correo:</span> ${pedido.correo || 'No especificado'}</div>
                    <div><span class="font-medium">Teléfono:</span> ${pedido.telefono || 'No especificado'}</div>
                </div>
            </div>
            
            <!-- Información del Pedido -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-2">💳 Información de Pago</h4>
                    <div class="space-y-1 text-sm">
                        <div><span class="font-medium">Método:</span> ${pedido.metodo_pago_nombre}</div>
                        ${pedido.promocion_nombre ? `<div><span class="font-medium">Promoción:</span> ${pedido.promocion_nombre}</div>` : ''}
                    </div>
                </div>
                
                <div class="bg-green-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-2">💰 Resumen Financiero</h4>
                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span class="font-medium">S/${parseFloat(pedido.subtotal).toFixed(2)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Descuento:</span>
                            <span class="font-medium">S/${parseFloat(pedido.descuento_promocion).toFixed(2)}</span>
                        </div>
                        <div class="flex justify-between border-t border-green-200 pt-1">
                            <span class="font-semibold">Total:</span>
                            <span class="font-bold text-green-600">S/${parseFloat(pedido.total).toFixed(2)}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Productos del Pedido -->
            <div>
                <h4 class="font-semibold text-gray-900 mb-3">🛍️ Productos</h4>
                <div class="space-y-3">
    `;

        // Agregar productos
        detalles.forEach(detalle => {
            html += `
                    <div class="flex justify-between items-center p-3 bg-white border border-gray-200 rounded-lg">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">${detalle.producto_nombre}</p>
                            <p class="text-sm text-gray-500">Cantidad: ${detalle.cantidad} x S/${parseFloat(detalle.precio_unitario).toFixed(2)}</p>
                        </div>
                        <span class="font-bold text-gray-700">S/${parseFloat(detalle.subtotal).toFixed(2)}</span>
                    </div>
        `;
        });

        html += `
                </div>
            </div>
            
            <!-- Notas -->
            ${pedido.notas ? `
            <div class="bg-yellow-50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-2">📝 Notas del Pedido</h4>
                <p class="text-sm text-gray-700">${pedido.notas}</p>
            </div>
            ` : ''}
            
            <!-- Acciones -->
            ${pedido.estado === 'pendiente' ? `
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <button onclick="marcarComoEntregado(${pedido.id}); cerrarModal();" 
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-check mr-2"></i>Marcar como Entregado
                </button>
                <button onclick="eliminarPedido(${pedido.id}); cerrarModal();" 
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-trash mr-2"></i>Eliminar Pedido
                </button>
            </div>
            ` : ''}
        </div>
    `;

        contenido.innerHTML = html;
        modal.classList.add('active');
    }

    function cerrarModal() {
        const modal = document.getElementById('modal-detalles');
        if (modal) {
            modal.classList.remove('active');
        }
    }

    // =====================================
    // 🔹 NOTIFICACIONES
    // =====================================

    function mostrarNotificacion(mensaje, tipo) {
        const notification = document.getElementById('notification');
        const message = document.getElementById('notification-message');

        if (!notification || !message) {
            console.error('❌ No se encontró el elemento de notificación');
            return;
        }

        const colors = {
            success: 'bg-green-500 text-white',
            error: 'bg-red-500 text-white',
            info: 'bg-blue-500 text-white',
            warning: 'bg-yellow-500 text-white'
        };

        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-triangle',
            info: 'fa-info-circle',
            warning: 'fa-exclamation-circle'
        };

        notification.className = 'fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ' + (colors[tipo] || colors.info);
        message.innerHTML = `<i class="fas ${icons[tipo] || icons.info} mr-2"></i> ${mensaje}`;

        notification.classList.remove('hidden');

        setTimeout(() => {
            notification.classList.add('hidden');
        }, 3000);
    }

    // =====================================
    // 🔹 FUNCIONES PARA LA PAPELERA
    // =====================================

    async function cargarPapelera() {
        const tabla = document.getElementById('tabla-papelera');

        try {
            console.log('🗑️ Cargando papelera...');

            // Mostrar loading
            tabla.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-8">
                    <div class="flex flex-col items-center justify-center text-gray-500">
                        <i class="fas fa-spinner fa-spin text-2xl mb-3 text-gray-500"></i>
                        <p>Cargando papelera...</p>
                    </div>
                </td>
            </tr>
        `;

            const response = await fetch('index.php?view=api-pedidos-eliminados');
            const result = await response.json();

            console.log('📦 Respuesta de papelera:', result);

            if (result.success && Array.isArray(result.data)) {
                actualizarTablaPapelera(result.data);
                actualizarContadoresPapelera(result.data.length);
            } else {
                throw new Error(result.error || 'Error desconocido');
            }

        } catch (error) {
            console.error('❌ Error cargando papelera:', error);

            tabla.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-8">
                    <div class="flex flex-col items-center justify-center text-red-500">
                        <i class="fas fa-exclamation-triangle text-2xl mb-3"></i>
                        <p>Error al cargar la papelera</p>
                        <p class="text-sm text-gray-500">${error.message}</p>
                    </div>
                </td>
            </tr>
        `;

            mostrarNotificacion('Error al cargar la papelera', 'error');
        }
    }

    function actualizarTablaPapelera(pedidos) {
        const tabla = document.getElementById('tabla-papelera');

        if (pedidos.length === 0) {
            tabla.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-12">
                    <div class="flex flex-col items-center justify-center text-gray-400">
                        <i class="fas fa-trash text-4xl mb-4 text-gray-300"></i>
                        <p class="text-lg font-medium text-gray-600">¡La papelera está vacía!</p>
                        <p class="text-sm">No hay pedidos eliminados</p>
                    </div>
                </td>
            </tr>
        `;
            return;
        }

        let html = '';
        pedidos.forEach(pedido => {
            const fechaEliminacion = pedido.fecha_eliminado ?
                new Date(pedido.fecha_eliminado).toLocaleDateString('es-PE', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                }) :
                'Fecha no disponible';

            const fechaOriginal = new Date(pedido.fecha).toLocaleDateString('es-PE', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });

            html += `
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="py-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                    #${pedido.id}
                </span>
            </td>
            <td class="py-4">
                <div>
                    <p class="font-semibold text-gray-900">${pedido.cliente_nombre || 'Cliente no encontrado'}</p>
                    <p class="text-sm text-gray-500">Creado: ${fechaOriginal}</p>
                </div>
            </td>
            <td class="py-4 text-sm text-gray-600">
                ${fechaEliminacion}
            </td>
            <td class="py-4">
                <span class="font-bold text-gray-600">S/${parseFloat(pedido.total).toFixed(2)}</span>
            </td>
            <td class="py-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                    pedido.estado === 'pendiente' 
                        ? 'bg-yellow-100 text-yellow-800' 
                        : 'bg-green-100 text-green-800'
                }">
                    ${pedido.estado === 'pendiente' ? '⏳ Pendiente' : '✅ Entregado'}
                </span>
            </td>
            <td class="py-4">
                <div class="flex space-x-2">
                    <button onclick="restaurarPedido(${pedido.id})" 
                            class="inline-flex items-center px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-lg transition-colors">
                        <i class="fas fa-undo mr-1"></i>Restaurar
                    </button>
                    <button onclick="verDetallesPedido(${pedido.id})" 
                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-eye mr-1"></i>Ver
                    </button>
                    <button onclick="eliminarPermanentemente(${pedido.id})" 
                            class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg transition-colors">
                        <i class="fas fa-times mr-1"></i>Eliminar
                    </button>
                </div>
            </td>
        </tr>
        `;
        });

        tabla.innerHTML = html;
    }

    function actualizarContadoresPapelera(cantidad) {
        // Actualizar contadores de papelera
        const contadores = [
            'papelera-badge',
            'papelera-count',
            'papelera-count-text'
        ];

        contadores.forEach(id => {
            const elemento = document.getElementById(id);
            if (elemento) {
                const span = elemento.querySelector('span') || elemento;
                span.textContent = cantidad;
            }
        });

        console.log(`🗑️ ${cantidad} pedidos en papelera - Contadores actualizados`);
    }

    async function restaurarPedido(pedidoId) {
        if (!confirm(`¿Estás seguro de restaurar el pedido #${pedidoId}?`)) {
            return;
        }

        try {
            console.log(`🔄 Restaurando pedido #${pedidoId}...`);

            const formData = new FormData();
            formData.append('pedido_id', pedidoId);

            const response = await fetch('index.php?view=api-restaurar-pedido', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                mostrarNotificacion(`✅ Pedido #${pedidoId} restaurado correctamente`, 'success');

                // Recargar ambas listas
                setTimeout(() => {
                    cargarPapelera();
                    cargarPedidosPendientes();
                    cargarContadorPedidosPendientes();
                }, 1000);

            } else {
                throw new Error(result.error);
            }

        } catch (error) {
            console.error('❌ Error al restaurar pedido:', error);
            mostrarNotificacion(`❌ Error: ${error.message}`, 'error');
        }
    }

    async function eliminarPermanentemente(pedidoId) {
        if (!confirm(`⚠️ ¿Estás seguro de ELIMINAR PERMANENTEMENTE el pedido #${pedidoId}?\n\n⚠️ ESTA ACCIÓN NO SE PUEDE DESHACER\n⚠️ Se perderán todos los datos del pedido para siempre`)) {
            return;
        }

        try {
            console.log(`💀 Eliminando permanentemente pedido #${pedidoId}...`);

            const formData = new FormData();
            formData.append('pedido_id', pedidoId);

            const response = await fetch('index.php?view=api-eliminar-permanentemente', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                mostrarNotificacion(`✅ Pedido #${pedidoId} eliminado permanentemente`, 'success');

                // Recargar la papelera
                setTimeout(() => {
                    cargarPapelera();
                }, 1000);

            } else {
                throw new Error(result.error);
            }

        } catch (error) {
            console.error('❌ Error al eliminar permanentemente:', error);
            mostrarNotificacion(`❌ Error: ${error.message}`, 'error');
        }
    }

    async function vaciarPapelera() {
        const response = await fetch('index.php?view=api-pedidos-eliminados');
        const result = await response.json();

        if (!result.success || !Array.isArray(result.data) || result.data.length === 0) {
            mostrarNotificacion('La papelera ya está vacía', 'info');
            return;
        }

        const cantidad = result.data.length;

        if (!confirm(`⚠️ ¿Estás seguro de VACIAR COMPLETAMENTE la papelera?\n\n⚠️ Se eliminarán PERMANENTEMENTE ${cantidad} pedidos\n⚠️ ESTA ACCIÓN NO SE PUEDE DESHACER`)) {
            return;
        }

        try {
            console.log(`🧹 Vaciamdo papelera con ${cantidad} pedidos...`);

            // Eliminar cada pedido permanentemente
            let eliminados = 0;
            for (const pedido of result.data) {
                const formData = new FormData();
                formData.append('pedido_id', pedido.id);

                const response = await fetch('index.php?view=api-eliminar-permanentemente', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                if (result.success) {
                    eliminados++;
                }
            }

            mostrarNotificacion(`✅ Papelera vaciada: ${eliminados} pedidos eliminados permanentemente`, 'success');

            // Recargar la papelera
            setTimeout(() => {
                cargarPapelera();
            }, 1000);

        } catch (error) {
            console.error('❌ Error al vaciar papelera:', error);
            mostrarNotificacion(`❌ Error al vaciar papelera: ${error.message}`, 'error');
        }
    }

    // =====================================
    // 🔹 FUNCIONES PARA PEDIDOS ENTREGADOS
    // =====================================

    async function cargarPedidosEntregados() {
        const tabla = document.getElementById('tabla-pedidos-entregados');

        try {
            console.log('✅ Cargando pedidos entregados...');

            // Mostrar loading
            tabla.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-8">
                    <div class="flex flex-col items-center justify-center text-gray-500">
                        <i class="fas fa-spinner fa-spin text-2xl mb-3 text-green-500"></i>
                        <p>Cargando pedidos entregados...</p>
                    </div>
                </td>
            </tr>
        `;

            const response = await fetch('index.php?view=api-pedidos-entregados');
            const result = await response.json();

            console.log('📦 Respuesta de pedidos entregados:', result);

            if (result.success && Array.isArray(result.data)) {
                actualizarTablaPedidosEntregados(result.data);
            } else {
                throw new Error(result.error || 'Error desconocido');
            }

        } catch (error) {
            console.error('❌ Error cargando pedidos entregados:', error);

            tabla.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-8">
                    <div class="flex flex-col items-center justify-center text-red-500">
                        <i class="fas fa-exclamation-triangle text-2xl mb-3"></i>
                        <p>Error al cargar pedidos entregados</p>
                        <p class="text-sm text-gray-500">${error.message}</p>
                    </div>
                </td>
            </tr>
        `;

            mostrarNotificacion('Error al cargar pedidos entregados', 'error');
        }
    }

    function actualizarTablaPedidosEntregados(pedidos) {
        const tabla = document.getElementById('tabla-pedidos-entregados');

        if (pedidos.length === 0) {
            tabla.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-12">
                    <div class="flex flex-col items-center justify-center text-gray-400">
                        <i class="fas fa-check-circle text-4xl mb-4 text-green-300"></i>
                        <p class="text-lg font-medium text-gray-600">¡No hay pedidos entregados!</p>
                        <p class="text-sm">Los pedidos entregados aparecerán aquí</p>
                    </div>
                </td>
            </tr>
        `;
            return;
        }

        let html = '';
        pedidos.forEach(pedido => {
            const fecha = new Date(pedido.fecha).toLocaleDateString('es-PE', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            html += `
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="py-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                    #${pedido.id}
                </span>
            </td>
            <td class="py-4">
                <div>
                    <p class="font-semibold text-gray-900">${pedido.cliente_nombre || 'Cliente no encontrado'}</p>
                    ${pedido.notas ? `<p class="text-sm text-gray-500 mt-1">${pedido.notas}</p>` : ''}
                </div>
            </td>
            <td class="py-4 text-sm text-gray-600">
                ${fecha}
            </td>
            <td class="py-4">
                <span class="font-bold text-green-600">S/${parseFloat(pedido.total).toFixed(2)}</span>
            </td>
            <td class="py-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <i class="fas fa-credit-card mr-1"></i>
                    ${pedido.metodo_pago_nombre || 'No especificado'}
                </span>
            </td>
            <td class="py-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <i class="fas fa-check mr-1"></i>
                    Entregado
                </span>
            </td>
            <td class="py-4">
                <div class="flex space-x-2">
                    <button onclick="verDetallesPedido(${pedido.id})" 
                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-eye mr-1"></i>Ver
                    </button>
                    <button onclick="moverAPapelera(${pedido.id})" 
                            class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg transition-colors">
                        <i class="fas fa-trash mr-1"></i>Eliminar
                    </button>
                </div>
            </td>
        </tr>
        `;
        });

        tabla.innerHTML = html;
    }

    // =====================================
    // 🔹 FUNCIONES PARA HISTORIAL DE VENTAS
    // =====================================

    async function cargarHistorialVentas() {
        const tabla = document.getElementById('tabla-historial-ventas');

        try {
            console.log('📊 Cargando historial de ventas...');

            // Mostrar loading
            tabla.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-8">
                    <div class="flex flex-col items-center justify-center text-gray-500">
                        <i class="fas fa-spinner fa-spin text-2xl mb-3 text-blue-500"></i>
                        <p>Cargando historial de ventas...</p>
                    </div>
                </td>
            </tr>
        `;

            const response = await fetch('index.php?view=api-historial-ventas');
            const result = await response.json();

            console.log('📦 Respuesta de historial de ventas:', result);

            if (result.success && Array.isArray(result.data)) {
                actualizarTablaHistorialVentas(result.data);
            } else {
                throw new Error(result.error || 'Error desconocido');
            }

        } catch (error) {
            console.error('❌ Error cargando historial de ventas:', error);

            tabla.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-8">
                    <div class="flex flex-col items-center justify-center text-red-500">
                        <i class="fas fa-exclamation-triangle text-2xl mb-3"></i>
                        <p>Error al cargar historial de ventas</p>
                        <p class="text-sm text-gray-500">${error.message}</p>
                    </div>
                </td>
            </tr>
        `;

            mostrarNotificacion('Error al cargar historial de ventas', 'error');
        }
    }

    function actualizarTablaHistorialVentas(pedidos) {
        const tabla = document.getElementById('tabla-historial-ventas');

        if (pedidos.length === 0) {
            tabla.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-12">
                    <div class="flex flex-col items-center justify-center text-gray-400">
                        <i class="fas fa-chart-line text-4xl mb-4 text-blue-300"></i>
                        <p class="text-lg font-medium text-gray-600">¡No hay ventas registradas!</p>
                        <p class="text-sm">El historial de ventas aparecerá aquí</p>
                    </div>
                </td>
            </tr>
        `;
            return;
        }

        let html = '';
        pedidos.forEach(pedido => {
            const fecha = new Date(pedido.fecha).toLocaleDateString('es-PE', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            const estadoClass = pedido.estado === 'pendiente' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800';
            const estadoText = pedido.estado === 'pendiente' ? 'Pendiente' : 'Entregado';

            html += `
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="py-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                    #${pedido.id}
                </span>
            </td>
            <td class="py-4">
                <div>
                    <p class="font-semibold text-gray-900">${pedido.cliente_nombre || 'Cliente no encontrado'}</p>
                </div>
            </td>
            <td class="py-4 text-sm text-gray-600">
                ${fecha}
            </td>
            <td class="py-4 text-sm text-gray-600">
                S/${parseFloat(pedido.subtotal).toFixed(2)}
            </td>
            <td class="py-4 text-sm text-gray-600">
                S/${parseFloat(pedido.descuento_promocion).toFixed(2)}
            </td>
            <td class="py-4">
                <span class="font-bold text-green-600">S/${parseFloat(pedido.total).toFixed(2)}</span>
            </td>
            <td class="py-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${estadoClass}">
                    ${estadoText}
                </span>
            </td>
            <td class="py-4">
                <div class="flex space-x-2">
                    <button onclick="verDetallesPedido(${pedido.id})" 
                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-eye mr-1"></i>Ver
                    </button>
                    <button onclick="moverAPapelera(${pedido.id})" 
                            class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg transition-colors">
                        <i class="fas fa-trash mr-1"></i>Eliminar
                    </button>
                </div>
            </td>
        </tr>
        `;
        });

        tabla.innerHTML = html;
    }

    // =====================================
    // 🔹 FUNCIÓN PARA MOVER A PAPELERA
    // =====================================

    async function moverAPapelera(pedidoId) {
        if (!confirm(`¿Estás seguro de mover el pedido #${pedidoId} a la papelera?`)) {
            return;
        }

        try {
            console.log(`🗑️ Moviendo pedido #${pedidoId} a papelera...`);

            const formData = new FormData();
            formData.append('pedido_id', pedidoId);

            const response = await fetch('index.php?view=api-mover-papelera', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                mostrarNotificacion(`✅ Pedido #${pedidoId} movido a la papelera`, 'success');

                // Recargar todas las listas activas
                setTimeout(() => {
                    cargarPedidosPendientes();
                    cargarContadorPedidosPendientes();
                    cargarPapelera();
                    cargarContadorPapelera(); // ← AGREGAR ESTA LÍNEA

                    // Si estamos en otras pestañas, recargarlas también
                    const tabActiva = document.querySelector('.tab-button.active');
                    if (tabActiva) {
                        const tabId = tabActiva.getAttribute('data-tab');
                        if (tabId === 'pedidos-entregados') {
                            cargarPedidosEntregados();
                        } else if (tabId === 'historial-ventas') {
                            cargarHistorialVentas();
                        }
                    }
                }, 1000);
            } else {
                throw new Error(result.error);
            }

        } catch (error) {
            console.error('❌ Error al mover a papelera:', error);
            mostrarNotificacion(`❌ Error: ${error.message}`, 'error');
        }
    }

    // =====================================
    // 🔹 SISTEMA COMPLETO DE EXPORTACIÓN
    // =====================================

    // Control del menú desplegable
    function toggleExportMenu() {
        const menu = document.getElementById('export-menu');
        menu.classList.toggle('hidden');
    }

    // Cerrar menú al hacer clic fuera
    document.addEventListener('click', function(event) {
        const exportMenu = document.getElementById('export-menu');
        const exportButton = event.target.closest('button');
        
        if (!exportButton && exportMenu && !exportMenu.contains(event.target)) {
            exportMenu.classList.add('hidden');
        }
    });

    // Función principal de exportación
    async function exportarHistorialVentas(formato) {
        try {
            console.log(`📤 Exportando en formato: ${formato}`);
            
            mostrarNotificacion(`⏳ Generando archivo ${formato.toUpperCase()}...`, 'info');

            const response = await fetch('index.php?view=api-historial-ventas');
            const result = await response.json();

            if (!result.success || !Array.isArray(result.data)) {
                throw new Error('No se pudieron obtener los datos para exportar');
            }

            const pedidos = result.data;

            if (pedidos.length === 0) {
                mostrarNotificacion('No hay datos para exportar', 'warning');
                return;
            }

            // Cerrar el menú
            document.getElementById('export-menu').classList.add('hidden');

            // Ejecutar exportación según formato
            switch(formato) {
                case 'csv':
                    await exportarCSV(pedidos);
                    break;
                case 'excel':
                    await exportarExcel(pedidos);
                    break;
                case 'pdf':
                    await exportarPDF(pedidos);
                    break;
                default:
                    await exportarCSV(pedidos);
            }

        } catch (error) {
            console.error('❌ Error al exportar historial:', error);
            mostrarNotificacion(`❌ Error al exportar: ${error.message}`, 'error');
        }
    }

    // =====================================
    // 🔹 EXPORTACIÓN CSV (MEJORADA)
    // =====================================

    async function exportarCSV(pedidos) {
        // 🔥 CABECERAS MEJORADAS
        let csvContent = '';

        // Encabezado informativo
        csvContent += 'SamyGlow - Historial de Ventas\n';
        csvContent += `Fecha de exportación: ${new Date().toLocaleDateString('es-PE', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        })}\n`;
        csvContent += `Total de registros: ${pedidos.length}\n`;
        csvContent += 'Generado por Sistema SamyGlow\n\n';

        // Cabeceras de columnas
        csvContent += 'N° Pedido,Cliente,DNI,Fecha,Subtotal (S/),Descuento (S/),Total (S/),Estado,Método Pago,Notas\n';

        // Datos formateados
        pedidos.forEach((pedido) => {
            const fecha = new Date(pedido.fecha).toLocaleDateString('es-PE', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            const fila = [
                `PED-${pedido.id}`,
                `"${(pedido.cliente_nombre || 'N/A').replace(/"/g, '""')}"`,
                `"${pedido.dni || 'N/A'}"`,
                `"${fecha}"`,
                parseFloat(pedido.subtotal || 0).toFixed(2),
                parseFloat(pedido.descuento_promocion || 0).toFixed(2),
                parseFloat(pedido.total || 0).toFixed(2),
                pedido.estado === 'pendiente' ? 'PENDIENTE' : 'ENTREGADO',
                `"${(pedido.metodo_pago_nombre || 'NO ESPECIFICADO').replace(/"/g, '""')}"`,
                `"${(pedido.notas || 'SIN NOTAS').replace(/"/g, '""')}"`
            ].join(',');

            csvContent += fila + '\n';
        });

        // 🔥 TOTALES AL FINAL
        const subtotalGeneral = pedidos.reduce((sum, pedido) => sum + parseFloat(pedido.subtotal || 0), 0);
        const descuentoGeneral = pedidos.reduce((sum, pedido) => sum + parseFloat(pedido.descuento_promocion || 0), 0);
        const totalGeneral = pedidos.reduce((sum, pedido) => sum + parseFloat(pedido.total || 0), 0);

        csvContent += '\n';
        csvContent += 'RESUMEN GENERAL\n';
        csvContent += `Subtotal General,S/${subtotalGeneral.toFixed(2)}\n`;
        csvContent += `Descuento General,S/${descuentoGeneral.toFixed(2)}\n`;
        csvContent += `TOTAL GENERAL,S/${totalGeneral.toFixed(2)}\n`;

        // Crear y descargar archivo
        descargarArchivo(csvContent, `SamyGlow_Ventas_${getFechaExportacion()}.csv`, 'text/csv;charset=utf-8;');
        
        mostrarNotificacion(`✅ CSV exportado: ${pedidos.length} ventas`, 'success');
    }

    // =====================================
    // 🔹 EXPORTACIÓN EXCEL (HTML)
    // =====================================

    async function exportarExcel(pedidos) {
        // Crear contenido HTML para Excel
        let html = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
        <head>
            <meta charset="UTF-8">
            <!--[if gte mso 9]>
            <xml>
                <x:ExcelWorkbook>
                    <x:ExcelWorksheets>
                        <x:ExcelWorksheet>
                            <x:Name>Historial Ventas</x:Name>
                            <x:WorksheetOptions>
                                <x:DisplayGridlines/>
                            </x:WorksheetOptions>
                        </x:ExcelWorksheet>
                    </x:ExcelWorksheets>
                </x:ExcelWorkbook>
            </xml>
            <![endif]-->
            <style>
                table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }
                th { background-color: #f472b6; color: white; padding: 10px; text-align: left; font-weight: bold; }
                td { border: 1px solid #ddd; padding: 8px; }
                .header { background-color: #f8f9fa; padding: 15px; border-bottom: 2px solid #f472b6; }
                .total-row { background-color: #e8f5e8; font-weight: bold; }
                .number { text-align: right; }
                .centered { text-align: center; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1 style="color: #f472b6; margin: 0;">SamyGlow - Historial de Ventas</h1>
                <p style="margin: 5px 0; color: #666;">Fecha de exportación: ${new Date().toLocaleString('es-PE')}</p>
                <p style="margin: 5px 0; color: #666;">Total de registros: ${pedidos.length}</p>
            </div>
            <br>
            <table>
                <thead>
                    <tr>
                        <th>N° Pedido</th>
                        <th>Cliente</th>
                        <th>DNI</th>
                        <th>Fecha y Hora</th>
                        <th>Subtotal (S/)</th>
                        <th>Descuento (S/)</th>
                        <th>Total (S/)</th>
                        <th>Estado</th>
                        <th>Método Pago</th>
                        <th>Notas</th>
                    </tr>
                </thead>
                <tbody>
        `;

        // Agregar filas de datos
        pedidos.forEach(pedido => {
            const fecha = new Date(pedido.fecha).toLocaleDateString('es-PE', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            html += `
            <tr>
                <td>PED-${pedido.id}</td>
                <td>${pedido.cliente_nombre || 'N/A'}</td>
                <td>${pedido.dni || 'N/A'}</td>
                <td>${fecha}</td>
                <td class="number">${parseFloat(pedido.subtotal || 0).toFixed(2)}</td>
                <td class="number">${parseFloat(pedido.descuento_promocion || 0).toFixed(2)}</td>
                <td class="number">${parseFloat(pedido.total || 0).toFixed(2)}</td>
                <td class="centered">${pedido.estado === 'pendiente' ? '🟡 PENDIENTE' : '🟢 ENTREGADO'}</td>
                <td>${pedido.metodo_pago_nombre || 'NO ESPECIFICADO'}</td>
                <td>${pedido.notas || 'SIN NOTAS'}</td>
            </tr>
            `;
        });

        // Agregar totales
        const subtotalGeneral = pedidos.reduce((sum, pedido) => sum + parseFloat(pedido.subtotal || 0), 0);
        const descuentoGeneral = pedidos.reduce((sum, pedido) => sum + parseFloat(pedido.descuento_promocion || 0), 0);
        const totalGeneral = pedidos.reduce((sum, pedido) => sum + parseFloat(pedido.total || 0), 0);

        html += `
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="4" style="text-align: right; font-weight: bold;">TOTALES GENERALES:</td>
                        <td class="number">S/${subtotalGeneral.toFixed(2)}</td>
                        <td class="number">S/${descuentoGeneral.toFixed(2)}</td>
                        <td class="number">S/${totalGeneral.toFixed(2)}</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </body>
        </html>
        `;

        descargarArchivo(html, `SamyGlow_Ventas_${getFechaExportacion()}.xls`, 'application/vnd.ms-excel');
        
        mostrarNotificacion(`✅ Excel exportado: ${pedidos.length} ventas`, 'success');
    }

    // =====================================
    // 🔹 EXPORTACIÓN PDF (Básica - usando print)
    // =====================================

    async function exportarPDF(pedidos) {
        // Crear una ventana de impresión para PDF
        const ventana = window.open('', '_blank');
        const fechaExportacion = new Date().toLocaleString('es-PE');
        
        ventana.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>SamyGlow - Historial de Ventas</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; border-bottom: 2px solid #f472b6; padding-bottom: 10px; margin-bottom: 20px; }
                .header h1 { color: #f472b6; margin: 0; }
                .header p { color: #666; margin: 5px 0; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f8f9fa; font-weight: bold; }
                .total-row { background-color: #e8f5e8; font-weight: bold; }
                .number { text-align: right; }
                @media print {
                    body { margin: 0; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>SamyGlow - Historial de Ventas</h1>
                <p>Fecha de exportación: ${fechaExportacion}</p>
                <p>Total de registros: ${pedidos.length}</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>N° Pedido</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Subtotal (S/)</th>
                        <th>Descuento (S/)</th>
                        <th>Total (S/)</th>
                        <th>Estado</th>
                        <th>Método Pago</th>
                    </tr>
                </thead>
                <tbody>
        `);

        // Agregar datos
        pedidos.forEach(pedido => {
            const fecha = new Date(pedido.fecha).toLocaleDateString('es-PE');
            
            ventana.document.write(`
                <tr>
                    <td>PED-${pedido.id}</td>
                    <td>${pedido.cliente_nombre || 'N/A'}</td>
                    <td>${fecha}</td>
                    <td class="number">${parseFloat(pedido.subtotal || 0).toFixed(2)}</td>
                    <td class="number">${parseFloat(pedido.descuento_promocion || 0).toFixed(2)}</td>
                    <td class="number">${parseFloat(pedido.total || 0).toFixed(2)}</td>
                    <td>${pedido.estado}</td>
                    <td>${pedido.metodo_pago_nombre || 'N/A'}</td>
                </tr>
            `);
        });

        // Totales
        const totalGeneral = pedidos.reduce((sum, pedido) => sum + parseFloat(pedido.total || 0), 0);
        
        ventana.document.write(`
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="5" style="text-align: right;">TOTAL GENERAL:</td>
                        <td class="number">S/${totalGeneral.toFixed(2)}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
            
            <div class="no-print" style="margin-top: 20px; text-align: center;">
                <button onclick="window.print()" style="background: #f472b6; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                    🖨️ Imprimir / Guardar como PDF
                </button>
            </div>
        </body>
        </html>
        `);

        ventana.document.close();
        mostrarNotificacion('✅ PDF listo para imprimir/guardar', 'success');
    }

    // =====================================
    // 🔹 FUNCIONES AUXILIARES
    // =====================================

    function descargarArchivo(contenido, nombre, tipo) {
        const blob = new Blob([contenido], { type: tipo });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        
        link.setAttribute('href', url);
        link.setAttribute('download', nombre);
        link.style.visibility = 'hidden';
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function getFechaExportacion() {
        return new Date().toISOString().split('T')[0];
    }
</script>

<?php
require __DIR__ . "/../templates/footer.php";
?>