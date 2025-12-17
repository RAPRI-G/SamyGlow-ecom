<?php
// app/controllers/ConfiguracionController.php
class ConfiguracionController
{

    private $pdo;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Establecer conexión a la base de datos
        global $pdo;
        $this->pdo = $pdo;
    }

    public function index()
    {
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php?view=login');
            exit;
        }

        // Cargar modelo
        require_once __DIR__ . '/../models/Configuracion.php';
        $configModel = new Configuracion($this->pdo);

        // Preparar datos para la vista
        $data = [
            'usuarios' => $configModel->obtenerUsuarios(),
            'configGeneral' => $configModel->obtenerConfigGeneral(),
            'backups' => $configModel->obtenerHistorialBackups(),
            'pageTitle' => 'Configuración del Sistema',
            'title' => 'Configuración - SamyGlow'
        ];

        // Extraer variables para la vista
        extract($data);

        // Incluir solo el header (que ya tiene toda la estructura)
        require_once __DIR__ . '/../views/templates/header.php';

        // Incluir el contenido específico
        require_once __DIR__ . '/../views/admin/configuracion.php';

        // Incluir el footer
        require_once __DIR__ . '/../views/templates/footer.php';
    }

    // 🔥 NUEVO: Métodos para las APIs que necesita JavaScript
    public function apiCrearUsuario()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            return;
        }

        try {
            $data = json_decode(file_get_contents('php://input'), true);

            // Validar datos
            if (empty($data['username']) || empty($data['correo']) || empty($data['password'])) {
                throw new Exception('Todos los campos son obligatorios');
            }

            // Verificar si el usuario o correo ya existen
            $sqlCheck = "SELECT id FROM usuarios WHERE username = :username OR correo = :correo";
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->execute([
                ':username' => $data['username'],
                ':correo' => $data['correo']
            ]);

            if ($stmtCheck->fetch()) {
                throw new Exception('El usuario o correo ya están registrados');
            }

            // Hashear contraseña
            $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

            // Insertar en base de datos
            $sql = "INSERT INTO usuarios (username, correo, password_hash, rol, activo) 
                    VALUES (:username, :correo, :password_hash, 'admin', 1)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':username' => $data['username'],
                ':correo' => $data['correo'],
                ':password_hash' => $passwordHash
            ]);

            echo json_encode(['success' => true, 'message' => 'Usuario creado correctamente']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function apiListarUsuarios()
    {
        header('Content-Type: application/json');

        try {
            $sql = "SELECT id, username, correo, activo, created_at FROM usuarios ORDER BY created_at DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'data' => $usuarios]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function apiActualizarUsuario()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            return;
        }

        try {
            $data = json_decode(file_get_contents('php://input'), true);

            // Validar datos
            if (empty($data['id'])) {
                throw new Exception('ID de usuario requerido');
            }

            // Verificar si el usuario existe
            $sqlCheck = "SELECT id FROM usuarios WHERE id = :id";
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->execute([':id' => $data['id']]);

            if (!$stmtCheck->fetch()) {
                throw new Exception('Usuario no encontrado');
            }

            // Verificar si el correo ya está en uso por otro usuario
            if (!empty($data['correo'])) {
                $sqlCheckEmail = "SELECT id FROM usuarios WHERE correo = :correo AND id != :id";
                $stmtCheckEmail = $this->pdo->prepare($sqlCheckEmail);
                $stmtCheckEmail->execute([
                    ':correo' => $data['correo'],
                    ':id' => $data['id']
                ]);

                if ($stmtCheckEmail->fetch()) {
                    throw new Exception('El correo ya está en uso por otro usuario');
                }
            }

            // Preparar consulta base
            $sql = "UPDATE usuarios SET username = :username, correo = :correo, activo = :activo";
            $params = [
                ':username' => $data['username'],
                ':correo' => $data['correo'],
                ':activo' => intval($data['activo']),
                ':id' => $data['id']
            ];

            // Si hay nueva contraseña, actualizarla
            if (!empty($data['password']) && trim($data['password']) !== '') {
                if (strlen($data['password']) < 6) {
                    throw new Exception('La contraseña debe tener al menos 6 caracteres');
                }
                $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
                $sql .= ", password_hash = :password_hash";
                $params[':password_hash'] = $passwordHash;
            }

            $sql .= " WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            echo json_encode(['success' => true, 'message' => 'Usuario actualizado correctamente']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // 🔥 OPCIONAL: Método para eliminar usuario
    public function apiEliminarUsuario()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            return;
        }

        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (empty($data['id'])) {
                echo json_encode(['success' => false, 'error' => 'ID de usuario requerido']);
                return;
            }

            $usuarioId = intval($data['id']);

            // No permitir eliminar el usuario actual (si tienes session de usuario)
            if (isset($_SESSION['usuario_id']) && $usuarioId == $_SESSION['usuario_id']) {
                echo json_encode(['success' => false, 'error' => 'No puedes eliminar tu propio usuario']);
                return;
            }

            // Verificar si el usuario existe
            $sqlCheck = "SELECT id FROM usuarios WHERE id = :id";
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->execute([':id' => $usuarioId]);

            if (!$stmtCheck->fetch()) {
                echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
                return;
            }

            // Eliminar usuario (o marcarlo como inactivo si prefieres borrado lógico)
            // Opción 1: Borrado físico
            $sql = "DELETE FROM usuarios WHERE id = :id";
            // Opción 2: Borrado lógico (recomendado)
            // $sql = "UPDATE usuarios SET activo = 0 WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $usuarioId]);

            echo json_encode(['success' => true, 'message' => 'Usuario eliminado correctamente']);
        } catch (Exception $e) {
            error_log("Error apiEliminarUsuario: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Error interno del servidor: ' . $e->getMessage()]);
        }
    }
}
