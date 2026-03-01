<?php
namespace App\Models;
use PDO;
use PDOException;
use Exception;
use App\Model;
class datosModel extends Model {

    private $id_datos_cuenta;
    private $telefono;
    private $banco;
    private $correo;
    private $numero_cuenta;
    private $fk_emprendedor;
    private $fk_metodo_pago;

    public function __construct() {
        try {
            $this->db = new PDO(DB_DSN_PROJUMI, DB_USER, DB_PASS, DB_OPTIONS);
        } catch (PDOException $e) {
            error_log('Error de conexión: ' . $e->getMessage());
            throw new Exception('Error al conectar con la base de datos');
        }
    }

    public function setData($id_datos_cuenta, $telefono, $banco, $correo, $numero_cuenta, $fk_emprendedor, $fk_metodo_pago) {
        $this->id_datos_cuenta = $id_datos_cuenta;
        $this->telefono = $telefono;
        $this->banco = $banco;
        $this->correo = $correo;
        $this->numero_cuenta = $numero_cuenta;
        $this->fk_emprendedor = $fk_emprendedor;
        $this->fk_metodo_pago = $fk_metodo_pago;
    }

    // Métodos CRUD
    public function getAll() {
        try {
            $stmt = $this->query("SELECT * FROM t_datos_cuenta");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getById($id) {
        try {
            $sql = 'SELECT * FROM t_datos_cuenta WHERE id_datos_cuenta = :id LIMIT 1';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error en getById (PDO): " . $e->getMessage());
            return false;
        }
    }

    public function getmetodosemprededor($idEmprendedor) {
    try {
            $sql = "SELECT 
                    dc.id_datos_cuenta, 
                    dc.telefono, 
                    dc.banco, 
                    dc.correo, 
                    dc.numero_cuenta, 
                    mp.id_metodo_pago, 
                    mp.nombre AS metodo_pago, 
                    mp.estatus AS estatus_metodo 
                    FROM t_datos_cuenta dc 
                    INNER JOIN t_metodo_pago mp 
                    ON dc.fk_metodo_pago = mp.id_metodo_pago 
                    WHERE dc.fk_emprendedor = :id_emprendedor";
        
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_emprendedor', $idEmprendedor, PDO::PARAM_INT);
            $stmt->execute();
        
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error en getCuentasConMetodosPorEmprendedor: " . $e->getMessage());
            return false;
        }
    }

    public function getmetodostodos() {
    try {
            $sql = "SELECT 
                    dc.id_datos_cuenta, 
                    dc.telefono, 
                    dc.banco, 
                    dc.correo, 
                    dc.numero_cuenta, 
                    mp.id_metodo_pago, 
                    mp.nombre AS metodo_pago, 
                    mp.estatus AS estatus_metodo 
                    FROM t_datos_cuenta dc 
                    INNER JOIN t_metodo_pago mp 
                    ON dc.fk_metodo_pago = mp.id_metodo_pago ";
        
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error en getCuentasConMetodosPorEmprendedor: " . $e->getMessage());
            return false;
        }
    }

    public function getByEmprendedor($fk_emprendedor) {
        try {
            $sql = 'SELECT * FROM t_datos_cuenta WHERE fk_emprendedor = :fk_emprendedor';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':fk_emprendedor' => $fk_emprendedor]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error en getByEmprendedor (PDO): " . $e->getMessage());
            return false;
        }
    }

    public function create() {
        try {
            $sql = 'INSERT INTO t_datos_cuenta (telefono, banco, correo, numero_cuenta, fk_emprendedor, fk_metodo_pago) 
                    VALUES (:telefono, :banco, :correo, :numero_cuenta, :fk_emprendedor, :fk_metodo_pago)';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':telefono' => $this->telefono,
                ':banco' => $this->banco,
                ':correo' => $this->correo,
                ':numero_cuenta' => $this->numero_cuenta,
                ':fk_emprendedor' => $this->fk_emprendedor,
                ':fk_metodo_pago' => $this->fk_metodo_pago
            ]);
        } catch(PDOException $e) {
            error_log("Error en create (PDO): " . $e->getMessage());
            return false;
        }
    }

    public function update() {
        try {
            $sql = 'UPDATE t_datos_cuenta 
                    SET telefono = :telefono, 
                        banco = :banco, 
                        correo = :correo, 
                        numero_cuenta = :numero_cuenta,
                        fk_emprendedor = :fk_emprendedor,
                        fk_metodo_pago = :fk_metodo_pago
                    WHERE id_datos_cuenta = :id';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $this->id_datos_cuenta,
                ':telefono' => $this->telefono,
                ':banco' => $this->banco,
                ':correo' => $this->correo,
                ':numero_cuenta' => $this->numero_cuenta,
                ':fk_emprendedor' => $this->fk_emprendedor,
                ':fk_metodo_pago' => $this->fk_metodo_pago
            ]);
        } catch(PDOException $e) {
            error_log("Error en update (PDO): " . $e->getMessage());
            return false;
        }
    }

    public function delete() {
        try {
            $sql = 'DELETE FROM t_datos_cuenta WHERE id_datos_cuenta = :id';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $this->id_datos_cuenta]);
        } catch(PDOException $e) {
            error_log("Error en delete (PDO): " . $e->getMessage());
            return false;
        }
    }
    public function obtenerDatosPagar($fk_emprendedor, $fk_metodo_pago) {
        try {
            $sql = "SELECT e.cedula, dc.*, mp.nombre AS metodo_pago_nombre 
            FROM t_datos_cuenta dc INNER JOIN t_metodo_pago mp 
            ON dc.fk_metodo_pago = mp.id_metodo_pago INNER JOIN 
            t_emprendedor e ON e.id_emprededor=dc.fk_emprendedor 
            WHERE e.id_emprededor = :fk_emprendedor AND dc.fk_metodo_pago = :fk_metodo_pago";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':fk_emprendedor', $fk_emprendedor, PDO::PARAM_INT);
            $stmt->bindParam(':fk_metodo_pago', $fk_metodo_pago, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener datos de cuenta por método y emprendedor: " . $e->getMessage());
        }
    }
}