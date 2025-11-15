<?php
class ConfiguracionController {
    
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index() {
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php?view=login');
            exit;
        }

        // Cargar modelo
        require_once __DIR__ . '/../models/Configuracion.php';
        $configModel = new Configuracion();
        
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
}
?>