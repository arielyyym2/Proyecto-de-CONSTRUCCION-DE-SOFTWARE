<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../src/config/database.php";
require_once __DIR__ . "/../../../src/repositories/ProductoRepository.php";
require_once __DIR__ . "/../../../src/repositories/ProveedorRepository.php";
require_once __DIR__ . "/../../../src/validators/ProductoValidator.php";
require_once __DIR__ . "/../../../src/controllers/ProductoController.php";

$database = new Database();
$conn = $database->connect();

$productoRepo = new ProductoRepository($conn);
$proveedorRepo = new ProveedorRepository($conn);
$validator = new ProductoValidator();

$productoController = new ProductoController($productoRepo, $validator);

// ================================
// Cargar producto por ID
// ================================
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];
$producto = $productoRepo->buscarPorId($id);

if (!$producto) {
    echo "Producto no encontrado";
    exit;
}

// ================================
// Guardar cambios (POST)
// ================================
if (isset($_POST['btnActualizar'])) {
    $productoController->actualizar($id, $_POST);
    header("Location: index.php");
    exit;
}

$proveedores = $proveedorRepo->listar();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-4">

    <h1 class="mb-4">Editar Producto</h1>

    <div class="mb-3">
        <a href="index.php" class="btn btn-secondary">
            Volver
        </a>
    </div>

    <form method="POST" class="col-md-6">

        <div class="mb-2">
            <label>Nombre</label>
            <input type="text" 
                   class="form-control" 
                   name="nombre"
                   value="<?= htmlspecialchars($producto['nombre']) ?>"
                   required>
        </div>

        <div class="mb-2">
            <label>Descripción</label>
            <textarea class="form-control" name="descripcion"><?= htmlspecialchars($producto['descripcion']) ?></textarea>
        </div>

        <div class="mb-2">
            <label>Precio</label>
            <input type="number" step="0.01"
                   class="form-control"
                   name="precio"
                   value="<?= $producto['precio'] ?>"
                   required>
        </div>

        <div class="mb-2">
            <label>Stock</label>
            <input type="number"
                   class="form-control"
                   name="stock"
                   value="<?= $producto['stock'] ?>"
                   required>
        </div>

        <div class="mb-2">
            <label>Proveedor</label>
            <select class="form-control" name="proveedor_id">
                <?php foreach ($proveedores as $prov): ?>
                    <option value="<?= $prov['id'] ?>"
                        <?= $producto['proveedor_id'] == $prov['id'] ? 'selected' : '' ?>>
                        <?= $prov['empresa'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button class="btn btn-primary mt-3" name="btnActualizar">Guardar Cambios</button>

    </form>

</div>

</body>
</html>
