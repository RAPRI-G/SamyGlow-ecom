<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/config/../config/conexion.php';

try {
    $db = Database::getInstance()->getConnection();
    $sql = 'SELECT id, nombre, activa FROM categorias WHERE activa = 1 ORDER BY nombre ASC';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $rows,
        'total' => count($rows),
        'message' => 'Categorías obtenidas'
    ]);
    exit;
} catch (Throwable $e) {
    error_log('api/categorias.php error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'data' => null,
        'total' => 0,
        'message' => 'Error interno del servidor'
    ]);
    exit;
}
