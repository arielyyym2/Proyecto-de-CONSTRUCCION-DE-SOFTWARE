<?php

class VentaRepository {

    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function listar() {
        $sql = "SELECT v.id, v.factura_id, v.producto_id, p.nombre AS producto,
                       v.cantidad, v.precio_unit, v.subtotal
                FROM venta v
                INNER JOIN productos p ON p.id = v.producto_id";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM venta WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function registrar($data) {
        $stmt = $this->db->prepare("INSERT INTO venta 
                (factura_id, producto_id, cantidad, precio_unit) 
                VALUES (?, ?, ?, ?)");

        return $stmt->execute([
            $data['factura_id'],
            $data['producto_id'],
            $data['cantidad'],
            $data['precio_unit']
        ]);
    }

    public function actualizar($id, $data) {
        $stmt = $this->db->prepare("UPDATE venta 
            SET factura_id=?, producto_id=?, cantidad=?, precio_unit=? 
            WHERE id=?");

        return $stmt->execute([
            $data['factura_id'],
            $data['producto_id'],
            $data['cantidad'],
            $data['precio_unit'],
            $id
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->db->prepare("DELETE FROM venta WHERE id=?");
        return $stmt->execute([$id]);
    }
}
