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

// Generar número de factura automático
$numeroFactura = $facturaController->generarNumeroFactura();

// Procesar formulario
if (isset($_POST['btnGuardar'])) {
    // Preparar datos de cabecera
    $datosFactura = [
        'numero_factura' => $_POST['numero_factura'],
        'fecha' => $_POST['fecha'],
        'cliente_nombre' => $_POST['cliente_nombre'],
        'cliente_identificacion' => $_POST['cliente_identificacion'],
        'cliente_direccion' => $_POST['cliente_direccion'] ?? '',
        'cliente_telefono' => $_POST['cliente_telefono'] ?? '',
        'cliente_email' => $_POST['cliente_email'] ?? '',
        'subtotal' => $_POST['subtotal'],
        'impuesto' => $_POST['impuesto'],
        'total' => $_POST['total'],
        'estado' => $_POST['estado'] ?? 'pendiente',
        'notas' => $_POST['notas'] ?? ''
    ];

    // Preparar detalles
    $detalles = [];
    if (isset($_POST['producto_nombre']) && is_array($_POST['producto_nombre'])) {
        foreach ($_POST['producto_nombre'] as $index => $nombre) {
            if (!empty($nombre)) {
                $cantidad = (int)$_POST['cantidad'][$index];
                $precioUnitario = (float)$_POST['precio_unitario'][$index];
                $detalles[] = [
                    'producto_nombre' => $nombre,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $cantidad * $precioUnitario
                ];
            }
        }
    }

    if ($facturaController->registrar($datosFactura, $detalles)) {
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Nueva Factura - Papelería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/25433711f4.js" crossorigin="anonymous"></script>
    <style>
        .detalle-row {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .totales-box {
            background-color: #e9ecef;
            padding: 20px;
            border-radius: 5px;
            position: sticky;
            top: 20px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center p-3 bg-light">
            <h1><i class="fa-solid fa-file-invoice-dollar"></i> Nueva Factura</h1>
            <a href="index.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
        </div>

        <div class="container mt-4">
            <form method="POST" id="formFactura">
                <div class="row">
                    <!-- Columna izquierda: Datos de factura y cliente -->
                    <div class="col-md-8">
                        <!-- Datos de la factura -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Datos de la Factura</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Número de Factura *</label>
                                        <input type="text"
                                            class="form-control"
                                            name="numero_factura"
                                            value="<?= $numeroFactura ?>"
                                            readonly
                                            required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fecha *</label>
                                        <input type="date"
                                            class="form-control"
                                            name="fecha"
                                            value="<?= date('Y-m-d') ?>"
                                            required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Datos del cliente -->
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">Datos del Cliente</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nombre del Cliente *</label>
                                        <input type="text"
                                            class="form-control"
                                            name="cliente_nombre"
                                            required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Identificación (RUC/CI) *</label>
                                        <input type="text"
                                            class="form-control"
                                            name="cliente_identificacion"
                                            required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Dirección</label>
                                        <input type="text"
                                            class="form-control"
                                            name="cliente_direccion">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Teléfono</label>
                                        <input type="text"
                                            class="form-control"
                                            name="cliente_telefono">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email"
                                            class="form-control"
                                            name="cliente_email">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detalles de la factura -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Productos / Servicios</h5>
                                <button type="button"
                                    class="btn btn-light btn-sm"
                                    onclick="agregarLinea()">
                                    <i class="fa-solid fa-plus"></i> Agregar Línea
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="detallesContainer">
                                    <!-- Las líneas se agregarán aquí dinámicamente -->
                                </div>
                            </div>
                        </div>

                        <!-- Notas adicionales -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Notas Adicionales</h5>
                            </div>
                            <div class="card-body">
                                <textarea class="form-control"
                                    name="notas"
                                    rows="3"
                                    placeholder="Observaciones o notas sobre la factura..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Columna derecha: Totales y acciones -->
                    <div class="col-md-4">
                        <div class="totales-box">
                            <h5 class="mb-3">Resumen de Factura</h5>

                            <div class="mb-3">
                                <label class="form-label">Estado</label>
                                <select name="estado" class="form-select">
                                    <option value="pendiente">Pendiente</option>
                                    <option value="pagada">Pagada</option>
                                </select>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <strong id="subtotalDisplay">$0.00</strong>
                            </div>
                            <input type="hidden" name="subtotal" id="subtotal" value="0">

                            <div class="d-flex justify-content-between mb-2">
                                <span>IVA (15%):</span>
                                <strong id="impuestoDisplay">$0.00</strong>
                            </div>
                            <input type="hidden" name="impuesto" id="impuesto" value="0">

                            <hr>

                            <div class="d-flex justify-content-between mb-3">
                                <h5>Total:</h5>
                                <h5 class="text-primary" id="totalDisplay">$0.00</h5>
                            </div>
                            <input type="hidden" name="total" id="total" value="0">

                            <button type="submit"
                                name="btnGuardar"
                                class="btn btn-primary w-100 mb-2">
                                <i class="fa-solid fa-save"></i> Guardar Factura
                            </button>

                            <a href="index.php" class="btn btn-secondary w-100">
                                <i class="fa-solid fa-times"></i> Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let lineaCounter = 0;
        const IVA_PORCENTAJE = 0.15;

        // Agregar línea de detalle
        function agregarLinea() {
            lineaCounter++;
            const container = document.getElementById('detallesContainer');
            const div = document.createElement('div');
            div.className = 'detalle-row';
            div.id = 'linea-' + lineaCounter;
            div.innerHTML = `
                <div class="row">
                    <div class="col-md-5">
                        <input type="text" 
                               class="form-control" 
                               name="producto_nombre[]" 
                               placeholder="Nombre del producto/servicio" 
                               required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" 
                               class="form-control cantidad" 
                               name="cantidad[]" 
                               placeholder="Cant." 
                               min="1" 
                               value="1" 
                               onchange="calcularTotales()"
                               required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" 
                               class="form-control precio" 
                               name="precio_unitario[]" 
                               placeholder="Precio Unit." 
                               step="0.01" 
                               min="0" 
                               onchange="calcularTotales()"
                               required>
                    </div>
                    <div class="col-md-2 d-flex align-items-center">
                        <span class="subtotal-linea me-2">$0.00</span>
                        <button type="button" 
                                class="btn btn-sm btn-danger" 
                                onclick="eliminarLinea(${lineaCounter})">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(div);
        }

        // Eliminar línea
        function eliminarLinea(id) {
            const linea = document.getElementById('linea-' + id);
            if (linea) {
                linea.remove();
                calcularTotales();
            }
        }

        // Calcular totales
        function calcularTotales() {
            let subtotal = 0;

            const cantidades = document.querySelectorAll('.cantidad');
            const precios = document.querySelectorAll('.precio');
            const subtotalesLinea = document.querySelectorAll('.subtotal-linea');

            cantidades.forEach((cantidadInput, index) => {
                const cantidad = parseFloat(cantidadInput.value) || 0;
                const precio = parseFloat(precios[index].value) || 0;
                const subtotalLinea = cantidad * precio;

                subtotalesLinea[index].textContent = '$' + subtotalLinea.toFixed(2);
                subtotal += subtotalLinea;
            });

            const impuesto = subtotal * IVA_PORCENTAJE;
            const total = subtotal + impuesto;

            // Actualizar displays
            document.getElementById('subtotalDisplay').textContent = '$' + subtotal.toFixed(2);
            document.getElementById('impuestoDisplay').textContent = '$' + impuesto.toFixed(2);
            document.getElementById('totalDisplay').textContent = '$' + total.toFixed(2);

            // Actualizar inputs ocultos
            document.getElementById('subtotal').value = subtotal.toFixed(2);
            document.getElementById('impuesto').value = impuesto.toFixed(2);
            document.getElementById('total').value = total.toFixed(2);
        }

        // Validar formulario antes de enviar
        document.getElementById('formFactura').addEventListener('submit', function(e) {
            const cantidades = document.querySelectorAll('.cantidad');
            if (cantidades.length === 0) {
                e.preventDefault();
                alert('Debe agregar al menos un producto a la factura');
                return false;
            }

            const total = parseFloat(document.getElementById('total').value);
            if (total <= 0) {
                e.preventDefault();
                alert('El total de la factura debe ser mayor a 0');
                return false;
            }
        });

        // Agregar primera línea al cargar
        window.onload = function() {
            agregarLinea();
        };
    </script>
</body>

</html>