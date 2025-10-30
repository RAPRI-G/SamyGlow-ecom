<script>
    document.addEventListener("DOMContentLoaded", () => {
        // 🧠 Usuario Dropdown
        const userBtn = document.getElementById("userMenuButton");
        const userMenu = document.getElementById("userDropdown");

        // 🔔 Notificaciones Dropdown
        const notifBtn = document.getElementById("notifButton");
        const notifMenu = document.getElementById("notifDropdown");
        const notifList = document.getElementById("notifList");
        const clearNotif = document.getElementById("clearNotif");

        // Función general para ocultar menús
        const closeAllMenus = () => {
            if (userMenu) userMenu.classList.add("hidden");
            if (notifMenu) notifMenu.classList.add("hidden");
        };

        // --- MENÚ DE USUARIO ---
        if (userBtn && userMenu) {
            userBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                notifMenu?.classList.add("hidden");
                userMenu.classList.toggle("hidden");
            });
        }

        // --- MENÚ DE NOTIFICACIONES ---
        if (notifBtn && notifMenu) {
            notifBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                userMenu?.classList.add("hidden");
                notifMenu.classList.toggle("hidden");
            });
        }

        // --- LIMPIAR NOTIFICACIONES ---
        if (clearNotif && notifList) {
            clearNotif.addEventListener("click", () => {
                notifList.innerHTML = '<li class="p-4 text-center text-gray-500 text-sm">Sin notificaciones 📭</li>';
                const dot = notifBtn.querySelector(".notification-dot");
                if (dot) dot.remove();
            });
        }

        // --- CLIC FUERA DE MENÚS ---
        document.addEventListener("click", (e) => {
            if (
                !userMenu?.contains(e.target) && !userBtn?.contains(e.target) &&
                !notifMenu?.contains(e.target) && !notifBtn?.contains(e.target)
            ) {
                closeAllMenus();
            }
        });
    });
</script>
<?php if (isset($_SESSION['usuario'])): ?>
    <script>
        // --- Evitar volver atrás SOLO si el usuario está logueado ---
        window.history.pushState(null, "", window.location.href);
        window.onpopstate = function() {
            window.history.pushState(null, "", window.location.href);
        };
    </script>
<?php endif; ?>
</body>

</html>