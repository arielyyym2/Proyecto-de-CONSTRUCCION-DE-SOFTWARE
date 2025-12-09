<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../repositories/UsuarioRepository.php';
require_once __DIR__ . '/../validators/UsuarioValidator.php';

class UsuarioController extends BaseController
{
    private UsuarioRepository $repository;
    private UsuarioValidator $validator;

    public function __construct(UsuarioRepository $repository, UsuarioValidator $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
    }

    public function listar(): array
    {
        try {
            return $this->repository->findAll();
        } catch (Exception $e) {
            $this->showError($e->getMessage());
            return [];
        }
    }

    public function obtenerPorId(int $id): ?array
    {
        try {
            return $this->repository->findById($id);
        } catch (Exception $e) {
            $this->showError($e->getMessage());
            return null;
        }
    }

    public function registrar(array $datos): bool
    {
        try {
            $datos = $this->sanitizeInput($datos);
            $datos['validar_clave'] = true; // Marcar que debe validar la clave

            $errors = $this->validator->validate($datos);
            if (!empty($errors)) {
                $this->showErrors($errors);
                return false;
            }

            // Hashear la contraseña
            $datos['clave'] = password_hash($datos['clave'], PASSWORD_DEFAULT);

            if ($this->repository->create($datos)) {
                $this->showSuccess("Usuario registrado correctamente");
                return true;
            }

            return false;
        } catch (Exception $e) {
            $this->showError($e->getMessage());
            return false;
        }
    }

    public function actualizar(int $id, array $datos): bool
    {
        try {
            $datos = $this->sanitizeInput($datos);

            // Solo validar clave si se proporciona
            if (!empty($datos['clave'])) {
                $datos['validar_clave'] = true;
                $datos['clave'] = password_hash($datos['clave'], PASSWORD_DEFAULT);
            }

            $errors = $this->validator->validate($datos);
            if (!empty($errors)) {
                $this->showErrors($errors);
                return false;
            }

            if ($this->repository->update($id, $datos)) {
                $this->showSuccess("Usuario actualizado correctamente");
                return true;
            }

            return false;
        } catch (Exception $e) {
            $this->showError($e->getMessage());
            return false;
        }
    }

    public function eliminar(int $id): bool
    {
        try {
            if ($this->repository->delete($id)) {
                $this->showSuccess("Usuario eliminado correctamente");
                return true;
            }
            return false;
        } catch (Exception $e) {
            $this->showError($e->getMessage());
            return false;
        }
    }
}
