<?php
// Verificar si hay mensajes de éxito/error
$mensaje_exito = $_SESSION['mensaje_exito'] ?? '';
$mensaje_error = $_SESSION['mensaje_error'] ?? '';

// Limpiar mensajes después de mostrarlos
unset($_SESSION['mensaje_exito'], $_SESSION['mensaje_error']);
?>

<style>
    :root {
        --primary: #f472b6;
        --secondary: #a78bfa;
    }

    .sidebar {
        background: linear-gradient(180deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
    }

    .active-menu {
        background: rgba(255, 255, 255, 0.2);
        border-right: 4px solid white;
    }

    .notification-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #ef4444;
    }

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

    .content-section {
        display: none;
    }

    .content-section.active {
        display: block;
    }

    .tab-button {
        transition: all 0.3s ease;
    }

    .tab-button.active {
        background-color: #f472b6;
        color: white;
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
        border-radius: 8px;
        width: 90%;
        max-width: 800px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    .fade-in {
        animation: fadeIn 0.3s ease;
    }

    .search-highlight {
        background-color: #fffacd;
        padding: 2px 4px;
        border-radius: 4px;
    }

    .promocion-card {
        transition: all 0.3s ease;
    }

    .promocion-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
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

    .tipo-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .tipo-descuento {
        background-color: #e0f2fe;
        color: #0369a1;
    }

    .tipo-combo {
        background-color: #f0fdf4;
        color: #166534;
    }

    .tipo-envio {
        background-color: #fef7cd;
        color: #854d0e;
    }
</style>
<main class="flex-1 overflow-y-auto p-6">
    <!-- Mensajes de éxito/error -->
    <?php if ($mensaje_exito): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 fade-in" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span class="block sm:inline"><?php echo htmlspecialchars($mensaje_exito); ?></span>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($mensaje_error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 fade-in" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span class="block sm:inline"><?php echo htmlspecialchars($mensaje_error); ?></span>
            </div>
        </div>
    <?php endif; ?>

    <!-- Sección de Gestión de Promociones -->
    <div class="content-section active" id="promociones">
        <!-- Tabs de navegación -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="flex border-b overflow-x-auto">
                <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-600 hover:bg-gray-50 active" data-tab="promociones-activas">
                    <i class="fas fa-bolt mr-2"></i>Promociones Activas
                    <span class="ml-2 bg-pink-500 text-white text-xs rounded-full px-2 py-1" id="total-promociones-badge">0</span>
                </button>
                <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-600 hover:bg-gray-50" data-tab="nueva-promocion">
                    <i class="fas fa-plus mr-2"></i>Nueva Promoción
                </button>
                <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-600 hover:bg-gray-50" data-tab="productos-promocion">
                    <i class="fas fa-gift mr-2"></i>Productos en Promoción
                    <span class="ml-2 bg-green-500 text-white text-xs rounded-full px-2 py-1" id="total-productos-promocion-badge">0</span>
                </button>
                <!-- En la sección de tabs, agrega este nuevo botón -->
                <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-600 hover:bg-gray-50" data-tab="papelera-promociones">
                    <i class="fas fa-trash mr-2"></i>Papelera
                    <span class="ml-2 bg-gray-500 text-white text-xs rounded-full px-2 py-1" id="total-papelera-badge">0</span>
                </button>
            </div>
        </div>

        <!-- Contenido de las pestañas -->
        <div id="tab-content">
            <!-- Pestaña: Promociones Activas -->
            <div class="tab-panel active" id="promociones-activas-panel">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-4 border-b flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold">Promociones Activas</h2>
                            <p class="text-gray-600"><span id="total-promociones-text">0</span> promociones activas en el sistema</p>
                        </div>
                        <div class="flex space-x-2">
                            <div class="relative">
                                <input type="text" id="buscador-promociones" placeholder="Buscar promoción..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 w-64">
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            </div>
                            <select id="filtro-tipo-promocion" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                                <option value="todos">Todos los tipos</option>
                                <option value="descuento_porcentaje">Descuento %</option>
                                <option value="descuento_monto">Descuento monto</option>
                                <option value="combo">Combo</option>
                                <option value="envio_gratis">Envío gratis</option>
                            </select>
                            <button class="bg-pink-500 hover:bg-pink-600 text-white font-medium py-2 px-4 rounded-lg transition-colors" onclick="exportarPromociones()">
                                <i class="fas fa-download mr-2"></i>Exportar
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Promoción</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descuento</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vigencia</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usos</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="tabla-promociones">
                                <!-- Las promociones se cargarán aquí dinámicamente -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Mensaje cuando no hay resultados -->
                    <div id="sin-resultados-promociones" class="hidden p-8 text-center">
                        <i class="fas fa-search text-gray-300 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-700">No se encontraron promociones</h3>
                        <p class="text-gray-500 mt-2">Intenta con otros términos de búsqueda</p>
                    </div>
                </div>
            </div>

            <!-- Pestaña: Nueva Promoción -->
            <div class="tab-panel" id="nueva-promocion-panel">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold mb-6">Crear Nueva Promoción</h2>

                    <form id="form-nueva-promocion" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Información Básica -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-pink-600 mb-3">Información Básica</h3>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Promoción *</label>
                                <input type="text" id="promocion-nombre" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" placeholder="Ej: Descuento 20% Fragancias" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                                <textarea id="promocion-descripcion" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" rows="3" placeholder="Descripción detallada de la promoción..."></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Promoción *</label>
                                <select id="promocion-tipo" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
                                    <option value="">Selecciona un tipo</option>
                                    <option value="descuento_porcentaje">Descuento Porcentaje</option>
                                    <option value="descuento_monto">Descuento Monto Fijo</option>
                                    <option value="combo">Combo</option>
                                    <option value="envio_gratis">Envío Gratis</option>
                                </select>
                            </div>

                            <div id="campo-valor-descuento" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-1" id="etiqueta-valor-descuento">Valor de Descuento *</label>
                                <div class="flex">
                                    <input type="number" id="promocion-valor-descuento" min="0" step="0.01" class="w-full border border-gray-300 rounded-l-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" placeholder="0.00">
                                    <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r-md px-3 py-2 text-gray-600" id="simbolo-descuento">%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Configuración de Vigencia y Límites -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-pink-600 mb-3">Configuración de Vigencia</h3>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inicio *</label>
                                    <input type="date" id="promocion-fecha-inicio" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Fin *</label>
                                    <input type="date" id="promocion-fecha-fin" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Límite de Usos</label>
                                <input type="number" id="promocion-max-usos" min="0" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" placeholder="Dejar vacío para ilimitado">
                                <p class="text-xs text-gray-500 mt-1">Número máximo de veces que se puede aplicar esta promoción</p>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" id="promocion-activa" class="rounded border-gray-300 text-pink-600 focus:ring-pink-500" checked>
                                <label for="promocion-activa" class="ml-2 text-sm text-gray-700">Promoción activa</label>
                            </div>

                            <div id="seleccion-productos" class="hidden">
                                <h3 class="text-lg font-semibold text-pink-600 mb-3">Productos en Promoción</h3>
                                <div class="border border-gray-300 rounded-md p-4 max-h-60 overflow-y-auto">
                                    <div id="lista-productos-promocion">
                                        <!-- Los productos se cargarán aquí dinámicamente -->
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Selecciona los productos que aplican para esta promoción</p>
                            </div>
                        </div>
                    </form>

                    <!-- Botones de acción -->
                    <div class="mt-8 flex justify-end space-x-4">
                        <button type="button" onclick="limpiarFormularioPromocion()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-6 rounded-lg transition-colors">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </button>
                        <button type="button" onclick="guardarNuevaPromocion()" class="bg-pink-500 hover:bg-pink-600 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                            <i class="fas fa-save mr-2"></i>Crear Promoción
                        </button>
                    </div>
                </div>
            </div>

            <!-- Pestaña: Productos en Promoción -->
            <div class="tab-panel" id="productos-promocion-panel">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-4 border-b flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold">Productos en Promoción</h2>
                            <p class="text-gray-600"><span id="total-productos-promocion-text">0</span> productos incluidos en promociones activas</p>
                        </div>
                        <div class="flex space-x-2">
                            <div class="relative">
                                <input type="text" id="buscador-productos-promocion" placeholder="Buscar producto..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 w-64">
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            </div>
                            <select id="filtro-categoria-producto" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                                <option value="todos">Todas las categorías</option>
                                <!-- Las categorías se cargarán dinámicamente -->
                            </select>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="grid-productos-promocion">
                            <!-- Los productos en promoción se cargarán aquí dinámicamente -->
                        </div>

                        <!-- Mensaje cuando no hay resultados -->
                        <div id="sin-resultados-productos-promocion" class="hidden text-center py-12">
                            <i class="fas fa-gift text-gray-300 text-5xl mb-4"></i>
                            <h3 class="text-xl font-medium text-gray-700">No se encontraron productos en promoción</h3>
                            <p class="text-gray-500 mt-2">Los productos aparecerán aquí cuando estén incluidos en promociones activas</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pestaña: Papelera -->
            <div class="tab-panel" id="papelera-promociones-panel">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-4 border-b flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold">Papelera de Promociones</h2>
                            <p class="text-gray-600"><span id="total-papelera-text">0</span> promociones en la papelera</p>
                        </div>
                        <div class="flex space-x-2">
                            <button class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg transition-colors" onclick="cargarPapelera()">
                                <i class="fas fa-sync-alt mr-2"></i>Actualizar
                            </button>
                            <button class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg transition-colors" onclick="vaciarPapelera()">
                                <i class="fas fa-broom mr-2"></i>Vaciar Papelera
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Promoción</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descuento</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Eliminada el</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usos</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="tabla-papelera">
                                <!-- Las promociones eliminadas se cargarán aquí dinámicamente -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Mensaje cuando no hay resultados -->
                    <div id="sin-resultados-papelera" class="hidden p-8 text-center">
                        <i class="fas fa-trash text-gray-300 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-700">La papelera está vacía</h3>
                        <p class="text-gray-500 mt-2">Las promociones eliminadas aparecerán aquí</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
</div>

<!-- Modal para editar promoción -->
<div class="modal" id="modal-editar-promocion">
    <div class="modal-content">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Editar Promoción</h3>
            <button class="text-gray-500 hover:text-gray-700" id="cerrar-modal-promocion">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-4" id="contenido-modal-promocion">
            <!-- El formulario de edición se cargará aquí -->
        </div>
    </div>
</div>

<!-- Modal para ver detalles de la promoción -->
<div class="modal" id="modal-detalles-promocion">
    <div class="modal-content" style="max-width: 700px;">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Detalles de la Promoción</h3>
            <button class="text-gray-500 hover:text-gray-700" id="cerrar-modal-detalles-promocion">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-4" id="contenido-detalles-promocion">
            <!-- Los detalles de la promoción se cargarán aquí -->
        </div>
    </div>
</div>
<script>
    // URLs de la API
    const API_BASE = 'index.php?view=';

    // Variables globales
    let promociones = [];
    let productos = [];
    let categorias = [];

    // Función para inicializar la aplicación
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

        // Tabs de Gestión de Promociones
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
                document.getElementById(tabId + '-panel').classList.add('active');
            });
        });

        // Toggle menu móvil
        document.getElementById('menuToggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('hidden');
        });
    }

    // Configurar event listeners
    function configurarEventListeners() {
        // Buscador de promociones
        document.getElementById('buscador-promociones')?.addEventListener('input', filtrarPromociones);

        // Filtro de tipo de promoción
        document.getElementById('filtro-tipo-promocion')?.addEventListener('change', filtrarPromociones);

        // Buscador de productos en promoción
        document.getElementById('buscador-productos-promocion')?.addEventListener('input', cargarProductosPromocion);

        // Filtro de categoría de productos
        document.getElementById('filtro-categoria-producto')?.addEventListener('change', cargarProductosPromocion);

        // Cambio de tipo de promoción
        document.getElementById('promocion-tipo')?.addEventListener('change', manejarCambioTipoPromocion);

        // Cerrar modales
        document.getElementById('cerrar-modal-promocion')?.addEventListener('click', function() {
            document.getElementById('modal-editar-promocion').classList.remove('active');
        });

        document.getElementById('cerrar-modal-detalles-promocion')?.addEventListener('click', function() {
            document.getElementById('modal-detalles-promocion').classList.remove('active');
        });
        
        document.querySelector('[data-tab="papelera-promociones"]')?.addEventListener('click', function() {
            // Pequeño delay para asegurar que la pestaña esté visible
            setTimeout(() => {
                cargarPapelera();
            }, 100);
        });

        // Validación de fechas
        document.getElementById('promocion-fecha-inicio')?.addEventListener('change', function() {
            const fechaFin = document.getElementById('promocion-fecha-fin');
            if (fechaFin.value && this.value > fechaFin.value) {
                fechaFin.value = this.value;
            }
        });
    }

    // Función para cargar promociones
    async function cargarPromociones() {
        try {
            const response = await fetch(`${API_BASE}api-listar-promociones`);
            const data = await response.json();

            if (data.success) {
                promociones = data.data;
                actualizarListaPromociones(promociones);
                actualizarEstadisticasPromociones();
            } else {
                mostrarNotificacion('Error al cargar promociones', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarNotificacion('Error de conexión', 'error');
        }
    }

    // Función para cargar productos
    async function cargarProductos() {
        try {
            const response = await fetch(`${API_BASE}api-listar-productos-promocion`);
            const data = await response.json();

            if (data.success) {
                productos = data.data;
                cargarProductosParaPromocion();
            } else {
                mostrarNotificacion('Error al cargar productos', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarNotificacion('Error de conexión', 'error');
        }
    }

    // Función para cargar categorías
    async function cargarCategorias() {
        try {
            const response = await fetch(`${API_BASE}api-listar-categorias`);
            const data = await response.json();

            if (data.success) {
                categorias = data.data;
                actualizarFiltroCategorias();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    // Función para cargar productos en promoción
    async function cargarProductosPromocion() {
        try {
            const searchTerm = document.getElementById('buscador-productos-promocion')?.value.toLowerCase() || '';
            const categoriaFiltro = document.getElementById('filtro-categoria-producto')?.value || 'todos';

            const response = await fetch(`${API_BASE}api-productos-promocion`);
            const data = await response.json();

            if (data.success) {
                let productosFiltrados = data.data;

                // Aplicar filtros locales
                if (searchTerm) {
                    productosFiltrados = productosFiltrados.filter(producto =>
                        producto.nombre.toLowerCase().includes(searchTerm)
                    );
                }

                if (categoriaFiltro !== 'todos') {
                    productosFiltrados = productosFiltrados.filter(producto =>
                        producto.categoria_id == categoriaFiltro
                    );
                }

                actualizarGridProductosPromocion(productosFiltrados);
            } else {
                mostrarNotificacion('Error al cargar productos en promoción', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarNotificacion('Error de conexión', 'error');
        }
    }

    // Función para cargar productos para selección en promoción
    function cargarProductosParaPromocion() {
        const lista = document.getElementById('lista-productos-promocion');
        if (!lista) return;

        let html = '';
        productos.forEach(producto => {
            const categoria = categorias.find(c => c.id == producto.categoria_id);
            html += `
            <div class="flex items-center mb-2">
                <input type="checkbox" id="producto-${producto.id}" value="${producto.id}" class="rounded border-gray-300 text-pink-600 focus:ring-pink-500">
                <label for="producto-${producto.id}" class="ml-2 text-sm text-gray-700">
                    ${producto.nombre} - S/ ${producto.precio} 
                    <span class="text-xs text-gray-500">(${categoria?.nombre || 'Sin categoría'})</span>
                </label>
            </div>
            `;
        });

        lista.innerHTML = html;
    }

    // Función para actualizar filtro de categorías
    function actualizarFiltroCategorias() {
        const filtro = document.getElementById('filtro-categoria-producto');
        if (!filtro) return;

        let html = '<option value="todos">Todas las categorías</option>';
        categorias.forEach(categoria => {
            html += `<option value="${categoria.id}">${categoria.nombre}</option>`;
        });

        filtro.innerHTML = html;
    }

    // Función para manejar cambio de tipo de promoción
    function manejarCambioTipoPromocion() {
        const tipo = document.getElementById('promocion-tipo').value;
        const campoValorDescuento = document.getElementById('campo-valor-descuento');
        const etiquetaValorDescuento = document.getElementById('etiqueta-valor-descuento');
        const simboloDescuento = document.getElementById('simbolo-descuento');
        const seleccionProductos = document.getElementById('seleccion-productos');

        // Mostrar/ocultar campo de valor de descuento
        if (tipo === 'envio_gratis') {
            campoValorDescuento.classList.add('hidden');
        } else {
            campoValorDescuento.classList.remove('hidden');

            // Configurar etiqueta y símbolo según el tipo
            if (tipo === 'descuento_monto') {
                etiquetaValorDescuento.textContent = 'Monto de Descuento *';
                simboloDescuento.textContent = 'S/';
            } else {
                etiquetaValorDescuento.textContent = 'Porcentaje de Descuento *';
                simboloDescuento.textContent = '%';
            }
        }

        // Mostrar/ocultar selección de productos
        if (tipo === 'envio_gratis') {
            seleccionProductos.classList.add('hidden');
        } else {
            seleccionProductos.classList.remove('hidden');
            cargarProductosParaPromocion();
        }
    }

    // Función para filtrar promociones
    function filtrarPromociones() {
        const searchTerm = document.getElementById('buscador-promociones').value.toLowerCase();
        const tipoFiltro = document.getElementById('filtro-tipo-promocion').value;

        const promocionesFiltradas = promociones.filter(promocion => {
            const coincideBusqueda = !searchTerm ||
                promocion.nombre.toLowerCase().includes(searchTerm) ||
                promocion.descripcion.toLowerCase().includes(searchTerm);

            const coincideTipo = tipoFiltro === 'todos' || promocion.tipo === tipoFiltro;

            return coincideBusqueda && coincideTipo;
        });

        actualizarListaPromociones(promocionesFiltradas);
    }

    // Función para actualizar la lista de promociones en la tabla
    function actualizarListaPromociones(promocionesFiltradas) {
        const tabla = document.getElementById('tabla-promociones');
        const sinResultados = document.getElementById('sin-resultados-promociones');

        if (!tabla || !sinResultados) return;

        if (promocionesFiltradas.length === 0) {
            tabla.innerHTML = '';
            sinResultados.classList.remove('hidden');
            return;
        }

        sinResultados.classList.add('hidden');

        let html = '';
        promocionesFiltradas.forEach(promocion => {
            // Determinar estado
            const hoy = new Date().toISOString().split('T')[0];
            let estado = 'activa';
            let estadoBadge = '<span class="status-badge status-activa">Activa</span>';

            if (!promocion.activa) {
                estado = 'inactiva';
                estadoBadge = '<span class="status-badge status-inactiva">Inactiva</span>';
            } else if (promocion.fecha_fin < hoy) {
                estado = 'expirada';
                estadoBadge = '<span class="status-badge status-expirada">Expirada</span>';
            }

            // Determinar tipo
            let tipoBadge = '';
            let descuentoTexto = '';

            switch (promocion.tipo) {
                case 'descuento_porcentaje':
                    tipoBadge = '<span class="tipo-badge tipo-descuento">Descuento %</span>';
                    descuentoTexto = `${promocion.valor_descuento}%`;
                    break;
                case 'descuento_monto':
                    tipoBadge = '<span class="tipo-badge tipo-descuento">Descuento Monto</span>';
                    descuentoTexto = `S/ ${promocion.valor_descuento}`;
                    break;
                case 'combo':
                    tipoBadge = '<span class="tipo-badge tipo-combo">Combo</span>';
                    descuentoTexto = `${promocion.valor_descuento}%`;
                    break;
                case 'envio_gratis':
                    tipoBadge = '<span class="tipo-badge tipo-envio">Envío Gratis</span>';
                    descuentoTexto = 'Gratis';
                    break;
            }

            // Calcular días restantes
            const fechaFin = new Date(promocion.fecha_fin);
            const hoyDate = new Date();
            const diasRestantes = Math.ceil((fechaFin - hoyDate) / (1000 * 60 * 60 * 24));
            let vigenciaTexto = `${promocion.fecha_inicio} al ${promocion.fecha_fin}`;

            if (diasRestantes > 0) {
                vigenciaTexto += ` (${diasRestantes} días restantes)`;
            } else if (diasRestantes === 0) {
                vigenciaTexto += ' (Hoy finaliza)';
            } else {
                vigenciaTexto += ' (Finalizada)';
            }

            // Usos
            let usosTexto = promocion.max_usos ?
                `${promocion.usos_actual} / ${promocion.max_usos}` :
                `${promocion.usos_actual} (sin límite)`;

            html += `
            <tr class="fade-in">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-tag text-purple-600"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">${promocion.nombre}</div>
                            <div class="text-sm text-gray-500">${promocion.descripcion}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">${tipoBadge}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${descuentoTexto}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${vigenciaTexto}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${usosTexto}</td>
                <td class="px-6 py-4 whitespace-nowrap">${estadoBadge}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button class="text-blue-600 hover:text-blue-900 mr-3" onclick="verDetallesPromocion(${promocion.id})">
                        <i class="fas fa-eye mr-1"></i>Ver
                    </button>
                    <button class="text-green-600 hover:text-green-900 mr-3" onclick="editarPromocion(${promocion.id})">
                        <i class="fas fa-edit mr-1"></i>Editar
                    </button>
                    <button class="text-red-600 hover:text-red-900" onclick="eliminarPromocion(${promocion.id})">
                        <i class="fas fa-trash mr-1"></i>Eliminar
                    </button>
                </td>
            </tr>
            `;
        });

        tabla.innerHTML = html;
    }

    // Función para actualizar grid de productos en promoción
    function actualizarGridProductosPromocion(productosFiltrados) {
        const grid = document.getElementById('grid-productos-promocion');
        const sinResultados = document.getElementById('sin-resultados-productos-promocion');

        if (!grid || !sinResultados) return;

        if (productosFiltrados.length === 0) {
            grid.innerHTML = '';
            sinResultados.classList.remove('hidden');
            return;
        }

        sinResultados.classList.add('hidden');

        let html = '';
        productosFiltrados.forEach(producto => {
            html += `
            <div class="promocion-card bg-white border border-gray-200 rounded-lg p-6 fade-in">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-pink-100 text-pink-600 mr-3">
                            <i class="fas fa-gift"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">${producto.nombre}</h3>
                            <p class="text-sm text-gray-600">${producto.categoria_nombre || 'Sin categoría'}</p>
                        </div>
                    </div>
                    <span class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded-full">S/ ${producto.precio}</span>
                </div>
                
                <div class="space-y-3 mb-4">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Stock:</span>
                        <span class="font-bold ${producto.stock <= 5 ? 'text-red-600' : 'text-gray-800'}">${producto.stock} unidades</span>
                    </div>
                </div>
                
                <div class="flex space-x-2">
                    <button class="flex-1 bg-pink-500 text-white hover:bg-pink-600 font-medium py-2 px-3 rounded transition-colors text-sm" onclick="verDetallesProducto(${producto.id})">
                        <i class="fas fa-eye mr-1"></i>Detalles
                    </button>
                </div>
            </div>
            `;
        });

        grid.innerHTML = html;

        // Actualizar contador
        document.getElementById('total-productos-promocion-badge').textContent = productosFiltrados.length;
        document.getElementById('total-productos-promocion-text').textContent = productosFiltrados.length;
    }

    // Función para guardar nueva promoción
    async function guardarNuevaPromocion() {
        // Validar formulario
        if (!validarFormularioPromocion()) {
            return;
        }

        const formData = new FormData();

        // Recopilar datos del formulario
        formData.append('nombre', document.getElementById('promocion-nombre').value);
        formData.append('descripcion', document.getElementById('promocion-descripcion').value);
        formData.append('tipo', document.getElementById('promocion-tipo').value);
        formData.append('valor_descuento', document.getElementById('promocion-valor-descuento').value || 0);
        formData.append('fecha_inicio', document.getElementById('promocion-fecha-inicio').value);
        formData.append('fecha_fin', document.getElementById('promocion-fecha-fin').value);
        formData.append('max_usos', document.getElementById('promocion-max-usos').value || '');
        formData.append('activa', document.getElementById('promocion-activa').checked ? 1 : 0);

        // Recopilar productos seleccionados
        const productosSeleccionados = [];
        document.querySelectorAll('#lista-productos-promocion input[type="checkbox"]:checked').forEach(checkbox => {
            productosSeleccionados.push(checkbox.value);
        });
        formData.append('productos', JSON.stringify(productosSeleccionados));

        try {
            const response = await fetch(`${API_BASE}api-registrar-promocion`, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                mostrarNotificacion('Promoción creada exitosamente', 'success');
                limpiarFormularioPromocion();
                await cargarPromociones();
                await cargarProductosPromocion();
                // Cambiar a la pestaña de promociones activas
                document.querySelector('[data-tab="promociones-activas"]').click();
            } else {
                mostrarNotificacion(data.message || 'Error al crear promoción', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarNotificacion('Error de conexión', 'error');
        }
    }

    // Función para validar formulario
    function validarFormularioPromocion() {
        const nombre = document.getElementById('promocion-nombre').value.trim();
        const tipo = document.getElementById('promocion-tipo').value;
        const fechaInicio = document.getElementById('promocion-fecha-inicio').value;
        const fechaFin = document.getElementById('promocion-fecha-fin').value;
        const valorDescuento = document.getElementById('promocion-valor-descuento').value;

        if (!nombre) {
            mostrarNotificacion('El nombre de la promoción es obligatorio', 'error');
            return false;
        }

        if (!tipo) {
            mostrarNotificacion('El tipo de promoción es obligatorio', 'error');
            return false;
        }

        if (!fechaInicio || !fechaFin) {
            mostrarNotificacion('Las fechas de inicio y fin son obligatorias', 'error');
            return false;
        }

        if (fechaInicio > fechaFin) {
            mostrarNotificacion('La fecha de inicio no puede ser posterior a la fecha de fin', 'error');
            return false;
        }

        if (tipo !== 'envio_gratis' && (!valorDescuento || parseFloat(valorDescuento) <= 0)) {
            mostrarNotificacion('El valor de descuento debe ser mayor a 0', 'error');
            return false;
        }

        if (tipo !== 'envio_gratis') {
            const productosSeleccionados = document.querySelectorAll('#lista-productos-promocion input[type="checkbox"]:checked').length;
            if (productosSeleccionados === 0) {
                mostrarNotificacion('Debes seleccionar al menos un producto para la promoción', 'error');
                return false;
            }
        }

        return true;
    }

    // Función para limpiar formulario
    function limpiarFormularioPromocion() {
        document.getElementById('form-nueva-promocion').reset();
        document.getElementById('promocion-activa').checked = true;
        document.getElementById('campo-valor-descuento').classList.add('hidden');
        document.getElementById('seleccion-productos').classList.add('hidden');
    }

    // Función para editar promoción
    async function editarPromocion(promocionId) {
        try {
            const response = await fetch(`${API_BASE}api-obtener-promocion&id=${promocionId}`);
            const data = await response.json();

            if (data.success) {
                const promocion = data.data;
                mostrarModalEdicion(promocion);
            } else {
                mostrarNotificacion('Error al cargar promoción', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarNotificacion('Error de conexión', 'error');
        }
    }

    // Función para mostrar modal de edición
    function mostrarModalEdicion(promocion) {
        let html = `
        <form id="form-editar-promocion" class="space-y-4">
            <input type="hidden" id="edit-promocion-id" value="${promocion.id}">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Información Básica -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-pink-600 mb-3">Información Básica</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Promoción</label>
                        <input type="text" id="edit-promocion-nombre" value="${promocion.nombre}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea id="edit-promocion-descripcion" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" rows="3">${promocion.descripcion}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Promoción</label>
                        <select id="edit-promocion-tipo" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
                            <option value="descuento_porcentaje" ${promocion.tipo === 'descuento_porcentaje' ? 'selected' : ''}>Descuento Porcentaje</option>
                            <option value="descuento_monto" ${promocion.tipo === 'descuento_monto' ? 'selected' : ''}>Descuento Monto Fijo</option>
                            <option value="combo" ${promocion.tipo === 'combo' ? 'selected' : ''}>Combo</option>
                            <option value="envio_gratis" ${promocion.tipo === 'envio_gratis' ? 'selected' : ''}>Envío Gratis</option>
                        </select>
                    </div>
                    
                    <div id="edit-campo-valor-descuento" ${promocion.tipo === 'envio_gratis' ? 'class="hidden"' : ''}>
                        <label class="block text-sm font-medium text-gray-700 mb-1" id="edit-etiqueta-valor-descuento">${promocion.tipo === 'descuento_monto' ? 'Monto de Descuento' : 'Porcentaje de Descuento'}</label>
                        <div class="flex">
                            <input type="number" id="edit-promocion-valor-descuento" value="${promocion.valor_descuento || ''}" min="0" step="0.01" class="w-full border border-gray-300 rounded-l-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                            <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r-md px-3 py-2 text-gray-600" id="edit-simbolo-descuento">${promocion.tipo === 'descuento_monto' ? 'S/' : '%'}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Configuración de Vigencia y Límites -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-pink-600 mb-3">Configuración de Vigencia</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inicio</label>
                            <input type="date" id="edit-promocion-fecha-inicio" value="${promocion.fecha_inicio}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Fin</label>
                            <input type="date" id="edit-promocion-fecha-fin" value="${promocion.fecha_fin}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Límite de Usos</label>
                        <input type="number" id="edit-promocion-max-usos" value="${promocion.max_usos || ''}" min="0" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" placeholder="Dejar vacío para ilimitado">
                        <p class="text-xs text-gray-500 mt-1">Usos actuales: ${promocion.usos_actual}</p>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="edit-promocion-activa" ${promocion.activa ? 'checked' : ''} class="rounded border-gray-300 text-pink-600 focus:ring-pink-500">
                        <label for="edit-promocion-activa" class="ml-2 text-sm text-gray-700">Promoción activa</label>
                    </div>
                    
                    <div id="edit-seleccion-productos" ${promocion.tipo === 'envio_gratis' ? 'class="hidden"' : ''}>
                        <h3 class="text-lg font-semibold text-pink-600 mb-3">Productos en Promoción</h3>
                        <div class="border border-gray-300 rounded-md p-4 max-h-60 overflow-y-auto">
                            <div id="edit-lista-productos-promocion">
                                <!-- Los productos se cargarán aquí dinámicamente -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-4 pt-4">
                <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-4 rounded transition-colors" onclick="document.getElementById('modal-editar-promocion').classList.remove('active')">
                    Cancelar
                </button>
                <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white font-medium py-2 px-4 rounded transition-colors">
                    Guardar Cambios
                </button>
            </div>
        </form>
        `;

        document.getElementById('contenido-modal-promocion').innerHTML = html;

        // Cargar productos para la edición
        if (promocion.tipo !== 'envio_gratis') {
            cargarProductosParaEdicionPromocion(promocion.productos || []);
        }

        // Configurar el evento submit del formulario
        document.getElementById('form-editar-promocion').addEventListener('submit', function(e) {
            e.preventDefault();
            guardarCambiosPromocion(promocion.id);
        });

        // Configurar cambio de tipo
        document.getElementById('edit-promocion-tipo').addEventListener('change', function() {
            const tipo = this.value;
            const campoValorDescuento = document.getElementById('edit-campo-valor-descuento');
            const etiquetaValorDescuento = document.getElementById('edit-etiqueta-valor-descuento');
            const simboloDescuento = document.getElementById('edit-simbolo-descuento');
            const seleccionProductos = document.getElementById('edit-seleccion-productos');

            if (tipo === 'envio_gratis') {
                campoValorDescuento.classList.add('hidden');
                seleccionProductos.classList.add('hidden');
            } else {
                campoValorDescuento.classList.remove('hidden');
                seleccionProductos.classList.remove('hidden');

                if (tipo === 'descuento_monto') {
                    etiquetaValorDescuento.textContent = 'Monto de Descuento';
                    simboloDescuento.textContent = 'S/';
                } else {
                    etiquetaValorDescuento.textContent = 'Porcentaje de Descuento';
                    simboloDescuento.textContent = '%';
                }

                cargarProductosParaEdicionPromocion(promocion.productos || []);
            }
        });

        document.getElementById('modal-editar-promocion').classList.add('active');
    }

    // Función para cargar productos para edición
    function cargarProductosParaEdicionPromocion(productosSeleccionados) {
        const lista = document.getElementById('edit-lista-productos-promocion');
        if (!lista) return;

        let html = '';
        productos.forEach(producto => {
            const estaSeleccionado = productosSeleccionados.some(p => p.id == producto.id);
            const categoria = categorias.find(c => c.id == producto.categoria_id);
            html += `
            <div class="flex items-center mb-2">
                <input type="checkbox" id="edit-producto-${producto.id}" value="${producto.id}" ${estaSeleccionado ? 'checked' : ''} class="rounded border-gray-300 text-pink-600 focus:ring-pink-500">
                <label for="edit-producto-${producto.id}" class="ml-2 text-sm text-gray-700">
                    ${producto.nombre} - S/ ${producto.precio}
                    <span class="text-xs text-gray-500">(${categoria?.nombre || 'Sin categoría'})</span>
                </label>
            </div>
            `;
        });

        lista.innerHTML = html;
    }

    // Función para guardar cambios de promoción
    async function guardarCambiosPromocion(promocionId) {
        if (!validarFormularioEdicionPromocion()) {
            return;
        }

        const formData = new FormData();
        formData.append('id', promocionId);
        formData.append('nombre', document.getElementById('edit-promocion-nombre').value);
        formData.append('descripcion', document.getElementById('edit-promocion-descripcion').value);
        formData.append('tipo', document.getElementById('edit-promocion-tipo').value);
        formData.append('valor_descuento', document.getElementById('edit-promocion-valor-descuento').value || 0);
        formData.append('fecha_inicio', document.getElementById('edit-promocion-fecha-inicio').value);
        formData.append('fecha_fin', document.getElementById('edit-promocion-fecha-fin').value);
        formData.append('max_usos', document.getElementById('edit-promocion-max-usos').value || '');
        formData.append('activa', document.getElementById('edit-promocion-activa').checked ? 1 : 0);

        // Recopilar productos seleccionados
        const productosSeleccionados = [];
        document.querySelectorAll('#edit-lista-productos-promocion input[type="checkbox"]:checked').forEach(checkbox => {
            productosSeleccionados.push(checkbox.value);
        });
        formData.append('productos', JSON.stringify(productosSeleccionados));

        try {
            const response = await fetch(`${API_BASE}api-actualizar-promocion`, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                mostrarNotificacion('Promoción actualizada exitosamente', 'success');
                document.getElementById('modal-editar-promocion').classList.remove('active');
                await cargarPromociones();
                await cargarProductosPromocion();
            } else {
                mostrarNotificacion(data.message || 'Error al actualizar promoción', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarNotificacion('Error de conexión', 'error');
        }
    }

    // Función para validar formulario de edición
    function validarFormularioEdicionPromocion() {
        const nombre = document.getElementById('edit-promocion-nombre').value.trim();
        const tipo = document.getElementById('edit-promocion-tipo').value;
        const fechaInicio = document.getElementById('edit-promocion-fecha-inicio').value;
        const fechaFin = document.getElementById('edit-promocion-fecha-fin').value;
        const valorDescuento = document.getElementById('edit-promocion-valor-descuento').value;

        if (!nombre) {
            mostrarNotificacion('El nombre de la promoción es obligatorio', 'error');
            return false;
        }

        if (!tipo) {
            mostrarNotificacion('El tipo de promoción es obligatorio', 'error');
            return false;
        }

        if (!fechaInicio || !fechaFin) {
            mostrarNotificacion('Las fechas de inicio y fin son obligatorias', 'error');
            return false;
        }

        if (fechaInicio > fechaFin) {
            mostrarNotificacion('La fecha de inicio no puede ser posterior a la fecha de fin', 'error');
            return false;
        }

        if (tipo !== 'envio_gratis' && (!valorDescuento || parseFloat(valorDescuento) <= 0)) {
            mostrarNotificacion('El valor de descuento debe ser mayor a 0', 'error');
            return false;
        }

        if (tipo !== 'envio_gratis') {
            const productosSeleccionados = document.querySelectorAll('#edit-lista-productos-promocion input[type="checkbox"]:checked').length;
            if (productosSeleccionados === 0) {
                mostrarNotificacion('Debes seleccionar al menos un producto para la promoción', 'error');
                return false;
            }
        }

        return true;
    }

    // Función para ver detalles de promoción
    async function verDetallesPromocion(promocionId) {
        try {
            const response = await fetch(`${API_BASE}api-obtener-promocion&id=${promocionId}`);
            const data = await response.json();

            if (data.success) {
                const promocion = data.data;
                mostrarModalDetalles(promocion);
            } else {
                mostrarNotificacion('Error al cargar detalles de la promoción', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarNotificacion('Error de conexión', 'error');
        }
    }

    // Función para mostrar modal de detalles
    function mostrarModalDetalles(promocion) {
        // Determinar tipo
        let tipoTexto = '';
        let descuentoTexto = '';

        switch (promocion.tipo) {
            case 'descuento_porcentaje':
                tipoTexto = 'Descuento Porcentaje';
                descuentoTexto = `${promocion.valor_descuento}% de descuento`;
                break;
            case 'descuento_monto':
                tipoTexto = 'Descuento Monto Fijo';
                descuentoTexto = `S/ ${promocion.valor_descuento} de descuento`;
                break;
            case 'combo':
                tipoTexto = 'Combo';
                descuentoTexto = `${promocion.valor_descuento}% de descuento en combo`;
                break;
            case 'envio_gratis':
                tipoTexto = 'Envío Gratis';
                descuentoTexto = 'Envío gratis en compras';
                break;
        }

        // Determinar estado
        const hoy = new Date().toISOString().split('T')[0];
        let estado = 'Activa';
        let estadoColor = 'text-green-600';

        if (!promocion.activa) {
            estado = 'Inactiva';
            estadoColor = 'text-yellow-600';
        } else if (promocion.fecha_fin < hoy) {
            estado = 'Expirada';
            estadoColor = 'text-red-600';
        }

        // Calcular días restantes
        const fechaFin = new Date(promocion.fecha_fin);
        const hoyDate = new Date();
        const diasRestantes = Math.ceil((fechaFin - hoyDate) / (1000 * 60 * 60 * 24));

        let html = `
        <div class="space-y-6">
            <!-- Información Básica -->
            <div>
                <h4 class="font-bold text-lg text-gray-800 mb-3">Información Básica</h4>
                <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
                    <div>
                        <span class="text-sm font-medium text-gray-600">Nombre:</span>
                        <p class="text-gray-900">${promocion.nombre}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Tipo:</span>
                        <p class="text-gray-900">${tipoTexto}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Descuento:</span>
                        <p class="text-gray-900">${descuentoTexto}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Estado:</span>
                        <p class="${estadoColor} font-medium">${estado}</p>
                    </div>
                    <div class="col-span-2">
                        <span class="text-sm font-medium text-gray-600">Descripción:</span>
                        <p class="text-gray-900">${promocion.descripcion}</p>
                    </div>
                </div>
            </div>
            
            <!-- Configuración de Vigencia -->
            <div>
                <h4 class="font-bold text-lg text-gray-800 mb-3">Configuración de Vigencia</h4>
                <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
                    <div>
                        <span class="text-sm font-medium text-gray-600">Fecha de Inicio:</span>
                        <p class="text-gray-900">${promocion.fecha_inicio}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Fecha de Fin:</span>
                        <p class="text-gray-900">${promocion.fecha_fin}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Días Restantes:</span>
                        <p class="text-gray-900">${diasRestantes > 0 ? diasRestantes : 'Finalizada'}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Usos:</span>
                        <p class="text-gray-900">${promocion.usos_actual} ${promocion.max_usos ? `/ ${promocion.max_usos}` : '(sin límite)'}</p>
                    </div>
                </div>
            </div>
        `;

        if (promocion.tipo !== 'envio_gratis' && promocion.productos && promocion.productos.length > 0) {
            html += `
            <!-- Productos en Promoción -->
            <div>
                <h4 class="font-bold text-lg text-gray-800 mb-3">Productos en Promoción (${promocion.productos.length})</h4>
                <div class="bg-gray-50 p-4 rounded-lg max-h-60 overflow-y-auto">
                    <div class="grid grid-cols-1 gap-2">
                        ${promocion.productos.map(producto => `
                            <div class="flex items-center justify-between py-2 border-b border-gray-200 last:border-0">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-pink-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-gift text-pink-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">${producto.nombre}</p>
                                        <p class="text-xs text-gray-600">${producto.categoria_nombre || 'Sin categoría'} - S/ ${producto.precio}</p>
                                    </div>
                                </div>
                                <span class="text-xs ${producto.stock <= 5 ? 'text-red-600' : 'text-gray-600'}">Stock: ${producto.stock}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
            `;
        }

        html += `</div>`;

        document.getElementById('contenido-detalles-promocion').innerHTML = html;
        document.getElementById('modal-detalles-promocion').classList.add('active');
    }

    // Función para ver detalles de producto
    async function verDetallesProducto(productoId) {
        const producto = productos.find(p => p.id == productoId);
        if (!producto) return;

        // Obtener promociones que incluyen este producto
        const promocionesProducto = promociones.filter(p =>
            p.activa && new Date(p.fecha_fin) >= new Date() &&
            p.productos && p.productos.some(prod => prod.id == productoId)
        );

        const categoria = categorias.find(c => c.id == producto.categoria_id);

        let html = `
        <div class="space-y-6">
            <!-- Información del Producto -->
            <div>
                <h4 class="font-bold text-lg text-gray-800 mb-3">Información del Producto</h4>
                <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
                    <div>
                        <span class="text-sm font-medium text-gray-600">Nombre:</span>
                        <p class="text-gray-900">${producto.nombre}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Categoría:</span>
                        <p class="text-gray-900">${categoria?.nombre || 'Sin categoría'}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Precio:</span>
                        <p class="text-gray-900">S/ ${producto.precio}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Stock:</span>
                        <p class="${producto.stock <= 5 ? 'text-red-600 font-medium' : 'text-gray-900'}">${producto.stock} unidades</p>
                    </div>
                </div>
            </div>
            
            <!-- Promociones Activas -->
            <div>
                <h4 class="font-bold text-lg text-gray-800 mb-3">Promociones Activas (${promocionesProducto.length})</h4>
                ${promocionesProducto.length > 0 ? `
                <div class="bg-gray-50 p-4 rounded-lg max-h-60 overflow-y-auto">
                    <div class="space-y-4">
                        ${promocionesProducto.map(promocion => {
                            // Calcular días restantes
                            const fechaFin = new Date(promocion.fecha_fin);
                            const hoyDate = new Date();
                            const diasRestantes = Math.ceil((fechaFin - hoyDate) / (1000 * 60 * 60 * 24));
                            
                            return `
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h5 class="font-medium text-gray-800">${promocion.nombre}</h5>
                                    <span class="text-xs ${diasRestantes <= 7 ? 'text-red-600' : 'text-gray-600'}">${diasRestantes} días restantes</span>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">${promocion.descripcion}</p>
                                <div class="flex justify-between text-xs">
                                    <span class="text-gray-600">Usos: ${promocion.usos_actual} ${promocion.max_usos ? `/ ${promocion.max_usos}` : ''}</span>
                                    <span class="font-medium text-pink-600">
                                        ${promocion.tipo === 'descuento_porcentaje' ? `${promocion.valor_descuento}% descuento` : ''}
                                        ${promocion.tipo === 'descuento_monto' ? `S/ ${promocion.valor_descuento} descuento` : ''}
                                        ${promocion.tipo === 'combo' ? `${promocion.valor_descuento}% descuento en combo` : ''}
                                        ${promocion.tipo === 'envio_gratis' ? 'Envío gratis' : ''}
                                    </span>
                                </div>
                            </div>
                            `;
                        }).join('')}
                    </div>
                </div>
                ` : `
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                    <i class="fas fa-info-circle text-yellow-500 text-xl mb-2"></i>
                    <p class="text-gray-700">Este producto no está incluido en ninguna promoción activa</p>
                </div>
                `}
            </div>
        </div>
        `;

        document.getElementById('contenido-detalles-promocion').innerHTML = html;
        document.getElementById('modal-detalles-promocion').classList.add('active');
    }

    // Función para eliminar promoción
    async function eliminarPromocion(promocionId) {
        const promocion = promociones.find(p => p.id == promocionId);
        if (!promocion) return;

        if (promocion.usos_actual > 0) {
            mostrarNotificacion('No se puede eliminar una promoción que ya ha sido utilizada', 'error');
            return;
        }

        if (confirm(`¿Estás seguro de que quieres eliminar la promoción "${promocion.nombre}"? Esta acción no se puede deshacer.`)) {
            const formData = new FormData();
            formData.append('id', promocionId);

            try {
                const response = await fetch(`${API_BASE}api-eliminar-promocion`, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    mostrarNotificacion('Promoción eliminada exitosamente', 'success');
                    await cargarPromociones();
                    await cargarProductosPromocion();
                } else {
                    mostrarNotificacion(data.message || 'Error al eliminar promoción', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarNotificacion('Error de conexión', 'error');
            }
        }
    }

    // Función para actualizar estadísticas
    function actualizarEstadisticasPromociones() {
        const promocionesActivas = promociones.filter(p =>
            p.activa && new Date(p.fecha_fin) >= new Date()
        ).length;

        // Actualizar badges
        document.getElementById('total-promociones-badge').textContent = promocionesActivas;
        document.getElementById('total-promociones-text').textContent = promocionesActivas;
    }

    // Función para exportar promociones
    function exportarPromociones() {
        mostrarNotificacion('Exportando lista de promociones...', 'info');
        setTimeout(() => {
            mostrarNotificacion('Lista de promociones exportada exitosamente', 'success');
        }, 1500);
    }

    // Función para mostrar notificación
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
                if (document.body.contains(notificacion)) {
                    document.body.removeChild(notificacion);
                }
            }, 300);
        }, 3000);
    }

    // Inicializar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        inicializarApp();
        configurarEventListeners();
        // En el DOMContentLoaded, agrega esta línea:
        actualizarContadorPapelera();

        // Cargar datos iniciales
        cargarPromociones();
        cargarProductos();
        cargarCategorias();
        cargarProductosPromocion();
    });

    // Función para cargar papelera (MEJORADA)
    async function cargarPapelera() {
        try {
            mostrarCargandoPapelera(true);

            const response = await fetch(`${API_BASE}api-listar-eliminadas-promociones`);
            const data = await response.json();

            if (data.success) {
                actualizarListaPapelera(data.data);
                actualizarContadorPapelera(data.total);
            } else {
                mostrarNotificacion('Error al cargar papelera', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarNotificacion('Error de conexión', 'error');
        } finally {
            mostrarCargandoPapelera(false);
        }
    }

    // Función para mostrar/ocultar carga en papelera
    function mostrarCargandoPapelera(mostrar) {
        const tabla = document.getElementById('tabla-papelera');
        const sinResultados = document.getElementById('sin-resultados-papelera');

        if (mostrar) {
            tabla.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-8 text-center">
                    <div class="flex justify-center items-center">
                        <i class="fas fa-spinner fa-spin text-pink-500 text-xl mr-2"></i>
                        <span class="text-gray-600">Cargando papelera...</span>
                    </div>
                </td>
            </tr>
        `;
            if (sinResultados) sinResultados.classList.add('hidden');
        }
    }

    // Función para actualizar lista de papelera (MEJORADA)
    function actualizarListaPapelera(promocionesEliminadas) {
        const tabla = document.getElementById('tabla-papelera');
        const sinResultados = document.getElementById('sin-resultados-papelera');

        if (!tabla || !sinResultados) return;

        if (promocionesEliminadas.length === 0) {
            tabla.innerHTML = '';
            sinResultados.classList.remove('hidden');
            return;
        }

        sinResultados.classList.add('hidden');

        let html = '';
        promocionesEliminadas.forEach(promocion => {
            // Determinar tipo
            let tipoBadge = '';
            let descuentoTexto = '';

            switch (promocion.tipo) {
                case 'descuento_porcentaje':
                    tipoBadge = '<span class="tipo-badge tipo-descuento">Descuento %</span>';
                    descuentoTexto = `${promocion.valor_descuento}%`;
                    break;
                case 'descuento_monto':
                    tipoBadge = '<span class="tipo-badge tipo-descuento">Descuento Monto</span>';
                    descuentoTexto = `S/ ${promocion.valor_descuento}`;
                    break;
                case 'combo':
                    tipoBadge = '<span class="tipo-badge tipo-combo">Combo</span>';
                    descuentoTexto = `${promocion.valor_descuento}%`;
                    break;
                case 'envio_gratis':
                    tipoBadge = '<span class="tipo-badge tipo-envio">Envío Gratis</span>';
                    descuentoTexto = 'Gratis';
                    break;
            }

            // Formatear fecha de eliminación
            const fechaEliminado = promocion.fecha_eliminado ?
                new Date(promocion.fecha_eliminado).toLocaleDateString('es-ES', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                }) :
                'Fecha no disponible';

            // Usos
            let usosTexto = promocion.max_usos ?
                `${promocion.usos_actual} / ${promocion.max_usos}` :
                `${promocion.usos_actual} (sin límite)`;

            html += `
        <tr class="fade-in">
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-trash text-gray-600"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">${promocion.nombre}</div>
                        <div class="text-sm text-gray-500">${promocion.descripcion}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">${tipoBadge}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${descuentoTexto}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${fechaEliminado}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${usosTexto}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button class="text-green-600 hover:text-green-900 mr-3" onclick="restaurarPromocion(${promocion.id})">
                    <i class="fas fa-undo mr-1"></i>Restaurar
                </button>
                <button class="text-red-600 hover:text-red-900" onclick="eliminarPermanentementePromocion(${promocion.id})">
                    <i class="fas fa-times mr-1"></i>Eliminar
                </button>
            </td>
        </tr>
        `;
        });

        tabla.innerHTML = html;
    }

    // Función para actualizar contador de papelera (MEJORADA)
    async function actualizarContadorPapelera(total = null) {
        try {
            if (total === null) {
                const response = await fetch(`${API_BASE}api-contar-papelera-promociones`);
                const data = await response.json();
                if (data.success) {
                    total = data.total;
                }
            }

            const badge = document.getElementById('total-papelera-badge');
            const text = document.getElementById('total-papelera-text');

            if (badge) badge.textContent = total || 0;
            if (text) text.textContent = total || 0;

        } catch (error) {
            console.error('Error al actualizar contador de papelera:', error);
        }
    }

    // Función para mover promoción a papelera (CORREGIDA)
    async function moverPapeleraPromocion(promocionId) {
        const promocion = promociones.find(p => p.id == promocionId);
        if (!promocion) return;

        if (confirm(`¿Estás seguro de que quieres mover la promoción "${promocion.nombre}" a la papelera?`)) {
            const formData = new FormData();
            formData.append('id', promocionId);

            try {
                const response = await fetch(`${API_BASE}api-mover-papelera-promocion`, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    mostrarNotificacion('Promoción movida a la papelera', 'success');

                    // Actualizar todas las vistas inmediatamente
                    await cargarPromociones(); // Recargar lista principal
                    await cargarPapelera(); // Recargar papelera
                    await actualizarContadorPapelera(); // Actualizar contador

                    // Si estamos en la pestaña de papelera, recargar
                    if (document.querySelector('[data-tab="papelera-promociones"]').classList.contains('active')) {
                        await cargarPapelera();
                    }
                } else {
                    mostrarNotificacion(data.message || 'Error al mover a papelera', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarNotificacion('Error de conexión', 'error');
            }
        }
    }

    // Función para restaurar promoción (MEJORADA)
    async function restaurarPromocion(promocionId) {
        if (confirm('¿Estás seguro de que quieres restaurar esta promoción?')) {
            const formData = new FormData();
            formData.append('id', promocionId);

            try {
                const response = await fetch(`${API_BASE}api-restaurar-promocion`, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    mostrarNotificacion('Promoción restaurada exitosamente', 'success');

                    // Actualizar todas las vistas inmediatamente
                    await cargarPromociones(); // Recargar lista principal
                    await cargarPapelera(); // Recargar papelera
                    await actualizarContadorPapelera(); // Actualizar contador

                } else {
                    mostrarNotificacion(data.message || 'Error al restaurar promoción', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarNotificacion('Error de conexión', 'error');
            }
        }
    }

    // Función para eliminar permanentemente (MEJORADA)
    async function eliminarPermanentementePromocion(promocionId) {
        if (confirm('¿Estás seguro de que quieres eliminar permanentemente esta promoción? Esta acción no se puede deshacer.')) {
            const formData = new FormData();
            formData.append('id', promocionId);

            try {
                const response = await fetch(`${API_BASE}api-eliminar-permanentemente-promocion`, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    mostrarNotificacion('Promoción eliminada permanentemente', 'success');

                    // Actualizar papelera inmediatamente
                    await cargarPapelera();
                    await actualizarContadorPapelera();

                } else {
                    mostrarNotificacion(data.message || 'Error al eliminar permanentemente', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarNotificacion('Error de conexión', 'error');
            }
        }
    }

    // Función para vaciar papelera (MEJORADA)
    async function vaciarPapelera() {
        if (confirm('¿Estás seguro de que quieres vaciar la papelera? Esta acción eliminará permanentemente todas las promociones que no hayan sido utilizadas y no se puede deshacer.')) {
            try {
                const response = await fetch(`${API_BASE}api-vaciar-papelera-promociones`, {
                    method: 'POST'
                });

                const data = await response.json();

                if (data.success) {
                    mostrarNotificacion(data.message, 'success');

                    // Actualizar papelera inmediatamente
                    await cargarPapelera();
                    await actualizarContadorPapelera(0);

                } else {
                    mostrarNotificacion(data.message || 'Error al vaciar papelera', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarNotificacion('Error de conexión', 'error');
            }
        }
    }

    // Función para eliminar promoción (CORREGIDA - ahora usa papelera)
    async function eliminarPromocion(promocionId) {
        const promocion = promociones.find(p => p.id == promocionId);
        if (!promocion) return;

        if (promocion.usos_actual > 0) {
            mostrarNotificacion('No se puede eliminar una promoción que ya ha sido utilizada', 'error');
            return;
        }

        // Cambiar para mover a papelera en lugar de eliminar directamente
        await moverPapeleraPromocion(promocionId);
    }
</script>