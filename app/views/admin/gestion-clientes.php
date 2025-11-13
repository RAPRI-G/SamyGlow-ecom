<?php
// app/views/admin/gestion-clientes.php

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

    /* Estilos para el menú desplegable de exportar */
    .export-menu {
        transform-origin: top right;
        transition: all 0.2s ease-in-out;
    }

    .export-menu-item {
        transition: all 0.2s ease;
    }

    .export-menu-item:hover {
        transform: translateX(2px);
    }

    /* Animación de entrada del menú */
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .menu-enter {
        animation: slideDown 0.2s ease-out;
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
                    <!-- En tu archivo gestion-clientes.php, reemplaza esta parte: -->
                    <div class="p-4 border-b flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold">Lista de Clientes</h2>
                            <p class="text-gray-600"><span id="total-clientes-text"><?= count($clientes) ?></span> clientes registrados en el sistema</p>
                        </div>
                        <!-- En tu archivo gestion-clientes.php, reemplaza el botón simple por este grupo de botones: -->
                        <div class="flex space-x-2">
                            <!-- Botón de Papelera -->
                            <button class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center relative" onclick="abrirPapeleraClientes()">
                                <i class="fas fa-trash mr-2"></i>Papelera
                                <span id="badge-papelera" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-2 py-1 hidden">0</span>
                            </button>
                            <div class="relative" id="dropdown-exportar">
                                <button class="bg-pink-500 hover:bg-pink-600 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center">
                                    <i class="fas fa-download mr-2"></i>Exportar
                                    <i class="fas fa-chevron-down ml-2 text-sm"></i>
                                </button>

                                <!-- Menú desplegable -->
                                <div class="absolute hidden right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50" id="menu-exportar">
                                    <div class="py-1">
                                        <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" onclick="exportarClientesCSV()">
                                            <i class="fas fa-file-csv mr-3 text-green-500"></i>
                                            Exportar a CSV
                                        </a>
                                        <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" onclick="exportarClientesExcel()">
                                            <i class="fas fa-file-excel mr-3 text-blue-500"></i>
                                            Exportar a Excel
                                        </a>
                                        <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" onclick="exportarClientesPDF()">
                                            <i class="fas fa-file-pdf mr-3 text-red-500"></i>
                                            Exportar a PDF
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
                    <!-- En la pestaña de Clientes Frecuentes, actualiza el header: -->
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
                            <button class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center" onclick="exportarClientesFrecuentes()">
                                <i class="fas fa-download mr-2"></i>Exportar
                            </button>
                        </div>
                    </div>

                    <!-- Y actualiza las tarjetas de estadísticas: -->
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
                                    <i class="fas fa-chart-line text-xl"></i>
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

                        <!-- QUITAMOS EL CHECKBOX DE ACTIVO Y LAS NOTAS -->
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
    // 🎯 INICIALIZACIÓN - ACTUALIZADA
    // =============================================

    document.addEventListener('DOMContentLoaded', function() {
        inicializarAppClientes();
        configurarEventListenersClientes();
        actualizarBadgePapelera();
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

        // Botón exportar con menú desplegable
        document.querySelector('#dropdown-exportar button').addEventListener('click', toggleMenuExportar);

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

        // Cerrar modales al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal')) {
                e.target.classList.remove('active');
            }
        });
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

        // Obtener valores del formulario (sin notas)
        const nombres = document.getElementById('cliente-nombres').value.trim();
        const apellidos = document.getElementById('cliente-apellidos').value.trim();
        const dni = document.getElementById('cliente-dni').value.trim();
        const correo = document.getElementById('cliente-correo').value.trim();
        const telefono = document.getElementById('cliente-telefono').value.trim();

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

            // ✅ URL CORREGIDA
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

        // Obtener valores del formulario (sin notas ni activo)
        const nombres = document.getElementById('edit-cliente-nombres').value;
        const apellidos = document.getElementById('edit-cliente-apellidos').value;
        const correo = document.getElementById('edit-cliente-correo').value;
        const telefono = document.getElementById('edit-cliente-telefono').value;

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

    // Reemplaza la función eliminarCliente existente por esta:
    // Función para mover a papelera - VERSIÓN CORREGIDA
    async function eliminarCliente(clienteId) {
        if (!confirm('¿Estás seguro de que quieres eliminar este cliente? Se moverá a la papelera y podrás restaurarlo después.')) {
            return;
        }

        try {
            mostrarLoading('Eliminando cliente...');

            const formData = new FormData();
            formData.append('id', clienteId);

            const response = await fetch('index.php?view=api-mover-papelera-cliente', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            ocultarLoading();

            if (result.success) {
                mostrarNotificacion('✅ Cliente movido a la papelera', 'success');

                // Actualizar la lista de clientes en tiempo real
                await actualizarListaClientes();

                // Actualizar el badge de papelera
                await actualizarBadgePapelera();

            } else {
                throw new Error(result.message || 'Error al eliminar cliente');
            }
        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al eliminar cliente: ' + error.message, 'error');
        }
    }

    async function verDetallesCliente(clienteId) {
        try {
            mostrarLoading('Cargando detalles...');

            // ✅ URL CORREGIDA
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
                        <span class="text-sm font-medium text-gray-600">Fecha Registro:</span>
                        <p class="text-gray-900">${new Date(cliente.created_at).toLocaleDateString()}</p>
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
                        <span class="text-sm font-medium text-gray-600">Total Pedidos:</span>
                        <p class="text-gray-900">${cliente.total_pedidos || 0}</p>
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
        </div>
    `;

        document.getElementById('contenido-detalles-cliente').innerHTML = html;
        document.getElementById('modal-detalles-cliente').classList.add('active');
    }

    function limpiarFormularioCliente() {
        document.getElementById('form-nuevo-cliente').reset();
    }

    // =============================================
    // 🔍 FILTRADO DE CLIENTES EN TIEMPO REAL
    // =============================================

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

    // =============================================
    // ⭐ FUNCIONALIDAD DE CLIENTES FRECUENTES
    // =============================================

    async function cargarClientesFrecuentes() {
        try {
            const filtro = document.getElementById('filtro-frecuentes').value;
            const response = await fetch(`index.php?view=api-clientes-frecuentes&filtro=${filtro}`);
            const result = await response.json();

            if (result.success) {
                actualizarVistaClientesFrecuentes(result.data, result.estadisticas || {});
                actualizarEstadisticasFrecuentes(result.estadisticas || {});
            } else {
                throw new Error(result.message || 'Error al cargar clientes frecuentes');
            }
        } catch (error) {
            console.error('Error al cargar clientes frecuentes:', error);
            mostrarNotificacion('❌ Error al cargar clientes frecuentes', 'error');
        }
    }


    // =============================================
    // 🛠️ FUNCIONES UTILITARIAS - AGREGA ESTAS
    // =============================================

    function empty(value) {
        return value === null || value === undefined || value === '';
    }

    function isNumeric(value) {
        return /^\d+$/.test(value);
    }

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
    // Función para animar la actualización de elementos
    function animarActualizacion(elemento) {
        if (elemento) {
            elemento.style.transform = 'scale(0.95)';
            setTimeout(() => {
                elemento.style.transform = 'scale(1)';
                elemento.style.transition = 'transform 0.3s ease';
            }, 150);
        }
    }

    function actualizarVistaClientesFrecuentes(clientes, estadisticas) {
        const grid = document.getElementById('grid-clientes-frecuentes');
        const sinClientes = document.getElementById('sin-clientes-frecuentes');

        if (!grid || !sinClientes) return;

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

            // Calcular porcentaje para la barra de progreso
            const porcentajePedidos = estadisticas.max_pedidos > 0 ?
                (cliente.total_pedidos / estadisticas.max_pedidos) * 100 : 0;

            const porcentajeGastado = estadisticas.max_gastado > 0 ?
                (cliente.total_gastado / estadisticas.max_gastado) * 100 : 0;

            html += `
            <div class="cliente-card bg-gradient-to-br ${colores[index]} border rounded-lg p-6 fade-in transform hover:scale-105 transition-all duration-300">
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
                    <span class="bg-white text-gray-700 text-xs font-bold px-2 py-1 rounded-full shadow-sm">#${index + 1}</span>
                </div>
                
                <!-- Barras de progreso -->
                <div class="space-y-2 mb-4">
                    <div>
                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                            <span>Pedidos</span>
                            <span>${cliente.total_pedidos}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-500 h-2 rounded-full" style="width: ${Math.min(porcentajePedidos, 100)}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                            <span>Total Gastado</span>
                            <span>S/${parseFloat(cliente.total_gastado).toFixed(2)}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: ${Math.min(porcentajeGastado, 100)}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="space-y-2 mb-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Pedidos:</span>
                        <span class="font-bold text-gray-800">${cliente.total_pedidos}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Gastado:</span>
                        <span class="font-bold text-gray-800">S/ ${parseFloat(cliente.total_gastado).toFixed(2)}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Promedio/Pedido:</span>
                        <span class="font-bold text-gray-800">S/ ${cliente.total_pedidos > 0 ? (cliente.total_gastado / cliente.total_pedidos).toFixed(2) : '0.00'}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Último Pedido:</span>
                        <span class="text-gray-800 text-xs">${cliente.ultimo_pedido ? new Date(cliente.ultimo_pedido).toLocaleDateString() : 'Nunca'}</span>
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

    function actualizarEstadisticasFrecuentes(estadisticas) {
        // Actualizar las tarjetas de estadísticas
        if (document.getElementById('cliente-top-pedidos')) {
            document.getElementById('cliente-top-pedidos').textContent = estadisticas.max_pedidos;
        }
        if (document.getElementById('cliente-top-gastado')) {
            document.getElementById('cliente-top-gastado').textContent = `S/ ${parseFloat(estadisticas.max_gastado).toFixed(2)}`;
        }
        if (document.getElementById('promedio-pedidos')) {
            document.getElementById('promedio-pedidos').textContent = parseFloat(estadisticas.promedio_pedidos).toFixed(1);
        }

        // Actualizar badge de clientes frecuentes
        if (document.getElementById('frecuentes-badge')) {
            document.getElementById('frecuentes-badge').textContent = estadisticas.total_frecuentes || 0;
        }
    }

    // =============================================
    // 📊 FUNCIONES ADICIONALES PARA CLIENTES FRECUENTES
    // =============================================

    // Función para ver el ranking completo
    function verRankingCompleto() {
        // Aquí puedes implementar una vista expandida del ranking
        mostrarNotificacion('📊 Función de ranking completo en desarrollo', 'info');
    }

    // Función para exportar clientes frecuentes
    async function exportarClientesFrecuentes() {
        try {
            mostrarLoading('Exportando clientes frecuentes...');

            const response = await fetch('index.php?view=api-clientes-frecuentes&filtro=pedidos');
            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message || 'Error al obtener clientes frecuentes');
            }

            const clientes = result.data;

            // Crear contenido CSV
            let csvContent = "Posición,DNI,Nombres,Apellidos,Correo,Teléfono,Total Pedidos,Total Gastado,Promedio por Pedido,Último Pedido\n";

            clientes.forEach((cliente, index) => {
                const promedio = cliente.total_pedidos > 0 ? (cliente.total_gastado / cliente.total_pedidos) : 0;
                const fila = [
                    `#${index + 1}`,
                    `"${cliente.dni}"`,
                    `"${cliente.nombres}"`,
                    `"${cliente.apellidos}"`,
                    `"${cliente.correo}"`,
                    `"${cliente.telefono || 'N/A'}"`,
                    cliente.total_pedidos,
                    `S/ ${parseFloat(cliente.total_gastado).toFixed(2)}`,
                    `S/ ${parseFloat(promedio).toFixed(2)}`,
                    `"${cliente.ultimo_pedido ? new Date(cliente.ultimo_pedido).toLocaleDateString() : 'Nunca'}"`
                ];
                csvContent += fila.join(',') + '\n';
            });

            // Crear y descargar archivo
            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);

            link.setAttribute('href', url);
            link.setAttribute('download', `clientes_frecuentes_samyglow_${new Date().toISOString().split('T')[0]}.csv`);

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);

            ocultarLoading();
            mostrarNotificacion('✅ Clientes frecuentes exportados exitosamente', 'success');

        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al exportar clientes frecuentes: ' + error.message, 'error');
        }
    }


    // =============================================
    // 📊 FUNCIÓN DE EXPORTAR CLIENTES
    // =============================================

    async function exportarClientes() {
        try {
            mostrarLoading('Generando archivo de exportación...');

            // Obtener todos los clientes
            const response = await fetch('index.php?view=api-listar-clientes');
            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message || 'Error al obtener datos para exportar');
            }

            const clientes = result.data;

            // Crear contenido CSV
            let csvContent = "DNI,Nombres,Apellidos,Correo,Teléfono,Total Pedidos,Total Gastado,Último Pedido\n";

            clientes.forEach(cliente => {
                const fila = [
                    `"${cliente.dni}"`,
                    `"${cliente.nombres}"`,
                    `"${cliente.apellidos}"`,
                    `"${cliente.correo}"`,
                    `"${cliente.telefono || ''}"`,
                    cliente.total_pedidos || 0,
                    `S/ ${parseFloat(cliente.total_gastado || 0).toFixed(2)}`,
                    `"${cliente.ultimo_pedido ? new Date(cliente.ultimo_pedido).toLocaleDateString() : 'Nunca'}"`
                ];
                csvContent += fila.join(',') + '\n';
            });

            // Crear y descargar archivo
            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);

            link.setAttribute('href', url);
            link.setAttribute('download', `clientes_samyglow_${new Date().toISOString().split('T')[0]}.csv`);
            link.style.visibility = 'hidden';

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            ocultarLoading();
            mostrarNotificacion('✅ Archivo exportado exitosamente', 'success');

        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al exportar: ' + error.message, 'error');
            console.error('Error al exportar:', error);
        }
    }

    // =============================================
    // 📊 FUNCIÓN ALTERNATIVA - EXPORTAR A EXCEL
    // =============================================

    async function exportarClientesExcel() {
        try {
            mostrarLoading('Generando archivo Excel...');

            const response = await fetch('index.php?view=api-listar-clientes');
            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message || 'Error al obtener datos para exportar');
            }

            const clientes = result.data;

            // Crear contenido HTML para Excel
            let htmlContent = `
            <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
            <head>
                <meta charset="UTF-8">
                <title>Clientes SamyGlow</title>
                <!--[if gte mso 9]>
                <xml>
                    <x:ExcelWorkbook>
                        <x:ExcelWorksheets>
                            <x:ExcelWorksheet>
                                <x:Name>Clientes</x:Name>
                                <x:WorksheetOptions>
                                    <x:DisplayGridlines/>
                                </x:WorksheetOptions>
                            </x:ExcelWorksheet>
                        </x:ExcelWorksheets>
                    </x:ExcelWorkbook>
                </xml>
                <![endif]-->
                <style>
                    table { border-collapse: collapse; width: 100%; }
                    th { background-color: #f472b6; color: white; font-weight: bold; padding: 8px; border: 1px solid #ddd; }
                    td { padding: 8px; border: 1px solid #ddd; }
                    .number { text-align: right; }
                </style>
            </head>
            <body>
                <h1>Lista de Clientes - SamyGlow</h1>
                <p>Generado el: ${new Date().toLocaleDateString()}</p>
                <table>
                    <thead>
                        <tr>
                            <th>DNI</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Total Pedidos</th>
                            <th>Total Gastado</th>
                            <th>Último Pedido</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

            clientes.forEach(cliente => {
                htmlContent += `
                <tr>
                    <td>${cliente.dni}</td>
                    <td>${cliente.nombres}</td>
                    <td>${cliente.apellidos}</td>
                    <td>${cliente.correo}</td>
                    <td>${cliente.telefono || ''}</td>
                    <td class="number">${cliente.total_pedidos || 0}</td>
                    <td class="number">S/ ${parseFloat(cliente.total_gastado || 0).toFixed(2)}</td>
                    <td>${cliente.ultimo_pedido ? new Date(cliente.ultimo_pedido).toLocaleDateString() : 'Nunca'}</td>
                </tr>
            `;
            });

            htmlContent += `
                    </tbody>
                </table>
            </body>
            </html>
        `;

            // Crear y descargar archivo
            const blob = new Blob([htmlContent], {
                type: 'application/vnd.ms-excel'
            });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);

            link.setAttribute('href', url);
            link.setAttribute('download', `clientes_samyglow_${new Date().toISOString().split('T')[0]}.xls`);
            link.style.visibility = 'hidden';

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            ocultarLoading();
            mostrarNotificacion('✅ Archivo Excel exportado exitosamente', 'success');

        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al exportar: ' + error.message, 'error');
            console.error('Error al exportar Excel:', error);
        }
    }

    // =============================================
    // 🔧 ACTUALIZA EL BOTÓN EXPORTAR PARA DAR OPCIONES
    // =============================================

    function exportarClientes() {
        // Crear modal de opciones de exportación
        const modalHtml = `
        <div class="modal active" id="modal-exportar">
            <div class="modal-content" style="max-width: 400px;">
                <div class="p-4 border-b flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">Exportar Clientes</h3>
                    <button class="text-gray-500 hover:text-gray-700" onclick="cerrarModalExportar()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 mb-4">Selecciona el formato de exportación:</p>
                    <div class="space-y-3">
                        <button class="w-full bg-green-500 hover:bg-green-600 text-white font-medium py-3 px-4 rounded-lg transition-colors flex items-center justify-center" onclick="exportarClientesCSV()">
                            <i class="fas fa-file-csv mr-2"></i>Exportar a CSV
                        </button>
                        <button class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-3 px-4 rounded-lg transition-colors flex items-center justify-center" onclick="exportarClientesExcel()">
                            <i class="fas fa-file-excel mr-2"></i>Exportar a Excel
                        </button>
                        <button class="w-full bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors" onclick="cerrarModalExportar()">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

        // Remover modal existente
        const modalExistente = document.getElementById('modal-exportar');
        if (modalExistente) {
            modalExistente.remove();
        }

        // Agregar nuevo modal
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    function cerrarModalExportar() {
        const modal = document.getElementById('modal-exportar');
        if (modal) {
            modal.remove();
        }
    }

    // Función específica para CSV
    async function exportarClientesCSV() {
        try {
            mostrarLoading('Generando archivo CSV...');
            cerrarModalExportar();

            const response = await fetch('index.php?view=api-listar-clientes');
            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message || 'Error al obtener datos para exportar');
            }

            const clientes = result.data;

            // Crear contenido CSV
            let csvContent = "DNI,Nombres,Apellidos,Correo,Teléfono,Total Pedidos,Total Gastado,Último Pedido\n";

            clientes.forEach(cliente => {
                const fila = [
                    `"${cliente.dni}"`,
                    `"${cliente.nombres}"`,
                    `"${cliente.apellidos}"`,
                    `"${cliente.correo}"`,
                    `"${cliente.telefono || ''}"`,
                    cliente.total_pedidos || 0,
                    `S/ ${parseFloat(cliente.total_gastado || 0).toFixed(2)}`,
                    `"${cliente.ultimo_pedido ? new Date(cliente.ultimo_pedido).toLocaleDateString() : 'Nunca'}"`
                ];
                csvContent += fila.join(',') + '\n';
            });

            // Crear y descargar archivo
            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);

            link.setAttribute('href', url);
            link.setAttribute('download', `clientes_samyglow_${new Date().toISOString().split('T')[0]}.csv`);
            link.style.visibility = 'hidden';

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            ocultarLoading();
            mostrarNotificacion('✅ Archivo CSV exportado exitosamente', 'success');

        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al exportar CSV: ' + error.message, 'error');
            console.error('Error al exportar CSV:', error);
        }
    }

    // =============================================
    // 📊 FUNCIONES DE EXPORTACIÓN CON MENÚ DESPLEGABLE
    // =============================================

    // Función para mostrar/ocultar el menú
    function toggleMenuExportar() {
        const menu = document.getElementById('menu-exportar');
        const dropdown = document.getElementById('dropdown-exportar');

        if (menu.classList.contains('hidden')) {
            // Mostrar menú
            menu.classList.remove('hidden');
            menu.classList.add('menu-enter');

            // Cerrar menú al hacer clic fuera
            setTimeout(() => {
                document.addEventListener('click', cerrarMenuAlClicExterno);
            }, 10);
        } else {
            cerrarMenuExportar();
        }
    }

    function cerrarMenuExportar() {
        const menu = document.getElementById('menu-exportar');
        menu.classList.add('hidden');
        menu.classList.remove('menu-enter');
        document.removeEventListener('click', cerrarMenuAlClicExterno);
    }

    function cerrarMenuAlClicExterno(event) {
        const dropdown = document.getElementById('dropdown-exportar');
        const menu = document.getElementById('menu-exportar');

        if (!dropdown.contains(event.target)) {
            cerrarMenuExportar();
        }
    }

    // Función para exportar a CSV
    async function exportarClientesCSV() {
        try {
            mostrarLoading('Generando archivo CSV...');
            cerrarMenuExportar();

            const response = await fetch('index.php?view=api-listar-clientes');
            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message || 'Error al obtener datos para exportar');
            }

            const clientes = result.data;

            // Crear contenido CSV
            let csvContent = "DNI,Nombres,Apellidos,Correo,Teléfono,Total Pedidos,Total Gastado,Último Pedido,Fecha Exportación\n";

            clientes.forEach(cliente => {
                const fila = [
                    `"${cliente.dni}"`,
                    `"${cliente.nombres}"`,
                    `"${cliente.apellidos}"`,
                    `"${cliente.correo}"`,
                    `"${cliente.telefono || 'N/A'}"`,
                    cliente.total_pedidos || 0,
                    `S/ ${parseFloat(cliente.total_gastado || 0).toFixed(2)}`,
                    `"${cliente.ultimo_pedido ? new Date(cliente.ultimo_pedido).toLocaleDateString() : 'Nunca'}"`,
                    `"${new Date().toLocaleDateString()}"`
                ];
                csvContent += fila.join(',') + '\n';
            });

            // Crear y descargar archivo
            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);

            link.setAttribute('href', url);
            link.setAttribute('download', `clientes_samyglow_${new Date().toISOString().split('T')[0]}.csv`);

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);

            ocultarLoading();
            mostrarNotificacion('✅ Archivo CSV exportado exitosamente', 'success');

        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al exportar CSV: ' + error.message, 'error');
            console.error('Error al exportar CSV:', error);
        }
    }
    // Función para exportar a Excel
    async function exportarClientesExcel() {
        try {
            mostrarLoading('Generando archivo Excel...');
            cerrarMenuExportar();

            const response = await fetch('index.php?view=api-listar-clientes');
            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message || 'Error al obtener datos para exportar');
            }

            const clientes = result.data;

            // Crear contenido HTML para Excel
            let htmlContent = `
            <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
            <head>
                <meta charset="UTF-8">
                <title>Clientes SamyGlow</title>
                <!--[if gte mso 9]>
                <xml>
                    <x:ExcelWorkbook>
                        <x:ExcelWorksheets>
                            <x:ExcelWorksheet>
                                <x:Name>Clientes</x:Name>
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
                    th { background-color: #f472b6; color: white; font-weight: bold; padding: 10px; border: 1px solid #ddd; text-align: left; }
                    td { padding: 8px 10px; border: 1px solid #ddd; }
                    .number { text-align: right; }
                    .header { background-color: #f8f9fa; padding: 15px; margin-bottom: 20px; }
                    .title { color: #f472b6; font-size: 18px; font-weight: bold; }
                </style>
            </head>
            <body>
                <div class="header">
                    <div class="title">Lista de Clientes - SamyGlow</div>
                    <div>Generado el: ${new Date().toLocaleDateString()}</div>
                    <div>Total de clientes: ${clientes.length}</div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>DNI</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Total Pedidos</th>
                            <th>Total Gastado</th>
                            <th>Último Pedido</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

            clientes.forEach(cliente => {
                htmlContent += `
                <tr>
                    <td>${cliente.dni}</td>
                    <td>${cliente.nombres}</td>
                    <td>${cliente.apellidos}</td>
                    <td>${cliente.correo}</td>
                    <td>${cliente.telefono || 'N/A'}</td>
                    <td class="number">${cliente.total_pedidos || 0}</td>
                    <td class="number">S/ ${parseFloat(cliente.total_gastado || 0).toFixed(2)}</td>
                    <td>${cliente.ultimo_pedido ? new Date(cliente.ultimo_pedido).toLocaleDateString() : 'Nunca'}</td>
                </tr>
            `;
            });

            htmlContent += `
                    </tbody>
                </table>
            </body>
            </html>
        `;

            // Crear y descargar archivo
            const blob = new Blob([htmlContent], {
                type: 'application/vnd.ms-excel'
            });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);

            link.setAttribute('href', url);
            link.setAttribute('download', `clientes_samyglow_${new Date().toISOString().split('T')[0]}.xls`);

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);

            ocultarLoading();
            mostrarNotificacion('✅ Archivo Excel exportado exitosamente', 'success');

        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al exportar Excel: ' + error.message, 'error');
            console.error('Error al exportar Excel:', error);
        }
    }

    // Función para exportar a PDF (versión simple usando print)
    async function exportarClientesPDF() {
        try {
            cerrarMenuExportar();
            mostrarLoading('Preparando documento para PDF...');

            const response = await fetch('index.php?view=api-listar-clientes');
            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message || 'Error al obtener datos para exportar');
            }

            const clientes = result.data;

            // Crear una ventana de impresión
            const ventana = window.open('', '_blank');
            ventana.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Clientes SamyGlow - ${new Date().toLocaleDateString()}</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #f472b6; padding-bottom: 20px; }
                    .title { color: #f472b6; font-size: 24px; font-weight: bold; margin-bottom: 10px; }
                    .subtitle { color: #666; font-size: 14px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th { background-color: #f472b6; color: white; padding: 10px; text-align: left; border: 1px solid #ddd; }
                    td { padding: 8px 10px; border: 1px solid #ddd; }
                    .number { text-align: right; }
                    .footer { margin-top: 30px; text-align: center; color: #666; font-size: 12px; }
                    @media print {
                        body { margin: 0; }
                        .no-print { display: none; }
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <div class="title">Lista de Clientes - SamyGlow</div>
                    <div class="subtitle">Generado el: ${new Date().toLocaleDateString()} | Total de clientes: ${clientes.length}</div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>DNI</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Total Pedidos</th>
                            <th>Total Gastado</th>
                            <th>Último Pedido</th>
                        </tr>
                    </thead>
                    <tbody>
        `);

            clientes.forEach(cliente => {
                ventana.document.write(`
                <tr>
                    <td>${cliente.dni}</td>
                    <td>${cliente.nombres}</td>
                    <td>${cliente.apellidos}</td>
                    <td>${cliente.correo}</td>
                    <td>${cliente.telefono || 'N/A'}</td>
                    <td class="number">${cliente.total_pedidos || 0}</td>
                    <td class="number">S/ ${parseFloat(cliente.total_gastado || 0).toFixed(2)}</td>
                    <td>${cliente.ultimo_pedido ? new Date(cliente.ultimo_pedido).toLocaleDateString() : 'Nunca'}</td>
                </tr>
            `);
            });

            ventana.document.write(`
                    </tbody>
                </table>
                <div class="footer">
                    Documento generado por Sistema SamyGlow
                </div>
                <div class="no-print" style="margin-top: 20px; text-align: center;">
                    <button onclick="window.print()" style="background: #f472b6; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                        Imprimir o Guardar como PDF
                    </button>
                </div>
            </body>
            </html>
        `);

            ventana.document.close();
            ocultarLoading();
            mostrarNotificacion('✅ Documento listo para imprimir/guardar como PDF', 'success');

        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al generar PDF: ' + error.message, 'error');
            console.error('Error al exportar PDF:', error);
        }
    }
    // Cerrar modales al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.classList.remove('active');
        }
    });
    // =============================================
    // 🗑️ FUNCIONALIDAD DE PAPELERA
    // =============================================

    // Función para mover cliente a papelera
    async function moverPapeleraCliente(clienteId) {
        if (!confirm('¿Estás seguro de que quieres mover este cliente a la papelera? Podrás restaurarlo después.')) {
            return;
        }

        try {
            mostrarLoading('Moviendo a papelera...');

            const formData = new FormData();
            formData.append('id', clienteId);

            const response = await fetch('index.php?view=api-mover-papelera-cliente', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            ocultarLoading();

            if (result.success) {
                mostrarNotificacion('✅ Cliente movido a la papelera', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(result.message || 'Error al mover a papelera');
            }
        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error: ' + error.message, 'error');
        }
    }

    // Función para abrir modal de papelera
    // Función para abrir modal de papelera - VERSIÓN MEJORADA
    async function abrirPapeleraClientes() {
        try {
            mostrarLoading('Cargando papelera...');

            const response = await fetch('index.php?view=api-listar-papelera-clientes');
            const result = await response.json();
            ocultarLoading();

            if (result.success) {
                mostrarModalPapelera(result.data);
            } else {
                throw new Error(result.message || 'Error al cargar papelera');
            }
        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al cargar papelera: ' + error.message, 'error');
        }
    }

    // Función para mostrar modal de papelera
    function mostrarModalPapelera(clientes) {
        let html = `
        <div class="modal active" id="modal-papelera">
            <div class="modal-content" style="max-width: 900px; max-height: 80vh;">
                <div class="p-4 border-b flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Papelera de Clientes</h3>
                        <p class="text-gray-600">${clientes.length} clientes en papelera</p>
                    </div>
                    <div class="flex space-x-2">
                        <button class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded transition-colors" onclick="vaciarPapeleraClientes()">
                            <i class="fas fa-trash mr-2"></i>Vaciar Papelera
                        </button>
                        <button class="text-gray-500 hover:text-gray-700" onclick="cerrarModalPapelera()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="p-4 overflow-y-auto">
    `;

        if (clientes.length === 0) {
            html += `
            <div class="text-center py-12">
                <i class="fas fa-trash-alt text-gray-300 text-5xl mb-4"></i>
                <h3 class="text-xl font-medium text-gray-700">Papelera vacía</h3>
                <p class="text-gray-500 mt-2">No hay clientes en la papelera</p>
            </div>
        `;
        } else {
            html += `
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DNI</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Eliminación</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
        `;

            clientes.forEach(cliente => {
                html += `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">${escapeHtml(cliente.nombres + ' ' + cliente.apellidos)}</div>
                                <div class="text-sm text-gray-500">${cliente.total_pedidos || 0} pedidos</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${escapeHtml(cliente.dni)}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${escapeHtml(cliente.correo)}</div>
                        <div class="text-sm text-gray-500">${escapeHtml(cliente.telefono || 'N/A')}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${cliente.fecha_eliminado ? new Date(cliente.fecha_eliminado).toLocaleDateString() : 'N/A'}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button class="text-green-600 hover:text-green-900 mr-3" onclick="restaurarCliente(${cliente.id})">
                            <i class="fas fa-undo mr-1"></i>Restaurar
                        </button>
                        <button class="text-red-600 hover:text-red-900" onclick="eliminarPermanentementeCliente(${cliente.id})">
                            <i class="fas fa-trash mr-1"></i>Eliminar
                        </button>
                    </td>
                </tr>
            `;
            });

            html += `
                    </tbody>
                </table>
            </div>
        `;
        }

        html += `
                </div>
            </div>
        </div>
    `;

        // Remover modal existente
        const modalExistente = document.getElementById('modal-papelera');
        if (modalExistente) {
            modalExistente.remove();
        }

        // Agregar nuevo modal
        document.body.insertAdjacentHTML('beforeend', html);
    }

    // Función para restaurar cliente - VERSIÓN CORREGIDA
    async function restaurarCliente(clienteId) {
        if (!confirm('¿Restaurar este cliente?')) {
            return;
        }

        try {
            mostrarLoading('Restaurando cliente...');

            const formData = new FormData();
            formData.append('id', clienteId);

            const response = await fetch('index.php?view=api-restaurar-cliente', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            ocultarLoading();

            if (result.success) {
                mostrarNotificacion('✅ Cliente restaurado exitosamente', 'success');

                // Cerrar el modal de papelera
                cerrarModalPapelera();

                // Actualizar la lista de clientes en la pestaña activa
                await actualizarListaClientes();

                // Actualizar el badge de papelera
                await actualizarBadgePapelera();

            } else {
                throw new Error(result.message || 'Error al restaurar cliente');
            }
        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error: ' + error.message, 'error');
        }
    }

    // =============================================
    // 🔄 FUNCIONES DE ACTUALIZACIÓN EN TIEMPO REAL
    // =============================================

    async function actualizarListaClientes() {
        try {
            // Obtener la pestaña activa
            const tabActiva = document.querySelector('.tab-button.active');
            if (!tabActiva) return;

            const tabId = tabActiva.getAttribute('data-tab');

            switch (tabId) {
                case 'lista-clientes':
                    await actualizarListaPrincipal();
                    break;
                case 'clientes-frecuentes':
                    await actualizarClientesFrecuentes();
                    break;
            }
        } catch (error) {
            console.error('Error al actualizar lista:', error);
        }
    }

    async function actualizarListaPrincipal() {
        try {
            const response = await fetch('index.php?view=api-listar-clientes');
            const result = await response.json();

            if (result.success) {
                // Actualizar la tabla de clientes
                actualizarTablaClientes(result.data);

                // Actualizar contadores
                document.getElementById('total-clientes-text').textContent = result.total;
                document.getElementById('total-clientes-badge').textContent = result.total;
            }
        } catch (error) {
            console.error('Error al actualizar lista principal:', error);
        }
    }

    async function actualizarClientesFrecuentes() {
        try {
            const filtro = document.getElementById('filtro-frecuentes').value;
            const response = await fetch(`index.php?view=api-clientes-frecuentes&filtro=${filtro}`);
            const result = await response.json();

            if (result.success) {
                actualizarVistaClientesFrecuentes(result.data, result.estadisticas);
                actualizarEstadisticasFrecuentes(result.estadisticas);
            }
        } catch (error) {
            console.error('Error al actualizar clientes frecuentes:', error);
        }
    }

    function actualizarTablaClientes(clientes) {
        const tbody = document.getElementById('tabla-clientes');
        const sinResultados = document.getElementById('sin-resultados-clientes');

        if (clientes.length === 0) {
            tbody.innerHTML = '';
            sinResultados.classList.remove('hidden');
            return;
        }

        sinResultados.classList.add('hidden');

        let html = '';
        clientes.forEach(cliente => {
            html += `
            <tr class="fade-in">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-purple-600"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">${escapeHtml(cliente.nombres + ' ' + cliente.apellidos)}</div>
                            <div class="text-sm text-gray-500">Registrado: ${new Date(cliente.created_at).toLocaleDateString()}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${escapeHtml(cliente.dni)}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${escapeHtml(cliente.correo)}</div>
                    <div class="text-sm text-gray-500">${escapeHtml(cliente.telefono)}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${cliente.total_pedidos} pedidos</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">S/ ${parseFloat(cliente.total_gastado).toFixed(2)}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        Activo
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button class="text-blue-600 hover:text-blue-900 mr-3" onclick="verDetallesCliente(${cliente.id})">
                        <i class="fas fa-eye mr-1"></i>Ver
                    </button>
                    <button class="text-green-600 hover:text-green-900 mr-3" onclick="editarCliente(${cliente.id})">
                        <i class="fas fa-edit mr-1"></i>Editar
                    </button>
                    <button class="text-red-600 hover:text-red-900" onclick="eliminarCliente(${cliente.id})">
                        <i class="fas fa-trash mr-1"></i>Eliminar
                    </button>
                </td>
            </tr>
        `;
        });

        tbody.innerHTML = html;
    }

    // Función para eliminar permanentemente - VERSIÓN CORREGIDA
    async function eliminarPermanentementeCliente(clienteId) {
        if (!confirm('¿Eliminar permanentemente este cliente? Esta acción no se puede deshacer.')) {
            return;
        }

        try {
            mostrarLoading('Eliminando permanentemente...');

            const formData = new FormData();
            formData.append('id', clienteId);

            const response = await fetch('index.php?view=api-eliminar-permanentemente-cliente', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            ocultarLoading();

            if (result.success) {
                mostrarNotificacion('✅ Cliente eliminado permanentemente', 'success');

                // Solo recargar el modal de papelera (no la página completa)
                await abrirPapeleraClientes();

                // Actualizar el badge de papelera
                await actualizarBadgePapelera();

            } else {
                throw new Error(result.message || 'Error al eliminar permanentemente');
            }
        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error: ' + error.message, 'error');
        }
    }

    // Función para vaciar papelera - VERSIÓN CORREGIDA
    async function vaciarPapeleraClientes() {
        if (!confirm('¿Vaciar toda la papelera? Esta acción eliminará permanentemente todos los clientes y no se puede deshacer.')) {
            return;
        }

        try {
            mostrarLoading('Vaciando papelera...');

            const response = await fetch('index.php?view=api-vaciar-papelera-clientes', {
                method: 'POST'
            });

            const result = await response.json();
            ocultarLoading();

            if (result.success) {
                mostrarNotificacion('✅ Papelera vaciada exitosamente', 'success');

                // Cerrar el modal
                cerrarModalPapelera();

                // Actualizar el badge de papelera
                await actualizarBadgePapelera();

            } else {
                throw new Error(result.message || 'Error al vaciar papelera');
            }
        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error: ' + error.message, 'error');
        }
    }

    // Función para cerrar modal de papelera
    function cerrarModalPapelera() {
        const modal = document.getElementById('modal-papelera');
        if (modal) {
            modal.remove();
        }
    }

    // Función para actualizar el badge de papelera
    async function actualizarBadgePapelera() {
        try {
            const response = await fetch('index.php?view=api-contar-papelera-clientes');
            const result = await response.json();

            if (result.success) {
                const badge = document.getElementById('badge-papelera');
                if (badge) {
                    badge.textContent = result.total;
                    if (result.total > 0) {
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
            }
        } catch (error) {
            console.error('Error al actualizar badge de papelera:', error);
        }
    }
</script>