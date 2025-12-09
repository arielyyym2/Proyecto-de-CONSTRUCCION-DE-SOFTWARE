<?php
require_once __DIR__ . '/../interfaces/IValidator.php';

class ProveedorValidator implements IValidator
{
    private array $errors = [];

    public function validate(array $data): array
    {
        $this->errors = [];

        // Validar nombre
        if (empty($data['nombre']) || strlen(trim($data['nombre'])) < 3) {
            $this->errors['nombre'] = 'El nombre debe tener al menos 3 caracteres';
        }

        // Validar empresa
        if (empty($data['empresa']) || strlen(trim($data['empresa'])) < 2) {
            $this->errors['empresa'] = 'La empresa debe tener al menos 2 caracteres';
        }

        // Validar teléfono (opcional, pero si existe debe ser válido)
        if (!empty($data['telefono'])) {
            $telefono = trim($data['telefono']);
            if (strlen($telefono) > 20) {
                $this->errors['telefono'] = 'El teléfono no puede tener más de 20 caracteres';
            } elseif (!preg_match('/^[\d\s\-\+\(\)]+$/', $telefono)) {
                $this->errors['telefono'] = 'El teléfono solo puede contener números, espacios, guiones, paréntesis y +';
            }
        }

        // Validar correo
        if (empty($data['correo'])) {
            $this->errors['correo'] = 'El correo es obligatorio';
        } elseif (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['correo'] = 'El correo no es válido';
        }

        return $this->errors;
    }

    public function isValid(array $data): bool
    {
        return empty($this->validate($data));
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
