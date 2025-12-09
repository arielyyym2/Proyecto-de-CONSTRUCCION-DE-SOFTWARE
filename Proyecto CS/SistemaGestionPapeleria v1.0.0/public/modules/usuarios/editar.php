<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../src/config/database.php";
require_once __DIR__ . "/../../../src/repositories/UsuarioRepository.php";
require_once __DIR__ . "/../../../src/validators/UsuarioValidator.php";
require_once __DIR__ . "/../../../src/controllers/UsuarioController.php";

try {
    $database = new Database();
    $conn = $database->connect();

    $repository = new UsuarioRepository($conn);
    $validator = new UsuarioValidator();
    $usuarioController = new UsuarioController($repository, $validator);

    // Verificar que se recibió un ID
    if (!isset($_GET['id'])) {
        header("Location: index.php");
        exit;
    }

    $id = $_GET['id'];
    $usuario = $usuarioController->obtenerPorId($id);

    if (!$usuario) {
        header("Location: index.php");
        exit;
    }

    // Manejar actualización
    if (isset($_POST['btnActualizar'])) {
        if ($usuarioController->actualizar($id, $_POST)) {
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
    <title>Editar Usuario - Papelería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/25433711f4.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning">
                        <h3 class="text-center">Editar Usuario</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre completo</label>
                                <input type="text" class="form-control" name="nombre"
                                    value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control" name="correo"
                                    value="<?= htmlspecialchars($usuario['correo']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="clave" class="form-label">Nueva Contraseña</label>
                                <input type="password" class="form-control" name="clave" minlength="6">
                                <small class="form-text text-muted">Dejar en blanco para no cambiar la contraseña</small>
                            </div>

                            <div class="mb-3">
                                <label for="rol" class="form-label">Rol</label>
                                <select class="form-select" name="rol" required>
                                    <option value="admin" <?= $usuario['rol'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
                                    <option value="empleado" <?= $usuario['rol'] == 'empleado' ? 'selected' : '' ?>>Empleado</option>
                                    <option value="vendedor" <?= $usuario['rol'] == 'vendedor' ? 'selected' : '' ?>>Vendedor</option>
                                    <option value="usuario" <?= $usuario['rol'] == 'usuario' ? 'selected' : '' ?>>Usuario</option>
                                </select>
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