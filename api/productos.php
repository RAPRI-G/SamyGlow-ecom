<?php
declare(strict_types=1);

// Cabeceras CORS y JSON
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	http_response_code(204);
	exit;
}

require_once __DIR__ . '/../models/ProductoModel.php';

// Usaremos un proxy público (`public/image.php`) que sirve archivos desde
// la carpeta `uploads/` ubicada fuera de `public/`. Devolveremos URLs
// relativas que serán resueltas por las páginas dentro de `public/`.
// Ejemplo resultante: `image.php?f=productos/miimagen.jpg`
$IMAGE_PROXY_PREFIX = 'image.php?f=';

try {
	$model = new ProductoModel();

	$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

	if ($id !== null && $id !== false) {
		$producto = $model->obtenerProductoPorId((int)$id);
		if ($producto === null) {
			http_response_code(404);
			echo json_encode([
				'success' => false,
				'data' => null,
				'total' => 0,
				'message' => 'Producto no encontrado'
			]);
			exit;
		}

		// 🔗 Agregar la URL al proxy público `image.php` (si la imagen no es URL absoluta)
		if (!empty($producto['imagen'])) {
			if (!preg_match('#^https?://#i', $producto['imagen'])) {
				$img = str_replace('\\', '/', $producto['imagen']);
				// Normalizar si la BD guarda rutas con `uploads/` o `uploads/productos/`
				$img = preg_replace('#.*/uploads/productos/#i', '', $img);
				$img = preg_replace('#.*/uploads/#i', '', $img);
				$img = ltrim($img, '/');
				// codificar cada segmento para URLs seguras
				$segments = explode('/', $img);
				$enc = implode('/', array_map('rawurlencode', $segments));
				$producto['imagen'] = $IMAGE_PROXY_PREFIX . $enc;
			}
		}

		echo json_encode([
			'success' => true,
			'data' => $producto,
			'total' => 1,
			'message' => 'Producto obtenido'
		]);
		exit;
	}

	// Obtener todos los productos
	$productos = $model->obtenerProductos();

	// 🔗 Agregar la URL completa a cada imagen
	foreach ($productos as &$p) {
		if (!empty($p['imagen'])) {
			if (!preg_match('#^https?://#i', $p['imagen'])) {
				// Extraer solo el nombre del archivo
				$img = basename($p['imagen']);  // Ej: "6915b016469b9_1763029014.jpg"
				$p['imagen'] = 'image.php?f=productos/' . $img;
			}
		}
	}
	unset($p);

	// Filtrado opcional: solo con descuento
	$discounted = isset($_GET['discounted']) ? (int)$_GET['discounted'] : 0;
	if ($discounted === 1) {
		$productos = array_values(array_filter($productos, function ($p) {
			return (isset($p['descuento']) && (float)$p['descuento'] > 0) ||
				   (isset($p['tipo_descuento']) && $p['tipo_descuento'] !== null);
		}));
	}

	// Límite opcional
	$limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT);
	if ($limit !== false && $limit !== null && $limit > 0) {
		$productos = array_slice($productos, 0, $limit);
	}

	echo json_encode([
		'success' => true,
		'data' => $productos,
		'total' => count($productos),
		'message' => 'Productos listados correctamente'
	]);
	exit;
} catch (Throwable $e) {
	error_log('api/productos.php error: ' . $e->getMessage());
	http_response_code(500);
	echo json_encode([
		'success' => false,
		'data' => null,
		'total' => 0,
		'message' => 'Error interno del servidor'
	]);
	exit;
}
