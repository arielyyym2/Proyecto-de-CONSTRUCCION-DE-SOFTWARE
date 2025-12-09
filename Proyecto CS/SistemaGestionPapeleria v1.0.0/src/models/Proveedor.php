<?php
class Proveedor
{
    private ?int $id;
    private string $nombre;
    private string $empresa;
    private ?string $telefono;
    private string $correo;
    private ?string $direccion;
    private ?string $creado_en;

    public function __construct(
        ?int $id = null,
        string $nombre = '',
        string $empresa = '',
        ?string $telefono = null,
        string $correo = '',
        ?string $direccion = null,
        ?string $creado_en = null
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->empresa = $empresa;
        $this->telefono = $telefono;
        $this->correo = $correo;
        $this->direccion = $direccion;
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

    public function getEmpresa(): string
    {
        return $this->empresa;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function getCorreo(): string
    {
        return $this->correo;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
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

    public function setEmpresa(string $empresa): void
    {
        $this->empresa = $empresa;
    }

    public function setTelefono(?string $telefono): void
    {
        $this->telefono = $telefono;
    }

    public function setCorreo(string $correo): void
    {
        $this->correo = $correo;
    }

    public function setDireccion(?string $direccion): void
    {
        $this->direccion = $direccion;
    }

    // Métodos de utilidad
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'empresa' => $this->empresa,
            'telefono' => $this->telefono,
            'correo' => $this->correo,
            'direccion' => $this->direccion,
            'creado_en' => $this->creado_en
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['nombre'] ?? '',
            $data['empresa'] ?? '',
            $data['telefono'] ?? null,
            $data['correo'] ?? '',
            $data['direccion'] ?? null,
            $data['creado_en'] ?? null
        );
    }
}
