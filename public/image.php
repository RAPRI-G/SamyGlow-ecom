<?php
declare(strict_types=1);

// image.php — Sirve imágenes desde ../uploads/productos/
// Uso: /public/image.php?f=uploads/productos/imagen.webp

// Permitir acceso público (CORS)
header('Access-Control-Allow-Origin: *');

// Obtener parámetro
$f = $_GET['f'] ?? '';
if (!is_string($f) || $f === '') {
    http_response_code(400);
    echo 'Bad Request: parámetro f ausente.';
    exit;
}

// Normalizar separadores
$f = str_replace('\\', '/', $f);

// Evitar intentos de traversal (../)
if (strpos($f, '..') !== false) {
    http_response_code(400);
    echo 'Bad Request: ruta no permitida.';
    exit;
}

// Asegurar que solo se sirvan archivos dentro de /uploads/productos/
$allowedPrefix = 'uploads/productos/';
if (stripos($f, $allowedPrefix) !== 0) {
    http_response_code(400);
    echo 'Bad Request: solo se permiten imágenes en uploads/productos/.';
    exit;
}

// Ruta base absoluta (sube un nivel desde /public/)
$baseUploads = realpath(__DIR__ . '/../uploads');
if ($baseUploads === false) {
    http_response_code(500);
    echo 'Server misconfiguration: carpeta uploads no encontrada.';
    exit;
}

// Ruta completa del archivo solicitado
$fullPath = realpath(__DIR__ . '/../' . $f);

// Validar ruta y existencia del archivo
if ($fullPath === false || strpos($fullPath, $baseUploads) !== 0 || !is_file($fullPath)) {
    http_response_code(404);
    echo 'Archivo no encontrado.';
    exit;
}

// Determinar tipo MIME
$mime = mime_content_type($fullPath) ?: 'application/octet-stream';
$size = filesize($fullPath);

// Enviar cabeceras
header('Content-Type: ' . $mime);
header('Content-Length: ' . $size);
header('Cache-Control: public, max-age=86400');

// Enviar imagen al navegador
readfile($fullPath);
exit;
