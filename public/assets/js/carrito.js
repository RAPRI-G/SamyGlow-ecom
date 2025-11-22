// carrito.js - Lógica específica de la página del carrito
// DEPENDE DE: cart-manager.js (debe cargarse ANTES)

/* ============================
   🔹 INICIALIZACIÓN
============================ */
document.addEventListener('DOMContentLoaded', () => {
  renderizarCarrito();
  cargarProductosRecomendados();
});

/* ============================
   🔹 RENDERIZAR CARRITO
============================ */
function renderizarCarrito() {
  const container = document.getElementById('cart-content');
  if (!container) return;

  const carrito = obtenerCarrito(); // Desde cart-manager.js

  if (carrito.length === 0) {
    container.innerHTML = `
      <div class="text-center py-16">
        <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-2xl font-semibold text-gray-700 mb-2">Tu carrito está vacío</h3>
        <p class="text-gray-500 mb-6">¡Agrega algunos productos increíbles!</p>
        <a href="tienda.html" class="btn-primary inline-block">
          <i class="fas fa-store mr-2"></i>Ir a la tienda
        </a>
      </div>
    `;
    return;
  }

  container.innerHTML = `
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h2 class="text-2xl font-bold mb-6 flex items-center">
            <i class="fas fa-shopping-bag mr-3" style="color: var(--rosa-neon);"></i>
            Productos en tu carrito
          </h2>
          <div id="cart-items-list" class="space-y-4"></div>
        </div>
      </div>

      <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-24">
          <h3 class="text-xl font-bold mb-4">Resumen del pedido</h3>
          
          <div class="space-y-3 mb-4 pb-4 border-b">
            <div class="flex justify-between text-gray-600">
              <span>Subtotal:</span>
              <span id="subtotal">S/ 0.00</span>
            </div>
            <div class="flex justify-between text-gray-600">
              <span>Envío:</span>
              <span id="shipping">S/ 10.00</span>
            </div>
            <div class="flex justify-between text-sm text-green-600">
              <span><i class="fas fa-truck mr-1"></i>Envío gratis desde S/ 150</span>
            </div>
          </div>

          <div class="flex justify-between text-xl font-bold mb-6">
            <span>Total:</span>
            <span id="total" style="color: var(--rosa-neon);">S/ 0.00</span>
          </div>

          <button onclick="procederAlPago()" class="btn-primary w-full mb-3 btn-pulse">
            <i class="fas fa-credit-card mr-2"></i>Proceder al Pago
          </button>

          <a href="tienda.html" class="block text-center text-gray-600 hover:text-[#FF1493] transition">
            <i class="fas fa-arrow-left mr-2"></i>Seguir comprando
          </a>
        </div>
      </div>
    </div>
  `;

  renderizarItems();
  actualizarTotales();
}

/* ============================
   🔹 RENDERIZAR ITEMS
============================ */
function renderizarItems() {
  const itemsList = document.getElementById('cart-items-list');
  if (!itemsList) return;

  const carrito = obtenerCarrito(); // Desde cart-manager.js
  itemsList.innerHTML = '';

  carrito.forEach(item => {
    const itemDiv = document.createElement('div');
    itemDiv.className = 'flex flex-col sm:flex-row gap-4 p-4 border-2 rounded-xl hover:border-[#FF1493] transition';
    
    const subtotal = item.precio * item.cantidad;
    const imagenSegura = item.imagen && item.imagen.trim() !== '' 
      ? escapeHtml(item.imagen) 
      : 'assets/img/logo.png';

    itemDiv.innerHTML = `
      <div class="w-full sm:w-24 h-24 bg-gradient-to-b from-pink-50 to-white rounded-lg flex items-center justify-center flex-shrink-0">
        <img src="${imagenSegura}" alt="${escapeHtml(item.nombre)}" 
             onerror="this.src='assets/img/logo.png'"
             class="max-h-20 max-w-20 object-contain">
      </div>

      <div class="flex-1">
        <h4 class="font-semibold text-lg mb-1">${escapeHtml(item.nombre)}</h4>
        <p class="text-gray-600 mb-2">S/ ${item.precio.toFixed(2)} c/u</p>
        
        <div class="flex items-center gap-3">
          <span class="text-sm text-gray-500">Cantidad:</span>
          <div class="flex items-center gap-2">
            <button onclick="cambiarCantidadItem(${item.id}, ${item.cantidad - 1})" 
                    class="w-8 h-8 rounded-full bg-gray-200 hover:bg-[#FF1493] hover:text-white transition">
              <i class="fas fa-minus text-xs"></i>
            </button>
            <span class="font-semibold w-8 text-center">${item.cantidad}</span>
            <button onclick="cambiarCantidadItem(${item.id}, ${item.cantidad + 1})" 
                    class="w-8 h-8 rounded-full bg-gray-200 hover:bg-[#FF1493] hover:text-white transition">
              <i class="fas fa-plus text-xs"></i>
            </button>
          </div>
        </div>

        ${item.stock && item.cantidad >= item.stock ? 
          `<p class="text-xs text-orange-500 mt-2"><i class="fas fa-exclamation-triangle mr-1"></i>Stock máximo alcanzado</p>` 
          : ''}
      </div>

      <div class="flex flex-row sm:flex-col justify-between sm:justify-start items-end sm:items-end gap-2">
        <p class="text-xl font-bold" style="color: var(--rosa-neon);">S/ ${subtotal.toFixed(2)}</p>
        <button onclick="eliminarItem(${item.id})" 
                class="text-red-500 hover:text-red-700 transition">
          <i class="fas fa-trash-alt"></i>
        </button>
      </div>
    `;

    itemsList.appendChild(itemDiv);
  });
}

/* ============================
   🔹 CAMBIAR CANTIDAD
============================ */
window.cambiarCantidadItem = function(productoId, nuevaCantidad) {
  const resultado = actualizarCantidadCarrito(productoId, nuevaCantidad); // Desde cart-manager.js
  
  if (!resultado.success) {
    mostrarNotificacionOriginal(resultado.message, 'warning');
  }
  
  renderizarCarrito();
};

/* ============================
   🔹 ELIMINAR ITEM
============================ */
window.eliminarItem = function(productoId) {
  const resultado = eliminarDelCarrito(productoId); // Desde cart-manager.js
  
  if (resultado.success) {
    mostrarNotificacionOriginal(resultado.message, 'success');
    renderizarCarrito();
  }
};

/* ============================
   🔹 ACTUALIZAR TOTALES
============================ */
function actualizarTotales() {
  const { subtotal, envio, total } = calcularTotalCarrito(); // Desde cart-manager.js

  const subtotalEl = document.getElementById('subtotal');
  const shippingEl = document.getElementById('shipping');
  const totalEl = document.getElementById('total');

  if (subtotalEl) subtotalEl.textContent = `S/ ${subtotal.toFixed(2)}`;
  if (shippingEl) shippingEl.textContent = envio === 0 ? 'Gratis' : `S/ ${envio.toFixed(2)}`;
  if (totalEl) totalEl.textContent = `S/ ${total.toFixed(2)}`;
}

/* ============================
   🔹 PROCEDER AL PAGO
============================ */
window.procederAlPago = function() {
  const carrito = obtenerCarrito();

  if (carrito.length === 0) {
    mostrarNotificacionOriginal('Tu carrito está vacío', 'warning');
    return;
  }

  // Llamar al flujo de nuevo-pedido para renderizar el formulario de checkout
  try {
    if (typeof iniciarCheckout === 'function') {
      iniciarCheckout();
    } else {
      // Fallback: mostrar notificación y mantener UX
      const { total } = calcularTotalCarrito();
      mostrarNotificacionOriginal(`Procesando pedido por S/ ${total.toFixed(2)}...`, 'success');
    }
  } catch (err) {
    console.error('Error iniciando checkout:', err);
    mostrarNotificacionOriginal('No se puede iniciar el checkout. Intente de nuevo.', 'error');
  }
};

// Compatibilidad: alias en inglés por si algún template usa `proceedToCheckout()`
window.proceedToCheckout = window.procederAlPago;

/* ============================
   🔹 PRODUCTOS RECOMENDADOS
============================ */
async function cargarProductosRecomendados() {
  try {
    const res = await fetch('./api/productos.php');
    if (!res.ok) return;
    const json = await res.json();
    if (!json.success || !json.data) return;

    const productosAleatorios = json.data
      .sort(() => Math.random() - 0.5)
      .slice(0, 4);
    
    renderizarRecomendados(productosAleatorios);
  } catch (err) {
    console.error('Error cargando recomendados:', err);
  }
}

function renderizarRecomendados(productos) {
  const grid = document.getElementById('recommended-grid');
  if (!grid || productos.length === 0) return;

  grid.innerHTML = '';

  productos.forEach(producto => {
    const precioFinal = parseFloat(producto.precio_final ?? producto.precio);
    const descuento = Number(producto.descuento) || 0;
    const sinStock = Number(producto.stock) <= 0;
    const imagenSegura = producto.imagen && producto.imagen.trim() !== ''
      ? `uploads/productos/${escapeHtml(producto.imagen)}`
      : 'assets/img/logo.png';

    const card = document.createElement('div');
    card.className = 'bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition';

    card.innerHTML = `
      <div class="relative h-48 bg-gradient-to-b from-pink-50 to-white flex items-center justify-center">
        ${descuento > 0 ? `<div class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded">-${descuento}%</div>` : ''}
        <img src="${imagenSegura}" alt="${escapeHtml(producto.nombre)}"
             onerror="this.src='assets/img/logo.png'"
             class="max-h-40 object-contain">
      </div>
      <div class="p-4">
        <h4 class="font-semibold mb-2 line-clamp-2">${escapeHtml(producto.nombre)}</h4>
        <p class="text-xl font-bold mb-3" style="color: var(--rosa-neon);">S/ ${precioFinal.toFixed(2)}</p>
        <button onclick="agregarRecomendado(${Number(producto.id)})" 
                ${sinStock ? 'disabled' : ''}
                class="btn-primary w-full text-sm">
          ${sinStock ? 'Agotado' : 'Agregar al carrito'}
        </button>
      </div>
    `;

    grid.appendChild(card);
  });
}

/* ============================
   🔹 AGREGAR RECOMENDADO
============================ */
window.agregarRecomendado = async function(productoId) {
  try {
    const res = await fetch('./api/productos.php');
    if (!res.ok) return;
    const json = await res.json();
    if (!json.success) return;

    const producto = json.data.find(p => Number(p.id) === Number(productoId));
    if (!producto) return;

    // Usar el gestor centralizado
    const resultado = agregarAlCarrito({
      id: producto.id,
      nombre: producto.nombre,
      precio: parseFloat(producto.precio_final ?? producto.precio),
      imagen: producto.imagen || 'assets/img/logo.png',
      stock: Number(producto.stock)
    });

    mostrarNotificacionOriginal(resultado.message, resultado.success ? 'success' : 'warning');
    
    if (resultado.success) {
      renderizarCarrito();
    }
  } catch (err) {
    console.error('Error agregando producto:', err);
  }
};