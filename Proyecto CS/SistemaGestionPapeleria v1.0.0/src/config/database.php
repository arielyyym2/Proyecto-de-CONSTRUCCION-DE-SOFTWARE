<?php
class Database
{
    private $host = "localhost";
    private $dbname = "papeleria";
    private $username = "root";
    private $password = "";
    private $port = 3307; // puerto correcto de XAMPP
    private $conn = null;

    public function connect(): PDO
    {
        if ($this->conn !== null) {
            return $this->conn;
        }

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->dbname,
                $this->username,
                $this->password
            );

            // Activar modo de excepciones
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Establecer el charset a UTF-8
            $this->conn->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");
            $this->conn->exec("SET NAMES utf8");

            return $this->conn;
        } catch (PDOException $e) {
            // Registrar el error y lanzar una excepción
            error_log("Error de conexión a la base de datos: " . $e->getMessage());
            throw new Exception("Error al conectar con la base de datos: " . $e->getMessage());
        }
    }

    public function getConnection(): ?PDO
    {
        return $this->conn;
    }

    public function __destruct()
    {
        $this->conn = null;
    }
}
