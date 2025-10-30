// ===== DATA =====
    const products = [
      { id: 1, name: 'Velvet Rose', category: 'eau-parfum', brand: 'samy-signature', price: 89, size: '100ml', discount: 15, image: 'fa-bottle-droplet' },
      { id: 2, name: 'Lavender Dream', category: 'eau-toilette', brand: 'glow-collection', price: 65, size: '75ml', discount: 0, image: 'fa-bottle-droplet' },
      { id: 3, name: 'Peony Bliss', category: 'eau-parfum', brand: 'velvet-dreams', price: 75, size: '50ml', discount: 20, image: 'fa-bottle-droplet' },
      { id: 4, name: 'Glam Set', category: 'sets', brand: 'rose-elite', price: 120, size: 'Premium', discount: 0, image: 'fa-gift' },
      { id: 5, name: 'Cherry Blossom', category: 'brumas', brand: 'samy-signature', price: 45, size: '200ml', discount: 10, image: 'fa-wind' },
      { id: 6, name: 'Midnight Rose', category: 'eau-parfum', brand: 'rose-elite', price: 95, size: '100ml', discount: 0, image: 'fa-bottle-droplet' },
      { id: 7, name: 'Ocean Breeze', category: 'eau-toilette', brand: 'glow-collection', price: 58, size: '50ml', discount: 0, image: 'fa-bottle-droplet' },
      { id: 8, name: 'Romantic Set', category: 'sets', brand: 'velvet-dreams', price: 110, size: 'Deluxe', discount: 25, image: 'fa-gift' },
      { id: 9, name: 'Jasmine Mist', category: 'brumas', brand: 'samy-signature', price: 42, size: '150ml', discount: 0, image: 'fa-wind' },
      { id: 10, name: 'Golden Amber', category: 'eau-parfum', brand: 'rose-elite', price: 105, size: '100ml', discount: 15, image: 'fa-bottle-droplet' },
      { id: 11, name: 'Sweet Vanilla', category: 'eau-toilette', brand: 'glow-collection', price: 68, size: '75ml', discount: 0, image: 'fa-bottle-droplet' },
      { id: 12, name: 'Paradise Set', category: 'sets', brand: 'velvet-dreams', price: 135, size: 'Luxury', discount: 30, image: 'fa-gift' }
    ];

    let filteredProducts = [...products];
    let currentPage = 1;
    const productsPerPage = 12;

    // ===== CART MANAGEMENT =====
    function updateCartCount() {
      const cart = JSON.parse(localStorage.getItem('cart') || '[]');
      const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
      document.getElementById('cart-count').textContent = totalItems;
    }

    function addToCart(productId) {
      const product = products.find(p => p.id === productId);
      if (!product) return;

      let cart = JSON.parse(localStorage.getItem('cart') || '[]');
      const existingItem = cart.find(item => item.id === productId);

      if (existingItem) {
        existingItem.quantity += 1;
      } else {
        cart.push({
          id: product.id,
          name: product.name,
          price: product.price,
          size: product.size,
          image: product.image,
          quantity: 1
        });
      }

      localStorage.setItem('cart', JSON.stringify(cart));
      updateCartCount();
      
      // Animation feedback
      const btn = event.target;
      const originalText = btn.innerHTML;
      btn.innerHTML = '<i class="fas fa-check mr-2"></i>Agregado';
      btn.style.background = '#10B981';
      
      setTimeout(() => {
        btn.innerHTML = originalText;
        btn.style.background = '';
      }, 1500);
    }

    // ===== RENDER PRODUCTS =====
    function renderProducts() {
      const grid = document.getElementById('products-grid');
      grid.innerHTML = '';

      filteredProducts.forEach((product, index) => {
        const finalPrice = product.discount > 0 
          ? (product.price * (1 - product.discount / 100)).toFixed(2)
          : product.price.toFixed(2);

        const card = document.createElement('div');
        card.className = 'card-product bg-white rounded-2xl shadow-lg overflow-hidden fade-in-up';
        card.style.animationDelay = `${index * 0.1}s`;
        
        card.innerHTML = `
          ${product.discount > 0 ? `<span class="badge-discount">-${product.discount}% OFF</span>` : ''}
          <i class="favorite-icon fas fa-heart"></i>
          <div class="h-48 md:h-64 bg-pastel flex items-center justify-center">
            <i class="fas ${product.image} text-5xl md:text-6xl" style="color: var(--rosa-neon);"></i>
          </div>
          <div class="p-4 md:p-6 text-center">
            <h3 class="font-semibold text-base md:text-lg mb-2">${product.name}</h3>
            <p class="text-gray-500 text-xs md:text-sm mb-3">${getCategoryName(product.category)} ${product.size}</p>
            <div class="mb-3">
              ${product.discount > 0 
                ? `<p class="text-xl md:text-2xl font-bold" style="color: var(--rosa-neon);">${finalPrice}</p>
                   <p class="text-sm text-gray-400 line-through">${product.price.toFixed(2)}</p>`
                : `<p class="text-xl md:text-2xl font-bold" style="color: var(--rosa-neon);">${finalPrice}</p>`
              }
            </div>
            <button onclick="addToCart(${product.id})" class="btn-primary w-full text-sm md:text-base">
              <i class="fas fa-shopping-cart mr-2"></i>Agregar al Carrito
            </button>
          </div>
        `;

        // Favorite toggle
        const heartIcon = card.querySelector('.favorite-icon');
        heartIcon.addEventListener('click', (e) => {
          e.stopPropagation();
          heartIcon.classList.toggle('fas');
          heartIcon.classList.toggle('far');
          if (heartIcon.classList.contains('fas')) {
            heartIcon.style.animation = 'pulse 0.5s';
          }
        });

        grid.appendChild(card);
      });

      updateResultsCount();
    }

    function getCategoryName(category) {
      const names = {
        'eau-parfum': 'Eau de Parfum',
        'eau-toilette': 'Eau de Toilette',
        'brumas': 'Bruma Corporal',
        'sets': 'Set Regalo'
      };
      return names[category] || category;
    }

    function updateResultsCount() {
      document.getElementById('results-count').textContent = filteredProducts.length;
      document.getElementById('total-count').textContent = products.length;
    }

    // ===== FILTERS =====
    function applyFilters() {
      const selectedCategories = Array.from(document.querySelectorAll('.category-filter:checked, .category-filter-mobile:checked'))
        .map(cb => cb.value);
      
      const selectedBrands = Array.from(document.querySelectorAll('.brand-filter:checked, .brand-filter-mobile:checked'))
        .map(cb => cb.value);
      
      const maxPrice = parseInt(document.getElementById('price-range').value);

      filteredProducts = products.filter(product => {
        return selectedCategories.includes(product.category) &&
               selectedBrands.includes(product.brand) &&
               product.price <= maxPrice;
      });

      applySorting();
      renderProducts();
    }

    function clearFilters() {
      document.querySelectorAll('.category-filter, .category-filter-mobile').forEach(cb => cb.checked = true);
      document.querySelectorAll('.brand-filter, .brand-filter-mobile').forEach(cb => cb.checked = true);
      document.getElementById('price-range').value = 200;
      document.getElementById('price-range-mobile').value = 200;
      document.getElementById('price-value').textContent = '$200';
      document.getElementById('price-value-mobile').textContent = '$200';
      filteredProducts = [...products];
      applySorting();
      renderProducts();
    }

    // ===== SORTING =====
    function applySorting() {
      const sortValue = document.getElementById('sort-select').value;

      switch(sortValue) {
        case 'price-asc':
          filteredProducts.sort((a, b) => a.price - b.price);
          break;
        case 'price-desc':
          filteredProducts.sort((a, b) => b.price - a.price);
          break;
        case 'newest':
          filteredProducts.sort((a, b) => b.id - a.id);
          break;
        case 'popular':
        default:
          filteredProducts.sort((a, b) => a.id - b.id);
          break;
      }
    }

    // ===== EVENT LISTENERS =====
    
    // Mobile menu
    document.getElementById('menu-btn').addEventListener('click', () => {
      document.getElementById('mobile-menu').classList.toggle('active');
    });

    // Mobile filters
    document.getElementById('mobile-filter-btn').addEventListener('click', () => {
      document.getElementById('mobile-filters').classList.add('active');
      document.getElementById('mobile-overlay').classList.add('active');
    });

    document.getElementById('close-mobile-filters').addEventListener('click', () => {
      document.getElementById('mobile-filters').classList.remove('active');
      document.getElementById('mobile-overlay').classList.remove('active');
    });

    document.getElementById('mobile-overlay').addEventListener('click', () => {
      document.getElementById('mobile-filters').classList.remove('active');
      document.getElementById('mobile-overlay').classList.remove('active');
    });

    // Price range
    document.getElementById('price-range').addEventListener('input', (e) => {
      document.getElementById('price-value').textContent = `${e.target.value}`;
    });

    document.getElementById('price-range-mobile').addEventListener('input', (e) => {
      document.getElementById('price-value-mobile').textContent = `${e.target.value}`;
      document.getElementById('price-range').value = e.target.value;
    });

    // Apply filters
    document.getElementById('apply-filters').addEventListener('click', applyFilters);
    document.getElementById('apply-filters-mobile').addEventListener('click', () => {
      applyFilters();
      document.getElementById('mobile-filters').classList.remove('active');
      document.getElementById('mobile-overlay').classList.remove('active');
    });

    // Clear filters
    document.getElementById('clear-filters').addEventListener('click', clearFilters);
    document.getElementById('clear-filters-mobile').addEventListener('click', clearFilters);

    // Sorting
    document.getElementById('sort-select').addEventListener('change', () => {
      applySorting();
      renderProducts();
    });

    // Sync mobile and desktop filters
    document.querySelectorAll('.category-filter').forEach(cb => {
      cb.addEventListener('change', (e) => {
        const mobileCb = document.querySelector(`.category-filter-mobile[value="${e.target.value}"]`);
        if (mobileCb) mobileCb.checked = e.target.checked;
      });
    });

    document.querySelectorAll('.category-filter-mobile').forEach(cb => {
      cb.addEventListener('change', (e) => {
        const desktopCb = document.querySelector(`.category-filter[value="${e.target.value}"]`);
        if (desktopCb) desktopCb.checked = e.target.checked;
      });
    });

    document.querySelectorAll('.brand-filter').forEach(cb => {
      cb.addEventListener('change', (e) => {
        const mobileCb = document.querySelector(`.brand-filter-mobile[value="${e.target.value}"]`);
        if (mobileCb) mobileCb.checked = e.target.checked;
      });
    });

    document.querySelectorAll('.brand-filter-mobile').forEach(cb => {
      cb.addEventListener('change', (e) => {
        const desktopCb = document.querySelector(`.brand-filter[value="${e.target.value}"]`);
        if (desktopCb) desktopCb.checked = e.target.checked;
      });
    });

    // Pagination (basic implementation)
    document.querySelectorAll('.pagination button[data-page]').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.pagination button[data-page]').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    });

    document.getElementById('prev-page').addEventListener('click', () => {
      const activeBtn = document.querySelector('.pagination button.active');
      const prevBtn = activeBtn.previousElementSibling;
      if (prevBtn && prevBtn.dataset.page) {
        prevBtn.click();
      }
    });

    document.getElementById('next-page').addEventListener('click', () => {
      const activeBtn = document.querySelector('.pagination button.active');
      const nextBtn = activeBtn.nextElementSibling;
      if (nextBtn && nextBtn.dataset.page) {
        nextBtn.click();
      }
    });

    // ===== INITIALIZATION =====
    document.addEventListener('DOMContentLoaded', () => {
      updateCartCount();
      renderProducts();
    });