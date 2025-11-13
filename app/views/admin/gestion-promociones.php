<?php
// Verificar si hay un mensaje de éxito
$mensaje_exito = '';
if (isset($_SESSION['mensaje_exito'])) {
    $mensaje_exito = $_SESSION['mensaje_exito'];
    unset($_SESSION['mensaje_exito']);
}

// Verificar si hay un mensaje de error
$mensaje_error = '';
if (isset($_SESSION['mensaje_error'])) {
    $mensaje_error = $_SESSION['mensaje_error'];
    unset($_SESSION['mensaje_error']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SamyGlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Tus estilos CSS originales aquí */
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
        
        /* ... resto de tus estilos CSS ... */
    </style>
</head>
<body class="flex h-screen bg-gray-50">
    <!-- Incluir sidebar -->
    <?php include __DIR__ . '/../templates/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Incluir header -->
        <?php include __DIR__ . '/../templates/header.php'; ?>
        
        <main class="flex-1 overflow-y-auto p-6">
            <!-- Mensajes de éxito/error -->
            <?php if ($mensaje_exito): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($mensaje_exito); ?></span>
            </div>
            <?php endif; ?>

            <?php if ($mensaje_error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($mensaje_error); ?></span>
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
                                        <!-- Las promociones se cargarán aquí dinámicamente via AJAX -->
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
                                                <!-- Los productos se cargarán aquí dinámicamente via AJAX -->
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
                                    <!-- Los productos en promoción se cargarán aquí dinámicamente via AJAX -->
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
                </div>
            </div>
        </main>
    </div>

    <!-- Modales -->
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
        
        // Función para cargar promociones via AJAX
        async function cargarPromociones() {
            try {
                const response = await fetch(`${API_BASE}api-listar-promociones`);
                const data = await response.json();
                
                if (data.success) {
                    actualizarListaPromociones(data.data);
                    actualizarEstadisticas(data.data);
                } else {
                    mostrarNotificacion('Error al cargar promociones', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarNotificacion('Error de conexión', 'error');
            }
        }

        // Función para guardar nueva promoción
        async function guardarNuevaPromocion() {
            const formData = new FormData();
            
            // Recopilar datos del formulario
            formData.append('nombre', document.getElementById('promocion-nombre').value);
            formData.append('descripcion', document.getElementById('promocion-descripcion').value);
            formData.append('tipo', document.getElementById('promocion-tipo').value);
            formData.append('valor_descuento', document.getElementById('promocion-valor-descuento').value || 0);
            formData.append('fecha_inicio', document.getElementById('promocion-fecha-inicio').value);
            formData.append('fecha_fin', document.getElementById('promocion-fecha-fin').value);
            formData.append('max_usos', document.getElementById('promocion-max-usos').value || null);
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
                    cargarPromociones();
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

        // Inicializar cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            // Tu código de inicialización original aquí
            inicializarApp();
            configurarEventListeners();
            
            // Cargar datos iniciales
            cargarPromociones();
            cargarProductosParaPromocion();
            cargarCategorias();
        });

        // ... el resto de tus funciones JavaScript originales ...

    </script>
</body>
</html>