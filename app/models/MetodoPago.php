<?php
// app/models/MetodoPago.php
class MetodoPago
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    // Obtener todos los métodos de pago
    public function obtenerTodos()
    {
        $query = "SELECT * FROM metodos_pago WHERE activo = 1 ORDER BY id";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener método por ID
    public function obtenerPorId($id)
    {
        $query = "SELECT * FROM metodos_pago WHERE id = :id AND activo = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Verificar si el nombre ya existe
    public function nombreExiste($nombre, $excluirId = null)
    {
        $query = "SELECT COUNT(*) as count FROM metodos_pago WHERE nombre = :nombre AND activo = 1";
        if ($excluirId) {
            $query .= " AND id != :excluir_id";
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        if ($excluirId) {
            $stmt->bindParam(':excluir_id', $excluirId);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    // Crear nuevo método de pago
    public function crear($datos)
    {
        // Verificar si el nombre ya existe
        if ($this->nombreExiste($datos['nombre'])) {
            throw new Exception("Ya existe un método de pago con el nombre: " . $datos['nombre']);
        }

        $query = "INSERT INTO metodos_pago (nombre, tipo, descripcion, icono, activo) 
                  VALUES (:nombre, :tipo, :descripcion, :icono, :activo)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':tipo', $datos['tipo']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':icono', $datos['icono']);
        $stmt->bindParam(':activo', $datos['activo']);
        return $stmt->execute();
    }

    // Actualizar método de pago
    public function actualizar($id, $datos)
    {
        // Verificar si el nombre ya existe (excluyendo el actual)
        if ($this->nombreExiste($datos['nombre'], $id)) {
            throw new Exception("Ya existe otro método de pago con el nombre: " . $datos['nombre']);
        }

        $query = "UPDATE metodos_pago SET 
                  nombre = :nombre, 
                  tipo = :tipo, 
                  descripcion = :descripcion, 
                  icono = :icono, 
                  activo = :activo,
                  updated_at = CURRENT_TIMESTAMP()
                  WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':tipo', $datos['tipo']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':icono', $datos['icono']);
        $stmt->bindParam(':activo', $datos['activo']);
        return $stmt->execute();
    }

    // Eliminar método de pago (soft delete)
    public function eliminar($id)
    {
        $query = "UPDATE metodos_pago SET activo = 0 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Obtener estadísticas de métodos de pago con datos REALES
    public function obtenerEstadisticas()
    {
        $query = "SELECT 
                    mp.id,
                    mp.nombre,
                    mp.tipo,
                    mp.descripcion,
                    mp.icono,
                    mp.activo,
                    COUNT(p.id) as pedidos_mes,
                    COALESCE(SUM(p.total), 0) as total_mes
                  FROM metodos_pago mp
                  LEFT JOIN pedidos p ON mp.id = p.metodo_pago_id 
                    AND p.estado = 'entregado' 
                    AND MONTH(p.fecha) = MONTH(CURRENT_DATE())
                    AND YEAR(p.fecha) = YEAR(CURRENT_DATE())
                    AND p.eliminado = 0
                  WHERE mp.activo = 1
                  GROUP BY mp.id, mp.nombre, mp.tipo, mp.descripcion, mp.icono, mp.activo
                  ORDER BY pedidos_mes DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Si no hay datos de pedidos, al menos devolver los métodos básicos
        if (empty($result)) {
            $query = "SELECT 
                        id,
                        nombre,
                        tipo,
                        descripcion,
                        icono,
                        activo,
                        0 as pedidos_mes,
                        0 as total_mes
                      FROM metodos_pago 
                      WHERE activo = 1 
                      ORDER BY id";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $result;
    }

    // Obtener configuración
    public function obtenerConfiguracion()
    {
        $query = "SELECT * FROM configuracion_metodos_pago ORDER BY id DESC LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $config = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$config) {
            // Crear configuración por defecto
            $this->crearConfiguracionPorDefecto();
            $stmt->execute();
            $config = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return $config;
    }

    // Actualizar configuración
    public function actualizarConfiguracion($datos)
    {
        $query = "UPDATE configuracion_metodos_pago SET 
                  multiples_metodos = :multiples_metodos,
                  notificaciones_pago = :notificaciones_pago,
                  confirmacion_automatica = :confirmacion_automatica,
                  metodo_predeterminado_id = :metodo_predeterminado_id,
                  orden_metodos = :orden_metodos,
                  updated_at = CURRENT_TIMESTAMP() 
                  ORDER BY id DESC LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':multiples_metodos', $datos['multiples_metodos'], PDO::PARAM_INT);
        $stmt->bindParam(':notificaciones_pago', $datos['notificaciones_pago'], PDO::PARAM_INT);
        $stmt->bindParam(':confirmacion_automatica', $datos['confirmacion_automatica'], PDO::PARAM_INT);
        $stmt->bindParam(':metodo_predeterminado_id', $datos['metodo_predeterminado_id'], PDO::PARAM_INT);
        $stmt->bindParam(':orden_metodos', json_encode($datos['orden_metodos']));
        return $stmt->execute();
    }

    // Crear configuración por defecto
    private function crearConfiguracionPorDefecto()
    {
        $query = "INSERT INTO configuracion_metodos_pago 
                  (multiples_metodos, notificaciones_pago, confirmacion_automatica, metodo_predeterminado_id, orden_metodos) 
                  VALUES (1, 1, 0, 1, '[1,2,3,4,5]')";
        $stmt = $this->db->prepare($query);
        return $stmt->execute();
    }
}
