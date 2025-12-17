
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login - SamyGlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* incluye tu CSS principal (asegúrate de que la ruta sea correcta) */
        <?php include __DIR__ . "/../../public/assets/css/login.css"; ?>
    </style>
</head>
<body>

    <div class="floating-elements" aria-hidden="true">
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
    </div>

    <div class="particles" id="particles" aria-hidden="true"></div>

    <div class="login-container" role="main">
        <div class="login-card" role="form">
            <div class="brand" aria-label="Marca SamyGlow">
                <div class="brand-logo" aria-hidden="true">
                    <i class="fas fa-crown"></i>
                </div>
                <h1>SamyGlow</h1>
                <p>"Donde la belleza se encuentra con la elegancia"</p>
            </div>

            <form id="loginForm" action="index.php?view=loginAuth" method="POST" autocomplete="off" novalidate>
                <div class="input-group">
                    <input type="text" name="username" id="username" placeholder="Usuario" required aria-label="Usuario">
                    <i class="fas fa-user input-icon" aria-hidden="true"></i>
                </div>

                <div class="input-group" style="position:relative;">
                    <input type="password" name="password" id="password" placeholder="Contraseña" required aria-label="Contraseña">
                    <i class="fas fa-lock input-icon" aria-hidden="true"></i>
                    <!-- botón accesible para alternar la visibilidad -->
                    <button type="button" id="togglePassword" class="password-toggle" aria-label="Mostrar contraseña">
                        <i class="fas fa-eye-slash" id="toggleIcon" aria-hidden="true"></i>
                    </button>
                </div>

                <!-- Mostrar error del backend -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div id="errorMessage" class="error-message" style="display:block;">
                        <?= htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <button type="submit" class="login-btn" id="loginBtn" aria-live="polite">
                    <i class="fas fa-sign-in-alt" aria-hidden="true"></i> <span id="loginBtnText">Iniciar Sesión</span>
                </button>

                <div class="admin-note" aria-hidden="true">
                    <p><i class="fas fa-shield-alt"></i> Acceso exclusivo para la administradora</p>
                </div>
            </form>

        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Partículas de fondo
    function createParticles() {
        const particlesContainer = document.getElementById('particles');
        if (!particlesContainer) return;
        const particleCount = 15;
        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.classList.add('particle');
            const size = Math.random() * 6 + 2;
            particle.style.width = `${size}px`;
            particle.style.height = `${size}px`;
            particle.style.left = `${Math.random() * 100}%`;
            particle.style.animationDelay = `${Math.random() * 8}s`;
            particlesContainer.appendChild(particle);
        }
    }

    createParticles();

    // Elementos del DOM
    const form = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');
    const loginBtnText = document.getElementById('loginBtnText');
    const errorMessage = document.getElementById('errorMessage');
    const togglePasswordBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');

    // Manejo de envío: desactivar botón y mostrar "Iniciando sesión..."
if (form && loginBtn && loginBtnText) {
    form.addEventListener('submit', function (e) {
        e.preventDefault(); // ✅ Detenemos el envío inmediato

        // Desactivar botón y mostrar animación
        loginBtn.disabled = true;
        loginBtn.setAttribute('aria-disabled', 'true');
        loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Iniciando sesión...';

        // ✅ Esperar 2 segundos antes de enviar
        setTimeout(() => {
            form.submit(); // 🔥 Envía el formulario después de la animación
        }, 2000);
    });
}




    // Ocultar mensaje de error automáticamente después de 3s
    if (errorMessage) {
        // Aseguramos que empiece opaco y luego animamos
        errorMessage.style.opacity = '1';
        setTimeout(() => {
            errorMessage.style.transition = 'opacity 0.5s ease';
            errorMessage.style.opacity = '0';
            setTimeout(() => {
                // Removemos del flujo para que no ocupe espacio
                if (errorMessage && errorMessage.parentNode) {
                    errorMessage.parentNode.removeChild(errorMessage);
                }
            }, 500);
        }, 3000);
    }

    // Toggle ver/ocultar contraseña - manejado con un solo listener y controles null-safe
    if (togglePasswordBtn && passwordInput && toggleIcon) {
        togglePasswordBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            // Actualizamos el icono de forma clara
            if (isPassword) {
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
                togglePasswordBtn.setAttribute('aria-label', 'Ocultar contraseña');
            } else {
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
                togglePasswordBtn.setAttribute('aria-label', 'Mostrar contraseña');
            }
            // Mantenemos el foco en el input
            passwordInput.focus();
        });
    } else {
        // Si falta algo, lo ignoramos silenciosamente (evita errores JS)
        // console.warn('Toggle password components missing.');
    }

    // Por accesibilidad: si el usuario presiona Escape mientras está en el input de contraseña, ocultarlo
    if (passwordInput) {
        passwordInput.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && passwordInput.type === 'text') {
                passwordInput.type = 'password';
                if (toggleIcon) {
                    toggleIcon.classList.remove('fa-eye');
                    toggleIcon.classList.add('fa-eye-slash');
                }
            }
        });
    }
});
</script>

</body>
</html>
