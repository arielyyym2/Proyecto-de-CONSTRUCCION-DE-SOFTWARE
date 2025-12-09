<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = isset($_SESSION["error"]) ? $_SESSION["error"] : "";
unset($_SESSION["error"]);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/25433711f4.js" crossorigin="anonymous"></script>

    <style>
        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f5f5f5;
        }
        .login-card {
            width: 380px;
        }
    </style>
</head>

<body>

    <div class="card p-4 shadow login-card">
        <h3 class="text-center mb-3">Login - Papelería</h3>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center p-2"><?= $error ?></div>
        <?php endif; ?>

        <form action="auth/procesar_login.php" method="POST">
            <div class="mb-3">
                <label>Email</label>
                <input type="email" class="form-control" name="email" placeholder="Ingresa tu email">
            </div>

            <div class="mb-3">
                <label>Contraseña</label>
                <input type="password" class="form-control" name="password" placeholder="Ingresa tu contraseña">
            </div>

            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
        </form>
    </div>

</body>

</html>
