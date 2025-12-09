<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../src/config/database.php";
require_once __DIR__ . "/../../../src/repositories/VentaRepository.php";
require_once __DIR__ . "/../../../src/validators/VentaValidator.php";
require_once __DIR__ . "/../../../src/controllers/VentaController.php";
require_once __DIR__ . "/../../../src/repositories/ProductoRepository.php";

$database = new Database();
$conn = $database->connect();

$repo = new VentaRepository($conn);
$validator = new VentaValidator();
$controller = new VentaController($repo, $validator);

$productoRepo = new ProductoRepository($conn);
$productos = $productoRepo->listar();

// Verificar ID
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];
$venta = $repo->buscarPorId($id);

if (!$venta) {
    header("Location: index.php");
    exit;
}

// Actualizar venta
if (isset($_POST['btnActualizar'])) {
    if ($controller->actualizar($id, $_POST)) {
        $_SESSION['success'] = "Venta actualizada correctamente";
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Venta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-4">

    <h2 class="mb-3">Editar Venta</h2>

    <a href="index.php" class="btn btn-secondary mb-3">Volver</a>

    <form method="POST" class="card p-4 shadow">

        <div class="mb-3">
            <label class="form-label">Factura ID</label>
            <input type="number" name="factura_id" class="form-control" 
                   value="<?= $venta['factura_id'] ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Producto</label>
            <select name="producto_id" class="form-select">
                <?php foreach ($productos as $p): ?>
                    <option value="<?= $p['id'] ?>"
                        <?= $p['id'] == $venta['producto_id'] ? 'selected' : '' ?>>
                        <?= $p['nombre'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Cantidad</label>
            <input type="number" name="cantidad" class="form-control" 
                   value="<?= $venta['cantidad'] ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Precio Unitario</label>
            <input type="number" step="0.01" name="precio_unit" class="form-control"
                   value="<?= $venta['precio_unit'] ?>" required>
        </div>

        <button class="btn btn-primary w-100" name="btnActualizar">
            Actualizar Venta
        </button>

    </form>

</div>

</body>
</html>
