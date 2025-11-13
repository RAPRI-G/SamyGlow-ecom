// home.js - carga dinámicamente categorías y productos destacados
document.addEventListener('DOMContentLoaded', () => {
  cargarCategorias();
  cargarProductosDestacados();
  configurarMenuMobile();
  cargarCarritoLocalStorage();
  actualizarContadorCarrito();
});

/** Cargar las primeras 4 categorías desde la API */
async function cargarCategorias() {
  try {
    const res = await fetch('./api/categorias.php');
    if (!res.ok) throw new Error('Error al obtener categorías (HTTP ' + res.status + ')');
    const json = await res.json();
    
    console.log('API /api/categorias.php response:', json);
    
    if (!json.success) {
      throw new Error(json.message || 'Respuesta inválida de la API');
    }

    const grid = document.getElementById('categories-grid');
    if (!grid) return;
    
    // Limpiar el contenido existente
    grid.innerHTML = '';

    // Tomar solo las primeras 4 categorías
    const categorias = json.data.slice(0, 4);

    // Iconos por tipo de categoría
    const iconosPorCategoria = {
      'Eau de Parfum': 'fa-bottle-droplet',
      'Eau de Toilette': 'fa-spray-can',
      'Brumas Corporales': 'fa-wind',
      'Sets Regalo': 'fa-gift',
      'default': 'fa-spray-can-sparkles'
    };

    categorias.forEach(cat => {
      // Seleccionar icono apropiado
      const iconClass = iconosPorCategoria[cat.nombre] || iconosPorCategoria['default'];
      
      const div = document.createElement('div');
      div.className = 'fade-in-up bg-white p-6 md:p-8 rounded-2xl shadow-lg text-center cursor-pointer hover:shadow-2xl transition border-neon-light';
      div.innerHTML = `
        <i class="fas ${iconClass} text-3xl md:text-4xl mb-4" style="color: var(--rosa-neon);"></i>
        <h3 class="font-semibold text-base md:text-lg">${escapeHtml(cat.nombre)}</h3>
      `;
      
      // Hacer clic para ir a tienda filtrada por categoría (opcional)
      div.addEventListener('click', () => {
        window.location.href = `tienda.html?categoria=${encodeURIComponent(cat.nombre)}`;
      });
      
      grid.appendChild(div);
    });

    // Si hay menos de 4 categorías, rellenar con placeholders
    if (categorias.length < 4) {
      const faltantes = 4 - categorias.length;
      for (let i = 0; i < faltantes; i++) {
        const placeholder = document.createElement('div');
        placeholder.className = 'fade-in-up bg-gray-100 p-6 md:p-8 rounded-2xl shadow text-center';
        placeholder.innerHTML = `
          <i class="fas fa-box text-3xl md:text-4xl mb-4 text-gray-400"></i>
          <h3 class="font-semibold text-base md:text-lg text-gray-400">Próximamente</h3>
        `;
        grid.appendChild(placeholder);
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

/** Cargar solo los primeros 8 productos con descuento */
async function cargarProductosDestacados() {
  try {
    const res = await fetch('./api/productos.php');
    if (!res.ok) throw new Error('Error al obtener productos (HTTP ' + res.status + ')');
    const json = await res.json();
    
    console.log('API /api/productos.php response:', json);
    
    if (!json.success) {
      throw new Error(json.message || 'Respuesta inválida de la API');
    }

    const grid = document.getElementById('products-grid');
    if (!grid) return;
    
    // Limpiar el contenido existente
    grid.innerHTML = '';

    // Filtrar productos con descuento y tomar solo los primeros 8
    const productosConDescuento = json.data
      .filter(prod => Number(prod.descuento) > 0)
      .slice(0, 8);

    // Si no hay productos con descuento, mostrar los primeros 8 productos
    const productosAMostrar = productosConDescuento.length > 0 
      ? productosConDescuento 
      : json.data.slice(0, 8);

    if (productosAMostrar.length === 0) {
      grid.innerHTML = `
        <div class="col-span-full text-center py-12">
          <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
          <p class="text-gray-500">No hay productos disponibles</p>
        </div>
      `;
      return;
    }

    productosAMostrar.forEach(prod => {
      const precioOriginal = parseFloat(prod.precio);
      const precioFinal = parseFloat(prod.precio_final ?? prod.precio);
      const descuento = Number(prod.descuento) || 0;
      const sinStock = Number(prod.stock) <= 0;

      const card = document.createElement('div');
      card.className = 'card-product bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition';
      
      card.innerHTML = `
        <div class="relative">
          ${descuento > 0 ? `
            <div class="absolute top-4 right-4 bg-red-500 text-white text-sm px-3 py-1 rounded-full font-bold z-10">
              -${descuento}%
            </div>
          ` : ''}
          <div class="h-48 md:h-64 bg-gradient-to-b from-pink-50 to-white flex items-center justify-center">
            <img src="${escapeHtml(prod.imagen || 'assets/img/placeholder.jpg')}" 
                 alt="${escapeHtml(prod.nombre)}" 
                 onerror="this.src='assets/img/placeholder.jpg'" 
                 class="max-h-40 md:max-h-56 object-contain" />
          </div>
        </div>
        
        <div class="p-4 md:p-6">
          <span class="text-xs bg-gray-100 px-2 py-1 rounded">${escapeHtml(prod.categoria)}</span>
          <h3 class="font-semibold text-base md:text-lg mb-2 mt-2">${escapeHtml(prod.nombre)}</h3>
          <p class="text-gray-500 text-xs md:text-sm mb-3 line-clamp-2">${escapeHtml(prod.descripcion || 'Fragancia exclusiva')}</p>
          
          <div class="mb-4">
            ${descuento > 0 ? `
              <p class="text-sm text-gray-400 line-through">S/ ${precioOriginal.toFixed(2)}</p>
              <p class="text-xl md:text-2xl font-bold" style="color: var(--rosa-neon);">S/ ${precioFinal.toFixed(2)}</p>
            ` : `
              <p class="text-xl md:text-2xl font-bold" style="color: var(--rosa-neon);">S/ ${precioFinal.toFixed(2)}</p>
            `}
          </div>

          ${sinStock ? `
            <p class="text-sm text-red-500 mb-2"><i class="fas fa-exclamation-circle mr-1"></i>Agotado</p>
          ` : `
            <p class="text-sm text-gray-600 mb-2"><i class="fas fa-check-circle mr-1 text-green-500"></i>Disponible</p>
          `}
          
          <button onclick="agregarAlCarritoDesdeHome(${Number(prod.id)})" 
                  ${sinStock ? 'disabled' : ''}
                  class="btn-primary w-full text-sm md:text-base ${sinStock ? 'opacity-50 cursor-not-allowed' : ''}">
            ${sinStock ? 'Sin Stock' : 'Agregar al Carrito'}
          </button>
        </div>
      `;
      
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
        </div>
      `;
    }
  }
}

/** Agregar producto al carrito desde la página de inicio */
window.agregarAlCarritoDesdeHome = async function(productoId) {
  try {
    const res = await fetch('./api/productos.php');
    if (!res.ok) return;
    const json = await res.json();
    
    if (!json.success) return;
    
    const producto = json.data.find(p => Number(p.id) === Number(productoId));
    if (!producto) {
      return mostrarNotificacion('Producto no encontrado', 'error');
    }

    if (Number(producto.stock) <= 0) {
      return mostrarNotificacion('Producto sin stock', 'warning');
    }

    // Cargar carrito actual
    let carrito = [];
    try {
      const raw = localStorage.getItem('carrito_samyglow');
      if (raw) {
        carrito = JSON.parse(raw);
      }
    } catch (e) {
      console.error('Error cargando carrito:', e);
    }

    const existente = carrito.find(i => Number(i.id) === Number(productoId));
    if (existente) {
      if ((existente.cantidad + 1) > Number(producto.stock)) {
        return mostrarNotificacion('No hay suficiente stock', 'warning');
      }
      existente.cantidad += 1;
    } else {
      carrito.push({
        id: Number(producto.id),
        nombre: producto.nombre,
        precio: parseFloat(producto.precio_final ?? producto.precio),
        imagen: producto.imagen || 'assets/img/placeholder.jpg',
        cantidad: 1,
        stock: Number(producto.stock)
      });
    }

    // Guardar carrito
    localStorage.setItem('carrito_samyglow', JSON.stringify(carrito));
    actualizarContadorCarrito();
    mostrarNotificacion('¡Producto agregado al carrito!', 'success');
  } catch (err) {
    console.error('Error agregando producto:', err);
    mostrarNotificacion('Error al agregar producto', 'error');
  }
};

/** Cargar carrito desde localStorage */
function cargarCarritoLocalStorage() {
  // Esta función se usa solo para inicializar el contador
  return;
}

/** Actualizar contador del carrito */
function actualizarContadorCarrito() {
  const countEl = document.getElementById('cart-count');
  if (!countEl) return;
  
  let carrito = [];
  try {
    const raw = localStorage.getItem('carrito_samyglow');
    if (raw) {
      carrito = JSON.parse(raw);
    }
  } catch (e) {
    console.error('Error leyendo carrito:', e);
  }
  
  const total = carrito.reduce((sum, item) => sum + (Number(item.cantidad) || 0), 0);
  countEl.textContent = String(total);
  countEl.style.display = total > 0 ? 'flex' : 'none';
}

/** Configurar menú móvil */
function configurarMenuMobile() {
  const btn = document.getElementById('menu-btn');
  const menu = document.getElementById('mobile-menu');
  if (!btn || !menu) return;
  
  btn.addEventListener('click', () => {
    menu.classList.toggle('active');
  });
}

/** Mostrar notificación */
function mostrarNotificacion(mensaje, tipo = 'success') {
  const colores = {
    success: 'bg-green-500',
    warning: 'bg-orange-500',
    error: 'bg-red-500'
  };

  const iconos = {
    success: 'fa-check-circle',
    warning: 'fa-exclamation-triangle',
    error: 'fa-times-circle'
  };

  const div = document.createElement('div');
  div.className = `fixed top-20 right-4 ${colores[tipo]} text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2`;
  div.innerHTML = `
    <i class="fas ${iconos[tipo]}"></i>
    <span>${mensaje}</span>
  `;
  
  document.body.appendChild(div);
  
  setTimeout(() => {
    div.style.opacity = '0';
    div.style.transition = 'opacity 0.3s';
    setTimeout(() => div.remove(), 300);
  }, 3000);
}

/** Utilidad para escapar HTML */
function escapeHtml(str) {
  if (str === null || str === undefined) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}