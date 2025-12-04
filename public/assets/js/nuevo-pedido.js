// assets/js/nuevo-pedido.js - VERSIÓN CON MODAL DE ÉXITO
// DEPENDE DE: cart-manager.js y carrito.js

/*
  Constantes de rutas usadas por este script.
  Mantener aquí las rutas facilita cambios y pruebas.
*/
const API_METODOS_PAGO = './api/metodos-pago.php';
const API_GUARDAR_PEDIDO = './api/guardar-pedido.php';
const DEP_CART_MANAGER = './assets/js/cart-manager.js';
const DEP_CARRITO = './assets/js/carrito.js';
const INDEX_PAGE = '../index.html';
const STORAGE_KEY_CARRITO = 'carrito';


/**
 * Función principal que se llama desde carrito.js
 * Se encarga de construir y mostrar el formulario de checkout.
 */
async function iniciarCheckout() {
  console.log('Iniciando checkout...');
  const cartContent = document.getElementById('cart-content');
  if (!cartContent) return;

  // 1. Deshabilitar botones de "Proceder al Pago" y cambiar su apariencia
  const botonesPago = document.querySelectorAll('button[onclick="procederAlPago()"], button[onclick="proceedToCheckout()"]');
  botonesPago.forEach(btn => {
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';
    btn.classList.add('opacity-50', 'cursor-not-allowed');
    btn.classList.remove('btn-pulse');
  });
  
  // 2. Ocultar el sticky checkout móvil
  const mobileCheckout = document.querySelector('.mobile-checkout');
  if (mobileCheckout) {
    mobileCheckout.style.display = 'none';
  }

  // 3. Buscar métodos de pago
  let metodosDePago = [];
  try {
    const response = await fetch(API_METODOS_PAGO);
    const data = await response.json();
    if (data.success && data.data.length > 0) {
      metodosDePago = data.data;
    } else {
      throw new Error(data.message || 'No se cargaron los métodos de pago.');
    }
  } catch (error) {
    console.error('Error fetching payment methods:', error);
    mostrarNotificacion('Error al cargar métodos de pago. Intente de nuevo.', 'error');
    reactivarBotonesPago();
    return;
  }

  // 4. Crear el contenedor del formulario si no existe
  let formContainer = document.getElementById('checkout-form-container');
  if (!formContainer) {
    formContainer = document.createElement('div');
    formContainer.id = 'checkout-form-container';
    formContainer.className = 'mt-8';
    
    // Insertarlo después del listado de productos
    const productosContainer = document.querySelector('.lg\\:col-span-2');
    if (productosContainer) {
      productosContainer.appendChild(formContainer);
    } else {
      cartContent.appendChild(formContainer);
    }
  }
  
  // 5. Renderizar el HTML del formulario
  formContainer.innerHTML = `
    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 slide-in">
      <h2 class="text-2xl font-bold mb-6 flex items-center">
        <i class="fas fa-user-check mr-3" style="color: var(--rosa-neon);"></i>
        Completa tu Información
      </h2>

      <form id="form-checkout">
        <!-- Sección Datos del Cliente -->
        <section class="mb-6">
          <h3 class="text-lg font-semibold border-b pb-2 mb-4">1. Datos Personales</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="checkout-nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombres *</label>
              <input type="text" id="checkout-nombre" name="nombre" class="form-input" required>
            </div>
            <div>
              <label for="checkout-apellido" class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
              <input type="text" id="checkout-apellido" name="apellido" class="form-input" required>
            </div>
            <div>
              <label for="checkout-email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
              <input type="email" id="checkout-email" name="email" class="form-input" required>
            </div>
            <div>
              <label for="checkout-telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono *</label>
              <input type="tel" id="checkout-telefono" name="telefono" class="form-input" required>
            </div>
            <div>
              <label for="checkout-documento" class="block text-sm font-medium text-gray-700 mb-1">Documento (DNI/CE) *</label>
              <input type="text" id="checkout-documento" name="documento" class="form-input" required>
            </div>
          </div>
        </section>

        <!-- Sección Dirección de Envío -->
        <section class="mb-6">
          <h3 class="text-lg font-semibold border-b pb-2 mb-4">2. Dirección de Envío</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
              <label for="checkout-direccion" class="block text-sm font-medium text-gray-700 mb-1">Dirección *</label>
              <input type="text" id="checkout-direccion" name="direccion" class="form-input" placeholder="Calle, Número, Departamento" required>
            </div>
            <div>
              <label for="checkout-referencia" class="block text-sm font-medium text-gray-700 mb-1">Referencia (opcional)</label>
              <input type="text" id="checkout-referencia" name="referencia" class="form-input" placeholder="Entre qué calles, punto de referencia">
            </div>
            <div>
              <label for="checkout-ciudad" class="block text-sm font-medium text-gray-700 mb-1">Ciudad *</label>
              <select id="checkout-ciudad" name="ciudad" class="form-input" required>
                <option value="">Selecciona tu ciudad</option>
                <option value="Lima">Lima</option>
                <option value="Arequipa">Arequipa</option>
                <option value="Trujillo">Trujillo</option>
                <option value="Chiclayo">Chiclayo</option>
                <option value="Piura">Piura</option>
                <option value="Iquitos">Iquitos</option>
                <option value="Cusco">Cusco</option>
                <option value="Huancayo">Huancayo</option>
              </select>
            </div>
            <div>
              <label for="checkout-codigo-postal" class="block text-sm font-medium text-gray-700 mb-1">Código Postal *</label>
              <input type="text" id="checkout-codigo-postal" name="codigo_postal" class="form-input" required>
            </div>
          </div>
        </section>

        <!-- Sección Método de Pago -->
        <section class="mb-6">
          <h3 class="text-lg font-semibold border-b pb-2 mb-4">3. Método de Pago</h3>
          <div class="space-y-3">
            ${metodosDePago.map(metodo => `
              <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer hover:border-[#FF1493] transition payment-method-label">
                <input type="radio" name="metodo_pago" value="${metodo.id}" class="mr-3" required>
                <div class="flex-1">
                  <div class="font-semibold">${metodo.nombre}</div>
                  <div class="text-sm text-gray-600">${metodo.descripcion}</div>
                </div>
              </label>
            `).join('')}
          </div>
        </section>

        <!-- Resumen del pedido en el formulario -->
        <section class="mb-6 bg-gray-50 rounded-lg p-4">
          <h3 class="text-lg font-semibold mb-3">Resumen del Pedido</h3>
          <div id="checkout-resumen" class="space-y-2 text-sm">
            <!-- Se llenará dinámicamente -->
          </div>
          <div class="border-t pt-2 mt-2">
            <div class="flex justify-between font-bold">
              <span>Total a pagar:</span>
              <span id="checkout-total" style="color: var(--rosa-neon);">S/ 0.00</span>
            </div>
          </div>
        </section>

        <!-- Botones de acción -->
        <div class="flex flex-col sm:flex-row gap-3 pt-4">
          <button type="button" onclick="cancelarCheckout()" class="btn-secondary flex-1">
            <i class="fas fa-arrow-left mr-2"></i>Volver al carrito
          </button>
          <button type="submit" class="btn-primary flex-1">
            <i class="fas fa-check mr-2"></i>Confirmar Pedido
          </button>
        </div>
      </form>
    </div>
  `;

  // 6. Llenar el resumen del pedido en el formulario
  llenarResumenCheckout();
  
  // 7. Agregar event listener al formulario
  document.getElementById('form-checkout').addEventListener('submit', procesarPedido);
  
  // 8. Hacer scroll suave al formulario
  formContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

/**
 * Llena el resumen del pedido en el formulario de checkout
 */
function llenarResumenCheckout() {
  const carrito = obtenerCarrito();
  const resumenContainer = document.getElementById('checkout-resumen');
  const totalContainer = document.getElementById('checkout-total');
  
  if (!resumenContainer || !totalContainer) return;
  
  let htmlResumen = '';
  let subtotal = 0;
  
  carrito.forEach(item => {
    const itemTotal = item.precio * item.cantidad;
    subtotal += itemTotal;
    htmlResumen += `
      <div class="flex justify-between">
        <span>${item.nombre} x${item.cantidad}</span>
        <span>S/ ${itemTotal.toFixed(2)}</span>
      </div>
    `;
  });
  
  const shipping = subtotal >= 150 ? 0 : 10;
  const total = subtotal + shipping;
  
  if (shipping > 0) {
    htmlResumen += `
      <div class="flex justify-between text-gray-600">
        <span>Envío:</span>
        <span>S/ ${shipping.toFixed(2)}</span>
      </div>
    `;
  } else {
    htmlResumen += `
      <div class="flex justify-between text-green-600">
        <span>Envío:</span>
        <span>¡Gratis!</span>
      </div>
    `;
  }
  
  resumenContainer.innerHTML = htmlResumen;
  totalContainer.textContent = `S/ ${total.toFixed(2)}`;
}

/**
 * Función mejorada para procesar el pedido
 */
async function procesarPedido(event) {
  event.preventDefault();
  
  const formData = new FormData(event.target);
  const carrito = obtenerCarrito();
  
  // Validar que hay productos en el carrito
  if (carrito.length === 0) {
    mostrarNotificacion('No hay productos en el carrito.', 'error');
    return;
  }
  
  // Mostrar indicador de carga
  const submitBtn = event.target.querySelector('button[type="submit"]');
  const originalText = submitBtn.innerHTML;
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';
  
  try {
    // Preparar datos del pedido
    const subtotal = carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
    const shipping = subtotal >= 150 ? 0 : 10;
    const total = subtotal + shipping;
    
    const pedidoData = {
      cliente: {
        nombre: formData.get('nombre'),
        apellido: formData.get('apellido'),
        email: formData.get('email'),
        telefono: formData.get('telefono'),
        documento: formData.get('documento'),
        direccion: `${formData.get('direccion')} ${formData.get('referencia') || ''}, ${formData.get('ciudad')}`.trim()
      },
      items: carrito.map(item => ({
        id: item.id,
        nombre: item.nombre,
        cantidad: item.cantidad,
        precio: item.precio
      })),
      totales: {
        subtotal: subtotal,
        shipping: shipping,
        total: total
      },
      metodo_pago_id: parseInt(formData.get('metodo_pago')),
      notas: `Dirección: ${formData.get('direccion')}, Ciudad: ${formData.get('ciudad')}, CP: ${formData.get('codigo_postal')}`
    };
    
    console.log('Enviando pedido:', pedidoData);
    
    // Enviar pedido al servidor
    const response = await fetch(API_GUARDAR_PEDIDO, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(pedidoData)
    });
    
    const resultado = await response.json();
    console.log('Respuesta del servidor:', resultado);
    
    if (resultado.success) {
      // Limpiar carrito
      localStorage.removeItem(STORAGE_KEY_CARRITO);
      
      // Mostrar modal de éxito en lugar de mensaje simple
      mostrarModalExito({
        cliente: pedidoData.cliente,
        pedido_id: resultado.pedido_id
      });
    } else {
      throw new Error(resultado.message || 'Error al procesar el pedido');
    }
    
  } catch (error) {
    console.error('Error al procesar el pedido:', error);
    mostrarNotificacion('Error al procesar el pedido. Intente de nuevo.', 'error');
    
    // Restaurar botón
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalText;
  }
}

/**
 * Muestra una ventana modal con animación para confirmar el éxito del pedido
 */
function mostrarModalExito(pedidoData) {
  // Crear el HTML de la modal
  const modalHTML = `
    <div id="success-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-backdrop">
      <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4 modal-content slide-in">
        <div class="text-center">
          <!-- Icono de check con animación -->
          <div class="text-green-500 text-8xl mb-6 check-animation">
            <i class="fas fa-check-circle"></i>
          </div>
          
          <!-- Título -->
          <h2 class="text-3xl font-bold mb-4 text-gradient" style="color: var(--rosa-neon);">
            ¡Pedido Confirmado!
          </h2>
          
          <!-- Mensaje principal -->
          <p class="text-gray-700 mb-6 text-lg leading-relaxed">
            Gracias ${pedidoData.cliente.nombre} por tu compra. 
            <strong>Un asesor de ventas se comunicará con usted</strong> para coordinar la entrega.
          </p>
          
          <!-- Información del pedido -->
          <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
            <h3 class="font-semibold mb-2 text-gray-800">Resumen de tu pedido:</h3>
            <p class="text-sm text-gray-600 mb-1"><strong>Pedido ID:</strong> #${pedidoData.pedido_id}</p>
            <p class="text-sm text-gray-600 mb-1"><strong>Email:</strong> ${pedidoData.cliente.email}</p>
            <p class="text-sm text-gray-600"><strong>Teléfono:</strong> ${pedidoData.cliente.telefono}</p>
          </div>
          
          <!-- Botones de acción -->
          <div class="flex flex-col sm:flex-row gap-3">
            <button onclick="cerrarModalExito()" class="btn-primary flex-1">
              <i class="fas fa-times mr-2"></i>Cerrar
            </button>
            <a href="${INDEX_PAGE}" class="btn-secondary flex-1">
              <i class="fas fa-home mr-2"></i>Volver al inicio
            </a>
          </div>
        </div>
      </div>
    </div>
  `;
  
  // Agregar la modal al body
  document.body.insertAdjacentHTML('beforeend', modalHTML);
  
  // Agregar estilos de animación si no existen
  if (!document.getElementById('modal-styles')) {
    const styles = `
      <style id="modal-styles">
        /* Animación del backdrop */
        .modal-backdrop {
          animation: fadeIn 0.3s ease-out;
        }
        
        /* Animación del contenido */
        .modal-content {
          animation: slideIn 0.5s ease-out;
        }
        
        /* Animación del check */
        .check-animation {
          animation: checkPulse 1s ease-in-out;
        }
        
        @keyframes fadeIn {
          from { opacity: 0; }
          to { opacity: 1; }
        }
        
        @keyframes slideIn {
          from { 
            opacity: 0;
            transform: translateY(-50px) scale(0.9);
          }
          to { 
            opacity: 1;
            transform: translateY(0) scale(1);
          }
        }
        
        @keyframes checkPulse {
          0% { transform: scale(0); }
          50% { transform: scale(1.2); }
          100% { transform: scale(1); }
        }
        
        /* Efecto de brillo para el icono */
        .fa-check-circle {
          filter: drop-shadow(0 0 10px rgba(34, 197, 94, 0.5));
        }
      </style>
    `;
    document.head.insertAdjacentHTML('beforeend', styles);
  }
  
  // Hacer que la modal se cierre al hacer clic fuera
  const modal = document.getElementById('success-modal');
  modal.addEventListener('click', function(e) {
    if (e.target === modal) {
      cerrarModalExito();
    }
  });
  
  // Hacer que se cierre con la tecla Escape
  document.addEventListener('keydown', function escapeHandler(e) {
    if (e.key === 'Escape') {
      cerrarModalExito();
      document.removeEventListener('keydown', escapeHandler);
    }
  });
}

/**
 * Cierra la modal de éxito
 */
function cerrarModalExito() {
  const modal = document.getElementById('success-modal');
  if (modal) {
    // Animación de cierre
    modal.style.animation = 'fadeOut 0.3s ease-out';
    modal.querySelector('.modal-content').style.animation = 'slideOut 0.3s ease-out';
    
    setTimeout(() => {
      modal.remove();
      // Redirigir a la página de inicio después de cerrar
      window.location.href = INDEX_PAGE;
    }, 300);
  }
}

/**
 * Función para cancelar el checkout y volver al carrito
 */
function cancelarCheckout() {
  // Remover el formulario de checkout
  const formContainer = document.getElementById('checkout-form-container');
  if (formContainer) {
    formContainer.remove();
  }
  
  // Reactivar botones de pago
  reactivarBotonesPago();
  
  // Mostrar el sticky checkout móvil nuevamente
  const mobileCheckout = document.querySelector('.mobile-checkout');
  if (mobileCheckout) {
    mobileCheckout.style.display = 'block';
  }
}

/**
 * Función para reactivar los botones de pago
 */
function reactivarBotonesPago() {
  const botonesPago = document.querySelectorAll('button[onclick="procederAlPago()"], button[onclick="proceedToCheckout()"]');
  botonesPago.forEach(btn => {
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-credit-card mr-2"></i>Proceder al Pago';
    btn.classList.remove('opacity-50', 'cursor-not-allowed');
    btn.classList.add('btn-pulse');
  });
}

/**
 * Función para mostrar notificaciones (si no existe en carrito.js)
 */
function mostrarNotificacion(mensaje, tipo = 'info') {
  // Verificar si ya existe la función en carrito.js
  if (typeof window.mostrarNotificacionOriginal === 'function') {
    window.mostrarNotificacionOriginal(mensaje, tipo);
    return;
  }
  
  // Crear notificación temporal si no existe la función
  const notification = document.createElement('div');
  notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
    tipo === 'success' ? 'bg-green-500 text-white' :
    tipo === 'error' ? 'bg-red-500 text-white' :
    'bg-blue-500 text-white'
  }`;
  notification.innerHTML = `
    <div class="flex items-center">
      <i class="fas ${
        tipo === 'success' ? 'fa-check-circle' :
        tipo === 'error' ? 'fa-exclamation-circle' :
        'fa-info-circle'
      } mr-2"></i>
      ${mensaje}
    </div>
  `;
  
  document.body.appendChild(notification);
  
  setTimeout(() => {
    notification.remove();
  }, 3000);
}