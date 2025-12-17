<?php
// app/models/Producto.php

class Producto
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // 🔹 Obtener todos los productos activos
    public function listar()
    {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
            FROM productos p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            WHERE p.eliminado = 0 
            ORDER BY p.nombre";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 Buscar productos
    public function buscar($termino)
    {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
            FROM productos p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            WHERE (p.nombre LIKE :termino OR p.descripcion LIKE :termino) 
            AND p.eliminado = 0 
            ORDER BY p.nombre";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['termino' => "%$termino%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 Obtener productos por categoría
    public function listarPorCategoria($categoria_id)
    {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
            FROM productos p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            WHERE p.categoria_id = :categoria_id 
            AND p.eliminado = 0 
            ORDER BY p.nombre";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['categoria_id' => $categoria_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 Obtener productos con stock bajo
    public function productosStockBajo($limite = 5)
    {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
            FROM productos p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            WHERE p.stock <= :limite 
            AND p.eliminado = 0 
            ORDER BY p.stock ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['limite' => $limite]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerUrlImagen($rutaImagen)
{
    if (empty($rutaImagen)) {
        return null;
    }
    
    // DEBUG: Ver qué contiene realmente
    error_log("DEBUG obtenerUrlImagen - Ruta original: " . $rutaImagen);
    
    // 🔴 ELIMINAR 'public/' si existe en la ruta
    $rutaImagen = str_replace('public/', '', $rutaImagen);
    
    // 🔴 ELIMINAR 'SamyGlow-ecom/' si existe
    $rutaImagen = str_replace('SamyGlow-ecom/', '', $rutaImagen);
    
    // Asegurar que empiece con 'uploads/'
    if (!str_starts_with($rutaImagen, 'uploads/')) {
        $rutaImagen = 'uploads/productos/' . $rutaImagen;
    }
    
    // Asegurar que NO tenga slash inicial
    $rutaImagen = ltrim($rutaImagen, '/');
    
    $urlFinal = '/' . $rutaImagen;
    error_log("DEBUG obtenerUrlImagen - URL final: " . $urlFinal);
    
    return $urlFinal;
}

// 🔹 Método para obtener ruta física del archivo
public function obtenerRutaFisica($rutaImagen)
{
    if (empty($rutaImagen)) {
        return null;
    }
    
    // 🔴 LIMPIAR LA RUTA primero
    $rutaImagen = str_replace('public/', '', $rutaImagen);
    $rutaImagen = str_replace('SamyGlow-ecom/', '', $rutaImagen);
    $rutaImagen = ltrim($rutaImagen, '/');
    
    // Base path corregido
    $basePath = $_SERVER['DOCUMENT_ROOT'] . '/SamyGlow-ecom/';
    
    if (str_starts_with($rutaImagen, 'uploads/productos/')) {
        return $basePath . $rutaImagen;
    } elseif (str_starts_with($rutaImagen, 'uploads/')) {
        return $basePath . $rutaImagen;
    } else {
        return $basePath . 'uploads/productos/' . $rutaImagen;
    }
}

    // 🔹 Obtener producto por ID
    public function obtenerPorId($id)
{
    $sql = "SELECT p.*, c.nombre as categoria_nombre 
            FROM productos p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            WHERE p.id = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($producto && !empty($producto['imagen'])) {
        // Corregir la ruta de la imagen si es necesario
        $producto['imagen_corregida'] = $this->corregirRutaImagen($producto['imagen']);
    }
    
    return $producto;
}

private function corregirRutaImagen($rutaImagen)
{
    if (empty($rutaImagen)) {
        return null;
    }
    
    // Eliminar 'public/' si existe
    $rutaImagen = str_replace('public/', '', $rutaImagen);
    
    // Eliminar 'SamyGlow-ecom/' si existe
    $rutaImagen = str_replace('SamyGlow-ecom/', '', $rutaImagen);
    
    // Si no empieza con 'uploads/productos/', agregarlo
    if (!str_starts_with($rutaImagen, 'uploads/productos/')) {
        // Si ya empieza con 'uploads/', dejarlo así
        if (!str_starts_with($rutaImagen, 'uploads/')) {
            $rutaImagen = 'uploads/productos/' . $rutaImagen;
        }
    }
    
    // Asegurar que no tenga slash inicial
    $rutaImagen = ltrim($rutaImagen, '/');
    
    return '/' . $rutaImagen;
}

    // 🔹 Crear nuevo producto (ACTUALIZADO para imágenes)
    public function crear($datos)
    {
        $sql = "INSERT INTO productos (nombre, descripcion, precio, stock, imagen, categoria_id, activo) 
            VALUES (:nombre, :descripcion, :precio, :stock, :imagen, :categoria_id, :activo)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($datos);
    }

    // 🔹 Actualizar producto
    // 🔹 Actualizar producto (ACTUALIZADO para imagen)
    public function actualizar($id, $datos)
    {
        $sql = "UPDATE productos 
            SET nombre = :nombre, descripcion = :descripcion, precio = :precio, 
                stock = :stock, imagen = :imagen, categoria_id = :categoria_id, activo = :activo 
            WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $datos['id'] = $id;
        return $stmt->execute($datos);
    }

    // 🔹 Eliminar producto (soft delete)
    public function eliminar($id)
    {
        $sql = "UPDATE productos SET eliminado = 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // 🔹 Actualizar stock
    public function actualizarStock($id, $nuevo_stock)
    {
        $sql = "UPDATE productos SET stock = :stock WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id, 'stock' => $nuevo_stock]);
    }

    // 🔹 Obtener productos eliminados (en papelera)
    public function listarEliminados()
    {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
            FROM productos p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            WHERE p.eliminado = 1 
            ORDER BY p.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 Restaurar desde papelera
    public function restaurar($id)
    {
        $sql = "UPDATE productos SET eliminado = 0 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // 🔹 Eliminar permanentemente
    public function eliminarPermanentemente($id)
    {
        $sql = "DELETE FROM productos WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // 🔹 Vaciar papelera
    public function vaciarPapelera()
    {
        $sql = "DELETE FROM productos WHERE eliminado = 1";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute();
    }

    // 🔹 Contar productos en papelera
    public function contarPapelera()
    {
        $sql = "SELECT COUNT(*) as total FROM productos WHERE eliminado = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // 🔹 Obtener estadísticas de productos (MODIFICADO)
    public function obtenerEstadisticas()
    {
        $sql = "SELECT 
            COUNT(*) as total_productos,
            SUM(stock) as total_stock,
            SUM(CASE WHEN stock <= 5 THEN 1 ELSE 0 END) as stock_bajo,
            SUM(precio * stock) as valor_inventario
            FROM productos 
            WHERE eliminado = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
