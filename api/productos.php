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

		echo json_encode([
			'success' => true,
			'data' => $producto,
			'total' => 1,
			'message' => 'Producto obtenido'
		]);
		exit;
	}

	// Obtener todos
	$productos = $model->obtenerProductos();

	// Filtrado opcional: solo con descuento
	$discounted = isset($_GET['discounted']) ? (int)$_GET['discounted'] : 0;
	if ($discounted === 1) {
		$productos = array_values(array_filter($productos, function ($p) {
			return (isset($p['descuento']) && (float)$p['descuento'] > 0) || (isset($p['tipo_descuento']) && $p['tipo_descuento'] !== null);
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
