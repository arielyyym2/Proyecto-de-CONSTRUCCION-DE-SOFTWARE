<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../src/config/database.php";
require_once __DIR__ . "/../../../src/repositories/ProductoRepository.php";
require_once __DIR__ . "/../../../src/validators/ProductoValidator.php";
require_once __DIR__ . "/../../../src/controllers/ProductoController.php";
require_once __DIR__ . "/../../../src/repositories/ProveedorRepository.php";

$database = new Database();
$conn = $database->connect();

$productoRepo = new ProductoRepository($conn);
$validator = new ProductoValidator();
$productoController = new ProductoController($productoRepo, $validator);

$proveedorRepo = new ProveedorRepository($conn);

// ===== Eliminar producto =====
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    $productoController->eliminar((int)$_GET['id']);
    header("Location: index.php");
    exit;
}

// ===== Registrar producto =====
if (isset($_POST['btnRegistrar'])) {
    $productoController->registrar($_POST);
}

$proveedores = $proveedorRepo->listar();
$productos = $productoController->listar();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-4">

    <h1 class="mb-4">Gestión de Productos</h1>

   <div class="mb-3">
        <a href="../../index.php" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Volver al Inicio
        </a>
    </div>


    <div class="row">
        <!-- Formulario -->
        <form class="col-md-4" method="POST">

            <h4 class="mb-3">Registrar Producto</h4>

            <div class="mb-2">
                <label>Nombre</label>
                <input type="text" class="form-control" name="nombre" required>
            </div>

            <div class="mb-2">
                <label>Descripción</label>
                <textarea class="form-control" name="descripcion"></textarea>
            </div>

            <div class="mb-2">
                <label>Precio</label>
                <input type="number" step="0.01" class="form-control" name="precio" required>
            </div>

            <div class="mb-2">
                <label>Stock</label>
                <input type="number" class="form-control" name="stock" required>
            </div>

            <div class="mb-2">
                <label>Proveedor</label>
                <select class="form-control" name="proveedor_id">
                    <?php foreach ($proveedores as $prov): ?>
                        <option value="<?= $prov['id'] ?>"><?= $prov['empresa'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button class="btn btn-primary w-100" name="btnRegistrar">Registrar</button>

        </form>

        <!-- Tabla -->
        <div class="col-md-8">
            <table class="table table-bordered table-striped mt-3">
                <thead class="table-info">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Proveedor</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (count($productos) > 0): ?>
                        <?php foreach ($productos as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><?= htmlspecialchars($p['nombre']) ?></td>
                            <td><?= $p['precio'] ?></td>
                            <td><?= $p['stock'] ?></td>
                            <td><?= $p['proveedor'] ?? 'Sin asignar' ?></td>
                            <td>
                                <a href="editar.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="index.php?action=delete&id=<?= $p['id'] ?>"
                                   onclick="return confirm('¿Seguro?')"
                                   class="btn btn-danger btn-sm">Eliminar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">No hay productos registrados</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>
