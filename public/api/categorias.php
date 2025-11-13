<?php
declare(strict_types=1);

// Evitar cualquier output antes del JSON
error_reporting(E_ALL);
ini_set('display_errors', '0'); // No mostrar errores en pantalla, solo en logs

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// Ruta corregida - ir un nivel arriba y entrar a config
require_once __DIR__ . '/../config/conexion.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Consulta para obtener categorías activas
    $sql = 'SELECT id, nombre, activa FROM categorias WHERE activa = 1 ORDER BY nombre ASC';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'data' => $rows,
        'total' => count($rows),
        'message' => 'Categorías obtenidas correctamente'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    // Error de base de datos
    error_log('api/categorias.php - Error DB: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'data' => [],
        'total' => 0,
        'message' => 'Error al consultar categorías'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Throwable $e) {
    // Cualquier otro error
    error_log('api/categorias.php - Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'data' => [],
        'total' => 0,
        'message' => 'Error interno del servidor'
    ], JSON_UNESCAPED_UNICODE);
}

exit;