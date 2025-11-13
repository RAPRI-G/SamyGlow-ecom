// tienda.js - Consume api/productos.php y gestiona carrito en localStorage
const productos = [];
let carrito = [];

/** Utilidades */
const escapeHtml = (str) => {
  if (str === null || str === undefined) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
};

/** Inicialización */
document.addEventListener('DOMContentLoaded', () => {
  cargarProductos();
  cargarCarritoLocalStorage();
  actualizarContadorCarrito();
  configurarMenuMobile();
});

/** Cargar productos desde la API */
async function cargarProductos() {
  try {
    // Usar ruta relativa explícita para evitar ambigüedades dependiendo de cómo se sirva la página
    const res = await fetch('./api/productos.php');
    if (!res.ok) throw new Error('Error al obtener el listado de productos (HTTP ' + res.status + ')');
    
    const json = await res.json();

    console.log('API /api/productos.php response:', json);

    if (!json.success) {
      throw new Error(json.message || 'Respuesta inválida de la API');
    }

    // Guardar productos en la variable global
    productos.length = 0;
    json.data.forEach(p => productos.push(p));

    renderizarProductos(productos);
  } catch (err) {
    console.error('cargarProductos error:', err);
    const grid = document.getElementById('products-grid');
    if (grid) grid.innerHTML = `<div class="col-span-full py-12 text-center"><p class="text-red-500">Error al cargar productos: ${escapeHtml(err.message || String(err))}</p></div>`;
  }
}

/** Renderiza la lista de productos en la grilla */
function renderizarProductos(listaProductos) {
  const grid = document.getElementById('products-grid');
  if (!grid) return;
  grid.innerHTML = '';

  if (!listaProductos || listaProductos.length === 0) {
    grid.innerHTML = '<p class="text-center">No hay productos disponibles</p>';
    const resultsCountEl = document.getElementById('results-count');
    if (resultsCountEl) resultsCountEl.textContent = '0';
    const totalCountEl = document.getElementById('total-count');
    if (totalCountEl) totalCountEl.textContent = '0';
    return;
  }

  listaProductos.forEach((producto) => {
    const precioOriginal = parseFloat(producto.precio);
    const precioFinal = parseFloat(producto.precio_final ?? producto.precio);
    const descuento = Number(producto.descuento) || 0;
    const sinStock = Number(producto.stock) <= 0;

    const card = document.createElement('div');
    card.className = 'product-card bg-white rounded-2xl shadow overflow-hidden relative';

    // Badge de descuento
    const badgeHtml = descuento > 0
      ? `<div class="absolute top-4 right-4 bg-red-500 text-white text-sm px-2 py-1 rounded">-${escapeHtml(String(descuento))}%</div>`
      : '';

    // Imagen del producto (usa placeholder si no hay imagen o hay error)
    const imgSrc = escapeHtml(producto.imagen || 'assets/img/logo.png');

    card.innerHTML = `
      <div class="relative">
        ${badgeHtml}
        <div class="relative h-64 bg-gradient-to-b from-pink-50 to-white flex items-center justify-center">
          <img src="${imgSrc}" alt="${escapeHtml(producto.nombre)}" onerror="this.src='assets/img/logo.png'" class="max-h-60 object-contain" />
        </div>
      </div>
      <div class="p-5">
        <span class="badge text-xs bg-gray-100 px-2 py-1 rounded">${escapeHtml(producto.categoria || 'Sin categoría')}</span>
        <h3 class="mt-2 font-semibold text-lg">${escapeHtml(producto.nombre)}</h3>
        <p class="text-sm text-gray-600 mt-1">${escapeHtml(producto.descripcion || '')}</p>

        <div class="mt-3">
          ${descuento > 0 ? `<p class="line-through text-gray-400">S/ ${precioOriginal.toFixed(2)}</p>` : ''}
          <p class="text-2xl font-bold">S/ ${precioFinal.toFixed(2)}</p>
        </div>

        <p class="text-sm text-gray-600 mt-2">Stock: ${escapeHtml(String(producto.stock))}</p>

        <button class="mt-4 w-full btn-primary" ${sinStock ? 'disabled' : ''} onclick="agregarAlCarrito(${Number(producto.id)})">
          ${sinStock ? 'Agotado' : 'Agregar al carrito'}
        </button>
      </div>
    `;

    grid.appendChild(card);
  });

  const resultsCountEl = document.getElementById('results-count');
  if (resultsCountEl) resultsCountEl.textContent = String(listaProductos.length);
  const totalCountEl = document.getElementById('total-count');
  if (totalCountEl) totalCountEl.textContent = String(listaProductos.length);
}

/** Agrega un producto al carrito (disponible globalmente) */
window.agregarAlCarrito = function (productoId) {
  const producto = productos.find(p => Number(p.id) === Number(productoId));
  if (!producto) return mostrarNotificacion('Producto no encontrado');

  if (Number(producto.stock) <= 0) return mostrarNotificacion('Producto sin stock');

  const existente = carrito.find(i => Number(i.id) === Number(productoId));
  if (existente) {
    if ((existente.cantidad + 1) > Number(producto.stock)) {
      return mostrarNotificacion('No hay suficiente stock');
    }
    existente.cantidad += 1;
  } else {
    carrito.push({
      id: Number(producto.id),
      nombre: producto.nombre,
      precio: parseFloat(producto.precio_final ?? producto.precio),
      imagen: producto.imagen || 'assets/img/logo.png',
      cantidad: 1,
      stock: Number(producto.stock)
    });
  }

  guardarCarritoLocalStorage();
  actualizarContadorCarrito();
  mostrarNotificacion('Producto agregado');
};

/** Guardar carrito en localStorage */
function guardarCarritoLocalStorage() {
  try {
    localStorage.setItem('carrito_samyglow', JSON.stringify(carrito));
  } catch (err) {
    console.error('Error guardando carrito:', err);
  }
}

/** Cargar carrito del localStorage */
function cargarCarritoLocalStorage() {
  try {
    const raw = localStorage.getItem('carrito_samyglow');
    if (!raw) return;
    const parsed = JSON.parse(raw);
    if (Array.isArray(parsed)) carrito = parsed;
  } catch (err) {
    console.error('Error cargando carrito:', err);
    carrito = [];
  }
}

/** Actualiza el contador del carrito en el header */
function actualizarContadorCarrito() {
  const countEl = document.getElementById('cart-count');
  if (!countEl) return;
  const total = carrito.reduce((sum, it) => sum + (Number(it.cantidad) || 0), 0);
  countEl.textContent = String(total);
  countEl.style.display = total > 0 ? 'inline-flex' : 'none';
}

/** Configura el menú móvil */
function configurarMenuMobile() {
  const btn = document.getElementById('menu-btn');
  const menu = document.getElementById('mobile-menu');
  if (!btn || !menu) return;
  btn.addEventListener('click', () => menu.classList.toggle('active'));
}

/** Muestra un aviso temporal (toast) */
function mostrarNotificacion(mensaje) {
  const div = document.createElement('div');
  div.textContent = mensaje;
  div.className = 'fixed top-20 right-4 bg-green-500 text-white px-4 py-2 rounded shadow z-50';
  document.body.appendChild(div);
  setTimeout(() => div.remove(), 3000);
}
