<?php
//listo
namespace App;
use InvalidArgumentException;
class UserDTO {
    private $id_usuario;
    private $cedula;
    private $fecha_nacimiento;
    private $nombre;
    private $apellido;
    private $telefono;
    private $edad;
    private $imagen;
    private $email;
    private $direccion;
    private $password;
    
    public function __construct(array $data) {
        $this->id_usuario = $data['id_usuario'] ?? null;
        $this->cedula = $data['cedula'] ?? null;
        $this->fecha_nacimiento = $data['fecha_nacimiento'] ?? null;
        $this->nombre = $data['nombre'] ?? null;
        $this->apellido = $data['apellido'] ?? null;
        $this->telefono = $data['telefono'] ?? null;
        $this->edad = $data['edad'] ?? null;
        $this->imagen = $data['imagen'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->direccion = $data['direccion'] ?? null;
        $this->password = $data['password'] ?? null;
    }
    
    // Getters
    public function getIdUsuario() { return $this->id_usuario; }
    public function getCedula() { return $this->cedula; }
    
    // Setters con validación básica
    public function setEmail(string $email): self {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Email no válido");
        }
        $this->email = $email;
        return $this;
    }
    
}

?>