// home.js - Lógica específica de la página principal
// DEPENDE DE: cart-manager.js (debe cargarse ANTES)

document.addEventListener('DOMContentLoaded', () => {
  cargarCategorias();
  cargarProductosDestacados();
  configurarMenuMobile();
  inicializarAnimaciones();
});

/* ============================
   🔹 CATEGORÍAS
============================ */
async function cargarCategorias() {
  try {
    const res = await fetch('./api/categorias.php');
    if (!res.ok) throw new Error(`Error HTTP ${res.status}`);

    const texto = await res.text();
    console.log('Respuesta cruda de categorias.php:', texto);

    let json;
    try {
      json = JSON.parse(texto);
    } catch (err) {
      throw new Error('Error analizando JSON de categorías: ' + err.message);
    }

    if (!json.success || !Array.isArray(json.data)) {
      throw new Error(json.message || 'Respuesta inválida de la API de categorías');
    }

    const grid = document.getElementById('categories-grid');
    if (!grid) return;
    grid.innerHTML = '';

    const categorias = json.data.slice(0, 4);
    const iconos = {
      'Eau de Parfum': 'fa-bottle-droplet',
      'Eau de Toilette': 'fa-spray-can',
      'Brumas Corporales': 'fa-wind',
      'Sets Regalo': 'fa-gift',
      default: 'fa-spray-can-sparkles'
    };

    categorias.forEach(cat => {
      const div = document.createElement('div');
      const icono = iconos[cat.nombre] || iconos.default;

      div.className =
        'fade-in-up bg-white p-6 md:p-8 rounded-2xl shadow-lg text-center cursor-pointer hover:shadow-2xl transition border-neon-light';
      div.innerHTML = `
        <i class="fas ${icono} text-3xl md:text-4xl mb-4" style="color: var(--rosa-neon);"></i>
        <h3 class="font-semibold text-base md:text-lg">${escapeHtml(cat.nombre)}</h3>
      `;

      div.addEventListener('click', () => {
        window.location.href = `tienda.html?categoria=${encodeURIComponent(cat.nombre)}`;
      });

      grid.appendChild(div);
    });

    // Si hay menos de 4, rellenar con placeholders
    if (categorias.length < 4) {
      for (let i = categorias.length; i < 4; i++) {
        const ph = document.createElement('div');
        ph.className = 'fade-in-up bg-gray-100 p-6 md:p-8 rounded-2xl shadow text-center';
        ph.innerHTML = `
          <i class="fas fa-box text-3xl md:text-4xl mb-4 text-gray-400"></i>
          <h3 class="font-semibold text-base md:text-lg text-gray-400">Próximamente</h3>
        `;
        grid.appendChild(ph);
      }
    }
  } catch (err) {
    console.error('Error cargando categorías:', err);
    const grid = document.getElementById('categories-grid');
    if (grid) {
      grid.innerHTML = `
        <div class="col-span-full text-center py-8">
          <p class="text-red-500">Error al cargar categorías: ${escapeHtml(err.message)}</p>
        </div>
      `;
    }
  }
}

/* ============================
   🔹 PRODUCTOS DESTACADOS
============================ */
async function cargarProductosDestacados() {
  try {
    const res = await fetch('./api/productos.php');
    if (!res.ok) throw new Error(`Error HTTP ${res.status}`);

    const texto = await res.text();
    console.log('Respuesta cruda de productos.php:', texto);

    let json;
    try {
      json = JSON.parse(texto);
    } catch (err) {
      throw new Error('Error analizando JSON de productos: ' + err.message);
    }

    if (!json.success || !Array.isArray(json.data)) {
      throw new Error(json.message || 'Respuesta inválida de la API de productos');
    }

    const grid = document.getElementById('products-grid');
    if (!grid) return;
    grid.innerHTML = '';

    // Mostrar hasta 8 productos con descuento
    const destacados = json.data.filter(p => Number(p.descuento) > 0).slice(0, 8);
    const productos = destacados.length ? destacados : json.data.slice(0, 8);

    if (productos.length === 0) {
      grid.innerHTML = `
        <div class="col-span-full text-center py-12">
          <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
          <p class="text-gray-500">No hay productos disponibles</p>
        </div>`;
      return;
    }

    productos.forEach(prod => {
      const precioOriginal = parseFloat(prod.precio);
      const precioFinal = parseFloat(prod.precio_final ?? prod.precio);
      const descuento = Number(prod.descuento) || 0;
      const sinStock = Number(prod.stock) <= 0;
      const imagenUrl = prod.imagen
        ? `image.php?f=uploads/productos/${escapeHtml(prod.imagen)}`
        : 'assets/img/logo.png';

      const card = document.createElement('div');
      card.className =
        'card-product bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition';

      card.innerHTML = `
        <div class="relative">
          ${
            descuento > 0
              ? `<div class="absolute top-4 right-4 bg-red-500 text-white text-sm px-3 py-1 rounded-full font-bold z-10">-${descuento}%</div>`
              : ''
          }
          <div class="h-48 md:h-64 bg-gradient-to-b from-pink-50 to-white flex items-center justify-center">
            <img src="${imagenUrl}" alt="${escapeHtml(prod.nombre)}"
                 onerror="this.src='assets/img/logo.png'"
                 class="max-h-40 md:max-h-56 object-contain" />
          </div>
        </div>

        <div class="p-4 md:p-6">
          <span class="text-xs bg-gray-100 px-2 py-1 rounded">${escapeHtml(
            prod.categoria
          )}</span>
          <h3 class="font-semibold text-base md:text-lg mb-2 mt-2">${escapeHtml(
            prod.nombre
          )}</h3>
          <p class="text-gray-500 text-xs md:text-sm mb-3 line-clamp-2">${escapeHtml(
            prod.descripcion || 'Fragancia exclusiva'
          )}</p>

          <div class="mb-4">
            ${
              descuento > 0
                ? `
                <p class="text-sm text-gray-400 line-through">S/ ${precioOriginal.toFixed(
                  2
                )}</p>
                <p class="text-xl md:text-2xl font-bold" style="color: var(--rosa-neon);">S/ ${precioFinal.toFixed(
                  2
                )}</p>`
                : `<p class="text-xl md:text-2xl font-bold" style="color: var(--rosa-neon);">S/ ${precioFinal.toFixed(
                    2
                  )}</p>`
            }
          </div>

          ${
            sinStock
              ? `<p class="text-sm text-red-500 mb-2"><i class="fas fa-exclamation-circle mr-1"></i>Agotado</p>`
              : `<p class="text-sm text-gray-600 mb-2"><i class="fas fa-check-circle mr-1 text-green-500"></i>Disponible</p>`
          }

          <button onclick="agregarProductoDesdeHome(${Number(prod.id)})"
                  ${sinStock ? 'disabled' : ''}
                  class="btn-primary w-full text-sm md:text-base ${
                    sinStock ? 'opacity-50 cursor-not-allowed' : ''
                  }">
            ${sinStock ? 'Sin Stock' : 'Agregar al Carrito'}
          </button>
        </div>`;

      grid.appendChild(card);
    });
  } catch (err) {
    console.error('Error cargando productos destacados:', err);
    const grid = document.getElementById('products-grid');
    if (grid) {
      grid.innerHTML = `
        <div class="col-span-full text-center py-12">
          <p class="text-red-500">Error al cargar productos: ${escapeHtml(err.message)}</p>
          <a href="tienda.html" class="btn-primary mt-4 inline-block">Ir a la Tienda</a>
        </div>`;
    }
  }
}

/* ============================
   🔹 AGREGAR AL CARRITO (HOME)
============================ */
window.agregarProductoDesdeHome = async function (id) {
  try {
    const res = await fetch('./api/productos.php');
    if (!res.ok) throw new Error('Error HTTP ' + res.status);
    const { success, data } = await res.json();
    if (!success) throw new Error('Error en la API');

    const producto = data.find(p => Number(p.id) === Number(id));
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
  } catch (err) {
    console.error('Error agregando producto:', err);
    mostrarNotificacion('Error al agregar producto', 'error');
  }
};

/* ============================
   🔹 MENÚ MÓVIL
============================ */
function configurarMenuMobile() {
  const btn = document.getElementById('menu-btn');
  const menu = document.getElementById('mobile-menu');
  if (!btn || !menu) return;
  btn.addEventListener('click', () => menu.classList.toggle('active'));
}

/* ============================
   🔹 ANIMACIONES
============================ */
function inicializarAnimaciones() {
  const elementos = document.querySelectorAll('.fade-in-up');
  const observer = new IntersectionObserver(
    entries => {
      entries.forEach(e => {
        if (e.isIntersecting) e.target.classList.add('visible');
      });
    },
    { threshold: 0.1 }
  );
  elementos.forEach(el => observer.observe(el));
}