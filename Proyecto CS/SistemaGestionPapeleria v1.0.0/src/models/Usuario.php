<?php
class Usuario
{
    private ?int $id;
    private string $nombre;
    private string $correo;
    private string $clave;
    private string $rol;
    private ?string $creado_en;

    public function __construct(
        ?int $id = null,
        string $nombre = '',
        string $correo = '',
        string $clave = '',
        string $rol = 'usuario',
        ?string $creado_en = null
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->correo = $correo;
        $this->clave = $clave;
        $this->rol = $rol;
        $this->creado_en = $creado_en;
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getCorreo(): string
    {
        return $this->correo;
    }

    public function getClave(): string
    {
        return $this->clave;
    }

    public function getRol(): string
    {
        return $this->rol;
    }

    public function getCreadoEn(): ?string
    {
        return $this->creado_en;
    }

    // Setters
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function setCorreo(string $correo): void
    {
        $this->correo = $correo;
    }

    public function setClave(string $clave): void
    {
        $this->clave = $clave;
    }

    public function setRol(string $rol): void
    {
        $this->rol = $rol;
    }

    // Métodos de utilidad
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'correo' => $this->correo,
            'rol' => $this->rol,
            'creado_en' => $this->creado_en
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['nombre'] ?? '',
            $data['correo'] ?? '',
            $data['clave'] ?? '',
            $data['rol'] ?? 'usuario',
            $data['creado_en'] ?? null
        );
    }

    public function hashPassword(string $password): void
    {
        $this->clave = password_hash($password, PASSWORD_DEFAULT);
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->clave);
    }
}
