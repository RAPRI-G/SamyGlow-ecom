<?php
class Cliente {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // 🔹 LISTAR TODOS LOS CLIENTES
    public function listar() {
        $sql = "SELECT c.*, 
                       COUNT(p.id) as total_pedidos,
                       COALESCE(SUM(p.total), 0) as total_gastado,
                       MAX(p.fecha) as ultimo_pedido
                FROM clientes c
                LEFT JOIN pedidos p ON c.id = p.cliente_id
                GROUP BY c.id
                ORDER BY c.nombres, c.apellidos";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 OBTENER CLIENTE POR ID
    public function obtener($id) {
        $sql = "SELECT c.*, 
                       COUNT(p.id) as total_pedidos,
                       COALESCE(SUM(p.total), 0) as total_gastado,
                       MAX(p.fecha) as ultimo_pedido
                FROM clientes c
                LEFT JOIN pedidos p ON c.id = p.cliente_id
                WHERE c.id = ?
                GROUP BY c.id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔹 BUSCAR CLIENTES
    public function buscar($term) {
        $sql = "SELECT c.*, 
                       COUNT(p.id) as total_pedidos,
                       COALESCE(SUM(p.total), 0) as total_gastado,
                       MAX(p.fecha) as ultimo_pedido
                FROM clientes c
                LEFT JOIN pedidos p ON c.id = p.cliente_id
                WHERE c.dni LIKE ?
                   OR c.nombres LIKE ?
                   OR c.apellidos LIKE ?
                   OR c.correo LIKE ?
                GROUP BY c.id
                ORDER BY c.nombres, c.apellidos";

        $stmt = $this->pdo->prepare($sql);
        $term = "%$term%";
        $stmt->execute([$term, $term, $term, $term]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 REGISTRAR CLIENTE
    public function registrar($data) {
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
    public function actualizar($id, $data) {
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
    public function eliminar($id) {
        $sql = "DELETE FROM clientes WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    // 🔹 OBTENER CLIENTES FRECUENTES (top 5 por pedidos)
    public function obtenerFrecuentes() {
        $sql = "SELECT c.*, 
                       COUNT(p.id) as total_pedidos,
                       COALESCE(SUM(p.total), 0) as total_gastado,
                       MAX(p.fecha) as ultimo_pedido
                FROM clientes c
                LEFT JOIN pedidos p ON c.id = p.cliente_id
                GROUP BY c.id
                HAVING total_pedidos > 0
                ORDER BY total_pedidos DESC, total_gastado DESC
                LIMIT 5";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 OBTENER ESTADÍSTICAS GENERALES (SIMPLIFICADO Y CORREGIDO)
    public function obtenerEstadisticas() {
        // Consulta simplificada para evitar errores
        $sql = "SELECT 
                COUNT(*) as total_clientes,
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
                    SELECT SUM(total) as total_gastado 
                    FROM pedidos 
                    GROUP BY cliente_id
                ) p), 0) as max_gastado";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Asegurar que todos los campos existan
        return [
            'total_clientes' => $result['total_clientes'] ?? 0,
            'clientes_con_pedidos' => $result['clientes_con_pedidos'] ?? 0,
            'promedio_pedidos' => $result['promedio_pedidos'] ?? 0,
            'max_pedidos' => $result['max_pedidos'] ?? 0,
            'max_gastado' => $result['max_gastado'] ?? 0
        ];
    }

    // 🔹 CONTAR TOTAL DE CLIENTES
    public function contarTotal() {
        $sql = "SELECT COUNT(*) as total FROM clientes";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // 🔹 VERIFICAR SI DNI EXISTE
    public function existeDni($dni, $excluirId = null) {
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
    public function existeCorreo($correo, $excluirId = null) {
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
    public function obtenerPorDni($dni) {
        $sql = "SELECT * FROM clientes WHERE dni = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$dni]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔹 MÉTODO SIMPLE PARA OBTENER ESTADÍSTICAS BÁSICAS (ALTERNATIVO)
    public function obtenerEstadisticasBasicas() {
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
}