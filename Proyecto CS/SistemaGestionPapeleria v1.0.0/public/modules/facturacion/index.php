<?php

session_start();

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

// Cargar dependencias
require_once __DIR__ . "/../../../src/config/database.php";
require_once __DIR__ . "/../../../src/interfaces/IRepository.php";
require_once __DIR__ . "/../../../src/interfaces/IValidator.php";
require_once __DIR__ . "/../../../src/repositories/FacturaRepository.php";
require_once __DIR__ . "/../../../src/repositories/DetalleFacturaRepository.php";
require_once __DIR__ . "/../../../src/validators/FacturaValidator.php";
require_once __DIR__ . "/../../../src/controllers/BaseController.php";
require_once __DIR__ . "/../../../src/controllers/FacturaController.php";

// Crear conexión a la base de datos
$database = new Database();
$conn = $database->connect();

// Crear repositorios y validador
$facturaRepository = new FacturaRepository($conn);
$detalleRepository = new DetalleFacturaRepository($conn);
$facturaValidator = new FacturaValidator();

// Instanciar el controlador
$facturaController = new FacturaController(
    $facturaRepository,
    $detalleRepository,
    $facturaValidator
);

// ===== Manejar eliminación =====
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    $facturaController->eliminar((int)$_GET['id']);
    header("Location: index.php");
    exit;
}

// ===== Manejar cambio de estado =====
if (isset($_POST['btnCambiarEstado'], $_POST['factura_id'], $_POST['nuevo_estado'])) {
    $facturaController->cambiarEstado((int)$_POST['factura_id'], $_POST['nuevo_estado']);
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Facturas - Papelería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/25433711f4.js" crossorigin="anonymous"></script>
    <style>
        .badge-pendiente {
            background-color: #ffc107;
        }

        .badge-pagada {
            background-color: #28a745;
        }

        .badge-anulada {
            background-color: #dc3545;
        }

        .factura-anulada {
            opacity: 0.6;
            text-decoration: line-through;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center p-3 bg-light">
            <h1>Gestión de Facturas</h1>
            <div>
                <a href="crear.php" class="btn btn-success me-2">
                    <i class="fa-solid fa-plus"></i> Nueva Factura
                </a>
                <a href="../../index.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Volver al Menú
                </a>
            </div>
        </div>

        <!-- Estadísticas rápidas -->
        <?php
        $todasFacturas = $facturaController->listar();
        $pendientes = array_filter($todasFacturas, fn($f) => $f['estado'] == 'pendiente');
        $pagadas = array_filter($todasFacturas, fn($f) => $f['estado'] == 'pagada');
        $totalPendiente = array_sum(array_column($pendientes, 'total'));
        $totalPagado = array_sum(array_column($pagadas, 'total'));
        ?>
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center border-primary">
                    <div class="card-body">
                        <h5 class="card-title"><?= count($todasFacturas) ?></h5>
                        <p class="card-text">Total Facturas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-warning">
                    <div class="card-body">
                        <h5 class="card-title"><?= count($pendientes) ?></h5>
                        <p class="card-text">Pendientes</p>
                        <small class="text-muted">$<?= number_format($totalPendiente, 2) ?></small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-success">
                    <div class="card-body">
                        <h5 class="card-title"><?= count($pagadas) ?></h5>
                        <p class="card-text">Pagadas</p>
                        <small class="text-muted">$<?= number_format($totalPagado, 2) ?></small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-info">
                    <div class="card-body">
                        <h5 class="card-title">$<?= number_format($totalPagado + $totalPendiente, 2) ?></h5>
                        <p class="card-text">Total Facturado</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de facturas -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Listado de Facturas</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Número</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Identificación</th>
                                <th>Items</th>
                                <th>Subtotal</th>
                                <th>Impuesto</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            $facturas = $facturaController->listar();

                            if (count($facturas) > 0) {
                                foreach ($facturas as $factura) {
                                    $claseAnulada = $factura['estado'] == 'anulada' ? 'factura-anulada' : '';
                            ?>
                                    <tr class="<?= $claseAnulada ?>">
                                        <td><strong><?= htmlspecialchars($factura['numero_factura']) ?></strong></td>
                                        <td><?= date('d/m/Y', strtotime($factura['fecha'])) ?></td>
                                        <td><?= htmlspecialchars($factura['cliente_nombre']) ?></td>
                                        <td><?= htmlspecialchars($factura['cliente_identificacion']) ?></td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= $factura['cantidad_items'] ?? 0 ?> items
                                            </span>
                                        </td>
                                        <td>$<?= number_format($factura['subtotal'], 2) ?></td>
                                        <td>$<?= number_format($factura['impuesto'], 2) ?></td>
                                        <td><strong>$<?= number_format($factura['total'], 2) ?></strong></td>
                                        <td>
                                            <?php
                                            $badgeClass = [
                                                'pendiente' => 'badge-pendiente',
                                                'pagada' => 'badge-pagada',
                                                'anulada' => 'badge-anulada'
                                            ][$factura['estado']] ?? 'bg-secondary';
                                            ?>
                                            <span class="badge <?= $badgeClass ?>">
                                                <?= ucfirst($factura['estado']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="ver.php?id=<?= $factura['id'] ?>"
                                                    class="btn btn-sm btn-info"
                                                    title="Ver detalle">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>

                                                <?php if ($factura['estado'] != 'anulada'): ?>
                                                    <a href="editar.php?id=<?= $factura['id'] ?>"
                                                        class="btn btn-sm btn-warning"
                                                        title="Editar">
                                                        <i class="fa-solid fa-edit"></i>
                                                    </a>

                                                    <!-- Botón de estado -->
                                                    <div class="btn-group">
                                                        <button type="button"
                                                            class="btn btn-sm btn-secondary dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                            <i class="fa-solid fa-exchange-alt"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <?php if ($factura['estado'] != 'pagada'): ?>
                                                                <li>
                                                                    <form method="POST" style="display: inline;">
                                                                        <input type="hidden" name="factura_id" value="<?= $factura['id'] ?>">
                                                                        <input type="hidden" name="nuevo_estado" value="pagada">
                                                                        <button type="submit" name="btnCambiarEstado" class="dropdown-item">
                                                                            <i class="fa-solid fa-check text-success"></i> Marcar como Pagada
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            <?php endif; ?>

                                                            <?php if ($factura['estado'] != 'pendiente'): ?>
                                                                <li>
                                                                    <form method="POST" style="display: inline;">
                                                                        <input type="hidden" name="factura_id" value="<?= $factura['id'] ?>">
                                                                        <input type="hidden" name="nuevo_estado" value="pendiente">
                                                                        <button type="submit" name="btnCambiarEstado" class="dropdown-item">
                                                                            <i class="fa-solid fa-clock text-warning"></i> Marcar como Pendiente
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            <?php endif; ?>

                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>
                                                            <li>
                                                                <form method="POST" style="display: inline;">
                                                                    <input type="hidden" name="factura_id" value="<?= $factura['id'] ?>">
                                                                    <input type="hidden" name="nuevo_estado" value="anulada">
                                                                    <button type="submit" name="btnCambiarEstado"
                                                                        class="dropdown-item text-danger"
                                                                        onclick="return confirm('¿Está seguro de anular esta factura?')">
                                                                        <i class="fa-solid fa-ban"></i> Anular
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                <?php endif; ?>

                                                <a onclick="return confirm('¿Está seguro de eliminar esta factura?')"
                                                    href="index.php?id=<?= $factura['id'] ?>&action=delete"
                                                    class="btn btn-sm btn-danger"
                                                    title="Eliminar">
                                                    <i class="fa-solid fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo '<tr><td colspan="10" class="text-center py-4">
                                            <i class="fa-solid fa-inbox fa-3x text-muted mb-3"></i>
                                            <p>No hay facturas registradas</p>
                                          </td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>