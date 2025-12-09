<?php
require_once __DIR__ . '/../interfaces/IAuthService.php';
require_once __DIR__ . '/../repositories/UsuarioRepository.php';

class AuthService implements IAuthService
{
    private UsuarioRepository $usuarioRepository;

    public function __construct(UsuarioRepository $usuarioRepository)
    {
        $this->usuarioRepository = $usuarioRepository;
    }

    public function login(string $email, string $password): ?array
    {
        // Validar entrada
        if (empty($email) || empty($password)) {
            throw new Exception("Debe ingresar usuario y contraseña");
        }

        // Buscar usuario por email
        $user = $this->usuarioRepository->findByEmail($email);

        if (!$user) {
            throw new Exception("El usuario no existe");
        }

        // Verificar contraseña
        if (!password_verify($password, $user['clave'])) {
            throw new Exception("Contraseña incorrecta");
        }

        // Iniciar sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nombre'];
        $_SESSION['user_email'] = $user['correo'];
        $_SESSION['user_rol'] = $user['rol'];

        return $user;
    }

    public function logout(): void
    {
        session_unset();
        session_destroy();
    }

    public function isAuthenticated(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public function getCurrentUser(): ?array
    {
        if (!$this->isAuthenticated()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'] ?? null,
            'nombre' => $_SESSION['user_name'] ?? null,
            'correo' => $_SESSION['user_email'] ?? null,
            'rol' => $_SESSION['user_rol'] ?? null
        ];
    }

    public function requireAuth(): void
    {
        if (!$this->isAuthenticated()) {
            header("Location: /login.php");
            exit;
        }
    }

    public function requireRole(string $rol): void
    {
        $this->requireAuth();

        $currentUser = $this->getCurrentUser();
        if ($currentUser['rol'] !== $rol && $currentUser['rol'] !== 'admin') {
            throw new Exception("No tiene permisos para acceder a esta sección");
        }
    }
}
