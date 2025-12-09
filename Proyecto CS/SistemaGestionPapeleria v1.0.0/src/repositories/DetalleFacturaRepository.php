<?php

class DetalleFacturaRepository
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function findByFacturaId(int $facturaId): array
    {
        try {
            $sql = "SELECT * FROM detalle_facturas WHERE factura_id = :factura_id ORDER BY id ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':factura_id', $facturaId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener detalles: " . $e->getMessage());
        }
    }

    public function create(array $data): bool
    {
        try {
            $sql = "INSERT INTO detalle_facturas 
                    (factura_id, producto_nombre, cantidad, precio_unitario, subtotal) 
                    VALUES 
                    (:factura_id, :producto_nombre, :cantidad, :precio_unitario, :subtotal)";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindParam(':factura_id', $data['factura_id'], PDO::PARAM_INT);
            $stmt->bindParam(':producto_nombre', $data['producto_nombre']);
            $stmt->bindParam(':cantidad', $data['cantidad']);
            $stmt->bindParam(':precio_unitario', $data['precio_unitario']);
            $stmt->bindParam(':subtotal', $data['subtotal']);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al crear detalle: " . $e->getMessage());
        }
    }

    public function deleteByFacturaId(int $facturaId): bool
    {
        try {
            $sql = "DELETE FROM detalle_facturas WHERE factura_id = :factura_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':factura_id', $facturaId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar detalles: " . $e->getMessage());
        }
    }

    public function createMultiple(int $facturaId, array $detalles): bool
    {
        try {
            // Las transacciones se manejan desde el controlador
            // Eliminar detalles existentes
            $this->deleteByFacturaId($facturaId);

            // Insertar nuevos detalles
            foreach ($detalles as $detalle) {
                $detalle['factura_id'] = $facturaId;
                $this->create($detalle);
            }

            return true;
        } catch (Exception $e) {
            throw new Exception("Error al crear múltiples detalles: " . $e->getMessage());
        }
    }
}
