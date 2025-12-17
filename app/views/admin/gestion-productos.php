<?php
// app/views/admin/gestion-productos.php

// Los datos ya vienen del controlador: $productos, $categorias, $estadisticas, $productosStockBajo
?>

<main class="flex-1 overflow-y-auto p-6">
    <!-- Sección de Gestión de Productos -->
    <div class="content-section active" id="productos">
        <!-- Tabs de navegación -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="flex border-b overflow-x-auto">
                <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-600 hover:bg-gray-50 active" data-tab="lista-productos">
                    <i class="fas fa-list mr-2"></i>Todos los Productos
                    <span class="ml-2 bg-pink-500 text-white text-xs rounded-full px-2 py-1" id="total-productos-badge"><?= count($productos) ?></span>
                </button>
                <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-600 hover:bg-gray-50" data-tab="categorias">
                    <i class="fas fa-tags mr-2"></i>Categorías
                    <span class="ml-2 bg-blue-500 text-white text-xs rounded-full px-2 py-1"><?= count($categorias) ?></span>
                </button>
                <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-600 hover:bg-gray-50" data-tab="inventario">
                    <i class="fas fa-boxes mr-2"></i>Control de Inventario
                </button>
                <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-600 hover:bg-gray-50" data-tab="stock-bajo">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Stock Bajo
                    <span class="ml-2 bg-red-500 text-white text-xs rounded-full px-2 py-1" id="stock-bajo-badge"><?= count($productosStockBajo) ?></span>
                </button>
                <!-- En la sección de Tabs de navegación, después del botón "Agregar Producto" -->
                <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-600 hover:bg-gray-50" data-tab="papelera">
                    <i class="fas fa-trash-alt mr-2"></i>Papelera
                    <span class="ml-2 bg-gray-500 text-white text-xs rounded-full px-2 py-1" id="papelera-badge">0</span>
                </button>
                <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-600 hover:bg-gray-50" data-tab="nuevo-producto">
                    <i class="fas fa-plus mr-2"></i>Agregar Producto
                </button>
            </div>
        </div>

        <!-- Contenido de las pestañas -->
        <div id="tab-content">
            <!-- Pestaña: Todos los Productos -->
            <div class="tab-panel active" id="lista-productos-panel">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-4 border-b flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold">Todos los Productos</h2>
                            <p class="text-gray-600"><span id="total-productos-text"><?= count($productos) ?></span> productos registrados en el sistema</p>
                        </div>
                        <div class="flex space-x-2">
                            <select id="filtro-categoria" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500 w-64">
                                <option value="">Todas las categorías</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= $categoria['id'] ?>"><?= htmlspecialchars($categoria['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="tabla-productos">
                                <?php foreach ($productos as $producto): ?>
                                    <tr class="fade-in">

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-pink-100 rounded-lg flex items-center justify-center">
                                                    <?php
                                                    // SOLUCIÓN TEMPORAL: Forzar la URL correcta
                                                    if (!empty($producto['imagen'])) {
                                                        // Extraer solo el nombre del archivo
                                                        $nombreArchivo = basename($producto['imagen']);
                                                        // Construir URL MANUALMENTE
                                                        $urlImagen = '/SamyGlow-ecom/uploads/productos/' . $nombreArchivo;
                                                        $rutaFisica = $_SERVER['DOCUMENT_ROOT'] . $urlImagen;

                                                        if (file_exists($rutaFisica)) {
                                                            echo '<img src="' . htmlspecialchars($urlImagen) . '" 
                          alt="' . htmlspecialchars($producto['nombre']) . '"
                          class="h-8 w-8 rounded-lg object-cover">';
                                                        } else {
                                                            echo '<i class="fas fa-cube text-pink-600"></i>';
                                                        }
                                                    } else {
                                                        echo '<i class="fas fa-cube text-pink-600"></i>';
                                                    }
                                                    ?>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($producto['nombre']) ?></div>
                                                    <div class="text-sm text-gray-500 truncate max-w-xs"><?= htmlspecialchars($producto['descripcion']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php
                                            $categoriaClass = '';
                                            if ($producto['categoria_id'] == 1) $categoriaClass = 'categoria-fragancias';
                                            elseif ($producto['categoria_id'] == 2) $categoriaClass = 'categoria-cremas';
                                            else $categoriaClass = 'categoria-splash';
                                            ?>
                                            <span class="categoria-badge <?= $categoriaClass ?>" data-categoria-id="<?= $producto['categoria_id'] ?>"><?= htmlspecialchars($producto['categoria_nombre']) ?></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">S/ <?= number_format($producto['precio'], 2) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $producto['stock'] ?> unidades</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if ($producto['activo']): ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Activo</span>
                                            <?php else: ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button class="text-blue-600 hover:text-blue-900 mr-3" onclick="editarProducto(<?= $producto['id'] ?>)">
                                                <i class="fas fa-edit mr-1"></i>Editar
                                            </button>
                                            <button class="text-red-600 hover:text-red-900" onclick="eliminarProducto(<?= $producto['id'] ?>)">
                                                <i class="fas fa-trash mr-1"></i>Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mensaje cuando no hay resultados -->
                    <div id="sin-resultados" class="hidden p-8 text-center">
                        <i class="fas fa-search text-gray-300 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-700">No se encontraron productos</h3>
                        <p class="text-gray-500 mt-2">Intenta con otros términos de búsqueda o ajusta los filtros</p>
                    </div>
                </div>
            </div>

            <!-- Pestaña: Categorías -->
            <div class="tab-panel" id="categorias-panel">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-4 border-b flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold">Categorías de Productos</h2>
                            <p class="text-gray-600"><?= count($categorias) ?> categorías activas en el sistema</p>
                        </div>
                        <button class="bg-pink-500 hover:bg-pink-600 text-white font-medium py-2 px-4 rounded-lg transition-colors" id="btn-nueva-categoria">
                            <i class="fas fa-plus mr-2"></i>Nueva Categoría
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6" id="grid-categorias">
                        <?php foreach ($categorias as $categoria): ?>
                            <div class="bg-gradient-to-br 
            <?= $categoria['id'] == 1 ? 'from-pink-50 to-purple-50 border-pink-100 text-pink-700' : '' ?>
            <?= $categoria['id'] == 2 ? 'from-blue-50 to-cyan-50 border-blue-100 text-blue-700' : '' ?>
            <?= $categoria['id'] == 3 ? 'from-amber-50 to-yellow-50 border-amber-100 text-amber-700' : '' ?>
            rounded-lg p-6 border fade-in">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-lg font-bold"><?= htmlspecialchars($categoria['nombre']) ?></h3>
                                        <p class="text-sm"><?= $categoria['productos_count'] ?> productos</p>
                                    </div>
                                    <span class="categoria-badge 
                    <?= $categoria['id'] == 1 ? 'categoria-fragancias' : '' ?>
                    <?= $categoria['id'] == 2 ? 'categoria-cremas' : '' ?>
                    <?= $categoria['id'] == 3 ? 'categoria-splash' : '' ?>
                "><?= $categoria['activa'] ? 'Activa' : 'Inactiva' ?></span>
                                </div>
                                <!-- AGREGAR LA DESCRIPCIÓN AQUÍ -->
                                <p class="text-gray-600 text-sm mb-4">
                                    <?= !empty($categoria['descripcion']) ? htmlspecialchars($categoria['descripcion']) : 'Sin descripción' ?>
                                </p>
                                <div class="flex space-x-2">
                                    <button class="flex-1 bg-white border 
                    <?= $categoria['id'] == 1 ? 'border-pink-100 text-pink-700' : '' ?>
                    <?= $categoria['id'] == 2 ? 'border-blue-100 text-blue-700' : '' ?>
                    <?= $categoria['id'] == 3 ? 'border-amber-100 text-amber-700' : '' ?>
                    hover:bg-opacity-50 font-medium py-2 px-3 rounded transition-colors text-sm"
                                        onclick="editarCategoria(<?= $categoria['id'] ?>)">
                                        <i class="fas fa-edit mr-1"></i>Editar
                                    </button>
                                    <button class="flex-1 
                    <?= $categoria['id'] == 1 ? 'bg-pink-600 text-white' : '' ?>
                    <?= $categoria['id'] == 2 ? 'bg-blue-600 text-white' : '' ?>
                    <?= $categoria['id'] == 3 ? 'bg-amber-600 text-white' : '' ?>
                    hover:opacity-90 font-medium py-2 px-3 rounded transition-colors text-sm"
                                        onclick="verProductosCategoria(<?= $categoria['id'] ?>)">
                                        <i class="fas fa-eye mr-1"></i>Ver Productos
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Pestaña: Control de Inventario -->
            <div class="tab-panel" id="inventario-panel">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-4 border-b">
                        <h2 class="text-xl font-bold">Control de Inventario</h2>
                        <p class="text-gray-600">Gestión completa del stock de productos</p>
                    </div>

                    <div class="p-6">
                        <!-- Estadísticas rápidas -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                                        <i class="fas fa-cubes text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-800" id="estadistica-total"><?= $estadisticas['total_productos'] ?></p>
                                        <p class="text-sm text-gray-600">Total Productos</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                                        <i class="fas fa-box-open text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-800" id="estadistica-stock"><?= $estadisticas['total_stock'] ?></p>
                                        <p class="text-sm text-gray-600">Unidades en Stock</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                                        <i class="fas fa-exclamation-triangle text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-800" id="estadistica-bajo"><?= $estadisticas['stock_bajo'] ?></p>
                                        <p class="text-sm text-gray-600">Stock Bajo</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                                        <i class="fas fa-chart-line text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-800" id="estadistica-valor">S/ <?= number_format($estadisticas['valor_inventario'], 2) ?></p>
                                        <p class="text-sm text-gray-600">Valor Inventario</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de inventario -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Actual</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Mínimo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="tabla-inventario">
                                    <?php foreach ($productos as $producto): ?>
                                        <tr class="fade-in <?= $producto['stock'] <= 5 ? 'stock-bajo' : 'stock-normal' ?>">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($producto['nombre']) ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($producto['categoria_nombre']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $producto['stock'] ?> unidades</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">5 unidades</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?php if ($producto['stock'] <= 5): ?>
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Stock Bajo</span>
                                                <?php else: ?>
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">En Stock</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button class="text-blue-600 hover:text-blue-900 mr-3" onclick="ajustarStock(<?= $producto['id'] ?>)">
                                                    <i class="fas fa-edit mr-1"></i>Ajustar
                                                </button>
                                                <button class="text-green-600 hover:text-green-900" onclick="agregarStock(<?= $producto['id'] ?>)">
                                                    <i class="fas fa-plus mr-1"></i>Agregar
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pestaña: Stock Bajo -->
            <div class="tab-panel" id="stock-bajo-panel">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-4 border-b flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold">Productos con Stock Bajo</h2>
                            <p class="text-gray-600"><span id="total-stock-bajo-text"><?= count($productosStockBajo) ?></span> productos necesitan reposición urgente</p>
                        </div>

                    </div>

                    <div class="p-6">
                        <!-- Información de alerta -->
                        <?php if (count($productosStockBajo) > 0): ?>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-triangle text-red-400 text-xl mt-1"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">Alerta de Stock Bajo</h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <p>Los siguientes productos han alcanzado o están por debajo de su stock mínimo. Se recomienda realizar pedidos de reposición lo antes posible.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="grid-stock-bajo">
                                <?php foreach ($productosStockBajo as $producto): ?>
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 fade-in">
                                        <div class="flex justify-between items-start mb-3">
                                            <h3 class="font-bold text-red-800"><?= htmlspecialchars($producto['nombre']) ?></h3>
                                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded">Stock Crítico</span>
                                        </div>
                                        <p class="text-red-600 text-sm mb-2"><?= htmlspecialchars($producto['categoria_nombre']) ?></p>
                                        <div class="flex justify-between items-center mb-3">
                                            <span class="text-red-700 font-bold">Stock: <?= $producto['stock'] ?> unidades</span>
                                            <span class="text-red-600 text-sm">Mínimo: 5</span>
                                        </div>
                                        <div class="w-full bg-red-200 rounded-full h-2 mb-4">
                                            <?php
                                            $porcentajeStock = ($producto['stock'] / 5) * 100;
                                            $bgColor = $porcentajeStock <= 25 ? 'bg-red-600' : ($porcentajeStock <= 50 ? 'bg-orange-500' : 'bg-yellow-400');
                                            ?>
                                            <div class="<?= $bgColor ?> h-2 rounded-full" style="width: <?= $porcentajeStock ?>%"></div>
                                        </div>
                                        <div class="text-xs text-red-600 mb-3">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            <?= $porcentajeStock <= 25 ? 'Stock crítico - Reposición urgente' : ($porcentajeStock <= 50 ? 'Stock bajo - Considerar reposición' :
                                                'Stock por debajo del mínimo') ?>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-3 rounded transition-colors text-sm" onclick="agregarStock(<?= $producto['id'] ?>)">
                                                <i class="fas fa-boxes mr-1"></i>Reponer
                                            </button>
                                            <button class="flex-1 bg-white text-red-600 border border-red-600 hover:bg-red-50 font-medium py-2 px-3 rounded transition-colors text-sm" onclick="editarProducto(<?= $producto['id'] ?>)">
                                                <i class="fas fa-edit mr-1"></i>Editar
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div id="sin-stock-bajo" class="text-center py-12">
                                <i class="fas fa-check-circle text-green-400 text-5xl mb-4"></i>
                                <h3 class="text-xl font-medium text-gray-700">¡Excelente!</h3>
                                <p class="text-gray-500 mt-2">Todos los productos tienen stock suficiente</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Pestaña: Papelera -->
            <div class="tab-panel" id="papelera-panel">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-4 border-b flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold">Papelera de Productos</h2>
                            <p class="text-gray-600"><span id="total-papelera-text">0</span> productos eliminados</p>
                        </div>
                        <div class="flex space-x-2">
                            <button class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors" id="btn-vaciar-papelera">
                                <i class="fas fa-broom mr-2"></i>Vaciar Papelera
                            </button>
                            <button class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg transition-colors" id="btn-restaurar-todos">
                                <i class="fas fa-undo mr-2"></i>Restaurar Todos
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- Información de la papelera -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-gray-400 text-xl mt-1"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-gray-800">Información de la Papelera</h3>
                                    <div class="mt-2 text-sm text-gray-700">
                                        <p>Los productos en la papelera permanecerán aquí por 30 días antes de ser eliminados permanentemente. Puedes restaurarlos o eliminarlos definitivamente.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de productos eliminados -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="tabla-papelera">
                                    <!-- Los productos eliminados se cargarán aquí dinámicamente -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Mensaje cuando la papelera está vacía -->
                        <div id="papelera-vacia" class="hidden text-center py-12">
                            <i class="fas fa-trash-alt text-gray-300 text-5xl mb-4"></i>
                            <h3 class="text-xl font-medium text-gray-700">Papelera vacía</h3>
                            <p class="text-gray-500 mt-2">No hay productos eliminados</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pestaña: Agregar Producto -->
            <div class="tab-panel" id="nuevo-producto-panel">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold mb-6">Agregar Nuevo Producto</h2>

                    <form id="form-nuevo-producto" class="grid grid-cols-1 md:grid-cols-2 gap-6" onsubmit="guardarNuevoProducto(event)">
                        <!-- Información Básica -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-pink-600 mb-3">Información Básica</h3>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Producto *</label>
                                <input type="text" id="producto-nombre" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" placeholder="Ej: Bare Vanilla Fragrance Mist" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                                <textarea id="producto-descripcion" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" rows="3" placeholder="Descripción detallada del producto..."></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Categoría *</label>
                                <select id="producto-categoria" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
                                    <option value="">Seleccionar categoría...</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= $categoria['id'] ?>"><?= htmlspecialchars($categoria['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Precio e Inventario -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-pink-600 mb-3">Precio e Inventario</h3>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Precio (S/) *</label>
                                <input type="number" id="producto-precio" step="0.01" min="0" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" placeholder="0.00" required>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock Inicial *</label>
                                    <input type="number" id="producto-stock" min="0" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" placeholder="0" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock Mínimo</label>
                                    <input type="number" id="producto-stock-minimo" min="0" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" placeholder="5" value="5">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Imagen del Producto</label>
                                <div class="border-2 border-dashed border-gray-300 rounded-md p-4 text-center cursor-pointer hover:border-pink-400 transition-colors"
                                    onclick="document.getElementById('producto-imagen').click()"
                                    ondrop="manejarDrop(event)"
                                    ondragover="manejarDragOver(event)">
                                    <div class="imagen-placeholder">
                                        <i class="fas fa-cloud-upload-alt text-gray-400 text-2xl mb-2"></i>
                                        <p class="text-sm text-gray-600">Arrastra una imagen o haz clic para subir</p>
                                        <p class="text-xs text-gray-500 mt-1">Formatos: JPG, PNG, GIF, WebP (Max. 5MB)</p>
                                    </div>
                                    <input type="file" id="producto-imagen" class="hidden" accept="image/*"
                                        onchange="previsualizarImagen(this)">
                                </div>
                                <div id="imagen-preview-container"></div>
                                <button type="button" id="btn-eliminar-imagen" class="hidden mt-2 bg-red-500 hover:bg-red-600 text-white font-medium py-1 px-3 rounded transition-colors text-sm"
                                    onclick="eliminarPrevisualizacion('nuevo')">
                                    <i class="fas fa-trash mr-1"></i>Eliminar Imagen
                                </button>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" id="producto-activo" class="rounded border-gray-300 text-pink-600 focus:ring-pink-500" checked>
                                <label for="producto-activo" class="ml-2 text-sm text-gray-700">Producto activo y disponible para venta</label>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="md:col-span-2 mt-8 flex justify-end space-x-4">
                            <button type="button" onclick="limpiarFormularioProducto()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-6 rounded-lg transition-colors">
                                <i class="fas fa-times mr-2"></i>Cancelar
                            </button>
                            <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                                <i class="fas fa-save mr-2"></i>Guardar Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* ============================================= */
        /* 🎨 ESTILOS ORIGINALES DEL DISEÑO */
        /* ============================================= */

        :root {
            --primary: #f472b6;
            --secondary: #a78bfa;
        }

        .sidebar {
            z-index: 100;
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
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
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
            z-index: 9999;
            /* Aumentamos el z-index */
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
            animation: fadeIn 0.3s ease;
        }

        .modal-content {
            background-color: white;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            transform: scale(0.9);
            animation: modalAppear 0.3s ease forwards;
            z-index: 10000;
            /* Aseguramos que el contenido esté por encima */
            position: relative;
        }

        @keyframes modalAppear {
            to {
                transform: scale(1);
            }
        }

        .product-item {
            transition: all 0.2s ease;
        }

        .product-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #d97706;
        }

        .status-delivered {
            background-color: #d1fae5;
            color: #065f46;
        }

        .fade-in {
            animation: fadeIn 0.5s ease;
        }

        .stock-bajo {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
        }

        .stock-normal {
            background-color: #f0fdf4;
            border-left: 4px solid #22c55e;
        }

        .categoria-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .categoria-fragancias {
            background-color: #fce7f3;
            color: #db2777;
        }

        .categoria-cremas {
            background-color: #f0f9ff;
            color: #0ea5e9;
        }

        .categoria-splash {
            background-color: #fef7cd;
            color: #ca8a04;
        }

        .search-highlight {
            background-color: #fffacd;
            padding: 2px 4px;
            border-radius: 4px;
        }

        /* Efectos hover para los tabs */
        .tab-button {
            position: relative;
            overflow: hidden;
        }

        .tab-button::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .tab-button.active::after {
            width: 100%;
        }

        .tab-button:hover::after {
            width: 100%;
        }

        /* Animación para las filas de la tabla */
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

        #tabla-productos tr {
            animation: slideIn 0.3s ease;
        }

        #tabla-productos tr:nth-child(1) {
            animation-delay: 0.1s;
        }

        #tabla-productos tr:nth-child(2) {
            animation-delay: 0.2s;
        }

        #tabla-productos tr:nth-child(3) {
            animation-delay: 0.3s;
        }

        #tabla-productos tr:nth-child(4) {
            animation-delay: 0.4s;
        }

        /* Efectos para las tarjetas de categorías */
        #grid-categorias>div {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        #grid-categorias>div:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        /* Efectos para los productos con stock bajo */
        #grid-stock-bajo>div {
            transition: all 0.3s ease;
        }

        #grid-stock-bajo>div:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.15);
        }

        /* Loading states */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #f472b6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        /* Transición suave para el filtro */
        #tabla-productos tr {
            transition: all 0.3s ease;
        }

        /* Estilo para el select de categorías */
        #filtro-categoria {
            transition: all 0.3s ease;
        }

        #filtro-categoria:focus {
            border-color: #ec4899;
            box-shadow: 0 0 0 2px rgba(236, 72, 153, 0.2);
        }

        /* Estilos para el modal de categorías */
        .modal-categoria-content {
            background: white;
            border-radius: 12px;
            padding: 0;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .categoria-color-preview {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            display: inline-block;
            margin-right: 8px;
            border: 2px solid #e5e7eb;
        }

        /* Estilos para el modal de productos por categoría */
        /* ESTILOS CORREGIDOS PARA LINE CLAMP */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;

            /* Fallback para navegadores modernos */
            display: -webkit-box;
            display: -moz-box;
            display: box;
            -webkit-box-orient: vertical;
            -moz-box-orient: vertical;
            box-orient: vertical;
            line-clamp: 2;
            -webkit-line-clamp: 2;
            -moz-line-clamp: 2;
        }

        .modal-productos-categoria {
            max-width: 800px;
        }

        .producto-card {
            transition: all 0.2s ease;
        }

        .producto-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Estilos específicos para el modal de productos por categoría */
        #modal-productos-categoria .modal-content {
            max-width: 800px;
            width: 95%;
        }

        /* Estilos para la previsualización de imágenes */
        #preview-imagen {
            transition: all 0.3s ease;
        }

        #preview-imagen:hover {
            transform: scale(1.02);
        }

        .imagen-placeholder {
            transition: all 0.3s ease;
        }

        .drag-over {
            border-color: #ec4899;
            background-color: #fdf2f8;
        }

        /* ============================================= */
        /* 🆕 ESTILOS NUEVOS CORREGIDOS */
        /* ============================================= */

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

        .input-error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .input-success {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        /* ============================================= */
        /* 🔔 ESTILOS CORREGIDOS PARA NOTIFICACIONES */
        /* ============================================= */

        .custom-notification {
            min-width: 300px;
            max-width: 400px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-radius: 12px;
            z-index: 10001 !important;
            /* Mayor que el header y modales */
            position: fixed;
            top: 100px;
            /* Debajo del header */
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

        @keyframes slideOutRight {
            from {
                opacity: 1;
                transform: translateX(0);
            }

            to {
                opacity: 0;
                transform: translateX(100%);
            }
        }

        .custom-notification.fade-out {
            animation: slideOutRight 0.3s ease-in forwards;
        }

        /* Asegurar que el header tenga un z-index adecuado */
        header {
            z-index: 1000;
            position: relative;
        }

        /* Asegurar que los modales tengan z-index alto */
        .modal {
            z-index: 9999;
        }

        .modal-content {
            z-index: 10000;
        }
    </style>
</main>

<!-- Modal para editar producto -->
<div class="modal" id="modal-editar-producto">
    <div class="modal-content">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Editar Producto</h3>
            <button class="text-gray-500 hover:text-gray-700" id="cerrar-modal-producto">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-4" id="contenido-modal-producto">
            <!-- El formulario de edición se cargará aquí dinámicamente -->
        </div>
    </div>
</div>

<!-- Modal para editar categoría -->
<div class="modal" id="modal-editar-categoria">
    <div class="modal-content">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Editar Categoría</h3>
            <button class="text-gray-500 hover:text-gray-700" id="cerrar-modal-categoria">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-4" id="contenido-modal-categoria">
            <!-- El formulario de edición de categoría se cargará aquí -->
        </div>
    </div>
</div>

<!-- Modal para ver productos de categoría -->
<div class="modal" id="modal-productos-categoria">
    <div class="modal-content" style="max-width: 800px; width: 95%;">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800" id="titulo-productos-categoria">Productos de la Categoría</h3>
            <button class="text-gray-500 hover:text-gray-700" id="cerrar-modal-productos-categoria">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-4" id="contenido-productos-categoria">
            <!-- Los productos de la categoría se cargarán aquí -->
        </div>
    </div>
</div>

<script>
    // =============================================
    // 🎯 CONFIGURACIÓN INICIAL Y EVENT LISTENERS
    // =============================================

    document.addEventListener('DOMContentLoaded', function() {
        inicializarApp();
        configurarEventListeners();
    });

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

        // Tabs de Gestión de Productos
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

                // Cargar papelera si es la pestaña activa
                if (tabId === 'papelera') {
                    cargarPapelera();
                }
            });
        });

        // Toggle menu móvil (CON VERIFICACIÓN)
        const menuToggle = document.getElementById('menuToggle');
        if (menuToggle) {
            menuToggle.addEventListener('click', function() {
                document.querySelector('.sidebar').classList.toggle('hidden');
            });
        }
    }

    function configurarEventListeners() {
        // SOLO filtro por categoría
        const filtroCategoria = document.getElementById('filtro-categoria');
        if (filtroCategoria) {
            filtroCategoria.addEventListener('change', filtrarProductos);
        }

        // Cerrar modales
        const cerrarModalProducto = document.getElementById('cerrar-modal-producto');
        if (cerrarModalProducto) {
            cerrarModalProducto.addEventListener('click', function() {
                document.getElementById('modal-editar-producto').classList.remove('active');
            });
        }

        const cerrarModalCategoria = document.getElementById('cerrar-modal-categoria');
        if (cerrarModalCategoria) {
            cerrarModalCategoria.addEventListener('click', function() {
                document.getElementById('modal-editar-categoria').classList.remove('active');
            });
        }

        const cerrarModalProductosCategoria = document.getElementById('cerrar-modal-productos-categoria');
        if (cerrarModalProductosCategoria) {
            cerrarModalProductosCategoria.addEventListener('click', function() {
                document.getElementById('modal-productos-categoria').classList.remove('active');
            });
        }

        // Botón nueva categoría
        const btnNuevaCategoria = document.getElementById('btn-nueva-categoria');
        if (btnNuevaCategoria) {
            btnNuevaCategoria.addEventListener('click', function() {
                crearNuevaCategoria();
            });
        }

        // Botones de papelera
        const btnVaciarPapelera = document.getElementById('btn-vaciar-papelera');
        if (btnVaciarPapelera) {
            btnVaciarPapelera.addEventListener('click', vaciarPapelera);
        }

        const btnRestaurarTodos = document.getElementById('btn-restaurar-todos');
        if (btnRestaurarTodos) {
            btnRestaurarTodos.addEventListener('click', restaurarTodos);
        }

        // Configurar drag and drop
        configurarDragAndDrop();

        // Cargar contador inicial de papelera
        cargarContadorPapelera();
    }

    function configurarDragAndDrop() {
        const dropZones = document.querySelectorAll('[ondragover]');
        dropZones.forEach(zone => {
            zone.addEventListener('dragover', manejarDragOver);
            zone.addEventListener('drop', manejarDrop);
            zone.addEventListener('dragleave', function(e) {
                if (!e.currentTarget.contains(e.relatedTarget)) {
                    e.currentTarget.classList.remove('drag-over');
                }
            });
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

    function validarFormularioProducto(datos) {
        const errores = [];

        if (!datos.nombre || datos.nombre.trim().length < 2) {
            errores.push('El nombre debe tener al menos 2 caracteres');
        }

        if (!datos.categoria_id) {
            errores.push('La categoría es obligatoria');
        }

        if (!datos.precio || parseFloat(datos.precio) <= 0) {
            errores.push('El precio debe ser mayor a 0');
        }

        if (!datos.stock || parseInt(datos.stock) < 0) {
            errores.push('El stock no puede ser negativo');
        }

        return errores;
    }

    // =============================================
    // 🖼️ FUNCIONES DE PREVISUALIZACIÓN DE IMÁGENES
    // =============================================

    function previsualizarImagen(input) {
        const placeholder = input.parentNode.querySelector('.imagen-placeholder');
        const previewContainer = document.getElementById('imagen-preview-container');
        const btnEliminar = document.getElementById('btn-eliminar-imagen');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            // Validar tipo y tamaño de archivo
            const file = input.files[0];
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            const maxSize = 5 * 1024 * 1024; // 5MB

            if (!validTypes.includes(file.type)) {
                mostrarNotificacion('Formato no válido. Use JPG, PNG, GIF o WebP', 'error');
                input.value = '';
                return;
            }

            if (file.size > maxSize) {
                mostrarNotificacion('La imagen es muy grande (máx. 5MB)', 'error');
                input.value = '';
                return;
            }

            reader.onload = function(e) {
                // Crear o actualizar preview
                let preview = previewContainer.querySelector('#preview-imagen');
                if (!preview) {
                    preview = document.createElement('img');
                    preview.id = 'preview-imagen';
                    preview.className = 'w-full h-48 object-contain rounded-lg mt-2 border';
                    previewContainer.appendChild(preview);
                }
                preview.src = e.target.result;

                // Ocultar placeholder y mostrar botón eliminar
                if (placeholder) placeholder.style.display = 'none';
                if (btnEliminar) btnEliminar.classList.remove('hidden');
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    function previsualizarImagenEditar(input) {
        const previewContainer = document.getElementById('imagen-preview-editar');
        const btnEliminar = document.getElementById('btn-eliminar-imagen-editar');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            const file = input.files[0];
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            const maxSize = 5 * 1024 * 1024;

            if (!validTypes.includes(file.type)) {
                mostrarNotificacion('Formato no válido. Use JPG, PNG, GIF o WebP', 'error');
                input.value = '';
                return;
            }

            if (file.size > maxSize) {
                mostrarNotificacion('La imagen es muy grande (máx. 5MB)', 'error');
                input.value = '';
                return;
            }

            reader.onload = function(e) {
                let preview = previewContainer.querySelector('#preview-imagen-editar');
                if (!preview) {
                    preview = document.createElement('img');
                    preview.id = 'preview-imagen-editar';
                    preview.className = 'w-full h-48 object-contain rounded-lg mt-2 border';
                    previewContainer.appendChild(preview);
                }
                preview.src = e.target.result;

                if (btnEliminar) btnEliminar.classList.remove('hidden');
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    function eliminarPrevisualizacion(tipo = 'nuevo') {
        const previewId = tipo === 'nuevo' ? 'preview-imagen' : 'preview-imagen-editar';
        const btnId = tipo === 'nuevo' ? 'btn-eliminar-imagen' : 'btn-eliminar-imagen-editar';
        const inputId = tipo === 'nuevo' ? 'producto-imagen' : 'edit-producto-imagen';
        const placeholder = tipo === 'nuevo' ?
            document.querySelector('.imagen-placeholder') :
            document.querySelector('.imagen-placeholder-editar');

        const preview = document.getElementById(previewId);
        const btnEliminar = document.getElementById(btnId);
        const input = document.getElementById(inputId);

        if (preview) preview.remove();
        if (btnEliminar) btnEliminar.classList.add('hidden');
        if (input) input.value = '';
        if (placeholder) placeholder.style.display = 'block';
    }

    function manejarDragOver(e) {
        e.preventDefault();
        e.currentTarget.classList.add('drag-over');
    }

    function manejarDrop(e) {
        e.preventDefault();
        e.currentTarget.classList.remove('drag-over');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const input = e.currentTarget.querySelector('input[type="file"]');
            if (input) {
                input.files = files;

                // Disparar evento change
                const event = new Event('change', {
                    bubbles: true
                });
                input.dispatchEvent(event);
            }
        }
    }

    // =============================================
    // 🔍 FUNCIÓN DE FILTRADO POR CATEGORÍA - CORREGIDA
    // =============================================

    function filtrarProductos() {
        const categoriaFiltro = document.getElementById('filtro-categoria').value;
        const filas = document.querySelectorAll('#tabla-productos tr');
        let productosVisibles = 0;

        filas.forEach(fila => {
            const categoriaBadge = fila.querySelector('.categoria-badge');

            if (!categoriaBadge) {
                fila.style.display = 'none';
                return;
            }

            // Obtener ID de categoría del data attribute
            const categoriaId = categoriaBadge.getAttribute('data-categoria-id');

            // Mostrar todas las categorías si no hay filtro, o solo la seleccionada
            if (!categoriaFiltro || categoriaId === categoriaFiltro) {
                fila.style.display = '';
                productosVisibles++;
            } else {
                fila.style.display = 'none';
            }
        });

        actualizarVistaFiltros(productosVisibles);
    }

    function actualizarVistaFiltros(productosVisibles) {
        document.getElementById('total-productos-text').textContent = productosVisibles;

        const sinResultados = document.getElementById('sin-resultados');
        if (productosVisibles === 0) {
            sinResultados.classList.remove('hidden');
        } else {
            sinResultados.classList.add('hidden');
        }
    }

    // =============================================
    // 🛍️ FUNCIONES CRUD DE PRODUCTOS - CORREGIDAS
    // =============================================

    async function guardarNuevoProducto(e) {
        e.preventDefault();

        // Obtener valores del formulario
        const nombre = document.getElementById('producto-nombre').value.trim();
        const descripcion = document.getElementById('producto-descripcion').value.trim();
        const categoria_id = document.getElementById('producto-categoria').value;
        const precio = document.getElementById('producto-precio').value;
        const stock = document.getElementById('producto-stock').value;
        const activo = document.getElementById('producto-activo').checked ? 1 : 0;
        const imagenInput = document.getElementById('producto-imagen');

        // Validaciones
        const errores = validarFormularioProducto({
            nombre: nombre,
            categoria_id: categoria_id,
            precio: precio,
            stock: stock
        });

        if (errores.length > 0) {
            mostrarNotificacion(errores.join(', '), 'error');
            return;
        }

        try {
            mostrarLoading('Guardando producto...');

            const formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('descripcion', descripcion);
            formData.append('categoria_id', categoria_id);
            formData.append('precio', parseFloat(precio));
            formData.append('stock', parseInt(stock));
            formData.append('activo', activo);

            // Agregar imagen si existe
            if (imagenInput.files.length > 0) {
                formData.append('imagen', imagenInput.files[0]);
            }

            const response = await fetch('index.php?view=api-guardar-producto', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            ocultarLoading();

            if (result.success) {
                mostrarNotificacion('✅ Producto creado exitosamente', 'success');
                limpiarFormularioProducto();

                // Cambiar a la pestaña de lista de productos
                setTimeout(() => {
                    document.querySelector('[data-tab="lista-productos"]').click();
                    // Recargar la página después de 2 segundos
                    setTimeout(() => window.location.reload(), 2000);
                }, 1500);
            } else {
                throw new Error(result.message || 'Error desconocido');
            }
        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al crear producto: ' + error.message, 'error');
            console.error('Error detallado:', error);
        }
    }

    async function editarProducto(productoId) {
        try {
            mostrarLoading('Cargando producto...');

            const response = await fetch(`index.php?view=api-obtener-producto&id=${productoId}`);

            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }

            const result = await response.json();
            ocultarLoading();

            if (result.success) {
                mostrarModalEditarProducto(result.data);
            } else {
                throw new Error(result.message || 'Error al cargar producto');
            }
        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al cargar producto: ' + error.message, 'error');
            console.error('Error detallado:', error);
        }
    }

    function mostrarModalEditarProducto(producto) {
    // CORREGIR LA RUTA DE LA IMAGEN
    let imagenUrl = '';
    let imagenHtml = '<p class="text-sm text-gray-500 mt-2">No hay imagen actual</p>';
    
    if (producto.imagen) {
        // Extraer solo el nombre del archivo
        let nombreArchivo = producto.imagen.split('/').pop();
        
        // Construir URL CORRECTA
        imagenUrl = '/SamyGlow-ecom/uploads/productos/' + nombreArchivo;
        
        imagenHtml = `
            <div class="mt-2">
                <p class="text-sm text-gray-600 mb-2">Imagen actual:</p>
                <img src="${imagenUrl}" 
                     alt="${escapeHtml(producto.nombre)}" 
                     class="w-32 h-32 object-cover rounded-lg border"
                     onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';">
                <p class="text-sm text-gray-500 mt-2" style="display:none;">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-1"></i>
                    No se pudo cargar la imagen (${nombreArchivo})
                </p>
            </div>
        `;
    }
    
    let html = `
    <form id="form-editar-producto" class="space-y-4" onsubmit="guardarCambiosProducto(event, ${producto.id})">
        <input type="hidden" id="edit-producto-id" value="${producto.id}">
        <input type="hidden" id="edit-imagen-actual" value="${producto.imagen || ''}">
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Producto *</label>
            <input type="text" id="edit-producto-nombre" value="${escapeHtml(producto.nombre)}" 
                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
            <textarea id="edit-producto-descripcion" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" 
                      rows="3">${escapeHtml(producto.descripcion || '')}</textarea>
        </div>
        
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Categoría *</label>
                <select id="edit-producto-categoria" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
                    ${getOpcionesCategorias(producto.categoria_id)}
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Precio (S/) *</label>
                <input type="number" id="edit-producto-precio" value="${producto.precio}" step="0.01" min="0" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stock Actual *</label>
                <input type="number" id="edit-producto-stock" value="${producto.stock}" min="0" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stock Mínimo</label>
                <input type="number" id="edit-producto-stock-minimo" value="5" min="0" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
            </div>
        </div>
        
        <!-- CAMPO DE IMAGEN PARA EDITAR -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Imagen del Producto</label>
            <div class="border-2 border-dashed border-gray-300 rounded-md p-4 text-center cursor-pointer hover:border-pink-400 transition-colors" 
                 onclick="document.getElementById('edit-producto-imagen').click()">
                <div class="imagen-placeholder-editar">
                    <i class="fas fa-cloud-upload-alt text-gray-400 text-2xl mb-2"></i>
                    <p class="text-sm text-gray-600">Haz clic para cambiar la imagen</p>
                    <p class="text-xs text-gray-500 mt-1">Formatos: JPG, PNG, GIF, WebP (Max. 5MB)</p>
                </div>
                <input type="file" id="edit-producto-imagen" class="hidden" accept="image/*" 
                       onchange="previsualizarImagenEditar(this)">
            </div>
            <div id="imagen-preview-editar">
                ${imagenHtml}
            </div>
            <button type="button" id="btn-eliminar-imagen-editar" class="hidden mt-2 bg-red-500 hover:bg-red-600 text-white font-medium py-1 px-3 rounded transition-colors text-sm"
                    onclick="eliminarImagenProducto(${producto.id})">
                <i class="fas fa-trash mr-1"></i>Eliminar Imagen Actual
            </button>
        </div>
        
        <div class="flex items-center">
            <input type="checkbox" id="edit-producto-activo" ${producto.activo ? 'checked' : ''} 
                   class="rounded border-gray-300 text-pink-600 focus:ring-pink-500">
            <label for="edit-producto-activo" class="ml-2 text-sm text-gray-700">Producto activo</label>
        </div>
        
        <div class="flex justify-end space-x-4 pt-4">
            <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-4 rounded transition-colors" 
                    onclick="cerrarModalProducto()">
                <i class="fas fa-times mr-2"></i>Cancelar
            </button>
            <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white font-medium py-2 px-4 rounded transition-colors">
                <i class="fas fa-save mr-2"></i>Guardar Cambios
            </button>
        </div>
    </form>
    `;

    document.getElementById('contenido-modal-producto').innerHTML = html;

    setTimeout(() => {
        document.getElementById('modal-editar-producto').classList.add('active');
    }, 10);
}

    function getOpcionesCategorias(categoriaSeleccionada) {
        const categorias = [{
                id: 1,
                nombre: 'Fragancias'
            },
            {
                id: 2,
                nombre: 'Cremas Corporales'
            },
            {
                id: 3,
                nombre: 'Body Splash'
            }
        ];

        return categorias.map(cat =>
            `<option value="${cat.id}" ${cat.id == categoriaSeleccionada ? 'selected' : ''}>${cat.nombre}</option>`
        ).join('');
    }

    function cerrarModalProducto() {
        document.getElementById('modal-editar-producto').classList.remove('active');
    }

    async function guardarCambiosProducto(e, productoId) {
        e.preventDefault();

        // Obtener valores del formulario
        const nombre = document.getElementById('edit-producto-nombre').value.trim();
        const descripcion = document.getElementById('edit-producto-descripcion').value.trim();
        const categoria_id = parseInt(document.getElementById('edit-producto-categoria').value);
        const precio = parseFloat(document.getElementById('edit-producto-precio').value);
        const stock = parseInt(document.getElementById('edit-producto-stock').value);
        const activo = document.getElementById('edit-producto-activo').checked ? 1 : 0;
        const imagenInput = document.getElementById('edit-producto-imagen');

        // Validaciones
        const errores = validarFormularioProducto({
            nombre: nombre,
            categoria_id: categoria_id,
            precio: precio,
            stock: stock
        });

        if (errores.length > 0) {
            mostrarNotificacion(errores.join(', '), 'error');
            return;
        }

        try {
            mostrarLoading('Actualizando producto...');

            const formData = new FormData();
            formData.append('id', productoId);
            formData.append('nombre', nombre);
            formData.append('descripcion', descripcion);
            formData.append('categoria_id', categoria_id);
            formData.append('precio', precio);
            formData.append('stock', stock);
            formData.append('activo', activo);

            // Agregar nueva imagen si se seleccionó
            if (imagenInput && imagenInput.files.length > 0) {
                formData.append('imagen', imagenInput.files[0]);
            }

            const response = await fetch('index.php?view=api-editar-producto', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            ocultarLoading();

            if (result.success) {
                mostrarNotificacion('✅ Producto actualizado exitosamente', 'success');
                document.getElementById('modal-editar-producto').classList.remove('active');
                // Recargar la página después de 1.5 segundos
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(result.message || 'Error al actualizar producto');
            }
        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al actualizar producto: ' + error.message, 'error');
            console.error('Error detallado:', error);
        }
    }

    async function eliminarProducto(productoId) {
        if (!confirm('¿Estás seguro de que quieres eliminar este producto? Esta acción no se puede deshacer.')) {
            return;
        }

        try {
            mostrarLoading('Eliminando producto...');

            const formData = new FormData();
            formData.append('id', productoId);

            const response = await fetch('index.php?view=api-eliminar-producto', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            ocultarLoading();

            if (result.success) {
                mostrarNotificacion('✅ Producto eliminado exitosamente', 'success');
                // Recargar la página después de 1.5 segundos
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(result.message || 'Error al eliminar producto');
            }
        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al eliminar producto: ' + error.message, 'error');
            console.error('Error detallado:', error);
        }
    }

    function limpiarFormularioProducto() {
        document.getElementById('form-nuevo-producto').reset();
        document.getElementById('producto-stock-minimo').value = '5';
        document.getElementById('producto-activo').checked = true;

        // Limpiar previsualización de imagen
        eliminarPrevisualizacion('nuevo');

        // Restaurar placeholder
        const placeholder = document.querySelector('.imagen-placeholder');
        if (placeholder) {
            placeholder.style.display = 'block';
        }
    }

    // =============================================
    // 📦 FUNCIONES DE GESTIÓN DE INVENTARIO
    // =============================================

    async function ajustarStock(productoId) {
        const producto = await obtenerProducto(productoId);
        if (!producto) return;

        const nuevoStock = prompt(`Ajustar stock para ${producto.nombre}\nStock actual: ${producto.stock}`, producto.stock);

        if (nuevoStock !== null && !isNaN(nuevoStock) && parseInt(nuevoStock) >= 0) {
            try {
                mostrarLoading('Actualizando stock...');

                const formData = new FormData();
                formData.append('id', productoId);
                formData.append('stock', nuevoStock);

                const response = await fetch('index.php?view=api-actualizar-stock', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                ocultarLoading();

                if (result.success) {
                    mostrarNotificacion(`✅ Stock de ${producto.nombre} actualizado a ${nuevoStock}`, 'success');
                    // Recargar la página después de 1.5 segundos
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    throw new Error(result.message || 'Error al actualizar stock');
                }
            } catch (error) {
                ocultarLoading();
                mostrarNotificacion('❌ Error al actualizar stock: ' + error.message, 'error');
            }
        }
    }

    async function agregarStock(productoId) {
        const producto = await obtenerProducto(productoId);
        if (!producto) return;

        const cantidad = prompt(`Agregar stock para ${producto.nombre}\nStock actual: ${producto.stock}\nCantidad a agregar:`, "10");

        if (cantidad !== null && !isNaN(cantidad) && parseInt(cantidad) > 0) {
            const nuevoStock = producto.stock + parseInt(cantidad);

            try {
                mostrarLoading('Agregando stock...');

                const formData = new FormData();
                formData.append('id', productoId);
                formData.append('stock', nuevoStock);

                const response = await fetch('index.php?view=api-actualizar-stock', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                ocultarLoading();

                if (result.success) {
                    mostrarNotificacion(`✅ Se agregaron ${cantidad} unidades a ${producto.nombre}`, 'success');
                    // Recargar la página después de 1.5 segundos
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    throw new Error(result.message || 'Error al agregar stock');
                }
            } catch (error) {
                ocultarLoading();
                mostrarNotificacion('❌ Error al agregar stock: ' + error.message, 'error');
            }
        }
    }

    async function obtenerProducto(productoId) {
        try {
            const response = await fetch(`index.php?view=api-obtener-producto&id=${productoId}`);
            const result = await response.json();

            if (result.success) {
                return result.data;
            } else {
                throw new Error(result.message || 'Error al obtener producto');
            }
        } catch (error) {
            mostrarNotificacion('❌ Error al obtener producto: ' + error.message, 'error');
            return null;
        }
    }

    // =============================================
    // 🏷️ FUNCIONES DE CATEGORÍAS - COMPLETAS
    // =============================================

    async function editarCategoria(categoriaId) {
        try {
            mostrarLoading('Cargando categoría...');

            const response = await fetch(`index.php?view=api-obtener-categoria&id=${categoriaId}`);
            const result = await response.json();
            ocultarLoading();

            if (result.success) {
                const categoria = result.data;
                mostrarModalEditarCategoria(categoria);
            } else {
                throw new Error(result.message || 'Error al cargar categoría');
            }
        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al cargar categoría: ' + error.message, 'error');
        }
    }

    function mostrarModalEditarCategoria(categoria) {
        let html = `
    <form id="form-editar-categoria" class="space-y-4" onsubmit="guardarCambiosCategoria(event, ${categoria.id})">
        <input type="hidden" id="edit-categoria-id" value="${categoria.id}">
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Categoría</label>
            <input type="text" id="edit-categoria-nombre" value="${escapeHtml(categoria.nombre)}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
            <textarea id="edit-categoria-descripcion" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" rows="3" placeholder="Descripción de la categoría...">${escapeHtml(categoria.descripcion || '')}</textarea>
        </div>
        
        <div class="flex items-center">
            <input type="checkbox" id="edit-categoria-activa" ${categoria.activa ? 'checked' : ''} class="rounded border-gray-300 text-pink-600 focus:ring-pink-500">
            <label for="edit-categoria-activa" class="ml-2 text-sm text-gray-700">Categoría activa</label>
        </div>
        
        <div class="bg-gray-50 p-3 rounded-md">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Información de la Categoría</h4>
            <div class="text-xs text-gray-600 space-y-1">
                <p><strong>ID:</strong> ${categoria.id}</p>
                <p><strong>Creada:</strong> ${new Date(categoria.created_at).toLocaleDateString()}</p>
            </div>
        </div>
        
        <div class="flex justify-end space-x-4 pt-4">
            <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-4 rounded transition-colors" onclick="cerrarModalCategoria()">
                Cancelar
            </button>
            <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white font-medium py-2 px-4 rounded transition-colors">
                Guardar Cambios
            </button>
        </div>
    </form>
    `;

        document.getElementById('contenido-modal-categoria').innerHTML = html;

        setTimeout(() => {
            document.getElementById('modal-editar-categoria').classList.add('active');
        }, 10);
    }

    function cerrarModalCategoria() {
        document.getElementById('modal-editar-categoria').classList.remove('active');
    }

    async function guardarCambiosCategoria(e, categoriaId) {
        e.preventDefault();

        // Obtener valores del formulario
        const nombre = document.getElementById('edit-categoria-nombre').value.trim();
        const descripcion = document.getElementById('edit-categoria-descripcion').value.trim();
        const activa = document.getElementById('edit-categoria-activa').checked ? 1 : 0;

        // Validaciones
        if (!nombre) {
            mostrarNotificacion('El nombre de la categoría es obligatorio', 'error');
            return;
        }

        try {
            mostrarLoading('Actualizando categoría...');

            const formData = new FormData();
            formData.append('id', categoriaId);
            formData.append('nombre', nombre);
            formData.append('descripcion', descripcion);
            formData.append('activa', activa);

            const response = await fetch('index.php?view=api-actualizar-categoria', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            ocultarLoading();

            if (result.success) {
                mostrarNotificacion('✅ Categoría actualizada exitosamente', 'success');
                document.getElementById('modal-editar-categoria').classList.remove('active');
                // Recargar la página después de 1.5 segundos
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(result.message || 'Error al actualizar categoría');
            }
        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al actualizar categoría: ' + error.message, 'error');
        }
    }

    async function crearNuevaCategoria() {
        let html = `
    <form id="form-nueva-categoria" class="space-y-4" onsubmit="guardarNuevaCategoria(event)">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Categoría *</label>
            <input type="text" id="nueva-categoria-nombre" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" placeholder="Ej: Perfumes" required>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
            <textarea id="nueva-categoria-descripcion" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" rows="3" placeholder="Descripción de la categoría..."></textarea>
        </div>
        
        <div class="flex items-center">
            <input type="checkbox" id="nueva-categoria-activa" checked class="rounded border-gray-300 text-pink-600 focus:ring-pink-500">
            <label for="nueva-categoria-activa" class="ml-2 text-sm text-gray-700">Categoría activa</label>
        </div>
        
        <div class="bg-blue-50 p-3 rounded-md">
            <h4 class="text-sm font-medium text-blue-700 mb-2">Información Importante</h4>
            <div class="text-xs text-blue-600 space-y-1">
                <p>• Las categorías te ayudan a organizar tus productos</p>
                <p>• Puedes desactivar una categoría sin eliminar sus productos</p>
                <p>• El nombre debe ser único y descriptivo</p>
            </div>
        </div>
        
        <div class="flex justify-end space-x-4 pt-4">
            <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-4 rounded transition-colors" onclick="document.getElementById('modal-editar-categoria').classList.remove('active')">
                Cancelar
            </button>
            <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white font-medium py-2 px-4 rounded transition-colors">
                Crear Categoría
            </button>
        </div>
    </form>
    `;

        document.getElementById('contenido-modal-categoria').innerHTML = html;
        document.getElementById('modal-editar-categoria').classList.add('active');
    }

    async function guardarNuevaCategoria(e) {
        e.preventDefault();

        // Obtener valores del formulario
        const nombre = document.getElementById('nueva-categoria-nombre').value.trim();
        const descripcion = document.getElementById('nueva-categoria-descripcion').value.trim();
        const activa = document.getElementById('nueva-categoria-activa').checked ? 1 : 0;

        // Validaciones
        if (!nombre) {
            mostrarNotificacion('El nombre de la categoría es obligatorio', 'error');
            return;
        }

        try {
            mostrarLoading('Creando categoría...');

            const formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('descripcion', descripcion);
            formData.append('activa', activa);

            const response = await fetch('index.php?view=api-crear-categoria', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            ocultarLoading();

            if (result.success) {
                mostrarNotificacion('✅ Categoría creada exitosamente', 'success');
                document.getElementById('modal-editar-categoria').classList.remove('active');
                // Recargar la página después de 1.5 segundos
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(result.message || 'Error al crear categoría');
            }
        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al crear categoría: ' + error.message, 'error');
        }
    }

    // =============================================
    // 👁️ FUNCIÓN PARA VER PRODUCTOS DE CATEGORÍA
    // =============================================

    async function verProductosCategoria(categoriaId) {
        try {
            // Mostrar loading
            document.getElementById('contenido-productos-categoria').innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-pink-500 text-2xl mb-2"></i>
                <p class="text-gray-600">Cargando productos...</p>
            </div>
        `;

            console.log('🔍 Solicitando productos para categoría:', categoriaId);

            // Obtener información de la categoría
            const responseCategoria = await fetch(`index.php?view=api-obtener-categoria&id=${categoriaId}`);
            const resultCategoria = await responseCategoria.json();

            if (!resultCategoria.success) {
                throw new Error('Error al cargar información de la categoría: ' + resultCategoria.message);
            }

            const categoria = resultCategoria.data;

            // ✅ URL CORREGIDA - usa & en lugar de ?
            const responseProductos = await fetch(`index.php?view=api-buscar-productos&categoria_id=${categoriaId}`);
            const resultProductos = await responseProductos.json();

            if (!resultProductos.success) {
                throw new Error('Error al cargar productos de la categoría: ' + resultProductos.message);
            }

            const productos = resultProductos.data;

            // Mostrar el modal con los productos
            mostrarModalProductosCategoria(categoria, productos);

        } catch (error) {
            console.error('💥 Error completo:', error);
            mostrarNotificacion('❌ Error al cargar productos: ' + error.message, 'error');
            document.getElementById('modal-productos-categoria').classList.remove('active');
        }
    }

    function mostrarModalProductosCategoria(categoria, productos) {
        // Actualizar título del modal
        document.getElementById('titulo-productos-categoria').textContent = `Productos de ${categoria.nombre}`;

        let html = '';

        if (productos.length === 0) {
            html = `
            <div class="text-center py-8">
                <i class="fas fa-box-open text-gray-300 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-700">No hay productos</h3>
                <p class="text-gray-500 mt-2">No se encontraron productos en esta categoría</p>
            </div>
        `;
        } else {
            html = `
            <div class="mb-4">
                <div class="flex justify-between items-center mb-2">
                    <p class="text-sm text-gray-600">
                        <strong>${productos.length}</strong> producto${productos.length !== 1 ? 's' : ''} encontrado${productos.length !== 1 ? 's' : ''}
                    </p>
                    <span class="categoria-badge ${getCategoriaClass(categoria.id)} text-xs">
                        ${categoria.nombre}
                    </span>
                </div>
                <p class="text-xs text-gray-500">${categoria.descripcion || 'Sin descripción'}</p>
            </div>

            <div class="overflow-y-auto max-h-96">
                <div class="grid grid-cols-1 gap-3">
                    ${productos.map(producto => `
                        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-medium text-gray-900">${producto.nombre}</h4>
                                <span class="text-sm font-semibold text-pink-600">S/ ${parseFloat(producto.precio).toFixed(2)}</span>
                            </div>
                            
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">${producto.descripcion || 'Sin descripción'}</p>
                            
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-4">
                                    <span class="text-xs px-2 py-1 rounded-full ${producto.stock <= 5 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'}">
                                        Stock: ${producto.stock}
                                    </span>
                                    <span class="text-xs px-2 py-1 rounded-full ${producto.activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                        ${producto.activo ? 'Activo' : 'Inactivo'}
                                    </span>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="editarProductoDesdeModal(${producto.id})" class="text-blue-600 hover:text-blue-800 text-xs">
                                        <i class="fas fa-edit mr-1"></i>Editar
                                    </button>
                                    <button onclick="eliminarProducto(${producto.id})" class="text-red-600 hover:text-red-800 text-xs">
                                        <i class="fas fa-trash mr-1"></i>Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
        }

        document.getElementById('contenido-productos-categoria').innerHTML = html;

        setTimeout(() => {
            document.getElementById('modal-productos-categoria').classList.add('active');
        }, 10);
    }

    // Función especial para editar desde el modal de productos
    async function editarProductoDesdeModal(productoId) {
        // Cerrar primero el modal actual
        document.getElementById('modal-productos-categoria').classList.remove('active');

        // Pequeño delay antes de abrir el modal de edición
        setTimeout(() => {
            editarProducto(productoId);
        }, 300);
    }

    // Función auxiliar para obtener clase de categoría
    function getCategoriaClass(categoriaId) {
        switch (parseInt(categoriaId)) {
            case 1:
                return 'categoria-fragancias';
            case 2:
                return 'categoria-cremas';
            case 3:
                return 'categoria-splash';
            default:
                return 'categoria-fragancias';
        }
    }

    // =============================================
    // 🗑️ FUNCIONES DE LA PAPELERA
    // =============================================

    // Cargar productos eliminados
    async function cargarPapelera() {
        try {
            mostrarLoading('Cargando papelera...');

            const response = await fetch('index.php?view=api-listar-eliminados');
            const result = await response.json();
            ocultarLoading();

            if (result.success) {
                actualizarVistaPapelera(result.data);
                actualizarContadorPapelera(result.total);
            } else {
                throw new Error(result.message || 'Error al cargar papelera');
            }
        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al cargar papelera: ' + error.message, 'error');
        }
    }

    // Actualizar vista de la papelera
    function actualizarVistaPapelera(productos) {
        const tabla = document.getElementById('tabla-papelera');
        const papeleraVacia = document.getElementById('papelera-vacia');

        if (productos.length === 0) {
            tabla.innerHTML = '';
            papeleraVacia.classList.remove('hidden');
            return;
        }

        papeleraVacia.classList.add('hidden');

        let html = '';
        productos.forEach(producto => {
            html += `
        <tr class="fade-in">
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-lg flex items-center justify-center">
                        <i class="fas fa-cube text-gray-600"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">${producto.nombre}</div>
                        <div class="text-sm text-gray-500 truncate max-w-xs">${producto.descripcion || 'Sin descripción'}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="categoria-badge ${getCategoriaClass(producto.categoria_id)}">
                    ${producto.categoria_nombre}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">S/ ${parseFloat(producto.precio).toFixed(2)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${producto.stock} unidades</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Eliminado</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button class="text-green-600 hover:text-green-900 mr-3" onclick="restaurarProducto(${producto.id})">
                    <i class="fas fa-undo mr-1"></i>Restaurar
                </button>
                <button class="text-red-600 hover:text-red-900" onclick="eliminarPermanentemente(${producto.id})">
                    <i class="fas fa-trash mr-1"></i>Eliminar
                </button>
            </td>
        </tr>
        `;
        });

        tabla.innerHTML = html;
    }

    // Actualizar contador de papelera
    function actualizarContadorPapelera(total) {
        document.getElementById('papelera-badge').textContent = total;
        document.getElementById('total-papelera-text').textContent = total;
    }

    // Restaurar producto
    async function restaurarProducto(productoId) {
        if (!confirm('¿Estás seguro de que quieres restaurar este producto?')) {
            return;
        }

        try {
            mostrarLoading('Restaurando producto...');

            const formData = new FormData();
            formData.append('id', productoId);

            const response = await fetch('index.php?view=api-restaurar-producto', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            ocultarLoading();

            if (result.success) {
                mostrarNotificacion('✅ Producto restaurado exitosamente. Recargando...', 'success');
                // Recargar la página después de 1 segundo para ver los cambios
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                throw new Error(result.message || 'Error al restaurar producto');
            }
        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al restaurar producto: ' + error.message, 'error');
        }
    }

    // Eliminar permanentemente
    async function eliminarPermanentemente(productoId) {
        if (!confirm('¿Estás seguro de que quieres eliminar este producto PERMANENTEMENTE? Esta acción NO se puede deshacer.')) {
            return;
        }

        try {
            mostrarLoading('Eliminando permanentemente...');

            const formData = new FormData();
            formData.append('id', productoId);

            const response = await fetch('index.php?view=api-eliminar-permanentemente-producto', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            ocultarLoading();

            if (result.success) {
                mostrarNotificacion('✅ Producto eliminado permanentemente', 'success');
                cargarPapelera();
                cargarContadorPapelera();
            } else {
                throw new Error(result.message || 'Error al eliminar producto');
            }
        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al eliminar producto: ' + error.message, 'error');
        }
    }

    // Vaciar papelera
    async function vaciarPapelera() {
        if (!confirm('¿Estás seguro de que quieres VACIAR la papelera? Todos los productos serán eliminados PERMANENTEMENTE. Esta acción NO se puede deshacer.')) {
            return;
        }

        try {
            mostrarLoading('Vaciando papelera...');

            const response = await fetch('index.php?view=api-vaciar-papelera', {
                method: 'POST'
            });

            const result = await response.json();
            ocultarLoading();

            if (result.success) {
                mostrarNotificacion('✅ Papelera vaciada exitosamente', 'success');
                cargarPapelera();
                cargarContadorPapelera();
            } else {
                throw new Error(result.message || 'Error al vaciar papelera');
            }
        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al vaciar papelera: ' + error.message, 'error');
        }
    }

    // Restaurar todos los productos
    async function restaurarTodos() {
        try {
            mostrarLoading('Verificando papelera...');

            const response = await fetch('index.php?view=api-listar-eliminados');
            const result = await response.json();
            ocultarLoading();

            if (result.success && result.data.length > 0) {
                if (!confirm(`¿Estás seguro de que quieres restaurar TODOS los productos (${result.data.length}) de la papelera?`)) {
                    return;
                }

                let restaurations = 0;
                let errors = 0;

                mostrarLoading(`Restaurando productos... (0/${result.data.length})`);

                for (const producto of result.data) {
                    try {
                        const formData = new FormData();
                        formData.append('id', producto.id);

                        const restoreResponse = await fetch('index.php?view=api-restaurar-producto', {
                            method: 'POST',
                            body: formData
                        });

                        const restoreResult = await restoreResponse.json();

                        if (restoreResult.success) {
                            restaurations++;
                        } else {
                            errors++;
                        }

                        // Actualizar loading
                        document.querySelector('.loading-overlay span').textContent =
                            `Restaurando productos... (${restaurations + errors}/${result.data.length})`;
                    } catch (error) {
                        errors++;
                    }
                }

                ocultarLoading();

                if (restaurations > 0) {
                    if (errors === 0) {
                        mostrarNotificacion(`✅ ¡Éxito! ${restaurations} productos restaurados correctamente`, 'success');
                    } else {
                        mostrarNotificacion(`⚠️ ${restaurations} productos restaurados, ${errors} errores`, 'warning');
                    }

                    // Recargar la página para ver los cambios en todas las pestañas
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);

                } else {
                    mostrarNotificacion('❌ No se pudo restaurar ningún producto', 'error');
                }
            } else {
                mostrarNotificacion('ℹ️ No hay productos en la papelera para restaurar', 'info');
            }
        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al restaurar productos: ' + error.message, 'error');
        }
    }

    // Cargar contador de papelera
    async function cargarContadorPapelera() {
        try {
            const response = await fetch('index.php?view=api-contar-papelera');
            const result = await response.json();

            if (result.success) {
                actualizarContadorPapelera(result.total);
            }
        } catch (error) {
            console.error('Error al cargar contador de papelera:', error);
        }
    }

    // Cerrar modales al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.classList.remove('active');
        }
    });
</script>