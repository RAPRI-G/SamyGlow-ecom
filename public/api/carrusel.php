<?php
header('Content-Type: application/json; charset=utf-8');

$baseDir = realpath(__DIR__ . '/../assets/img/carrusel-index');
$folder = isset($_GET['folder']) ? (string)$_GET['folder'] : '';
$folder = trim($folder);

$useDir = $baseDir;
$prefix = 'assets/img/carrusel-index/';

$folderMap = [
    'index' => 'carrusel-index',
    'contacto' => 'carrusel-contacto',
    'tienda' => 'carrusel-tienda'
];

if ($folder !== '' && isset($folderMap[$folder])) {
    $dirName = $folderMap[$folder];
    $candidate = realpath(__DIR__ . '/../assets/img/' . $dirName);
    if ($candidate && is_dir($candidate)) {
        $useDir = $candidate;
        $prefix = 'assets/img/' . $dirName . '/';
    }
}

if (!$useDir || !is_dir($useDir)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'images' => [],
        'message' => 'Directorio de carrusel no encontrado.'
    ]);
    exit;
}

$allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
$files = @scandir($useDir);
if ($files === false) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'images' => [],
        'message' => 'No se pudo leer el directorio de carrusel.'
    ]);
    exit;
}

$images = [];
foreach ($files as $file) {
    if ($file === '.' || $file === '..') {
        continue;
    }

    $path = $useDir . DIRECTORY_SEPARATOR . $file;
    if (!is_file($path)) {
        continue;
    }

    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) {
        continue;
    }

    $images[] = $prefix . $file;
}

sort($images, SORT_NATURAL | SORT_FLAG_CASE);

echo json_encode([
    'success' => true,
    'images' => $images,
    'total' => count($images)
]);
