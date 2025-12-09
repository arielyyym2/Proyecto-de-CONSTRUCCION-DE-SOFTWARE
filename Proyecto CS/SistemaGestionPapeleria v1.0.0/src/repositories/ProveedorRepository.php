<?php
require_once __DIR__ . '/../interfaces/IRepository.php';

class ProveedorRepository implements IRepository
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function findAll(): array
    {
        try {
            $sql = "SELECT * FROM proveedores ORDER BY id DESC";
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al listar proveedores: " . $e->getMessage());
        }
    }

    public function findById(int $id): ?array
    {
        try {
            $sql = "SELECT * FROM proveedores WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener proveedor: " . $e->getMessage());
        }
    }

    public function create(array $data): bool
    {
        try {
            $sql = "INSERT INTO proveedores (nombre, empresa, telefono, correo, direccion) 
                    VALUES (:nombre, :empresa, :telefono, :correo, :direccion)";

            $stmt = $this->conn->prepare($sql);

            $telefono = !empty($data['telefono']) ? trim($data['telefono']) : null;
            $direccion = !empty($data['direccion']) ? trim($data['direccion']) : null;

            $stmt->bindParam(':nombre', $data['nombre']);
            $stmt->bindParam(':empresa', $data['empresa']);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':correo', $data['correo']);
            $stmt->bindParam(':direccion', $direccion);

            return $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                throw new Exception("El correo ya está registrado");
            }
            throw new Exception("Error al crear proveedor: " . $e->getMessage());
        }
    }

    public function listar() {
    $sql = "SELECT * FROM proveedores";
    $stmt = $this->conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function update(int $id, array $data): bool
    {
        try {
            $sql = "UPDATE proveedores SET nombre = :nombre, empresa = :empresa, 
                    telefono = :telefono, correo = :correo, direccion = :direccion 
                    WHERE id = :id";

            $stmt = $this->conn->prepare($sql);

            $telefono = !empty($data['telefono']) ? trim($data['telefono']) : null;
            $direccion = !empty($data['direccion']) ? trim($data['direccion']) : null;

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $data['nombre']);
            $stmt->bindParam(':empresa', $data['empresa']);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':correo', $data['correo']);
            $stmt->bindParam(':direccion', $direccion);

            return $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                throw new Exception("El correo ya está registrado por otro proveedor");
            }
            throw new Exception("Error al actualizar proveedor: " . $e->getMessage());
        }
    }

    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM proveedores WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar proveedor: " . $e->getMessage());
        }
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        try {
            if ($excludeId !== null) {
                $sql = "SELECT COUNT(*) FROM proveedores WHERE correo = :correo AND id != :id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':correo', $email);
                $stmt->bindParam(':id', $excludeId, PDO::PARAM_INT);
            } else {
                $sql = "SELECT COUNT(*) FROM proveedores WHERE correo = :correo";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':correo', $email);
            }
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new Exception("Error al verificar email: " . $e->getMessage());
        }
    }
}
