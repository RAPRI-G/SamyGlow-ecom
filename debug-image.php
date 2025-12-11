<?php
// debug-image.php
echo "<h1>Debug de Imágenes</h1>";

// Información del servidor
echo "<h2>Información del Servidor:</h2>";
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "<br>";

// Ruta de la imagen en BD
$imagenBD = 'uploads/productos/fullimage-1.webp';
echo "<h2>Ruta en Base de Datos:</h2>";
echo "'$imagenBD'<br>";

// Probar diferentes URLs
echo "<h2>Probar URLs:</h2>";
$urls = [
    '/' . $imagenBD,
    '/public/' . $imagenBD,
    '/SamyGlow-ecom/' . $imagenBD,
    '/SamyGlow-ecom/public/' . $imagenBD,
    dirname($_SERVER['SCRIPT_NAME']) . '/' . $imagenBD
];

foreach ($urls as $url) {
    echo "URL: <a href='$url' target='_blank'>$url</a><br>";
}

// Verificar archivo físico
echo "<h2>Archivos físicos:</h2>";
$rutasFisicas = [
    $_SERVER['DOCUMENT_ROOT'] . '/' . $imagenBD,
    $_SERVER['DOCUMENT_ROOT'] . '/public/' . $imagenBD,
    __DIR__ . '/' . $imagenBD,
    __DIR__ . '/public/' . $imagenBD,
    __DIR__ . '/uploads/productos/fullimage-1.webp'
];

foreach ($rutasFisicas as $ruta) {
    echo "Ruta: $ruta<br>";
    echo "Existe: " . (file_exists($ruta) ? '✅ SÍ' : '❌ NO') . "<br><br>";
}