<?php
require_once __DIR__ . '/../interfaces/IValidator.php';

class FacturaValidator implements IValidator
{
    private array $errors = [];

    public function validate(array $data): array
    {
        $this->errors = [];

        // Validar número de factura
        if (empty($data['numero_factura'])) {
            $this->errors['numero_factura'] = 'El número de factura es obligatorio';
        }

        // Validar fecha
        if (empty($data['fecha'])) {
            $this->errors['fecha'] = 'La fecha es obligatoria';
        } elseif (!$this->isValidDate($data['fecha'])) {
            $this->errors['fecha'] = 'La fecha no es válida';
        }

        // Validar cliente
        if (empty($data['cliente_nombre']) || strlen(trim($data['cliente_nombre'])) < 3) {
            $this->errors['cliente_nombre'] = 'El nombre del cliente debe tener al menos 3 caracteres';
        }

        if (empty($data['cliente_identificacion'])) {
            $this->errors['cliente_identificacion'] = 'La identificación del cliente es obligatoria';
        }

        // Validar email del cliente (opcional pero debe ser válido)
        if (!empty($data['cliente_email']) && !filter_var($data['cliente_email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['cliente_email'] = 'El email del cliente no es válido';
        }

        // Validar teléfono del cliente (opcional pero debe ser válido)
        if (!empty($data['cliente_telefono'])) {
            $telefono = trim($data['cliente_telefono']);
            if (strlen($telefono) > 20) {
                $this->errors['cliente_telefono'] = 'El teléfono no puede tener más de 20 caracteres';
            } elseif (!preg_match('/^[\d\s\-\+\(\)]+$/', $telefono)) {
                $this->errors['cliente_telefono'] = 'El teléfono solo puede contener números y símbolos válidos';
            }
        }

        // Validar montos
        if (!isset($data['subtotal']) || !is_numeric($data['subtotal']) || $data['subtotal'] < 0) {
            $this->errors['subtotal'] = 'El subtotal debe ser un número válido';
        }

        if (!isset($data['impuesto']) || !is_numeric($data['impuesto']) || $data['impuesto'] < 0) {
            $this->errors['impuesto'] = 'El impuesto debe ser un número válido';
        }

        if (!isset($data['total']) || !is_numeric($data['total']) || $data['total'] < 0) {
            $this->errors['total'] = 'El total debe ser un número válido';
        }

        // Validar estado
        $estadosValidos = ['pendiente', 'pagada', 'anulada'];
        if (empty($data['estado']) || !in_array($data['estado'], $estadosValidos)) {
            $this->errors['estado'] = 'El estado debe ser: pendiente, pagada o anulada';
        }

        return $this->errors;
    }

    public function validateDetalles(array $detalles): array
    {
        $errors = [];

        if (empty($detalles)) {
            $errors[] = 'La factura debe tener al menos un producto';
            return $errors;
        }

        foreach ($detalles as $index => $detalle) {
            $lineaErrors = [];

            if (empty($detalle['producto_nombre'])) {
                $lineaErrors[] = "Línea " . ($index + 1) . ": El nombre del producto es obligatorio";
            }

            if (!isset($detalle['cantidad']) || !is_numeric($detalle['cantidad']) || $detalle['cantidad'] <= 0) {
                $lineaErrors[] = "Línea " . ($index + 1) . ": La cantidad debe ser mayor a 0";
            }

            if (!isset($detalle['precio_unitario']) || !is_numeric($detalle['precio_unitario']) || $detalle['precio_unitario'] < 0) {
                $lineaErrors[] = "Línea " . ($index + 1) . ": El precio unitario debe ser válido";
            }

            $errors = array_merge($errors, $lineaErrors);
        }

        return $errors;
    }

    public function isValid(array $data): bool
    {
        return empty($this->validate($data));
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function isValidDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}
