<?php
namespace App\Models;
use App\ValidadorTrait;
use PDO;
use PDOException;
use Exception;
use App\Model;

class empresaEnvioModel extends Model {
    use ValidadorTrait;
    protected $connectionKey = 'projumi';
    private $id;
    private $nombre;
    private $telefono;
    private $direccion;
    private $estatus;

    public function setData($id, $nombre, $telefono, $direccion, $estatus): array
{
    $this->errores = []; // limpiar errores previos

    // Validaciones
    if (!empty($id) && !is_numeric($id)) {
        $this->errores['id'] = "El ID debe ser numérico.";
    }

    $resultadoNombre = $this->validarTexto($nombre, 'nombre', 3, 100);
    if ($resultadoNombre !== true) {
        $this->errores['nombre'] = $resultadoNombre;
    }

    // Validar teléfono
    $resultadoTelefono = $this->validarTelefono($telefono, 'telefono', true);
    if ($resultadoTelefono !== true) {
        $this->errores['telefono'] = $resultadoTelefono;
    }

    // Validar dirección
    $resultadoDir = $this->validarDireccion($direccion, 'direccion', 3, 255);
    if ($resultadoDir !== true) {
        $this->errores['direccion'] = $resultadoDir;
    }

    $resultadoEstatus = $this->validarStatus($estatus);
    if ($resultadoEstatus !== true) {
        $this->errores['estatus'] = $resultadoEstatus;
    }

    // Si hay errores, no asignar nada y devolverlos
    if (!$this->sinErrores()) {
        return [
            'success' => false,
            'errors' => $this->obtenerErrores()
        ];
    }

    // Si todo es válido, SANITIZAR y asignar los valores
    $this->id = $id;
    $this->nombre = trim($nombre);
    $this->telefono = trim($telefono);
    $this->direccion = trim($direccion);
    $this->estatus = trim($estatus);

    return ['success' => true];
}

    // Métodos CRUD
     public function getAll() {
        $stmt = $this->query("SELECT * FROM t_empresa_envio");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getById($id) {
        try {
            $sql = 'SELECT * FROM t_empresa_envio WHERE id_empresa_envio = :id LIMIT 1';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error en getById (PDO): " . $e->getMessage());
            return false;
        }
    }

    public function getByName(string $nombre) {
        $stmt = $this->query(
            "SELECT * FROM t_empresa_envio WHERE nombre = :nombre LIMIT 1",
            [':nombre' => $nombre]
        );
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    

   public function registerEmpresaEnvio() {
    try {
        // Validaciones rápidas
        if (empty($this->nombre)) {
            return [
                'success' => false,
                'message' => 'El nombre de la empresa es obligatorio.'
            ];
        }

        if (empty($this->telefono)) {
            return [
                'success' => false,
                'message' => 'El teléfono de la empresa es obligatorio.'
            ];
        }

        if (empty($this->direccion)) {
            return [
                'success' => false,
                'message' => 'La dirección de la empresa es obligatoria.'
            ];
        }

        if (empty($this->estatus)) {
            return [
                'success' => false,
                'message' => 'El estatus de la empresa es obligatorio.'
            ];
        }

        // Verificar si el nombre ya existe
        if ($this->verificarNombreEmpresaExiste()) {
            return [
                'success' => false,
                'message' => 'Ya existe una empresa con ese nombre.'
            ];
        }

            $this->query(
                "INSERT INTO t_empresa_envio (nombre, telefono, direccion, estatus)
                 VALUES (:nombre, :telefono, :direccion, :estatus)",
                [
                   
                   ':nombre' => $this->nombre,
                    ':telefono' => $this->telefono,
                    ':direccion' => $this->direccion,
                    ':estatus' => $this->estatus
                ]
            );

             return [
                'success' => true,
                'message' => 'Empresa registrada correctamente.',
                'id_empresa_envio' => $this->lastInsertId()
            ];
        } catch (Exception $e) {
            error_log("Error en registerEmpresaEnvio: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al registrar la empresa de envios.'];
        }
}

private function verificarNombreEmpresaExiste() {
   
    $stmt = $this->query(
            "SELECT COUNT(*) as count FROM t_empresa_envio WHERE nombre = :nombre",
            [':nombre' => $this->nombre]
        );
        return $stmt->fetchColumn() > 0;
}


    public function update() {
    try {
        // Validaciones rápidas
        if (empty($this->id)) {
            return [
                'success' => false,
                'message' => 'El ID de la empresa es obligatorio.'
            ];
        }

        if (!is_numeric($this->id)) {
            return [
                'success' => false,
                'message' => 'El ID de la empresa debe ser un valor numérico.'
            ];
        }

        if (empty($this->nombre)) {
            return [
                'success' => false,
                'message' => 'El nombre de la empresa es obligatorio.'
            ];
        }

        if (empty($this->telefono)) {
            return [
                'success' => false,
                'message' => 'El teléfono de la empresa es obligatorio.'
            ];
        }

        if (empty($this->direccion)) {
            return [
                'success' => false,
                'message' => 'La dirección de la empresa es obligatoria.'
            ];
        }

        if (empty($this->estatus)) {
            return [
                'success' => false,
                'message' => 'El estatus de la empresa es obligatorio.'
            ];
        }

        // Verificar si la empresa existe
        if (!$this->verificarEmpresaExiste()) {
            return [
                'success' => false,
                'message' => 'La empresa no existe.'
            ];
        }

        // Verificar si el nombre ya existe en OTRA empresa (no en la misma)
        if ($this->verificarNombreDuplicado()) {
            return [
                'success' => false,
                'message' => 'Ya existe otra empresa con ese nombre.'
            ];
        }

        // Actualización en BD
       $this->query(
                "UPDATE t_empresa_envio
                 SET nombre = :nombre,
                    telefono = :telefono,
                    direccion = :direccion,
                    estatus = :estatus
                WHERE id_empresa_envio = :id",
                [
                    ':id' => $this->id,
                    ':nombre' => $this->nombre,
                    ':telefono' => $this->telefono,
                    ':direccion' => $this->direccion,
                    ':estatus' => $this->estatus
                ]
            );

            return ['success' => true, 'message' => 'Empresa de envios actualizada correctamente.'];

    } catch(PDOException $e) {
        error_log("Error en update: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error de base de datos al actualizar la empresa de envío.'
        ];
    }
}


private function verificarEmpresaExiste() {
    
    $stmt = $this->query(
            "SELECT COUNT(*) as count FROM t_empresa_envio WHERE id_empresa_envio = :id",
            [':id' => $this->id]
        );
        return $stmt->fetchColumn() > 0;

}

private function verificarNombreDuplicado() {
        
       $stmt = $this->query(
            "SELECT COUNT(*) as count FROM t_empresa_envio WHERE nombre = :nombre AND id_empresa_envio != :id",
            [':nombre' => $this->nombre,
            ':id' => $this->id]
        );
        return $stmt->fetchColumn() > 0;
}

    public function delete() {
    try {
        // Validaciones rápidas
        if (empty($this->id)) {
            return [
                'success' => false,
                'message' => 'El ID de la empresa es obligatorio.'
            ];
        }

        if (!is_numeric($this->id)) {
            return [
                'success' => false,
                'message' => 'El ID de la empresa debe ser un valor numérico.'
            ];
        }

        if ($this->id <= 0) {
            return [
                'success' => false,
                'message' => 'El ID de la empresa debe ser un número positivo.'
            ];
        }

        // Verificar si la empresa existe antes de eliminar
        if (!$this->verificarEmpresaExiste()) {
            return [
                'success' => false,
                'message' => 'La empresa no existe o ya fue eliminada.'
            ];
        }

        // Eliminación en BD

            $this->query("DELETE FROM t_empresa_envio WHERE id_empresa_envio = :id", [':id' => $this->id]);
            return ['success' => true, 'message' => 'Empresa de envio eliminado correctamente.'];



    } catch(PDOException $e) {
        error_log("Error en delete: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error de base de datos al eliminar la empresa de envío.'
        ];
    }
}


    // Getters
    public function getId() {
        return $this->id;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getTelefono() {
        return $this->telefono;
    }

    public function getDireccion() {
        return $this->direccion;
    }

    public function getEstatus() {
        return $this->estatus;
    }
}