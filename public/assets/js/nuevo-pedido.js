// public/assets/js/nuevo-pedido.js
(() => {
  // Config
  const API_PRODUCTOS = 'index.php?view=api-productos';
  const API_CLIENTES = 'index.php?view=api-clientes';
  const API_CLIENTE_SAVE = 'index.php?view=api-cliente-save';
  const API_SAVE_PEDIDO = 'index.php?view=api-save-pedido';

  // Estado UI
  let products = [];      // traerá todos los productos desde backend
  let cart = [];          // { producto_id, nombre, precio (opcional), cantidad, stock }
  let selectedClient = null;
  let orderNotes = '';

  // DOM
  const productsGrid = document.getElementById('productsGrid');
  const categoryFilter = document.getElementById('categoryFilter');
  const searchProduct = document.getElementById('searchProduct');
  const cartItemsEl = document.getElementById('cartItems');
  const subtotalEl = document.getElementById('subtotal');
  const discountRow = document.getElementById('discountRow');
  const discountEl = document.getElementById('discount');
  const totalEl = document.getElementById('total');

  const searchClientInput = document.getElementById('searchClient');
  const clientSuggestions = document.getElementById('clientSuggestions');
  const selectedClientEl = document.getElementById('selectedClient');
  const clientNameEl = document.getElementById('clientName');
  const clientInfoEl = document.getElementById('clientInfo');

  const toggleClientFormBtn = document.getElementById('toggleClientForm');
  const newClientForm = document.getElementById('newClientForm');
  const saveClientBtn = document.getElementById('saveClientBtn');

  const notesTextarea = document.getElementById('orderNotes');
  const notesCounter = document.getElementById('notesCounter');
  const savedNotesEl = document.getElementById('savedNotes');
  const notesPreview = document.getElementById('notesPreview');

  const selectFragancia = document.getElementById('selectFragancia');
  const selectCrema = document.getElementById('selectCrema');
  const promoQuantity = document.getElementById('promoQuantity');
  const promoCountEl = document.getElementById('promoCount');
  const addPromoBtn = document.getElementById('addPromoBtn');

  // Inicial
  document.addEventListener('DOMContentLoaded', () => {
    loadProducts();
    setupEventListeners();
    renderCart();
    updateNotesCounter();
  });

  // ---------------------------
  // Fetch / API
  // ---------------------------
  async function loadProducts() {
    try {
      const res = await fetch(API_PRODUCTOS);
      if (!res.ok) throw new Error('Error al cargar productos');
      products = await res.json();
      renderProducts();
    } catch (err) {
      console.error(err);
      productsGrid.innerHTML = `<div class="text-red-500 p-4">No se pudieron cargar los productos</div>`;
    }
  }

  async function searchClientsAPI(q) {
    try {
      const res = await fetch(`${API_CLIENTES}&q=${encodeURIComponent(q)}`);
      if (!res.ok) throw new Error('Error buscando clientes');
      return await res.json();
    } catch (err) {
      console.error(err);
      return [];
    }
  }

  async function saveClientAPI(formData) {
    try {
      const res = await fetch(API_CLIENTE_SAVE, {
        method: 'POST',
        body: new URLSearchParams(formData) // tu controller espera $_POST
      });
      if (!res.ok) throw new Error('Error guardando cliente');
      return await res.json();
    } catch (err) {
      console.error(err);
      return { ok: false };
    }
  }

  async function saveOrderAPI(payload) {
    try {
      const res = await fetch(API_SAVE_PEDIDO, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      if (!res.ok) throw new Error('Error guardando pedido');
      return await res.json();
    } catch (err) {
      console.error(err);
      return { ok: false, error: err.message };
    }
  }

  // ---------------------------
  // Eventos y UI
  // ---------------------------
  function setupEventListeners() {
    categoryFilter?.addEventListener('change', renderProducts);
    searchProduct?.addEventListener('input', renderProducts);

    // Clientes
    toggleClientFormBtn?.addEventListener('click', toggleClientForm);
    searchClientInput?.addEventListener('input', debounce(handleClientSearch, 300));
    saveClientBtn?.addEventListener('click', handleSaveClient);

    // Notas
    document.getElementById('toggleNotes')?.addEventListener('click', toggleNotesSection);
    notesTextarea?.addEventListener('input', updateNotesCounter);
    document.getElementById('clearNotes')?.addEventListener('click', () => { notesTextarea.value=''; updateNotesCounter(); });
    document.getElementById('saveNotes')?.addEventListener('click', saveNotes);
    document.getElementById('editNotes')?.addEventListener('click', editNotes);

    // Promoción
    document.querySelector('.decrease-promo')?.addEventListener('click', () => { promoQuantity.value = Math.max(1, Number(promoQuantity.value)-1); updatePrecioNormal(); });
    document.querySelector('.increase-promo')?.addEventListener('click', () => { promoQuantity.value = Math.min(10, Number(promoQuantity.value)+1); updatePrecioNormal(); });
    promoQuantity?.addEventListener('change', () => { promoQuantity.value = Math.max(1, Math.min(10, Number(promoQuantity.value)||1)); updatePrecioNormal(); });
    addPromoBtn?.addEventListener('click', addPromoHandler);

    // Finalizar / Cancelar
    document.getElementById('finalizeOrder')?.addEventListener('click', finalizeOrder);
    document.getElementById('cancelBtn')?.addEventListener('click', cancelOrder);
  }

  // ---------------------------
  // Productos UI
  // ---------------------------
  function renderProducts() {
    if (!productsGrid) return;
    const cat = categoryFilter?.value || 'all';
    const term = (searchProduct?.value || '').toLowerCase();

    const filtered = products.filter(p => {
      if (cat !== 'all' && String(p.categoria_id || p.categoria) !== String(cat)) return false;
      if (term && !p.nombre.toLowerCase().includes(term)) return false;
      return true;
    });

    if (filtered.length === 0) {
      productsGrid.innerHTML = `<div class="p-4 text-gray-500">No hay productos</div>`;
      return;
    }

    productsGrid.innerHTML = filtered.map(p => {
      const stock = p.stock || 0;
      const badge = stock > 5 ? 'badge-success' : 'badge-warning';
      return `
        <div class="product-card p-4 rounded-lg border" data-product-id="${p.id}" role="button">
          <div class="flex space-x-3">
            <div class="w-12 h-12 rounded-full bg-pink-100 flex items-center justify-center flex-shrink-0">
              <i class="fas fa-cube text-pink-500"></i>
            </div>
            <div class="flex-1">
              <h4 class="font-semibold text-gray-900 text-sm">${escapeHtml(p.nombre)}</h4>
              <p class="text-xs text-gray-600 mb-2">${escapeHtml((p.categoria || p.categoria_id) ? getCategoryName(p.categoria || p.categoria_id) : '')}</p>
              <div class="flex justify-between items-center">
                <span class="font-bold text-pink-600">S/ ${Number(p.precio).toFixed(2)}</span>
                <span class="badge ${badge}">
                  ${stock} disponibles
                </span>
              </div>
            </div>
          </div>
        </div>
      `;
    }).join('');

    // listeners
    document.querySelectorAll('.product-card').forEach(card => {
      card.addEventListener('click', () => {
        const id = Number(card.dataset.productId);
        addToCart(id, 1);
      });
    });
  }

  function getCategoryName(id) {
    if (id == 1) return 'Fragancia';
    if (id == 2) return 'Crema Corporal';
    if (id == 3) return 'Body Splash';
    return '';
  }

  // ---------------------------
  // Carrito
  // ---------------------------
  function addToCart(producto_id, cantidad) {
    const prod = products.find(p => Number(p.id) === Number(producto_id));
    if (!prod) return alert('Producto no encontrado');

    const existing = cart.find(i => Number(i.producto_id) === Number(producto_id) && !i.esPromocion);
    if (existing) {
      if (existing.cantidad + cantidad > (prod.stock || 0)) return alert('No hay suficiente stock');
      existing.cantidad += cantidad;
    } else {
      if (cantidad > (prod.stock || 0)) return alert('No hay suficiente stock');
      cart.push({
        producto_id: Number(prod.id),
        nombre: prod.nombre,
        cantidad: cantidad,
        precio: Number(prod.precio),
        stock: prod.stock || 0,
        esPromocion: false
      });
    }
    renderCart();
  }

  function addPromoHandler() {
    const frag = Number(selectFragancia.value);
    const crema = Number(selectCrema.value);
    const qty = Number(promoQuantity.value) || 1;
    if (!frag || !crema) return alert('Selecciona fragancia y crema');

    // cada combo se guarda como item con esPromocion=true y productosIncluidos
    for (let i = 0; i < qty; i++) {
      cart.push({
        producto_id: `promo-${Date.now()}-${Math.random().toString(36).slice(2,7)}`, // id único para UI
        nombre: `PROMO: ${getNameById(frag)} + ${getNameById(crema)}`,
        cantidad: 1,
        precio: 125.00,
        esPromocion: true,
        productosIncluidos: [frag, crema], // para backend podría descomponerse si se desea
      });
    }

    updatePromoCounter();
    renderCart();

    // reset
    selectFragancia.value = '';
    selectCrema.value = '';
    promoQuantity.value = '1';
    updatePrecioNormal();
  }

  function getNameById(id) {
    const p = products.find(x => Number(x.id) === Number(id));
    return p ? p.nombre : 'Producto';
  }

  function updatePromoCounter() {
    const c = cart.filter(i => i.esPromocion).length;
    promoCountEl.textContent = c;
  }

  function renderCart() {
    if (!cartItemsEl) return;
    if (cart.length === 0) {
      cartItemsEl.innerHTML = `<div class="text-center text-gray-500 py-8"><i class="fas fa-shopping-cart text-3xl mb-2"></i><p>No hay productos agregados</p></div>`;
      subtotalEl.textContent = 'S/ 0.00';
      totalEl.textContent = 'S/ 0.00';
      discountRow.style.display = 'none';
      return;
    }

    let subtotal = 0;
    let discount = 0;

    cartItemsEl.innerHTML = '';
    cart.forEach(item => {
      const itemTotal = (item.precio || 0) * item.cantidad;
      if (!item.esPromocion) subtotal += itemTotal;
      else discount += ((item.precio === 125) ? (( (item.precio + 0) ) - 125) : 0); // promos handled in UI
      const node = document.createElement('div');
      node.className = `cart-item p-3 border rounded-lg ${item.esPromocion ? 'promo-applied' : ''}`;
      node.innerHTML = `
        <div class="flex justify-between items-center">
          <div class="flex-1">
            <h4 class="font-semibold text-sm ${item.esPromocion ? 'text-green-700' : 'text-gray-900'}">${escapeHtml(item.nombre)} ${item.esPromocion ? '<span class="badge badge-success ml-2">PROMO</span>' : ''}</h4>
            <p class="text-gray-600 text-sm">S/ ${Number(item.precio).toFixed(2)} c/u</p>
          </div>
          <div class="flex items-center space-x-2">
            ${!item.esPromocion ? `<button class="quantity-btn decrease" data-id="${item.producto_id}">-</button>
            <input type="number" value="${item.cantidad}" min="1" max="${item.stock || 999}" class="quantity-input" data-id="${item.producto_id}">
            <button class="quantity-btn increase" data-id="${item.producto_id}">+</button>` : `<span class="text-sm text-gray-500">1 unidad</span>`}
          </div>
          <div class="text-right">
            <p class="font-semibold ${item.esPromocion ? 'text-green-700' : ''}">S/ ${(itemTotal).toFixed(2)}</p>
            <button class="text-red-500 hover:text-red-700 text-sm remove-item" data-id="${item.producto_id}" data-es-promocion="${item.esPromocion}"><i class="fas fa-trash"></i></button>
          </div>
        </div>
      `;
      cartItemsEl.appendChild(node);
    });

    // Notar: la lógica de descuento se muestra en UI si hay promo(s)
    const total = subtotal - discount;
    subtotalEl.textContent = `S/ ${subtotal.toFixed(2)}`;
    if (discount > 0) {
      discountRow.style.display = 'flex';
      discountEl.textContent = `- S/ ${discount.toFixed(2)}`;
    } else {
      discountRow.style.display = 'none';
    }
    totalEl.textContent = `S/ ${total.toFixed(2)}`;

    // attach listeners (delegado simple)
    attachCartListeners();
  }

  function attachCartListeners() {
    // cantidad botones
    document.querySelectorAll('.quantity-btn.decrease').forEach(btn => btn.onclick = () => {
      const id = btn.dataset.id;
      const item = cart.find(i => String(i.producto_id) === String(id) && !i.esPromocion);
      if (!item) return;
      if (item.cantidad > 1) item.cantidad--;
      renderCart();
    });
    document.querySelectorAll('.quantity-btn.increase').forEach(btn => btn.onclick = () => {
      const id = btn.dataset.id;
      const item = cart.find(i => String(i.producto_id) === String(id) && !i.esPromocion);
      if (!item) return;
      if (item.cantidad < (item.stock || 999)) item.cantidad++;
      renderCart();
    });

    document.querySelectorAll('.quantity-input').forEach(input => {
      input.onchange = () => {
        const id = input.dataset.id;
        const item = cart.find(i => String(i.producto_id) === String(id) && !i.esPromocion);
        if (!item) return;
        const v = Number(input.value) || 1;
        if (v < 1) input.value = item.cantidad;
        else {
          item.cantidad = v;
          renderCart();
        }
      };
    });

    document.querySelectorAll('.remove-item').forEach(btn => {
      btn.onclick = () => {
        const id = btn.dataset.id;
        const isPromo = btn.dataset.esPromocion === 'true';
        cart = cart.filter(i => String(i.producto_id) !== String(id));
        if (isPromo) updatePromoCounter();
        renderCart();
      };
    });
  }

  // ---------------------------
  // Clientes
  // ---------------------------
  async function handleClientSearch(e) {
    const q = e.target.value.trim();
    if (q.length < 2) {
      clientSuggestions.classList.add('hidden');
      return;
    }
    const list = await searchClientsAPI(q);
    if (!Array.isArray(list) || list.length === 0) {
      clientSuggestions.classList.add('hidden');
      return;
    }
    clientSuggestions.innerHTML = list.map(c => `
      <div class="p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200 client-suggestion" data-client-id="${c.id}" data-client-nombre="${escapeHtml(c.nombres)}" data-client-apellidos="${escapeHtml(c.apellidos)}" data-client-dni="${escapeHtml(c.dni)}" data-client-correo="${escapeHtml(c.correo)}" data-client-telefono="${escapeHtml(c.telefono)}">
        <div class="font-medium">${escapeHtml(c.nombres)} ${escapeHtml(c.apellidos)}</div>
        <div class="text-sm text-gray-600">DNI: ${escapeHtml(c.dni)} | ${escapeHtml(c.correo)}</div>
      </div>
    `).join('');
    clientSuggestions.classList.remove('hidden');

    document.querySelectorAll('.client-suggestion').forEach(el => {
      el.addEventListener('click', () => {
        const id = el.dataset.clientId;
        const nombres = el.dataset.clientNombre;
        const apellidos = el.dataset.clientApellidos;
        const dni = el.dataset.clientDni;
        const correo = el.dataset.clientCorreo;
        const telefono = el.dataset.clientTelefono;
        selectedClient = { id, nombres, apellidos, dni, correo, telefono };
        showSelectedClient();
        clientSuggestions.classList.add('hidden');
        searchClientInput.value = '';
      });
    });
  }

  function showSelectedClient() {
    if (!selectedClient) return;
    clientNameEl.textContent = `${selectedClient.nombres} ${selectedClient.apellidos}`;
    clientInfoEl.textContent = `DNI: ${selectedClient.dni} | ${selectedClient.correo} | ${selectedClient.telefono}`;
    selectedClientEl.classList.remove('hidden');
  }

  document.getElementById('removeClient')?.addEventListener('click', () => {
    selectedClient = null;
    selectedClientEl.classList.add('hidden');
  });

  function toggleClientForm() {
    newClientForm.classList.toggle('open');
    if (newClientForm.classList.contains('open')) {
      toggleClientFormBtn.innerHTML = '<i class="fas fa-times mr-2"></i>Cerrar Formulario';
      toggleClientFormBtn.classList.remove('bg-pink-500','hover:bg-pink-600');
      toggleClientFormBtn.classList.add('bg-gray-500','hover:bg-gray-600');
    } else {
      toggleClientFormBtn.innerHTML = '<i class="fas fa-user-plus mr-2"></i>Nuevo Cliente';
      toggleClientFormBtn.classList.remove('bg-gray-500','hover:bg-gray-600');
      toggleClientFormBtn.classList.add('bg-pink-500','hover:bg-pink-600');
      // clear
      document.getElementById('clientNombres').value = '';
      document.getElementById('clientApellidos').value = '';
      document.getElementById('clientDni').value = '';
      document.getElementById('clientTelefono').value = '';
      document.getElementById('clientCorreo').value = '';
    }
  }

  async function handleSaveClient(e) {
    e.preventDefault();
    const nombres = document.getElementById('clientNombres').value.trim();
    const apellidos = document.getElementById('clientApellidos').value.trim();
    const dni = document.getElementById('clientDni').value.trim();
    const telefono = document.getElementById('clientTelefono').value.trim();
    const correo = document.getElementById('clientCorreo').value.trim();

    if (!nombres || !apellidos || !dni || !correo) return alert('Completa campos obligatorios');
    if (dni.length !== 8) return alert('DNI debe tener 8 dígitos');

    const form = { nombres, apellidos, dni, telefono, correo };
    const res = await saveClientAPI(form);
    if (res && res.ok) {
      // Si el controlador devuelve el nuevo cliente (mejor), úsalo. Si sólo devuelve ok=true, hacemos buscar.
      selectedClient = res.cliente || { id: res.id || null, nombres, apellidos, dni, correo, telefono };
      showSelectedClient();
      toggleClientForm();
      alert('Cliente guardado exitosamente');
    } else {
      alert('Error guardando cliente');
    }
  }

  // ---------------------------
  // Notas
  // ---------------------------
  function toggleNotesSection() {
    const notesSection = document.getElementById('notesSection');
    const toggleBtn = document.getElementById('toggleNotes');
    notesSection.classList.toggle('open');
    if (notesSection.classList.contains('open')) {
      toggleBtn.innerHTML = '<i class="fas fa-times mr-1"></i>Cerrar Notas';
      toggleBtn.classList.remove('bg-gray-100','text-gray-700','hover:bg-gray-200');
      toggleBtn.classList.add('bg-pink-500','text-white','hover:bg-pink-600');
    } else {
      toggleBtn.innerHTML = '<i class="fas fa-edit mr-1"></i>Agregar Notas';
      toggleBtn.classList.remove('bg-pink-500','text-white','hover:bg-pink-600');
      toggleBtn.classList.add('bg-gray-100','text-gray-700','hover:bg-gray-200');
    }
  }

  function updateNotesCounter() {
    const length = notesTextarea?.value.length || 0;
    notesCounter.textContent = `${length}/500 caracteres`;
    if (length > 450) {
      notesCounter.classList.add('text-red-500');
      notesCounter.classList.remove('text-gray-500');
    } else {
      notesCounter.classList.remove('text-red-500');
      notesCounter.classList.add('text-gray-500');
    }
  }

  function saveNotes() {
    const txt = notesTextarea.value.trim();
    if (!txt) return alert('Escribe alguna nota antes de guardar');
    orderNotes = txt;
    notesPreview.textContent = txt.length > 100 ? txt.substring(0,100) + '...' : txt;
    savedNotesEl.classList.remove('hidden');
    toggleNotesSection();
    alert('Notas guardadas');
  }

  function editNotes() {
    notesTextarea.value = orderNotes;
    updateNotesCounter();
    toggleNotesSection();
  }

  // ---------------------------
  // Finalizar / Cancelar pedido
  // ---------------------------
  async function finalizeOrder() {
    if (cart.length === 0) return alert('Agrega al menos un producto');
    if (!selectedClient || !selectedClient.id) return alert('Selecciona o registra un cliente');

    // construir payload: cliente (id), items [{producto_id,cantidad}], payment, notes
    const items = [];
    for (const it of cart) {
      if (it.esPromocion) {
        // expansion: cada promoción contiene productosIncluidos
        if (Array.isArray(it.productosIncluidos)) {
          it.productosIncluidos.forEach(pid => items.push({ producto_id: Number(pid), cantidad: 1 }));
        } else {
          // fallback: si no hay desglose, ignorar (no debería pasar)
        }
      } else {
        items.push({ producto_id: Number(it.producto_id), cantidad: Number(it.cantidad) });
      }
    }

    const payload = {
      cliente: Number(selectedClient.id),
      items: items,
      payment: Number(document.querySelector('input[name="payment"]:checked').value),
      notes: orderNotes
    };

    // enviar
    const res = await saveOrderAPI(payload);
    if (res && res.ok) {
      alert('Pedido guardado. ID: ' + (res.pedido_id || ' — '));
      // reset
      cart = [];
      selectedClient = null;
      orderNotes = '';
      renderCart();
      selectedClientEl.classList.add('hidden');
      savedNotesEl.classList.add('hidden');
      notesTextarea.value = '';
      updateNotesCounter();
      updatePromoCounter();
    } else {
      alert('Error guardando pedido: ' + (res.error || ''));
      console.error(res);
    }
  }

  function cancelOrder() {
    if (!confirm('¿Estás seguro que quieres cancelar el pedido?')) return;
    cart = [];
    selectedClient = null;
    orderNotes = '';
    renderCart();
    selectedClientEl.classList.add('hidden');
    document.getElementById('newClientForm').classList.remove('open');
    // limpiar form
    document.getElementById('clientNombres').value = '';
    document.getElementById('clientApellidos').value = '';
    document.getElementById('clientDni').value = '';
    document.getElementById('clientTelefono').value = '';
    document.getElementById('clientCorreo').value = '';
    notesTextarea.value = '';
    updateNotesCounter();
  }

  // ---------------------------
  // Helpers
  // ---------------------------
  function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>"'`=\/]/g, s => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[s]));
  }

  function debounce(fn, wait) {
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(()=>fn(...args), wait); };
  }

  function updatePrecioNormal() {
    const q = Number(promoQuantity.value) || 1;
    const precioNormalTotal = 130 * q;
    const el = document.getElementById('precioNormal');
    if (el) el.textContent = `S/ ${precioNormalTotal.toFixed(2)}`;
  }

})();
