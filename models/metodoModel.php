<?php
namespace App\Models;
use PDO;
use PDOException;
use Exception;
use App\Model;
class MetodoModel extends Model {
    protected $connectionKey = 'projumi';
    private $nombre;
    private $id_metodo_pago;
    private $descripcion;
    private $estatus;

    function set_nombre($nombre){
		$this->nombre = trim($nombre);
	}
    function set_id_metodo_pago($id_metodo_pago){
		$this->id_metodo_pago = trim($id_metodo_pago);
	}
    function set_descripcion($descripcion){
		$this->descripcion = trim($descripcion);
	}
    function set_estatus($estatus){
		$this->estatus = trim($estatus);
	}   
    function get_nombre(){
		return $this->nombre;
	}
    function get_descripcion(){
		return $this->descripcion;
	}
    function get_estatus(){
		return $this->estatus;
	}
    function get_id_metodo_pago(){
		return $this->id_metodo_pago;
	}

    public function obtenerMetodos() {
        try {
            $stmt = $this->query("SELECT * FROM t_metodo_pago");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw $e;
        }
    }

}