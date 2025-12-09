<?php

class ProductoController {

    private $repo;
    private $validator;

    public function __construct($repo, $validator) {
        $this->repo = $repo;
        $this->validator = $validator;
    }

    public function listar() {
        return $this->repo->listar();
    }

    public function registrar($data) {
        $errores = $this->validator->validarRegistro($data);

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            return false;
        }

        $this->repo->registrar($data);
        $_SESSION['success'] = "Producto registrado correctamente.";
    }

    public function eliminar($id) {
        $this->repo->eliminar($id);
        $_SESSION['success'] = "Producto eliminado.";
    }

    public function actualizar($id, $data) {

    $errores = $this->validator->validarRegistro($data);

    if (!empty($errores)) {
        return $errores;
    }

    return $this->repo->actualizar($id, [
        'nombre' => $data['nombre'],
        'descripcion' => $data['descripcion'],
        'precio' => $data['precio'],
        'stock' => $data['stock'],
        'proveedor_id' => $data['proveedor_id']
    ]);
    }

}
