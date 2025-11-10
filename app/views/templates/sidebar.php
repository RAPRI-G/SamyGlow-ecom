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

    <!-- MENÚ PRINCIPAL -->
    <nav class="flex-1 overflow-y-auto py-4">

        <!-- DASHBOARD -->
        <a href="?view=dashboard" class="menu-item flex items-center px-6 py-3 text-white <?= activeClass('dashboard', $currentView) ?>">
            <i class="fas fa-chart-pie mr-3"></i>
            <span>Dashboard</span>
        </a>

        <!-- VENTAS Y PEDIDOS -->
        <div class="menu-group">
            <a href="#" class="menu-item flex items-center justify-between px-6 py-3 text-white" data-target="ventas-pedidos">
                <div class="flex items-center">
                    <i class="fas fa-shopping-bag mr-3"></i>
                    <span>Ventas & Pedidos</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform"></i>
            </a>
            <div class="submenu">
                <a href="?view=nuevo-pedido" class="menu-item flex items-center px-10 py-2 text-white text-sm opacity-90">
                    <i class="fas fa-plus-circle mr-2"></i> Nuevo Pedido
                </a>
                <a href="?view=pedidos-pendientes" class="menu-item flex items-center px-10 py-2 text-white text-sm opacity-90">
                    <i class="fas fa-clock mr-2"></i> Pedidos Pendientes
                    <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1">4</span>
                </a>
                <a href="?view=pedidos-entregados" class="menu-item flex items-center px-10 py-2 text-white text-sm opacity-90">
                    <i class="fas fa-check-circle mr-2"></i> Pedidos Entregados
                </a>
                <a href="?view=historial-ventas" class="menu-item flex items-center px-10 py-2 text-white text-sm opacity-90">
                    <i class="fas fa-history mr-2"></i> Historial de Ventas
                </a>
            </div>
        </div>

        <!-- PRODUCTOS -->
        <div class="menu-group">
            <a href="#" class="menu-item flex items-center justify-between px-6 py-3 text-white" data-target="productos">
                <div class="flex items-center">
                    <i class="fas fa-cubes mr-3"></i>
                    <span>Gestión de Productos</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform"></i>
            </a>
            <div class="submenu">
                <a href="?view=gestion-productos" class="menu-item flex items-center px-10 py-2 text-white text-sm opacity-90">
                    <i class="fas fa-list mr-2"></i> Todos los Productos
                </a>
                <a href="?view=categorias" class="menu-item flex items-center px-10 py-2 text-white text-sm opacity-90">
                    <i class="fas fa-tags mr-2"></i> Categorías
                </a>
                <a href="?view=inventario" class="menu-item flex items-center px-10 py-2 text-white text-sm opacity-90">
                    <i class="fas fa-boxes mr-2"></i> Control de Inventario
                </a>
                <a href="?view=stock-bajo" class="menu-item flex items-center px-10 py-2 text-white text-sm opacity-90">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Stock Bajo
                </a>
                <a href="?view=nuevo-producto" class="menu-item flex items-center px-10 py-2 text-white text-sm opacity-90">
                    <i class="fas fa-plus mr-2"></i> Agregar Producto
                </a>
            </div>
        </div>

        <!-- CLIENTES -->
        <div class="menu-group">
            <a href="#" class="menu-item flex items-center justify-between px-6 py-3 text-white" data-target="clientes">
                <div class="flex items-center">
                    <i class="fas fa-users mr-3"></i>
                    <span>Clientes</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform"></i>
            </a>
            <div class="submenu">
                <a href="?view=clientes" class="menu-item flex items-center px-10 py-2 text-white text-sm opacity-90">
                    <i class="fas fa-address-book mr-2"></i> Lista de Clientes
                </a>
                <a href="?view=clientes-frecuentes" class="menu-item flex items-center px-10 py-2 text-white text-sm opacity-90">
                    <i class="fas fa-star mr-2"></i> Clientes Frecuentes
                </a>
                <a href="?view=nuevo-cliente" class="menu-item flex items-center px-10 py-2 text-white text-sm opacity-90">
                    <i class="fas fa-user-plus mr-2"></i> Nuevo Cliente
                </a>
            </div>
        </div>

        <!-- PROMOCIONES -->
        <div class="menu-group">
            <a href="#" class="menu-item flex items-center justify-between px-6 py-3 text-white" data-target="promociones">
                <div class="flex items-center">
                    <i class="fas fa-tags mr-3"></i>
                    <span>Promociones</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform"></i>
            </a>
            <div class="submenu">
                <a href="?view=promociones" class="menu-item flex items-center px-10 py-2 text-white text-sm opacity-90">
                    <i class="fas fa-bolt mr-2"></i> Promociones Activas
                </a>
                <a href="?view=nueva-promocion" class="menu-item flex items-center px-10 py-2 text-white text-sm opacity-90">
                    <i class="fas fa-plus mr-2"></i> Nueva Promoción
                </a>
                <a href="?view=productos-promocion" class="menu-item flex items-center px-10 py-2 text-white text-sm opacity-90">
                    <i class="fas fa-gift mr-2"></i> Productos en Promoción
                </a>
            </div>
        </div>

        <!-- MÉTODOS DE PAGO -->
        <a href="?view=metodos-pago" class="menu-item flex items-center px-6 py-3 text-white <?= activeClass('metodos-pago', $currentView) ?>">
            <i class="fas fa-credit-card mr-3"></i>
            <span>Métodos de Pago</span>
        </a>

        <!-- REPORTES -->
        <div class="menu-group">
            <a href="#" class="menu-item flex items-center justify-between px-6 py-3 text-white" data-target="reportes">
                <div class="flex items-center">
                    <i class="fas fa-chart-bar mr-3"></i>
                    <span>Reportes</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform"></i>
            </a>
            <div class="submenu">
                <a href="?view=reporte-ventas" class="menu-item flex items-center px-10 py-2 text-white text-sm opacity-90">
                    <i class="fas fa-chart-line mr-2"></i> Reporte de Ventas
                </a>
                <a href="?view=reporte-productos" class="menu-item flex items-center px-10 py-2 text-white text-sm opacity-90">
                    <i class="fas fa-cube mr-2"></i> Productos Más Vendidos
                </a>
                <a href="?view=reporte-inventario" class="menu-item flex items-center px-10 py-2 text-white text-sm opacity-90">
                    <i class="fas fa-boxes mr-2"></i> Estado de Inventario
                </a>
            </div>
        </div>

        <!-- CONFIGURACIÓN -->
        <div class="menu-group">
            <a href="#" class="menu-item flex items-center justify-between px-6 py-3 text-white" data-target="configuracion">
                <div class="flex items-center">
                    <i class="fas fa-cog mr-3"></i>
                    <span>Configuración</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform"></i>
            </a>
            <div class="submenu">
                <a href="?view=usuarios" class="menu-item flex items-center px-10 py-2 text-white text-sm opacity-90">
                    <i class="fas fa-user-shield mr-2"></i> Usuarios Administradores
                </a>
                <a href="?view=backup" class="menu-item flex items-center px-10 py-2 text-white text-sm opacity-90">
                    <i class="fas fa-database mr-2"></i> Backup de Datos
                </a>
                <a href="?view=config-general" class="menu-item flex items-center px-10 py-2 text-white text-sm opacity-90">
                    <i class="fas fa-sliders-h mr-2"></i> Configuración General
                </a>
            </div>
        </div>

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

<!-- JS DEL SIDEBAR -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Submenús desplegables
        document.querySelectorAll('.menu-group > .menu-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const submenu = this.nextElementSibling;
                const icon = this.querySelector('.fa-chevron-down');
                if (submenu.classList.contains('submenu')) {
                    submenu.classList.toggle('open');
                    icon.classList.toggle('rotate-180');
                }
            });
        });

        // Marcar menú activo
        document.querySelectorAll('.menu-item:not(.menu-group > .menu-item)').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('bg-white', 'bg-opacity-25', 'font-semibold', 'border-r-4'));
                this.classList.add('bg-white', 'bg-opacity-25', 'font-semibold', 'border-r-4');
            });
        });
    });
</script>

<style>
    .submenu {
        display: none;
        flex-direction: column;
    }

    .submenu.open {
        display: flex;
    }

    .menu-item i {
        transition: transform 0.3s ease;
    }

    .menu-item i.rotate-180 {
        transform: rotate(180deg);
    }
</style>