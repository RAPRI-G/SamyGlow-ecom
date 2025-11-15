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

        // --- MARCADOR DE MENÚ ACTIVO (SIDEBAR SIMPLIFICADO) ---
        document.querySelectorAll('.menu-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('bg-white', 'bg-opacity-25', 'font-semibold', 'border-r-4'));
                this.classList.add('bg-white', 'bg-opacity-25', 'font-semibold', 'border-r-4');
            });
        });

        // --- BUSCADOR DINÁMICO ---
        const searchInput = document.getElementById("searchInput");
        const searchResults = document.getElementById("searchResults");
        const resultsList = document.getElementById("resultsList");

        if (searchInput && searchResults && resultsList) {
            searchInput.addEventListener("focus", () => {
                searchResults.classList.remove("hidden");
            });

            searchInput.addEventListener("input", (e) => {
                const query = e.target.value.toLowerCase();
                if (query.length > 2) {
                    // Simulación de resultados de búsqueda
                    resultsList.innerHTML = `
                        <li class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-cube text-green-600 text-sm"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-800">Perfume SamyGlow</div>
                                    <div class="text-xs text-gray-500">Producto • En stock</div>
                                </div>
                            </div>
                        </li>
                        <li class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-800">María González</div>
                                    <div class="text-xs text-gray-500">Cliente • 5 compras</div>
                                </div>
                            </div>
                        </li>
                        <li class="px-4 py-3 hover:bg-gray-50 cursor-pointer">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                                    <i class="fas fa-shopping-bag text-purple-600 text-sm"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-800">Pedido #SG-001</div>
                                    <div class="text-xs text-gray-500">Pedido • Pendiente</div>
                                </div>
                            </div>
                        </li>
                    `;
                } else {
                    resultsList.innerHTML = '<li class="px-4 py-3 text-center text-gray-500">Escribe al menos 3 caracteres...</li>';
                }
            });

            document.addEventListener("click", (e) => {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.classList.add("hidden");
                }
            });
        }
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