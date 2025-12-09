<?php

class ProductoValidator {

    public function validarRegistro($data) {
        $errores = [];

        if (empty($data['nombre'])) $errores[] = "El nombre es obligatorio.";
        if (!is_numeric($data['precio'])) $errores[] = "El precio debe ser numérico.";
        if (!is_numeric($data['stock'])) $errores[] = "El stock debe ser numérico.";

        return $errores;
    }
}
