// cart-manager.js - Gestor centralizado del carrito de compras
// Este archivo maneja TODA la lógica del carrito para evitar duplicación

/* ============================
   🔹 UTILIDADES GLOBALES
============================ */
window.escapeHtml = function(str) {
  if (str == null) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
};

/* ============================
   🔹 GESTIÓN DEL CARRITO
============================ */

const CARRITO_KEY = 'carrito_samyglow';

/**
 * Obtiene el carrito desde localStorage
 * @returns {Array} Array de productos en el carrito
 */
window.obtenerCarrito = function() {
  try {
    const raw = localStorage.getItem(CARRITO_KEY);
    if (!raw) return [];
    const parsed = JSON.parse(raw);
    return Array.isArray(parsed) ? parsed : [];
  } catch (err) {
    console.error('Error obteniendo carrito:', err);
    return [];
  }
};

/**
 * Guarda el carrito en localStorage
 * @param {Array} carrito - Array de productos
 */
window.guardarCarrito = function(carrito) {
  try {
    localStorage.setItem(CARRITO_KEY, JSON.stringify(carrito));
  } catch (err) {
    console.error('Error guardando carrito:', err);
  }
};

/**
 * Agrega un producto al carrito
 * @param {Object} producto - Datos del producto
 * @param {number} producto.id - ID del producto
 * @param {string} producto.nombre - Nombre del producto
 * @param {number} producto.precio - Precio final del producto
 * @param {string} producto.imagen - Ruta de la imagen
 * @param {number} producto.stock - Stock disponible
 * @returns {Object} {success: boolean, message: string}
 */
window.agregarAlCarrito = function(producto) {
  const carrito = obtenerCarrito();

  // Validar stock
  if (Number(producto.stock) <= 0) {
    return { success: false, message: 'Producto sin stock' };
  }

  // Buscar si ya existe en el carrito
  const existente = carrito.find(item => Number(item.id) === Number(producto.id));

  if (existente) {
    // Verificar stock disponible
    if (existente.cantidad + 1 > Number(producto.stock)) {
      return { success: false, message: 'Stock insuficiente' };
    }
    existente.cantidad += 1;
  } else {
    // Agregar nuevo producto
    carrito.push({
      id: Number(producto.id),
      nombre: producto.nombre,
      precio: parseFloat(producto.precio),
      imagen: producto.imagen || 'assets/img/logo.png',
      cantidad: 1,
      stock: Number(producto.stock)
    });
  }

  guardarCarrito(carrito);
  actualizarContadorCarrito();
  
  return { success: true, message: 'Producto agregado al carrito' };
};

/**
 * Actualiza la cantidad de un producto en el carrito
 * @param {number} productoId - ID del producto
 * @param {number} nuevaCantidad - Nueva cantidad
 * @returns {Object} {success: boolean, message: string}
 */
window.actualizarCantidadCarrito = function(productoId, nuevaCantidad) {
  const carrito = obtenerCarrito();
  const item = carrito.find(i => Number(i.id) === Number(productoId));

  if (!item) {
    return { success: false, message: 'Producto no encontrado' };
  }

  if (nuevaCantidad < 1) {
    return eliminarDelCarrito(productoId);
  }

  if (item.stock && nuevaCantidad > item.stock) {
    return { success: false, message: 'Stock insuficiente' };
  }

  item.cantidad = nuevaCantidad;
  guardarCarrito(carrito);
  actualizarContadorCarrito();

  return { success: true, message: 'Cantidad actualizada' };
};

/**
 * Elimina un producto del carrito
 * @param {number} productoId - ID del producto
 * @returns {Object} {success: boolean, message: string}
 */
window.eliminarDelCarrito = function(productoId) {
  let carrito = obtenerCarrito();
  const longitudAntes = carrito.length;
  
  carrito = carrito.filter(item => Number(item.id) !== Number(productoId));
  
  if (carrito.length === longitudAntes) {
    return { success: false, message: 'Producto no encontrado' };
  }

  guardarCarrito(carrito);
  actualizarContadorCarrito();

  return { success: true, message: 'Producto eliminado' };
};

/**
 * Vacía completamente el carrito
 */
window.vaciarCarrito = function() {
  localStorage.removeItem(CARRITO_KEY);
  actualizarContadorCarrito();
};

/**
 * Calcula el total del carrito
 * @returns {Object} {subtotal, envio, total}
 */
window.calcularTotalCarrito = function() {
  const carrito = obtenerCarrito();
  const subtotal = carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
  const envio = subtotal >= 150 ? 0 : 10;
  const total = subtotal + envio;

  return { subtotal, envio, total };
};

/**
 * Actualiza el contador visual del carrito en el header
 */
window.actualizarContadorCarrito = function() {
  const countEl = document.getElementById('cart-count');
  if (!countEl) {
    console.warn('⚠️ Elemento #cart-count no encontrado en el DOM');
    return;
  }

  const carrito = obtenerCarrito();
  const totalItems = carrito.reduce((sum, item) => sum + (Number(item.cantidad) || 0), 0);

  countEl.textContent = String(totalItems);
  
  // Asegurar que siempre sea visible cuando hay items
  if (totalItems > 0) {
    countEl.style.display = 'flex';
    countEl.style.visibility = 'visible';
    countEl.style.opacity = '1';
  } else {
    countEl.style.display = 'none';
  }

  console.log(`✅ Contador actualizado: ${totalItems} items`);
};

/* ============================
   🔹 NOTIFICACIONES
============================ */

/**
 * Muestra una notificación temporal
 * @param {string} mensaje - Texto a mostrar
 * @param {string} tipo - 'success', 'warning', 'error'
 */
window.mostrarNotificacion = function(mensaje, tipo = 'success') {
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
  div.className = `fixed top-20 right-4 ${colores[tipo]} text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2 animate-fade-in`;
  div.innerHTML = `
    <i class="fas ${iconos[tipo]}"></i>
    <span>${escapeHtml(mensaje)}</span>
  `;

  document.body.appendChild(div);

  setTimeout(() => {
    div.style.opacity = '0';
    div.style.transition = 'opacity 0.3s';
    setTimeout(() => div.remove(), 300);
  }, 3000);
};

/* ============================
   🔹 INICIALIZACIÓN
============================ */

// Actualizar contador al cargar cualquier página
document.addEventListener('DOMContentLoaded', () => {
  console.log('🚀 cart-manager.js iniciado');
  
  // Esperar un momento para asegurar que el DOM está listo
  setTimeout(() => {
    actualizarContadorCarrito();
  }, 100);
});

// También actualizar cuando la página se vuelve visible (cambio de pestaña)
document.addEventListener('visibilitychange', () => {
  if (!document.hidden) {
    actualizarContadorCarrito();
  }
});