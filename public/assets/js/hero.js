  // ===== HERO.JS - Typing Effect =====
    const subtitle = document.getElementById('hero-subtitle');
    const text = 'Perfumes premium para cada estilo de vida';
    let index = 0;

    function typeWriter() {
      if (!subtitle) return;
      if (index < text.length) {
        subtitle.textContent += text.charAt(index);
        index++;
        setTimeout(typeWriter, 80);
      } else {
        if (!subtitle) return;
        const cursor = document.createElement('span');
        cursor.className = 'typing-cursor';
        subtitle.appendChild(cursor);
      }
    }

    if (subtitle) {
      setTimeout(typeWriter, 1000);
    }

    // ===== MENU MOBILE =====
    const menuBtn = document.getElementById('menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (menuBtn && mobileMenu) {
      menuBtn.addEventListener('click', () => {
        if (mobileMenu.classList.contains('hidden')) {
          mobileMenu.classList.toggle('hidden');
          return;
        }
        mobileMenu.classList.toggle('active');
      });
    }

    // ===== CATEGORIES.JS - Fade in on scroll =====
    const observerOptions = {
      threshold: 0.2,
      rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry, index) => {
        if (entry.isIntersecting) {
          setTimeout(() => {
            entry.target.classList.add('visible');
          }, index * 150);
        }
      });
    }, observerOptions);

    document.querySelectorAll('.fade-in-up').forEach(el => {
      observer.observe(el);
    });

    // ===== PRODUCTS.JS - Zoom effect =====
    const products = document.querySelectorAll('.card-product');
    products.forEach((product, index) => {
      product.style.animationDelay = `${index * 0.1}s`;
    });

    // ===== OFFERS.JS - Pulse animation =====
    const offers = document.querySelectorAll('.pulse-glam');
    offers.forEach(offer => {
      offer.addEventListener('mouseenter', () => {
        offer.style.animationPlayState = 'paused';
      });
      offer.addEventListener('mouseleave', () => {
        offer.style.animationPlayState = 'running';
      });
    });

    // ===== BENEFITS.JS - Lateral reveal =====
    const benefitsObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry, index) => {
        if (entry.isIntersecting) {
          setTimeout(() => {
            entry.target.style.transform = 'translateX(0)';
            entry.target.style.opacity = '1';
          }, index * 200);
        }
      });
    }, observerOptions);

    document.querySelectorAll('#benefits-grid .fade-in-up').forEach((el, index) => {
      el.style.transform = index % 2 === 0 ? 'translateX(-50px)' : 'translateX(50px)';
      el.style.opacity = '0';
      el.style.transition = 'all 0.6s ease';
      benefitsObserver.observe(el);
    });

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    });

    // Fix para prevenir scroll horizontal en dispositivos móviles
    window.addEventListener('resize', () => {
      document.body.style.overflowX = 'hidden';
    });