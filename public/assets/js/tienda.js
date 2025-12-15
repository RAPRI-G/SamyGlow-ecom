// tienda.js - Lógica específica de la página de la tienda
// DEPENDE DE: cart-manager.js (debe cargarse ANTES)

let productos = [];
let paginaActual = 1;
const productosPorPagina = 9;
let totalPaginas = 1;
let listaRenderActual = [];

let categoriasDisponibles = [];
let marcasDisponibles = [];
let maxPrecioDisponible = 0;

const estadoFiltros = {
  categorias: new Set(),
  marcas: new Set(),
  precioMax: null,
  sort: 'popular',
};

function normalizarTexto(valor) {
  if (valor == null) return '';
  return String(valor).trim();
}

function obtenerPrecioProducto(producto) {
  const precio = parseFloat(producto?.precio_final ?? producto?.precio);
  return Number.isFinite(precio) ? precio : 0;
}

function obtenerCategoriaProducto(producto) {
  const categoria = normalizarTexto(producto?.categoria);
  return categoria || 'Sin categoría';
}

function inferirMarcaDesdeNombre(nombre) {
  const raw = normalizarTexto(nombre);
  if (!raw) return '';

  const stopWords = new Set([
    'fragrance',
    'mist',
    'body',
    'lotion',
    'cream',
    'butter',
    'moisturizing',
    'hydrating',
    'hair',
    'spray',
    'refreshing',
    'refresh',
    'luxury',
    'shea',
    'gold',
    'shine',
    'and',
  ]);

  const tokens = raw.split(/\s+/).filter(Boolean);
  const kept = [];
  for (const tok of tokens) {
    const clean = tok.replace(/[^\p{L}\p{N}&]/gu, '').toLowerCase();
    if (clean && stopWords.has(clean)) break;
    kept.push(tok);
  }

  return kept.length ? kept.join(' ').trim() : raw;
}

function obtenerMarcaProducto(producto) {
  const marca = normalizarTexto(producto?.marca);
  const inferida = inferirMarcaDesdeNombre(producto?.nombre);
  return marca || inferida || 'Otros';
}

async function cargarCategoriasParaFiltros() {
  try {
    const res = await fetch('./api/categorias.php');
    if (!res.ok) throw new Error(`Error HTTP ${res.status}`);
    const json = await res.json();
    if (!json.success || !Array.isArray(json.data)) throw new Error(json.message || 'Respuesta inválida de categorías');
    categoriasDisponibles = json.data.map((c) => normalizarTexto(c.nombre)).filter(Boolean);
  } catch (e) {
    categoriasDisponibles = [];
  }
}

function construirMarcasDisponibles() {
  const set = new Set();
  productos.forEach((p) => {
    const m = obtenerMarcaProducto(p);
    if (m) set.add(m);
  });
  marcasDisponibles = Array.from(set).sort((a, b) => a.localeCompare(b));
}

function construirCategoriasDisponiblesDesdeProductos() {
  const set = new Set();
  productos.forEach((p) => {
    const c = obtenerCategoriaProducto(p);
    if (c) set.add(c);
  });
  categoriasDisponibles = Array.from(set).sort((a, b) => a.localeCompare(b));
}

function crearCheckboxItem({ value, label, checked, className }) {
  const safeValue = normalizarTexto(value);
  const safeLabel = normalizarTexto(label) || safeValue;

  const labelEl = document.createElement('label');
  labelEl.className = 'flex items-center cursor-pointer hover:text-[#FFB6D9]';

  const input = document.createElement('input');
  input.type = 'checkbox';
  input.className = className;
  input.value = safeValue;
  input.checked = Boolean(checked);
  input.dataset.value = safeValue;

  const span = document.createElement('span');
  span.className = 'text-sm';
  span.textContent = safeLabel;

  labelEl.appendChild(input);
  labelEl.appendChild(span);
  return labelEl;
}

function renderizarFiltrosUI() {
  const catDesktop = document.getElementById('category-filters');
  const catMobile = document.getElementById('category-filters-mobile');
  const brandDesktop = document.getElementById('brand-filters');
  const brandMobile = document.getElementById('brand-filters-mobile');

  if (catDesktop) catDesktop.innerHTML = '';
  if (catMobile) catMobile.innerHTML = '';
  if (brandDesktop) brandDesktop.innerHTML = '';
  if (brandMobile) brandMobile.innerHTML = '';

  const categorias = categoriasDisponibles.length ? categoriasDisponibles : [];
  const marcas = marcasDisponibles.length ? marcasDisponibles : [];

  categorias.forEach((cat) => {
    const isChecked = estadoFiltros.categorias.has(cat);
    if (catDesktop) {
      catDesktop.appendChild(
        crearCheckboxItem({
          value: cat,
          label: cat,
          checked: isChecked,
          className: 'category-filter mr-2',
        })
      );
    }
    if (catMobile) {
      catMobile.appendChild(
        crearCheckboxItem({
          value: cat,
          label: cat,
          checked: isChecked,
          className: 'category-filter-mobile mr-2',
        })
      );
    }
  });

  marcas.forEach((m) => {
    const isChecked = estadoFiltros.marcas.has(m);
    if (brandDesktop) {
      brandDesktop.appendChild(
        crearCheckboxItem({
          value: m,
          label: m,
          checked: isChecked,
          className: 'brand-filter mr-2',
        })
      );
    }
    if (brandMobile) {
      brandMobile.appendChild(
        crearCheckboxItem({
          value: m,
          label: m,
          checked: isChecked,
          className: 'brand-filter-mobile mr-2',
        })
      );
    }
  });
}

function leerCheckboxSeleccionados(selectors) {
  const values = new Set();
  selectors.forEach((sel) => {
    document.querySelectorAll(sel).forEach((el) => {
      if (el.checked) values.add(normalizarTexto(el.value));
    });
  });
  return values;
}

function leerPrecioMaxDesdeUI() {
  const desktop = document.getElementById('price-range');
  const mobile = document.getElementById('price-range-mobile');
  const val = desktop ? Number(desktop.value) : mobile ? Number(mobile.value) : NaN;
  return Number.isFinite(val) ? val : null;
}

function leerSortDesdeUI() {
  const select = document.getElementById('sort-select');
  const val = select ? normalizarTexto(select.value) : '';
  return val || 'popular';
}

function actualizarPrecioUI(valor) {
  const desktop = document.getElementById('price-range');
  const mobile = document.getElementById('price-range-mobile');
  const valueDesktop = document.getElementById('price-value');
  const valueMobile = document.getElementById('price-value-mobile');

  if (desktop && Number.isFinite(valor)) desktop.value = String(valor);
  if (mobile && Number.isFinite(valor)) mobile.value = String(valor);
  if (valueDesktop && Number.isFinite(valor)) valueDesktop.textContent = `S/ ${valor}`;
  if (valueMobile && Number.isFinite(valor)) valueMobile.textContent = `S/ ${valor}`;
}

function actualizarMaxPrecioUI(max) {
  const desktop = document.getElementById('price-range');
  const mobile = document.getElementById('price-range-mobile');
  if (desktop) desktop.max = String(max);
  if (mobile) mobile.max = String(max);
}

function ordenarProductos(lista, sort) {
  const items = Array.isArray(lista) ? [...lista] : [];
  const modo = normalizarTexto(sort);

  if (modo === 'price-asc') {
    items.sort((a, b) => obtenerPrecioProducto(a) - obtenerPrecioProducto(b));
    return items;
  }
  if (modo === 'price-desc') {
    items.sort((a, b) => obtenerPrecioProducto(b) - obtenerPrecioProducto(a));
    return items;
  }
  if (modo === 'newest') {
    items.sort((a, b) => Number(b.id) - Number(a.id));
    return items;
  }

  items.sort((a, b) => {
    const da = Number(a.descuento) || 0;
    const db = Number(b.descuento) || 0;
    if (db !== da) return db - da;
    return Number(b.stock) - Number(a.stock);
  });
  return items;
}

function aplicarFiltrosDesdeUI() {
  const categoriasChecked = leerCheckboxSeleccionados(['.category-filter', '.category-filter-mobile']);
  const marcasChecked = leerCheckboxSeleccionados(['.brand-filter', '.brand-filter-mobile']);
  const precioMax = leerPrecioMaxDesdeUI();
  const sort = leerSortDesdeUI();

  estadoFiltros.categorias = categoriasChecked;
  estadoFiltros.marcas = marcasChecked;
  estadoFiltros.precioMax = precioMax;
  estadoFiltros.sort = sort;

  let filtrados = productos.filter((p) => {
    const categoria = obtenerCategoriaProducto(p);
    const marca = obtenerMarcaProducto(p);
    const precio = obtenerPrecioProducto(p);

    const okCategoria = estadoFiltros.categorias.size > 0 && estadoFiltros.categorias.has(categoria);
    const okMarca = estadoFiltros.marcas.size > 0 && estadoFiltros.marcas.has(marca);
    const okPrecio = estadoFiltros.precioMax == null || precio <= estadoFiltros.precioMax;

    return okCategoria && okMarca && okPrecio;
  });

  filtrados = ordenarProductos(filtrados, estadoFiltros.sort);
  paginaActual = 1;
  renderizarProductos(filtrados);
}

function limpiarFiltrosUI() {
  document.querySelectorAll('.category-filter, .category-filter-mobile, .brand-filter, .brand-filter-mobile').forEach((el) => {
    el.checked = true;
  });

  const sortSelect = document.getElementById('sort-select');
  if (sortSelect) sortSelect.value = 'popular';

  if (Number.isFinite(maxPrecioDisponible) && maxPrecioDisponible > 0) actualizarPrecioUI(maxPrecioDisponible);
}

function configurarSyncFiltros() {
  const catDesktop = document.getElementById('category-filters');
  const catMobile = document.getElementById('category-filters-mobile');
  const brandDesktop = document.getElementById('brand-filters');
  const brandMobile = document.getElementById('brand-filters-mobile');

  const syncChange = (fromEl, toEl) => {
    fromEl.addEventListener('change', (e) => {
      const input = e.target;
      if (!input || input.tagName !== 'INPUT' || input.type !== 'checkbox') return;
      const v = input.dataset.value;
      if (!v) return;
      if (typeof CSS !== 'undefined' && CSS.escape) {
        toEl.querySelectorAll(`input[type="checkbox"][data-value="${CSS.escape(v)}"]`).forEach((other) => {
          other.checked = input.checked;
        });
        return;
      }
      toEl.querySelectorAll('input[type="checkbox"]').forEach((other) => {
        if (other.dataset.value === v) other.checked = input.checked;
      });
    });
  };

  if (catDesktop && catMobile) {
    syncChange(catDesktop, catMobile);
    syncChange(catMobile, catDesktop);
  }
  if (brandDesktop && brandMobile) {
    syncChange(brandDesktop, brandMobile);
    syncChange(brandMobile, brandDesktop);
  }
}

function configurarEventosFiltros() {
  const aplicarBtn = document.getElementById('apply-filters');
  const limpiarBtn = document.getElementById('clear-filters');
  const aplicarMobileBtn = document.getElementById('apply-filters-mobile');
  const limpiarMobileBtn = document.getElementById('clear-filters-mobile');

  const sortSelect = document.getElementById('sort-select');
  if (sortSelect) {
    sortSelect.addEventListener('change', () => {
      aplicarFiltrosDesdeUI();
    });
  }

  const cerrarMobile = () => {
    const mobileFilters = document.getElementById('mobile-filters');
    const mobileOverlay = document.getElementById('mobile-overlay');
    if (mobileFilters) mobileFilters.classList.remove('active');
    if (mobileOverlay) mobileOverlay.classList.remove('active');
  };

  if (aplicarBtn) aplicarBtn.addEventListener('click', () => aplicarFiltrosDesdeUI());
  if (aplicarMobileBtn) aplicarMobileBtn.addEventListener('click', () => {
    aplicarFiltrosDesdeUI();
    cerrarMobile();
  });

  if (limpiarBtn) limpiarBtn.addEventListener('click', () => {
    estadoFiltros.categorias = new Set(categoriasDisponibles);
    estadoFiltros.marcas = new Set(marcasDisponibles);
    estadoFiltros.precioMax = maxPrecioDisponible;
    estadoFiltros.sort = 'popular';
    limpiarFiltrosUI();
    renderizarFiltrosUI();
    aplicarFiltrosDesdeUI();
  });
  if (limpiarMobileBtn) limpiarMobileBtn.addEventListener('click', () => {
    estadoFiltros.categorias = new Set(categoriasDisponibles);
    estadoFiltros.marcas = new Set(marcasDisponibles);
    estadoFiltros.precioMax = maxPrecioDisponible;
    estadoFiltros.sort = 'popular';
    limpiarFiltrosUI();
    renderizarFiltrosUI();
    aplicarFiltrosDesdeUI();
    cerrarMobile();
  });

  const priceDesktop = document.getElementById('price-range');
  const priceMobile = document.getElementById('price-range-mobile');
  const onPriceInput = (val) => {
    const v = Number(val);
    if (!Number.isFinite(v)) return;
    actualizarPrecioUI(v);
  };

  if (priceDesktop) {
    priceDesktop.addEventListener('input', (e) => onPriceInput(e.target.value));
  }
  if (priceMobile) {
    priceMobile.addEventListener('input', (e) => onPriceInput(e.target.value));
  }
}

function aplicarFiltrosDesdeQueryString() {
  const params = new URLSearchParams(window.location.search);
  const categoriaParam = normalizarTexto(params.get('categoria'));
  if (!categoriaParam) return;

  const categorias = categoriasDisponibles.map((c) => c.toLowerCase());
  const idx = categorias.indexOf(categoriaParam.toLowerCase());
  if (idx < 0) return;

  const categoriaExacta = categoriasDisponibles[idx];
  document.querySelectorAll('.category-filter, .category-filter-mobile').forEach((el) => {
    el.checked = normalizarTexto(el.value).toLowerCase() === categoriaExacta.toLowerCase();
  });
}

/* ============================
   🔹 FUNCIÓN PARA CORREGIR URLS DE IMAGEN
============================ */
function corregirUrlImagen(url) {
  if (!url || typeof url !== 'string') return 'assets/img/logo.png';

  console.log('🔧 URL original:', url);

  // Si ya es una URL correcta y no tiene duplicación
  if (url.startsWith('image.php?f=productos/') && !url.includes('image.php?f=uploads/')) {
    console.log('✅ URL ya está correcta');
    return url;
  }

  // Si tiene duplicación (image.php?f= dentro de image.php?f=)
  if (url.includes('image.php?f=') && url.split('image.php?f=').length > 2) {
    console.log('🔄 Corrigiendo URL duplicada');
    // Tomar solo la última parte después del último image.php?f=
    const partes = url.split('image.php?f=');
    const ultimaParte = partes[partes.length - 1];
    const urlCorregida = 'image.php?f=' + ultimaParte.replace(/^uploads\//, '');
    console.log('✅ URL corregida:', urlCorregida);
    return urlCorregida;
  }

  // Si es un nombre simple (solo el archivo)
  if (!url.includes('/') && !url.includes('\\')) {
    return 'image.php?f=productos/' + url;
  }

  // Si es otra cosa, devolver tal cual
  return url;
}

/* ============================
   🔹 INICIALIZACIÓN
============================ */
document.addEventListener('DOMContentLoaded', () => {
  cargarProductos();
  configurarMenuMobile();
  inicializarPaginacion();
});

function inicializarPaginacion() {
  const prevBtn = document.getElementById('prev-page');
  const nextBtn = document.getElementById('next-page');
  if (prevBtn) {
    prevBtn.addEventListener('click', () => {
      if (paginaActual <= 1) return;
      paginaActual -= 1;
      renderizarProductos(listaRenderActual);
    });
  }
  if (nextBtn) {
    nextBtn.addEventListener('click', () => {
      if (paginaActual >= totalPaginas) return;
      paginaActual += 1;
      renderizarProductos(listaRenderActual);
    });
  }
}

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

    // DEBUG: Ver las URLs que vienen del API
    console.log('🔍 Primer producto recibido:', {
      nombre: json.data[0]?.nombre,
      imagen: json.data[0]?.imagen,
      tipo: typeof json.data[0]?.imagen
    });

    // Verificar URLs
    console.log('🔍 Verificando URLs de los primeros 3 productos:');
    json.data.slice(0, 3).forEach((p, i) => {
      console.log(`Producto ${i+1}:`, {
        nombre: p.nombre,
        imagen: p.imagen,
        tieneDuplicacion: p.imagen?.includes('image.php?f=uploads/productos/image.php?f=')
      });
    });

    // Guardar productos en la variable global
    productos = json.data;
    console.log(`✅ ${productos.length} productos cargados exitosamente`);

    // Construir opciones de filtros (categorías/marcas/precio)
    await cargarCategoriasParaFiltros();

    // Unir categorías del API + las que realmente llegan en productos
    const setCategorias = new Set(
      (categoriasDisponibles || []).map((c) => normalizarTexto(c)).filter(Boolean)
    );
    productos.forEach((p) => setCategorias.add(obtenerCategoriaProducto(p)));
    categoriasDisponibles = Array.from(setCategorias).sort((a, b) => a.localeCompare(b));

    construirMarcasDisponibles();

    maxPrecioDisponible = Math.ceil(
      productos.reduce((max, p) => Math.max(max, obtenerPrecioProducto(p)), 0)
    );
    if (!Number.isFinite(maxPrecioDisponible) || maxPrecioDisponible <= 0) maxPrecioDisponible = 200;

    estadoFiltros.categorias = new Set(categoriasDisponibles);
    estadoFiltros.marcas = new Set(marcasDisponibles);
    estadoFiltros.precioMax = maxPrecioDisponible;
    estadoFiltros.sort = 'popular';

    actualizarMaxPrecioUI(maxPrecioDisponible);
    actualizarPrecioUI(maxPrecioDisponible);
    renderizarFiltrosUI();
    configurarSyncFiltros();
    configurarEventosFiltros();
    aplicarFiltrosDesdeQueryString();

    listaRenderActual = productos;
    aplicarFiltrosDesdeUI();
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

  listaRenderActual = Array.isArray(listaProductos) ? listaProductos : [];
  totalPaginas = Math.max(1, Math.ceil(listaRenderActual.length / productosPorPagina));
  if (paginaActual > totalPaginas) paginaActual = totalPaginas;
  if (paginaActual < 1) paginaActual = 1;

  if (!listaRenderActual || listaRenderActual.length === 0) {
    grid.innerHTML = '<p class="text-center col-span-full py-12 text-gray-500">No hay productos disponibles</p>';
    actualizarContadores(0);
    actualizarControlesPaginacion();
    return;
  }

  const inicio = (paginaActual - 1) * productosPorPagina;
  const fin = inicio + productosPorPagina;
  const paginaProductos = listaRenderActual.slice(inicio, fin);

  paginaProductos.forEach((producto) => {
    const precioOriginal = parseFloat(producto.precio);
    const precioFinal = parseFloat(producto.precio_final ?? producto.precio);
    const descuento = Number(producto.descuento) || 0;
    const sinStock = Number(producto.stock) <= 0;

    const card = document.createElement('div');
    card.className = 'product-card bg-white rounded-2xl shadow overflow-hidden relative hover:shadow-xl transition';

    const badgeHtml = descuento > 0
      ? `<div class="absolute top-4 right-4 bg-red-500 text-white text-sm px-2 py-1 rounded z-10">-${escapeHtml(String(descuento))}%</div>`
      : '';

    // CORRECCIÓN IMPORTANTE: Usar corregirUrlImagen
    const imgSrc = corregirUrlImagen(producto.imagen) || 'assets/img/logo.png';
    console.log(`🖼️ Producto: ${producto.nombre}`);
    console.log(`   Imagen original: ${producto.imagen}`);
    console.log(`   Imagen corregida: ${imgSrc}`);

    card.innerHTML = `
      <div class="relative">
        ${badgeHtml}
        <div class="relative h-64 bg-gradient-to-b from-pink-50 to-white flex items-center justify-center">
          <img src="${escapeHtml(imgSrc)}" alt="${escapeHtml(producto.nombre)}" 
               onerror="this.src='assets/img/logo.png'; console.error('❌ Error cargando imagen: ${escapeHtml(imgSrc)}')" 
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
                style="${sinStock ? 'opacity:0.6; cursor:not-allowed;' : ''}"
                ${sinStock ? 'disabled' : ''} 
                onclick="agregarProductoDesdeTienda(${Number(producto.id)})">
          ${sinStock ? 'Agotado' : 'Agregar al carrito'}
        </button>
      </div>
    `;

    grid.appendChild(card);
  });

  actualizarContadores(listaRenderActual.length);
  actualizarControlesPaginacion();
}

function actualizarControlesPaginacion() {
  const container = document.getElementById('pagination-container');
  const pageNumbers = document.getElementById('page-numbers');
  const prevBtn = document.getElementById('prev-page');
  const nextBtn = document.getElementById('next-page');

  if (!container || !pageNumbers) return;

  if (totalPaginas <= 1) {
    container.style.display = 'none';
    return;
  }

  container.style.display = 'flex';

  if (prevBtn) prevBtn.disabled = paginaActual <= 1;
  if (nextBtn) nextBtn.disabled = paginaActual >= totalPaginas;

  pageNumbers.innerHTML = '';

  const maxBotones = 5;
  let start = Math.max(1, paginaActual - Math.floor(maxBotones / 2));
  let end = Math.min(totalPaginas, start + maxBotones - 1);
  start = Math.max(1, end - maxBotones + 1);

  for (let p = start; p <= end; p += 1) {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = p === paginaActual ? 'pagination-btn active' : 'pagination-btn';
    btn.textContent = String(p);
    btn.addEventListener('click', () => {
      if (p === paginaActual) return;
      paginaActual = p;
      renderizarProductos(listaRenderActual);
    });
    pageNumbers.appendChild(btn);
  }
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

  console.log('📤 Agregando producto al carrito:');
  console.log('   Nombre:', producto.nombre);
  console.log('   Imagen antes de sanear:', producto.imagen);

  // CORRECCIÓN: Usar corregirUrlImagen también aquí
  const imagenCorregida = corregirUrlImagen(producto.imagen);
  console.log('   Imagen corregida:', imagenCorregida);

  // Usar el gestor centralizado con imagen corregida
  const resultado = agregarAlCarrito({
    id: producto.id,
    nombre: producto.nombre,
    precio: parseFloat(producto.precio_final ?? producto.precio),
    imagen: imagenCorregida || 'assets/img/logo.png',
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
  if (totalCountEl) {
    const total = Array.isArray(productos) ? productos.length : cantidad;
    totalCountEl.textContent = String(total);
  }
}

function configurarMenuMobile() {
  const btn = document.getElementById('menu-btn');
  const menu = document.getElementById('mobile-menu');
  if (!btn || !menu) return;
  btn.addEventListener('click', () => menu.classList.toggle('active'));
}