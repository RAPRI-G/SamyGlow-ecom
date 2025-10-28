<?php
require_once __DIR__ . '/../models/Dashboard.php';

class DashboardController {
    private $model;

    public function __construct() {
        $this->model = new Dashboard();
    }

    public function index() {
        $data = [];
        $data['estadisticas'] = $this->model->obtenerEstadisticas();
        $data['pedidos'] = $this->model->obtenerPedidosRecientes();
        $data['alertas_stock'] = $this->model->obtenerProductosBajoStock();

        // Verificación temporal (para debug)
        // var_dump($data); die();

        require __DIR__ . '/../views/admin/dashboard.php';
    }
}
