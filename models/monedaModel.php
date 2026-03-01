<?php
namespace App\Models;
use PDO;
use PDOException;
use Exception;
use App\Model;
class MonedaModel extends Model {

    private $nombre;
    private $id_moneda;
    private $descripcion;
    private $estatus;
    public function __construct() {
        try {
            $this->db = new PDO(DB_DSN_PROJUMI, DB_USER, DB_PASS, DB_OPTIONS);
        } catch (PDOException $e) {
            // Log del error y mostrar mensaje seguro
            error_log('Error de conexión: ' . $e->getMessage());
            throw new Exception('Error al conectar con la base de datos');
        }
    }


    public function obtenerPorMetodo($id_metodo) {
        try {
            $sql = "SELECT dmp.id_detalle_metodo_pago as id_detalle_pago , m.simbolo FROM t_detalle_metodo_pago dmp INNER JOIN t_metodo_pago mp ON dmp.fk_metodo_pago = mp.id_metodo_pago INNER JOIN t_moneda m ON dmp.fk_moneda = m.id_moneda WHERE mp.id_metodo_pago = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_metodo]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw $e;
        }
    }

}