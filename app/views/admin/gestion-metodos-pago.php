<?php
// app/views/admin/gestion-metodos-pago.php
?>
<style>
    :root {
        --primary: #f472b6;
        --secondary: #a78bfa;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
    }

    .sidebar {
        background: linear-gradient(180deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
    }

    .active-menu {
        background: rgba(255, 255, 255, 0.2);
        border-right: 4px solid white;
    }

    .content-section {
        display: none;
    }

    .content-section.active {
        display: block;
        animation: fadeIn 0.5s ease;
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

    .tab-button {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .tab-button.active {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        box-shadow: 0 4px 12px rgba(244, 114, 182, 0.3);
    }

    .tab-button::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 3px;
        background: var(--primary);
        transition: all 0.3s ease;
        transform: translateX(-50%);
    }

    .tab-button.active::after {
        width: 80%;
    }

    .card-hover {
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }

    .card-hover:hover {
        transform: translateY(-5px);
        border-color: var(--primary);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .status-active {
        background: rgba(16, 185, 129, 0.1);
        color: #065f46;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .status-inactive {
        background: rgba(239, 68, 68, 0.1);
        color: #7f1d1d;
        border: 1px solid rgba(239, 68, 68, 0.2);
    }

    .type-badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }

    .type-digital {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
    }

    .type-card {
        background: linear-gradient(135deg, #10b981, #047857);
        color: white;
    }

    .type-cash {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .type-transfer {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: white;
    }

    .progress-bar {
        height: 8px;
        border-radius: 10px;
        background: #e5e7eb;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        border-radius: 10px;
        transition: width 1s ease-in-out;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }

    .floating-action-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 20px rgba(244, 114, 182, 0.4);
        cursor: pointer;
        z-index: 100;
        transition: all 0.3s ease;
    }

    .floating-action-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 8px 25px rgba(244, 114, 182, 0.6);
    }

    .method-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }

    .method-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .method-header {
        padding: 20px;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .method-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .icon-digital {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
    }

    .icon-card {
        background: linear-gradient(135deg, #10b981, #047857);
        color: white;
    }

    .icon-cash {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .icon-transfer {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: white;
    }

    .pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }

    .slide-in {
        animation: slideIn 0.5s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

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
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background-color: white;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 25px rgba(0, 0, 0, 0.2);
    }

    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        z-index: 1100;
        display: flex;
        align-items: center;
        gap: 10px;
        transform: translateX(150%);
        transition: transform 0.3s ease;
    }

    .notification.show {
        transform: translateX(0);
    }

    .notification.success {
        background: var(--success);
    }

    .notification.error {
        background: var(--danger);
    }

    .notification.info {
        background: #3b82f6;
    }

    .section-transition {
        animation: sectionTransition 0.4s ease;
    }

    @keyframes sectionTransition {
        from {
            opacity: 0;
            transform: translateX(20px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .tab-panel {
        display: none;
    }

    .tab-panel.active {
        display: block;
    }

    .loading {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #f472b6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>
<main class="flex-1 overflow-y-auto p-6">
    <!-- Sección de Métodos de Pago -->
    <div class="content-section active" id="metodos-pago">
        <!-- Header con estadísticas rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8" id="stats-cards">
            <div class="stat-card border-l-blue-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm text-gray-500">Total Métodos</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-1">
                            <span class="loading"></span>
                        </h3>
                    </div>
                    <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                        <i class="fas fa-credit-card"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Cargando...</p>
            </div>
        </div>

        <!-- Tabs de navegación -->
        <div class="bg-white rounded-xl shadow-sm mb-6 overflow-hidden">
            <div class="flex border-b overflow-x-auto">
                <button class="tab-button flex-1 py-4 px-6 text-center font-medium text-gray-600 hover:bg-gray-50 active" data-tab="lista-metodos">
                    <i class="fas fa-list mr-2"></i>Todos los Métodos
                </button>
                <button class="tab-button flex-1 py-4 px-6 text-center font-medium text-gray-600 hover:bg-gray-50" data-tab="estadisticas">
                    <i class="fas fa-chart-pie mr-2"></i>Estadísticas
                </button>
                <button class="tab-button flex-1 py-4 px-6 text-center font-medium text-gray-600 hover:bg-gray-50" data-tab="configuracion">
                    <i class="fas fa-cog mr-2"></i>Configuración
                </button>
            </div>
        </div>

        <!-- Contenido de las pestañas -->
        <div id="tab-content">
            <!-- Pestaña: Lista de Métodos -->
            <div class="tab-panel active section-transition" id="lista-metodos-panel">
                <div class="mb-6 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-800">Métodos de Pago Disponibles</h2>
                    <button id="btn-new-method" class="bg-gradient-to-r from-pink-500 to-purple-600 hover:from-pink-600 hover:to-purple-700 text-white font-medium py-2 px-4 rounded-lg transition-all duration-300 flex items-center gap-2">
                        <i class="fas fa-plus"></i> Nuevo Método
                    </button>
                </div>

                <!-- Filtros -->
                <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600">Filtrar por:</span>
                            <select id="filter-type" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500 text-sm">
                                <option value="all">Todos los tipos</option>
                                <option value="digital">Pago Digital</option>
                                <option value="card">Tarjeta</option>
                                <option value="cash">Efectivo</option>
                                <option value="transfer">Transferencia</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600">Estado:</span>
                            <select id="filter-status" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500 text-sm">
                                <option value="all">Todos</option>
                                <option value="active">Activos</option>
                                <option value="inactive">Inactivos</option>
                            </select>
                        </div>
                        <div class="flex-1"></div>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            <input type="text" id="search-methods" placeholder="Buscar método..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 w-64 text-sm">
                        </div>
                    </div>
                </div>

                <!-- Grid de Métodos de Pago -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="methods-grid">
                    <div class="col-span-full text-center py-8">
                        <div class="loading mx-auto mb-4"></div>
                        <p class="text-gray-500">Cargando métodos de pago...</p>
                    </div>
                </div>
            </div>

            <!-- Pestaña: Estadísticas -->
            <div class="tab-panel section-transition" id="estadisticas-panel">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Estadísticas de Métodos de Pago</h2>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Gráfico de distribución -->
                        <div>
                            <h3 class="font-bold text-gray-800 mb-4">Distribución de Uso</h3>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <canvas id="distributionChart" height="250"></canvas>
                            </div>
                        </div>

                        <!-- Tendencias mensuales -->
                        <div>
                            <h3 class="font-bold text-gray-800 mb-4">Tendencias Mensuales</h3>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <canvas id="trendsChart" height="250"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Métricas detalladas -->
                    <div class="mt-8">
                        <h3 class="font-bold text-gray-800 mb-4">Métricas Detalladas</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pedidos</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Promedio</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">% del Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tendencia</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="stats-table">
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            <div class="loading mx-auto mb-2"></div>
                                            Cargando estadísticas...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pestaña: Configuración -->
            <div class="tab-panel section-transition" id="configuracion-panel">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Configuración de Métodos de Pago</h2>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Configuración General -->
                        <div>
                            <h3 class="font-bold text-gray-800 mb-4">Configuración General</h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-800">Habilitar múltiples métodos</p>
                                        <p class="text-sm text-gray-600">Permitir que los clientes elijan entre varios métodos</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="multi-methods" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-500"></div>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-800">Notificaciones de pago</p>
                                        <p class="text-sm text-gray-600">Recibir alertas por nuevos pagos</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="payment-notifications" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-500"></div>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-800">Confirmación automática</p>
                                        <p class="text-sm text-gray-600">Confirmar pedidos automáticamente al recibir pago</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="auto-confirm" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-500"></div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Preferencias de Métodos -->
                        <div>
                            <h3 class="font-bold text-gray-800 mb-4">Preferencias de Métodos</h3>
                            <div class="space-y-4">
                                <div class="p-4 border border-gray-200 rounded-lg">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Método predeterminado</label>
                                    <select id="default-method" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                                        <option value="">Cargando métodos...</option>
                                    </select>
                                </div>

                                <div class="p-4 border border-gray-200 rounded-lg">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Orden de visualización</label>
                                    <div class="space-y-2" id="method-order">
                                        <div class="text-center py-4 text-gray-500">
                                            <div class="loading mx-auto mb-2"></div>
                                            Cargando orden...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button id="save-settings" class="bg-pink-500 hover:bg-pink-600 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                            <i class="fas fa-save mr-2"></i> Guardar Configuración
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Botón flotante para agregar método -->
<div class="floating-action-btn" id="addMethodBtn">
    <i class="fas fa-plus text-xl"></i>
</div>

<!-- Modal para Ver/Editar Método -->
<div class="modal" id="methodModal">
    <div class="modal-content">
        <div class="p-6 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800" id="modalTitle">Detalles del Método de Pago</h3>
            <button class="text-gray-500 hover:text-gray-700" id="closeModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6" id="modalContent">
            <!-- El contenido se cargará dinámicamente -->
        </div>
    </div>
</div>

<!-- Modal para Nuevo Método -->
<div class="modal" id="newMethodModal">
    <div class="modal-content">
        <div class="p-6 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Nuevo Método de Pago</h3>
            <button class="text-gray-500 hover:text-gray-700" id="closeNewModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6">
            <form id="newMethodForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Método *</label>
                    <input type="text" id="method-name" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Método *</label>
                    <select id="method-type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
                        <option value="">Selecciona un tipo</option>
                        <option value="digital">Pago Digital</option>
                        <option value="card">Tarjeta de Crédito/Débito</option>
                        <option value="cash">Efectivo</option>
                        <option value="transfer">Transferencia Bancaria</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea id="method-description" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" rows="3"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Icono</label>
                    <select id="method-icon" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                        <option value="fab fa-google-wallet">Yape/Plin</option>
                        <option value="fas fa-mobile-alt">Móvil</option>
                        <option value="far fa-credit-card">Tarjeta</option>
                        <option value="fas fa-university">Banco</option>
                        <option value="fas fa-money-bill-wave">Efectivo</option>
                        <option value="fas fa-wallet">Billetera</option>
                    </select>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="method-active" class="rounded border-gray-300 text-pink-600 focus:ring-pink-500" checked>
                    <label for="method-active" class="ml-2 text-sm text-gray-700">Método activo</label>
                </div>

                <div class="flex justify-end space-x-4 pt-4">
                    <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-4 rounded transition-colors" id="cancelNewMethod">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white font-medium py-2 px-4 rounded transition-colors flex items-center gap-2">
                        <i class="fas fa-plus"></i> Crear Método
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Notificaciones -->
<div class="notification" id="notification">
    <i class="fas fa-check-circle"></i>
    <span id="notification-text">Operación completada con éxito</span>
</div>

<script>
    // Variables globales
    let paymentMethods = [];
    let settings = {};
    let distributionChart = null;
    let trendsChart = null;

    // Inicialización
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 Iniciando aplicación de métodos de pago...');
        initApp();
    });

    async function initApp() {
        try {
            console.log('🚀 Iniciando aplicación de métodos de pago...');

            // Debug: Verificar datos iniciales
            console.log('🔍 Debug - paymentMethods inicial:', paymentMethods);

            // Configurar event listeners primero
            setupBasicEventListeners();
            setupTabsNavigation();
            setupFilters();

            // Cargar datos
            await loadDataFromDatabase();

            // Debug: Verificar datos cargados
            console.log('🔍 Debug - paymentMethods después de carga:', paymentMethods);
            console.log('🔍 Debug - IDs disponibles:', paymentMethods.map(m => m.id));

            // Configurar interfaz con los datos
            loadStatsCards();
            loadMethodsGrid();
            loadSettings();

            console.log('✅ Aplicación inicializada correctamente');
        } catch (error) {
            console.error('❌ Error inicializando app:', error);
            showNotification('Error al cargar los datos', 'error');
        }
    }

    function setupBasicEventListeners() {
        console.log('🔧 Configurando event listeners...');

        // Botones principales
        document.getElementById('btn-new-method')?.addEventListener('click', showNewMethodModal);
        document.getElementById('addMethodBtn')?.addEventListener('click', showNewMethodModal);
        document.getElementById('save-settings')?.addEventListener('click', saveSettings);

        // Modales
        document.getElementById('closeModal')?.addEventListener('click', closeModal);
        document.getElementById('closeNewModal')?.addEventListener('click', closeNewMethodModal);
        document.getElementById('cancelNewMethod')?.addEventListener('click', closeNewMethodModal);

        // Formularios
        document.getElementById('newMethodForm')?.addEventListener('submit', createNewMethod);

        // Cerrar modales al hacer clic fuera
        document.getElementById('methodModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
        document.getElementById('newMethodModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeNewMethodModal();
        });
    }

    function setupTabsNavigation() {
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function() {
                // Remover clase activa de todos los botones
                document.querySelectorAll('.tab-button').forEach(btn => {
                    btn.classList.remove('active');
                });

                // Agregar clase activa al botón clickeado
                this.classList.add('active');

                // Ocultar todos los paneles
                document.querySelectorAll('.tab-panel').forEach(panel => {
                    panel.classList.remove('active');
                });

                // Mostrar el panel correspondiente
                const tabId = this.getAttribute('data-tab');
                const panel = document.getElementById(tabId + '-panel');
                if (panel) {
                    panel.classList.add('active');
                }

                // Si es la pestaña de estadísticas, actualizar gráficos
                if (tabId === 'estadisticas') {
                    setTimeout(() => {
                        initCharts();
                    }, 100);
                }
            });
        });
    }

    function setupFilters() {
        document.getElementById('filter-type')?.addEventListener('change', filterMethods);
        document.getElementById('filter-status')?.addEventListener('change', filterMethods);
        document.getElementById('search-methods')?.addEventListener('input', filterMethods);
    }

    // Función para cargar datos desde la base de datos
    async function loadDataFromDatabase() {
        try {
            console.log('📥 Cargando datos desde la base de datos...');

            // Cargar estadísticas (que incluyen los métodos)
            const statsResponse = await fetchAPI('api-estadisticas-metodos-pago');
            console.log('📊 Respuesta de estadísticas:', statsResponse);

            if (statsResponse.success && statsResponse.data) {
                paymentMethods = statsResponse.data;
                console.log('✅ Métodos cargados:', paymentMethods.length);
            } else {
                console.error('❌ Error cargando estadísticas:', statsResponse.error);
                paymentMethods = [];
            }

            // Cargar configuración
            const settingsResponse = await fetchAPI('api-configuracion-metodos-pago');
            console.log('⚙️ Respuesta de configuración:', settingsResponse);

            if (settingsResponse.success && settingsResponse.data) {
                settings = settingsResponse.data;
                console.log('✅ Configuración cargada');
            } else {
                console.error('❌ Error cargando configuración:', settingsResponse.error);
                settings = {};
            }

        } catch (error) {
            console.error('❌ Error cargando datos:', error);
            throw error;
        }
    }

    // Función genérica para llamadas API
    async function fetchAPI(endpoint) {
        try {
            console.log(`🌐 Haciendo request a: ${endpoint}`);
            const response = await fetch(`index.php?view=${endpoint}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            });

            if (!response.ok) {
                throw new Error(`Error ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            return data;

        } catch (error) {
            console.error(`❌ Error en API call ${endpoint}:`, error);
            throw error;
        }
    }

    // Función para enviar datos a la API
    async function postAPI(endpoint, data) {
        try {
            console.log(`📤 Enviando datos a: ${endpoint}`, data);
            const response = await fetch(`index.php?view=${endpoint}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                throw new Error(`Error ${response.status}: ${response.statusText}`);
            }

            return await response.json();
        } catch (error) {
            console.error(`❌ Error en POST API call ${endpoint}:`, error);
            throw error;
        }
    }

    function loadStatsCards() {
        const totalMethods = paymentMethods.length;
        const activeMethods = paymentMethods.filter(m => m.activo == 1).length;

        // Calcular estadísticas de pedidos
        const stats = paymentMethods.reduce((acc, method) => {
            acc.totalOrders += parseInt(method.pedidos_mes) || 0;
            acc.totalAmount += parseFloat(method.total_mes) || 0;
            return acc;
        }, {
            totalOrders: 0,
            totalAmount: 0
        });

        const popularMethod = paymentMethods.reduce((prev, current) => {
            const prevOrders = parseInt(prev.pedidos_mes) || 0;
            const currentOrders = parseInt(current.pedidos_mes) || 0;
            return (prevOrders > currentOrders) ? prev : current;
        }, {
            pedidos_mes: 0,
            nombre: 'Ninguno'
        });

        const statsHTML = `
            <div class="stat-card border-l-blue-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm text-gray-500">Total Métodos</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-1">${totalMethods}</h3>
                    </div>
                    <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                        <i class="fas fa-credit-card"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">${activeMethods} activos, ${totalMethods - activeMethods} inactivos</p>
            </div>
            
            <div class="stat-card border-l-green-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm text-gray-500">Pedidos del Mes</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-1">${stats.totalOrders}</h3>
                    </div>
                    <div class="p-3 rounded-full bg-green-100 text-green-500">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">+${Math.floor(Math.random() * 20)}% vs mes anterior</p>
            </div>
            
            <div class="stat-card border-l-purple-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm text-gray-500">Total Procesado</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-1">S/ ${stats.totalAmount.toLocaleString('es-PE', {minimumFractionDigits: 2})}</h3>
                    </div>
                    <div class="p-3 rounded-full bg-purple-100 text-purple-500">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">+${Math.floor(Math.random() * 25)}% vs mes anterior</p>
            </div>
            
            <div class="stat-card border-l-yellow-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm text-gray-500">Método Popular</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-1">${popularMethod.nombre}</h3>
                    </div>
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                        <i class="fas fa-crown"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">${popularMethod.pedidos_mes && stats.totalOrders > 0 ? Math.round((popularMethod.pedidos_mes / stats.totalOrders) * 100) : 0}% de los pedidos</p>
            </div>
        `;

        document.getElementById('stats-cards').innerHTML = statsHTML;

        // Actualizar contador en el sidebar
        const sidebarCount = document.getElementById('sidebar-methods-count');
        if (sidebarCount) {
            sidebarCount.textContent = activeMethods;
        }
    }

    function loadMethodsGrid() {
        const methodsGrid = document.getElementById('methods-grid');

        if (!methodsGrid) {
            console.error('❌ No se encontró el contenedor methods-grid');
            return;
        }

        console.log('🔄 Cargando grid de métodos...');

        if (paymentMethods.length === 0) {
            methodsGrid.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-credit-card text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay métodos de pago</h3>
                    <p class="text-gray-500 mb-4">Comienza agregando tu primer método de pago.</p>
                    <button class="bg-pink-500 hover:bg-pink-600 text-white font-medium py-2 px-4 rounded-lg transition-colors" onclick="showNewMethodModal()">
                        <i class="fas fa-plus mr-2"></i> Agregar Primer Método
                    </button>
                </div>
            `;
            return;
        }

        // Calcular total de pedidos para porcentajes
        const totalOrders = paymentMethods.reduce((sum, method) => {
            return sum + (parseInt(method.pedidos_mes) || 0);
        }, 0);

        let methodsHTML = '';

        paymentMethods.forEach((method, index) => {
            const orders = parseInt(method.pedidos_mes) || 0;
            const percentage = totalOrders > 0 ? Math.round((orders / totalOrders) * 100) : 0;
            const typeClass = `type-${method.tipo}`;
            const iconClass = `icon-${method.tipo}`;
            const isActive = method.activo == 1;

            methodsHTML += `
                <div class="method-card slide-in" style="animation-delay: ${index * 0.1}s">
                    <div class="method-header">
                        <div class="flex items-center gap-3">
                            <div class="method-icon ${iconClass}">
                                <i class="${method.icono || 'fas fa-credit-card'}"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800">${method.nombre}</h3>
                                <span class="type-badge ${typeClass}">${getTypeName(method.tipo)}</span>
                            </div>
                        </div>
                        <span class="status-badge ${isActive ? 'status-active' : 'status-inactive'}">
                            <i class="fas fa-circle text-xs"></i> ${isActive ? 'Activo' : 'Inactivo'}
                        </span>
                    </div>
                    <div class="p-5">
                        <div class="flex justify-between text-sm mb-4">
                            <span class="text-gray-600">Pedidos este mes:</span>
                            <span class="font-bold text-gray-800">${orders}</span>
                        </div>
                        <div class="flex justify-between text-sm mb-4">
                            <span class="text-gray-600">Total procesado:</span>
                            <span class="font-bold text-gray-800">S/ ${(parseFloat(method.total_mes) || 0).toLocaleString('es-PE', {minimumFractionDigits: 2})}</span>
                        </div>
                        <div class="mb-4">
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-gray-600">Porcentaje de uso:</span>
                                <span class="font-bold text-gray-800">${percentage}%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: ${percentage}%; background-color: ${getChartColorByType(method.tipo)}"></div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-3 rounded-lg transition-colors text-sm flex items-center justify-center gap-1 view-method-btn" data-id="${method.id}">
                                <i class="fas fa-eye"></i> Ver
                            </button>
                            <button class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-3 rounded-lg transition-colors text-sm flex items-center justify-center gap-1 edit-method-btn" data-id="${method.id}">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        // Agregar tarjeta para nuevo método
        methodsHTML += `
            <div class="method-card border-2 border-dashed border-gray-300 hover:border-pink-400 transition-colors flex flex-col items-center justify-center p-8 text-center">
                <div class="w-16 h-16 rounded-full bg-pink-100 flex items-center justify-center mb-4">
                    <i class="fas fa-plus text-pink-500 text-2xl"></i>
                </div>
                <h3 class="font-bold text-gray-800 mb-2">Agregar Nuevo Método</h3>
                <p class="text-gray-600 text-sm mb-4">Configura un nuevo método de pago para tus clientes</p>
                <button class="bg-pink-500 hover:bg-pink-600 text-white py-2 px-4 rounded-lg transition-colors text-sm flex items-center gap-2" onclick="showNewMethodModal()">
                    <i class="fas fa-plus"></i> Crear Método
                </button>
            </div>
        `;

        methodsGrid.innerHTML = methodsHTML;

        // Agregar event listeners a los botones
        setTimeout(() => {
            document.querySelectorAll('.view-method-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const methodId = parseInt(this.getAttribute('data-id'));
                    console.log('👁️ Ver método:', methodId);
                    viewMethod(methodId);
                });
            });

            document.querySelectorAll('.edit-method-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const methodId = parseInt(this.getAttribute('data-id'));
                    console.log('✏️ Editar método:', methodId);
                    editMethod(methodId);
                });
            });
        }, 100);

        console.log(`✅ Grid cargado: ${paymentMethods.length} métodos`);
    }

    function viewMethod(methodId) {
        console.log('🔍 Abriendo vista del método:', methodId);

        const method = paymentMethods.find(m => m.id === methodId);
        if (!method) {
            showNotification('Método no encontrado', 'error');
            return;
        }

        // Calcular estadísticas para la vista detallada
        const totalOrdersAll = paymentMethods.reduce((sum, m) => sum + (parseInt(m.pedidos_mes) || 0), 0);
        const percentage = totalOrdersAll > 0 ? Math.round(((parseInt(method.pedidos_mes) || 0) / totalOrdersAll) * 100) : 0;
        const average = (parseInt(method.pedidos_mes) || 0) > 0 ?
            (parseFloat(method.total_mes) || 0) / (parseInt(method.pedidos_mes) || 1) : 0;

        document.getElementById('modalTitle').textContent = `Detalles: ${method.nombre}`;

        const modalContent = `
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <div class="method-icon icon-${method.tipo}">
                    <i class="${method.icono || 'fas fa-credit-card'}"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-gray-800">${method.nombre}</h3>
                    <span class="type-badge type-${method.tipo}">${getTypeName(method.tipo)}</span>
                </div>
            </div>
            
            <div>
                <p class="text-sm text-gray-600 mb-2">Descripción:</p>
                <p class="text-gray-800">${method.descripcion || 'Sin descripción'}</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-sm text-gray-600">Estado</p>
                    <p class="font-medium ${method.activo == 1 ? 'text-green-600' : 'text-red-600'}">${method.activo == 1 ? 'Activo' : 'Inactivo'}</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-sm text-gray-600">Pedidos este mes</p>
                    <p class="font-medium text-gray-800">${parseInt(method.pedidos_mes) || 0}</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-sm text-gray-600">Total procesado</p>
                    <p class="font-medium text-gray-800">S/ ${(parseFloat(method.total_mes) || 0).toLocaleString('es-PE', {minimumFractionDigits: 2})}</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-sm text-gray-600">Ticket promedio</p>
                    <p class="font-medium text-gray-800">S/ ${average.toLocaleString('es-PE', {minimumFractionDigits: 2})}</p>
                </div>
            </div>
            
            <div class="flex gap-2 pt-4">
                <button class="flex-1 bg-pink-500 hover:bg-pink-600 text-white py-2 px-3 rounded-lg transition-colors" id="edit-from-view-btn">
                    <i class="fas fa-edit mr-1"></i> Editar
                </button>
                <button class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2 px-3 rounded-lg transition-colors" id="delete-from-view-btn">
                    <i class="fas fa-trash mr-1"></i> Eliminar
                </button>
                <button class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-3 rounded-lg transition-colors" onclick="closeModal()">
                    <i class="fas fa-times mr-1"></i> Cerrar
                </button>
            </div>
        </div>
    `;

        document.getElementById('modalContent').innerHTML = modalContent;
        document.getElementById('methodModal').classList.add('active');

        // Agregar event listeners después de que el modal se renderice
        setTimeout(() => {
            document.getElementById('edit-from-view-btn').addEventListener('click', function() {
                closeModal();
                setTimeout(() => {
                    editMethod(methodId);
                }, 300);
            });

            document.getElementById('delete-from-view-btn').addEventListener('click', function() {
                closeModal();
                setTimeout(() => {
                    if (confirm('¿Estás seguro de que quieres eliminar este método de pago?')) {
                        deleteMethod(methodId);
                    }
                }, 300);
            });
        }, 100);
    }

    function editMethod(methodId) {
        console.log('✏️ Abriendo edición del método:', methodId);

        const method = paymentMethods.find(m => m.id === methodId);
        if (!method) {
            showNotification('Método no encontrado', 'error');
            return;
        }

        document.getElementById('modalTitle').textContent = `Editar: ${method.nombre}`;

        const modalContent = `
        <form id="editMethodForm" class="space-y-4">
            <input type="hidden" id="edit-method-id" value="${method.id}">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Método *</label>
                <input type="text" id="edit-method-name" value="${method.nombre}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Método *</label>
                <select id="edit-method-type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
                    <option value="digital" ${method.tipo === 'digital' ? 'selected' : ''}>Pago Digital</option>
                    <option value="card" ${method.tipo === 'card' ? 'selected' : ''}>Tarjeta de Crédito/Débito</option>
                    <option value="cash" ${method.tipo === 'cash' ? 'selected' : ''}>Efectivo</option>
                    <option value="transfer" ${method.tipo === 'transfer' ? 'selected' : ''}>Transferencia Bancaria</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea id="edit-method-description" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" rows="3">${method.descripcion || ''}</textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Icono</label>
                <select id="edit-method-icon" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                    <option value="fab fa-google-wallet" ${method.icono === 'fab fa-google-wallet' ? 'selected' : ''}>Yape/Plin</option>
                    <option value="fas fa-mobile-alt" ${method.icono === 'fas fa-mobile-alt' ? 'selected' : ''}>Móvil</option>
                    <option value="far fa-credit-card" ${method.icono === 'far fa-credit-card' ? 'selected' : ''}>Tarjeta</option>
                    <option value="fas fa-university" ${method.icono === 'fas fa-university' ? 'selected' : ''}>Banco</option>
                    <option value="fas fa-money-bill-wave" ${method.icono === 'fas fa-money-bill-wave' ? 'selected' : ''}>Efectivo</option>
                    <option value="fas fa-wallet" ${method.icono === 'fas fa-wallet' ? 'selected' : ''}>Billetera</option>
                </select>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" id="edit-method-active" ${method.activo == 1 ? 'checked' : ''} class="rounded border-gray-300 text-pink-600 focus:ring-pink-500">
                <label for="edit-method-active" class="ml-2 text-sm text-gray-700">Método activo</label>
            </div>
            
            <div class="flex gap-2 pt-4">
                <button type="submit" class="flex-1 bg-pink-500 hover:bg-pink-600 text-white py-2 px-3 rounded-lg transition-colors flex items-center justify-center gap-1">
                    <i class="fas fa-save"></i> Guardar
                </button>
                <button type="button" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-3 rounded-lg transition-colors flex items-center justify-center gap-1" onclick="closeModal()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </div>
        </form>
    `;

        document.getElementById('modalContent').innerHTML = modalContent;
        document.getElementById('methodModal').classList.add('active');

        // Configurar el envío del formulario
        document.getElementById('editMethodForm').addEventListener('submit', function(e) {
            e.preventDefault();
            saveMethodChanges(methodId);
        });
    }

    async function saveMethodChanges(methodId) {
        console.log('💾 Guardando cambios para método:', methodId);

        const submitBtn = document.querySelector('#editMethodForm button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        try {
            // Mostrar loading
            submitBtn.innerHTML = '<div class="loading"></div> Guardando...';
            submitBtn.disabled = true;

            const methodData = {
                id: methodId,
                nombre: document.getElementById('edit-method-name').value.trim(),
                tipo: document.getElementById('edit-method-type').value,
                descripcion: document.getElementById('edit-method-description').value.trim(),
                icono: document.getElementById('edit-method-icon').value,
                activo: document.getElementById('edit-method-active').checked ? 1 : 0
            };

            console.log('📤 Datos a actualizar:', methodData);

            // Validaciones
            if (!methodData.nombre) {
                showNotification('El nombre del método es requerido', 'error');
                return;
            }

            const result = await postAPI('api-actualizar-metodo-pago', methodData);
            console.log('📥 Respuesta del servidor:', result);

            if (result.success) {
                // Actualizar localmente
                const methodIndex = paymentMethods.findIndex(m => m.id === methodId);
                if (methodIndex !== -1) {
                    paymentMethods[methodIndex] = {
                        ...paymentMethods[methodIndex],
                        ...methodData
                    };
                }

                closeModal();
                loadMethodsGrid();
                loadStatsCards();
                showNotification('Método actualizado correctamente', 'success');
            } else {
                showNotification(result.error || 'Error al actualizar el método', 'error');
            }
        } catch (error) {
            console.error('❌ Error:', error);
            showNotification('Error de conexión: ' + error.message, 'error');
        } finally {
            // Restaurar botón
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }

    // Funciones auxiliares
    function getTypeName(type) {
        const types = {
            'digital': 'Digital',
            'card': 'Tarjeta',
            'cash': 'Efectivo',
            'transfer': 'Transferencia'
        };
        return types[type] || 'Otro';
    }

    function getChartColorByType(type) {
        const colors = {
            'digital': '#3b82f6',
            'card': '#10b981',
            'cash': '#f59e0b',
            'transfer': '#8b5cf6'
        };
        return colors[type] || '#6b7280';
    }

    function showNotification(message, type) {
        const notification = document.getElementById('notification');
        const notificationText = document.getElementById('notification-text');

        notificationText.textContent = message;
        notification.className = `notification ${type} show`;

        setTimeout(() => {
            notification.classList.remove('show');
        }, 3000);
    }

    function showNewMethodModal() {
        document.getElementById('newMethodModal').classList.add('active');
    }

    function closeNewMethodModal() {
        document.getElementById('newMethodModal').classList.remove('active');
        document.getElementById('newMethodForm').reset();
    }

    function closeModal() {
        document.getElementById('methodModal').classList.remove('active');
    }

    // Funciones que necesitan ser globales para los onclick
    window.showNewMethodModal = showNewMethodModal;
    window.editMethod = editMethod;
    window.closeModal = closeModal;

    // Función para crear nuevo método
    async function createNewMethod(e) {
        e.preventDefault();
        console.log('➕ Creando nuevo método...');

        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        try {
            // Mostrar loading
            submitBtn.innerHTML = '<div class="loading"></div> Creando...';
            submitBtn.disabled = true;

            const methodData = {
                nombre: document.getElementById('method-name').value.trim(),
                tipo: document.getElementById('method-type').value,
                descripcion: document.getElementById('method-description').value.trim(),
                icono: document.getElementById('method-icon').value,
                activo: document.getElementById('method-active').checked ? 1 : 0
            };

            console.log('📤 Datos a enviar:', methodData);

            // Validaciones
            if (!methodData.nombre) {
                showNotification('El nombre del método es requerido', 'error');
                return;
            }

            if (!methodData.tipo) {
                showNotification('El tipo de método es requerido', 'error');
                return;
            }

            const result = await postAPI('api-crear-metodo-pago', methodData);
            console.log('📥 Respuesta del servidor:', result);

            if (result.success) {
                showNotification('Método creado correctamente', 'success');
                closeNewMethodModal();

                // Recargar todos los datos
                await loadDataFromDatabase();
                loadStatsCards();
                loadMethodsGrid();
                loadSettings();

            } else {
                showNotification(result.error || 'Error al crear el método', 'error');
            }
        } catch (error) {
            console.error('❌ Error:', error);
            showNotification('Error de conexión: ' + error.message, 'error');
        } finally {
            // Restaurar botón
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }

    // Función para eliminar método
    async function deleteMethod(methodId) {
        if (!confirm('¿Estás seguro de que quieres eliminar este método de pago?')) {
            return;
        }

        try {
            const methodData = {
                id: methodId
            };

            const result = await postAPI('api-eliminar-metodo-pago', methodData);

            if (result.success) {
                showNotification('Método eliminado correctamente', 'success');

                // Recargar datos
                await loadDataFromDatabase();
                loadStatsCards();
                loadMethodsGrid();
                loadSettings();
            } else {
                showNotification(result.error || 'Error al eliminar el método', 'error');
            }
        } catch (error) {
            console.error('❌ Error:', error);
            showNotification('Error de conexión: ' + error.message, 'error');
        }
    }

    // Función para filtrar métodos
    function filterMethods() {
        console.log('🔍 Filtrando métodos...');

        const typeFilter = document.getElementById('filter-type').value;
        const statusFilter = document.getElementById('filter-status').value;
        const searchTerm = document.getElementById('search-methods').value.toLowerCase();

        const filteredMethods = paymentMethods.filter(method => {
            const typeMatch = typeFilter === 'all' || method.tipo === typeFilter;
            const statusMatch = statusFilter === 'all' ||
                (statusFilter === 'active' && method.activo == 1) ||
                (statusFilter === 'inactive' && method.activo == 0);
            const searchMatch = method.nombre.toLowerCase().includes(searchTerm) ||
                (method.descripcion && method.descripcion.toLowerCase().includes(searchTerm));

            return typeMatch && statusMatch && searchMatch;
        });

        // Calcular total de pedidos para porcentajes (usando todos los métodos, no solo los filtrados)
        const totalOrders = paymentMethods.reduce((sum, method) => {
            return sum + (parseInt(method.pedidos_mes) || 0);
        }, 0);

        // Actualizar la cuadrícula con métodos filtrados
        const methodsGrid = document.getElementById('methods-grid');
        let methodsHTML = '';

        if (filteredMethods.length === 0) {
            methodsHTML = `
                <div class="col-span-full text-center py-8">
                    <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No se encontraron métodos</h3>
                    <p class="text-gray-500">Intenta con otros criterios de búsqueda</p>
                </div>
            `;
        } else {
            filteredMethods.forEach((method, index) => {
                const orders = parseInt(method.pedidos_mes) || 0;
                const percentage = totalOrders > 0 ? Math.round((orders / totalOrders) * 100) : 0;
                const typeClass = `type-${method.tipo}`;
                const iconClass = `icon-${method.tipo}`;
                const isActive = method.activo == 1;

                methodsHTML += `
                    <div class="method-card slide-in" style="animation-delay: ${index * 0.1}s">
                        <div class="method-header">
                            <div class="flex items-center gap-3">
                                <div class="method-icon ${iconClass}">
                                    <i class="${method.icono || 'fas fa-credit-card'}"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-800">${method.nombre}</h3>
                                    <span class="type-badge ${typeClass}">${getTypeName(method.tipo)}</span>
                                </div>
                            </div>
                            <span class="status-badge ${isActive ? 'status-active' : 'status-inactive'}">
                                <i class="fas fa-circle text-xs"></i> ${isActive ? 'Activo' : 'Inactivo'}
                            </span>
                        </div>
                        <div class="p-5">
                            <div class="flex justify-between text-sm mb-4">
                                <span class="text-gray-600">Pedidos este mes:</span>
                                <span class="font-bold text-gray-800">${orders}</span>
                            </div>
                            <div class="flex justify-between text-sm mb-4">
                                <span class="text-gray-600">Total procesado:</span>
                                <span class="font-bold text-gray-800">S/ ${(parseFloat(method.total_mes) || 0).toLocaleString('es-PE', {minimumFractionDigits: 2})}</span>
                            </div>
                            <div class="mb-4">
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="text-gray-600">Porcentaje de uso:</span>
                                    <span class="font-bold text-gray-800">${percentage}%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: ${percentage}%; background-color: ${getChartColorByType(method.tipo)}"></div>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-3 rounded-lg transition-colors text-sm flex items-center justify-center gap-1 view-method-btn" data-id="${method.id}">
                                    <i class="fas fa-eye"></i> Ver
                                </button>
                                <button class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-3 rounded-lg transition-colors text-sm flex items-center justify-center gap-1 edit-method-btn" data-id="${method.id}">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
        }

        methodsGrid.innerHTML = methodsHTML;

        // Re-configurar event listeners después del filtrado
        setTimeout(() => {
            document.querySelectorAll('.view-method-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const methodId = parseInt(this.getAttribute('data-id'));
                    viewMethod(methodId);
                });
            });

            document.querySelectorAll('.edit-method-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const methodId = parseInt(this.getAttribute('data-id'));
                    editMethod(methodId);
                });
            });
        }, 50);
    }

    // Función para cargar configuración
    function loadSettings() {
        console.log('⚙️ Cargando configuración en la interfaz...');

        // Cargar configuración general
        const multiMethods = document.getElementById('multi-methods');
        const paymentNotifications = document.getElementById('payment-notifications');
        const autoConfirm = document.getElementById('auto-confirm');

        if (multiMethods) {
            multiMethods.checked = settings.multiples_metodos == 1;
        }
        if (paymentNotifications) {
            paymentNotifications.checked = settings.notificaciones_pago == 1;
        }
        if (autoConfirm) {
            autoConfirm.checked = settings.confirmacion_automatica == 1;
        }

        // Cargar método predeterminado
        const defaultMethodSelect = document.getElementById('default-method');
        if (defaultMethodSelect) {
            defaultMethodSelect.innerHTML = '<option value="">Selecciona un método</option>';

            paymentMethods.forEach(method => {
                const option = document.createElement('option');
                option.value = method.id;
                option.textContent = method.nombre;
                option.selected = method.id == settings.metodo_predeterminado_id;
                defaultMethodSelect.appendChild(option);
            });
        }

        // Cargar orden de métodos
        const methodOrderContainer = document.getElementById('method-order');
        if (methodOrderContainer) {
            methodOrderContainer.innerHTML = '';

            let orderArray = [1, 2, 3, 4, 5]; // Orden por defecto

            if (settings.orden_metodos) {
                try {
                    orderArray = JSON.parse(settings.orden_metodos);
                } catch (e) {
                    console.error('Error parsing order:', e);
                }
            }

            orderArray.forEach(methodId => {
                const method = paymentMethods.find(m => m.id == methodId);
                if (method) {
                    const methodElement = document.createElement('div');
                    methodElement.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg mb-2';
                    methodElement.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="method-icon icon-${method.tipo}">
                                <i class="${method.icono || 'fas fa-credit-card'}"></i>
                            </div>
                            <span class="font-medium">${method.nombre}</span>
                        </div>
                        <div class="flex gap-1">
                            <button class="p-2 text-gray-500 hover:text-pink-500 move-up ${orderArray.indexOf(methodId) === 0 ? 'opacity-50 cursor-not-allowed' : ''}" 
                                    data-id="${method.id}" 
                                    ${orderArray.indexOf(methodId) === 0 ? 'disabled' : ''}>
                                <i class="fas fa-arrow-up"></i>
                            </button>
                            <button class="p-2 text-gray-500 hover:text-pink-500 move-down ${orderArray.indexOf(methodId) === orderArray.length - 1 ? 'opacity-50 cursor-not-allowed' : ''}" 
                                    data-id="${method.id}" 
                                    ${orderArray.indexOf(methodId) === orderArray.length - 1 ? 'disabled' : ''}>
                                <i class="fas fa-arrow-down"></i>
                            </button>
                        </div>
                    `;
                    methodOrderContainer.appendChild(methodElement);
                }
            });

            // Agregar event listeners para mover métodos
            document.querySelectorAll('.move-up').forEach(button => {
                button.addEventListener('click', function() {
                    const methodId = parseInt(this.getAttribute('data-id'));
                    moveMethodUp(methodId);
                });
            });

            document.querySelectorAll('.move-down').forEach(button => {
                button.addEventListener('click', function() {
                    const methodId = parseInt(this.getAttribute('data-id'));
                    moveMethodDown(methodId);
                });
            });
        }
    }

    function moveMethodUp(methodId) {
        let orderArray = [1, 2, 3, 4, 5];
        if (settings.orden_metodos) {
            try {
                orderArray = JSON.parse(settings.orden_metodos);
            } catch (e) {
                console.error('Error parsing order:', e);
            }
        }

        const index = orderArray.indexOf(methodId);
        if (index > 0) {
            // Intercambiar con el elemento anterior
            [orderArray[index], orderArray[index - 1]] = [orderArray[index - 1], orderArray[index]];
            settings.orden_metodos = JSON.stringify(orderArray);
            loadSettings();
        }
    }

    function moveMethodDown(methodId) {
        let orderArray = [1, 2, 3, 4, 5];
        if (settings.orden_metodos) {
            try {
                orderArray = JSON.parse(settings.orden_metodos);
            } catch (e) {
                console.error('Error parsing order:', e);
            }
        }

        const index = orderArray.indexOf(methodId);
        if (index < orderArray.length - 1) {
            // Intercambiar con el elemento siguiente
            [orderArray[index], orderArray[index + 1]] = [orderArray[index + 1], orderArray[index]];
            settings.orden_metodos = JSON.stringify(orderArray);
            loadSettings();
        }
    }

    // Función para guardar configuración
    async function saveSettings() {
        console.log('💾 Guardando configuración...');

        const saveBtn = document.getElementById('save-settings');
        const originalText = saveBtn.innerHTML;

        try {
            // Mostrar loading
            saveBtn.innerHTML = '<div class="loading"></div> Guardando...';
            saveBtn.disabled = true;

            // Obtener orden actual
            let orderArray = [1, 2, 3, 4, 5];
            if (settings.orden_metodos) {
                try {
                    orderArray = JSON.parse(settings.orden_metodos);
                } catch (e) {
                    console.error('Error parsing order:', e);
                }
            }

            const settingsData = {
                multiples_metodos: document.getElementById('multi-methods').checked ? 1 : 0,
                notificaciones_pago: document.getElementById('payment-notifications').checked ? 1 : 0,
                confirmacion_automatica: document.getElementById('auto-confirm').checked ? 1 : 0,
                metodo_predeterminado_id: parseInt(document.getElementById('default-method').value) || 1,
                orden_metodos: orderArray
            };

            console.log('📤 Configuración a guardar:', settingsData);

            const result = await postAPI('api-actualizar-configuracion-metodos', settingsData);
            console.log('📥 Respuesta del servidor:', result);

            if (result.success) {
                settings = {
                    ...settings,
                    ...settingsData
                };
                showNotification('Configuración guardada correctamente', 'success');
            } else {
                showNotification(result.error || 'Error al guardar la configuración', 'error');
            }
        } catch (error) {
            console.error('❌ Error:', error);
            showNotification('Error de conexión: ' + error.message, 'error');
        } finally {
            // Restaurar botón
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
        }
    }

    function initCharts() {
        // Destruir gráficos existentes
        if (distributionChart) {
            distributionChart.destroy();
        }
        if (trendsChart) {
            trendsChart.destroy();
        }

        // Gráfico de distribución
        const distributionCtx = document.getElementById('distributionChart').getContext('2d');
        distributionChart = new Chart(distributionCtx, {
            type: 'doughnut',
            data: {
                labels: paymentMethods.map(m => m.nombre),
                datasets: [{
                    data: paymentMethods.map(m => parseInt(m.pedidos_mes) || 0),
                    backgroundColor: paymentMethods.map(m => getChartColorByType(m.tipo)),
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = paymentMethods.reduce((sum, m) => sum + (parseInt(m.pedidos_mes) || 0), 0);
                                const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                                return `${context.label}: ${context.raw} pedidos (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de tendencias (datos de ejemplo)
        const trendsCtx = document.getElementById('trendsChart').getContext('2d');
        trendsChart = new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: ['Nov', 'Dic', 'Ene'],
                datasets: paymentMethods.slice(0, 3).map((method, index) => {
                    const baseOrders = parseInt(method.pedidos_mes) || 0;
                    return {
                        label: method.nombre,
                        data: [
                            Math.max(0, baseOrders - 6 + index * 2),
                            Math.max(0, baseOrders - 3 + index),
                            baseOrders
                        ],
                        borderColor: getChartColorByType(method.tipo),
                        backgroundColor: `${getChartColorByType(method.tipo)}20`,
                        tension: 0.3,
                        fill: true
                    };
                })
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Número de Pedidos'
                        }
                    }
                }
            }
        });

        // Actualizar tabla de estadísticas
        updateStatsTable();
    }

    function updateStatsTable() {
        const totalOrders = paymentMethods.reduce((sum, m) => sum + (parseInt(m.pedidos_mes) || 0), 0);
        const totalAmount = paymentMethods.reduce((sum, m) => sum + (parseFloat(m.total_mes) || 0), 0);

        let tableHTML = '';

        paymentMethods.forEach(method => {
            const orders = parseInt(method.pedidos_mes) || 0;
            const total = parseFloat(method.total_mes) || 0;
            const percentage = totalOrders > 0 ? ((orders / totalOrders) * 100).toFixed(1) : 0;
            const average = orders > 0 ? (total / orders) : 0;
            const trend = Math.random() > 0.5 ? 'up' : 'down';
            const trendValue = (Math.random() * 15).toFixed(1);

            tableHTML += `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-${getColorByType(method.tipo)}-100 flex items-center justify-center mr-3">
                                <i class="${method.icono || 'fas fa-credit-card'} text-${getColorByType(method.tipo)}-500"></i>
                            </div>
                            <span class="font-medium text-gray-900">${method.nombre}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${orders}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">S/ ${total.toLocaleString('es-PE', {minimumFractionDigits: 2})}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">S/ ${average.toLocaleString('es-PE', {minimumFractionDigits: 2})}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${percentage}%</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center ${trend === 'up' ? 'text-green-500' : 'text-red-500'}">
                            <i class="fas fa-arrow-${trend === 'up' ? 'up' : 'down'} mr-1"></i>
                            <span class="text-sm">${trendValue}%</span>
                        </div>
                    </td>
                </tr>
            `;
        });

        document.getElementById('stats-table').innerHTML = tableHTML;
    }

    // Funciones auxiliares adicionales
    function getColorByType(type) {
        const colors = {
            'digital': 'blue',
            'card': 'green',
            'cash': 'yellow',
            'transfer': 'purple'
        };
        return colors[type] || 'gray';
    }

    // Hacer funciones globales
    window.deleteMethod = deleteMethod;
    window.filterMethods = filterMethods;
    // Funciones restantes (filterMethods, createNewMethod, etc.) se mantienen igual...
    // [Aquí van las demás funciones que ya tenías]
</script>