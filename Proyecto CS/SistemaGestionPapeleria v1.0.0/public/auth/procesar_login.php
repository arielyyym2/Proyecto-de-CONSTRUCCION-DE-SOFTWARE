<?php
session_start();
require_once "../../src/config/Database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../login.php");
    exit;
}

$email = trim($_POST["email"]);
$password = trim($_POST["password"]);

if (empty($email) || empty($password)) {
    $_SESSION['error'] = "Debe ingresar usuario y contraseña.";
    header("Location: ../login.php");
    exit;
}

$db = new Database();
$conn = $db->connect();

// OJO: tus campos reales son correo y clave
$sql = "SELECT id, nombre, correo, clave, rol
        FROM usuarios 
        WHERE correo = :correo
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bindParam(":correo", $email);
$stmt->execute();

if ($stmt->rowCount() === 1) {

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar clave
    if (password_verify($password, $user["clave"])) {

        $_SESSION["user_id"]     = $user["id"];
        $_SESSION["user_name"]   = $user["nombre"];
        $_SESSION["user_email"]  = $user["correo"];
        $_SESSION["user_rol"]    = $user["rol"];

        header("Location: ../index.php");
        exit;
    } else {
        $_SESSION["error"] = "Contraseña incorrecta.";
        header("Location: ../login.php");
        exit;
    }
} else {
    $_SESSION["error"] = "El usuario no existe.";
    header("Location: ../login.php");
    exit;
}
