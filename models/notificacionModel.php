<?php
namespace App\Models;
use App\ValidadorTrait;
use PDO;
use PDOException;
use Exception;
use App\Model;

class notificacionModel extends Model {
    use ValidadorTrait;
    protected $connectionKey = 'default';

    private $id;
    private $titulo;
    private $descripcion;
    private $fecha;
    private $fk_emisor;
    private $fk_receptor;
    private $status;
    private $ruta;

    public function setData($titulo, $descripcion, $fecha, $fk_emisor, $fk_receptor, $status = 0, $ruta = null, $id = null) {
        $this->errores = []; // limpiar errores previos

        // Validar ID si se proporciona
        if (!empty($id) && !is_numeric($id)) {
            $this->errores['id'] = "El ID debe ser numérico.";
        }

        // Validar título
        $resultadoTitulo = $this->validarTituloNotificacion($titulo, 'título', 3, 100);
        if ($resultadoTitulo !== true) {
            $this->errores['titulo'] = $resultadoTitulo;
        }

        // Validar descripción
        $resultadoDescripcion = $this->validarDescripcionNotificacion($descripcion, 'descripción', 5, 500);
        if ($resultadoDescripcion !== true) {
            $this->errores['descripcion'] = $resultadoDescripcion;
        }

        // Validar fecha
        $resultadoFecha = $this->validarFechaNotificacion($fecha, 'fecha', true);
        if ($resultadoFecha !== true) {
            $this->errores['fecha'] = $resultadoFecha;
        }

        // Validar emisor
        $resultadoEmisor = $this->validarUsuario($fk_emisor, 'emisor', true);
        if ($resultadoEmisor !== true) {
            $this->errores['fk_emisor'] = $resultadoEmisor;
        }

        // Validar receptor
        $resultadoReceptor = $this->validarUsuario($fk_receptor, 'receptor', true);
        if ($resultadoReceptor !== true) {
            $this->errores['fk_receptor'] = $resultadoReceptor;
        }

        // Validar status
        $resultadoStatus = $this->validarStatusNotificacion($status, 'status');
        if ($resultadoStatus !== true) {
            $this->errores['status'] = $resultadoStatus;
        }

        // Validar ruta (opcional)
        if (!empty($ruta)) {
            $resultadoRuta = $this->validarRuta($ruta, 'ruta', false);
            if ($resultadoRuta !== true) {
                $this->errores['ruta'] = $resultadoRuta;
            }
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
        $this->titulo = trim($titulo);
        $this->descripcion = trim($descripcion);
        $this->fecha = trim($fecha);
        $this->fk_emisor = trim($fk_emisor);
        $this->fk_receptor = trim($fk_receptor);
        $this->status = trim($status);
        $this->ruta = $ruta ? trim($ruta) : null;

        return ['success' => true];
    }

    // Registrar una nueva notificación
    public function registrar() {
        try {
            $this->query(
                "INSERT INTO t_notificacion (titulo, descripcion, fecha, fk_usuario_emisor, fk_usuario_receptor, status, ruta)
                VALUES (:titulo, :descripcion, :fecha, :emisor, :receptor, :status, :ruta)",
                [
                    ':titulo' => $this->titulo,
                    ':descripcion' => $this->descripcion,
                    ':fecha' => $this->fecha,
                    ':emisor' => $this->fk_emisor,
                    ':receptor' => $this->fk_receptor,
                    ':status' => $this->status,
                    ':ruta' => $this->ruta
                ]
            );
            return true;
        } catch(Exception $e) {
            error_log("Error en registrarNotificacion: " . $e->getMessage());
            return false;
        }
    }

    // Obtener notificaciones por usuario receptor
    public function obtenerNotificacionesPorUsuario($idUsuario) {
        try {
            $stmt = $this->query(
                "SELECT * FROM t_notificacion 
                 WHERE fk_usuario_receptor = :idUsuario 
                 ORDER BY fecha DESC",
                [':idUsuario' => $idUsuario]
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(Exception $e) {
            error_log("Error en obtenerNotificacionesPorUsuario: " . $e->getMessage());
            return false;
        }
    }

    // Cambiar estado de notificación (leída = 1)
    public function marcarComoLeida($idNotificacion) {
        try {
            $this->query(
                "UPDATE t_notificacion SET status = 1 WHERE id_notificacion = :id",
                [':id' => $idNotificacion]
            );
            return true;
        } catch(Exception $e) {
            error_log("Error en marcarComoLeida: " . $e->getMessage());
            return false;
        }
    }

    // Eliminar notificación (opcional)
    public function eliminar($idNotificacion) {
        try {
            $this->query(
                "DELETE FROM t_notificacion WHERE id_notificacion = :id",
                [':id' => $idNotificacion]
            );
            return true;
        } catch(Exception $e) {
            error_log("Error en eliminar: " . $e->getMessage());
            return false;
        }
    }
}