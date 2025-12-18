<?php
class Usuario {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Intenta autenticar al usuario por username y password.
     * - Soporta hashes modernos (password_hash / password_verify)
     * - Soporta hashes legacy SHA-256 (creados con SHA2(...,256))
     *   y los actualiza automáticamente a password_hash() en la primera autenticación exitosa.
     *
     * @param string $username
     * @param string $password
     * @return array|false  Datos del usuario (array) o false si falla
     */
    public function login($username, $password) {
        try {
            $sql = "SELECT * FROM usuarios WHERE username = :username AND activo = 1 LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                error_log("Auth: usuario no encontrado -> $username");
                return false;
            }

            $stored = $user['password_hash'] ?? '';

            // 1) Si el hash almacenado parece ser uno creado por password_hash()
            if (password_verify($password, $stored)) {
                return $user;
            }

            // 2) Si no, detectar si es un SHA-256 (hex de 64 caracteres)
            //    (tu SQL de datos usó SHA2(...,256) que produce hex length 64)
            if (preg_match('/^[0-9a-f]{64}$/i', $stored)) {
                // comparar con SHA-256
                $sha256 = hash('sha256', $password);
                if (hash_equals($stored, $sha256)) {
                    // autenticación correcta — migrar hash a password_hash()
                    try {
                        $newHash = password_hash($password, PASSWORD_DEFAULT);
                        $update = $this->pdo->prepare("UPDATE usuarios SET password_hash = :ph WHERE id = :id");
                        $update->execute(['ph' => $newHash, 'id' => $user['id']]);
                        // refrescar el user['password_hash'] por seguridad antes de devolver 
                        $user['password_hash'] = $newHash;
                        error_log("Auth: usuario '$username' migrado SHA256 -> password_hash().");
                    } catch (Exception $e) {
                        error_log("Auth: fallo al migrar hash para $username: " . $e->getMessage());
                    }
                    return $user;
                }
            }

            // 3) Si nada coincide -> fallo
            error_log("Auth: contraseña incorrecta para usuario -> $username");
            return false;

        } catch (Exception $e) {
            error_log("Auth: Exception en Usuario::login -> " . $e->getMessage());
            return false;
        }
    }
}
