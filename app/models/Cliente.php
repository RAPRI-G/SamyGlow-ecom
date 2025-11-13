<?php
class Cliente
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // 🔹 ACTUALIZAR MÉTODOS EXISTENTES PARA CONSIDERAR ELIMINADO
    public function listar()
    {
        $sql = "SELECT c.*, 
                   COUNT(p.id) as total_pedidos,
                   COALESCE(SUM(p.total), 0) as total_gastado,
                   MAX(p.fecha) as ultimo_pedido
            FROM clientes c
            LEFT JOIN pedidos p ON c.id = p.cliente_id
            WHERE c.eliminado = 0
            GROUP BY c.id
            ORDER BY c.nombres, c.apellidos";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtener($id)
    {
        $sql = "SELECT c.*, 
                   COUNT(p.id) as total_pedidos,
                   COALESCE(SUM(p.total), 0) as total_gastado,
                   MAX(p.fecha) as ultimo_pedido
            FROM clientes c
            LEFT JOIN pedidos p ON c.id = p.cliente_id
            WHERE c.id = ? AND c.eliminado = 0
            GROUP BY c.id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscar($term)
    {
        $sql = "SELECT c.*, 
                   COUNT(p.id) as total_pedidos,
                   COALESCE(SUM(p.total), 0) as total_gastado,
                   MAX(p.fecha) as ultimo_pedido
            FROM clientes c
            LEFT JOIN pedidos p ON c.id = p.cliente_id
            WHERE (c.dni LIKE ? OR c.nombres LIKE ? OR c.apellidos LIKE ? OR c.correo LIKE ?)
            AND c.eliminado = 0
            GROUP BY c.id
            ORDER BY c.nombres, c.apellidos";

        $stmt = $this->pdo->prepare($sql);
        $term = "%$term%";
        $stmt->execute([$term, $term, $term, $term]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 REGISTRAR CLIENTE
    public function registrar($data)
    {
        $sql = "INSERT INTO clientes (nombres, apellidos, dni, correo, telefono)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->pdo->prepare($sql);

        $ok = $stmt->execute([
            $data["nombres"],
            $data["apellidos"],
            $data["dni"],
            $data["correo"],
            $data["telefono"],
        ]);

        return $ok ? $this->pdo->lastInsertId() : false;
    }

    // 🔹 ACTUALIZAR CLIENTE
    public function actualizar($id, $data)
    {
        $sql = "UPDATE clientes 
                SET nombres = ?, apellidos = ?, correo = ?, telefono = ?
                WHERE id = ?";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            $data["nombres"],
            $data["apellidos"],
            $data["correo"],
            $data["telefono"],
            $id
        ]);
    }

    // 🔹 ELIMINAR CLIENTE
    public function eliminar($id)
    {
        $sql = "DELETE FROM clientes WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }



    // 🔹 OBTENER ESTADÍSTICAS GENERALES - VERSIÓN MEJORADA
    public function obtenerEstadisticas()
    {
        try {
            // Consulta optimizada y más robusta
            $sql = "SELECT 
                (SELECT COUNT(*) FROM clientes) as total_clientes,
                (SELECT COUNT(DISTINCT cliente_id) FROM pedidos) as clientes_con_pedidos,
                COALESCE((SELECT AVG(pedidos_count) FROM (
                    SELECT COUNT(*) as pedidos_count 
                    FROM pedidos 
                    GROUP BY cliente_id
                ) p), 0) as promedio_pedidos,
                COALESCE((SELECT MAX(pedidos_count) FROM (
                    SELECT COUNT(*) as pedidos_count 
                    FROM pedidos 
                    GROUP BY cliente_id
                ) p), 0) as max_pedidos,
                COALESCE((SELECT MAX(total_gastado) FROM (
                    SELECT COALESCE(SUM(total), 0) as total_gastado 
                    FROM pedidos 
                    GROUP BY cliente_id
                ) p), 0) as max_gastado";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Asegurar que todos los campos existan con valores numéricos
            return [
                'total_clientes' => intval($result['total_clientes'] ?? 0),
                'clientes_con_pedidos' => intval($result['clientes_con_pedidos'] ?? 0),
                'promedio_pedidos' => floatval($result['promedio_pedidos'] ?? 0),
                'max_pedidos' => intval($result['max_pedidos'] ?? 0),
                'max_gastado' => floatval($result['max_gastado'] ?? 0)
            ];
        } catch (Exception $e) {
            error_log("Error en obtenerEstadisticas: " . $e->getMessage());
            return [
                'total_clientes' => 0,
                'clientes_con_pedidos' => 0,
                'promedio_pedidos' => 0,
                'max_pedidos' => 0,
                'max_gastado' => 0
            ];
        }
    }

    // 🔹 CONTAR TOTAL DE CLIENTES
    public function contarTotal()
    {
        $sql = "SELECT COUNT(*) as total FROM clientes";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // 🔹 VERIFICAR SI DNI EXISTE
    public function existeDni($dni, $excluirId = null)
    {
        $sql = "SELECT COUNT(*) as total FROM clientes WHERE dni = ?";
        $params = [$dni];

        if ($excluirId) {
            $sql .= " AND id != ?";
            $params[] = $excluirId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total'] > 0;
    }

    // 🔹 VERIFICAR SI CORREO EXISTE
    public function existeCorreo($correo, $excluirId = null)
    {
        $sql = "SELECT COUNT(*) as total FROM clientes WHERE correo = ?";
        $params = [$correo];

        if ($excluirId) {
            $sql .= " AND id != ?";
            $params[] = $excluirId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total'] > 0;
    }

    // 🔹 OBTENER CLIENTE POR DNI
    public function obtenerPorDni($dni)
    {
        $sql = "SELECT * FROM clientes WHERE dni = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$dni]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔹 MÉTODO SIMPLE PARA OBTENER ESTADÍSTICAS BÁSICAS (ALTERNATIVO)
    public function obtenerEstadisticasBasicas()
    {
        // Método alternativo más simple
        $sqlTotal = "SELECT COUNT(*) as total FROM clientes";
        $sqlConPedidos = "SELECT COUNT(DISTINCT cliente_id) as total FROM pedidos";

        $stmt1 = $this->pdo->prepare($sqlTotal);
        $stmt1->execute();
        $total = $stmt1->fetch(PDO::FETCH_ASSOC);

        $stmt2 = $this->pdo->prepare($sqlConPedidos);
        $stmt2->execute();
        $conPedidos = $stmt2->fetch(PDO::FETCH_ASSOC);

        return [
            'total_clientes' => $total['total'] ?? 0,
            'clientes_con_pedidos' => $conPedidos['total'] ?? 0,
            'promedio_pedidos' => 0,
            'max_pedidos' => 0,
            'max_gastado' => 0
        ];
    }

    // En tu Cliente.php, actualiza el método obtenerFrecuentes:

    // 🔹 OBTENER CLIENTES FRECUENTES MEJORADO
    public function obtenerFrecuentes($filtro = 'pedidos')
    {
        $orden = '';

        switch ($filtro) {
            case 'gastado':
                $orden = 'total_gastado DESC, total_pedidos DESC';
                break;
            case 'reciente':
                $orden = 'ultimo_pedido DESC, total_pedidos DESC';
                break;
            case 'pedidos':
            default:
                $orden = 'total_pedidos DESC, total_gastado DESC';
                break;
        }

        $sql = "SELECT c.*, 
                   COUNT(p.id) as total_pedidos,
                   COALESCE(SUM(p.total), 0) as total_gastado,
                   MAX(p.fecha) as ultimo_pedido
            FROM clientes c
            LEFT JOIN pedidos p ON c.id = p.cliente_id
            GROUP BY c.id
            HAVING total_pedidos > 0
            ORDER BY {$orden}
            LIMIT 5";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 OBTENER ESTADÍSTICAS AVANZADAS PARA CLIENTES FRECUENTES
    public function obtenerEstadisticasFrecuentes()
    {
        $sql = "SELECT 
            COALESCE(MAX(pedidos_count), 0) as max_pedidos,
            COALESCE(MAX(total_gastado), 0) as max_gastado,
            COALESCE(AVG(pedidos_count), 0) as promedio_pedidos,
            COUNT(*) as total_frecuentes
            FROM (
                SELECT 
                    cliente_id,
                    COUNT(*) as pedidos_count,
                    COALESCE(SUM(total), 0) as total_gastado
                FROM pedidos 
                GROUP BY cliente_id
                HAVING pedidos_count > 0
            ) as stats";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'max_pedidos' => intval($result['max_pedidos'] ?? 0),
            'max_gastado' => floatval($result['max_gastado'] ?? 0),
            'promedio_pedidos' => floatval($result['promedio_pedidos'] ?? 0),
            'total_frecuentes' => intval($result['total_frecuentes'] ?? 0)
        ];
    }

    // 🔹 MARCAR CLIENTE COMO ELIMINADO (PAPELERA)
    public function moverPapelera($id)
    {
        $sql = "UPDATE clientes SET eliminado = 1, fecha_eliminado = NOW() WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    // 🔹 RESTAURAR CLIENTE DESDE LA PAPELERA
    public function restaurar($id)
    {
        $sql = "UPDATE clientes SET eliminado = 0, fecha_eliminado = NULL WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    // 🔹 ELIMINAR CLIENTE PERMANENTEMENTE
    public function eliminarPermanentemente($id)
    {
        $sql = "DELETE FROM clientes WHERE id = ? AND eliminado = 1";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    // 🔹 OBTENER CLIENTES ELIMINADOS (PAPELERA)
    public function obtenerEliminados()
    {
        $sql = "SELECT c.*, 
                   COUNT(p.id) as total_pedidos,
                   COALESCE(SUM(p.total), 0) as total_gastado,
                   MAX(p.fecha) as ultimo_pedido
            FROM clientes c
            LEFT JOIN pedidos p ON c.id = p.cliente_id
            WHERE c.eliminado = 1
            GROUP BY c.id
            ORDER BY c.fecha_eliminado DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 CONTAR CLIENTES EN PAPELERA
    public function contarPapelera()
    {
        $sql = "SELECT COUNT(*) as total FROM clientes WHERE eliminado = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // 🔹 VACIAR PAPELERA (ELIMINAR PERMANENTEMENTE TODOS)
    public function vaciarPapelera()
    {
        $sql = "DELETE FROM clientes WHERE eliminado = 1";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute();
    }
}
