<?php

class VentaValidator {

    public function validar($data) {
        $errors = [];

        if (empty($data['factura_id'])) {
            $errors[] = "Factura ID es obligatorio";
        }

        if (empty($data['producto_id'])) {
            $errors[] = "Producto es obligatorio";
        }

        if (empty($data['cantidad']) || $data['cantidad'] <= 0) {
            $errors[] = "Cantidad inválida";
        }

        if (empty($data['precio_unit']) || $data['precio_unit'] <= 0) {
            $errors[] = "Precio unitario inválido";
        }

        return $errors;
    }
}
