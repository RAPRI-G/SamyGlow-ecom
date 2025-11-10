<?php
// app/models/Producto.php

class Producto {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // 🔹 Obtener todos los productos activos
    public function listar() {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM productos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.activo = 1 
                ORDER BY p.nombre";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 🔹 Buscar productos
    public function buscar($termino) {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM productos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                WHERE (p.nombre LIKE :termino OR p.descripcion LIKE :termino) 
                AND p.activo = 1 
                ORDER BY p.nombre";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['termino' => "%$termino%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 🔹 Obtener productos por categoría
    public function listarPorCategoria($categoria_id) {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM productos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.categoria_id = :categoria_id 
                AND p.activo = 1 
                ORDER BY p.nombre";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['categoria_id' => $categoria_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 🔹 Obtener productos con stock bajo
    public function productosStockBajo($limite = 5) {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM productos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.stock <= :limite 
                AND p.activo = 1 
                ORDER BY p.stock ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['limite' => $limite]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 🔹 Obtener producto por ID
    public function obtenerPorId($id) {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM productos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // 🔹 Crear nuevo producto
    public function crear($datos) {
        $sql = "INSERT INTO productos (nombre, descripcion, precio, stock, categoria_id, activo) 
                VALUES (:nombre, :descripcion, :precio, :stock, :categoria_id, :activo)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($datos);
    }
    
    // 🔹 Actualizar producto
    public function actualizar($id, $datos) {
        $sql = "UPDATE productos 
                SET nombre = :nombre, descripcion = :descripcion, precio = :precio, 
                    stock = :stock, categoria_id = :categoria_id, activo = :activo 
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $datos['id'] = $id;
        return $stmt->execute($datos);
    }
    
    // 🔹 Eliminar producto (soft delete)
    public function eliminar($id) {
        $sql = "UPDATE productos SET activo = 0 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    // 🔹 Actualizar stock
    public function actualizarStock($id, $nuevo_stock) {
        $sql = "UPDATE productos SET stock = :stock WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id, 'stock' => $nuevo_stock]);
    }
    
    // 🔹 Obtener estadísticas de productos
    public function obtenerEstadisticas() {
        $sql = "SELECT 
                COUNT(*) as total_productos,
                SUM(stock) as total_stock,
                SUM(CASE WHEN stock <= 5 THEN 1 ELSE 0 END) as stock_bajo,
                SUM(precio * stock) as valor_inventario
                FROM productos 
                WHERE activo = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>