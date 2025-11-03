<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

class ProductoModel
{
    /** @var PDO */
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Retorna todos los productos activos con datos de categoría y posible promoción activa.
     * @return array
     */
    public function obtenerProductos(): array
    {
        try {
            $sql = "
                SELECT
                    p.id,
                    p.nombre,
                    p.descripcion,
                    p.precio,
                    p.stock,
                    p.imagen,
                    p.categoria_id,
                    p.activo,
                    c.nombre AS categoria,
                    pr.id AS promocion_id,
                    pr.tipo AS tipo_descuento,
                    pr.valor_descuento,
                    pr.fecha_inicio,
                    pr.fecha_fin,
                    pr.activa AS promocion_activa
                FROM productos p
                LEFT JOIN categorias c ON c.id = p.categoria_id
                LEFT JOIN productos_promocion pp ON pp.producto_id = p.id
                LEFT JOIN promociones pr ON pr.id = pp.promocion_id
                    AND pr.activa = 1
                    AND CURDATE() BETWEEN pr.fecha_inicio AND pr.fecha_fin
                WHERE p.activo = 1
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Evitar duplicados si un producto tiene varias filas (por múltiples promociones).
            $productos = [];
            foreach ($rows as $row) {
                $id = (int)$row['id'];
                if (!isset($productos[$id])) {
                    $precio = (float)$row['precio'];

                    $producto = [
                        'id' => $id,
                        'nombre' => $row['nombre'],
                        'descripcion' => $row['descripcion'],
                        'precio' => number_format($precio, 2, '.', ''),
                        'stock' => (int)$row['stock'],
                        'imagen' => $row['imagen'] ?? '',
                        'categoria' => $row['categoria'] ?? '',
                        'descuento' => 0,
                        'tipo_descuento' => null,
                        'precio_final' => number_format($precio, 2, '.', ''),
                    ];

                    // Si hay una promoción activa en la fila, calcular precio final
                    if (!empty($row['promocion_id'])) {
                        $tipo = $row['tipo_descuento'];
                        $valor = (float)$row['valor_descuento'];

                        if ($tipo === 'descuento_porcentaje' && $valor > 0) {
                            $precio_final = $precio - ($precio * $valor / 100);
                            $producto['descuento'] = (int)$valor;
                            $producto['tipo_descuento'] = $tipo;
                            $producto['precio_final'] = number_format(max(0, $precio_final), 2, '.', '');
                        } elseif ($tipo === 'descuento_monto' && $valor > 0) {
                            $precio_final = $precio - $valor;
                            $producto['descuento'] = number_format($valor, 2, '.', '');
                            $producto['tipo_descuento'] = $tipo;
                            $producto['precio_final'] = number_format(max(0, $precio_final), 2, '.', '');
                        } else {
                            // Otros tipos (combo, envio_gratis) - no modificar precio aquí
                        }
                    }

                    $productos[$id] = $producto;
                }
            }

            // Reindexar array
            return array_values($productos);
        } catch (PDOException $e) {
            error_log('ProductoModel::obtenerProductos error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Retorna un producto por su id (si está activo). Retorna null si no existe.
     * @param int $id
     * @return array|null
     */
    public function obtenerProductoPorId(int $id): ?array
    {
        try {
            $sql = "
                SELECT
                    p.id,
                    p.nombre,
                    p.descripcion,
                    p.precio,
                    p.stock,
                    p.imagen,
                    c.nombre AS categoria,
                    pr.id AS promocion_id,
                    pr.tipo AS tipo_descuento,
                    pr.valor_descuento,
                    pr.fecha_inicio,
                    pr.fecha_fin,
                    pr.activa AS promocion_activa
                FROM productos p
                LEFT JOIN categorias c ON c.id = p.categoria_id
                LEFT JOIN productos_promocion pp ON pp.producto_id = p.id
                LEFT JOIN promociones pr ON pr.id = pp.promocion_id
                    AND pr.activa = 1
                    AND CURDATE() BETWEEN pr.fecha_inicio AND pr.fecha_fin
                WHERE p.activo = 1
                  AND p.id = :id
                LIMIT 1
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                return null;
            }

            $precio = (float)$row['precio'];
            $producto = [
                'id' => (int)$row['id'],
                'nombre' => $row['nombre'],
                'descripcion' => $row['descripcion'],
                'precio' => number_format($precio, 2, '.', ''),
                'stock' => (int)$row['stock'],
                'imagen' => $row['imagen'] ?? '',
                'categoria' => $row['categoria'] ?? '',
                'descuento' => 0,
                'tipo_descuento' => null,
                'precio_final' => number_format($precio, 2, '.', ''),
            ];

            if (!empty($row['promocion_id'])) {
                $tipo = $row['tipo_descuento'];
                $valor = (float)$row['valor_descuento'];

                if ($tipo === 'descuento_porcentaje' && $valor > 0) {
                    $precio_final = $precio - ($precio * $valor / 100);
                    $producto['descuento'] = (int)$valor;
                    $producto['tipo_descuento'] = $tipo;
                    $producto['precio_final'] = number_format(max(0, $precio_final), 2, '.', '');
                } elseif ($tipo === 'descuento_monto' && $valor > 0) {
                    $precio_final = $precio - $valor;
                    $producto['descuento'] = number_format($valor, 2, '.', '');
                    $producto['tipo_descuento'] = $tipo;
                    $producto['precio_final'] = number_format(max(0, $precio_final), 2, '.', '');
                }
            }

            return $producto;
        } catch (PDOException $e) {
            error_log('ProductoModel::obtenerProductoPorId error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verifica si hay stock suficiente para un producto.
     * @param int $productoId
     * @param int $cantidad
     * @return bool
     */
    public function verificarStock(int $productoId, int $cantidad): bool
    {
        try {
            $sql = 'SELECT stock FROM productos WHERE id = :id AND activo = 1 LIMIT 1';
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $productoId, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) return false;
            return ((int)$row['stock'] >= $cantidad);
        } catch (PDOException $e) {
            error_log('ProductoModel::verificarStock error: ' . $e->getMessage());
            return false;
        }
    }
}

?>
