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

    // Manejar eliminación
    if (isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] == 'delete') {
        $usuarioController->eliminar($_GET['id']);
        header("Location: index.php");
        exit;
    }

    // Manejar registro
    if (isset($_POST['btnRegistrar'])) {
        $usuarioController->registrar($_POST);
    }
} catch (Exception $e) {
    die('<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>');
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Papelería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/25433711f4.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center p-3 bg-light">
            <h1>Gestión de Usuarios</h1>
            <a href="../../index.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Volver al Menú
            </a>
        </div>

        <div class="container-fluid row mt-3">
            <form class="col-md-4 p-3 border rounded bg-light" method="POST">
                <h3 class="text-center text-secondary mb-4">Registro de Usuarios</h3>

                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre completo *</label>
                    <input type="text" class="form-control" name="nombre" required>
                </div>

                <div class="mb-3">
                    <label for="correo" class="form-label">Correo electrónico *</label>
                    <input type="email" class="form-control" name="correo" required>
                </div>

                <div class="mb-3">
                    <label for="clave" class="form-label">Contraseña *</label>
                    <input type="password" class="form-control" name="clave" required minlength="6">
                    <small class="form-text text-muted">Mínimo 6 caracteres</small>
                </div>

                <div class="mb-3">
                    <label for="rol" class="form-label">Rol *</label>
                    <select class="form-select" name="rol" required>
                        <option value="">Seleccione un rol</option>
                        <option value="admin">Administrador</option>
                        <option value="empleado">Empleado</option>
                        <option value="vendedor">Vendedor</option>
                        <option value="usuario">Usuario</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100" name="btnRegistrar" value="ok">
                    <i class="fa-solid fa-user-plus"></i> Registrar Usuario
                </button>
            </form>

            <div class="col-md-8 p-4">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-info">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Correo</th>
                                <th scope="col">Rol</th>
                                <th scope="col">Creado</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $usuarios = $usuarioController->listar();
                            if (count($usuarios) > 0) {
                                foreach ($usuarios as $datos) { ?>
                                    <tr>
                                        <td><?= $datos['id'] ?></td>
                                        <td><?= htmlspecialchars($datos['nombre']) ?></td>
                                        <td><?= htmlspecialchars($datos['correo']) ?></td>
                                        <td>
                                            <?php
                                            $badgeClass = 'bg-secondary';
                                            if ($datos['rol'] == 'admin') $badgeClass = 'bg-danger';
                                            elseif ($datos['rol'] == 'empleado') $badgeClass = 'bg-primary';
                                            elseif ($datos['rol'] == 'vendedor') $badgeClass = 'bg-success';
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($datos['rol']) ?></span>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($datos['creado_en'])) ?></td>
                                        <td>
                                            <a href="editar.php?id=<?= $datos['id'] ?>" class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </a>
                                            <a onclick="return confirm('¿Está seguro de eliminar este usuario?')"
                                                href="index.php?id=<?= $datos['id'] ?>&action=delete"
                                                class="btn btn-sm btn-danger" title="Eliminar">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo '<tr><td colspan="6" class="text-center">No hay usuarios registrados</td></tr>';
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