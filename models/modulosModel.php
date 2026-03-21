<?php
namespace App\Models;
use App\ValidadorTrait;
use PDO;
use PDOException;
use Exception;
use App\Model;

class modulosModel extends Model {
    use ValidadorTrait;
    protected $connectionKey = 'default';

    private $id;
    private $nombre;
    private $estatus;

    public function setData($nombre, $estatus, $id = null) {
        $this->errores = []; // limpiar errores previos

        // Validar ID si se proporciona
        if (!empty($id) && !is_numeric($id)) {
            $this->errores['id'] = "El ID debe ser numérico.";
        }

        // Validar nombre
        $resultadoNombre = $this->validarTexto($nombre, 'nombre', 2, 50);
        if ($resultadoNombre !== true) {
            $this->errores['nombre'] = $resultadoNombre;
        }

        // Validar estatus
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
        $this->estatus = trim($estatus);

        return ['success' => true];
    }

    // Obtener todos los modulos
    public function getModulos() {
        try {
            $stmt = $this->query("SELECT * FROM t_modulo");
            //$stmt = $this->query("SELECT * FROM t_modulo WHERE ruta != '#' AND ruta !='roles/index'");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw $e;
        }
    }
}
