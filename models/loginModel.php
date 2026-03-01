<?php
//listo
namespace App\Models;
use PDO;
use PDOException;
use Exception;
use App\Model;
class loginModel extends Model {

    private $cedula;
    private $password;



    function set_cedula($cedula){
		$this->cedula = trim($cedula);
	}
	
    function set_password($password){
		$this->password = trim($password);
	}
    
	
	function get_cedula(){
		return $this->cedula;
	}

    function get_password(){
		return $this->password;
	}
   
    public function __construct() {
        try {
            $this->db = new PDO(DB_DSN, DB_USER, DB_PASS, DB_OPTIONS);
        } catch (PDOException $e) {
            // Log del error y mostrar mensaje seguro
            error_log('Error de conexión: ' . $e->getMessage());
            throw new Exception('Error al conectar con la base de datos');
        }
    }



    public function session($cedula, $password) {
        try {
            $sql = 'SELECT id_usuario, cedula, fecha_nacimiento, nombre, apellido, telefono, correo, direccion, password
                    FROM t_usuario 
                    WHERE cedula = :cedula 
                    LIMIT 1';
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':cedula' => $cedula]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$item) {
                return false;
            }
            
            if (!password_verify($password, $item['password'])) {
                return false;
            }
    
            // Eliminar contraseña del resultado
            unset($item['password']);
            
            // Devolver los datos del usuario después de registrar en bitácora
            return $item;
    
        } catch(PDOException $e) {
            error_log("Error en session (PDO): " . $e->getMessage());
            return false;
        }
    }

  
   


}