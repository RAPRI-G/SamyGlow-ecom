// assets/js/nuevo-pedido.js
// DEPENDE DE: cart-manager.js y carrito.js (para mostrarNotificacion)

/**
 * Función principal que se llama desde carrito.js
 * Se encarga de construir y mostrar el formulario de checkout.
 */
async function iniciarCheckout() {
  console.log('Iniciando checkout...');
  const cartContent = document.getElementById('cart-content');
  if (!cartContent) return;

  // 1. Deshabilitar botones de "Proceder al Pago"
  document.querySelectorAll('button[onclick="procederAlPago()"], button[onclick="proceedToCheckout()"]')
    .forEach(btn => {
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Cargando...';
    });
  
  // 2. Ocultar el resumen de pedido original (el de la derecha)
  const resumenOriginal = document.querySelector('.lg\\:col-span-1 .sticky');
  if (resumenOriginal) {
    resumenOriginal.style.display = 'none';
  }
  
  // 3. Ocultar el sticky checkout móvil
  const mobileCheckout = document.querySelector('.mobile-checkout');
  if (mobileCheckout) {
    mobileCheckout.style.display = 'none';
  }

  // 4. Buscar métodos de pago
  let metodosDePago = [];
  try {
    const response = await fetch('./api/metodos-pago.php');
    const data = await response.json();
    if (data.success && data.data.length > 0) {
      metodosDePago = data.data;
    } else {
      throw new Error(data.message || 'No se cargaron los métodos de pago.');
    }
  } catch (error) {
    console.error('Error fetching payment methods:', error);
    mostrarNotificacion('Error al cargar métodos de pago. Intente de nuevo.', 'error');
    // Reactivar botones si falla
    reactivarBotonesPago();
    return;
  }

  // 5. Crear el contenedor del formulario si no existe
  let formContainer = document.getElementById('checkout-form-container');
  if (!formContainer) {
    formContainer = document.createElement('div');
    formContainer.id = 'checkout-form-container';
    formContainer.className = 'mt-8'; // Espacio superior
    
    // Insertarlo después del listado de productos
    const productosContainer = document.querySelector('.lg\\:col-span-2');
    if (productosContainer) {
      productosContainer.appendChild(formContainer);
    } else {
      cartContent.appendChild(formContainer);
    }
  }
  
  // 6. Renderizar el HTML del formulario
  formContainer.innerHTML = `
    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
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
          <div>
            <label for="checkout-direccion" class="block text-sm font-medium text-gray-700 mb-1">Dirección Completa *</label>
            <input type="text" id="checkout-direccion" name="direccion" class="form-input" placeholder="Ej: Av. Siempre Viva 123, Sprinfield" required>
          </div>
          <!-- Aquí podrías agregar campos de Ciudad, Región, etc. si los tienes en tu BD -->
        </section>

        <!-- Sección Método de Pago -->
        <section class="mb-6">
          <h3 class="text-lg font-semibold border-b pb-2 mb-4">3. Método de Pago</h3>
          <div id="payment-methods-list" class="space-y-3">
            ${metodosDePago.map((metodo, index) => `
              <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer hover:border-[#FF1493] transition">
                <input type="radio" name="metodo_pago_id" value="${metodo.id}" class="h-5 w-5 text-[#FF1493] focus:ring-[#FF1493]" ${index === 0 ? 'checked' : ''} required>
                <span class="ml-3 flex-1">
                  <span class="font-semibold text-gray-800">${escapeHtml(metodo.nombre)}</span>
                  ${metodo.descripcion ? `<p class="text-sm text-gray-500">${escapeHtml(metodo.descripcion)}</p>` : ''}
                </span>
                <i class="${escapeHtml(metodo.icono || 'fas fa-credit-card')} text-2xl" style="color: var(--rosa-neon);"></i>
              </label>
            `).join('')}
          </div>
        </section>
        
        <!-- Botón de Finalizar Pedido -->
        <div class="mt-8 text-right">
          <button type="submit" id="btn-submit-order" class="btn-primary btn-pulse w-full md:w-auto text-lg px-8 py-3">
            <i class="fas fa-check-circle mr-2"></i>
            Finalizar Pedido
          </button>
        </div>
      </form>
    </div>
  `;
  
  // 7. Añadir el listener al formulario
  const form = document.getElementById('form-checkout');
  if (form) {
    form.addEventListener('submit', handleFormSubmit);
  }

  // 8. Hacer scroll hacia el formulario
  formContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

/**
 * Maneja el envío del formulario de checkout
 */
async function handleFormSubmit(event) {
  event.preventDefault();
  
  const submitButton = document.getElementById('btn-submit-order');
  submitButton.disabled = true;
  submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando pedido...';

  // Obtener datos del formulario
  const formData = new FormData(event.target);
  const clienteData = {
    nombre: formData.get('nombre'),
    apellido: formData.get('apellido'),
    email: formData.get('email'),
    telefono: formData.get('telefono'),
    documento: formData.get('documento'),
    direccion: formData.get('direccion'),
  };
  
  // Obtener datos del carrito (desde cart-manager.js)
  const items = obtenerCarrito();
  const totales = calcularTotalCarrito();
  const metodo_pago_id = formData.get('metodo_pago_id');

  // Construir el payload para la API
  const payload = {
    cliente: clienteData,
    items: items, // Enviamos el carrito completo
    totales: totales, // Enviamos los totales calculados
    metodo_pago_id: parseInt(metodo_pago_id, 10),
  };

  try {
    // Enviar a la API para guardar el pedido
    const response = await fetch('./api/guardar-pedido.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(payload)
    });
    
    const result = await response.json();

    if (response.ok && result.success) {
      // ÉXITO: Pedido guardado
      mostrarConfirmacionPedido(result.pedido_id);
      
      // Limpiar carrito (de cart-manager.js)
      limpiarCarrito();
      
      // Actualizar el contador del header (de cart-manager.js)
      actualizarContadorCarrito();

    } else {
      // ERROR: No se guardó el pedido
      throw new Error(result.message || 'No se pudo completar el pedido.');
    }

  } catch (error) {
    console.error('Error al enviar el pedido:', error);
    mostrarNotificacion(error.message, 'error');
    submitButton.disabled = false;
    submitButton.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Finalizar Pedido';
  }
}

/**
 * Muestra el mensaje de éxito y oculta el formulario
 */
function mostrarConfirmacionPedido(pedidoId) {
  const cartContent = document.getElementById('cart-content');
  if (!cartContent) return;

  // Ocultar productos recomendados
  const recommendedSection = document.querySelector('.bg-pastel-gradient');
  if (recommendedSection) {
    recommendedSection.style.display = 'none';
  }

  // Ocultar sticky checkout móvil (por si acaso)
  const mobileCheckout = document.querySelector('.mobile-checkout');
  if (mobileCheckout) {
    mobileCheckout.style.display = 'none';
  }

  // Reemplazar todo el contenido del carrito con el mensaje de éxito
  cartContent.innerHTML = `
    <div class="text-center py-16 max-w-2xl mx-auto">
      <i class="fas fa-check-circle text-6xl mb-4" style="color: var(--rosa-neon);"></i>
      <h2 class="text-3xl font-bold text-gray-800 mb-3">¡Pedido Registrado con Éxito!</h2>
      <p class="text-lg text-gray-600 mb-6">
        Gracias por tu compra. Tu pedido con el ID <strong>#${pedidoId}</strong> ha sido recibido
        y está siendo procesado. Te enviaremos un email con los detalles.
      </p>
      <a href="tienda.html" class="btn-primary inline-block">
        <i class="fas fa-store mr-2"></i>Seguir Comprando
      </a>
    </div>
  `;
  
  // Hacer scroll al inicio del mensaje
  cartContent.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

/**
 * Reactiva los botones de pago si la carga inicial falla
 */
function reactivarBotonesPago() {
  document.querySelectorAll('button[disabled][onclick="procederAlPago()"], button[disabled][onclick="proceedToCheckout()"]')
    .forEach(btn => {
      btn.disabled = false;
      // Revertir el texto
      if (btn.classList.contains('mobile-checkout')) {
         btn.innerHTML = '<i class="fas fa-credit-card mr-2"></i>Proceder al Pago';
      } else {
         btn.innerHTML = '<i class="fas fa-credit-card mr-2"></i>Proceder al Pago';
      }
    });
}

// Función auxiliar para escapar HTML (prevenir XSS)
function escapeHtml(str) {
  if (!str) return '';
  return str.replace(/[&<>"']/g, function(m) {
    return {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    }[m];
  });
}