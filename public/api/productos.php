<?php
/**
 * Endpoint público para acceder a la API de productos.
 * Redirige la solicitud al archivo real ubicado en /api/productos.php.
 * 
 * Se recomienda usar este wrapper cuando el servidor sirve desde /public/
 * y la API está en la raíz del proyecto (fuera del alcance público).
 */

// Asegurar respuesta JSON siempre
header('Content-Type: application/json; charset=utf-8');

// Ruta real del archivo de la API
$apiPath = realpath(__DIR__ . '/../../api/productos.php');

// Verificar si el archivo existe y es accesible
if ($apiPath && file_exists($apiPath)) {
    require_once $apiPath;
    exit;
}

// Si el archivo no existe o hay un error de ruta
http_response_code(500);
echo json_encode([
    'success' => false,
    'data' => null,
    'total' => 0,
    'message' => 'Error: no se encontró el archivo de la API de productos en el servidor.'
]);
exit;
