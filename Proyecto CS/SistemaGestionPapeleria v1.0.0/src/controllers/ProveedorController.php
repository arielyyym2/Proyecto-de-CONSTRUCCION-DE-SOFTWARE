<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../repositories/ProveedorRepository.php';
require_once __DIR__ . '/../validators/ProveedorValidator.php';

class ProveedorController extends BaseController
{
    private ProveedorRepository $repository;
    private ProveedorValidator $validator;

    public function __construct(ProveedorRepository $repository, ProveedorValidator $validator)
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

            $errors = $this->validator->validate($datos);
            if (!empty($errors)) {
                $this->showErrors($errors);
                return false;
            }

            if ($this->repository->create($datos)) {
                $this->showSuccess("Proveedor registrado correctamente");
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

            $errors = $this->validator->validate($datos);
            if (!empty($errors)) {
                $this->showErrors($errors);
                return false;
            }

            if ($this->repository->update($id, $datos)) {
                $this->showSuccess("Proveedor actualizado correctamente");
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
                $this->showSuccess("Proveedor eliminado correctamente");
                return true;
            }
            return false;
        } catch (Exception $e) {
            $this->showError($e->getMessage());
            return false;
        }
    }
}
