<?php
class Cliente {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function buscar($term) {
        $sql = "SELECT * FROM clientes
                WHERE dni LIKE ?
                OR nombres LIKE ?
                OR apellidos LIKE ?";

        $stmt = $this->pdo->prepare($sql);
        $term = "%$term%";
        $stmt->execute([$term,$term,$term]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrar($data) {
        $sql = "INSERT INTO clientes (nombres, apellidos, dni, correo, telefono)
                VALUES (?,?,?,?,?)";

        $stmt = $this->pdo->prepare($sql);

        $ok = $stmt->execute([
            $data["nombres"],
            $data["apellidos"],
            $data["dni"],
            $data["correo"],
            $data["telefono"],
        ]);

        // 🔥 Retornar el ID del nuevo cliente
        return $ok ? $this->pdo->lastInsertId() : false;
    }
}
