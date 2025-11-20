<?php
// public/api/metodos-pago.php
// Wrapper público que utiliza el modelo del aplicativo y la clase Database

define('BASE_PATH', __DIR__ . '/../../');

// Forzar JSON como tipo de respuesta
header('Content-Type: application/json; charset=utf-8');

// Evitar que warnings/notices rompan el JSON de salida
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

$response = ['success' => false, 'data' => [], 'message' => ''];

try {
    // Incluir la clase de conexión y el modelo
    require_once BASE_PATH . 'config/conexion.php';
    require_once BASE_PATH . 'models/MetodoPagoModel.php';

    // Obtener la conexión PDO desde la clase Database
    if (!class_exists('Database')) {
        throw new Exception('Clase Database no encontrada en config/conexion.php');
    }

    $dbInstance = Database::getInstance();
    $pdo = $dbInstance->getConnection();

    // Instanciar el modelo y obtener métodos activos
    $model = new MetodoPago($pdo);
    $metodos = $model->obtenerTodos();

    if (!empty($metodos)) {
        $response['success'] = true;
        $response['data'] = $metodos;
    } else {
        $response['message'] = 'No se encontraron métodos de pago activos.';
    }

} catch (Throwable $e) {
    http_response_code(500);
    $response['message'] = 'Error interno del servidor.';
    // Registrar el error en el log sin imprimirlo al cliente
    error_log('public/api/metodos-pago.php error: ' . $e->getMessage());
}

echo json_encode($response);