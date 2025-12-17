<?php
// models/Configuracion.php
class Configuracion {
    private $pdo;

    public function __construct($pdo) { // Cambiar aquí
        $this->pdo = $pdo; // Cambiar aquí
    }

    public function obtenerUsuarios() {
        try {
            $sql = "SELECT id, username, correo, activo, created_at FROM usuarios ORDER BY created_at DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error obtenerUsuarios: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerConfigGeneral() {
        return [
            'nombre_tienda' => 'SamyGlow',
            'correo_tienda' => 'info@samyglow.com',
            'moneda_tienda' => 'PEN'
        ];
    }

    public function obtenerHistorialBackups() {
        return [];
    }
}
?>