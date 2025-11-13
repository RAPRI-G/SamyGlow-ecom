<?php
// test-completo.php
session_start();
require_once 'config/database.php';
require_once 'app/models/MetodoPago.php';

echo "<h1>Test Completo de Métodos de Pago</h1>";

try {
    $model = new MetodoPago($pdo);
    
    echo "<h2>1. Crear método de prueba:</h2>";
    $nuevoMetodo = [
        'nombre' => 'Método de Prueba',
        'tipo' => 'digital',
        'descripcion' => 'Este es un método de prueba',
        'icono' => 'fas fa-wallet',
        'activo' => 1
    ];
    
    $resultado = $model->crear($nuevoMetodo);
    echo "Resultado: " . ($resultado ? "Éxito" : "Error") . "<br>";
    
    echo "<h2>2. Listar métodos después de crear:</h2>";
    $metodos = $model->obtenerTodos();
    echo "<pre>" . print_r($metodos, true) . "</pre>";
    
    echo "<h2>3. Actualizar método:</h2>";
    $ultimoId = end($metodos)['id'];
    $datosActualizar = [
        'nombre' => 'Método Actualizado',
        'tipo' => 'card',
        'descripcion' => 'Descripción actualizada',
        'icono' => 'far fa-credit-card',
        'activo' => 1
    ];
    
    $resultado = $model->actualizar($ultimoId, $datosActualizar);
    echo "Resultado: " . ($resultado ? "Éxito" : "Error") . "<br>";
    
    echo "<h2>4. Estadísticas actualizadas:</h2>";
    $estadisticas = $model->obtenerEstadisticas();
    echo "<pre>" . print_r($estadisticas, true) . "</pre>";
    
    echo "<h2>5. Configuración:</h2>";
    $config = $model->obtenerConfiguracion();
    echo "<pre>" . print_r($config, true) . "</pre>";
    
} catch (Exception $e) {
    echo "<h2>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>