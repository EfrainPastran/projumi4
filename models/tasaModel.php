<?php
namespace App\Models;
use PDO;
use PDOException;
use Exception;
use App\Model;
class TasaModel extends Model {
    private $tasa_cambio;
    private $fecha_cambio;
    private $estatus;

    public function __construct() {
        try {
            $this->db = new PDO(DB_DSN_PROJUMI, DB_USER, DB_PASS, DB_OPTIONS);
        } catch (PDOException $e) {
            throw new Exception("Error de conexión: " . $e->getMessage());
        }
    }

    public function setDatos($tasa_cambio, $fecha_cambio, $estatus = 1) {
        $this->tasa_cambio = $tasa_cambio;
        $this->fecha_cambio = $fecha_cambio;
        $this->estatus = $estatus;
    }

    public function registrarTasa() {
        $sql = "INSERT INTO t_cambio (tasa_cambio, fecha_cambio, estatus) 
                VALUES (:tasa_cambio, :fecha_cambio, :estatus)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':tasa_cambio' => $this->tasa_cambio,
            ':fecha_cambio' => $this->fecha_cambio,
            ':estatus' => $this->estatus
        ]);
    }

    public function yaExisteTasaEnFecha($fecha) {
        $sql = "SELECT COUNT(*) FROM t_cambio 
                WHERE DATE(fecha_cambio) = DATE(:fecha) AND estatus = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':fecha' => $fecha]);
        return $stmt->fetchColumn() > 0;
    }

    public function obtenerUltimaTasa() {
        $sql = "SELECT * FROM t_cambio WHERE estatus = 1 ORDER BY fecha_cambio DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener todos los registros
    public function obtenerTodosLosCambios() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM t_cambio ORDER BY fecha_cambio DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener cambios: " . $e->getMessage());
        }
    }
}

