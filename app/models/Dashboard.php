<?php
require_once __DIR__ . '/../../config/database.php';

class Dashboard {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function obtenerEstadisticas() {
        $data = [];

        $data['total_ventas'] = $this->pdo
            ->query("SELECT IFNULL(SUM(total),0) AS total_ventas FROM pedidos")
            ->fetch(PDO::FETCH_ASSOC)['total_ventas'];

        $data['pedidos_pendientes'] = $this->pdo
            ->query("SELECT COUNT(*) AS pedidos_pendientes FROM pedidos WHERE estado='pendiente'")
            ->fetch(PDO::FETCH_ASSOC)['pedidos_pendientes'];

        $data['clientes'] = $this->pdo
            ->query("SELECT COUNT(*) AS clientes FROM clientes")
            ->fetch(PDO::FETCH_ASSOC)['clientes'];

        $data['productos_activos'] = $this->pdo
            ->query("SELECT COUNT(*) AS productos_activos FROM productos WHERE activo=1")
            ->fetch(PDO::FETCH_ASSOC)['productos_activos'];

        return $data;
    }

    public function obtenerPedidosRecientes($limit = 5) {
        $stmt = $this->pdo->prepare("
            SELECT p.id, c.nombres, c.apellidos, p.total, p.estado, p.fecha
            FROM pedidos p
            JOIN clientes c ON p.cliente_id = c.id
            ORDER BY p.fecha DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerProductosBajoStock($limite = 10) {
        $stmt = $this->pdo->prepare("
            SELECT nombre, stock
            FROM productos
            WHERE stock <= 10 AND activo = 1
            ORDER BY stock ASC
            LIMIT :limite
        ");
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
