<?php
// app/models/Promocion.php
class Promocion
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // 🔹 LISTAR TODAS LAS PROMOCIONES
    public function listar()
    {
        $sql = "SELECT p.*, 
                       COUNT(pp.producto_id) as total_productos,
                       (SELECT COUNT(*) FROM pedidos WHERE promocion_id = p.id) as usos_actual
                FROM promociones p
                LEFT JOIN productos_promocion pp ON p.id = pp.promocion_id
                GROUP BY p.id
                ORDER BY p.fecha_inicio DESC, p.fecha_fin DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 OBTENER PROMOCIÓN POR ID
    public function obtener($id)
    {
        $sql = "SELECT p.*, 
                       COUNT(pp.producto_id) as total_productos,
                       (SELECT COUNT(*) FROM pedidos WHERE promocion_id = p.id) as usos_actual
                FROM promociones p
                LEFT JOIN productos_promocion pp ON p.id = pp.promocion_id
                WHERE p.id = ?
                GROUP BY p.id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);

        $promocion = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($promocion) {
            // Obtener productos de la promoción
            $sqlProductos = "SELECT p.*, c.nombre as categoria_nombre
                            FROM productos p
                            INNER JOIN productos_promocion pp ON p.id = pp.producto_id
                            LEFT JOIN categorias c ON p.categoria_id = c.id
                            WHERE pp.promocion_id = ?";
            
            $stmtProductos = $this->pdo->prepare($sqlProductos);
            $stmtProductos->execute([$id]);
            $promocion['productos'] = $stmtProductos->fetchAll(PDO::FETCH_ASSOC);
        }

        return $promocion;
    }

    // 🔹 REGISTRAR NUEVA PROMOCIÓN
    public function registrar($datos, $productos = [])
    {
        try {
            $this->pdo->beginTransaction();

            // Insertar promoción
            $sql = "INSERT INTO promociones (nombre, descripcion, tipo, valor_descuento, fecha_inicio, fecha_fin, activa, max_usos) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $datos['nombre'],
                $datos['descripcion'],
                $datos['tipo'],
                $datos['valor_descuento'],
                $datos['fecha_inicio'],
                $datos['fecha_fin'],
                $datos['activa'],
                $datos['max_usos']
            ]);

            $promocionId = $this->pdo->lastInsertId();

            // Insertar productos de la promoción
            if (!empty($productos)) {
                $sqlProductos = "INSERT INTO productos_promocion (promocion_id, producto_id) VALUES (?, ?)";
                $stmtProductos = $this->pdo->prepare($sqlProductos);

                foreach ($productos as $productoId) {
                    $stmtProductos->execute([$promocionId, $productoId]);
                }
            }

            $this->pdo->commit();
            return $promocionId;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    // 🔹 ACTUALIZAR PROMOCIÓN
    public function actualizar($id, $datos, $productos = [])
    {
        try {
            $this->pdo->beginTransaction();

            // Actualizar promoción
            $sql = "UPDATE promociones 
                    SET nombre = ?, descripcion = ?, tipo = ?, valor_descuento = ?, 
                        fecha_inicio = ?, fecha_fin = ?, activa = ?, max_usos = ?
                    WHERE id = ?";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $datos['nombre'],
                $datos['descripcion'],
                $datos['tipo'],
                $datos['valor_descuento'],
                $datos['fecha_inicio'],
                $datos['fecha_fin'],
                $datos['activa'],
                $datos['max_usos'],
                $id
            ]);

            // Eliminar productos actuales y agregar nuevos
            $sqlEliminar = "DELETE FROM productos_promocion WHERE promocion_id = ?";
            $stmtEliminar = $this->pdo->prepare($sqlEliminar);
            $stmtEliminar->execute([$id]);

            // Insertar nuevos productos
            if (!empty($productos)) {
                $sqlInsertar = "INSERT INTO productos_promocion (promocion_id, producto_id) VALUES (?, ?)";
                $stmtInsertar = $this->pdo->prepare($sqlInsertar);

                foreach ($productos as $productoId) {
                    $stmtInsertar->execute([$id, $productoId]);
                }
            }

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    // 🔹 ELIMINAR PROMOCIÓN
    public function eliminar($id)
    {
        try {
            $this->pdo->beginTransaction();

            // Verificar si la promoción ha sido usada
            $sqlUsos = "SELECT COUNT(*) as total FROM pedidos WHERE promocion_id = ?";
            $stmtUsos = $this->pdo->prepare($sqlUsos);
            $stmtUsos->execute([$id]);
            $usos = $stmtUsos->fetch(PDO::FETCH_ASSOC);

            if ($usos['total'] > 0) {
                throw new Exception('No se puede eliminar una promoción que ya ha sido utilizada');
            }

            // Eliminar productos de la promoción
            $sqlProductos = "DELETE FROM productos_promocion WHERE promocion_id = ?";
            $stmtProductos = $this->pdo->prepare($sqlProductos);
            $stmtProductos->execute([$id]);

            // Eliminar promoción
            $sqlPromocion = "DELETE FROM promociones WHERE id = ?";
            $stmtPromocion = $this->pdo->prepare($sqlPromocion);
            $stmtPromocion->execute([$id]);

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    // 🔹 OBTENER PROMOCIONES ACTIVAS
    public function obtenerActivas()
    {
        $sql = "SELECT p.*, 
                       COUNT(pp.producto_id) as total_productos,
                       (SELECT COUNT(*) FROM pedidos WHERE promocion_id = p.id) as usos_actual
                FROM promociones p
                LEFT JOIN productos_promocion pp ON p.id = pp.promocion_id
                WHERE p.activa = 1 
                AND CURDATE() BETWEEN p.fecha_inicio AND p.fecha_fin
                GROUP BY p.id
                ORDER BY p.fecha_inicio DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 OBTENER PRODUCTOS EN PROMOCIÓN
    public function obtenerProductosEnPromocion()
    {
        $sql = "SELECT DISTINCT p.*, c.nombre as categoria_nombre
                FROM productos p
                INNER JOIN productos_promocion pp ON p.id = pp.producto_id
                INNER JOIN promociones pr ON pp.promocion_id = pr.id
                LEFT JOIN categorias c ON p.categoria_id = c.id
                WHERE pr.activa = 1 
                AND CURDATE() BETWEEN pr.fecha_inicio AND pr.fecha_fin
                ORDER BY p.nombre";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 OBTENER ESTADÍSTICAS DE PROMOCIONES
    public function obtenerEstadisticas()
    {
        $sql = "SELECT 
                COUNT(*) as total_promociones,
                SUM(CASE WHEN activa = 1 AND CURDATE() BETWEEN fecha_inicio AND fecha_fin THEN 1 ELSE 0 END) as promociones_activas,
                SUM(CASE WHEN CURDATE() > fecha_fin THEN 1 ELSE 0 END) as promociones_expiradas,
                COALESCE(SUM(usos_actual), 0) as total_usos
                FROM promociones";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔹 BUSCAR PROMOCIONES
    public function buscar($termino)
    {
        $sql = "SELECT p.*, 
                       COUNT(pp.producto_id) as total_productos,
                       (SELECT COUNT(*) FROM pedidos WHERE promocion_id = p.id) as usos_actual
                FROM promociones p
                LEFT JOIN productos_promocion pp ON p.id = pp.promocion_id
                WHERE p.nombre LIKE ? OR p.descripcion LIKE ?
                GROUP BY p.id
                ORDER BY p.fecha_inicio DESC";

        $stmt = $this->pdo->prepare($sql);
        $termino = "%$termino%";
        $stmt->execute([$termino, $termino]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 FILTRAR PROMOCIONES POR TIPO
    public function filtrarPorTipo($tipo)
    {
        $sql = "SELECT p.*, 
                       COUNT(pp.producto_id) as total_productos,
                       (SELECT COUNT(*) FROM pedidos WHERE promocion_id = p.id) as usos_actual
                FROM promociones p
                LEFT JOIN productos_promocion pp ON p.id = pp.promocion_id
                WHERE p.tipo = ?
                GROUP BY p.id
                ORDER BY p.fecha_inicio DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$tipo]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>