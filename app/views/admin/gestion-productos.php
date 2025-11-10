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
                            <div class="relative">
                                <input type="text" id="buscador-productos" placeholder="Buscar producto..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 w-64">
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            </div>
                            <select id="filtro-categoria" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                                <option value="">Todas las categorías</option>
                                <?php foreach($categorias as $categoria): ?>
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
                                <?php foreach($productos as $producto): ?>
                                <tr class="fade-in">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-pink-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-cube text-pink-600"></i>
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
                                        if($producto['categoria_id'] == 1) $categoriaClass = 'categoria-fragancias';
                                        elseif($producto['categoria_id'] == 2) $categoriaClass = 'categoria-cremas';
                                        else $categoriaClass = 'categoria-splash';
                                        ?>
                                        <span class="categoria-badge <?= $categoriaClass ?>"><?= htmlspecialchars($producto['categoria_nombre']) ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">S/ <?= number_format($producto['precio'], 2) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $producto['stock'] ?> unidades</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if($producto['activo']): ?>
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
                        <?php foreach($categorias as $categoria): ?>
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
                            <p class="text-gray-600 text-sm mb-4"><?= htmlspecialchars($categoria['descripcion'] ?? 'Sin descripción') ?></p>
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
                                    <?php foreach($productos as $producto): ?>
                                    <tr class="fade-in <?= $producto['stock'] <= 5 ? 'stock-bajo' : 'stock-normal' ?>">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($producto['nombre']) ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($producto['categoria_nombre']) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $producto['stock'] ?> unidades</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">5 unidades</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if($producto['stock'] <= 5): ?>
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
                        <button class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg transition-colors" id="btn-notificar-reposicion">
                            <i class="fas fa-bell mr-2"></i>Notificar Reposición
                        </button>
                    </div>
                    
                    <div class="p-6">
                        <!-- Información de alerta -->
                        <?php if(count($productosStockBajo) > 0): ?>
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
                            <?php foreach($productosStockBajo as $producto): ?>
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
                                    $bgColor = $porcentajeStock <= 25 ? 'bg-red-600' : 
                                              ($porcentajeStock <= 50 ? 'bg-orange-500' : 'bg-yellow-400');
                                    ?>
                                    <div class="<?= $bgColor ?> h-2 rounded-full" style="width: <?= $porcentajeStock ?>%"></div>
                                </div>
                                <div class="text-xs text-red-600 mb-3">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    <?= $porcentajeStock <= 25 ? 'Stock crítico - Reposición urgente' : 
                                      ($porcentajeStock <= 50 ? 'Stock bajo - Considerar reposición' : 
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
                                    <?php foreach($categorias as $categoria): ?>
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
                                <div class="border-2 border-dashed border-gray-300 rounded-md p-4 text-center">
                                    <i class="fas fa-cloud-upload-alt text-gray-400 text-2xl mb-2"></i>
                                    <p class="text-sm text-gray-600">Arrastra una imagen o haz clic para subir</p>
                                    <input type="file" id="producto-imagen" class="hidden" accept="image/*">
                                    <button type="button" onclick="document.getElementById('producto-imagen').click()" class="mt-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded transition-colors text-sm">
                                        Seleccionar Imagen
                                    </button>
                                </div>
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
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
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

#tabla-productos tr:nth-child(1) { animation-delay: 0.1s; }
#tabla-productos tr:nth-child(2) { animation-delay: 0.2s; }
#tabla-productos tr:nth-child(3) { animation-delay: 0.3s; }
#tabla-productos tr:nth-child(4) { animation-delay: 0.4s; }

/* Efectos para las tarjetas de categorías */
#grid-categorias > div {
    transition: all 0.3s ease;
    cursor: pointer;
}

#grid-categorias > div:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Efectos para los productos con stock bajo */
#grid-stock-bajo > div {
    transition: all 0.3s ease;
}

#grid-stock-bajo > div:hover {
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
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
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
    <div class="modal-content" style="max-width: 800px;">
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
        });
    });

    // Toggle menu móvil
    document.getElementById('menuToggle').addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('hidden');
    });
}

function configurarEventListeners() {
    // Buscador de productos
    document.getElementById('buscador-productos').addEventListener('input', filtrarProductos);
    document.getElementById('filtro-categoria').addEventListener('change', filtrarProductos);
    
    // Cerrar modales
    document.getElementById('cerrar-modal-producto').addEventListener('click', function() {
        document.getElementById('modal-editar-producto').classList.remove('active');
    });
    
    document.getElementById('cerrar-modal-categoria').addEventListener('click', function() {
        document.getElementById('modal-editar-categoria').classList.remove('active');
    });
    
    document.getElementById('cerrar-modal-productos-categoria').addEventListener('click', function() {
        document.getElementById('modal-productos-categoria').classList.remove('active');
    });
    
    // Botón nueva categoría
    document.getElementById('btn-nueva-categoria').addEventListener('click', function() {
        mostrarNotificacion('Funcionalidad de nueva categoría en desarrollo', 'info');
    });
    
    // Botón notificar reposición
    document.getElementById('btn-notificar-reposicion').addEventListener('click', function() {
        mostrarNotificacion('Notificación de reposición enviada a proveedores', 'success');
    });
}

// =============================================
// 🔍 FUNCIONES DE BÚSQUEDA Y FILTRADO
// =============================================

function filtrarProductos() {
    const searchTerm = document.getElementById('buscador-productos').value.toLowerCase();
    const categoriaFiltro = document.getElementById('filtro-categoria').value;
    const filas = document.querySelectorAll('#tabla-productos tr');
    
    let productosVisibles = 0;
    
    filas.forEach(fila => {
        const nombre = fila.querySelector('td:nth-child(1) .text-sm.font-medium').textContent.toLowerCase();
        const descripcion = fila.querySelector('td:nth-child(1) .text-gray-500').textContent.toLowerCase();
        const categoria = fila.querySelector('td:nth-child(2) .categoria-badge').textContent.toLowerCase();
        const categoriaId = fila.querySelector('td:nth-child(2) .categoria-badge').classList.contains('categoria-fragancias') ? '1' :
                           fila.querySelector('td:nth-child(2) .categoria-badge').classList.contains('categoria-cremas') ? '2' : '3';
        
        const coincideBusqueda = !searchTerm || 
            nombre.includes(searchTerm) ||
            descripcion.includes(searchTerm) ||
            categoria.includes(searchTerm);
        
        const coincideCategoria = !categoriaFiltro || categoriaId === categoriaFiltro;
        
        if (coincideBusqueda && coincideCategoria) {
            fila.style.display = '';
            productosVisibles++;
        } else {
            fila.style.display = 'none';
        }
    });
    
    // Mostrar/ocultar mensaje de no resultados
    const sinResultados = document.getElementById('sin-resultados');
    if (productosVisibles === 0) {
        sinResultados.classList.remove('hidden');
    } else {
        sinResultados.classList.add('hidden');
    }
}

// =============================================
// 🛍️ FUNCIONES CRUD DE PRODUCTOS
// =============================================

async function guardarNuevoProducto(e) {
    e.preventDefault();
    
    // Obtener valores del formulario
    const nombre = document.getElementById('producto-nombre').value;
    const descripcion = document.getElementById('producto-descripcion').value;
    const categoria_id = document.getElementById('producto-categoria').value;
    const precio = parseFloat(document.getElementById('producto-precio').value);
    const stock = parseInt(document.getElementById('producto-stock').value);
    const activo = document.getElementById('producto-activo').checked ? 1 : 0;
    
    // Validaciones básicas
    if (!nombre || !categoria_id || !precio || isNaN(stock)) {
        mostrarNotificacion('Por favor completa todos los campos obligatorios', 'error');
        return;
    }
    
    if (precio <= 0) {
        mostrarNotificacion('El precio debe ser mayor a 0', 'error');
        return;
    }
    
    if (stock < 0) {
        mostrarNotificacion('El stock no puede ser negativo', 'error');
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('nombre', nombre);
        formData.append('descripcion', descripcion);
        formData.append('categoria_id', categoria_id);
        formData.append('precio', precio);
        formData.append('stock', stock);
        formData.append('activo', activo);
        
        const response = await fetch('index.php?view=api-guardar-producto', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            mostrarNotificacion('Producto creado exitosamente', 'success');
            limpiarFormularioProducto();
            // Recargar la página para ver los cambios
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        mostrarNotificacion('Error al crear producto: ' + error.message, 'error');
    }
}

async function editarProducto(productoId) {
    try {
        const response = await fetch(`index.php?view=api-obtener-producto&id=${productoId}`);
        const result = await response.json();
        
        if (result.success) {
            const producto = result.data;
            mostrarModalEditarProducto(producto);
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        mostrarNotificacion('Error al cargar producto: ' + error.message, 'error');
    }
}

function mostrarModalEditarProducto(producto) {
    let html = `
    <form id="form-editar-producto" class="space-y-4" onsubmit="guardarCambiosProducto(event, ${producto.id})">
        <input type="hidden" id="edit-producto-id" value="${producto.id}">
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Producto</label>
            <input type="text" id="edit-producto-nombre" value="${producto.nombre}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
            <textarea id="edit-producto-descripcion" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" rows="3">${producto.descripcion || ''}</textarea>
        </div>
        
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                <select id="edit-producto-categoria" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
                    <option value="1" ${producto.categoria_id == 1 ? 'selected' : ''}>Fragancias</option>
                    <option value="2" ${producto.categoria_id == 2 ? 'selected' : ''}>Cremas Corporales</option>
                    <option value="3" ${producto.categoria_id == 3 ? 'selected' : ''}>Body Splash</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Precio (S/)</label>
                <input type="number" id="edit-producto-precio" value="${producto.precio}" step="0.01" min="0" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stock Actual</label>
                <input type="number" id="edit-producto-stock" value="${producto.stock}" min="0" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stock Mínimo</label>
                <input type="number" id="edit-producto-stock-minimo" value="5" min="0" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
            </div>
        </div>
        
        <div class="flex items-center">
            <input type="checkbox" id="edit-producto-activo" ${producto.activo ? 'checked' : ''} class="rounded border-gray-300 text-pink-600 focus:ring-pink-500">
            <label for="edit-producto-activo" class="ml-2 text-sm text-gray-700">Producto activo</label>
        </div>
        
        <div class="flex justify-end space-x-4 pt-4">
            <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-4 rounded transition-colors" onclick="document.getElementById('modal-editar-producto').classList.remove('active')">
                Cancelar
            </button>
            <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white font-medium py-2 px-4 rounded transition-colors">
                Guardar Cambios
            </button>
        </div>
    </form>
    `;
    
    document.getElementById('contenido-modal-producto').innerHTML = html;
    document.getElementById('modal-editar-producto').classList.add('active');
}

async function guardarCambiosProducto(e, productoId) {
    e.preventDefault();
    
    // Obtener valores del formulario
    const nombre = document.getElementById('edit-producto-nombre').value;
    const descripcion = document.getElementById('edit-producto-descripcion').value;
    const categoria_id = parseInt(document.getElementById('edit-producto-categoria').value);
    const precio = parseFloat(document.getElementById('edit-producto-precio').value);
    const stock = parseInt(document.getElementById('edit-producto-stock').value);
    const activo = document.getElementById('edit-producto-activo').checked ? 1 : 0;
    
    // Validaciones
    if (!nombre || !precio || isNaN(stock)) {
        mostrarNotificacion('Por favor completa todos los campos obligatorios', 'error');
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('id', productoId);
        formData.append('nombre', nombre);
        formData.append('descripcion', descripcion);
        formData.append('categoria_id', categoria_id);
        formData.append('precio', precio);
        formData.append('stock', stock);
        formData.append('activo', activo);
        
        const response = await fetch('index.php?view=api-editar-producto', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            mostrarNotificacion('Producto actualizado exitosamente', 'success');
            document.getElementById('modal-editar-producto').classList.remove('active');
            // Recargar la página para ver los cambios
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        mostrarNotificacion('Error al actualizar producto: ' + error.message, 'error');
    }
}

async function eliminarProducto(productoId) {
    if (!confirm('¿Estás seguro de que quieres eliminar este producto? Esta acción no se puede deshacer.')) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('id', productoId);
        
        const response = await fetch('index.php?view=api-eliminar-producto', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            mostrarNotificacion('Producto eliminado exitosamente', 'success');
            // Recargar la página para ver los cambios
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        mostrarNotificacion('Error al eliminar producto: ' + error.message, 'error');
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
            const formData = new FormData();
            formData.append('id', productoId);
            formData.append('stock', nuevoStock);
            
            const response = await fetch('index.php?view=api-actualizar-stock', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                mostrarNotificacion(`Stock de ${producto.nombre} actualizado a ${nuevoStock}`, 'success');
                // Recargar la página para ver los cambios
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            mostrarNotificacion('Error al actualizar stock: ' + error.message, 'error');
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
            const formData = new FormData();
            formData.append('id', productoId);
            formData.append('stock', nuevoStock);
            
            const response = await fetch('index.php?view=api-actualizar-stock', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                mostrarNotificacion(`Se agregaron ${cantidad} unidades a ${producto.nombre}`, 'success');
                // Recargar la página para ver los cambios
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            mostrarNotificacion('Error al agregar stock: ' + error.message, 'error');
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
            throw new Error(result.message);
        }
    } catch (error) {
        mostrarNotificacion('Error al obtener producto: ' + error.message, 'error');
        return null;
    }
}

// =============================================
// 🏷️ FUNCIONES DE CATEGORÍAS
// =============================================

function editarCategoria(categoriaId) {
    mostrarNotificacion('Funcionalidad de edición de categorías en desarrollo', 'info');
}

function verProductosCategoria(categoriaId) {
    // Navegar a la pestaña de productos y filtrar por categoría
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
        if (btn.getAttribute('data-tab') === 'lista-productos') {
            btn.classList.add('active');
        }
    });
    
    document.querySelectorAll('.tab-panel').forEach(panel => {
        panel.classList.remove('active');
    });
    document.getElementById('lista-productos-panel').classList.add('active');
    
    // Aplicar filtro de categoría
    document.getElementById('filtro-categoria').value = categoriaId;
    filtrarProductos();
}

// =============================================
// 🛠️ FUNCIONES UTILITARIAS
// =============================================

function limpiarFormularioProducto() {
    document.getElementById('form-nuevo-producto').reset();
    document.getElementById('producto-stock-minimo').value = '5';
    document.getElementById('producto-activo').checked = true;
}

function mostrarNotificacion(mensaje, tipo) {
    // Crear elemento de notificación
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
    
    // Agregar al documento
    document.body.appendChild(notificacion);
    
    // Remover después de 3 segundos
    setTimeout(() => {
        notificacion.classList.add('opacity-0');
        setTimeout(() => {
            if (document.body.contains(notificacion)) {
                document.body.removeChild(notificacion);
            }
        }, 300);
    }, 3000);
}

// Cerrar modales al hacer clic fuera
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.classList.remove('active');
    }
});
</script>