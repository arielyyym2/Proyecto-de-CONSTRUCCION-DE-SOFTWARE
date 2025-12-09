<?php
require_once __DIR__ . '/../interfaces/IValidator.php';

class UsuarioValidator implements IValidator
{
    private array $errors = [];

    public function validate(array $data): array
    {
        $this->errors = [];

        // Validar nombre
        if (empty($data['nombre']) || strlen(trim($data['nombre'])) < 3) {
            $this->errors['nombre'] = 'El nombre debe tener al menos 3 caracteres';
        }

        // Validar correo
        if (empty($data['correo'])) {
            $this->errors['correo'] = 'El correo es obligatorio';
        } elseif (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['correo'] = 'El correo no es válido';
        }

        // Validar clave (solo al crear, no al actualizar si está vacía)
        if (isset($data['validar_clave']) && $data['validar_clave'] === true) {
            if (empty($data['clave'])) {
                $this->errors['clave'] = 'La contraseña es obligatoria';
            } elseif (strlen($data['clave']) < 6) {
                $this->errors['clave'] = 'La contraseña debe tener al menos 6 caracteres';
            }
        }

        // Validar rol
        $rolesValidos = ['admin', 'empleado', 'vendedor', 'usuario'];
        if (empty($data['rol']) || !in_array($data['rol'], $rolesValidos)) {
            $this->errors['rol'] = 'El rol no es válido';
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
