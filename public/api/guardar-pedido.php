<?php
// public/api/guardar-pedido.php - VERSIÓN DESARROLLO
// Wrapper público que utiliza el modelo Pedido.php

define('BASE_PATH', __DIR__ . '/../../');

// ✅ Forzar JSON como tipo de respuesta
header('Content-Type: application/json; charset=utf-8');

$response = ['success' => false, 'message' => '', 'pedido_id' => null];

try {
    // Incluir la clase de conexión y el modelo
    require_once BASE_PATH . 'config/conexion.php';
    require_once BASE_PATH . 'models/PedidosModel.php';

    // Obtener la conexión PDO desde la clase Database
    if (!class_exists('Database')) {
        throw new Exception('Clase Database no encontrada en config/conexion.php');
    }

    $dbInstance = Database::getInstance();
    $pdo = $dbInstance->getConnection();

    // 1. Obtener el cuerpo de la solicitud (será JSON)
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    // Validar datos de entrada
    if (!$data || !isset($data['cliente']) || !isset($data['items']) || !isset($data['metodo_pago_id'])) {
        http_response_code(400);
        $response['message'] = 'Datos incompletos para procesar el pedido. Recibido: ' . $raw;
        echo json_encode($response);
        exit;
    }

    if (count($data['items']) === 0) {
        http_response_code(400);
        $response['message'] = 'No se puede procesar un pedido sin productos.';
        echo json_encode($response);
        exit;
    }

    // 2. Buscar o crear el cliente
    $cliente = $data['cliente'];
    $cliente_id = null;

    // ✅ Cambio aquí: correo en lugar de email, dni en lugar de documento
    $sql_find_cliente = "SELECT id FROM clientes WHERE correo = ? OR dni = ?";
    $stmt_find = $pdo->prepare($sql_find_cliente);
    $stmt_find->execute([$cliente['email'], $cliente['documento']]);
    $result_cliente = $stmt_find->fetch(PDO::FETCH_ASSOC);

    if ($result_cliente) {
        $cliente_id = $result_cliente['id'];
    } else {
        // ✅ Cambio aquí: nombres, apellidos, dni, correo, telefono
        $sql_create_cliente = "INSERT INTO clientes (nombres, apellidos, dni, correo, telefono) VALUES (?, ?, ?, ?, ?)";
        $stmt_create = $pdo->prepare($sql_create_cliente);
        $stmt_create->execute([
            $cliente['nombre'],
            $cliente['apellido'],
            $cliente['documento'],
            $cliente['email'],
            $cliente['telefono']
        ]);
        $cliente_id = $pdo->lastInsertId();
    }

    if (!$cliente_id) {
        throw new Exception("No se pudo obtener el ID del cliente.");
    }

    // 3. Preparar datos para el modelo Pedido
    $pedidoData = [
        'cliente' => $cliente_id,
        'items' => [],
        'payment' => intval($data['metodo_pago_id']),
        'notes' => $data['notas'] ?? null
    ];

    foreach ($data['items'] as $item) {
        $pedidoData['items'][] = [
            'producto_id' => intval($item['id']),
            'cantidad' => intval($item['cantidad'])
        ];
    }

    // 4. Usar el modelo Pedido para guardar
    $pedidoModel = new Pedido($pdo);
    $resultado = $pedidoModel->guardar($pedidoData);

    if ($resultado['ok']) {
        $response['success'] = true;
        $response['message'] = 'Pedido guardado exitosamente';
        $response['pedido_id'] = $resultado['pedido_id'];

        // Limpiar el carrito de la sesión si existe
        if (isset($_SESSION['carrito'])) {
            unset($_SESSION['carrito']);
        }
    } else {
        http_response_code(400);
        $response['message'] = $resultado['error'];
    }

} catch (Throwable $e) {
    http_response_code(500);
    // ✅ Muestra el error real (solo en desarrollo)
    $response['message'] = 'Error interno: ' . $e->getMessage();
    error_log('public/api/guardar-pedido.php error: ' . $e->getMessage());
}
