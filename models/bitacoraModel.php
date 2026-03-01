<?php
namespace App\Models;
use App\ValidadorTrait;
use PDO;
use PDOException;
use Exception;
use App\Model;

class bitacoraModel extends Model {
    use ValidadorTrait; 
    
    protected $connectionKey = 'default';
    private $id;
    private $modulo;
    private $descripcion;
    private $fecha;
    private $fk_usuario;

    public function setData($modulo, $descripcion, $fecha, $fk_usuario, $id = null) {
        $this->errores = []; // limpiar errores previos
        // Validar módulo
        $resultadoModulo = $this->validarTexto($modulo, 'módulo', 2, 50);
        if ($resultadoModulo !== true) {
            $this->errores['modulo'] = $resultadoModulo;
        }

        // Validar descripción
        $resultadoDescripcion = $this->validarDescripcionBitacora($descripcion, 'descripción', 5, 500);
        if ($resultadoDescripcion !== true) {
            $this->errores['descripcion'] = $resultadoDescripcion;
        }

        if (!empty($fk_usuario) && !is_numeric($fk_usuario)) {
            $this->errores['fk_usuario'] = "El ID del usuario debe ser numérico.";
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
        $this->modulo = trim($modulo);
        $this->descripcion = trim($descripcion);
        $this->fecha = trim($fecha);
        $this->fk_usuario = trim($fk_usuario);

        return ['success' => true];
    }

    // Obtener todas las entradas de la bitácora
    public function getBitacora() {
        try {
            $stmt = $this->query("SELECT b.id_bitacora as id, b.modulo_accionado, b.descripcion_accion, b.fecha_registro, CONCAT(u.nombre, u.apellido) as usuario_nombre FROM t_bitacora b INNER JOIN t_usuario u ON b.fk_usuario=u.id_usuario ORDER BY fecha_registro DESC");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Registrar una nueva entrada en la bitácora
    public function registrarBitacora() {
        try {
            $this->query(
                "INSERT INTO t_bitacora (modulo_accionado, descripcion_accion, fecha_registro, fk_usuario)
                 VALUES (:modulo, :descripcion, :fecha, :fk_usuario)",
                [
                    ':modulo' => $this->modulo,
                    ':descripcion' => $this->descripcion,
                    ':fecha' => $this->fecha,
                    ':fk_usuario' => $this->fk_usuario
                ]
            );
            return true;
        } catch(Exception $e) {
            error_log("Error en registerBitacora: " . $e->getMessage());
            return false;
        }
    }

    // Buscar por módulo (opcional)
    public function getByModulo($modulo) {
        try {
            $stmt = $this->query(
                "SELECT * FROM t_bitacora WHERE modulo_accionado = :modulo ORDER BY fecha_registro DESC",
                [':modulo' => $modulo]
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(Exception $e) {
            error_log("Error en getByModulo: " . $e->getMessage());
            return false;
        }
    }
}