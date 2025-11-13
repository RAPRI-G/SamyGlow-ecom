<?php
// Wrapper para exponer el endpoint de API cuando el servidor sirva desde el directorio `public/`.
// Incluye el archivo real ubicado en la raíz del proyecto: /api/productos.php

// Evitar errores si se accede directamente
$realPath = __DIR__ . '/../../api/productos.php';
if (file_exists($realPath)) {
    require_once $realPath;
} else {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'data' => null,
        'total' => 0,
        'message' => 'API no encontrada en el servidor (archivo faltante)'
    ]);
}