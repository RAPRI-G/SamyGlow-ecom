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

        // 🔴 AGREGAR: Procesar las imágenes para cada producto
        foreach ($productos as &$producto) {
            if (!empty($producto['imagen'])) {
                // Usar los métodos del modelo para obtener URL y ruta física
                $producto['imagen_url'] = $this->productoModel->obtenerUrlImagen($producto['imagen']);
                $producto['imagen_ruta_fisica'] = $this->productoModel->obtenerRutaFisica($producto['imagen']);
                $producto['imagen_existe'] = file_exists($producto['imagen_ruta_fisica']);
            } else {
                $producto['imagen_url'] = null;
                $producto['imagen_existe'] = false;
            }
        }
        unset($producto); // Importante: romper la referencia

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
    // 🔹 API: GUARDAR NUEVO PRODUCTO (ACTUALIZADO para imágenes)
    public function guardarProducto()
    {
        header('Content-Type: application/json');

        try {
            // Validar que se hayan enviado datos
            if (empty($_POST['nombre'])) {
                throw new Exception('El nombre del producto es obligatorio');
            }

            $datos = [
                'nombre' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'precio' => floatval($_POST['precio'] ?? 0),
                'stock' => intval($_POST['stock'] ?? 0),
                'categoria_id' => intval($_POST['categoria_id'] ?? 0),
                'activo' => isset($_POST['activo']) ? 1 : 0,
                'imagen' => '' // Inicialmente vacío
            ];

            // Validaciones básicas
            if (empty($datos['nombre']) || $datos['precio'] <= 0) {
                throw new Exception('Nombre y precio son obligatorios');
            }

            if ($datos['stock'] < 0) {
                throw new Exception('El stock no puede ser negativo');
            }

            // Manejar la subida de imagen
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $imagen = $this->manejarSubidaImagen($_FILES['imagen']);
                if ($imagen) {
                    $datos['imagen'] = $imagen;
                }
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

    // 🔹 MÉTODO PARA MANEJAR SUBIDA DE IMAGEN
    // En ProductoController.php, modifica el método manejarSubidaImagen:
    private function manejarSubidaImagen($archivo)
    {
        // Ruta ABSOLUTA en el servidor
        $directorioDestino = $_SERVER['DOCUMENT_ROOT'] . '/SamyGlow-ecom/uploads/productos/';

        // Crear directorio si no existe
        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0777, true);
        }

        // Validar tipo de archivo
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $tipoArchivo = mime_content_type($archivo['tmp_name']);

        if (!in_array($tipoArchivo, $tiposPermitidos)) {
            throw new Exception('Solo se permiten imágenes JPEG, PNG, GIF y WebP');
        }

        // Validar tamaño (máximo 5MB)
        $tamañoMaximo = 5 * 1024 * 1024; // 5MB
        if ($archivo['size'] > $tamañoMaximo) {
            throw new Exception('La imagen no puede ser mayor a 5MB');
        }

        // Generar nombre único
        $nombreOriginal = pathinfo($archivo['name'], PATHINFO_FILENAME);
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $nombreLimpio = $this->sanitizarNombreArchivo($nombreOriginal);

        if (empty($nombreLimpio)) {
            $nombreLimpio = 'producto_' . uniqid();
        }

        $nombreArchivo = $nombreLimpio . '.' . $extension;
        $rutaCompleta = $directorioDestino . $nombreArchivo;

        // Si ya existe, agregar timestamp
        if (file_exists($rutaCompleta)) {
            $nombreArchivo = $nombreLimpio . '_' . time() . '.' . $extension;
            $rutaCompleta = $directorioDestino . $nombreArchivo;
        }

        // Mover archivo
        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            // 🔴 GUARDAR SOLO EL NOMBRE DEL ARCHIVO, NO LA RUTA
            return $nombreArchivo;
        } else {
            throw new Exception('Error al subir la imagen.');
        }
    }

    // Asegúrate de que el método sanitizarNombreArchivo esté presente:
    private function sanitizarNombreArchivo($nombre)
    {
        // Reemplazar espacios por guiones
        $nombre = str_replace(' ', '-', $nombre);
        // Eliminar caracteres especiales excepto guiones
        $nombre = preg_replace('/[^A-Za-z0-9\-]/', '', $nombre);
        // Convertir a minúsculas
        $nombre = strtolower($nombre);
        // Eliminar múltiples guiones consecutivos
        $nombre = preg_replace('/-+/', '-', $nombre);
        // Eliminar guiones al inicio y final
        $nombre = trim($nombre, '-');
        // Limitar longitud
        $nombre = substr($nombre, 0, 100);

        return $nombre;
    }

    // 🔹 API: EDITAR PRODUCTO
    public function editarProducto()
    {
        header('Content-Type: application/json');

        try {
            $id = intval($_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception('ID de producto inválido');
            }

            // Obtener producto actual para mantener la imagen existente
            $productoActual = $this->productoModel->obtenerPorId($id);

            $datos = [
                'nombre' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'precio' => floatval($_POST['precio'] ?? 0),
                'stock' => intval($_POST['stock'] ?? 0),
                'categoria_id' => intval($_POST['categoria_id'] ?? 0),
                'activo' => isset($_POST['activo']) ? 1 : 0,
                'imagen' => $productoActual['imagen'] ?? '' // Mantener imagen actual por defecto
            ];

            // Manejar nueva imagen si se subió
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $nuevaImagen = $this->manejarSubidaImagen($_FILES['imagen']);
                if ($nuevaImagen) {
                    // Eliminar imagen anterior si existe
                    if (!empty($productoActual['imagen'])) {
                        $rutaImagenAnterior = __DIR__ . '/../../' . $productoActual['imagen'];
                        if (file_exists($rutaImagenAnterior)) {
                            unlink($rutaImagenAnterior);
                        }
                    }
                    $datos['imagen'] = $nuevaImagen;
                }
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
    // 🔹 API: LISTAR PRODUCTOS ELIMINADOS
    public function listarEliminados()
    {
        header('Content-Type: application/json');

        try {
            $productos = $this->productoModel->listarEliminados();
            echo json_encode([
                'success' => true,
                'data' => $productos,
                'total' => count($productos)
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener productos eliminados: ' . $e->getMessage()
            ]);
        }
    }

    // 🔹 API: RESTAURAR PRODUCTO
    public function restaurarProducto()
    {
        header('Content-Type: application/json');

        try {
            $id = intval($_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception('ID de producto inválido');
            }

            $resultado = $this->productoModel->restaurar($id);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Producto restaurado exitosamente'
                ]);
            } else {
                throw new Exception('Error al restaurar el producto');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // 🔹 API: ELIMINAR PERMANENTEMENTE
    public function eliminarPermanentemente()
    {
        header('Content-Type: application/json');

        try {
            $id = intval($_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception('ID de producto inválido');
            }

            $resultado = $this->productoModel->eliminarPermanentemente($id);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Producto eliminado permanentemente'
                ]);
            } else {
                throw new Exception('Error al eliminar el producto permanentemente');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // 🔹 API: VACIAR PAPELERA
    public function vaciarPapelera()
    {
        header('Content-Type: application/json');

        try {
            $resultado = $this->productoModel->vaciarPapelera();

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Papelera vaciada exitosamente'
                ]);
            } else {
                throw new Exception('Error al vaciar la papelera');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // 🔹 API: CONTAR PAPELERA
    public function contarPapelera()
    {
        header('Content-Type: application/json');

        try {
            $total = $this->productoModel->contarPapelera();
            echo json_encode([
                'success' => true,
                'total' => $total
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al contar papelera: ' . $e->getMessage()
            ]);
        }
    }

    // 🔹 API: OBTENER CATEGORÍA POR ID
    public function obtenerCategoria()
    {
        header('Content-Type: application/json');

        try {
            $id = intval($_GET['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception('ID de categoría inválido');
            }

            $categoria = $this->categoriaModel->obtenerPorId($id);

            if ($categoria) {
                echo json_encode([
                    'success' => true,
                    'data' => $categoria
                ]);
            } else {
                throw new Exception('Categoría no encontrada');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // 🔹 API: ACTUALIZAR CATEGORÍA
    public function actualizarCategoria()
    {
        header('Content-Type: application/json');

        try {
            $id = intval($_POST['id'] ?? 0);
            $datos = [
                'nombre' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'activa' => isset($_POST['activa']) ? 1 : 0
            ];

            if ($id <= 0) {
                throw new Exception('ID de categoría inválido');
            }

            if (empty($datos['nombre'])) {
                throw new Exception('El nombre de la categoría es obligatorio');
            }

            $resultado = $this->categoriaModel->actualizar($id, $datos);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Categoría actualizada exitosamente'
                ]);
            } else {
                throw new Exception('Error al actualizar la categoría');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // 🔹 API: CREAR CATEGORÍA
    public function crearCategoria()
    {
        header('Content-Type: application/json');

        try {
            $datos = [
                'nombre' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'activa' => isset($_POST['activa']) ? 1 : 0
            ];

            if (empty($datos['nombre'])) {
                throw new Exception('El nombre de la categoría es obligatorio');
            }

            $resultado = $this->categoriaModel->crear($datos);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Categoría creada exitosamente'
                ]);
            } else {
                throw new Exception('Error al crear la categoría');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
