<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Papelería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        #sidebar {
            width: 250px;
            height: 100vh;
            transition: all 0.3s;
        }
    </style>
</head>

<body>
    <div class="d-flex">

        <!-- Sidebar -->
        <div class="bg-light border-end" id="sidebar">
            <h4 class="p-3">Menú</h4>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><a href="modules/productos/index.php">Productos</a></li>
                <li class="list-group-item"><a href="modules/proveedores/index.php">Proveedores</a></li>
                <li class="list-group-item"><a href="modules/usuarios/index.php">Usuarios</a></li>
                <li class="list-group-item"><a href="modules/ventas/index.php">Ventas</a></li>
                <li class="list-group-item"><a href="modules/facturacion/index.php">Facturación</a></li>
                <li class="list-group-item"><a href="logout.php">Cerrar sesión</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1 p-3">

            <!-- Botón para ocultar / mostrar Sidebar -->
            <button id="toggleSidebar" class="btn btn-primary mb-3">Ocultar/Mostrar Menú</button>

            <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
            <p class="mt-3">Selecciona una opción del menú lateral.</p>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle Sidebar
        document.getElementById('toggleSidebar').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.style.display = (sidebar.style.display === 'none') ? 'block' : 'none';
        });
    </script>

</body>

</html>