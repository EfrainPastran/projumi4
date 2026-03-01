<?php
namespace App\Models;
use PDO;
use PDOException;
use Exception;
use App\Model;
class UsuariosModel extends Model {

    private $id_usuario;
    private $cedula;
    private $nombre;
    private $apellido;
    private $correo;
    private $password;
    private $direccion;
    private $telefono;
    private $fecha_registro;
    private $fecha_nacimiento;
    private $estatus;
    private $fk_rol;

    public function __construct() {
        try {
            $this->db = new PDO(DB_DSN, DB_USER, DB_PASS, DB_OPTIONS);
        } catch (PDOException $e) {
            error_log('Error de conexión: ' . $e->getMessage());
            throw new Exception('Error al conectar con la base de datos');
        }
    }

    public function setData(        
        $cedula,
        $nombre,
        $apellido,
        $correo,
        $password,
        $direccion,
        $telefono,
        $fecha_registro,
        $fecha_nacimiento,
        $estatus,
        $fk_rol,
        $id_usuario = null
    ) {
        $this->id_usuario = $id_usuario;
        $this->cedula = $cedula;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->correo = $correo;
        $this->password = $password;
        $this->direccion = $direccion;
        $this->telefono = $telefono;
        $this->fecha_registro = $fecha_registro;
        $this->fecha_nacimiento = $fecha_nacimiento;
        $this->estatus = $estatus;
        $this->fk_rol = $fk_rol;
    }

    // Métodos CRUD
    public function getUsuarios() {
        try {
            $sql = "SELECT u.*, r.nombre as rol_nombre 
                    FROM t_usuario u
                    JOIN t_rol r ON u.fk_rol = r.id_rol  WHERE u.estatus = 1 OR u.estatus = 0
                    ORDER BY u.id_usuario DESC ";
            $stmt = $this->query($sql);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getUsuarioById($id) {
        try {
            $sql = 'SELECT * FROM t_usuario WHERE id_usuario = :id LIMIT 1';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error en getUsuarioById (PDO): " . $e->getMessage());
            return false;
        }
    }

    public function getUsuarioByCedula($cedula) {
        try {
            $sql = 'SELECT * FROM t_usuario WHERE cedula = :cedula LIMIT 1';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':cedula' => $cedula]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error en getUsuarioByCedula (PDO): " . $e->getMessage());
            return false;
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

    public function getUsuarioByEmail($correo) {
        try {
            $sql = 'SELECT * FROM t_usuario WHERE correo = :correo LIMIT 1';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':correo' => $correo]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error en getUsuarioByEmail (PDO): " . $e->getMessage());
            return false;
        }
    }

    private function validarDatos(bool $esActualizacion = false)
    {
        if(!$esActualizacion){
            if (empty($this->cedula) || !ctype_digit($this->cedula) || strlen($this->cedula) < 6) {
                return ['success' => false, 'message' => 'La cédula es obligatoria y debe ser numérica (mínimo 6 dígitos).'];
            }
        }
        if (empty($this->nombre) || strlen($this->nombre) < 2) {
            return ['success' => false, 'message' => 'El nombre es obligatorio y debe tener al menos 2 caracteres.'];
        }

        if (empty($this->apellido) || strlen($this->apellido) < 2) {
            return ['success' => false, 'message' => 'El apellido es obligatorio y debe tener al menos 2 caracteres.'];
        }

        if (empty($this->correo)) {
            return ['success' => false, 'message' => 'El correo electrónico no es válido.'];
        }

        // Solo exigir contraseña si NO es actualización
        if (!$esActualizacion) {
            if (empty($this->password) || strlen($this->password) < 6) {
                return ['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres.'];
            }
        } else {
            // Si se envía una nueva contraseña en la actualización, debe cumplir los requisitos
            if (!empty($this->password) && strlen($this->password) < 6) {
                return ['success' => false, 'message' => 'La nueva contraseña debe tener al menos 6 caracteres.'];
            }
        }

        if (!empty($this->telefono) && !preg_match('/^[0-9]{10,15}$/', $this->telefono)) {
            return ['success' => false, 'message' => 'El número de teléfono no tiene un formato válido.'];
        }

        if (!in_array($this->estatus, [0, 1])) {
            return ['success' => false, 'message' => 'El estatus debe ser 0 (inactivo) o 1 (activo).'];
        }

        if ($this->fk_rol <= 0) {
            return ['success' => false, 'message' => 'Debe especificar un rol de usuario válido.'];
        }

        return ['success' => true];
    }


    public function registerUsuario()
    {
        try {
            // Validar los datos de entrada con tu función actual
            $validacion = $this->validarDatos();
            if (!$validacion['success']) {
                return $validacion;
            }

            // Validar duplicado por cédula
            $existeCedula = $this->getUsuarioByCedula($this->cedula);
            if ($existeCedula) {
                return [
                    'success' => false,
                    'message' => 'La cédula ya está registrada en el sistema.'
                ];
            }

            // Validar duplicado por correo
            $sqlCorreo = "SELECT COUNT(*) FROM t_usuario WHERE correo = :correo";
            $stmtCorreo = $this->db->prepare($sqlCorreo);
            $stmtCorreo->execute([':correo' => $this->correo]);
            if ($stmtCorreo->fetchColumn() > 0) {
                return [
                    'success' => false,
                    'message' => 'El correo ya está registrado en el sistema.'
                ];
            }

            // Insertar usuario si todo es válido
            $sql = 'INSERT INTO t_usuario (
                        cedula, nombre, apellido, correo, password,
                        direccion, telefono, fecha_registro,
                        fecha_nacimiento, estatus, fk_rol
                    ) VALUES (
                        :cedula, :nombre, :apellido, :correo, :password,
                        :direccion, :telefono, :fecha_registro,
                        :fecha_nacimiento, :estatus, :fk_rol
                    )';

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':cedula' => $this->cedula,
                ':nombre' => $this->nombre,
                ':apellido' => $this->apellido,
                ':correo' => $this->correo,
                ':password' => password_hash($this->password, PASSWORD_DEFAULT),
                ':direccion' => $this->direccion,
                ':telefono' => $this->telefono,
                ':fecha_registro' => $this->fecha_registro,
                ':fecha_nacimiento' => $this->fecha_nacimiento,
                ':estatus' => $this->estatus,
                ':fk_rol' => $this->fk_rol
            ]);

            // Retornar resultado exitoso
            return [
                'success' => true,
                'message' => 'Usuario registrado correctamente.',
                'id_usuario' => $this->db->lastInsertId()
            ];

        } catch (PDOException $e) {
            error_log("Error en registerUsuario(): " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al registrar el usuario: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Excepción: ' . $e->getMessage()
            ];
        }
    }

    public function updateUsuario()
    {
        try {
            // Validar datos antes de actualizar
            $validacion = $this->validarDatos(true); // parámetro true indica que es actualización
            if (!$validacion['success']) {
                return $validacion;
            }

            // Validar que el usuario exista
            $sqlCheck = "SELECT id_usuario FROM t_usuario WHERE id_usuario = :id_usuario";
            $stmtCheck = $this->db->prepare($sqlCheck);
            $stmtCheck->execute([':id_usuario' => $this->id_usuario]);
            if (!$stmtCheck->fetch()) {
                return [
                    'success' => false,
                    'message' => 'El usuario especificado no existe.'
                ];
            }

            // Verificar duplicados (cedula y correo) con excepción del mismo usuario
            $sqlCorreo = "SELECT COUNT(*) FROM t_usuario WHERE correo = :correo AND id_usuario != :id_usuario";
            $stmtCorreo = $this->db->prepare($sqlCorreo);
            $stmtCorreo->execute([':correo' => $this->correo, ':id_usuario' => $this->id_usuario]);
            if ($stmtCorreo->fetchColumn() > 0) {
                return [
                    'success' => false,
                    'message' => 'El correo ya está registrado en otro usuario.'
                ];
            }

            // Construir SQL dinámicamente
            $sql = 'UPDATE t_usuario 
                    SET nombre = :nombre,
                        apellido = :apellido,
                        correo = :correo,
                        direccion = :direccion,
                        telefono = :telefono,
                        fecha_nacimiento = :fecha_nacimiento,
                        estatus = :estatus,
                        fk_rol = :fk_rol';

            $params = [
                ':id_usuario' => $this->id_usuario,
                ':nombre' => $this->nombre,
                ':apellido' => $this->apellido,
                ':correo' => $this->correo,
                ':direccion' => $this->direccion,
                ':telefono' => $this->telefono,
                ':fecha_nacimiento' => $this->fecha_nacimiento,
                ':estatus' => $this->estatus,
                ':fk_rol' => $this->fk_rol
            ];

            // Solo actualizar password si se envía una nueva
            if (!empty($this->password)) {
                $sql .= ', password = :password';
                $params[':password'] = password_hash($this->password, PASSWORD_DEFAULT);
            }

            $sql .= ' WHERE id_usuario = :id_usuario';

            // Ejecutar actualización
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            if ($stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Usuario actualizado correctamente.'
                ];
            } else {
                return [
                    'success' => true,
                    'message' => 'No se realizaron cambios (datos idénticos).'
                ];
            }

        } catch (PDOException $e) {
            error_log("Error en updateUsuario(): " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al actualizar usuario: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Excepción: ' . $e->getMessage()
            ];
        }
    }

    public function deleteUsuario($id)
    {
        try {
            // Validación básica
            if (empty($id) || !ctype_digit((string)$id)) {
                return [
                    'success' => false,
                    'message' => 'El ID de usuario es inválido.'
                ];
            }

            // Verificar si el usuario existe y no está ya eliminado
            $sqlCheck = "SELECT estatus FROM t_usuario WHERE id_usuario = :id";
            $stmtCheck = $this->db->prepare($sqlCheck);
            $stmtCheck->execute([':id' => $id]);
            $usuario = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if (!$usuario) {
                return [
                    'success' => false,
                    'message' => 'El usuario no existe.'
                ];
            }

            if ($usuario['estatus'] == 2) {
                return [
                    'success' => false,
                    'message' => 'El usuario ya está eliminado.'
                ];
            }

            // Eliminación lógica
            $sql = "UPDATE t_usuario SET estatus = 2 WHERE id_usuario = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);

            if ($stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Usuario eliminado correctamente.',
                    'rows_affected' => $stmt->rowCount()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'No se realizaron cambios (usuario no actualizado).'
                ];
            }

        } catch (PDOException $e) {
            error_log("Error en deleteUsuario (PDO): " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al eliminar el usuario: ' . $e->getMessage()
            ];
        }
    }

    public function cambiarEstatus($id_usuario, $nuevo_estatus)
    {
        try {
            // Validaciones mínimas
            if (empty($id_usuario) || !ctype_digit((string)$id_usuario)) {
                return [
                    'success' => false,
                    'message' => 'ID de usuario inválido.'
                ];
            }

            if (!in_array($nuevo_estatus, [0, 1])) {
                return [
                    'success' => false,
                    'message' => 'Estatus inválido. Solo se permite 0 o 1.'
                ];
            }

            // Verificar si el usuario existe
            $sqlCheck = "SELECT estatus FROM t_usuario WHERE id_usuario = :id";
            $stmtCheck = $this->db->prepare($sqlCheck);
            $stmtCheck->execute([':id' => $id_usuario]);
            $usuario = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if (!$usuario) {
                return [
                    'success' => false,
                    'message' => 'El usuario no existe.'
                ];
            }

            // Evitar actualizar si ya tiene ese mismo estatus
            if ((int)$usuario['estatus'] === (int)$nuevo_estatus) {
                return [
                    'success' => false,
                    'message' => 'El usuario ya tiene ese estatus.'
                ];
            }

            // Actualizar estatus
            $sql = "UPDATE t_usuario SET estatus = :estatus WHERE id_usuario = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id' => $id_usuario,
                ':estatus' => $nuevo_estatus
            ]);

            if ($stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Estatus actualizado correctamente.',
                    'nuevo_estatus' => $nuevo_estatus
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'No se realizaron cambios en el estatus.'
                ];
            }

        } catch (PDOException $e) {
            error_log("Error en cambiarEstatus (PDO): " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al cambiar estatus: ' . $e->getMessage()
            ];
        }
    }

    public function activarUsuarioPorCedula($cedula) {
        try {
            $sql = "UPDATE t_usuario SET estatus = 1 WHERE cedula = :cedula";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cedula', $cedula, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return true; // Usuario activado correctamente
            } else {
                return false; // No se actualizó ningún registro (puede que no exista)
            }
        } catch (PDOException $e) {
            error_log("Error en aprobarUsuarioPorCedula: " . $e->getMessage());
            return false;
        }
    }
    
    public function desactivarUsuarioPorCedula($cedula) {
        try {
            $sql = "UPDATE t_usuario SET estatus = 1 WHERE cedula = :cedula";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cedula', $cedula, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return true; // Usuario activado correctamente
            } else {
                return false; // No se actualizó ningún registro (puede que no exista)
            }
        } catch (PDOException $e) {
            error_log("Error en aprobarUsuarioPorCedula: " . $e->getMessage());
            return false;
        }
    }
}