<?php
namespace App\Models;
use App\ValidadorTrait;
use PDO;
use PDOException;
use Exception;
use App\Model;
class eventosModel extends Model {
    use ValidadorTrait;
    // Esta clase usa la conexión "projumi"
    protected $connectionKey = 'projumi';

    private $id_eventos;
    private $fecha_inicio;
    private $fecha_fin;
    private $nombre;
    private $direccion;
    private $status;

  public function setData($id_eventos, $fecha_inicio, $fecha_fin, $nombre, $direccion, $status): array
{
    $this->errores = []; // limpiar errores previos

    // Valid_eventosaciones
    if (!empty($id_eventos) && !is_numeric($id_eventos)) {
        $this->errores['id_eventos'] = "El ID debe ser numérico.";
    }

    // Validar nombre
    $resultadoNombre = $this->validarTexto($nombre, 'nombre', 3, 100);
    if ($resultadoNombre !== true) {
        $this->errores['nombre'] = $resultadoNombre;
    }

    // Validar dirección
    $resultadoDir = $this->validarDireccion($direccion, 'direccion', 3, 255);
    if ($resultadoDir !== true) {
        $this->errores['direccion'] = $resultadoDir;
    }

    // Validar fechas
    $resultadoFechaInicio = $this->validarFecha($fecha_inicio, 'fecha_inicio', true);
    if ($resultadoFechaInicio !== true) {
        $this->errores['fecha_inicio'] = $resultadoFechaInicio;
    }

    $resultadoFechaFin = $this->validarFecha($fecha_fin, 'fecha_fin', true);
    if ($resultadoFechaFin !== true) {
        $this->errores['fecha_fin'] = $resultadoFechaFin;
    }

    // Validar rango de fechas si ambas son válidas
    if ($resultadoFechaInicio === true && $resultadoFechaFin === true) {
        $resultadoRangoFechas = $this->validarRangoFechas($fecha_inicio, $fecha_fin);
        if ($resultadoRangoFechas !== true) {
            $this->errores['fecha_fin'] = $resultadoRangoFechas;
        }
    }

    $resultadoStatus = $this->validarStatus($status);
    if ($resultadoStatus !== true) {
        $this->errores['status'] = $resultadoStatus;
    }

    // Si hay errores, no asignar nada y devolverlos
    if (!$this->sinErrores()) {
        return [
            'success' => false,
            'errors' => $this->errores
        ];
    }

    // Si todo es válido, SANITIZAR y asignar los valores
    $this->id_eventos = $id_eventos;
    $this->fecha_inicio = $fecha_inicio;
    $this->fecha_fin = $fecha_fin;
    $this->nombre = trim($nombre);
    $this->direccion = trim($direccion);
    $this->status = trim($status);

    return ['success' => true];
}
    // Métodos CRUD

    public function getAll() {
        $stmt = $this->query("SELECT * FROM t_evento");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cargarEventosActuales() {
        $stmt = $this->query("SELECT * FROM t_evento WHERE DATE(fecha_fin) >= CURDATE() AND DATE(fecha_inicio) <= CURDATE() AND status = 1");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getByName(string $nombre) {
        $stmt = $this->query(
            "SELECT * FROM t_evento WHERE nombre = :nombre LIMIT 1",
            [':nombre' => $nombre]
        );
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }



    public function registerEventos() {
    try {
        // Validaciones rápidas
        if (empty($this->fecha_inicio)) {
            return [
                'success' => false,
                'message' => 'La fecha de inicio es obligatoria.'
            ];
        }

        if (empty($this->fecha_fin)) {
            return [
                'success' => false,
                'message' => 'La fecha de fin es obligatoria.'
            ];
        }

        if (empty($this->nombre)) {
            return [
                'success' => false,
                'message' => 'El nombre del evento es obligatorio.'
            ];
        }

        if (empty($this->direccion)) {
            return [
                'success' => false,
                'message' => 'La dirección del evento es obligatoria.'
            ];
        }

        if (empty($this->status)) {
            return [
                'success' => false,
                'message' => 'El status del evento es obligatorio.'
            ];
        }

        if ($this->fecha_inicio > $this->fecha_fin) {
            return [
                'success' => false,
                'message' => 'La fecha de inicio no puede ser mayor que la fecha de fin.'
            ];
        }

        // Verificar si el nombre ya existe
        if ($this->verificarNombreEventoExiste()) {
            return [
                'success' => false,
                'message' => 'Ya existe un evento con ese nombre.'
            ];
        }

    // Insertar
            $this->query(
                "INSERT INTO t_evento (fecha_inicio, fecha_fin, nombre, direccion, status)
                 VALUES (:fecha_inicio, :fecha_fin, :nombre, :direccion, :status)",
                [
                   
                    ':fecha_inicio' => $this->fecha_inicio,
                    ':fecha_fin' => $this->fecha_fin,
                    ':nombre' => $this->nombre,
                    ':direccion' => $this->direccion,
                    ':status' => $this->status
                ]
            );

            return [
                'success' => true,
                'message' => 'Evento registrado correctamente.',
                'id_eventos' => $this->lastInsertId()
            ];
        } catch (Exception $e) {
            error_log("Error en registerEventos: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al registrar el evento.'];
        }
}


public function verificarNombreEventoExiste() {

          $stmt = $this->query(
            "SELECT COUNT(*) as count FROM t_evento WHERE nombre = :nombre",
            [':nombre' => $this->nombre]
        );
        return $stmt->fetchColumn() > 0;
    
}

   public function update() {
    try {
        // Validaciones rápidas
        if (empty($this->id_eventos)) {
            return [
                'success' => false,
                'message' => 'El ID del evento es obligatorio.'
            ];
        }

        if (empty($this->fecha_inicio)) {
            return [
                'success' => false,
                'message' => 'La fecha de inicio es obligatoria.'
            ];
        }

        if (empty($this->fecha_fin)) {
            return [
                'success' => false,
                'message' => 'La fecha de fin es obligatoria.'
            ];
        }

        if (empty($this->nombre)) {
            return [
                'success' => false,
                'message' => 'El nombre del evento es obligatorio.'
            ];
        }

        if (empty($this->direccion)) {
            return [
                'success' => false,
                'message' => 'La dirección del evento es obligatoria.'
            ];
        }

        if (empty($this->status)) {
            return [
                'success' => false,
                'message' => 'El status del evento es obligatorio.'
            ];
        }

        if ($this->fecha_inicio > $this->fecha_fin) {
            return [
                'success' => false,
                'message' => 'La fecha de inicio no puede ser mayor que la fecha de fin.'
            ];
        }

        // Verificar si el nombre ya existe en OTRO evento (no en el mismo)
        if ($this->verificarNombreDuplicado()) {
            return [
                'success' => false,
                'message' => 'Ya existe otro evento con ese nombre.'
            ];
        }

 $this->query(
                "UPDATE t_evento
                 SET fecha_inicio = :fecha_inicio, 
                    fecha_fin = :fecha_fin,
                    nombre = :nombre,
                    direccion = :direccion, 
                    status = :status
                    WHERE id_eventos = :id_eventos",
                [
                    ':id_eventos' => $this->id_eventos,
                    ':fecha_inicio' => $this->fecha_inicio,
                    ':fecha_fin' => $this->fecha_fin,
                    ':nombre' => $this->nombre,
                    ':direccion' => $this->direccion,
                    ':status' => $this->status
                ]
            );

            return ['success' => true, 'message' => 'Evento actualizado correctamente.'];

        } catch (Exception $e) {
            error_log("Error en updateCategoria: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al actualizar el evento.'];
        }

}

private function verificarNombreDuplicado() {
        
       $stmt = $this->query(
            "SELECT COUNT(*) as count FROM t_evento WHERE nombre = :nombre AND id_eventos != :id_eventos",
            [':nombre' => $this->nombre,
            ':id_eventos' => $this->id_eventos]
        );
        return $stmt->fetchColumn() > 0;
}

    public function delete($id_eventos) {
    try {

        // var_dump($id_eventos);
        // exit;
        // Validaciones rápidas
       if (empty($this->id_eventos)) {
           return [
               'success' => false,
               'message' => 'El ID del evento es obligatorio.'
           ];
       }

       if (!is_numeric($this->id_eventos)) {
           return [
               'success' => false,
               'message' => 'El ID del evento debe ser un valor numérico.'
           ];
       }

       if ($this->id_eventos <= 0) {
           return [
               'success' => false,
               'message' => 'El ID del evento debe ser un número positivo.'
           ];
       }

       // Verificar si el evento existe antes de eliminar
       if (!$this->verificarEventoExiste()) {
           return [
               'success' => false,
               'message' => 'El evento no existe o ya fue eliminado.'
           ];
       }

       if ($this->verificarRelaciones()) {
           return [
               'success' => false,
               'message' => 'No se puede eliminar: tiene Ventas asociadas.'
           ];
       }
     

            // Eliminar
            $this->query("DELETE FROM t_evento WHERE id_eventos = :id_eventos", [':id_eventos' => $this->id_eventos]);
            return ['success' => true, 'message' => 'Evento eliminado correctamente.'];



    } catch(PDOException $e) {
        error_log("Error en delete: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error de base de datos al eliminar el evento.'
        ];
    }

}

private function verificarRelaciones() {
    
    $stmt = $this->query(
            "SELECT COUNT(*) AS total FROM t_venta_evento WHERE fk_evento = :id_eventos",
            [':id_eventos' => $this->id_eventos]
        );
        return $stmt->fetchColumn() > 0;

}


private function verificarEventoExiste() {
    
    $stmt = $this->query(
            "SELECT COUNT(*) as count FROM t_evento WHERE id_eventos = :id_eventos",
            [':id_eventos' => $this->id_eventos]
        );
        return $stmt->fetchColumn() > 0;

}

    public function obtenerEvento($id_eventos) {
      
         $stmt = $this->query(
            "SELECT * FROM t_evento WHERE id_eventos = :id_eventos",
            [':id_eventos' => $id_eventos]
        );
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>