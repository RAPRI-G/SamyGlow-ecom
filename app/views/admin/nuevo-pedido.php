<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Evitar que el navegador guarde en caché el dashboard
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
require __DIR__ . "/../templates/footer.php";
?>
<style>
    /* Mantén tu CSS (igual que el que ya tienes) */
    .product-card { border: 2px solid transparent; transition: all 0.3s ease; cursor: pointer; }
    .product-card:hover { border-color: #f472b6; }
    .product-card.selected { border-color: #f472b6; background-color: #fdf2f8; }
    .promo-combo { border: 2px dashed #f472b6; background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 100%); }
    .quantity-btn { width: 32px; height: 32px; border: 1px solid #d1d5db; background: white; border-radius: 6px; display: flex; align-items: center; justify-content: center; cursor: pointer; }
    .quantity-btn:hover { background: #f3f4f6; }
    .quantity-input { width: 50px; text-align: center; border: 1px solid #d1d5db; border-radius: 6px; padding: 4px; }
    .cart-item { transition: all 0.3s ease; }
    .promo-applied { background-color: #f0fdf4; border-left: 4px solid #10b981; }
    .new-client-form { max-height: 0; overflow: hidden; transition: max-height 0.5s ease; }
    .new-client-form.open { max-height: 500px; }
    .promo-counter { background-color: #f472b6; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: bold; }
    .notes-section { max-height: 0; overflow: hidden; transition: max-height 0.5s ease; }
    .notes-section.open { max-height: 200px; }
</style>

<!-- CONTENIDO PRINCIPAL -->
<main class="flex-1 overflow-y-auto p-6">
    <div class="gap-6 mb-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Nuevo Pedido</h1>
                <p class="text-gray-600">Crear un nuevo pedido para un cliente</p>
            </div>
            <div class="flex space-x-3">
                <button class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600" id="cancelBtn">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Columna Izquierda - Información del Cliente y Productos -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Información del Cliente -->
                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-user mr-2 text-pink-500"></i>Información del Cliente
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar Cliente Existente</label>
                            <div class="relative">
                                <input type="text"
                                    placeholder="DNI, nombre o email..."
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                    id="searchClient">
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            </div>
                            <!-- Sugerencias de clientes -->
                            <div id="clientSuggestions" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                <!-- Las sugerencias se cargarán aquí -->
                            </div>
                        </div>
                        <div class="flex items-end">
                            <button class="px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 flex items-center" id="toggleClientForm">
                                <i class="fas fa-user-plus mr-2"></i>Nuevo Cliente
                            </button>
                        </div>
                    </div>

                    <!-- Formulario Nuevo Cliente -->
                    <div class="new-client-form" id="newClientForm">
                        <div class="border-t pt-4 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombres *</label>
                                    <input type="text" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500" id="clientNombres" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Apellidos *</label>
                                    <input type="text" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500" id="clientApellidos" required>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">DNI *</label>
                                    <input type="text" maxlength="8" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500" id="clientDni" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                                    <input type="text" maxlength="9" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500" id="clientTelefono">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico *</label>
                                <input type="email" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500" id="clientCorreo" required>
                            </div>
                            <div class="flex justify-end">
                                <button class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 flex items-center" id="saveClientBtn">
                                    <i class="fas fa-save mr-2"></i>Guardar Cliente
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Cliente Seleccionado -->
                    <div id="selectedClient" class="hidden p-4 bg-green-50 rounded-lg border border-green-200 mt-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-semibold text-green-800" id="clientName"></h4>
                                <p class="text-sm text-green-600" id="clientInfo"></p>
                            </div>
                            <button class="text-red-500 hover:text-red-700" id="removeClient">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Notas del Pedido -->
                <div class="card p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-sticky-note mr-2 text-pink-500"></i>Notas del Pedido
                        </h3>
                        <button class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm flex items-center" id="toggleNotes">
                            <i class="fas fa-edit mr-1"></i>Agregar Notas
                        </button>
                    </div>

                    <!-- Sección de Notas (expandible) -->
                    <div class="notes-section" id="notesSection">
                        <div class="border-t pt-4">
                            <textarea
                                id="orderNotes"
                                placeholder="Escribe aquí cualquier nota especial para este pedido (instrucciones de entrega, regalo, preferencias del cliente, etc.)..."
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent resize-none"
                                rows="4"
                                maxlength="500"></textarea>
                            <div class="flex justify-between items-center mt-2">
                                <span class="text-xs text-gray-500" id="notesCounter">0/500 caracteres</span>
                                <div class="flex space-x-2">
                                    <button class="px-3 py-1 bg-gray-500 text-white rounded text-sm hover:bg-gray-600" id="clearNotes">
                                        Limpiar
                                    </button>
                                    <button class="px-3 py-1 bg-pink-500 text-white rounded text-sm hover:bg-pink-600" id="saveNotes">
                                        Guardar Notas
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notas Guardadas (se muestra cuando hay notas) -->
                    <div id="savedNotes" class="hidden mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="font-semibold text-blue-800 text-sm mb-1">
                                    <i class="fas fa-check-circle mr-1"></i>Notas del Pedido
                                </h4>
                                <p class="text-blue-700 text-sm" id="notesPreview"></p>
                            </div>
                            <button class="text-blue-500 hover:text-blue-700 ml-2" id="editNotes">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Promoción Especial - MÚLTIPLES VECES -->
                <div class="card p-6 promo-combo">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-pink-700">
                                <i class="fas fa-crown mr-2"></i>Promoción Especial
                                <span class="promo-counter ml-2" id="promoCount">0</span>
                            </h3>
                            <p class="text-pink-600">¡Lleva cualquier fragancia (S/65) + cualquier crema corporal (S/65) por solo S/ 125!</p>
                            <p class="text-sm text-pink-500 mt-1">Ahorras S/ 5 en cada combo - ¡Agrega tantos combos como quieras!</p>
                        </div>
                        <span class="badge badge-promo">Ahorra S/ 5 c/u</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- Selección de Fragancia -->
                        <div>
                            <label class="block text-sm font-medium text-pink-700 mb-2">Seleccionar Fragancia (S/65)</label>
                            <select class="w-full p-3 border border-pink-300 rounded-lg focus:ring-2 focus:ring-pink-500" id="selectFragancia">
                                <option value="">Selecciona una fragancia...</option>
                                <!-- Opciones se rellenarán desde JS si quieres; de momento están hardcodeadas -->
                                <option value="1">Bare Vanilla Fragrance Mist</option>
                                <option value="2">Velvet Petals Fragrance Mist</option>
                                <option value="3">Pure Seduction Fragrance Mist</option>
                                <option value="4">Love Spell Fragrance Mist</option>
                                <option value="5">Aqua Kiss Fragrance Mist</option>
                                <option value="6">Midnight Bloom Fragrance Mist</option>
                            </select>
                        </div>

                        <!-- Selección de Crema -->
                        <div>
                            <label class="block text-sm font-medium text-pink-700 mb-2">Seleccionar Crema Corporal (S/65)</label>
                            <select class="w-full p-3 border border-pink-300 rounded-lg focus:ring-2 focus:ring-pink-500" id="selectCrema">
                                <option value="">Selecciona una crema...</option>
                                <option value="13">Bare Vanilla Body Lotion</option>
                                <option value="14">Velvet Petals Body Cream</option>
                                <option value="15">Pure Seduction Body Lotion</option>
                                <option value="16">Love Spell Moisturizing Lotion</option>
                                <option value="17">Aqua Kiss Hydrating Lotion</option>
                                <option value="18">Coconut Passion Body Butter</option>
                            </select>
                        </div>
                    </div>

                    <!-- Cantidad de Combos -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-pink-700 mb-2">Cantidad de Combos</label>
                        <div class="flex items-center space-x-3">
                            <button class="quantity-btn decrease-promo">-</button>
                            <input type="number" value="1" min="1" max="10" class="quantity-input" id="promoQuantity">
                            <button class="quantity-btn increase-promo">+</button>
                            <span class="text-sm text-gray-600 ml-2">combos</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-sm text-gray-600">Precio normal: </span>
                            <span class="text-sm text-gray-500 line-through" id="precioNormal">S/ 130.00</span>
                            <span class="text-sm text-pink-600 ml-2">Precio promoción: S/ 125.00 c/u</span>
                        </div>
                        <button class="px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 flex items-center" id="addPromoBtn">
                            <i class="fas fa-tag mr-2"></i>Agregar Promoción
                        </button>
                    </div>
                </div>

                <!-- Catálogo de Productos Individuales -->
                <div class="card p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-cubes mr-2 text-pink-500"></i>Productos Individuales
                        </h3>
                        <div class="flex space-x-2">
                            <select class="border border-gray-300 rounded-lg px-3 py-1" id="categoryFilter">
                                <option value="all">Todas las categorías</option>
                                <option value="1">Fragancias</option>
                                <option value="2">Cremas Corporales</option>
                                <option value="3">Body Splash</option>
                            </select>
                            <div class="relative">
                                <input type="text"
                                    placeholder="Buscar producto..."
                                    class="pl-10 pr-4 py-1 border border-gray-300 rounded-lg w-64"
                                    id="searchProduct">
                                <i class="fas fa-search absolute left-3 top-2 text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de Productos -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-96 overflow-y-auto" id="productsGrid">
                        <!-- Los productos se cargarán dinámicamente -->
                    </div>
                </div>
            </div>

            <!-- Columna Derecha - Resumen del Pedido -->
            <div class="space-y-6">
                <!-- Resumen del Pedido -->
                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-shopping-cart mr-2 text-pink-500"></i>Resumen del Pedido
                    </h3>

                    <!-- Items del Carrito -->
                    <div id="cartItems" class="space-y-3 mb-4 max-h-64 overflow-y-auto">
                        <div class="text-center text-gray-500 py-8">
                            <i class="fas fa-shopping-cart text-3xl mb-2"></i>
                            <p>No hay productos agregados</p>
                        </div>
                    </div>

                    <!-- Subtotal -->
                    <div class="border-t pt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-semibold" id="subtotal">S/ 0.00</span>
                        </div>

                        <!-- Descuento por Promoción -->
                        <div class="flex justify-between text-sm" id="discountRow" style="display: none;">
                            <span class="text-gray-600">Descuento promoción:</span>
                            <span class="font-semibold text-green-600" id="discount">- S/ 0.00</span>
                        </div>

                        <!-- Total -->
                        <div class="flex justify-between text-lg font-bold border-t pt-2">
                            <span>Total:</span>
                            <span id="total">S/ 0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Método de Pago -->
                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-credit-card mr-2 text-pink-500"></i>Método de Pago
                    </h3>

                    <div class="space-y-3">
                        <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment" value="1" class="text-pink-500" checked>
                            <i class="fas fa-mobile-alt text-green-500"></i>
                            <span>Yape</span>
                        </label>

                        <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment" value="2" class="text-pink-500">
                            <i class="fas fa-mobile-alt text-blue-500"></i>
                            <span>Plin</span>
                        </label>

                        <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment" value="3" class="text-pink-500">
                            <i class="fas fa-credit-card text-purple-500"></i>
                            <span>Tarjeta de Crédito</span>
                        </label>

                        <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment" value="4" class="text-pink-500">
                            <i class="fas fa-university text-yellow-500"></i>
                            <span>Transferencia Bancaria</span>
                        </label>

                        <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment" value="5" class="text-pink-500">
                            <i class="fas fa-money-bill-wave text-green-500"></i>
                            <span>Efectivo</span>
                        </label>
                    </div>
                </div>

                <!-- Botón Finalizar -->
                <button class="w-full py-4 bg-pink-500 text-white rounded-lg hover:bg-pink-600 font-semibold text-lg" id="finalizeOrder">
                    <i class="fas fa-check-circle mr-2"></i>Finalizar Pedido
                </button>
            </div>
        </div>
    </div>
</main>

<!-- Cargar el JS (ruta robusta) -->
<script src="<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') ?>/assets/js/nuevo-pedido.js"></script>
