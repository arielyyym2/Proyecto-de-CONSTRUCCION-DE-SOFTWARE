<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../src/config/database.php";
require_once __DIR__ . "/../../../src/interfaces/IRepository.php";
require_once __DIR__ . "/../../../src/interfaces/IValidator.php";
require_once __DIR__ . "/../../../src/repositories/FacturaRepository.php";
require_once __DIR__ . "/../../../src/repositories/DetalleFacturaRepository.php";
require_once __DIR__ . "/../../../src/validators/FacturaValidator.php";
require_once __DIR__ . "/../../../src/controllers/BaseController.php";
require_once __DIR__ . "/../../../src/controllers/FacturaController.php";

$database = new Database();
$conn = $database->connect();

$facturaRepository = new FacturaRepository($conn);
$detalleRepository = new DetalleFacturaRepository($conn);
$facturaValidator = new FacturaValidator();

$facturaController = new FacturaController(
    $facturaRepository,
    $detalleRepository,
    $facturaValidator
);

// Verificar que se recibió el ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$facturaId = (int)$_GET['id'];

// Obtener datos de la factura
$factura = $facturaController->obtenerPorId($facturaId);
if (!$factura) {
    header("Location: index.php");
    exit;
}

// Obtener detalles de la factura
$detalles = $facturaController->obtenerDetalles($facturaId);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Factura <?= htmlspecialchars($factura['numero_factura']) ?> - Papelería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/25433711f4.js" crossorigin="anonymous"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            .factura-container {
                box-shadow: none !important;
                border: none !important;
            }
        }

        .factura-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 30px;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .logo-empresa {
            font-size: 28px;
            font-weight: bold;
            color: #0d6efd;
        }

        .badge-estado {
            font-size: 16px;
            padding: 8px 15px;
        }

        .tabla-detalles {
            margin-top: 20px;
        }

        .totales-factura {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .anulada-watermark {
            position: relative;
        }

        .anulada-watermark::after {
            content: "ANULADA";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(220, 53, 69, 0.2);
            font-weight: bold;
            z-index: 1;
            pointer-events: none;
        }
    </style>
</head>

<body class="bg-light">
    <!-- Botones de acción (no se imprimen) -->
    <div class="container-fluid p-3 no-print">
        <div class="d-flex justify-content-between align-items-center">
            <a href="index.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Volver al Listado
            </a>
            <div class="btn-group">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fa-solid fa-print"></i> Imprimir
                </button>
                <?php if ($factura['estado'] != 'anulada'): ?>
                    <a href="editar.php?id=<?= $factura['id'] ?>" class="btn btn-warning">
                        <i class="fa-solid fa-edit"></i> Editar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Contenedor de la factura -->
    <div class="factura-container mt-3 mb-5 <?= $factura['estado'] == 'anulada' ? 'anulada-watermark' : '' ?>">

        <!-- Encabezado -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="logo-empresa">
                    PAPELERÍA LA 14
                </div>
                <p class="mb-0">RUC: 1234567890001</p>
                <p class="mb-0">Dirección: Fransico Segura entre la 13 y la 14 #123</p>
                <p class="mb-0">Teléfono: 0994008460</p>
                <p class="mb-0">Email: papeleria14@gmail.com</p>
            </div>
            <div class="col-md-6 text-end">
                <h2 class="mb-3">FACTURA</h2>
                <p class="mb-1">
                    <strong>N° <?= htmlspecialchars($factura['numero_factura']) ?></strong>
                </p>
                <p class="mb-1">
                    <strong>Fecha:</strong> <?= date('d/m/Y', strtotime($factura['fecha'])) ?>
                </p>
                <p class="mb-3">
                    <?php
                    $badgeClass = [
                        'pendiente' => 'bg-warning text-dark',
                        'pagada' => 'bg-success',
                        'anulada' => 'bg-danger'
                    ][$factura['estado']] ?? 'bg-secondary';
                    ?>
                    <span class="badge <?= $badgeClass ?> badge-estado">
                        <?= strtoupper($factura['estado']) ?>
                    </span>
                </p>
            </div>
        </div>

        <hr class="my-4">

        <!-- Datos del cliente -->
        <div class="row mb-4">
            <div class="col-md-12">
                <h5 class="mb-3 text-primary">
                    DATOS DEL CLIENTE
                </h5>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1">
                            <strong>Nombre/Razón Social:</strong><br>
                            <?= htmlspecialchars($factura['cliente_nombre']) ?>
                        </p>
                        <p class="mb-1">
                            <strong>RUC/CI:</strong><br>
                            <?= htmlspecialchars($factura['cliente_identificacion']) ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <?php if (!empty($factura['cliente_direccion'])): ?>
                            <p class="mb-1">
                                <strong>Dirección:</strong><br>
                                <?= htmlspecialchars($factura['cliente_direccion']) ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($factura['cliente_telefono'])): ?>
                            <p class="mb-1">
                                <strong>Teléfono:</strong> <?= htmlspecialchars($factura['cliente_telefono']) ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($factura['cliente_email'])): ?>
                            <p class="mb-1">
                                <strong>Email:</strong> <?= htmlspecialchars($factura['cliente_email']) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <!-- Detalle de productos -->
        <div class="tabla-detalles">
            <h5 class="mb-3 text-primary">
                DETALLE DE PRODUCTOS/SERVICIOS
            </h5>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th width="10%" class="text-center">#</th>
                        <th width="45%">Descripción</th>
                        <th width="15%" class="text-center">Cantidad</th>
                        <th width="15%" class="text-end">P. Unitario</th>
                        <th width="15%" class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $contador = 1;
                    foreach ($detalles as $detalle):
                    ?>
                        <tr>
                            <td class="text-center"><?= $contador++ ?></td>
                            <td><?= htmlspecialchars($detalle['producto_nombre']) ?></td>
                            <td class="text-center"><?= $detalle['cantidad'] ?></td>
                            <td class="text-end">$<?= number_format($detalle['precio_unitario'], 2) ?></td>
                            <td class="text-end">
                                <strong>$<?= number_format($detalle['subtotal'], 2) ?></strong>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Totales -->
        <div class="row mt-4">
            <div class="col-md-7">
                <?php if (!empty($factura['notas'])): ?>
                    <div class="border p-3 rounded">
                        <h6 class="text-primary mb-2">
                            Notas:
                        </h6>
                        <p class="mb-0"><?= nl2br(htmlspecialchars($factura['notas'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-5">
                <div class="totales-factura">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong>$<?= number_format($factura['subtotal'], 2) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>IVA (15%):</span>
                        <strong>$<?= number_format($factura['impuesto'], 2) ?></strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <h5 class="mb-0">TOTAL:</h5>
                        <h5 class="mb-0 text-primary">
                            $<?= number_format($factura['total'], 2) ?>
                        </h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie de página -->
        <div class="row mt-5">
            <div class="col-md-12 text-center text-muted">
                <hr>
                <small>
                    Factura generada el <?= date('d/m/Y H:i', strtotime($factura['creado_en'])) ?>
                    <br>
                    Gracias por su preferencia
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>