<?php
require_once __DIR__ . '/../interfaces/IRepository.php';

class FacturaRepository implements IRepository
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    // Método para acceder a la conexión desde el controlador
    public function getConnection(): PDO
    {
        return $this->conn;
    }

    // Métodos para manejar transacciones
    public function beginTransaction(): bool
    {
        return $this->conn->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->conn->commit();
    }

    public function rollBack(): bool
    {
        return $this->conn->rollBack();
    }

    public function findAll(): array
    {
        try {
            $sql = "SELECT f.*, 
                    (SELECT COUNT(*) FROM detalle_facturas WHERE factura_id = f.id) as cantidad_items
                    FROM facturas f 
                    ORDER BY f.id DESC";
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al listar facturas: " . $e->getMessage());
        }
    }

    public function findById(int $id): ?array
    {
        try {
            $sql = "SELECT * FROM facturas WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener factura: " . $e->getMessage());
        }
    }

    public function create(array $data): bool
    {
        try {
            $sql = "INSERT INTO facturas 
                    (numero_factura, fecha, cliente_nombre, cliente_identificacion, 
                     cliente_direccion, cliente_telefono, cliente_email, 
                     subtotal, impuesto, total, estado, notas) 
                    VALUES 
                    (:numero_factura, :fecha, :cliente_nombre, :cliente_identificacion,
                     :cliente_direccion, :cliente_telefono, :cliente_email,
                     :subtotal, :impuesto, :total, :estado, :notas)";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindParam(':numero_factura', $data['numero_factura']);
            $stmt->bindParam(':fecha', $data['fecha']);
            $stmt->bindParam(':cliente_nombre', $data['cliente_nombre']);
            $stmt->bindParam(':cliente_identificacion', $data['cliente_identificacion']);
            $stmt->bindParam(':cliente_direccion', $data['cliente_direccion']);
            $stmt->bindParam(':cliente_telefono', $data['cliente_telefono']);
            $stmt->bindParam(':cliente_email', $data['cliente_email']);
            $stmt->bindParam(':subtotal', $data['subtotal']);
            $stmt->bindParam(':impuesto', $data['impuesto']);
            $stmt->bindParam(':total', $data['total']);
            $stmt->bindParam(':estado', $data['estado']);
            $stmt->bindParam(':notas', $data['notas']);

            return $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                throw new Exception("El número de factura ya existe");
            }
            throw new Exception("Error al crear factura: " . $e->getMessage());
        }
    }

    public function update(int $id, array $data): bool
    {
        try {
            $sql = "UPDATE facturas SET 
                    fecha = :fecha,
                    cliente_nombre = :cliente_nombre,
                    cliente_identificacion = :cliente_identificacion,
                    cliente_direccion = :cliente_direccion,
                    cliente_telefono = :cliente_telefono,
                    cliente_email = :cliente_email,
                    subtotal = :subtotal,
                    impuesto = :impuesto,
                    total = :total,
                    estado = :estado,
                    notas = :notas
                    WHERE id = :id";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':fecha', $data['fecha']);
            $stmt->bindParam(':cliente_nombre', $data['cliente_nombre']);
            $stmt->bindParam(':cliente_identificacion', $data['cliente_identificacion']);
            $stmt->bindParam(':cliente_direccion', $data['cliente_direccion']);
            $stmt->bindParam(':cliente_telefono', $data['cliente_telefono']);
            $stmt->bindParam(':cliente_email', $data['cliente_email']);
            $stmt->bindParam(':subtotal', $data['subtotal']);
            $stmt->bindParam(':impuesto', $data['impuesto']);
            $stmt->bindParam(':total', $data['total']);
            $stmt->bindParam(':estado', $data['estado']);
            $stmt->bindParam(':notas', $data['notas']);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar factura: " . $e->getMessage());
        }
    }

    public function delete(int $id): bool
    {
        try {
            // Primero eliminar los detalles
            $sqlDetalle = "DELETE FROM detalle_facturas WHERE factura_id = :id";
            $stmtDetalle = $this->conn->prepare($sqlDetalle);
            $stmtDetalle->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtDetalle->execute();

            // Luego eliminar la factura
            $sql = "DELETE FROM facturas WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar factura: " . $e->getMessage());
        }
    }

    public function getLastInsertId(): int
    {
        return (int)$this->conn->lastInsertId();
    }

    public function numeroFacturaExists(string $numero, ?int $excludeId = null): bool
    {
        try {
            if ($excludeId !== null) {
                $sql = "SELECT COUNT(*) FROM facturas WHERE numero_factura = :numero AND id != :id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':numero', $numero);
                $stmt->bindParam(':id', $excludeId, PDO::PARAM_INT);
            } else {
                $sql = "SELECT COUNT(*) FROM facturas WHERE numero_factura = :numero";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':numero', $numero);
            }
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new Exception("Error al verificar número de factura: " . $e->getMessage());
        }
    }

    public function generarNumeroFactura(): string
    {
        try {
            $sql = "SELECT numero_factura FROM facturas ORDER BY id DESC LIMIT 1";
            $stmt = $this->conn->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                // Extraer el número y sumar 1
                $ultimoNumero = (int)substr($result['numero_factura'], 4);
                $nuevoNumero = $ultimoNumero + 1;
            } else {
                $nuevoNumero = 1;
            }

            return 'FAC-' . str_pad($nuevoNumero, 6, '0', STR_PAD_LEFT);
        } catch (PDOException $e) {
            throw new Exception("Error al generar número de factura: " . $e->getMessage());
        }
    }

    public function cambiarEstado(int $id, string $estado): bool
    {
        try {
            $sql = "UPDATE facturas SET estado = :estado WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estado);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al cambiar estado: " . $e->getMessage());
        }
    }

    public function findByEstado(string $estado): array
    {
        try {
            $sql = "SELECT * FROM facturas WHERE estado = :estado ORDER BY fecha DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':estado', $estado);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al buscar por estado: " . $e->getMessage());
        }
    }

    public function findByFechas(string $fechaInicio, string $fechaFin): array
    {
        try {
            $sql = "SELECT * FROM facturas 
                    WHERE fecha BETWEEN :fecha_inicio AND :fecha_fin 
                    ORDER BY fecha DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':fecha_inicio', $fechaInicio);
            $stmt->bindParam(':fecha_fin', $fechaFin);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al buscar por fechas: " . $e->getMessage());
        }
    }
}
