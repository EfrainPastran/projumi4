<?php
namespace App\Models;
use PDO;
use PDOException;
use Exception;
use App\Model;
class UserModel extends Model {

    private $cedula;
	private $fecha_nacimiento;
	private $nombre;
	private $apellido;
	private $telefono;
    private $imagen;
    private $email;
    private $direccion;
    private $password;
    public function __construct() {
        try {
            $this->db = new PDO(DB_DSN, DB_USER, DB_PASS, DB_OPTIONS);
        } catch (PDOException $e) {
            // Log del error y mostrar mensaje seguro
            error_log('Error de conexión: ' . $e->getMessage());
            throw new Exception('Error al conectar con la base de datos');
        }
    }
    function set_cedula($cedula){
		$this->cedula = trim($cedula);
	}
	function set_fecha_nacimiento($fecha_nacimiento){
		$this->fecha_nacimiento = trim($fecha_nacimiento);
	}
    function set_nombre($nombre){
		$this->nombre = trim($nombre);
	}
	function set_apellido($apellido){
		$this->apellido = trim($apellido);
	}
	
	function set_telefono($telefono){
		$this->telefono = trim($telefono);
	}
    function set_imagen($imagen){
		$this->imagen = trim($imagen);
	}
    function set_email($email){
        //if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
       //     throw new InvalidArgumentException("Email no válido");
      //  }
		$this->email = trim($email);
	}
    function set_direccion($direccion){
		$this->direccion = trim($direccion);
	}
    function set_password($password){
		$this->password = trim($password);
	}
    
	
	function get_cedula(){
		return $this->cedula;
	}
	function get_fecha_nacimiento(){
		return $this->fecha_nacimiento;
	}
	function get_nombre(){
		return $this->nombre;
	}
	function get_apellido(){
		return $this->apellido;
	}
	function get_telefono(){
		return $this->telefono;
	}
    function get_imagen(){
		return $this->imagen;
	}
    function get_email(){
		return $this->email;
	}
    function get_direccion(){
		return $this->direccion;
	}
    function get_password(){
		return $this->password;
	}
   
    public function registerUser($cedula, $fecha_nacimiento, $nombre, $apellido, $telefono, $imagen, $email, $direccion, $password) {
        try {
            // Hash de la contraseña
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    
            $sql = "INSERT INTO t_usuario (cedula, fecha_nacimiento, nombre, apellido, telefono, imagen, email, direccion, password) 
                    VALUES (:cedula, :fecha_nacimiento, :nombre, :apellido, :telefono, :imagen, :email, :direccion, :password)";
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':cedula', $cedula);
            $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellido', $apellido);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':imagen', $imagen); // Usa $imagen (la ruta)
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':direccion', $direccion);
            $stmt->bindParam(':password', $passwordHash);
            
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                throw new Exception('El usuario o correo ya existe');
            }
            throw new Exception('Error al registrar usuario: ' . $e->getMessage());
        }
    }

    public function getUsers() {
        try {
            $stmt = $this->query("SELECT * FROM t_usuario");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            // Puedes manejar el error específico aquí o dejar que se propague
            throw $e;
        }
    }


    public function getUserById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM t_usuario WHERE id_usuario = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function updateUser($data) {
        try {
            $sql = "UPDATE t_usuario SET 
                    nombre = :nombre,
                    apellido = :apellido,
                    email = :email,
                    rol = :rol,
                    estado = :estado
                    " . (!empty($data['pass']) ? ", pass = :pass" : "") . "
                    WHERE id_usuario = :id_usuario";
                    
            $stmt = $this->db->prepare($sql);
            
            $params = [
                ':nombre' => $data['nombre'],
                ':apellido' => $data['apellido'],
                ':email' => $data['email'],
                ':rol' => $data['rol'],
                ':estado' => $data['estado'],
                ':id_usuario' => $data['id_usuario']
            ];
            
            if (!empty($data['password'])) {
                $params[':password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            }
            
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    public function deleteUser($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM t_usuario WHERE id_usuario = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function session($cedula, $pass) {
        try {
            $sql = 'SELECT *
                    FROM t_usuario 
                    WHERE cedula = :cedula 
                    LIMIT 1';
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':cedula' => $cedula]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$item) {
                return false;
            }
            
            if (!password_verify($pass, $item['pass'])) {
                return false;
            }
    
            // Eliminar contraseña del resultado
            unset($item['pass']);
            
            // Devolver los datos del usuario después de registrar en bitácora
            return $item;
    
        } catch(PDOException $e) {
            error_log("Error en session (PDO): " . $e->getMessage());
            return false;
        }
    }

    public function updatePass($id_usuario, $pass_actual, $nueva_pass) {
        try {
            // 1. Verificar que la contraseña actual sea correcta
            $query = $this->db->prepare("SELECT password FROM t_usuario WHERE id_usuario = :id_usuario");
            $query->execute([':id_usuario' => $id_usuario]);  // Corrección 1: parámetro nombrado
            $user = $query->fetch(PDO::FETCH_ASSOC);
    
            if (!$user) {
                return "Usuario no encontrado";
            }
            
            if (!password_verify($pass_actual, $user['password'])) {
                return "La contraseña actual es incorrecta";
            }
            
            // 2. Verificar que la nueva contraseña sea diferente
            if (password_verify($nueva_pass, $user['password'])) {
                return "La nueva contraseña debe ser diferente a la actual";
            }
            
            // 3. Hashear la nueva contraseña
            $nueva_pass_hash = password_hash($nueva_pass, PASSWORD_DEFAULT);
            
            // 4. Actualizar en la base de datos
            $update = $this->db->prepare("UPDATE t_usuario SET password = :nueva_pass WHERE id_usuario = :id_usuario");  // Corrección 2: parámetros nombrados
            $success = $update->execute([
                ':nueva_pass' => $nueva_pass_hash,
                ':id_usuario' => $id_usuario
            ]);
            
            if ($success && $update->rowCount() > 0) {  // Corrección 3: verificar filas afectadas
                return true;
            } else {
                return "Error al guardar la nueva contraseña";
            }
        } catch(PDOException $e) {
            error_log("Error en updatePass: " . $e->getMessage());  // Corrección 4: manejo de excepciones
            return "Error en el sistema";
        }
    }

    
    public function updateProfile($id_usuario, $cedula, $nombre, $email, $pass = null) {
        try {
            // Construir la consulta SQL dinámicamente
            $sql = "UPDATE t_usuario SET cedula = :cedula, nombre = :nombre, email = :email";
            
            // Solo añadir la contraseña si se proporciona
            if (!empty($pass)) {
                $sql .= ", pass = :pass";
                $hashedPass = password_hash($pass, PASSWORD_DEFAULT);
            }
            
            $sql .= " WHERE id_usuario = :id_usuario";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cedula', $cedula);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':email', $email);
            
            // Bind de la contraseña solo si existe
            if (!empty($pass)) {
                $stmt->bindParam(':pass', $hashedPass);
            }
            
            $stmt->bindParam(':id_usuario', $id_usuario);
            $stmt->execute();
            
            return true;
        } catch(PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function obtenerCedulaPorId($idUsuario) {
        $sql = "SELECT cedula FROM t_usuario WHERE id_usuario = ? AND estatus = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idUsuario]);
        return $stmt->fetchColumn(); // devuelve la cédula
    }
    
}