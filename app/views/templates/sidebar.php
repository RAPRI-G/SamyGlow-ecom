<?php
// Detectar vista actual desde la URL
$currentView = $_GET['view'] ?? 'dashboard';

// Función auxiliar para asignar clases dinámicas
function activeClass($viewName, $currentView)
{
    return $viewName === $currentView
        ? 'bg-white bg-opacity-25 font-semibold border-r-4 border-white'
        : 'hover:bg-white hover:bg-opacity-10';
}
?>

<!-- Sidebar optimizado para SamyGlow -->
<div class="sidebar w-64 flex-shrink-0 flex flex-col">

    <!-- LOGO -->
    <div class="p-6 border-b border-white border-opacity-20">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center">
                <i class="fas fa-crown text-pink-500 text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold">SamyGlow</h2>
                <p class="text-xs opacity-80">Victoria's Secret</p>
            </div>
        </div>
    </div>

    <!-- MENÚ PRINCIPAL SIMPLIFICADO -->
    <nav class="flex-1 overflow-y-auto py-4">

        <!-- DASHBOARD -->
        <a href="?view=dashboard" class="menu-item flex items-center px-6 py-3 text-white <?= activeClass('dashboard', $currentView) ?>">
            <i class="fas fa-chart-pie mr-3"></i>
            <span>Dashboard</span>
        </a>

        <!-- VENTAS Y PEDIDOS -->
        <a href="?view=nuevo-pedido" class="menu-item flex items-center px-6 py-3 text-white <?= activeClass('nuevo-pedido', $currentView) ?>">
            <i class="fas fa-shopping-bag mr-3"></i>
            <span>Ventas & Pedidos</span>
        </a>

        <!-- PRODUCTOS -->
        <a href="?view=gestion-productos" class="menu-item flex items-center px-6 py-3 text-white <?= activeClass('gestion-productos', $currentView) ?>">
            <i class="fas fa-cubes mr-3"></i>
            <span>Gestión de Productos</span>
        </a>

        <!-- CLIENTES -->
        <a href="?view=gestion-clientes" class="menu-item flex items-center px-6 py-3 text-white <?= activeClass('gestion-clientes', $currentView) ?>">
            <i class="fas fa-users mr-3"></i>
            <span>Clientes</span>
        </a>

        <!-- PROMOCIONES -->
        <a href="?view=gestion-promociones" class="menu-item flex items-center px-6 py-3 text-white <?= activeClass('gestion-promociones', $currentView) ?>">
            <i class="fas fa-tags mr-3"></i>
            <span>Promociones</span>
        </a>

        <!-- MÉTODOS DE PAGO -->
        <a href="?view=gestion-metodos-pago" class="menu-item flex items-center px-6 py-3 text-white <?= activeClass('gestion-metodos-pago', $currentView) ?>">
            <i class="fas fa-credit-card mr-3"></i>
            <span>Métodos de Pago</span>
        </a>

        <!-- REPORTES -->
        <a href="?view=reportes-analytics" class="menu-item flex items-center px-6 py-3 text-white <?= activeClass('reportes-analytics', $currentView) ?>">
            <i class="fas fa-chart-bar mr-3"></i>
            <span>Reportes</span>
        </a>

        <!-- CONFIGURACIÓN -->
        <a href="?view=configuracion" class="menu-item flex items-center px-6 py-3 text-white <?= activeClass('config-general', $currentView) ?>">
            <i class="fas fa-cog mr-3"></i>
            <span>Configuración</span>
        </a>

    </nav>

    <!-- FOOTER DEL SIDEBAR -->
    <div class="p-4 border-t border-white border-opacity-20">
        <div class="flex items-center space-x-3 mb-3">
            <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center">
                <i class="fas fa-user text-pink-500 text-sm"></i>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium"><?= htmlspecialchars($_SESSION['usuario']['nombres'] ?? 'Administradora') ?></p>
                <p class="text-xs opacity-80">En línea</p>
            </div>
        </div>
        <a href="index.php?view=logout"
            class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
            <i class="fas fa-sign-out-alt mr-2 text-red-500"></i> Cerrar sesión
        </a>
    </div>
</div>

<!-- JS SIMPLIFICADO -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Marcar menú activo
        document.querySelectorAll('.menu-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('bg-white', 'bg-opacity-25', 'font-semibold', 'border-r-4'));
                this.classList.add('bg-white', 'bg-opacity-25', 'font-semibold', 'border-r-4');
            });
        });
    });
</script>