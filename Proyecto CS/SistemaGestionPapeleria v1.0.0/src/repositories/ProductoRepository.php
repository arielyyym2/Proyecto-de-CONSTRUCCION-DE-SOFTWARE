<?php

class ProductoRepository {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function listar() {
        $sql = "SELECT p.*, pr.empresa AS proveedor 
                FROM productos p 
                LEFT JOIN proveedores pr ON p.proveedor_id = pr.id";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM productos WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function registrar($data) {
        $sql = "INSERT INTO productos (nombre, descripcion, precio, stock, proveedor_id)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['nombre'],
            $data['descripcion'],
            $data['precio'],
            $data['stock'],
            $data['proveedor_id']
        ]);
    }

    
    public function eliminar($id) {
        $sql = "DELETE FROM productos WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function buscarPorId($id) {
    $sql = "SELECT * FROM productos WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($id, $data) {
    $sql = "UPDATE productos 
            SET nombre = ?, descripcion = ?, precio = ?, stock = ?, proveedor_id = ?
            WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute([
        $data['nombre'],
        $data['descripcion'],
        $data['precio'],
        $data['stock'],
        $data['proveedor_id'],
        $id
    ]);
    }

}
