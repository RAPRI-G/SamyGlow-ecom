<?php
// app/models/Promocion.php - VERSIÓN CORREGIDA
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
                WHERE p.fecha_eliminado IS NULL
                GROUP BY p.id
                ORDER BY p.fecha_inicio DESC, p.fecha_fin DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 OBTENER PRODUCTOS EN OTRAS PROMOCIONES ACTIVAS
    public function obtenerProductosEnPromocionesActivas($excluirPromocionId = null)
    {
        $sql = "SELECT DISTINCT pp.producto_id, p.nombre as producto_nombre
            FROM productos_promocion pp
            INNER JOIN promociones pr ON pp.promocion_id = pr.id
            INNER JOIN productos p ON pp.producto_id = p.id
            WHERE pr.activa = 1 
            AND pr.fecha_eliminado IS NULL
            AND CURDATE() BETWEEN pr.fecha_inicio AND pr.fecha_fin";

        $params = [];

        if ($excluirPromocionId) {
            $sql .= " AND pp.promocion_id != ?";
            $params[] = $excluirPromocionId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 OBTENER PRODUCTOS DE UNA PROMOCIÓN ESPECÍFICA
    public function obtenerProductosDePromocion($promocionId)
    {
        $sql = "SELECT p.*, c.nombre as categoria_nombre
            FROM productos p
            INNER JOIN productos_promocion pp ON p.id = pp.producto_id
            LEFT JOIN categorias c ON p.categoria_id = c.id
            WHERE pp.promocion_id = ?
            AND p.eliminado = 0
            ORDER BY p.nombre";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$promocionId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 VERIFICAR SI UN PRODUCTO ESTÁ EN ESTA PROMOCIÓN
    public function productoEnEstaPromocion($productoId, $promocionId)
    {
        $sql = "SELECT COUNT(*) as total 
            FROM productos_promocion 
            WHERE producto_id = ? AND promocion_id = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$productoId, $promocionId]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }

    // 🔹 OBTENER PROMOCIÓN DONDE ESTÁ UN PRODUCTO
    public function obtenerPromocionDeProducto($productoId)
    {
        $sql = "SELECT pr.id, pr.nombre as promocion_nombre, pr.tipo
            FROM promociones pr
            INNER JOIN productos_promocion pp ON pr.id = pp.promocion_id
            WHERE pp.producto_id = ?
            AND pr.activa = 1
            AND pr.fecha_eliminado IS NULL
            AND CURDATE() BETWEEN pr.fecha_inicio AND pr.fecha_fin
            LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$productoId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔹 OBTENER TODOS LOS PRODUCTOS CON SU ESTADO DE PROMOCIÓN
    public function obtenerProductosConEstadoPromocion($excluirPromocionId = null)
    {
        $sql = "SELECT p.*, 
                   c.nombre as categoria_nombre,
                   GROUP_CONCAT(DISTINCT pr.id) as promociones_ids,
                   GROUP_CONCAT(DISTINCT pr.nombre) as promociones_nombres,
                   COUNT(DISTINCT pr.id) as total_promociones_activas
            FROM productos p
            LEFT JOIN categorias c ON p.categoria_id = c.id
            LEFT JOIN productos_promocion pp ON p.id = pp.producto_id
            LEFT JOIN promociones pr ON pp.promocion_id = pr.id 
                AND pr.activa = 1 
                AND pr.fecha_eliminado IS NULL
                AND CURDATE() BETWEEN pr.fecha_inicio AND pr.fecha_fin";

        if ($excluirPromocionId) {
            $sql .= " AND (pr.id IS NULL OR pr.id = ?)";
        }

        $sql .= " WHERE p.eliminado = 0
              GROUP BY p.id
              ORDER BY p.nombre";

        $stmt = $this->pdo->prepare($sql);

        if ($excluirPromocionId) {
            $stmt->execute([$excluirPromocionId]);
        } else {
            $stmt->execute();
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 OBTENER TODOS LOS PRODUCTOS CON SU ESTADO (EN QUÉ PROMOCIÓN ESTÁN)
    public function obtenerTodosProductosConEstado($promocionIdActual = null)
    {
        $sql = "SELECT 
                p.*,
                c.nombre as categoria_nombre,
                -- Verificar si está en ESTA promoción
                CASE 
                    WHEN pp_actual.promocion_id IS NOT NULL THEN 'en_esta_promocion'
                    WHEN pp_otras.promocion_id IS NOT NULL THEN 'en_otra_promocion'
                    ELSE 'disponible'
                END as estado_promocion,
                -- Nombre de la promoción donde está (si está en otra)
                pr_otras.nombre as otra_promocion_nombre,
                -- ID de la promoción donde está (si está en otra)
                pp_otras.promocion_id as otra_promocion_id
            FROM productos p
            LEFT JOIN categorias c ON p.categoria_id = c.id
            -- Verificar si está en ESTA promoción (si se proporciona promocionIdActual)
            LEFT JOIN productos_promocion pp_actual ON p.id = pp_actual.producto_id 
                AND pp_actual.promocion_id = ?
            -- Verificar si está en OTRAS promociones ACTIVAS
            LEFT JOIN productos_promocion pp_otras ON p.id = pp_otras.producto_id 
                AND pp_otras.promocion_id != COALESCE(?, 0)
            LEFT JOIN promociones pr_otras ON pp_otras.promocion_id = pr_otras.id
                AND pr_otras.activa = 1
                AND pr_otras.fecha_eliminado IS NULL
                AND CURDATE() BETWEEN pr_otras.fecha_inicio AND pr_otras.fecha_fin
            WHERE p.eliminado = 0
            GROUP BY p.id
            ORDER BY 
                -- Primero los que están en esta promoción
                CASE WHEN pp_actual.promocion_id IS NOT NULL THEN 0 ELSE 1 END,
                -- Luego los disponibles
                CASE WHEN pp_otras.promocion_id IS NOT NULL THEN 2 ELSE 1 END,
                p.nombre";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$promocionIdActual, $promocionIdActual]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 VERIFICAR SI UN PRODUCTO PUEDE SER SELECCIONADO
    public function productoPuedeSeleccionarse($productoId, $promocionIdActual = null)
    {
        $sql = "SELECT 
                CASE 
                    -- Si está en esta promoción, SÍ puede seleccionarse (ya está seleccionado)
                    WHEN pp_actual.promocion_id IS NOT NULL THEN 1
                    -- Si está en otra promoción activa, NO puede seleccionarse
                    WHEN pp_otras.promocion_id IS NOT NULL THEN 0
                    -- Si no está en ninguna, SÍ puede seleccionarse
                    ELSE 1
                END as puede_seleccionarse,
                pr_otras.nombre as otra_promocion_nombre
            FROM productos p
            LEFT JOIN productos_promocion pp_actual ON p.id = pp_actual.producto_id 
                AND pp_actual.promocion_id = ?
            LEFT JOIN productos_promocion pp_otras ON p.id = pp_otras.producto_id 
                AND pp_otras.promocion_id != COALESCE(?, 0)
            LEFT JOIN promociones pr_otras ON pp_otras.promocion_id = pr_otras.id
                AND pr_otras.activa = 1
                AND pr_otras.fecha_eliminado IS NULL
                AND CURDATE() BETWEEN pr_otras.fecha_inicio AND pr_otras.fecha_fin
            WHERE p.id = ?
            GROUP BY p.id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$promocionIdActual, $promocionIdActual, $productoId]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? [
            'puede_seleccionarse' => $result['puede_seleccionarse'] == 1,
            'otra_promocion_nombre' => $result['otra_promocion_nombre']
        ] : ['puede_seleccionarse' => true, 'otra_promocion_nombre' => null];
    }

    public function obtenerProductosDisponibles($excluirPromocionId = null)
    {
        // Primero obtener productos usados en otras promociones activas
        $productosUsados = $this->obtenerProductosEnPromocionesActivas($excluirPromocionId);
        $productosUsadosIds = array_column($productosUsados, 'producto_id');

        // Si no hay productos usados, devolver todos
        if (empty($productosUsadosIds)) {
            $sql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM productos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.eliminado = 0 
                ORDER BY p.nombre";
        } else {
            // Excluir productos ya usados
            $placeholders = str_repeat('?,', count($productosUsadosIds) - 1) . '?';
            $sql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM productos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.eliminado = 0 
                AND p.id NOT IN ($placeholders)
                ORDER BY p.nombre";
        }

        $stmt = $this->pdo->prepare($sql);

        if (!empty($productosUsadosIds)) {
            $stmt->execute($productosUsadosIds);
        } else {
            $stmt->execute();
        }

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
    // app/models/Promocion.php - VERIFICA EL MÉTODO actualizar()

    public function actualizar($id, $datos, $productos = [])
    {
        try {
            error_log("🔄 Iniciando actualización de promoción ID: $id");
            error_log("📝 Datos: " . print_r($datos, true));
            error_log("📦 Productos: " . print_r($productos, true));

            $this->pdo->beginTransaction();

            // Actualizar promoción
            $sql = "UPDATE promociones 
                SET nombre = ?, descripcion = ?, tipo = ?, valor_descuento = ?, 
                    fecha_inicio = ?, fecha_fin = ?, activa = ?, max_usos = ?
                WHERE id = ?";

            $stmt = $this->pdo->prepare($sql);
            $resultado = $stmt->execute([
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

            error_log("✅ Resultado update promoción: " . ($resultado ? 'true' : 'false'));
            error_log("📊 Filas afectadas: " . $stmt->rowCount());

            // Eliminar productos actuales de la promoción
            $sqlEliminar = "DELETE FROM productos_promocion WHERE promocion_id = ?";
            $stmtEliminar = $this->pdo->prepare($sqlEliminar);
            $stmtEliminar->execute([$id]);

            error_log("🗑️ Productos eliminados: " . $stmtEliminar->rowCount());

            // Insertar nuevos productos
            if (!empty($productos)) {
                $sqlInsertar = "INSERT INTO productos_promocion (promocion_id, producto_id) VALUES (?, ?)";
                $stmtInsertar = $this->pdo->prepare($sqlInsertar);

                $productosInsertados = 0;
                foreach ($productos as $productoId) {
                    $stmtInsertar->execute([$id, $productoId]);
                    $productosInsertados++;
                }

                error_log("📥 Productos insertados: $productosInsertados");
            } else {
                error_log("ℹ️ No hay productos para insertar");
            }

            $this->pdo->commit();

            error_log("🎉 Actualización completada exitosamente");
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("❌ ERROR en Promocion::actualizar(): " . $e->getMessage());
            error_log("📝 Trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    // 🔹 ELIMINAR PROMOCIÓN
    public function eliminar($id)
    {
        try {
            $this->pdo->beginTransaction();

            $sqlUsos = "SELECT COUNT(*) as total FROM pedidos WHERE promocion_id = ?";
            $stmtUsos = $this->pdo->prepare($sqlUsos);
            $stmtUsos->execute([$id]);
            $usos = $stmtUsos->fetch(PDO::FETCH_ASSOC);

            if ($usos['total'] > 0) {
                throw new Exception('No se puede eliminar una promoción que ya ha sido utilizada');
            }

            $sqlProductos = "DELETE FROM productos_promocion WHERE promocion_id = ?";
            $stmtProductos = $this->pdo->prepare($sqlProductos);
            $stmtProductos->execute([$id]);

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
                AND p.fecha_eliminado IS NULL
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
                AND pr.fecha_eliminado IS NULL
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
                    SUM(CASE WHEN activa = 1 AND fecha_eliminado IS NULL AND CURDATE() BETWEEN fecha_inicio AND fecha_fin THEN 1 ELSE 0 END) as promociones_activas,
                    SUM(CASE WHEN fecha_eliminado IS NULL AND CURDATE() > fecha_fin THEN 1 ELSE 0 END) as promociones_expiradas,
                    COALESCE(SUM(usos_actual), 0) as total_usos
                FROM promociones
                WHERE fecha_eliminado IS NULL";

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
                WHERE (p.nombre LIKE ? OR p.descripcion LIKE ?)
                AND p.fecha_eliminado IS NULL
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
                AND p.fecha_eliminado IS NULL
                GROUP BY p.id
                ORDER BY p.fecha_inicio DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$tipo]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 PAPELERA: MOVER PROMOCIÓN A PAPELERA
    public function moverPapelera($id)
    {
        try {
            $sql = "UPDATE promociones SET activa = 0, fecha_eliminado = NOW() WHERE id = ? AND fecha_eliminado IS NULL";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            throw $e;
        }
    }

    // 🔹 PAPELERA: RESTAURAR PROMOCIÓN
    public function restaurar($id)
    {
        try {
            $sql = "UPDATE promociones SET activa = 1, fecha_eliminado = NULL WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            throw $e;
        }
    }

    // 🔹 PAPELERA: ELIMINAR PERMANENTEMENTE
    public function eliminarPermanentemente($id)
    {
        try {
            $this->pdo->beginTransaction();

            $sqlUsos = "SELECT COUNT(*) as total FROM pedidos WHERE promocion_id = ?";
            $stmtUsos = $this->pdo->prepare($sqlUsos);
            $stmtUsos->execute([$id]);
            $usos = $stmtUsos->fetch(PDO::FETCH_ASSOC);

            if ($usos['total'] > 0) {
                throw new Exception('No se puede eliminar permanentemente una promoción que ya ha sido utilizada');
            }

            $sqlProductos = "DELETE FROM productos_promocion WHERE promocion_id = ?";
            $stmtProductos = $this->pdo->prepare($sqlProductos);
            $stmtProductos->execute([$id]);

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

    // 🔹 PAPELERA: LISTAR PROMOCIONES ELIMINADAS
    public function listarEliminadas()
    {
        $sql = "SELECT p.*, 
                       COUNT(pp.producto_id) as total_productos,
                       (SELECT COUNT(*) FROM pedidos WHERE promocion_id = p.id) as usos_actual
                FROM promociones p
                LEFT JOIN productos_promocion pp ON p.id = pp.promocion_id
                WHERE p.fecha_eliminado IS NOT NULL
                GROUP BY p.id
                ORDER BY p.fecha_eliminado DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 PAPELERA: CONTAR PROMOCIONES EN PAPELERA
    public function contarPapelera()
    {
        $sql = "SELECT COUNT(*) as total FROM promociones WHERE fecha_eliminado IS NOT NULL";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // 🔹 PAPELERA: VACIAR PAPELERA
    public function vaciarPapelera()
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "SELECT p.id 
                    FROM promociones p 
                    LEFT JOIN pedidos pd ON p.id = pd.promocion_id 
                    WHERE p.fecha_eliminado IS NOT NULL 
                    AND pd.promocion_id IS NULL";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $promocionesEliminables = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (empty($promocionesEliminables)) {
                throw new Exception('No hay promociones que se puedan eliminar permanentemente');
            }

            if (!empty($promocionesEliminables)) {
                $placeholders = str_repeat('?,', count($promocionesEliminables) - 1) . '?';
                $sqlProductos = "DELETE FROM productos_promocion WHERE promocion_id IN ($placeholders)";
                $stmtProductos = $this->pdo->prepare($sqlProductos);
                $stmtProductos->execute($promocionesEliminables);

                $sqlPromociones = "DELETE FROM promociones WHERE id IN ($placeholders)";
                $stmtPromociones = $this->pdo->prepare($sqlPromociones);
                $stmtPromociones->execute($promocionesEliminables);
            }

            $this->pdo->commit();
            return count($promocionesEliminables);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
