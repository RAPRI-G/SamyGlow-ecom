<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';

class CategoriaModel {
	private $conn;

	public function __construct() {
		$this->conn = Database::getConnection();
	}

	public function obtenerCategorias(): array {
		$sql = "SELECT id, nombre, descripcion FROM categorias WHERE eliminado = 0 ORDER BY nombre ASC";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function obtenerCategoriaPorId(int $id): ?array {
		$sql = "SELECT id, nombre, descripcion FROM categorias WHERE id = :id AND eliminado = 0";
		$stmt = $this->conn->prepare($sql);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result ?: null;
	}
}
