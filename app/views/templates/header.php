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
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
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

        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-primary {
            background-color: #fce7f3;
            color: #be185d;
        }

        .notification-dot {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #ef4444;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            }
            
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 6px rgba(239, 68, 68, 0);
            }
            
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }

        #userDropdown {
            transform-origin: top right;
            transition: all 0.15s ease-in-out;
        }

        #userDropdown.hidden {
            opacity: 0;
            transform: scale(0.95);
            pointer-events: none;
        }

        #notifDropdown {
            transform-origin: top right;
            transition: all 0.15s ease-in-out;
        }

        #notifDropdown.hidden {
            opacity: 0;
            transform: scale(0.95);
            pointer-events: none;
        }

        /* Scrollbar personalizado */
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        /* Efectos hover para menús */
        .menu-item {
            transition: all 0.2s ease;
            border-right: 4px solid transparent;
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.1);
            border-right-color: rgba(255, 255, 255, 0.3);
        }

        /* Buscador animado */
        #searchInput:focus {
            box-shadow: 0 0 0 3px rgba(244, 114, 182, 0.1);
        }

        /* Loading spinner */
        .spinner {
            border: 2px solid #f3f4f6;
            border-top: 2px solid #f472b6;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Tablas estilizadas */
        .table-styled {
            border-collapse: collapse;
            width: 100%;
        }

        .table-styled th {
            background-color: #f8fafc;
            padding: 12px 16px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
        }

        .table-styled td {
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
        }

        .table-styled tr:hover {
            background-color: #f9fafb;
        }

        /* Botones personalizados */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
            outline: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(244, 114, 182, 0.4);
        }

        .btn-secondary {
            background-color: #f3f4f6;
            color: #374151;
        }

        .btn-secondary:hover {
            background-color: #e5e7eb;
        }

        .btn-success {
            background-color: #10b981;
            color: white;
        }

        .btn-success:hover {
            background-color: #059669;
        }

        .btn-danger {
            background-color: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        /* Modal styles */
        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        .modal-content {
            transform: scale(0.95);
            opacity: 0;
            transition: all 0.2s ease;
        }

        .modal-content.show {
            transform: scale(1);
            opacity: 1;
        }

        /* Form styles */
        .form-input {
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            border-color: #f472b6;
            box-shadow: 0 0 0 3px rgba(244, 114, 182, 0.1);
            outline: none;
        }

        /* Stats cards */
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(30deg);
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                height: 100vh;
                z-index: 50;
            }
            
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            
            .mobile-menu-btn {
                display: block;
            }
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
                        
                        <!-- Dropdown de Notificaciones -->
                        <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white border border-gray-100 rounded-lg shadow-xl z-50">
                            <div class="p-4 border-b border-gray-100">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-semibold text-gray-800">Notificaciones</h3>
                                    <button id="clearNotif" class="text-xs text-pink-500 hover:text-pink-700">Limpiar</button>
                                </div>
                            </div>
                            <ul id="notifList" class="max-h-64 overflow-y-auto">
                                <li class="p-4 border-b border-gray-50 hover:bg-gray-50 cursor-pointer">
                                    <div class="flex items-start space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-shopping-cart text-green-600 text-sm"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-800">Nuevo pedido recibido</p>
                                            <p class="text-xs text-gray-500">Hace 5 minutos</p>
                                        </div>
                                    </div>
                                </li>
                                <li class="p-4 border-b border-gray-50 hover:bg-gray-50 cursor-pointer">
                                    <div class="flex items-start space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-exclamation-triangle text-yellow-600 text-sm"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-800">Stock bajo en producto</p>
                                            <p class="text-xs text-gray-500">Hace 1 hora</p>
                                        </div>
                                    </div>
                                </li>
                                <li class="p-4 hover:bg-gray-50 cursor-pointer">
                                    <div class="flex items-start space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-user-plus text-blue-600 text-sm"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-800">Nuevo cliente registrado</p>
                                            <p class="text-xs text-gray-500">Hace 2 horas</p>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
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