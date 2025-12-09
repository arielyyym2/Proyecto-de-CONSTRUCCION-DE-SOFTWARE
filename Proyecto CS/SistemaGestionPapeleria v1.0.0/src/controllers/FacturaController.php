<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../repositories/FacturaRepository.php';
require_once __DIR__ . '/../repositories/DetalleFacturaRepository.php';
require_once __DIR__ . '/../validators/FacturaValidator.php';

class FacturaController extends BaseController
{
    private FacturaRepository $repository;
    private DetalleFacturaRepository $detalleRepository;
    private FacturaValidator $validator;

    public function __construct(
        FacturaRepository $repository,
        DetalleFacturaRepository $detalleRepository,
        FacturaValidator $validator
    ) {
        $this->repository = $repository;
        $this->detalleRepository = $detalleRepository;
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

    public function obtenerDetalles(int $facturaId): array
    {
        try {
            return $this->detalleRepository->findByFacturaId($facturaId);
        } catch (Exception $e) {
            $this->showError($e->getMessage());
            return [];
        }
    }

    public function registrar(array $datos, array $detalles): bool
    {
        try {
            $datos = $this->sanitizeInput($datos);

            // Validar cabecera
            $errors = $this->validator->validate($datos);

            // Validar detalles
            $detalleErrors = $this->validator->validateDetalles($detalles);
            $errors = array_merge($errors, $detalleErrors);

            if (!empty($errors)) {
                $this->showErrors($errors);
                return false;
            }

            // Verificar que el número de factura no exista
            if ($this->repository->numeroFacturaExists($datos['numero_factura'])) {
                $this->showError("El número de factura ya existe");
                return false;
            }

            // Iniciar transacción
            $this->repository->beginTransaction();

            // Crear factura
            if ($this->repository->create($datos)) {
                $facturaId = $this->repository->getLastInsertId();

                // Crear detalles
                $this->detalleRepository->createMultiple($facturaId, $detalles);

                $this->repository->commit();
                $this->showSuccess("Factura registrada correctamente");
                return true;
            }

            $this->repository->rollBack();
            return false;
        } catch (Exception $e) {
            $this->repository->rollBack();
            $this->showError($e->getMessage());
            return false;
        }
    }

    public function actualizar(int $id, array $datos, array $detalles): bool
    {
        try {
            $datos = $this->sanitizeInput($datos);

            // Validar cabecera
            $errors = $this->validator->validate($datos);

            // Validar detalles
            $detalleErrors = $this->validator->validateDetalles($detalles);
            $errors = array_merge($errors, $detalleErrors);

            if (!empty($errors)) {
                $this->showErrors($errors);
                return false;
            }

            // Iniciar transacción
            $this->repository->beginTransaction();

            // Actualizar factura
            if ($this->repository->update($id, $datos)) {
                // Actualizar detalles
                $this->detalleRepository->createMultiple($id, $detalles);

                $this->repository->commit();
                $this->showSuccess("Factura actualizada correctamente");
                return true;
            }

            $this->repository->rollBack();
            return false;
        } catch (Exception $e) {
            $this->repository->rollBack();
            $this->showError($e->getMessage());
            return false;
        }
    }

    public function eliminar(int $id): bool
    {
        try {
            if ($this->repository->delete($id)) {
                $this->showSuccess("Factura eliminada correctamente");
                return true;
            }
            return false;
        } catch (Exception $e) {
            $this->showError($e->getMessage());
            return false;
        }
    }

    public function cambiarEstado(int $id, string $estado): bool
    {
        try {
            $estadosValidos = ['pendiente', 'pagada', 'anulada'];
            if (!in_array($estado, $estadosValidos)) {
                $this->showError("Estado inválido");
                return false;
            }

            if ($this->repository->cambiarEstado($id, $estado)) {
                $this->showSuccess("Estado actualizado correctamente");
                return true;
            }
            return false;
        } catch (Exception $e) {
            $this->showError($e->getMessage());
            return false;
        }
    }

    public function generarNumeroFactura(): string
    {
        try {
            return $this->repository->generarNumeroFactura();
        } catch (Exception $e) {
            $this->showError($e->getMessage());
            return '';
        }
    }

    public function buscarPorEstado(string $estado): array
    {
        try {
            return $this->repository->findByEstado($estado);
        } catch (Exception $e) {
            $this->showError($e->getMessage());
            return [];
        }
    }

    public function buscarPorFechas(string $fechaInicio, string $fechaFin): array
    {
        try {
            return $this->repository->findByFechas($fechaInicio, $fechaFin);
        } catch (Exception $e) {
            $this->showError($e->getMessage());
            return [];
        }
    }

    public function calcularTotales(array $detalles, float $porcentajeImpuesto = 15): array
    {
        $subtotal = 0;

        foreach ($detalles as $detalle) {
            $subtotal += $detalle['cantidad'] * $detalle['precio_unitario'];
        }

        $impuesto = $subtotal * ($porcentajeImpuesto / 100);
        $total = $subtotal + $impuesto;

        return [
            'subtotal' => round($subtotal, 2),
            'impuesto' => round($impuesto, 2),
            'total' => round($total, 2)
        ];
    }
}
