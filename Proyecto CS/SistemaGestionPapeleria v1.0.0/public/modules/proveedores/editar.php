<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../src/config/database.php";
require_once __DIR__ . "/../../../src/repositories/ProveedorRepository.php";
require_once __DIR__ . "/../../../src/validators/ProveedorValidator.php";
require_once __DIR__ . "/../../../src/controllers/ProveedorController.php";

try {
    $database = new Database();
    $conn = $database->connect();

    $repository = new ProveedorRepository($conn);
    $validator = new ProveedorValidator();
    $proveedorController = new ProveedorController($repository, $validator);

    // Verificar que se recibió un ID
    if (!isset($_GET['id'])) {
        header("Location: index.php");
        exit;
    }

    $id = $_GET['id'];
    $proveedor = $proveedorController->obtenerPorId($id);

    if (!$proveedor) {
        header("Location: index.php");
        exit;
    }

    // Manejar actualización
    if (isset($_POST['btnActualizar'])) {
        if ($proveedorController->actualizar($id, $_POST)) {
            header("Location: index.php");
            exit;
        }
    }
} catch (Exception $e) {
    die('<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>');
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Proveedor - Papelería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/25433711f4.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning">
                        <h3 class="text-center">Editar Proveedor</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre del contacto *</label>
                                <input type="text" class="form-control" name="nombre"
                                    value="<?= htmlspecialchars($proveedor['nombre']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="empresa" class="form-label">Empresa *</label>
                                <input type="text" class="form-control" name="empresa"
                                    value="<?= htmlspecialchars($proveedor['empresa']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="tel"
                                    class="form-control"
                                    name="telefono"
                                    maxlength="20"
                                    pattern="[\d\s\-\+\(\)]+"
                                    value="<?= htmlspecialchars($proveedor['telefono'] ?? '') ?>"
                                    placeholder="Ej: +593 99 999 9999"
                                    title="Solo números, espacios, guiones, paréntesis y el signo +. Máximo 20 caracteres">
                                <small class="form-text text-muted">Máximo 20 caracteres</small>
                            </div>

                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo electrónico *</label>
                                <input type="email" class="form-control" name="correo"
                                    value="<?= htmlspecialchars($proveedor['correo']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="direccion" class="form-label">Dirección</label>
                                <textarea class="form-control" name="direccion" rows="3"><?= htmlspecialchars($proveedor['direccion'] ?? '') ?></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fa-solid fa-arrow-left"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-warning" name="btnActualizar">
                                    <i class="fa-solid fa-save"></i> Actualizar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>