// Array para almacenar los productos del carrito
let cart = [];

// Función para agregar producto al carrito
function addToCart(id, name, price, image) {
  const existingProduct = cart.find(item => item.id === id);

  if (existingProduct) {
    existingProduct.quantity++;
  } else {
    cart.push({ id, name, price, image, quantity: 1 });
  }

  updateCartUI();
}

// Función para eliminar un producto del carrito
function removeFromCart(id) {
  cart = cart.filter(item => item.id !== id);
  updateCartUI();
}

// Función para calcular subtotal
function calculateSubtotal() {
  return cart.reduce((acc, item) => acc + item.price * item.quantity, 0);
}

// Función para calcular envío
function calculateShipping(subtotal) {
  return subtotal >= 150 ? 0 : 10; // ← puedes cambiar el 150 y 10 si quieres
}

// Función para calcular total final
function calculateTotal() {
  const subtotal = calculateSubtotal();
  const shipping = calculateShipping(subtotal);
  return subtotal + shipping;
}

// Función para actualizar la vista del carrito
function updateCartUI() {
  const cartItemsContainer = document.getElementById('cart-items');
  const subtotalSpan = document.getElementById('subtotal');
  const shippingSpan = document.getElementById('shipping');
  const totalSpan = document.getElementById('total');

  cartItemsContainer.innerHTML = '';

  if (cart.length === 0) {
    cartItemsContainer.innerHTML = '<p>Tu carrito está vacío.</p>';
    subtotalSpan.textContent = '0.00';
    shippingSpan.textContent = '0.00';
    totalSpan.textContent = '0.00';
    return;
  }

  cart.forEach(item => {
    const div = document.createElement('div');
    div.classList.add('cart-item');

    div.innerHTML = `
      <img src="${item.image}" alt="${item.name}">
      <div class="cart-info">
        <h4>${item.name}</h4>
        <p>S/ ${item.price.toFixed(2)}</p>
        <p>Cantidad: ${item.quantity}</p>
        <button onclick="removeFromCart(${item.id})">Eliminar</button>
      </div>
    `;

    cartItemsContainer.appendChild(div);
  });

  const subtotal = calculateSubtotal();
  const shipping = calculateShipping(subtotal);

  subtotalSpan.textContent = subtotal.toFixed(2);
  shippingSpan.textContent = shipping === 0 ? 'Gratis' : `+ ${shipping.toFixed(2)}`;
  totalSpan.textContent = calculateTotal().toFixed(2);
}

// Función checkout
function checkout() {
  if (cart.length === 0) {
    alert('El carrito está vacío.');
    return;
  }

  alert(`Redirigiendo al proceso de pago...\n\nTotal a pagar: ${calculateTotal().toFixed(2)}`);
}