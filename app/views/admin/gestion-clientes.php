<?php
// app/views/admin/gestion-clientes.php
// Debug temporal
error_log("=== DEBUG CLIENTES ===");
error_log("Total clientes: " . count($clientes));
error_log("Estadísticas: " . print_r($estadisticas, true));
error_log("Frecuentes: " . count($clientesFrecuentes));

// Verificar si hay datos
if (empty($clientes)) {
    error_log("⚠️ NO HAY CLIENTES EN LA BASE DE DATOS");
} else {
    error_log("✅ Clientes encontrados: " . count($clientes));
    foreach ($clientes as $cliente) {
        error_log(" - " . $cliente['nombres'] . " " . $cliente['apellidos'] . " (ID: " . $cliente['id'] . ")");
    }
}
// Los datos ya vienen del controlador: $clientes, $estadisticas, $clientesFrecuentes
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
        max-width: 600px;
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

    .cliente-card {
        transition: all 0.3s ease;
    }

    .cliente-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-activo {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-inactivo {
        background-color: #fef3c7;
        color: #d97706;
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    }

    .loading-spinner {
        border: 3px solid #f3f3f3;
        border-top: 3px solid #ec4899;
        border-radius: 50%;
        width: 30px;
        height: 30px;
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

    .custom-notification {
        min-width: 300px;
        max-width: 400px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-radius: 12px;
        z-index: 10001;
        position: fixed;
        top: 100px;
        right: 20px;
        animation: slideInRight 0.3s ease-out;
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100%);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
</style>

<main class="flex-1 overflow-y-auto p-6">
    <!-- Sección de Gestión de Clientes -->
    <div class="content-section active" id="clientes">
        <!-- Tabs de navegación -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="flex border-b overflow-x-auto">
                <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-600 hover:bg-gray-50 active" data-tab="lista-clientes">
                    <i class="fas fa-address-book mr-2"></i>Lista de Clientes
                    <span class="ml-2 bg-pink-500 text-white text-xs rounded-full px-2 py-1" id="total-clientes-badge"><?= count($clientes) ?></span>
                </button>
                <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-600 hover:bg-gray-50" data-tab="clientes-frecuentes">
                    <i class="fas fa-star mr-2"></i>Clientes Frecuentes
                    <span class="ml-2 bg-yellow-500 text-white text-xs rounded-full px-2 py-1" id="frecuentes-badge"><?= count($clientesFrecuentes) ?></span>
                </button>
                <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-600 hover:bg-gray-50" data-tab="nuevo-cliente">
                    <i class="fas fa-user-plus mr-2"></i>Nuevo Cliente
                </button>
            </div>
        </div>

        <!-- Contenido de las pestañas -->
        <div id="tab-content">
            <!-- Pestaña: Lista de Clientes -->
            <div class="tab-panel active" id="lista-clientes-panel">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-4 border-b flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold">Lista de Clientes</h2>
                            <p class="text-gray-600"><span id="total-clientes-text"><?= count($clientes) ?></span> clientes registrados en el sistema</p>
                        </div>
                        <div class="flex space-x-2">
                            <div class="relative">
                                <input type="text" id="buscador-clientes" placeholder="Buscar cliente..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 w-64">
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            </div>
                            <button class="bg-pink-500 hover:bg-pink-600 text-white font-medium py-2 px-4 rounded-lg transition-colors" onclick="exportarClientes()">
                                <i class="fas fa-download mr-2"></i>Exportar
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DNI</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pedidos</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Gastado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="tabla-clientes">
                                <?php foreach ($clientes as $cliente): ?>
                                    <tr class="fade-in">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-user text-purple-600"></i>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($cliente['nombres'] . ' ' . $cliente['apellidos']) ?></div>
                                                    <div class="text-sm text-gray-500">Registrado: <?= date('d/m/Y', strtotime($cliente['created_at'])) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($cliente['dni']) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?= htmlspecialchars($cliente['correo']) ?></div>
                                            <div class="text-sm text-gray-500"><?= htmlspecialchars($cliente['telefono']) ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $cliente['total_pedidos'] ?> pedidos</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">S/ <?= number_format($cliente['total_gastado'], 2) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Activo
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button class="text-blue-600 hover:text-blue-900 mr-3" onclick="verDetallesCliente(<?= $cliente['id'] ?>)">
                                                <i class="fas fa-eye mr-1"></i>Ver
                                            </button>
                                            <button class="text-green-600 hover:text-green-900 mr-3" onclick="editarCliente(<?= $cliente['id'] ?>)">
                                                <i class="fas fa-edit mr-1"></i>Editar
                                            </button>
                                            <button class="text-red-600 hover:text-red-900" onclick="eliminarCliente(<?= $cliente['id'] ?>)">
                                                <i class="fas fa-trash mr-1"></i>Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mensaje cuando no hay resultados -->
                    <div id="sin-resultados-clientes" class="hidden p-8 text-center">
                        <i class="fas fa-search text-gray-300 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-700">No se encontraron clientes</h3>
                        <p class="text-gray-500 mt-2">Intenta con otros términos de búsqueda</p>
                    </div>
                </div>
            </div>

            <!-- Pestaña: Clientes Frecuentes -->
            <div class="tab-panel" id="clientes-frecuentes-panel">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-4 border-b flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold">Clientes Frecuentes</h2>
                            <p class="text-gray-600">Top 5 clientes con mayor fidelidad y compras</p>
                        </div>
                        <div class="flex space-x-2">
                            <select id="filtro-frecuentes" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                                <option value="pedidos">Ordenar por Pedidos</option>
                                <option value="gastado">Ordenar por Total Gastado</option>
                                <option value="reciente">Ordenar por Más Reciente</option>
                            </select>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- Estadísticas de clientes frecuentes -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border border-yellow-200 rounded-lg p-6">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                                        <i class="fas fa-crown text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-800" id="cliente-top-pedidos"><?= $estadisticas['max_pedidos'] ?? 0 ?></p>
                                        <p class="text-sm text-gray-600">Máx. pedidos por cliente</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-6">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                                        <i class="fas fa-money-bill-wave text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-800" id="cliente-top-gastado">S/ <?= number_format($estadisticas['max_gastado'] ?? 0, 2) ?></p>
                                        <p class="text-sm text-gray-600">Máx. gastado por cliente</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border border-blue-200 rounded-lg p-6">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                                        <i class="fas fa-users text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-800" id="promedio-pedidos"><?= number_format($estadisticas['promedio_pedidos'] ?? 0, 1) ?></p>
                                        <p class="text-sm text-gray-600">Promedio de pedidos</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Lista de clientes frecuentes -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="grid-clientes-frecuentes">
                            <?php foreach ($clientesFrecuentes as $index => $cliente): ?>
                                <div class="cliente-card bg-gradient-to-br 
                                    <?= $index == 0 ? 'from-yellow-100 to-amber-100 border-yellow-200' : '' ?>
                                    <?= $index == 1 ? 'from-gray-100 to-slate-100 border-gray-200' : '' ?>
                                    <?= $index == 2 ? 'from-amber-100 to-orange-100 border-amber-200' : '' ?>
                                    <?= $index == 3 ? 'from-blue-100 to-cyan-100 border-blue-200' : '' ?>
                                    <?= $index == 4 ? 'from-green-100 to-emerald-100 border-green-200' : '' ?>
                                    border rounded-lg p-6 fade-in">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-center">
                                            <div class="p-3 rounded-full 
                                                <?= $index == 0 ? 'bg-yellow-100 text-yellow-600' : '' ?>
                                                <?= $index == 1 ? 'bg-gray-100 text-gray-600' : '' ?>
                                                <?= $index == 2 ? 'bg-amber-100 text-amber-600' : '' ?>
                                                <?= $index == 3 ? 'bg-blue-100 text-blue-600' : '' ?>
                                                <?= $index == 4 ? 'bg-green-100 text-green-600' : '' ?>
                                                mr-3">
                                                <i class="fas 
                                                    <?= $index == 0 ? 'fa-crown' : '' ?>
                                                    <?= $index == 1 ? 'fa-star' : '' ?>
                                                    <?= $index == 2 ? 'fa-medal' : '' ?>
                                                    <?= $index == 3 ? 'fa-trophy' : '' ?>
                                                    <?= $index == 4 ? 'fa-award' : '' ?>
                                                "></i>
                                            </div>
                                            <div>
                                                <h3 class="font-bold text-gray-800"><?= htmlspecialchars($cliente['nombres'] . ' ' . $cliente['apellidos']) ?></h3>
                                                <p class="text-sm text-gray-600"><?= htmlspecialchars($cliente['dni']) ?></p>
                                            </div>
                                        </div>
                                        <span class="bg-white text-gray-700 text-xs font-bold px-2 py-1 rounded-full">#<?= $index + 1 ?></span>
                                    </div>

                                    <div class="space-y-3 mb-4">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Total Pedidos:</span>
                                            <span class="font-bold text-gray-800"><?= $cliente['total_pedidos'] ?></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Total Gastado:</span>
                                            <span class="font-bold text-gray-800">S/ <?= number_format($cliente['total_gastado'], 2) ?></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Último Pedido:</span>
                                            <span class="text-sm text-gray-800"><?= $cliente['ultimo_pedido'] ? date('d/m/Y', strtotime($cliente['ultimo_pedido'])) : 'Nunca' ?></span>
                                        </div>
                                    </div>

                                    <div class="flex space-x-2">
                                        <button class="flex-1 bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 font-medium py-2 px-3 rounded transition-colors text-sm" onclick="verDetallesCliente(<?= $cliente['id'] ?>)">
                                            <i class="fas fa-eye mr-1"></i>Detalles
                                        </button>
                                        <button class="flex-1 bg-pink-500 text-white hover:bg-pink-600 font-medium py-2 px-3 rounded transition-colors text-sm" onclick="editarCliente(<?= $cliente['id'] ?>)">
                                            <i class="fas fa-edit mr-1"></i>Editar
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Mensaje cuando no hay clientes frecuentes -->
                        <div id="sin-clientes-frecuentes" class="<?= count($clientesFrecuentes) > 0 ? 'hidden' : '' ?> text-center py-12">
                            <i class="fas fa-star text-gray-300 text-5xl mb-4"></i>
                            <h3 class="text-xl font-medium text-gray-700">No hay clientes frecuentes</h3>
                            <p class="text-gray-500 mt-2">Los clientes aparecerán aquí cuando realicen múltiples pedidos</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pestaña: Nuevo Cliente -->
            <div class="tab-panel" id="nuevo-cliente-panel">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold mb-6">Registrar Nuevo Cliente</h2>

                    <form id="form-nuevo-cliente" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Información Personal -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-pink-600 mb-3">Información Personal</h3>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombres *</label>
                                    <input type="text" id="cliente-nombres" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" placeholder="Ej: María" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
                                    <input type="text" id="cliente-apellidos" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" placeholder="Ej: Gonzales López" required>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">DNI *</label>
                                <input type="text" id="cliente-dni" maxlength="8" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" placeholder="8 dígitos" required>
                                <p class="text-xs text-gray-500 mt-1">El DNI debe tener exactamente 8 dígitos</p>
                            </div>
                        </div>

                        <!-- Información de Contacto -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-pink-600 mb-3">Información de Contacto</h3>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico *</label>
                                <input type="email" id="cliente-correo" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" placeholder="ejemplo@email.com" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono *</label>
                                <input type="text" id="cliente-telefono" maxlength="9" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" placeholder="9 dígitos" required>
                                <p class="text-xs text-gray-500 mt-1">El teléfono debe tener exactamente 9 dígitos</p>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" id="cliente-activo" class="rounded border-gray-300 text-pink-600 focus:ring-pink-500" checked>
                                <label for="cliente-activo" class="ml-2 text-sm text-gray-700">Cliente activo</label>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Notas Adicionales</label>
                                <textarea id="cliente-notas" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" rows="3" placeholder="Información adicional sobre el cliente..."></textarea>
                            </div>
                        </div>
                    </form>

                    <!-- Botones de acción -->
                    <div class="mt-8 flex justify-end space-x-4">
                        <button type="button" onclick="limpiarFormularioCliente()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-6 rounded-lg transition-colors">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </button>
                        <button type="submit" form="form-nuevo-cliente" class="bg-pink-500 hover:bg-pink-600 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                            <i class="fas fa-save mr-2"></i>Registrar Cliente
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal para editar cliente -->
<div class="modal" id="modal-editar-cliente">
    <div class="modal-content">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Editar Cliente</h3>
            <button class="text-gray-500 hover:text-gray-700" id="cerrar-modal-cliente">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-4" id="contenido-modal-cliente">
            <!-- El formulario de edición se cargará aquí -->
        </div>
    </div>
</div>

<!-- Modal para ver detalles del cliente -->
<div class="modal" id="modal-detalles-cliente">
    <div class="modal-content" style="max-width: 700px;">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Detalles del Cliente</h3>
            <button class="text-gray-500 hover:text-gray-700" id="cerrar-modal-detalles-cliente">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-4" id="contenido-detalles-cliente">
            <!-- Los detalles del cliente se cargarán aquí -->
        </div>
    </div>
</div>

<style>
    /* Estilos específicos para gestión de clientes */
    .cliente-card {
        transition: all 0.3s ease;
    }

    .cliente-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-activo {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-inactivo {
        background-color: #fef3c7;
        color: #d97706;
    }

    .search-highlight {
        background-color: #fffacd;
        padding: 2px 4px;
        border-radius: 4px;
    }
</style>

<script>
    // =============================================
    // 🎯 CONFIGURACIÓN INICIAL Y EVENT LISTENERS
    // =============================================

    document.addEventListener('DOMContentLoaded', function() {
        inicializarAppClientes();
        configurarEventListenersClientes();
    });

    function inicializarAppClientes() {
        // Tabs de Gestión de Clientes
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
    }

    function configurarEventListenersClientes() {
        // Formulario de nuevo cliente
        document.getElementById('form-nuevo-cliente').addEventListener('submit', guardarNuevoCliente);

        // Buscador de clientes
        document.getElementById('buscador-clientes').addEventListener('input', filtrarClientes);

        // Filtro de clientes frecuentes
        document.getElementById('filtro-frecuentes').addEventListener('change', cargarClientesFrecuentes);

        // Cerrar modales
        document.getElementById('cerrar-modal-cliente').addEventListener('click', function() {
            document.getElementById('modal-editar-cliente').classList.remove('active');
        });

        document.getElementById('cerrar-modal-detalles-cliente').addEventListener('click', function() {
            document.getElementById('modal-detalles-cliente').classList.remove('active');
        });

        // Validación de DNI y teléfono
        document.getElementById('cliente-dni').addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 8);
        });

        document.getElementById('cliente-telefono').addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 9);
        });
    }

    // =============================================
    // 🛠️ FUNCIONES UTILITARIAS
    // =============================================

    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe
            .toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function mostrarLoading(mensaje = 'Procesando...') {
        // Remover loading existente
        ocultarLoading();

        const loadingOverlay = document.createElement('div');
        loadingOverlay.className = 'loading-overlay';
        loadingOverlay.id = 'loading-overlay';
        loadingOverlay.innerHTML = `
            <div class="bg-white rounded-lg p-6 shadow-xl flex items-center space-x-4">
                <div class="loading-spinner"></div>
                <span class="text-gray-700 font-medium">${mensaje}</span>
            </div>
        `;
        document.body.appendChild(loadingOverlay);
    }

    function ocultarLoading() {
        const loadingOverlay = document.getElementById('loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.remove();
        }
    }

    function mostrarNotificacion(mensaje, tipo = 'info') {
        // Remover notificaciones existentes
        document.querySelectorAll('.custom-notification').forEach(notif => {
            if (document.body.contains(notif)) {
                document.body.removeChild(notif);
            }
        });

        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-triangle',
            warning: 'fa-exclamation-circle',
            info: 'fa-info-circle'
        };

        const notificacion = document.createElement('div');
        notificacion.className = `custom-notification fixed top-20 right-4 p-4 rounded-lg shadow-lg z-[10001] transform transition-all duration-300 ${
            tipo === 'success' ? 'bg-green-500 text-white' :
            tipo === 'error' ? 'bg-red-500 text-white' :
            tipo === 'warning' ? 'bg-yellow-500 text-white' :
            'bg-blue-500 text-white'
        }`;

        notificacion.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${icons[tipo]} mr-3"></i>
                <span class="font-medium">${mensaje}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        document.body.appendChild(notificacion);

        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (document.body.contains(notificacion)) {
                notificacion.style.opacity = '0';
                notificacion.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (document.body.contains(notificacion)) {
                        document.body.removeChild(notificacion);
                    }
                }, 300);
            }
        }, 5000);
    }

    // =============================================
    // 👥 FUNCIONES CRUD DE CLIENTES
    // =============================================

    async function guardarNuevoCliente(e) {
        e.preventDefault();

        // Obtener valores del formulario
        const nombres = document.getElementById('cliente-nombres').value.trim();
        const apellidos = document.getElementById('cliente-apellidos').value.trim();
        const dni = document.getElementById('cliente-dni').value.trim();
        const correo = document.getElementById('cliente-correo').value.trim();
        const telefono = document.getElementById('cliente-telefono').value.trim();
        const activo = document.getElementById('cliente-activo').checked;
        const notas = document.getElementById('cliente-notas').value.trim();

        // Validaciones
        if (!nombres || !apellidos || !dni || !correo || !telefono) {
            mostrarNotificacion('Por favor completa todos los campos obligatorios', 'error');
            return;
        }

        if (dni.length !== 8) {
            mostrarNotificacion('El DNI debe tener exactamente 8 dígitos', 'error');
            return;
        }

        if (telefono.length !== 9) {
            mostrarNotificacion('El teléfono debe tener exactamente 9 dígitos', 'error');
            return;
        }

        try {
            mostrarLoading('Registrando cliente...');

            const formData = new FormData();
            formData.append('nombres', nombres);
            formData.append('apellidos', apellidos);
            formData.append('dni', dni);
            formData.append('correo', correo);
            formData.append('telefono', telefono);

            const response = await fetch('index.php?view=api-registrar-cliente', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            ocultarLoading();

            if (result.success) {
                mostrarNotificacion('✅ Cliente registrado exitosamente', 'success');
                limpiarFormularioCliente();

                // Cambiar a la pestaña de lista de clientes
                setTimeout(() => {
                    document.querySelector('[data-tab="lista-clientes"]').click();
                    // Recargar la página después de 2 segundos
                    setTimeout(() => window.location.reload(), 2000);
                }, 1500);
            } else {
                throw new Error(result.message || 'Error al registrar cliente');
            }
        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al registrar cliente: ' + error.message, 'error');
            console.error('Error detallado:', error);
        }
    }

    async function editarCliente(clienteId) {
        try {
            mostrarLoading('Cargando cliente...');

            const response = await fetch(`index.php?view=api-obtener-cliente&id=${clienteId}`);
            const result = await response.json();
            ocultarLoading();

            if (result.success) {
                mostrarModalEditarCliente(result.data);
            } else {
                throw new Error(result.message || 'Error al cargar cliente');
            }
        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al cargar cliente: ' + error.message, 'error');
        }
    }

    function mostrarModalEditarCliente(cliente) {
        let html = `
            <form id="form-editar-cliente" class="space-y-4" onsubmit="guardarCambiosCliente(event, ${cliente.id})">
                <input type="hidden" id="edit-cliente-id" value="${cliente.id}">
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombres</label>
                        <input type="text" id="edit-cliente-nombres" value="${escapeHtml(cliente.nombres)}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos</label>
                        <input type="text" id="edit-cliente-apellidos" value="${escapeHtml(cliente.apellidos)}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">DNI</label>
                    <input type="text" id="edit-cliente-dni" value="${escapeHtml(cliente.dni)}" maxlength="8" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required readonly>
                    <p class="text-xs text-gray-500 mt-1">El DNI no se puede modificar</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                    <input type="email" id="edit-cliente-correo" value="${escapeHtml(cliente.correo)}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="text" id="edit-cliente-telefono" value="${escapeHtml(cliente.telefono)}" maxlength="9" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="edit-cliente-activo" ${true ? 'checked' : ''} class="rounded border-gray-300 text-pink-600 focus:ring-pink-500">
                    <label for="edit-cliente-activo" class="ml-2 text-sm text-gray-700">Cliente activo</label>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notas Adicionales</label>
                    <textarea id="edit-cliente-notas" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" rows="3">${escapeHtml(cliente.notas || '')}</textarea>
                </div>
                
                <div class="flex justify-end space-x-4 pt-4">
                    <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-4 rounded transition-colors" onclick="document.getElementById('modal-editar-cliente').classList.remove('active')">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white font-medium py-2 px-4 rounded transition-colors">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        `;

        document.getElementById('contenido-modal-cliente').innerHTML = html;

        setTimeout(() => {
            document.getElementById('modal-editar-cliente').classList.add('active');
        }, 10);
    }

    async function guardarCambiosCliente(e, clienteId) {
        e.preventDefault();

        // Obtener valores del formulario
        const nombres = document.getElementById('edit-cliente-nombres').value;
        const apellidos = document.getElementById('edit-cliente-apellidos').value;
        const correo = document.getElementById('edit-cliente-correo').value;
        const telefono = document.getElementById('edit-cliente-telefono').value;
        const activo = document.getElementById('edit-cliente-activo').checked;
        const notas = document.getElementById('edit-cliente-notas').value;

        // Validaciones
        if (!nombres || !apellidos) {
            mostrarNotificacion('Nombres y apellidos son obligatorios', 'error');
            return;
        }

        if (!empty(telefono) && (telefono.length !== 9 || !isNumeric(telefono))) {
            mostrarNotificacion('El teléfono debe tener exactamente 9 dígitos', 'error');
            return;
        }

        try {
            mostrarLoading('Actualizando cliente...');

            const formData = new FormData();
            formData.append('id', clienteId);
            formData.append('nombres', nombres);
            formData.append('apellidos', apellidos);
            formData.append('correo', correo);
            formData.append('telefono', telefono);

            const response = await fetch('index.php?view=api-actualizar-cliente', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            ocultarLoading();

            if (result.success) {
                mostrarNotificacion('✅ Cliente actualizado exitosamente', 'success');
                document.getElementById('modal-editar-cliente').classList.remove('active');
                // Recargar la página después de 1.5 segundos
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(result.message || 'Error al actualizar cliente');
            }
        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al actualizar cliente: ' + error.message, 'error');
        }
    }

    async function eliminarCliente(clienteId) {
        if (!confirm('¿Estás seguro de que quieres eliminar este cliente? Esta acción no se puede deshacer.')) {
            return;
        }

        try {
            mostrarLoading('Eliminando cliente...');

            const formData = new FormData();
            formData.append('id', clienteId);

            const response = await fetch('index.php?view=api-eliminar-cliente', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            ocultarLoading();

            if (result.success) {
                mostrarNotificacion('✅ Cliente eliminado exitosamente', 'success');
                // Recargar la página después de 1.5 segundos
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(result.message || result.message || 'Error al eliminar cliente');
            }
        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al eliminar cliente: ' + error.message, 'error');
        }
    }

    async function verDetallesCliente(clienteId) {
        try {
            mostrarLoading('Cargando detalles...');

            const response = await fetch(`index.php?view=api-obtener-cliente&id=${clienteId}`);
            const result = await response.json();
            ocultarLoading();

            if (result.success) {
                mostrarModalDetallesCliente(result.data);
            } else {
                throw new Error(result.message || 'Error al cargar detalles del cliente');
            }
        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al cargar detalles: ' + error.message, 'error');
        }
    }

    function mostrarModalDetallesCliente(cliente) {
        const estadisticas = cliente.estadisticas || {};

        let html = `
            <div class="space-y-6">
                <!-- Información Personal -->
                <div>
                    <h4 class="font-bold text-lg text-gray-800 mb-3">Información Personal</h4>
                    <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
                        <div>
                            <span class="text-sm font-medium text-gray-600">Nombres:</span>
                            <p class="text-gray-900">${escapeHtml(cliente.nombres)}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-600">Apellidos:</span>
                            <p class="text-gray-900">${escapeHtml(cliente.apellidos)}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-600">DNI:</span>
                            <p class="text-gray-900">${escapeHtml(cliente.dni)}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-600">Estado:</span>
                            <span class="status-badge status-activo">Activo</span>
                        </div>
                    </div>
                </div>
                
                <!-- Información de Contacto -->
                <div>
                    <h4 class="font-bold text-lg text-gray-800 mb-3">Información de Contacto</h4>
                    <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
                        <div>
                            <span class="text-sm font-medium text-gray-600">Correo:</span>
                            <p class="text-gray-900">${escapeHtml(cliente.correo)}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-600">Teléfono:</span>
                            <p class="text-gray-900">${escapeHtml(cliente.telefono)}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-600">Fecha Registro:</span>
                            <p class="text-gray-900">${new Date(cliente.created_at).toLocaleDateString()}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-600">Último Pedido:</span>
                            <p class="text-gray-900">${cliente.ultimo_pedido ? new Date(cliente.ultimo_pedido).toLocaleDateString() : 'No tiene pedidos'}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Estadísticas -->
                <div>
                    <h4 class="font-bold text-lg text-gray-800 mb-3">Estadísticas de Compras</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-pink-600">${cliente.total_pedidos}</p>
                            <p class="text-sm text-gray-600">Total Pedidos</p>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-green-600">S/${parseFloat(cliente.total_gastado).toFixed(2)}</p>
                            <p class="text-sm text-gray-600">Total Gastado</p>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-blue-600">${cliente.total_pedidos > 0 ? 'S/' + (cliente.total_gastado / cliente.total_pedidos).toFixed(2) : 'S/0'}</p>
                            <p class="text-sm text-gray-600">Promedio por Pedido</p>
                        </div>
                    </div>
                </div>
        `;

        if (cliente.notas) {
            html += `
                <div>
                    <h4 class="font-bold text-lg text-gray-800 mb-3">Notas Adicionales</h4>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-gray-700">${escapeHtml(cliente.notas)}</p>
                    </div>
                </div>
            `;
        }

        html += `</div>`;

        document.getElementById('contenido-detalles-cliente').innerHTML = html;
        document.getElementById('modal-detalles-cliente').classList.add('active');
    }

    function limpiarFormularioCliente() {
        document.getElementById('form-nuevo-cliente').reset();
        document.getElementById('cliente-activo').checked = true;
    }

    function filtrarClientes() {
        const searchTerm = document.getElementById('buscador-clientes').value.toLowerCase();
        const filas = document.querySelectorAll('#tabla-clientes tr');
        let clientesVisibles = 0;

        filas.forEach(fila => {
            const textoFila = fila.textContent.toLowerCase();

            if (!searchTerm || textoFila.includes(searchTerm)) {
                fila.style.display = '';
                clientesVisibles++;

                // Resaltar texto de búsqueda
                if (searchTerm) {
                    const celdas = fila.querySelectorAll('td');
                    celdas.forEach(celda => {
                        const textoOriginal = celda.textContent;
                        const regex = new RegExp(`(${searchTerm})`, 'gi');
                        celda.innerHTML = textoOriginal.replace(regex, '<span class="search-highlight">$1</span>');
                    });
                }
            } else {
                fila.style.display = 'none';
            }
        });

        actualizarVistaFiltrosClientes(clientesVisibles);
    }

    function actualizarVistaFiltrosClientes(clientesVisibles) {
        document.getElementById('total-clientes-text').textContent = clientesVisibles;

        const sinResultados = document.getElementById('sin-resultados-clientes');
        if (clientesVisibles === 0) {
            sinResultados.classList.remove('hidden');
        } else {
            sinResultados.classList.add('hidden');
        }
    }

    async function cargarClientesFrecuentes() {
        try {
            const filtro = document.getElementById('filtro-frecuentes').value;
            const response = await fetch(`index.php?view=api-clientes-frecuentes&filtro=${filtro}`);
            const result = await response.json();

            if (result.success) {
                actualizarVistaClientesFrecuentes(result.data);
            } else {
                throw new Error(result.message || 'Error al cargar clientes frecuentes');
            }
        } catch (error) {
            console.error('Error al cargar clientes frecuentes:', error);
        }
    }

    function actualizarVistaClientesFrecuentes(clientes) {
        const grid = document.getElementById('grid-clientes-frecuentes');
        const sinClientes = document.getElementById('sin-clientes-frecuentes');

        if (clientes.length === 0) {
            grid.innerHTML = '';
            sinClientes.classList.remove('hidden');
            return;
        }

        sinClientes.classList.add('hidden');

        let html = '';
        clientes.forEach((cliente, index) => {
            const colores = [
                'from-yellow-100 to-amber-100 border-yellow-200',
                'from-gray-100 to-slate-100 border-gray-200',
                'from-amber-100 to-orange-100 border-amber-200',
                'from-blue-100 to-cyan-100 border-blue-200',
                'from-green-100 to-emerald-100 border-green-200'
            ];

            const iconos = ['fa-crown', 'fa-star', 'fa-medal', 'fa-trophy', 'fa-award'];
            const coloresIconos = ['text-yellow-600', 'text-gray-600', 'text-amber-600', 'text-blue-600', 'text-green-600'];
            const fondosIconos = ['bg-yellow-100', 'bg-gray-100', 'bg-amber-100', 'bg-blue-100', 'bg-green-100'];

            html += `
                <div class="cliente-card bg-gradient-to-br ${colores[index]} border ${colores[index].split(' ')[2]} rounded-lg p-6 fade-in">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full ${fondosIconos[index]} ${coloresIconos[index]} mr-3">
                                <i class="fas ${iconos[index]}"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800">${escapeHtml(cliente.nombres + ' ' + cliente.apellidos)}</h3>
                                <p class="text-sm text-gray-600">${escapeHtml(cliente.dni)}</p>
                            </div>
                        </div>
                        <span class="bg-white text-gray-700 text-xs font-bold px-2 py-1 rounded-full">#${index + 1}</span>
                    </div>
                    
                    <div class="space-y-3 mb-4">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total Pedidos:</span>
                            <span class="font-bold text-gray-800">${cliente.total_pedidos}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total Gastado:</span>
                            <span class="font-bold text-gray-800">S/${parseFloat(cliente.total_gastado).toFixed(2)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Último Pedido:</span>
                            <span class="text-sm text-gray-800">${cliente.ultimo_pedido ? new Date(cliente.ultimo_pedido).toLocaleDateString() : 'Nunca'}</span>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <button class="flex-1 bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 font-medium py-2 px-3 rounded transition-colors text-sm" onclick="verDetallesCliente(${cliente.id})">
                            <i class="fas fa-eye mr-1"></i>Detalles
                        </button>
                        <button class="flex-1 bg-pink-500 text-white hover:bg-pink-600 font-medium py-2 px-3 rounded transition-colors text-sm" onclick="editarCliente(${cliente.id})">
                            <i class="fas fa-edit mr-1"></i>Editar
                        </button>
                    </div>
                </div>
            `;
        });

        grid.innerHTML = html;
    }

    function exportarClientes() {
        // Simular exportación de clientes
        mostrarNotificacion('Exportando lista de clientes...', 'info');
        setTimeout(() => {
            mostrarNotificacion('Lista de clientes exportada exitosamente', 'success');
        }, 1500);
    }

    // Cerrar modales al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.classList.remove('active');
        }
    });
</script>