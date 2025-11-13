<?php
// public/api/guardar-pedido.php
// Este es el wrapper público para crear un nuevo pedido

define('BASE_PATH', __DIR__ . '/../../');
require_once BASE_PATH . 'config/conexion.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

// 1. Obtener el cuerpo de la solicitud (será JSON)
$data = json_decode(file_get_contents('php://input'), true);

// Validar datos de entrada
if (!$data || !isset($data['cliente']) || !isset($data['items']) || !isset($data['totales']) || !isset($data['metodo_pago_id'])) {
    http_response_code(400); // Bad Request
    $response['message'] = 'Datos incompletos para procesar el pedido.';
    echo json_encode($response);
    exit;
}
if (count($data['items']) === 0) {
    http_response_code(400);
    $response['message'] = 'No se puede procesar un pedido sin productos.';
    echo json_encode($response);
    exit;
}

$conn = null;
try {
    $conn = getConexion();
    
    // Iniciar transacción
    $conn->begin_transaction();

    // 2. Buscar o crear el cliente
    $cliente = $data['cliente'];
    $cliente_id = null;

    // Asumimos que el 'documento' o 'email' debe ser único
    $sql_find_cliente = "SELECT id FROM clientes WHERE email = ? OR documento = ?";
    $stmt_find = $conn->prepare($sql_find_cliente);
    $stmt_find->bind_param("ss", $cliente['email'], $cliente['documento']);
    $stmt_find->execute();
    $result_cliente = $stmt_find->get_result();

    if ($result_cliente->num_rows > 0) {
        // Cliente encontrado
        $cliente_id = $result_cliente->fetch_assoc()['id'];
    } else {
        // Cliente no encontrado, lo creamos
        $sql_create_cliente = "INSERT INTO clientes (nombre, apellido, email, telefono, documento, direccion) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_create = $conn->prepare($sql_create_cliente);
        $stmt_create->bind_param("ssssss",
            $cliente['nombre'],
            $cliente['apellido'],
            $cliente['email'],
            $cliente['telefono'],
            $cliente['documento'],
            $cliente['direccion']
        );
        $stmt_create->execute();
        $cliente_id = $stmt_create->insert_id;
        $stmt_create->close();
    }
    $stmt_find->close();

    if (!$cliente_id) {
        throw new Exception("No se pudo obtener el ID del cliente.");
    }

    // 3. Insertar el Pedido (tabla `pedidos`)
    $totales = $data['totales'];
    $sql_pedido = "INSERT INTO pedidos (cliente_id, subtotal, total, metodo_pago_id, estado) VALUES (?, ?, ?, ?, 'pendiente')";
    $stmt_pedido = $conn->prepare($sql_pedido);
    $stmt_pedido->bind_param("iddi", 
        $cliente_id, 
        $totales['subtotal'], 
        $totales['total'], 
        $data['metodo_pago_id']
    );
    $stmt_pedido->execute();
    $pedido_id = $stmt_pedido->insert_id;
    $stmt_pedido->close();

    if (!$pedido_id) {
        throw new Exception("No se pudo crear el registro del pedido.");
    }

    // 4. Insertar el Detalle del Pedido (tabla `detalle_pedidos`)
    $items = $data['items'];
    $sql_detalle = "INSERT INTO detalle_pedidos (pedido_id, producto_id, nombre_producto, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_detalle = $conn->prepare($sql_detalle);

    foreach ($items as $item) {
        $producto_id = intval($item['id']);
        $nombre_producto = $item['nombre'];
        $cantidad = intval($item['cantidad']);
        $precio = floatval($item['precio']);
        $subtotal_item = $cantidad * $precio;

        $stmt_detalle->bind_param("iisidd",
            $pedido_id,
            $producto_id,
            $nombre_producto,
            $cantidad,
            $precio,
            $subtotal_item
        );
        $stmt_detalle->execute();
    }
    $stmt_detalle->close();

    // 5. Si todo fue bien, confirmar la transacción
    $conn->commit();
    
    $response['success'] = true;
    $response['message'] = '¡Pedido registrado con éxito!';
    $response['pedido_id'] = $pedido_id; // Devolvemos el ID del nuevo pedido

} catch (Exception $e) {
    // Si algo falla, revertir la transacción
    if ($conn) {
        $conn->rollback();
    }
    http_response_code(500); // Internal Server Error
    $response['message'] = 'Error al guardar el pedido: ' . $e->getMessage();
    
} finally {
    if ($conn) {
        $conn->close();
    }
}

// Devolver la respuesta
echo json_encode($response);