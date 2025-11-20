<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// Evitar caché en páginas privadas
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Panel - SamyGlow' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #f472b6;
            --secondary: #a78bfa;
            --accent: #fbbf24;
            --dark: #1f2937;
            --light: #f9fafb;
        }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background-color: #f8fafc;
        }

        .sidebar {
            background: linear-gradient(180deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-3px);
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .notification-dot {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #ef4444;
        }

        #userDropdown {
            transform-origin: top right;
            transition: all 0.15s ease-in-out;
        }

        #userDropdown.hidden {
            opacity: 0;
            transform: scale(0.95);
        }

        /* ESTILOS PARA MODALES - AGREGAR ESTOS */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background-color: white;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        /* Loading styles */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
        }

        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #ec4899;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body class="flex h-screen">

    <!-- Sidebar a la izquierda -->
    <?php require __DIR__ . '/sidebar.php'; ?>

    <!-- Contenedor principal (header + contenido) -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Header / Topbar -->
        <header class="bg-white shadow-sm relative w-full">
            <div class="flex items-center justify-between px-6 py-4">
                <!-- Título dinámico -->
                <h1 class="text-2xl font-bold text-gray-800">
                    <?= htmlspecialchars($pageTitle ?? 'Dashboard') ?>
                </h1>

                <!-- Buscador -->
                <div class="flex-1 mx-6 hidden md:flex">
                    <div class="relative w-full max-w-md">
                        <input
                            type="text"
                            id="searchInput"
                            placeholder="Buscar productos, clientes o pedidos..."
                            class="w-full px-4 py-2 pl-10 pr-4 text-sm text-gray-700 border border-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent"
                            autocomplete="off" />
                        <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                        <div id="searchResults"
                            class="hidden absolute mt-2 w-full bg-white border border-gray-100 rounded-lg shadow-xl z-50 max-h-64 overflow-y-auto">
                            <ul id="resultsList" class="divide-y divide-gray-100 text-sm text-gray-700"></ul>
                        </div>
                    </div>
                </div>

                <!-- Notificaciones y Usuario -->
                <div class="flex items-center space-x-6">
                    <div class="relative">
                        <button id="notifButton" class="p-2 text-gray-600 hover:text-gray-900 focus:outline-none relative">
                            <i class="fas fa-bell text-xl"></i>
                            <span class="notification-dot"></span>
                        </button>
                    </div>

                    <div class="relative">
                        <button id="userMenuButton" class="flex items-center space-x-2 focus:outline-none">
                            <div class="w-9 h-9 rounded-full bg-pink-100 flex items-center justify-center">
                                <i class="fas fa-user text-pink-500"></i>
                            </div>
                            <span class="font-medium text-gray-700">
                                <?= htmlspecialchars($_SESSION['usuario']['nombres'] ?? 'Administradora') ?>
                            </span>
                            <i class="fas fa-chevron-down text-gray-500 text-sm"></i>
                        </button>

                        <div id="userDropdown"
                            class="hidden absolute right-0 mt-2 w-44 bg-white border border-gray-100 rounded-lg shadow-lg z-50">
                            <a href="?view=dashboard" class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
                                <i class="fas fa-chart-pie mr-2 text-pink-500"></i> Dashboard
                            </a>
                            <a href="index.php?view=logout"
                                class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
                                <i class="fas fa-sign-out-alt mr-2 text-red-500"></i> Cerrar sesión
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>