<?php
// app/models/Categoria.php

class Categoria {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // 🔹 Obtener todas las categorías activas
    public function listar() {
        $sql = "SELECT c.*, 
                (SELECT COUNT(*) FROM productos p WHERE p.categoria_id = c.id AND p.activo = 1) as productos_count
                FROM categorias c 
                WHERE c.activa = 1 
                ORDER BY c.nombre";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 🔹 Obtener categoría por ID
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM categorias WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // 🔹 Crear nueva categoría
    public function crear($datos) {
        $sql = "INSERT INTO categorias (nombre, descripcion) VALUES (:nombre, :descripcion)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($datos);
    }
    
    // 🔹 Actualizar categoría
    public function actualizar($id, $datos) {
        $sql = "UPDATE categorias SET nombre = :nombre, descripcion = :descripcion, activa = :activa WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $datos['id'] = $id;
        return $stmt->execute($datos);
    }
}
?>