<?php

class VentaController {

    private $repo;
    private $validator;

    public function __construct($repo, $validator) {
        $this->repo = $repo;
        $this->validator = $validator;
    }

    public function listar() {
        return $this->repo->listar();
    }

    public function buscarPorId($id) {
        return $this->repo->buscarPorId($id);
    }

    public function registrar($data) {

        $errores = $this->validator->validar($data);

        if (!empty($errores)) {
            return $errores;
        }

        return $this->repo->registrar($data);
    }

    public function actualizar($id, $data) {

        $errores = $this->validator->validar($data);

        if (!empty($errores)) {
            return $errores;
        }

        return $this->repo->actualizar($id, $data);
    }

    public function eliminar($id) {
        return $this->repo->eliminar($id);
    }
}
