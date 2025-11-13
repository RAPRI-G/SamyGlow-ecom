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
    from { opacity: 0; }
    to { opacity: 1; }
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
            switch(tabId) {
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
        return fecha.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' });
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

        const response = await fetch(`${API_BASE}api-reporte-productos&${params}`);
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
        console.error('Error:', error);
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

        const response = await fetch(`${API_BASE}api-reporte-clientes&${params}`);
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
        console.error('Error:', error);
        mostrarNotificacion('Error al cargar reporte de clientes: ' + error.message, 'error');
    } finally {
        ocultarLoading();
    }
}

// 🔹 REPORTE DE INVENTARIO
async function cargarReporteInventario() {
    try {
        mostrarLoading('Cargando reporte de inventario...');
        
        const categoriaId = document.getElementById('filtro-categoria-inventario').value;
        const estadoStock = document.getElementById('filtro-estado-stock').value;

        const params = new URLSearchParams({
            categoria_id: categoriaId,
            estado_stock: estadoStock
        });

        const response = await fetch(`${API_BASE}api-reporte-inventario&${params}`);
        const result = await response.json();

        if (!result.success) {
            throw new Error(result.error);
        }

        const data = result.data;
        
        actualizarMetricasInventario(data.metrics);
        actualizarTablaEstadoInventario(data.productos);
        actualizarGraficoInventarioPorCategoria(data.inventario_por_categoria);
        actualizarGraficoEstadoStock(data.estado_stock_data);

        mostrarNotificacion('Reporte de inventario actualizado', 'success');

    } catch (error) {
        console.error('Error:', error);
        mostrarNotificacion('Error al cargar reporte de inventario: ' + error.message, 'error');
    } finally {
        ocultarLoading();
    }
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
    setTimeout(() => {
        mostrarNotificacion('Reporte de inventario generado exitosamente', 'success');
    }, 1500);
}

// Funciones placeholder para los otros reportes (las implementaremos después)
function actualizarMetricasProductos(metrics) {
    const container = document.getElementById('metricas-productos');
    container.innerHTML = `
        <div class="metric-card">
            <div class="text-3xl font-bold">${metrics.total_unidades || 0}</div>
            <div class="text-sm opacity-90">Productos Vendidos</div>
            <div class="text-xs mt-2 flex items-center">
                <i class="fas fa-shopping-cart mr-1"></i>
                <span>Total unidades</span>
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
            <div class="text-3xl font-bold">S/ ${metrics.producto_mas_caro?.toFixed(2) || '0.00'}</div>
            <div class="text-sm opacity-90">Producto Más Caro Vendido</div>
            <div class="text-xs mt-2">${metrics.nombre_producto_caro || 'N/A'}</div>
        </div>
    `;
}

function actualizarTablaProductosVendidos(productos) {
    const tbody = document.getElementById('tabla-productos-vendidos');
    
    if (productos.length === 0) {
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
        
        html += `
        <tr class="fade-in">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${posicionBadge}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${producto.nombre}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${producto.categoria_nombre}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${producto.cantidad_vendida}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">S/ ${parseFloat(producto.ingresos_totales || 0).toFixed(2)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm ${producto.stock_actual <= 5 ? 'text-red-600 font-medium' : 'text-gray-900'}">${producto.stock_actual}</td>
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

    if (top_productos.length === 0) {
        ctx.font = '16px Arial';
        ctx.fillStyle = '#999';
        ctx.textAlign = 'center';
        ctx.fillText('No hay datos para mostrar', ctx.canvas.width / 2, ctx.canvas.height / 2);
        return;
    }

    const labels = top_productos.map(p => p.nombre);
    const data = top_productos.map(p => parseInt(p.cantidad_vendida));

    productosMasVendidosChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Unidades Vendidas',
                data: data,
                backgroundColor: '#f472b6',
                borderColor: '#f472b6',
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true
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

    if (distribucion.length === 0) {
        ctx.font = '16px Arial';
        ctx.fillStyle = '#999';
        ctx.textAlign = 'center';
        ctx.fillText('No hay datos para mostrar', ctx.canvas.width / 2, ctx.canvas.height / 2);
        return;
    }

    const labels = distribucion.map(d => d.categoria);
    const data = distribucion.map(d => d.cantidad);
    const backgroundColors = ['#f472b6', '#a78bfa', '#60a5fa', '#34d399', '#fbbf24'];

    distribucionCategoriaChart = new Chart(ctx, {
        type: 'pie',
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
                }
            }
        }
    });
}

function actualizarMetricasClientes(metrics) {
    const container = document.getElementById('metricas-clientes');
    container.innerHTML = `
        <div class="metric-card">
            <div class="text-3xl font-bold">${metrics.total_clientes || 0}</div>
            <div class="text-sm opacity-90">Clientes Totales</div>
            <div class="text-xs mt-2 flex items-center">
                <i class="fas fa-users mr-1"></i>
                <span>En base de datos</span>
            </div>
        </div>
        <div class="metric-card">
            <div class="text-3xl font-bold">S/ ${metrics.ticket_promedio?.toFixed(2) || '0.00'}</div>
            <div class="text-sm opacity-90">Ticket Promedio</div>
            <div class="text-xs mt-2 flex items-center">
                <i class="fas fa-receipt mr-1"></i>
                <span>Gasto promedio</span>
            </div>
        </div>
        <div class="metric-card">
            <div class="text-3xl font-bold">${metrics.frecuencia_compra?.toFixed(1) || '0.0'}</div>
            <div class="text-sm opacity-90">Frecuencia de Compra</div>
            <div class="text-xs mt-2">Promedio de pedidos por cliente</div>
        </div>
    `;
}

function actualizarTablaClientesFrecuentes(clientes) {
    const tbody = document.getElementById('tabla-clientes-frecuentes');
    
    if (clientes.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay clientes frecuentes</td></tr>';
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
        
        const ultimaCompra = cliente.ultimo_pedido ? new Date(cliente.ultimo_pedido).toLocaleDateString('es-ES') : 'Sin compras';
        
        html += `
        <tr class="fade-in">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${posicionBadge}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${cliente.nombres} ${cliente.apellidos}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${cliente.correo}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${cliente.total_pedidos || 0}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">S/ ${parseFloat(cliente.total_gastado || 0).toFixed(2)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${ultimaCompra}</td>
        </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function actualizarGraficoClientesPorGasto(top_clientes) {
    const ctx = document.getElementById('clientesPorGastoChart').getContext('2d');
    
    if (clientesPorGastoChart) {
        clientesPorGastoChart.destroy();
    }

    if (top_clientes.length === 0) {
        ctx.font = '16px Arial';
        ctx.fillStyle = '#999';
        ctx.textAlign = 'center';
        ctx.fillText('No hay datos para mostrar', ctx.canvas.width / 2, ctx.canvas.height / 2);
        return;
    }

    const labels = top_clientes.map(c => `${c.nombres} ${c.apellidos}`.substring(0, 15) + '...');
    const data = top_clientes.map(c => parseFloat(c.total_gastado || 0));

    clientesPorGastoChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Gastado (S/)',
                data: data,
                backgroundColor: '#a78bfa',
                borderColor: '#a78bfa',
                borderWidth: 0
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

function actualizarGraficoDistribucionClientes(distribucion) {
    const ctx = document.getElementById('distribucionClientesChart').getContext('2d');
    
    if (distribucionClientesChart) {
        distribucionClientesChart.destroy();
    }

    if (distribucion.length === 0) {
        ctx.font = '16px Arial';
        ctx.fillStyle = '#999';
        ctx.textAlign = 'center';
        ctx.fillText('No hay datos para mostrar', ctx.canvas.width / 2, ctx.canvas.height / 2);
        return;
    }

    const labels = distribucion.map(d => d.tipo);
    const data = distribucion.map(d => d.cantidad);
    const backgroundColors = ['#60a5fa', '#a78bfa', '#f472b6'];

    distribucionClientesChart = new Chart(ctx, {
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
                }
            }
        }
    });
}

function actualizarMetricasInventario(metrics) {
    const container = document.getElementById('metricas-inventario');
    container.innerHTML = `
        <div class="metric-card">
            <div class="text-3xl font-bold">${metrics.total_productos || 0}</div>
            <div class="text-sm opacity-90">Productos Totales</div>
            <div class="text-xs mt-2 flex items-center">
                <i class="fas fa-cubes mr-1"></i>
                <span>En inventario</span>
            </div>
        </div>
        <div class="metric-card">
            <div class="text-3xl font-bold">${metrics.productos_stock_bajo || 0}</div>
            <div class="text-sm opacity-90">Productos con Stock Bajo</div>
            <div class="text-xs mt-2 flex items-center">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                <span>Necesitan reposición</span>
            </div>
        </div>
        <div class="metric-card">
            <div class="text-3xl font-bold">${metrics.productos_agotados || 0}</div>
            <div class="text-sm opacity-90">Productos Agotados</div>
            <div class="text-xs mt-2 flex items-center">
                <i class="fas fa-times-circle mr-1"></i>
                <span>Sin stock</span>
            </div>
        </div>
        <div class="metric-card">
            <div class="text-3xl font-bold">S/ ${metrics.valor_total_inventario?.toFixed(2) || '0.00'}</div>
            <div class="text-sm opacity-90">Valor Total Inventario</div>
            <div class="text-xs mt-2">Valor estimado</div>
        </div>
    `;
}

function actualizarTablaEstadoInventario(productos) {
    const tbody = document.getElementById('tabla-estado-inventario');
    
    if (productos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay productos en el inventario</td></tr>';
        return;
    }
    
    let html = '';
    productos.forEach(producto => {
        const valorInventario = parseFloat(producto.precio) * parseInt(producto.stock);
        let estado = '';
        
        if (producto.stock === 0) {
            estado = '<span class="status-badge status-expirada">AGOTADO</span>';
        } else if (producto.stock <= 5) {
            estado = '<span class="status-badge status-inactiva">STOCK BAJO</span>';
        } else {
            estado = '<span class="status-badge status-activa">DISPONIBLE</span>';
        }
        
        html += `
        <tr class="fade-in">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${producto.nombre}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${producto.categoria_nombre}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">S/ ${parseFloat(producto.precio || 0).toFixed(2)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm ${producto.stock <= 5 ? 'text-red-600 font-medium' : 'text-gray-900'}">${producto.stock}</td>
            <td class="px-6 py-4 whitespace-nowrap">${estado}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">S/ ${valorInventario.toFixed(2)}</td>
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

    if (inventario.length === 0) {
        ctx.font = '16px Arial';
        ctx.fillStyle = '#999';
        ctx.textAlign = 'center';
        ctx.fillText('No hay datos para mostrar', ctx.canvas.width / 2, ctx.canvas.height / 2);
        return;
    }

    const labels = inventario.map(i => i.categoria);
    const data = inventario.map(i => i.stock);
    const backgroundColors = ['#f472b6', '#a78bfa', '#60a5fa', '#34d399', '#fbbf24'];

    inventarioPorCategoriaChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Unidades en Stock',
                data: data,
                backgroundColor: backgroundColors,
                borderColor: backgroundColors,
                borderWidth: 0
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
                    beginAtZero: true
                }
            }
        }
    });
}

function actualizarGraficoEstadoStock(estadoStock) {
    const ctx = document.getElementById('estadoStockChart').getContext('2d');
    
    if (estadoStockChart) {
        estadoStockChart.destroy();
    }

    if (estadoStock.length === 0) {
        ctx.font = '16px Arial';
        ctx.fillStyle = '#999';
        ctx.textAlign = 'center';
        ctx.fillText('No hay datos para mostrar', ctx.canvas.width / 2, ctx.canvas.height / 2);
        return;
    }

    const labels = estadoStock.map(e => e.estado);
    const data = estadoStock.map(e => e.cantidad);
    const backgroundColors = ['#10b981', '#f59e0b', '#ef4444'];

    estadoStockChart = new Chart(ctx, {
        type: 'pie',
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
                }
            }
        }
    });
}
</script>

<!-- Incluir footer -->
<?php require_once __DIR__ . '/../templates/footer.php'; ?>