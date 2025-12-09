<?php
require_once __DIR__ . '/../interfaces/IRepository.php';

class UsuarioRepository implements IRepository
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function findAll(): array
    {
        try {
            $sql = "SELECT id, nombre, correo, rol, creado_en FROM usuarios ORDER BY id DESC";
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al listar usuarios: " . $e->getMessage());
        }
    }

    public function findById(int $id): ?array
    {
        try {
            $sql = "SELECT * FROM usuarios WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener usuario: " . $e->getMessage());
        }
    }

    public function findByEmail(string $email): ?array
    {
        try {
            $sql = "SELECT * FROM usuarios WHERE correo = :correo LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':correo', $email);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            throw new Exception("Error al buscar usuario por email: " . $e->getMessage());
        }
    }

    public function create(array $data): bool
    {
        try {
            $sql = "INSERT INTO usuarios (nombre, correo, clave, rol) 
                    VALUES (:nombre, :correo, :clave, :rol)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nombre', $data['nombre']);
            $stmt->bindParam(':correo', $data['correo']);
            $stmt->bindParam(':clave', $data['clave']);
            $stmt->bindParam(':rol', $data['rol']);

            return $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                throw new Exception("El correo ya está registrado");
            }
            throw new Exception("Error al crear usuario: " . $e->getMessage());
        }
    }

    public function update(int $id, array $data): bool
    {
        try {
            if (!empty($data['clave'])) {
                $sql = "UPDATE usuarios SET nombre = :nombre, correo = :correo, 
                        clave = :clave, rol = :rol WHERE id = :id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':clave', $data['clave']);
            } else {
                $sql = "UPDATE usuarios SET nombre = :nombre, correo = :correo, 
                        rol = :rol WHERE id = :id";
                $stmt = $this->conn->prepare($sql);
            }

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $data['nombre']);
            $stmt->bindParam(':correo', $data['correo']);
            $stmt->bindParam(':rol', $data['rol']);

            return $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                throw new Exception("El correo ya está registrado por otro usuario");
            }
            throw new Exception("Error al actualizar usuario: " . $e->getMessage());
        }
    }

    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM usuarios WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar usuario: " . $e->getMessage());
        }
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        try {
            if ($excludeId !== null) {
                $sql = "SELECT COUNT(*) FROM usuarios WHERE correo = :correo AND id != :id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':correo', $email);
                $stmt->bindParam(':id', $excludeId, PDO::PARAM_INT);
            } else {
                $sql = "SELECT COUNT(*) FROM usuarios WHERE correo = :correo";
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
