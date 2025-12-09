<?php

interface IVentaRepository {
    public function listar();
    public function buscarPorId($id);
    public function registrar($data);
    public function actualizar($id, $data);
    public function eliminar($id);
}
