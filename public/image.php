<?php
// public/image.php
/**
 * Proxy de imágenes seguro
 * Sirve archivos desde ../uploads/ sin exponer la ruta completa
 */

// Configuración básica
$baseDir = realpath(__DIR__ . '/../uploads');
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$defaultImage = realpath(__DIR__ . '/assets/default.jpg');

// Obtener archivo solicitado
$file = $_GET['f'] ?? '';
if (strpos($file, 'uploads/') === 0) {
    $file = substr($file, 8); // Quita "uploads/"
}

// Decodificar y sanitizar
$file = urldecode($file);
$file = ltrim($file, '/');
$path = realpath($baseDir . '/' . $file);

// Verificar seguridad
if ($path === false || strpos($path, $baseDir) !== 0) {
    // Archivo no encontrado o fuera del directorio permitido
    serveDefaultImage();
    exit;
}

// Verificar extensión
$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
if (!in_array($ext, $allowedExtensions)) {
    serveDefaultImage();
    exit;
}

// Verificar que exista
if (!file_exists($path) || !is_file($path)) {
    serveDefaultImage();
    exit;
}

// Determinar tipo MIME
$mimeTypes = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'webp' => 'image/webp'
];

$contentType = $mimeTypes[$ext] ?? 'application/octet-stream';

// Enviar imagen
header('Content-Type: ' . $contentType);
header('Content-Length: ' . filesize($path));
header('Cache-Control: public, max-age=86400'); // Cache por 24 horas

readfile($path);
exit;

function serveDefaultImage()
{
    global $defaultImage;
    
    if ($defaultImage && file_exists($defaultImage)) {
        header('Content-Type: image/jpeg');
        header('Cache-Control: public, max-age=3600');
        readfile($defaultImage);
    } else {
        http_response_code(404);
        header('Content-Type: image/svg+xml');
        echo '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100">
                <rect width="100" height="100" fill="#ddd"/>
                <text x="50" y="50" text-anchor="middle" fill="#666" font-size="10">Imagen no encontrada</text>
              </svg>';
    }
}