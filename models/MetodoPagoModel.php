<?php
// models/MetodoPago.php
class MetodoPago {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Obtiene todos los métodos de pago activos
     * @return array Lista de métodos de pago
     */
    public function obtenerTodos() {
        try {
            $sql = "SELECT 
                        id,
                        nombre,
                        activo,
                        tipo,
                        descripcion,
                        icono,
                        created_at,
                        updated_at
                    FROM metodos_pago 
                    WHERE activo = 1 
                    ORDER BY id ASC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            $metodos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Formatear los datos para la respuesta
            return array_map(function($metodo) {
                return [
                    'id' => (int)$metodo['id'],
                    'nombre' => $metodo['nombre'],
                    'activo' => (bool)$metodo['activo'],
                    'tipo' => $metodo['tipo'],
                    'descripcion' => $metodo['descripcion'],
                    'icono' => $metodo['icono'],
                    'created_at' => $metodo['created_at'],
                    'updated_at' => $metodo['updated_at']
                ];
            }, $metodos);

        } catch (Exception $e) {
            error_log("Error en MetodoPago::obtenerTodos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene un método de pago por ID
     * @param int $id ID del método de pago
     * @return array|null Datos del método de pago o null si no existe
     */
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT 
                        id,
                        nombre,
                        activo,
                        tipo,
                        descripcion,
                        icono,
                        created_at,
                        updated_at
                    FROM metodos_pago 
                    WHERE id = ? AND activo = 1";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            
            $metodo = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($metodo) {
                return [
                    'id' => (int)$metodo['id'],
                    'nombre' => $metodo['nombre'],
                    'activo' => (bool)$metodo['activo'],
                    'tipo' => $metodo['tipo'],
                    'descripcion' => $metodo['descripcion'],
                    'icono' => $metodo['icono'],
                    'created_at' => $metodo['created_at'],
                    'updated_at' => $metodo['updated_at']
                ];
            }

            return null;

        } catch (Exception $e) {
            error_log("Error en MetodoPago::obtenerPorId: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene métodos de pago por tipo
     * @param string $tipo Tipo de método (digital, card, cash, transfer)
     * @return array Lista de métodos de pago del tipo especificado
     */
    public function obtenerPorTipo($tipo) {
        try {
            $tiposValidos = ['digital', 'card', 'cash', 'transfer'];
            
            if (!in_array($tipo, $tiposValidos)) {
                return [];
            }

            $sql = "SELECT 
                        id,
                        nombre,
                        activo,
                        tipo,
                        descripcion,
                        icono,
                        created_at,
                        updated_at
                    FROM metodos_pago 
                    WHERE tipo = ? AND activo = 1 
                    ORDER BY id ASC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$tipo]);
            
            $metodos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map(function($metodo) {
                return [
                    'id' => (int)$metodo['id'],
                    'nombre' => $metodo['nombre'],
                    'activo' => (bool)$metodo['activo'],
                    'tipo' => $metodo['tipo'],
                    'descripcion' => $metodo['descripcion'],
                    'icono' => $metodo['icono'],
                    'created_at' => $metodo['created_at'],
                    'updated_at' => $metodo['updated_at']
                ];
            }, $metodos);

        } catch (Exception $e) {
            error_log("Error en MetodoPago::obtenerPorTipo: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene la configuración general de métodos de pago
     * @return array Configuración de métodos de pago
     */
    public function obtenerConfiguracion() {
        try {
            $sql = "SELECT 
                        multiples_metodos,
                        notificaciones_pago,
                        confirmacion_automatica,
                        metodo_predeterminado_id,
                        orden_metodos,
                        created_at,
                        updated_at
                    FROM configuracion_metodos_pago 
                    WHERE id = 1 
                    LIMIT 1";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            $config = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($config) {
                return [
                    'multiples_metodos' => (bool)$config['multiples_metodos'],
                    'notificaciones_pago' => (bool)$config['notificaciones_pago'],
                    'confirmacion_automatica' => (bool)$config['confirmacion_automatica'],
                    'metodo_predeterminado_id' => $config['metodo_predeterminado_id'] ? (int)$config['metodo_predeterminado_id'] : null,
                    'orden_metodos' => $config['orden_metodos'] ? json_decode($config['orden_metodos'], true) : [],
                    'created_at' => $config['created_at'],
                    'updated_at' => $config['updated_at']
                ];
            }

            // Configuración por defecto si no existe en la base de datos
            return [
                'multiples_metodos' => true,
                'notificaciones_pago' => true,
                'confirmacion_automatica' => false,
                'metodo_predeterminado_id' => null,
                'orden_metodos' => [],
                'created_at' => null,
                'updated_at' => null
            ];

        } catch (Exception $e) {
            error_log("Error en MetodoPago::obtenerConfiguracion: " . $e->getMessage());
            return [
                'multiples_metodos' => true,
                'notificaciones_pago' => true,
                'confirmacion_automatica' => false,
                'metodo_predeterminado_id' => null,
                'orden_metodos' => []
            ];
        }
    }

    /**
     * Obtiene métodos de pago ordenados según la configuración
     * @return array Métodos de pago ordenados
     */
    public function obtenerOrdenados() {
        try {
            $configuracion = $this->obtenerConfiguracion();
            $metodos = $this->obtenerTodos();

            // Si hay un orden definido en la configuración, ordenar según ese orden
            if (!empty($configuracion['orden_metodos']) && is_array($configuracion['orden_metodos'])) {
                $orden = $configuracion['orden_metodos'];
                
                usort($metodos, function($a, $b) use ($orden) {
                    $posA = array_search($a['id'], $orden);
                    $posB = array_search($b['id'], $orden);
                    
                    if ($posA === false && $posB === false) return 0;
                    if ($posA === false) return 1;
                    if ($posB === false) return -1;
                    
                    return $posA - $posB;
                });
            }

            return $metodos;

        } catch (Exception $e) {
            error_log("Error en MetodoPago::obtenerOrdenados: " . $e->getMessage());
            return $this->obtenerTodos(); // Fallback a obtener todos sin orden
        }
    }

    /**
     * Verifica si un método de pago está activo y existe
     * @param int $id ID del método de pago
     * @return bool True si existe y está activo
     */
    public function existeYActivo($id) {
        try {
            $sql = "SELECT COUNT(*) as count 
                    FROM metodos_pago 
                    WHERE id = ? AND activo = 1";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;

        } catch (Exception $e) {
            error_log("Error en MetodoPago::existeYActivo: " . $e->getMessage());
            return false;
        }
    }
}
?>