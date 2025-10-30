// ===== CART COUNT =====
    function updateCartCount() {
      const cart = JSON.parse(localStorage.getItem('cart') || '[]');
      const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
      document.getElementById('cart-count').textContent = totalItems;
    }

    // ===== MOBILE MENU =====
    document.getElementById('menu-btn').addEventListener('click', () => {
      document.getElementById('mobile-menu').classList.toggle('active');
    });

    // ===== FORM VALIDATION =====
    const form = document.getElementById('contact-form');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const subjectInput = document.getElementById('subject');
    const messageInput = document.getElementById('message');

    // Validation functions
    function validateName() {
      const name = nameInput.value.trim();
      const error = document.getElementById('name-error');
      
      if (name.length < 3) {
        nameInput.classList.add('error');
        error.classList.add('active');
        return false;
      } else {
        nameInput.classList.remove('error');
        error.classList.remove('active');
        return true;
      }
    }

    function validateEmail() {
      const email = emailInput.value.trim();
      const error = document.getElementById('email-error');
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      
      if (!emailRegex.test(email)) {
        emailInput.classList.add('error');
        error.classList.add('active');
        return false;
      } else {
        emailInput.classList.remove('error');
        error.classList.remove('active');
        return true;
      }
    }

    function validateSubject() {
      const subject = subjectInput.value;
      const error = document.getElementById('subject-error');
      
      if (subject === '') {
        subjectInput.classList.add('error');
        error.classList.add('active');
        return false;
      } else {
        subjectInput.classList.remove('error');
        error.classList.remove('active');
        return true;
      }
    }

    function validateMessage() {
      const message = messageInput.value.trim();
      const error = document.getElementById('message-error');
      
      if (message.length < 20) {
        messageInput.classList.add('error');
        error.classList.add('active');
        return false;
      } else {
        messageInput.classList.remove('error');
        error.classList.remove('active');
        return true;
      }
    }

    // Real-time validation
    nameInput.addEventListener('blur', validateName);
    nameInput.addEventListener('input', () => {
      if (nameInput.value.trim().length >= 3) {
        validateName();
      }
    });

    emailInput.addEventListener('blur', validateEmail);
    emailInput.addEventListener('input', () => {
      if (emailInput.value.trim().length > 0) {
        validateEmail();
      }
    });

    subjectInput.addEventListener('change', validateSubject);

    messageInput.addEventListener('blur', validateMessage);
    messageInput.addEventListener('input', () => {
      if (messageInput.value.trim().length >= 20) {
        validateMessage();
      }
    });

    // Form submission
    form.addEventListener('submit', (e) => {
      e.preventDefault();

      // Validate all fields
      const isNameValid = validateName();
      const isEmailValid = validateEmail();
      const isSubjectValid = validateSubject();
      const isMessageValid = validateMessage();

      if (isNameValid && isEmailValid && isSubjectValid && isMessageValid) {
        // Show success message
        const successMessage = document.getElementById('success-message');
        successMessage.classList.add('active');

        // Scroll to success message
        successMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Reset form
        form.reset();

        // Hide success message after 5 seconds
        setTimeout(() => {
          successMessage.classList.remove('active');
        }, 5000);

        // In a real application, you would send the data to a server here
        console.log('Form data:', {
          name: nameInput.value,
          email: emailInput.value,
          phone: phoneInput.value,
          subject: subjectInput.value,
          message: messageInput.value
        });
      } else {
        // Scroll to first error
        const firstError = document.querySelector('.form-input.error, .form-textarea.error, .form-select.error');
        if (firstError) {
          firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }
    });

    // ===== FAQ ACCORDION =====
    const faqQuestions = document.querySelectorAll('.faq-question');

    faqQuestions.forEach(question => {
      question.addEventListener('click', () => {
        const isActive = question.classList.contains('active');
        
        // Close all FAQ items
        faqQuestions.forEach(q => {
          q.classList.remove('active');
          q.nextElementSibling.classList.remove('active');
        });

        // Open clicked item if it wasn't active
        if (!isActive) {
          question.classList.add('active');
          question.nextElementSibling.classList.add('active');
        }
      });
    });

    // ===== SOCIAL ICONS ANIMATION =====
    const socialIcons = document.querySelectorAll('.social-icon');

    socialIcons.forEach(icon => {
      icon.addEventListener('mouseenter', () => {
        icon.style.animation = 'bounce 0.5s';
      });

      icon.addEventListener('animationend', () => {
        icon.style.animation = '';
      });
    });

    // Add bounce animation
    const style = document.createElement('style');
    style.textContent = `
      @keyframes bounce {
        0%, 100% { transform: scale(1) rotate(0deg); }
        25% { transform: scale(1.2) rotate(-5deg); }
        75% { transform: scale(1.2) rotate(5deg); }
      }
    `;
    document.head.appendChild(style);

    // ===== SCROLL ANIMATIONS =====
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
        }
      });
    }, observerOptions);

    document.querySelectorAll('.fade-in-up').forEach(el => {
      el.style.opacity = '0';
      el.style.transform = 'translateY(30px)';
      observer.observe(el);
    });

    // ===== FORM INPUT GLOW EFFECT =====
    const formInputs = document.querySelectorAll('.form-input, .form-textarea, .form-select');

    formInputs.forEach(input => {
      input.addEventListener('focus', () => {
        input.style.transition = 'all 0.3s ease';
      });
    });

    // ===== INITIALIZE =====
    document.addEventListener('DOMContentLoaded', () => {
      updateCartCount();

      // Add typing effect to hero subtitle (optional)
      const heroSubtitle = document.querySelector('.hero-contact p');
      if (heroSubtitle) {
        const text = heroSubtitle.textContent;
        heroSubtitle.textContent = '';
        let i = 0;

        function typeWriter() {
          if (i < text.length) {
            heroSubtitle.textContent += text.charAt(i);
            i++;
            setTimeout(typeWriter, 50);
          }
        }

        setTimeout(typeWriter, 500);
      }
    });

    // ===== PREVENT FORM RESUBMISSION ON PAGE REFRESH =====
    if (window.history.replaceState) {
      window.history.replaceState(null, null, window.location.href);
    }

    // ===== CONTACT CARD HOVER EFFECT =====
    const contactCards = document.querySelectorAll('.contact-card');

    contactCards.forEach(card => {
      card.addEventListener('mouseenter', () => {
        card.style.transform = 'translateY(-5px) scale(1.02)';
      });

      card.addEventListener('mouseleave', () => {
        card.style.transform = 'translateY(0) scale(1)';
      });
    });

    // ===== FORM CHARACTER COUNTER FOR MESSAGE =====
    messageInput.addEventListener('input', () => {
      const charCount = messageInput.value.length;
      const minChars = 20;
      
      if (charCount > 0 && charCount < minChars) {
        const remaining = minChars - charCount;
        const error = document.getElementById('message-error');
        error.textContent = `El mensaje debe tener al menos 20 caracteres (${remaining} restantes)`;
        error.classList.add('active');
        messageInput.classList.add('error');
      } else if (charCount >= minChars) {
        messageInput.classList.remove('error');
        document.getElementById('message-error').classList.remove('active');
      }
    });

    // ===== PHONE INPUT FORMATTING =====
    phoneInput.addEventListener('input', (e) => {
      let value = e.target.value.replace(/\D/g, '');
      
      if (value.length > 0) {
        if (value.length <= 3) {
          value = '+51 ' + value;
        } else if (value.length <= 6) {
          value = '+51 ' + value.slice(2, 5) + ' ' + value.slice(5);
        } else {
          value = '+51 ' + value.slice(2, 5) + ' ' + value.slice(5, 8) + ' ' + value.slice(8, 11);
        }
      }
      
      e.target.value = value;
    });

    // ===== SMOOTH SCROLL FOR INTERNAL LINKS =====
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      });
    });

    // ===== ACCESSIBILITY: KEYBOARD NAVIGATION FOR FAQ =====
    faqQuestions.forEach((question, index) => {
      question.setAttribute('tabindex', '0');
      question.setAttribute('role', 'button');
      question.setAttribute('aria-expanded', 'false');

      question.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          question.click();
          question.setAttribute('aria-expanded', 
            question.classList.contains('active') ? 'true' : 'false'
          );
        }
      });
    });

    // ===== EMAIL SUGGESTIONS =====
    const commonDomains = ['gmail.com', 'hotmail.com', 'yahoo.com', 'outlook.com'];
    
    emailInput.addEventListener('blur', () => {
      const email = emailInput.value.trim();
      if (email.includes('@')) {
        const [username, domain] = email.split('@');
        
        // Check for common typos
        const suggestions = {
          'gmial.com': 'gmail.com',
          'gmai.com': 'gmail.com',
          'hotmial.com': 'hotmail.com',
          'yahooo.com': 'yahoo.com'
        };

        if (suggestions[domain]) {
          const suggested = `${username}@${suggestions[domain]}`;
          if (confirm(`¿Quisiste decir ${suggested}?`)) {
            emailInput.value = suggested;
            validateEmail();
          }
        }
      }
    });