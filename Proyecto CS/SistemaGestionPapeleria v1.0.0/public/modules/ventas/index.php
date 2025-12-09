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

$database = new Database();
$conn = $database->connect();

$repo = new VentaRepository($conn);
$validator = new VentaValidator();
$controller = new VentaController($repo, $validator);

// Obtener datos
$ventas = $controller->listar();

// Mostrar alerta de éxito
$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-4">

    <h1 class="mb-4">Gestión de Ventas</h1>

    <!-- Volver -->
    <div class="mb-3">
        <a href="../../index.php" class="btn btn-secondary">
            Volver al Inicio
        </a>
        <a href="registrar.php" class="btn btn-primary ms-2">
            Registrar nueva venta
        </a>
    </div>

    <!-- Mensaje -->
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <!-- Tabla -->
    <div class="card shadow">
        <div class="card-header bg-info text-dark fw-bold">
            Listado de Ventas
        </div>

        <div class="card-body p-0">
            <table class="table table-striped table-bordered m-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Factura</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unit</th>
                        <th>Subtotal</th>
                        <th width="150">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (count($ventas) > 0): ?>
                    <?php foreach ($ventas as $v): ?>
                        <tr>
                            <td><?= $v['id']; ?></td>
                            <td><?= $v['factura_id']; ?></td>
                            <td><?= htmlspecialchars($v['producto']); ?></td>
                            <td><?= $v['cantidad']; ?></td>
                            <td>$<?= number_format($v['precio_unit'], 2); ?></td>
                            <td>$<?= number_format($v['subtotal'], 2); ?></td>

                            <td>
                                <a href="editar.php?id=<?= $v['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="eliminar.php?id=<?= $v['id'] ?>"
                                   onclick="return confirm('¿Seguro de eliminar esta venta?')"
                                   class="btn btn-danger btn-sm">
                                   Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center">No hay ventas registradas</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>
