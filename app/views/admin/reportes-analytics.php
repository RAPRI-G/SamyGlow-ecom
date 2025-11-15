<!-- Agregar estos estilos en tu header.php -->
<style>
    .metric-card {
        background: linear-gradient(135deg, #f472b6 0%, #a78bfa 100%);
        color: white;
        border-radius: 8px;
        padding: 1.5rem;
        text-align: center;
    }

    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }

    .filter-bar {
        background: white;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-activa {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-inactiva {
        background-color: #fef3c7;
        color: #d97706;
    }

    .status-expirada {
        background-color: #fee2e2;
        color: #dc2626;
    }

    .tab-panel {
        display: none;
    }

    .tab-panel.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    /* Estilos para la tabla de productos */
    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-activa {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-inactiva {
        background-color: #fef3c7;
        color: #d97706;
    }

    .status-expirada {
        background-color: #fee2e2;
        color: #dc2626;
    }

    /* Efectos hover para tablas */
    .table-row-hover:hover {
        background-color: #f9fafb;
    }

    /* Badges para categorías */
    .badge-categoria {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
    }

    /* Animaciones */
    .fade-in {
        animation: fadeIn 0.5s ease-in;
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

    /* Métricas cards */
    .metric-card {
        background: linear-gradient(135deg, #f472b6 0%, #a78bfa 100%);
        color: white;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease;
    }

    .metric-card:hover {
        transform: translateY(-2px);
    }

    /* Filtros */
    .filter-bar {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
    }

    /* Badges para segmentos de clientes */
    .badge-vip {
        background-color: #f3e8ff;
        color: #7c3aed;
        border: 1px solid #ddd6fe;
    }

    .badge-frecuente {
        background-color: #dbeafe;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .badge-ocasional {
        background-color: #fce7f3;
        color: #be185d;
        border: 1px solid #fbcfe8;
    }

    /* Efectos para las tablas */
    .client-row:hover {
        background-color: #f8fafc;
        transform: translateX(4px);
        transition: all 0.2s ease;
    }

    /* Animaciones suaves */
    .smooth-transition {
        transition: all 0.3s ease;
    }

    /* Mejoras para métricas */
    .metric-highlight {
        position: relative;
        overflow: hidden;
    }

    .metric-highlight::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transition: left 0.5s;
    }

    .metric-highlight:hover::after {
        left: 100%;
    }

    /* Estilos específicos para inventario */
    .bg-red-50 {
        background-color: #fef2f2;
    }

    .bg-yellow-50 {
        background-color: #fffbeb;
    }

    .bg-orange-50 {
        background-color: #fff7ed;
    }

    /* Mejoras para las tablas */
    .table-inventario tr:hover {
        transform: translateX(2px);
        transition: transform 0.2s ease;
    }

    /* Badges de prioridad */
    .badge-prioridad-urgente {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        font-weight: bold;
    }

    .badge-prioridad-alta {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        font-weight: bold;
    }

    .badge-prioridad-media {
        background: linear-gradient(135deg, #eab308, #ca8a04);
        color: white;
    }

    /* Efectos para métricas */
    .metric-card {
        position: relative;
        overflow: hidden;
    }

    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.6s;
    }

    .metric-card:hover::before {
        left: 100%;
    }

    /* Animaciones para alertas */
    @keyframes pulse-alert {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.7;
        }
    }

    .alert-pulse {
        animation: pulse-alert 2s infinite;
    }

    /* Asegurar que los canvas de gráficos sean visibles */
    .chart-container canvas {
        display: block !important;
        max-width: 100% !important;
        height: 300px !important;
    }

    /* Forzar colores en los gráficos si es necesario */
    .chartjs-render-monitor {
        background-color: transparent !important;
    }
</style>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?view=login");
    exit;
}

$title = "Reportes & Analytics - SamyGlow";
$pageTitle = "Reportes & Analytics";
?>
<main class="flex-1 overflow-y-auto p-6">
    <!-- Sección de Reportes & Analytics -->
    <div class="content-section active" id="reportes">
        <!-- Tabs de navegación -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="flex border-b overflow-x-auto">
                <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-600 hover:bg-gray-50 active" data-tab="reporte-ventas">
                    <i class="fas fa-chart-line mr-2"></i>Reporte de Ventas
                </button>
                <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-600 hover:bg-gray-50" data-tab="reporte-productos">
                    <i class="fas fa-cube mr-2"></i>Productos Más Vendidos
                </button>
                <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-600 hover:bg-gray-50" data-tab="reporte-clientes">
                    <i class="fas fa-user-chart mr-2"></i>Clientes Frecuentes
                </button>
                <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-600 hover:bg-gray-50" data-tab="reporte-inventario">
                    <i class="fas fa-boxes mr-2"></i>Estado de Inventario
                </button>
            </div>
        </div>

        <!-- Contenido de las pestañas -->
        <div id="tab-content">
            <!-- Pestaña: Reporte de Ventas -->
            <div class="tab-panel active" id="reporte-ventas-panel">
                <!-- Filtros -->
                <div class="filter-bar">
                    <div class="flex flex-wrap gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rango de Fechas</label>
                            <div class="flex space-x-2">
                                <input type="date" id="fecha-inicio" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                                <span class="self-center text-gray-500">a</span>
                                <input type="date" id="fecha-fin" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                            <select id="filtro-categoria-ventas" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                                <option value="todos">Todas las categorías</option>
                                <?php
                                global $pdo;
                                $stmt = $pdo->query("SELECT id, nombre FROM categorias WHERE activa = 1");
                                $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($categorias as $categoria): ?>
                                    <option value="<?= $categoria['id'] ?>"><?= htmlspecialchars($categoria['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Método de Pago</label>
                            <select id="filtro-metodo-pago" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                                <option value="todos">Todos los métodos</option>
                                <?php
                                $stmt = $pdo->query("SELECT id, nombre FROM metodos_pago WHERE activo = 1");
                                $metodos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($metodos as $metodo): ?>
                                    <option value="<?= $metodo['id'] ?>"><?= htmlspecialchars($metodo['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="self-end">
                            <button class="bg-pink-500 hover:bg-pink-600 text-white font-medium py-2 px-4 rounded-lg transition-colors" onclick="cargarReporteVentas()">
                                <i class="fas fa-filter mr-2"></i>Aplicar Filtros
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Métricas principales -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6" id="metricas-ventas">
                    <!-- Se cargarán dinámicamente -->
                </div>

                <!-- Gráficos y tablas -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Gráfico de ventas por día -->
                    <div class="card">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Ventas por Día</h3>
                        <div class="chart-container">
                            <canvas id="ventasPorDiaChart"></canvas>
                        </div>
                    </div>

                    <!-- Gráfico de ventas por categoría -->
                    <div class="card">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Ventas por Categoría</h3>
                        <div class="chart-container">
                            <canvas id="ventasPorCategoriaChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Tabla de pedidos recientes -->
                <div class="card">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Pedidos Recientes</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Pedido</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método Pago</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="tabla-pedidos-recientes">
                                <!-- Los pedidos se cargarán aquí dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pestaña: Productos Más Vendidos -->
            <div class="tab-panel" id="reporte-productos-panel">
                <!-- Filtros -->
                <div class="filter-bar">
                    <div class="flex flex-wrap gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rango de Fechas</label>
                            <div class="flex space-x-2">
                                <input type="date" id="fecha-inicio-productos" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                                <span class="self-center text-gray-500">a</span>
                                <input type="date" id="fecha-fin-productos" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                            <select id="filtro-categoria-productos" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                                <option value="todos">Todas las categorías</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= $categoria['id'] ?>"><?= htmlspecialchars($categoria['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="self-end">
                            <button class="bg-pink-500 hover:bg-pink-600 text-white font-medium py-2 px-4 rounded-lg transition-colors" onclick="cargarReporteProductos()">
                                <i class="fas fa-filter mr-2"></i>Aplicar Filtros
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Métricas principales -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6" id="metricas-productos">
                    <!-- Se cargarán dinámicamente -->
                </div>

                <!-- Gráficos y tablas -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Gráfico de productos más vendidos -->
                    <div class="card">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Top 10 Productos Más Vendidos</h3>
                        <div class="chart-container">
                            <canvas id="productosMasVendidosChart"></canvas>
                        </div>
                    </div>

                    <!-- Gráfico de distribución por categoría -->
                    <div class="card">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Distribución por Categoría</h3>
                        <div class="chart-container">
                            <canvas id="distribucionCategoriaChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Tabla de productos más vendidos -->
                <div class="card">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Ranking de Productos</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posición</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unidades Vendidas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ingresos Totales</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Actual</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="tabla-productos-vendidos">
                                <!-- Los productos se cargarán aquí dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pestaña: Clientes Frecuentes -->
            <div class="tab-panel" id="reporte-clientes-panel">
                <!-- Filtros -->
                <div class="filter-bar">
                    <div class="flex flex-wrap gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rango de Fechas</label>
                            <div class="flex space-x-2">
                                <input type="date" id="fecha-inicio-clientes" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                                <span class="self-center text-gray-500">a</span>
                                <input type="date" id="fecha-fin-clientes" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                            </div>
                        </div>
                        <div class="self-end">
                            <button class="bg-pink-500 hover:bg-pink-600 text-white font-medium py-2 px-4 rounded-lg transition-colors" onclick="cargarReporteClientes()">
                                <i class="fas fa-filter mr-2"></i>Aplicar Filtros
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Métricas principales -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6" id="metricas-clientes">
                    <!-- Se cargarán dinámicamente -->
                </div>

                <!-- Gráficos y tablas -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Gráfico de clientes por gasto -->
                    <div class="card">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Top 10 Clientes por Gasto</h3>
                        <div class="chart-container">
                            <canvas id="clientesPorGastoChart"></canvas>
                        </div>
                    </div>

                    <!-- Gráfico de distribución de clientes -->
                    <div class="card">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Distribución de Clientes</h3>
                        <div class="chart-container">
                            <canvas id="distribucionClientesChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Tabla de clientes frecuentes -->
                <div class="card">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Ranking de Clientes Frecuentes</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posición</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pedidos</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Gastado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Última Compra</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="tabla-clientes-frecuentes">
                                <!-- Los clientes se cargarán aquí dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pestaña: Estado de Inventario -->
            <div class="tab-panel" id="reporte-inventario-panel">
                <!-- Filtros -->
                <div class="filter-bar">
                    <div class="flex flex-wrap gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                            <select id="filtro-categoria-inventario" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                                <option value="todos">Todas las categorías</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= $categoria['id'] ?>"><?= htmlspecialchars($categoria['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado de Stock</label>
                            <select id="filtro-estado-stock" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                                <option value="todos">Todos los estados</option>
                                <option value="agotado">Agotado</option>
                                <option value="bajo">Stock Bajo</option>
                                <option value="disponible">Disponible</option>
                            </select>
                        </div>
                        <div class="self-end">
                            <button class="bg-pink-500 hover:bg-pink-600 text-white font-medium py-2 px-4 rounded-lg transition-colors" onclick="cargarReporteInventario()">
                                <i class="fas fa-filter mr-2"></i>Aplicar Filtros
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Métricas principales -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6" id="metricas-inventario">
                    <!-- Se cargarán dinámicamente -->
                </div>

                <!-- Gráficos y tablas -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Gráfico de inventario por categoría -->
                    <div class="card">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Inventario por Categoría</h3>
                        <div class="chart-container">
                            <canvas id="inventarioPorCategoriaChart"></canvas>
                        </div>
                    </div>

                    <!-- Gráfico de estado de stock -->
                    <div class="card">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Estado del Stock</h3>
                        <div class="chart-container">
                            <canvas id="estadoStockChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Tabla de estado de inventario -->
                <div class="card">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Estado de Inventario</h3>
                        <button class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg transition-colors" onclick="generarReporteInventario()">
                            <i class="fas fa-file-export mr-2"></i>Generar Reporte
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Actual</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor en Inventario</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="tabla-estado-inventario">
                                <!-- Los productos se cargarán aquí dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
</div>

<!-- JavaScript para Reportes Dinámicos -->
<script>
    // Configuración de la API
    const API_BASE = 'index.php?view=';

    // Variables para almacenar gráficos
    let ventasPorDiaChart, ventasPorCategoriaChart, productosMasVendidosChart, distribucionCategoriaChart;
    let clientesPorGastoChart, distribucionClientesChart, inventarioPorCategoriaChart, estadoStockChart;

    document.addEventListener('DOMContentLoaded', function() {
        // Configurar fechas por defecto
        const hoy = new Date();
        const hace30Dias = new Date();
        hace30Dias.setDate(hoy.getDate() - 30);

        // Establecer fechas en todos los filtros
        document.querySelectorAll('input[type="date"]').forEach(input => {
            if (input.id.includes('inicio')) {
                input.value = hace30Dias.toISOString().split('T')[0];
            } else if (input.id.includes('fin')) {
                input.value = hoy.toISOString().split('T')[0];
            }
        });

        // Configurar navegación de tabs
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active'));
                const tabId = this.getAttribute('data-tab');
                document.getElementById(tabId + '-panel').classList.add('active');

                // Cargar el reporte correspondiente al cambiar de tab
                switch (tabId) {
                    case 'reporte-ventas':
                        cargarReporteVentas();
                        break;
                    case 'reporte-productos':
                        cargarReporteProductos();
                        break;
                    case 'reporte-clientes':
                        cargarReporteClientes();
                        break;
                    case 'reporte-inventario':
                        cargarReporteInventario();
                        break;
                }
            });
        });

        // Cargar reporte de ventas inicial
        cargarReporteVentas();
    });

    // 🔹 REPORTE DE VENTAS
    async function cargarReporteVentas() {
        try {
            mostrarLoading('Cargando reporte de ventas...');

            const fechaInicio = document.getElementById('fecha-inicio').value;
            const fechaFin = document.getElementById('fecha-fin').value;
            const categoriaId = document.getElementById('filtro-categoria-ventas').value;
            const metodoPagoId = document.getElementById('filtro-metodo-pago').value;

            const params = new URLSearchParams({
                fecha_inicio: fechaInicio,
                fecha_fin: fechaFin,
                categoria_id: categoriaId,
                metodo_pago_id: metodoPagoId
            });

            const response = await fetch(`${API_BASE}api-reporte-ventas&${params}`);
            const result = await response.json();

            if (!result.success) {
                throw new Error(result.error);
            }

            const data = result.data;

            // Actualizar métricas
            actualizarMetricasVentas(data.metrics);

            // Actualizar tabla de pedidos
            actualizarTablaPedidos(data.pedidos);

            // Actualizar gráficos
            actualizarGraficoVentasPorDia(data.ventas_por_dia);
            actualizarGraficoVentasPorCategoria(data.ventas_por_categoria);

            mostrarNotificacion('Reporte de ventas actualizado', 'success');

        } catch (error) {
            console.error('Error:', error);
            mostrarNotificacion('Error al cargar reporte de ventas: ' + error.message, 'error');
        } finally {
            ocultarLoading();
        }
    }

    function actualizarMetricasVentas(metrics) {
        const container = document.getElementById('metricas-ventas');
        container.innerHTML = `
        <div class="metric-card">
            <div class="text-3xl font-bold">S/ ${metrics.ventas_totales?.toFixed(2) || '0.00'}</div>
            <div class="text-sm opacity-90">Ventas Totales</div>
            <div class="text-xs mt-2 flex items-center">
                <i class="fas fa-chart-line mr-1"></i>
                <span>Período seleccionado</span>
            </div>
        </div>
        <div class="metric-card">
            <div class="text-3xl font-bold">${metrics.total_pedidos || 0}</div>
            <div class="text-sm opacity-90">Pedidos Totales</div>
            <div class="text-xs mt-2 flex items-center">
                <i class="fas fa-shopping-bag mr-1"></i>
                <span>Total de pedidos</span>
            </div>
        </div>
        <div class="metric-card">
            <div class="text-3xl font-bold">S/ ${metrics.ticket_promedio?.toFixed(2) || '0.00'}</div>
            <div class="text-sm opacity-90">Ticket Promedio</div>
            <div class="text-xs mt-2 flex items-center">
                <i class="fas fa-receipt mr-1"></i>
                <span>Promedio por pedido</span>
            </div>
        </div>
        <div class="metric-card">
            <div class="text-3xl font-bold">${metrics.pedidos_entregados || 0}</div>
            <div class="text-sm opacity-90">Pedidos Entregados</div>
            <div class="text-xs mt-2 flex items-center">
                <i class="fas fa-check mr-1"></i>
                <span>${metrics.total_pedidos ? Math.round((metrics.pedidos_entregados / metrics.total_pedidos) * 100) : 0}% completados</span>
            </div>
        </div>
    `;
    }

    function actualizarTablaPedidos(pedidos) {
        const tbody = document.getElementById('tabla-pedidos-recientes');

        if (pedidos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay pedidos en este período</td></tr>';
            return;
        }

        let html = '';
        pedidos.forEach(pedido => {
            const fecha = new Date(pedido.fecha).toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            let estadoBadge = '';

            if (pedido.estado === 'entregado') {
                estadoBadge = '<span class="status-badge status-activa">Entregado</span>';
            } else {
                estadoBadge = '<span class="status-badge status-inactiva">Pendiente</span>';
            }

            html += `
        <tr class="fade-in">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#${pedido.id}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${pedido.cliente_nombre || 'N/A'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${fecha}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">S/ ${parseFloat(pedido.total || 0).toFixed(2)}</td>
            <td class="px-6 py-4 whitespace-nowrap">${estadoBadge}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${pedido.metodo_pago_nombre || 'N/A'}</td>
        </tr>
        `;
        });

        tbody.innerHTML = html;
    }

    function actualizarGraficoVentasPorDia(ventasPorDia) {
        const ctx = document.getElementById('ventasPorDiaChart').getContext('2d');

        const labels = ventasPorDia.map(item => {
            const fecha = new Date(item.fecha);
            return fecha.toLocaleDateString('es-ES', {
                day: 'numeric',
                month: 'short'
            });
        });

        const data = ventasPorDia.map(item => parseFloat(item.total_ventas || 0));

        if (ventasPorDiaChart) {
            ventasPorDiaChart.destroy();
        }

        // Si no hay datos, mostrar mensaje
        if (ventasPorDia.length === 0) {
            ctx.font = '16px Arial';
            ctx.fillStyle = '#999';
            ctx.textAlign = 'center';
            ctx.fillText('No hay datos para mostrar', ctx.canvas.width / 2, ctx.canvas.height / 2);
            return;
        }

        ventasPorDiaChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ventas (S/)',
                    data: data,
                    borderColor: '#f472b6',
                    backgroundColor: 'rgba(244, 114, 182, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'S/ ' + value;
                            }
                        }
                    }
                }
            }
        });
    }

    function actualizarGraficoVentasPorCategoria(ventasPorCategoria) {
        const ctx = document.getElementById('ventasPorCategoriaChart').getContext('2d');

        const labels = ventasPorCategoria.map(item => item.categoria);
        const data = ventasPorCategoria.map(item => parseFloat(item.total_ventas || 0));
        const backgroundColors = ['#f472b6', '#a78bfa', '#60a5fa', '#34d399', '#fbbf24', '#f87171', '#38bdf8', '#a78bfa'];

        if (ventasPorCategoriaChart) {
            ventasPorCategoriaChart.destroy();
        }

        // Si no hay datos, mostrar mensaje
        if (ventasPorCategoria.length === 0) {
            ctx.font = '16px Arial';
            ctx.fillStyle = '#999';
            ctx.textAlign = 'center';
            ctx.fillText('No hay datos para mostrar', ctx.canvas.width / 2, ctx.canvas.height / 2);
            return;
        }

        ventasPorCategoriaChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: S/ ${value.toFixed(2)} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // 🔹 REPORTE DE PRODUCTOS
    async function cargarReporteProductos() {
        try {
            mostrarLoading('Cargando reporte de productos...');

            const fechaInicio = document.getElementById('fecha-inicio-productos').value;
            const fechaFin = document.getElementById('fecha-fin-productos').value;
            const categoriaId = document.getElementById('filtro-categoria-productos').value;

            const params = new URLSearchParams({
                fecha_inicio: fechaInicio,
                fecha_fin: fechaFin,
                categoria_id: categoriaId
            });

            console.log('Solicitando API productos:', `${API_BASE}api-reporte-productos&${params}`);

            const response = await fetch(`${API_BASE}api-reporte-productos&${params}`);

            // Verificar si la respuesta es JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Respuesta no JSON:', text.substring(0, 500));
                throw new Error('El servidor respondió con HTML en lugar de JSON. Revisa los errores PHP.');
            }

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.error);
            }

            const data = result.data;

            actualizarMetricasProductos(data.metrics);
            actualizarTablaProductosVendidos(data.productos);
            actualizarGraficoProductosMasVendidos(data.top_productos);
            actualizarGraficoDistribucionCategoria(data.distribucion_categoria);

            mostrarNotificacion('Reporte de productos actualizado', 'success');

        } catch (error) {
            console.error('Error en reporte productos:', error);
            mostrarNotificacion('Error al cargar reporte de productos: ' + error.message, 'error');
        } finally {
            ocultarLoading();
        }
    }

    // 🔹 REPORTE DE CLIENTES
    async function cargarReporteClientes() {
        try {
            mostrarLoading('Cargando reporte de clientes...');

            const fechaInicio = document.getElementById('fecha-inicio-clientes').value;
            const fechaFin = document.getElementById('fecha-fin-clientes').value;

            const params = new URLSearchParams({
                fecha_inicio: fechaInicio,
                fecha_fin: fechaFin
            });

            console.log('Solicitando API clientes:', `${API_BASE}api-reporte-clientes&${params}`);

            const response = await fetch(`${API_BASE}api-reporte-clientes&${params}`);

            // Verificar si la respuesta es JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Respuesta no JSON:', text.substring(0, 500));
                throw new Error('El servidor respondió con HTML en lugar de JSON. Revisa los errores PHP.');
            }

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.error);
            }

            const data = result.data;

            actualizarMetricasClientes(data.metrics);
            actualizarTablaClientesFrecuentes(data.clientes);
            actualizarGraficoClientesPorGasto(data.top_clientes_gasto);
            actualizarGraficoDistribucionClientes(data.distribucion_clientes);

            mostrarNotificacion('Reporte de clientes actualizado', 'success');

        } catch (error) {
            console.error('Error en reporte clientes:', error);
            mostrarNotificacion('Error al cargar reporte de clientes: ' + error.message, 'error');
        } finally {
            ocultarLoading();
        }
    }


    async function cargarReporteInventario() {

        try {
            mostrarLoading('Cargando reporte de inventario...');

            const categoriaId = document.getElementById('filtro-categoria-inventario').value;
            const estadoStock = document.getElementById('filtro-estado-stock').value;

            const params = new URLSearchParams({
                categoria_id: categoriaId,
                estado_stock: estadoStock
            });

            console.log('Solicitando API inventario:', `${API_BASE}api-reporte-inventario&${params}`);

            const response = await fetch(`${API_BASE}api-reporte-inventario&${params}`);

            // Verificar si la respuesta es JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Respuesta no JSON:', text.substring(0, 500));
                throw new Error('El servidor respondió con HTML en lugar de JSON. Revisa los errores PHP.');
            }

            const result = await response.json();

            console.log('Respuesta completa del servidor:', result); // 🔍 DEBUG COMPLETO

            if (!result.success) {
                throw new Error(result.error);
            }

            const data = result.data;

            console.log('Datos del inventario:', data); // 🔍 DEBUG DE DATOS

            actualizarMetricasInventario(data.metrics);
            actualizarTablaEstadoInventario(data.productos);

            // Verificar específicamente los datos de gráficos
            console.log('Inventario por categoría:', data.inventario_por_categoria);
            console.log('Estado stock data:', data.estado_stock_data);

            actualizarGraficoInventarioPorCategoria(data.inventario_por_categoria);
            actualizarGraficoEstadoStock(data.estado_stock_data);
            actualizarAlertasReposicion(data.productos_reposicion);

            mostrarNotificacion('Reporte de inventario actualizado', 'success');

        } catch (error) {
            console.error('Error en reporte inventario:', error);
            mostrarNotificacion('Error al cargar reporte de inventario: ' + error.message, 'error');
        } finally {
            ocultarLoading();
        }

    }

    function actualizarAlertasReposicion(productosReposicion) {
        // Crear o actualizar sección de alertas
        let alertasSection = document.getElementById('alertas-reposicion');

        if (!alertasSection) {
            alertasSection = document.createElement('div');
            alertasSection.id = 'alertas-reposicion';
            alertasSection.className = 'card p-6 mt-6';

            const tabPanel = document.getElementById('reporte-inventario-panel');

            // Buscar el contenedor de la tabla de inventario dentro del tab panel
            const tablaInventario = tabPanel.querySelector('.card:has(table)');

            // Insertar después de la tabla de inventario
            if (tablaInventario && tablaInventario.parentNode) {
                tablaInventario.parentNode.insertBefore(alertasSection, tablaInventario.nextSibling);
            } else {
                // Fallback: agregar al final del tab panel
                tabPanel.appendChild(alertasSection);
            }
        }

        if (!productosReposicion || productosReposicion.length === 0) {
            alertasSection.innerHTML = `
            <h3 class="text-lg font-bold text-gray-800 mb-4">Alertas de Reposición</h3>
            <div class="text-center py-8">
                <i class="fas fa-check-circle text-4xl text-green-500 mb-4"></i>
                <p class="text-gray-600">No hay productos que necesiten reposición urgente</p>
            </div>
        `;
            return;
        }

        let alertasHTML = `
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
            Alertas de Reposición (${productosReposicion.length})
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Actual</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioridad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor Actual</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
    `;

        productosReposicion.forEach(producto => {
            let prioridadBadge = '';
            let prioridadClass = '';

            switch (producto.prioridad_reposicion) {
                case 'URGENTE':
                    prioridadBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">URGENTE</span>';
                    prioridadClass = 'bg-red-50';
                    break;
                case 'ALTA':
                    prioridadBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">ALTA</span>';
                    prioridadClass = 'bg-orange-50';
                    break;
                case 'MEDIA':
                    prioridadBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">MEDIA</span>';
                    prioridadClass = 'bg-yellow-50';
                    break;
                default:
                    prioridadBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">BAJA</span>';
            }

            alertasHTML += `
            <tr class="fade-in hover:bg-gray-50 ${prioridadClass}">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${producto.nombre}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${producto.categoria_nombre}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold ${producto.stock === 0 ? 'text-red-600' : 'text-orange-600'}">
                    ${producto.stock}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">${prioridadBadge}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">S/ ${parseFloat(producto.valor_actual || 0).toFixed(2)}</td>
            </tr>
        `;
        });

        alertasHTML += `
                </tbody>
            </table>
        </div>
    `;

        alertasSection.innerHTML = alertasHTML;
    }

    // Funciones de utilidad
    function mostrarLoading(mensaje = 'Cargando...') {
        // Puedes implementar un spinner aquí si quieres
        console.log(mensaje);
    }

    function ocultarLoading() {
        // Ocultar spinner si implementaste uno
    }

    function mostrarNotificacion(mensaje, tipo) {
        const notificacion = document.createElement('div');
        notificacion.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-transform duration-300 ${
        tipo === 'success' ? 'bg-green-500 text-white' :
        tipo === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
        notificacion.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${tipo === 'success' ? 'check' : tipo === 'error' ? 'exclamation-triangle' : 'info-circle'} mr-2"></i>
            <span>${mensaje}</span>
        </div>
    `;

        document.body.appendChild(notificacion);

        setTimeout(() => {
            notificacion.classList.add('opacity-0');
            setTimeout(() => {
                document.body.removeChild(notificacion);
            }, 300);
        }, 3000);
    }

    function exportarReporteCompleto() {
        mostrarNotificacion('Exportando reporte completo...', 'info');
        setTimeout(() => {
            mostrarNotificacion('Reporte completo exportado exitosamente', 'success');
        }, 2000);
    }

    function generarReporteInventario() {
        mostrarNotificacion('Generando reporte de inventario...', 'info');

        // Simular generación de reporte
        setTimeout(() => {
            // Crear contenido del reporte
            const fecha = new Date().toLocaleDateString('es-ES');
            const metricas = document.getElementById('metricas-inventario').innerText;
            const tabla = document.getElementById('tabla-estado-inventario').innerText;

            // En una implementación real, aquí enviarías una solicitud al servidor
            // para generar un PDF o Excel con los datos

            mostrarNotificacion('Reporte de inventario generado exitosamente', 'success');

            // Simular descarga
            const enlace = document.createElement('a');
            enlace.href = '#'; // En realidad sería la URL del archivo generado
            enlace.download = `reporte-inventario-${fecha}.pdf`;
            enlace.click();

        }, 2000);
    }

    function actualizarMetricasProductos(metrics) {
        const container = document.getElementById('metricas-productos');
        container.innerHTML = `
        <div class="metric-card">
            <div class="text-3xl font-bold">${metrics.total_unidades || 0}</div>
            <div class="text-sm opacity-90">Unidades Vendidas</div>
            <div class="text-xs mt-2 flex items-center">
                <i class="fas fa-shopping-cart mr-1"></i>
                <span>Total vendido</span>
            </div>
        </div>
        <div class="metric-card">
            <div class="text-3xl font-bold">${metrics.total_productos || 0}</div>
            <div class="text-sm opacity-90">Productos Diferentes</div>
            <div class="text-xs mt-2 flex items-center">
                <i class="fas fa-cubes mr-1"></i>
                <span>Variedad vendida</span>
            </div>
        </div>
        <div class="metric-card">
            <div class="text-3xl font-bold">S/ ${metrics.ingresos_totales?.toFixed(2) || '0.00'}</div>
            <div class="text-sm opacity-90">Ingresos Totales</div>
            <div class="text-xs mt-2 flex items-center">
                <i class="fas fa-chart-line mr-1"></i>
                <span>Por productos</span>
            </div>
        </div>
    `;
    }

    function actualizarTablaProductosVendidos(productos) {
        const tbody = document.getElementById('tabla-productos-vendidos');

        if (!productos || productos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay productos vendidos en este período</td></tr>';
            return;
        }

        let html = '';
        productos.forEach((producto, index) => {
            let posicionBadge = '';
            if (index === 0) {
                posicionBadge = '<span class="inline-flex items-center justify-center w-6 h-6 bg-yellow-500 text-white text-xs font-bold rounded-full">1°</span>';
            } else if (index === 1) {
                posicionBadge = '<span class="inline-flex items-center justify-center w-6 h-6 bg-gray-400 text-white text-xs font-bold rounded-full">2°</span>';
            } else if (index === 2) {
                posicionBadge = '<span class="inline-flex items-center justify-center w-6 h-6 bg-orange-700 text-white text-xs font-bold rounded-full">3°</span>';
            } else {
                posicionBadge = `<span class="text-xs font-medium text-gray-500">${index + 1}°</span>`;
            }

            // Manejar imagen del producto
            const imagenHTML = producto.imagen ?
                `<img src="${producto.imagen}" alt="${producto.nombre}" class="w-8 h-8 rounded object-cover">` :
                '<div class="w-8 h-8 rounded bg-gray-200 flex items-center justify-center"><i class="fas fa-cube text-gray-400 text-xs"></i></div>';

            html += `
        <tr class="fade-in hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${posicionBadge}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="flex-shrink-0 mr-3">
                        ${imagenHTML}
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">${producto.nombre}</div>
                        <div class="text-xs text-gray-500">ID: ${producto.id}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                    ${producto.categoria_nombre}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <div class="flex items-center">
                    <span class="font-bold text-green-600">${producto.cantidad_vendida || 0}</span>
                    <span class="text-xs text-gray-500 ml-1">unidades</span>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                S/ ${parseFloat(producto.ingresos_totales || 0).toFixed(2)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm ${producto.stock_actual <= 5 ? 'text-red-600 font-bold' : producto.stock_actual <= 10 ? 'text-orange-500 font-medium' : 'text-gray-900'}">
                <div class="flex items-center">
                    ${producto.stock_actual}
                    ${producto.stock_actual <= 5 ? '<i class="fas fa-exclamation-triangle ml-1 text-red-500"></i>' : ''}
                </div>
            </td>
        </tr>
        `;
        });

        tbody.innerHTML = html;
    }

    function actualizarGraficoProductosMasVendidos(top_productos) {
        const ctx = document.getElementById('productosMasVendidosChart').getContext('2d');

        if (productosMasVendidosChart) {
            productosMasVendidosChart.destroy();
        }

        if (!top_productos || top_productos.length === 0) {
            // Mostrar mensaje de no datos
            ctx.font = '16px Arial';
            ctx.fillStyle = '#999';
            ctx.textAlign = 'center';
            ctx.fillText('No hay datos para mostrar', ctx.canvas.width / 2, ctx.canvas.height / 2);
            return;
        }

        // Limitar a 10 productos para mejor visualización
        const productosMostrar = top_productos.slice(0, 10);

        const labels = productosMostrar.map(p => {
            // Acortar nombres largos
            const nombre = p.nombre.length > 20 ? p.nombre.substring(0, 20) + '...' : p.nombre;
            return nombre;
        });

        const data = productosMostrar.map(p => parseInt(p.cantidad_vendida || 0));

        // Colores para las barras
        const backgroundColors = productosMostrar.map((_, index) => {
            const colors = ['#f472b6', '#a78bfa', '#60a5fa', '#34d399', '#fbbf24', '#f87171', '#38bdf8', '#a78bfa', '#f472b6', '#60a5fa'];
            return colors[index % colors.length];
        });

        productosMasVendidosChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Unidades Vendidas',
                    data: data,
                    backgroundColor: backgroundColors,
                    borderColor: backgroundColors,
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y', // Gráfico horizontal
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const producto = productosMostrar[context.dataIndex];
                                return [
                                    `Producto: ${producto.nombre}`,
                                    `Unidades: ${context.raw}`,
                                    `Ingresos: S/ ${parseFloat(producto.ingresos_totales || 0).toFixed(2)}`,
                                    `Categoría: ${producto.categoria_nombre}`
                                ];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Unidades Vendidas'
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    },
                    y: {
                        ticks: {
                            autoSkip: false,
                            maxRotation: 45
                        }
                    }
                }
            }
        });
    }

    function actualizarGraficoDistribucionCategoria(distribucion) {
        const ctx = document.getElementById('distribucionCategoriaChart').getContext('2d');

        if (distribucionCategoriaChart) {
            distribucionCategoriaChart.destroy();
        }

        if (!distribucion || distribucion.length === 0) {
            ctx.font = '16px Arial';
            ctx.fillStyle = '#999';
            ctx.textAlign = 'center';
            ctx.fillText('No hay datos para mostrar', ctx.canvas.width / 2, ctx.canvas.height / 2);
            return;
        }

        const labels = distribucion.map(d => d.categoria);
        const data = distribucion.map(d => d.cantidad);
        const ingresos = distribucion.map(d => d.ingresos);

        // Colores para el gráfico
        const backgroundColors = ['#f472b6', '#a78bfa', '#60a5fa', '#34d399', '#fbbf24', '#f87171', '#38bdf8', '#a78bfa'];

        distribucionCategoriaChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const categoria = distribucion[context.dataIndex];
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;

                                return [
                                    `Categoría: ${context.label}`,
                                    `Unidades: ${context.raw.toLocaleString()}`,
                                    `Ingresos: S/ ${parseFloat(categoria.ingresos || 0).toFixed(2)}`,
                                    `Porcentaje: ${percentage}%`,
                                    `Productos: ${categoria.productos}`
                                ];
                            }
                        }
                    }
                },
                cutout: '50%'
            }
        });
    }

    function actualizarMetricasClientes(metrics) {
        const container = document.getElementById('metricas-clientes');

        container.innerHTML = `
        <div class="metric-card">
            <div class="text-3xl font-bold">${metrics.total_clientes_registrados?.toLocaleString() || 0}</div>
            <div class="text-sm opacity-90">Total Clientes</div>
            <div class="text-xs mt-2 flex items-center">
                <i class="fas fa-users mr-1"></i>
                <span>En base de datos</span>
            </div>
        </div>
        <div class="metric-card">
            <div class="text-3xl font-bold">${metrics.clientes_con_pedidos?.toLocaleString() || 0}</div>
            <div class="text-sm opacity-90">Clientes Activos</div>
            <div class="text-xs mt-2 flex items-center">
                <i class="fas fa-shopping-bag mr-1"></i>
                <span>Con al menos 1 pedido</span>
            </div>
        </div>
        <div class="metric-card">
            <div class="text-3xl font-bold">${metrics.tasa_retencion?.toFixed(1) || 0}%</div>
            <div class="text-sm opacity-90">Tasa de Retención</div>
            <div class="text-xs mt-2">Clientes que han comprado</div>
        </div>
        <div class="metric-card">
            <div class="text-3xl font-bold">S/ ${metrics.valor_vida_cliente?.toFixed(2) || '0.00'}</div>
            <div class="text-sm opacity-90">Valor Vida Cliente</div>
            <div class="text-xs mt-2">Gasto promedio por cliente</div>
        </div>
    `;
    }

    function actualizarTablaClientesFrecuentes(clientes) {
        const tbody = document.getElementById('tabla-clientes-frecuentes');

        if (!clientes || clientes.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No hay clientes con compras en este período</td></tr>';
            return;
        }

        let html = '';
        clientes.forEach((cliente, index) => {
            let posicionBadge = '';
            if (index === 0) {
                posicionBadge = '<span class="inline-flex items-center justify-center w-6 h-6 bg-yellow-500 text-white text-xs font-bold rounded-full">1°</span>';
            } else if (index === 1) {
                posicionBadge = '<span class="inline-flex items-center justify-center w-6 h-6 bg-gray-400 text-white text-xs font-bold rounded-full">2°</span>';
            } else if (index === 2) {
                posicionBadge = '<span class="inline-flex items-center justify-center w-6 h-6 bg-orange-700 text-white text-xs font-bold rounded-full">3°</span>';
            } else {
                posicionBadge = `<span class="text-xs font-medium text-gray-500">${index + 1}°</span>`;
            }

            // Determinar badge de segmento
            let segmentoBadge = '';
            const segmento = cliente.segmento || 'Ocasional';
            switch (segmento) {
                case 'VIP':
                    segmentoBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">VIP</span>';
                    break;
                case 'Frecuente':
                    segmentoBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Frecuente</span>';
                    break;
                default:
                    segmentoBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Ocasional</span>';
            }

            let segmentoTooltip = '';
            switch (segmento) {
                case 'VIP':
                    segmentoTooltip = 'Cliente VIP: 5+ pedidos';
                    break;
                case 'Frecuente':
                    segmentoTooltip = 'Cliente Frecuente: 3-4 pedidos';
                    break;
                case 'Recurrentes':
                    segmentoTooltip = 'Cliente Recurrente: 2+ pedidos, activo recientemente';
                    break;
                case 'Ocasional':
                    segmentoTooltip = 'Cliente Ocasional: 1-2 pedidos';
                    break;
                default:
                    segmentoTooltip = segmento;
            }

            // En el HTML del badge:
            segmentoBadge = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getSegmentoClass(segmento)}" title="${segmentoTooltip}">${segmento}</span>`;

            // Formatear fechas
            const primerPedido = cliente.primer_pedido ? new Date(cliente.primer_pedido).toLocaleDateString('es-ES') : 'N/A';
            const ultimoPedido = cliente.ultimo_pedido ? new Date(cliente.ultimo_pedido).toLocaleDateString('es-ES') : 'N/A';

            // Calcular antigüedad en días
            const diasActividad = cliente.dias_actividad ? `${cliente.dias_actividad} días` : 'N/A';

            html += `
        <tr class="fade-in hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${posicionBadge}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div>
                    <div class="text-sm font-medium text-gray-900">${cliente.nombres} ${cliente.apellidos}</div>
                    <div class="text-xs text-gray-500">${cliente.correo}</div>
                    <div class="text-xs text-gray-400">DNI: ${cliente.dni}</div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${segmentoBadge}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center font-semibold">
                ${cliente.total_pedidos || 0}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold text-green-600">
                S/ ${parseFloat(cliente.total_gastado || 0).toFixed(2)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <div class="text-center">
                    <div class="text-xs">${primerPedido}</div>
                    <div class="text-xs text-gray-500">${diasActividad}</div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                ${ultimoPedido}
            </td>
        </tr>
        `;
        });

        tbody.innerHTML = html;
    }

    // Agrega esta función helper para las clases CSS:
    function getSegmentoClass(segmento) {
        switch (segmento) {
            case 'VIP':
                return 'bg-purple-100 text-purple-800 border border-purple-200';
            case 'Frecuente':
                return 'bg-blue-100 text-blue-800 border border-blue-200';
            case 'Recurrentes':
                return 'bg-green-100 text-green-800 border border-green-200';
            case 'Ocasional':
                return 'bg-pink-100 text-pink-800 border border-pink-200';
            case 'Nuevos':
                return 'bg-yellow-100 text-yellow-800 border border-yellow-200';
            case 'Inactivos':
                return 'bg-gray-100 text-gray-800 border border-gray-200';
            default:
                return 'bg-gray-100 text-gray-800 border border-gray-200';
        }
    }



    function actualizarGraficoClientesPorGasto(top_clientes) {
        const ctx = document.getElementById('clientesPorGastoChart').getContext('2d');

        if (clientesPorGastoChart) {
            clientesPorGastoChart.destroy();
        }

        if (!top_clientes || top_clientes.length === 0) {
            // Mostrar mensaje de no datos
            ctx.font = '16px Arial';
            ctx.fillStyle = '#999';
            ctx.textAlign = 'center';
            ctx.fillText('No hay datos para mostrar', ctx.canvas.width / 2, ctx.canvas.height / 2);
            return;
        }

        // Limitar a 10 clientes para mejor visualización
        const clientesMostrar = top_clientes.slice(0, 10);

        const labels = clientesMostrar.map(c => {
            // Acortar nombres para mejor visualización
            const nombreCompleto = `${c.nombres} ${c.apellidos}`;
            return nombreCompleto.length > 15 ? nombreCompleto.substring(0, 15) + '...' : nombreCompleto;
        });

        const data = clientesMostrar.map(c => parseFloat(c.total_gastado || 0));

        // Colores basados en el segmento
        const backgroundColors = clientesMostrar.map(cliente => {
            switch (cliente.segmento) {
                case 'VIP':
                    return '#8b5cf6'; // Purple
                case 'Frecuente':
                    return '#3b82f6'; // Blue
                default:
                    return '#f472b6'; // Pink
            }
        });

        clientesPorGastoChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Gastado (S/)',
                    data: data,
                    backgroundColor: backgroundColors,
                    borderColor: backgroundColors,
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const cliente = clientesMostrar[context.dataIndex];
                                return [
                                    `Cliente: ${cliente.nombres} ${cliente.apellidos}`,
                                    `Total Gastado: S/ ${context.raw.toFixed(2)}`,
                                    `Pedidos: ${cliente.total_pedidos}`,
                                    `Segmento: ${cliente.segmento}`,
                                    `Ticket Promedio: S/ ${parseFloat(cliente.promedio_pedido || 0).toFixed(2)}`
                                ];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Gastado (S/)'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'S/ ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    function actualizarGraficoDistribucionClientes(distribucion) {
        const ctx = document.getElementById('distribucionClientesChart').getContext('2d');

        if (distribucionClientesChart) {
            distribucionClientesChart.destroy();
        }

        if (!distribucion || distribucion.length === 0) {
            ctx.font = '16px Arial';
            ctx.fillStyle = '#999';
            ctx.textAlign = 'center';
            ctx.fillText('No hay datos para mostrar', ctx.canvas.width / 2, ctx.canvas.height / 2);
            return;
        }

        const labels = distribucion.map(d => d.tipo);
        const data = distribucion.map(d => d.cantidad);

        // Colores actualizados para incluir Recurrentes e Inactivos
        const backgroundColors = distribucion.map(item => {
            switch (item.tipo) {
                case 'VIP':
                    return '#8b5cf6'; // Purple
                case 'Frecuente':
                    return '#3b82f6'; // Blue
                case 'Ocasional':
                    return '#f472b6'; // Pink
                case 'Recurrentes':
                    return '#10b981'; // Green
                case 'Nuevos':
                    return '#f59e0b'; // Yellow
                case 'Inactivos':
                    return '#6b7280'; // Gray
                default:
                    return '#94a3b8'; // Slate
            }
        });

        distribucionClientesChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors,
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                                return `${context.label}: ${context.raw} clientes (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '50%',
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });
    }

    function actualizarMetricasInventario(metrics) {
        const container = document.getElementById('metricas-inventario');

        container.innerHTML = `
        <div class="metric-card">
            <div class="text-3xl font-bold">${metrics.total_productos?.toLocaleString() || 0}</div>
            <div class="text-sm opacity-90">Total Productos</div>
            <div class="text-xs mt-2 flex items-center">
                <i class="fas fa-cubes mr-1"></i>
                <span>${metrics.productos_activos || 0} activos</span>
            </div>
        </div>
        <div class="metric-card">
            <div class="text-3xl font-bold">${metrics.stock_total?.toLocaleString() || 0}</div>
            <div class="text-sm opacity-90">Stock Total</div>
            <div class="text-xs mt-2 flex items-center">
                <i class="fas fa-boxes mr-1"></i>
                <span>Unidades en inventario</span>
            </div>
        </div>
        <div class="metric-card">
            <div class="text-3xl font-bold">S/ ${metrics.valor_total_inventario?.toFixed(2) || '0.00'}</div>
            <div class="text-sm opacity-90">Valor Total</div>
            <div class="text-xs mt-2 flex items-center">
                <i class="fas fa-dollar-sign mr-1"></i>
                <span>Valor del inventario</span>
            </div>
        </div>
        <div class="metric-card">
            <div class="text-3xl font-bold ${metrics.productos_stock_bajo > 0 ? 'text-yellow-300' : 'text-green-300'}">
                ${metrics.productos_stock_bajo || 0}
            </div>
            <div class="text-sm opacity-90">Stock Bajo</div>
            <div class="text-xs mt-2 flex items-center">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                <span>Necesitan reposición</span>
            </div>
        </div>
    `;
    }

    function actualizarTablaEstadoInventario(productos) {
        const tbody = document.getElementById('tabla-estado-inventario');

        if (!productos || productos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No hay productos que coincidan con los filtros</td></tr>';
            return;
        }

        let html = '';
        productos.forEach(producto => {
            const valorInventario = parseFloat(producto.precio) * parseInt(producto.stock);

            // Determinar estado y clases CSS
            let estado = '';
            let estadoClass = '';
            let stockClass = '';

            if (producto.stock === 0) {
                estado = '<span class="status-badge status-expirada">AGOTADO</span>';
                estadoClass = 'bg-red-50';
                stockClass = 'text-red-600 font-bold';
            } else if (producto.stock <= 5) {
                estado = '<span class="status-badge status-inactiva">STOCK BAJO</span>';
                estadoClass = 'bg-yellow-50';
                stockClass = 'text-orange-600 font-medium';
            } else {
                estado = '<span class="status-badge status-activa">DISPONIBLE</span>';
                stockClass = 'text-green-600 font-medium';
            }

            // Estado activo/inactivo
            const estadoActivo = producto.activo === '1' ?
                '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Activo</span>' :
                '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Inactivo</span>';

            // Imagen del producto
            const imagenHTML = producto.imagen ?
                `<img src="${producto.imagen}" alt="${producto.nombre}" class="w-10 h-10 rounded-lg object-cover border">` :
                '<div class="w-10 h-10 rounded-lg bg-gray-200 flex items-center justify-center border"><i class="fas fa-cube text-gray-400"></i></div>';

            html += `
        <tr class="fade-in hover:bg-gray-50 ${estadoClass}">
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="flex-shrink-0 mr-3">
                        ${imagenHTML}
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">${producto.nombre}</div>
                        <div class="text-xs text-gray-500">ID: ${producto.id}</div>
                        ${producto.descripcion ? `<div class="text-xs text-gray-400 mt-1">${producto.descripcion.substring(0, 50)}${producto.descripcion.length > 50 ? '...' : ''}</div>` : ''}
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                    ${producto.categoria_nombre}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                S/ ${parseFloat(producto.precio || 0).toFixed(2)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm ${stockClass}">
                <div class="flex items-center">
                    <span class="font-bold">${producto.stock}</span>
                    ${producto.stock <= 5 ? '<i class="fas fa-exclamation-triangle ml-1 text-orange-500"></i>' : ''}
                    ${producto.stock === 0 ? '<i class="fas fa-times-circle ml-1 text-red-500"></i>' : ''}
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                ${estado}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                S/ ${valorInventario.toFixed(2)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                ${estadoActivo}
            </td>
        </tr>
        `;
        });

        tbody.innerHTML = html;
    }


    function actualizarGraficoInventarioPorCategoria(inventario) {
        const ctx = document.getElementById('inventarioPorCategoriaChart').getContext('2d');

        if (inventarioPorCategoriaChart) {
            inventarioPorCategoriaChart.destroy();
        }

        console.log('Datos REALES para inventario por categoría:', inventario);

        // Verificar y limpiar los datos
        if (!inventario || inventario.length === 0) {
            mostrarMensajeSinDatos(ctx, 'No hay datos de categorías para mostrar');
            return;
        }

        // Usar los datos reales del backend
        const datosUsar = inventario;

        const labels = datosUsar.map(i => i.categoria_nombre || 'Sin Categoría');
        const dataStock = datosUsar.map(i => parseInt(i.stock_total) || 0);
        const dataValor = datosUsar.map(i => parseFloat(i.valor_total) || 0);

        console.log('Labels REALES:', labels);
        console.log('Stock data REAL:', dataStock);
        console.log('Valor data REAL:', dataValor);

        // Si todos los datos son cero, mostrar mensaje
        const totalStock = dataStock.reduce((a, b) => a + b, 0);
        const totalValor = dataValor.reduce((a, b) => a + b, 0);

        if (totalStock === 0 && totalValor === 0) {
            mostrarMensajeSinDatos(ctx, 'No hay stock disponible en las categorías');
            return;
        }

        // Colores vibrantes para mejor visibilidad
        const stockColors = '#f472b6'; // Rosa
        const valorColors = '#8b5cf6'; // Violeta

        inventarioPorCategoriaChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                        label: 'Stock Total',
                        data: dataStock,
                        backgroundColor: stockColors,
                        borderColor: stockColors,
                        borderWidth: 2,
                        borderRadius: 8,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Valor Total (S/)',
                        data: dataValor,
                        backgroundColor: valorColors,
                        borderColor: valorColors,
                        borderWidth: 2,
                        borderRadius: 8,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: '#374151',
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                        titleColor: '#1f2937',
                        bodyColor: '#374151',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                let value = context.raw;

                                if (context.dataset.label === 'Valor Total (S/)') {
                                    value = 'S/ ' + value.toFixed(2);
                                } else {
                                    value = value.toLocaleString();
                                }

                                return `${label}: ${value}`;
                            },
                            afterLabel: function(context) {
                                const categoria = datosUsar[context.dataIndex];
                                return [
                                    `Productos: ${categoria.total_productos || 0}`,
                                    `Agotados: ${categoria.productos_agotados || 0}`,
                                    `Stock Bajo: ${categoria.productos_stock_bajo || 0}`,
                                    `Disponibles: ${categoria.productos_disponibles || 0}`
                                ];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6b7280'
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Stock Total',
                            color: '#6b7280'
                        },
                        beginAtZero: true,
                        ticks: {
                            color: '#6b7280',
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Valor Total (S/)',
                            color: '#6b7280'
                        },
                        beginAtZero: true,
                        grid: {
                            drawOnChartArea: false,
                        },
                        ticks: {
                            color: '#6b7280',
                            callback: function(value) {
                                return 'S/ ' + value.toLocaleString();
                            }
                        }
                    }
                },
                animation: {
                    duration: 1000
                }
            }
        });
    }

    // Función auxiliar para mostrar mensajes en el canvas
    function mostrarMensajeSinDatos(ctx, mensaje) {
        ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
        ctx.font = '16px Arial';
        ctx.fillStyle = '#6b7280';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(mensaje, ctx.canvas.width / 2, ctx.canvas.height / 2);
    }

    function actualizarGraficoEstadoStock(estadoStock) {
        const ctx = document.getElementById('estadoStockChart').getContext('2d');

        if (estadoStockChart) {
            estadoStockChart.destroy();
        }

        console.log('Datos para estado de stock:', estadoStock);

        // Si no hay datos, usar datos de ejemplo
        if (!estadoStock || estadoStock.length === 0) {
            console.log('Usando datos de ejemplo para estado de stock');
            estadoStock = [{
                    estado: 'Disponible',
                    cantidad: 45,
                    color: '#10b981'
                },
                {
                    estado: 'Stock Bajo',
                    cantidad: 12,
                    color: '#f59e0b'
                },
                {
                    estado: 'Agotado',
                    cantidad: 8,
                    color: '#ef4444'
                }
            ];
        }

        const labels = estadoStock.map(e => e.estado);
        const data = estadoStock.map(e => parseInt(e.cantidad) || 0);

        // 🔥 SOLUCIÓN: Usar colores predefinidos en lugar de los que vienen del backend
        const backgroundColors = estadoStock.map((e, index) => {
            // Asignar colores específicos según el estado
            switch (e.estado.toLowerCase()) {
                case 'disponible':
                case 'disponibles':
                    return '#10b981'; // Verde
                case 'stock bajo':
                case 'bajo stock':
                case 'bajo':
                    return '#f59e0b'; // Amarillo/Naranja
                case 'agotado':
                case 'agotados':
                    return '#ef4444'; // Rojo
                case 'medio':
                case 'moderado':
                    return '#3b82f6'; // Azul
                default:
                    // Colores por defecto para otros estados
                    const defaultColors = ['#10b981', '#f59e0b', '#ef4444', '#3b82f6', '#8b5cf6', '#f472b6'];
                    return defaultColors[index % defaultColors.length];
            }
        });

        console.log('Estado stock labels:', labels);
        console.log('Estado stock data:', data);
        console.log('Estado stock colors DEFINITIVOS:', backgroundColors);

        estadoStockChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors,
                    borderWidth: 3, // Aumentar borde para mejor visibilidad
                    borderColor: '#ffffff',
                    hoverOffset: 20,
                    hoverBorderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            color: '#374151',
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                        titleColor: '#1f2937',
                        bodyColor: '#374151',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                                return `${context.label}: ${context.raw} productos (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '40%',
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 1000
                }
            }
        });
    }
</script>

<!-- Incluir footer -->
<?php require_once __DIR__ . '/../templates/footer.php'; ?>