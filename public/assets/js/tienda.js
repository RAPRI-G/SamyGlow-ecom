// tienda.js - Lógica específica de la página de la tienda
// DEPENDE DE: cart-manager.js (debe cargarse ANTES)

let productos = [];

/* ============================
   🔹 INICIALIZACIÓN
============================ */
document.addEventListener('DOMContentLoaded', () => {
  cargarProductos();
  configurarMenuMobile();
});

/* ============================
   🔹 CARGAR PRODUCTOS
============================ */
async function cargarProductos() {
  const grid = document.getElementById('products-grid');
  
  // Mostrar indicador de carga
  if (grid) {
    grid.innerHTML = `
      <div class="col-span-full py-12 text-center">
        <i class="fas fa-spinner fa-spin text-4xl text-pink-500 mb-4"></i>
        <p class="text-gray-600">Cargando productos...</p>
      </div>`;
  }

  try {
    console.log('📦 Intentando cargar productos desde: ./api/productos.php');
    
    const res = await fetch('./api/productos.php');
    
    console.log('📡 Respuesta recibida:', {
      status: res.status,
      statusText: res.statusText,
      ok: res.ok
    });
    
    if (!res.ok) {
      throw new Error(`Error HTTP ${res.status}: ${res.statusText}`);
    }
    
    const texto = await res.text();
    console.log('📄 Texto crudo recibido:', texto.substring(0, 200));
    
    let json;
    try {
      json = JSON.parse(texto);
    } catch (parseErr) {
      console.error('❌ Error parseando JSON:', parseErr);
      throw new Error('La respuesta no es un JSON válido. Revisa tu API.');
    }

    console.log('✅ JSON parseado:', json);

    if (!json.success) {
      throw new Error(json.message || 'La API retornó success=false');
    }

    if (!Array.isArray(json.data)) {
      throw new Error('La respuesta no contiene un array de productos en "data"');
    }

    // Guardar productos en la variable global
    productos = json.data;
    console.log(`✅ ${productos.length} productos cargados exitosamente`);
    
    renderizarProductos(productos);
  } catch (err) {
    console.error('❌ Error cargando productos:', err);
    
    if (grid) {
      grid.innerHTML = `
        <div class="col-span-full py-12 text-center">
          <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
          <p class="text-red-600 font-semibold mb-2">Error al cargar productos</p>
          <p class="text-gray-600 text-sm mb-4">${escapeHtml(err.message)}</p>
          <button onclick="cargarProductos()" class="btn-primary">
            <i class="fas fa-redo mr-2"></i>Reintentar
          </button>
        </div>`;
    }
  }
}

/* ============================
   🔹 RENDERIZAR PRODUCTOS
============================ */
function renderizarProductos(listaProductos) {
  const grid = document.getElementById('products-grid');
  if (!grid) return;
  grid.innerHTML = '';

  if (!listaProductos || listaProductos.length === 0) {
    grid.innerHTML = '<p class="text-center col-span-full py-12 text-gray-500">No hay productos disponibles</p>';
    actualizarContadores(0);
    return;
  }

  listaProductos.forEach((producto) => {
    const precioOriginal = parseFloat(producto.precio);
    const precioFinal = parseFloat(producto.precio_final ?? producto.precio);
    const descuento = Number(producto.descuento) || 0;
    const sinStock = Number(producto.stock) <= 0;

    const card = document.createElement('div');
    card.className = 'product-card bg-white rounded-2xl shadow overflow-hidden relative hover:shadow-xl transition';

    const badgeHtml = descuento > 0
      ? `<div class="absolute top-4 right-4 bg-red-500 text-white text-sm px-2 py-1 rounded z-10">-${escapeHtml(String(descuento))}%</div>`
      : '';

    const imgSrc = producto.imagen 
      ? `image.php?f=uploads/productos/${escapeHtml(producto.imagen)}`
      : 'assets/img/logo.png';

    card.innerHTML = `
      <div class="relative">
        ${badgeHtml}
        <div class="relative h-64 bg-gradient-to-b from-pink-50 to-white flex items-center justify-center">
          <img src="${imgSrc}" alt="${escapeHtml(producto.nombre)}" 
               onerror="this.src='assets/img/logo.png'" 
               class="max-h-60 object-contain" />
        </div>
      </div>
      <div class="p-5">
        <span class="badge text-xs bg-gray-100 px-2 py-1 rounded">${escapeHtml(producto.categoria || 'Sin categoría')}</span>
        <h3 class="mt-2 font-semibold text-lg">${escapeHtml(producto.nombre)}</h3>
        <p class="text-sm text-gray-600 mt-1 line-clamp-2">${escapeHtml(producto.descripcion || '')}</p>

        <div class="mt-3">
          ${descuento > 0 ? `<p class="line-through text-gray-400 text-sm">S/ ${precioOriginal.toFixed(2)}</p>` : ''}
          <p class="text-2xl font-bold" style="color: var(--rosa-neon);">S/ ${precioFinal.toFixed(2)}</p>
        </div>

        <p class="text-sm text-gray-600 mt-2">
          ${sinStock 
            ? '<i class="fas fa-times-circle text-red-500 mr-1"></i>Sin stock' 
            : `<i class="fas fa-check-circle text-green-500 mr-1"></i>Stock: ${escapeHtml(String(producto.stock))}`
          }
        </p>

        <button class="mt-4 w-full btn-primary" 
                ${sinStock ? 'disabled' : ''} 
                onclick="agregarProductoDesdeTienda(${Number(producto.id)})">
          ${sinStock ? 'Agotado' : 'Agregar al carrito'}
        </button>
      </div>
    `;

    grid.appendChild(card);
  });

  actualizarContadores(listaProductos.length);
}

/* ============================
   🔹 AGREGAR AL CARRITO (TIENDA)
============================ */
window.agregarProductoDesdeTienda = function (productoId) {
  const producto = productos.find(p => Number(p.id) === Number(productoId));
  
  if (!producto) {
    mostrarNotificacion('Producto no encontrado', 'error');
    return;
  }

  // Usar el gestor centralizado
  const resultado = agregarAlCarrito({
    id: producto.id,
    nombre: producto.nombre,
    precio: parseFloat(producto.precio_final ?? producto.precio),
    imagen: producto.imagen || 'assets/img/logo.png',
    stock: Number(producto.stock)
  });

  mostrarNotificacion(resultado.message, resultado.success ? 'success' : 'warning');
};

/* ============================
   🔹 UTILIDADES
============================ */
function actualizarContadores(cantidad) {
  const resultsCountEl = document.getElementById('results-count');
  if (resultsCountEl) resultsCountEl.textContent = String(cantidad);
  
  const totalCountEl = document.getElementById('total-count');
  if (totalCountEl) totalCountEl.textContent = String(cantidad);
}

function configurarMenuMobile() {
  const btn = document.getElementById('menu-btn');
  const menu = document.getElementById('mobile-menu');
  if (!btn || !menu) return;
  btn.addEventListener('click', () => menu.classList.toggle('active'));
} 