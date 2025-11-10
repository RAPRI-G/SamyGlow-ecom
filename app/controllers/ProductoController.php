<?php
// app/controllers/ProductoController.php

require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Categoria.php';

class ProductoController
{
    private $productoModel;
    private $categoriaModel;

    public function __construct()
    {
        global $pdo; // 🔥 AGREGAR ESTA LÍNEA
        $this->productoModel = new Producto($pdo); // 🔥 AGREGAR $pdo
        $this->categoriaModel = new Categoria($pdo); // 🔥 AGREGAR $pdo
    }

    // 🔹 VISTA PRINCIPAL DE GESTIÓN DE PRODUCTOS
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Evitar caché
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");

        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?view=login");
            exit;
        }

        $title = "Gestión de Productos - SamyGlow";
        $pageTitle = "Gestión de Productos";

        // Obtener datos para la vista
        $productos = $this->productoModel->listar();
        $categorias = $this->categoriaModel->listar();
        $estadisticas = $this->productoModel->obtenerEstadisticas();
        $productosStockBajo = $this->productoModel->productosStockBajo();

        require_once __DIR__ . '/../views/templates/header.php';
        require_once __DIR__ . '/../views/admin/gestion-productos.php';
        require_once __DIR__ . '/../views/templates/footer.php';
    }

    // 🔹 API: LISTAR PRODUCTOS (para AJAX)
    public function listarProductos()
    {
        header('Content-Type: application/json');

        try {
            $productos = $this->productoModel->listar();
            echo json_encode([
                'success' => true,
                'data' => $productos,
                'total' => count($productos)
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener productos: ' . $e->getMessage()
            ]);
        }
    }

    // 🔹 API: LISTAR CATEGORÍAS
    public function listarCategorias()
    {
        header('Content-Type: application/json');

        try {
            $categorias = $this->categoriaModel->listar();
            echo json_encode([
                'success' => true,
                'data' => $categorias
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener categorías: ' . $e->getMessage()
            ]);
        }
    }

    // 🔹 API: GUARDAR NUEVO PRODUCTO
    public function guardarProducto()
    {
        header('Content-Type: application/json');

        try {
            $datos = [
                'nombre' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'precio' => floatval($_POST['precio'] ?? 0),
                'stock' => intval($_POST['stock'] ?? 0),
                'categoria_id' => intval($_POST['categoria_id'] ?? 0),
                'activo' => isset($_POST['activo']) ? 1 : 0
            ];

            // Validaciones básicas
            if (empty($datos['nombre']) || $datos['precio'] <= 0) {
                throw new Exception('Nombre y precio son obligatorios');
            }

            if ($datos['stock'] < 0) {
                throw new Exception('El stock no puede ser negativo');
            }

            $resultado = $this->productoModel->crear($datos);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Producto creado exitosamente'
                ]);
            } else {
                throw new Exception('Error al crear el producto');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // 🔹 API: EDITAR PRODUCTO
    public function editarProducto()
    {
        header('Content-Type: application/json');

        try {
            $id = intval($_POST['id'] ?? 0);
            $datos = [
                'nombre' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'precio' => floatval($_POST['precio'] ?? 0),
                'stock' => intval($_POST['stock'] ?? 0),
                'categoria_id' => intval($_POST['categoria_id'] ?? 0),
                'activo' => isset($_POST['activo']) ? 1 : 0
            ];

            if ($id <= 0) {
                throw new Exception('ID de producto inválido');
            }

            $resultado = $this->productoModel->actualizar($id, $datos);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Producto actualizado exitosamente'
                ]);
            } else {
                throw new Exception('Error al actualizar el producto');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // 🔹 API: ELIMINAR PRODUCTO (lógico)
    public function eliminarProducto()
    {
        header('Content-Type: application/json');

        try {
            $id = intval($_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception('ID de producto inválido');
            }

            $resultado = $this->productoModel->eliminar($id);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Producto eliminado exitosamente'
                ]);
            } else {
                throw new Exception('Error al eliminar el producto');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // 🔹 API: PRODUCTOS CON STOCK BAJO
    public function productosStockBajo()
    {
        header('Content-Type: application/json');

        try {
            $productos = $this->productoModel->productosStockBajo();
            echo json_encode([
                'success' => true,
                'data' => $productos,
                'total' => count($productos)
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener productos con stock bajo: ' . $e->getMessage()
            ]);
        }
    }

    // 🔹 API: ACTUALIZAR STOCK
    public function actualizarStock()
    {
        header('Content-Type: application/json');

        try {
            $id = intval($_POST['id'] ?? 0);
            $nuevoStock = intval($_POST['stock'] ?? 0);

            if ($id <= 0 || $nuevoStock < 0) {
                throw new Exception('Datos inválidos para actualizar stock');
            }

            $resultado = $this->productoModel->actualizarStock($id, $nuevoStock);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Stock actualizado exitosamente'
                ]);
            } else {
                throw new Exception('Error al actualizar el stock');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // 🔹 API: BUSCAR PRODUCTOS
    public function buscarProductos()
    {
        header('Content-Type: application/json');

        try {
            $termino = $_GET['q'] ?? '';
            $categoria_id = $_GET['categoria_id'] ?? '';

            if (!empty($categoria_id)) {
                $productos = $this->productoModel->listarPorCategoria($categoria_id);
            } elseif (!empty($termino)) {
                $productos = $this->productoModel->buscar($termino);
            } else {
                $productos = $this->productoModel->listar();
            }

            echo json_encode([
                'success' => true,
                'data' => $productos,
                'total' => count($productos)
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al buscar productos: ' . $e->getMessage()
            ]);
        }
    }
    
    // 🔹 API: OBTENER PRODUCTO POR ID
    public function obtenerProducto()
    {
        header('Content-Type: application/json');

        try {
            $id = intval($_GET['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception('ID de producto inválido');
            }

            $producto = $this->productoModel->obtenerPorId($id);

            if ($producto) {
                echo json_encode([
                    'success' => true,
                    'data' => $producto
                ]);
            } else {
                throw new Exception('Producto no encontrado');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
?>