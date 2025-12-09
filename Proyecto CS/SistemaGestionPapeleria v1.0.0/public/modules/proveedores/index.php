<?php
session_start();

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

// Cargar dependencias
require_once __DIR__ . "/../../../src/config/database.php";
require_once __DIR__ . "/../../../src/repositories/ProveedorRepository.php";
require_once __DIR__ . "/../../../src/validators/ProveedorValidator.php";
require_once __DIR__ . "/../../../src/controllers/ProveedorController.php";

// Crear conexión a la base de datos
$database = new Database();
$conn = $database->connect();

// Crear repositorio y validador
$proveedorRepository = new ProveedorRepository($conn);
$proveedorValidator = new ProveedorValidator();

// Instanciar el controlador correctamente
$proveedorController = new ProveedorController($proveedorRepository, $proveedorValidator);

// ===== Manejar eliminación =====
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    $proveedorController->eliminar((int)$_GET['id']);
    header("Location: index.php");
    exit;
}

// ===== Manejar registro =====
if (isset($_POST['btnRegistrar'])) {
    $proveedorController->registrar($_POST);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Proveedores - Papelería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/25433711f4.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center p-3 bg-light">
            <h1>Gestión de Proveedores</h1>
            <a href="../../index.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Volver al Menú
            </a>
        </div>

        <div class="container-fluid row mt-3">
            <!-- Formulario de registro -->
            <form class="col-md-4 p-3 border rounded bg-light" method="POST">
                <h3 class="text-center text-secondary mb-4">Registro de Proveedores</h3>

                <div class="mb-3">
                    <label for="empresa" class="form-label">Empresa</label>
                    <input type="text" class="form-control" name="empresa" required>
                </div>

                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del proveedor</label>
                    <input type="text" class="form-control" name="nombre" required>
                </div>

                <div class="mb-3">
                    <label for="correo" class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control" name="correo" required>
                </div>

                <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="text" class="form-control" name="telefono" required>
                </div>

                <div class="mb-3">
                    <label for="direccion" class="form-label">Dirección</label>
                    <input type="text" class="form-control" name="direccion" required>
                </div>

                <button type="submit" class="btn btn-primary w-100" name="btnRegistrar" value="ok">
                    <i class="fa-solid fa-plus"></i> Registrar Proveedor
                </button>
            </form>

            <!-- Tabla de proveedores -->
            <div class="col-md-8 p-4">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-info">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Empresa</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Correo</th>
                                <th scope="col">Teléfono</th>
                                <th scope="col">Dirección</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $proveedores = $proveedorController->listar();
                            if (count($proveedores) > 0) {
                                foreach ($proveedores as $datos) { ?>
                                    <tr>
                                        <td><?= $datos['id'] ?></td>
                                        <td><?= htmlspecialchars($datos['empresa']) ?></td>
                                        <td><?= htmlspecialchars($datos['nombre']) ?></td>
                                        <td><?= htmlspecialchars($datos['correo']) ?></td>
                                        <td><?= htmlspecialchars($datos['telefono']) ?></td>
                                        <td><?= htmlspecialchars($datos['direccion']) ?></td>
                                        <td>
                                            <a href="editar.php?id=<?= $datos['id'] ?>" class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </a>
                                            <a onclick="return confirm('¿Está seguro de eliminar este proveedor?')"
                                                href="index.php?id=<?= $datos['id'] ?>&action=delete"
                                                class="btn btn-sm btn-danger" title="Eliminar">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo '<tr><td colspan="7" class="text-center">No hay proveedores registrados</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>