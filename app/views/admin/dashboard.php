<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Evitar que el navegador guarde en caché el dashboard
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?view=login");
    exit;
}

$title = "Dashboard - SamyGlow";
$pageTitle = "Dashboard";

require __DIR__ . "/../templates/header.php";
require __DIR__ . "/../templates/footer.php";
?>
    <!-- CONTENIDO PRINCIPAL -->
    <main class="flex-1 overflow-y-auto p-6">
        <!-- TARJETAS ESTADÍSTICAS -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="card p-6">
                <p class="text-gray-500 text-sm">Total Ventas</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">
                    S/ <?= number_format($data['estadisticas']['total_ventas'], 2) ?>
                </h3>
            </div>
            <div class="card p-6">
                <p class="text-gray-500 text-sm">Pedidos Pendientes</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">
                    <?= $data['estadisticas']['pedidos_pendientes'] ?>
                </h3>
            </div>
            <div class="card p-6">
                <p class="text-gray-500 text-sm">Clientes Registrados</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">
                    <?= $data['estadisticas']['clientes'] ?>
                </h3>
            </div>
            <div class="card p-6">
                <p class="text-gray-500 text-sm">Productos Activos</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">
                    <?= $data['estadisticas']['productos_activos'] ?>
                </h3>
            </div>
        </div>

        <!-- GRÁFICO DE VENTAS -->
        <div class="card p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Ventas Mensuales</h3>
            <canvas id="salesChart" height="100"></canvas>
        </div>

        <script>
            const ctx = document.getElementById('salesChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
                    datasets: [{
                        label: 'Ventas (S/)',
                        data: [950, 1200, 1400, 1100, 1550, 1300, 1600, 1800, 1500, 1700, 1900, 2100],
                        backgroundColor: 'rgba(244, 114, 182, 0.7)',
                    }]
                },
                options: { responsive: true, scales: { y: { beginAtZero: true } } }
            });
        </script>

        <!-- PEDIDOS RECIENTES -->
        <div class="card p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">Pedidos Recientes</h3>
                <a href="#" class="text-sm text-pink-500 font-medium">Ver todos</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-gray-500 text-sm border-b">
                            <th class="pb-3">Cliente</th>
                            <th class="pb-3">Fecha</th>
                            <th class="pb-3">Total</th>
                            <th class="pb-3">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($data['pedidos'] as $p): ?>
                        <tr>
                            <td class="py-3 font-medium">
                                <?= htmlspecialchars($p['nombres'] . ' ' . $p['apellidos']) ?>
                            </td>
                            <td class="py-3"><?= date('d/m/Y H:i', strtotime($p['fecha'])) ?></td>
                            <td class="py-3 font-medium">S/ <?= number_format($p['total'], 2) ?></td>
                            <td class="py-3">
                                <?php if ($p['estado'] === 'entregado'): ?>
                                    <span class="badge badge-success">Entregado</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Pendiente</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ALERTAS DE STOCK -->
        <div class="card p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Alertas de Stock</h3>
            <?php if (count($data['alertas_stock']) === 0): ?>
                <p class="text-gray-500">No hay alertas de stock bajo 🎉</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($data['alertas_stock'] as $prod): ?>
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                            <div>
                                <p class="font-medium"><?= htmlspecialchars($prod['nombre']) ?></p>
                                <p class="text-sm text-gray-500">Stock: <?= $prod['stock'] ?> unidades</p>
                            </div>
                            <span class="badge badge-danger">BAJO</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
