<?php
namespace App\Models;
use PDO;
use PDOException;
use Exception;
use App\Model;
class rolesModel extends Model {

    private $id;
    private $nombre;
    private $descripcion;
    private $estatus;

    public function __construct() {
        try {
            $this->db = new PDO(DB_DSN, DB_USER, DB_PASS, DB_OPTIONS);
        } catch (PDOException $e) {
            error_log('Error de conexión: ' . $e->getMessage());
            throw new Exception('Error al conectar con la base de datos');
        }
    }

    public function setData($id, $nombre, $descripcion, $estatus) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->estatus = $estatus;
    }

    // Obtener todos los roles
    public function getRoles() {
        try {
            $stmt = $this->query("SELECT * FROM t_rol");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Obtener un rol por nombre
    public function getByName($nombre) {
        try {
            $sql = 'SELECT * FROM t_rol WHERE nombre = :nombre LIMIT 1';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':nombre' => $nombre]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error en getByName (PDO): " . $e->getMessage());
            return false;
        }
    }

    // Registrar nuevo rol
    public function registerRol() {
        try {
            // === VALIDACIONES ===
            // 1. Validar campos vacíos
            if (empty($this->nombre)) {
                return ['success' => false, 'message' => 'El nombre del rol es obligatorio.'];
            }

            if (empty($this->descripcion)) {
                return ['success' => false, 'message' => 'La descripción del rol es obligatoria.'];
            }

            if (!isset($this->estatus) || $this->estatus === '') {
                return ['success' => false, 'message' => 'El estatus del rol es obligatorio.'];
            }

            // 2. Verificar duplicados por nombre
            $sqlCheck = 'SELECT COUNT(*) AS total FROM t_rol WHERE LOWER(nombre) = LOWER(:nombre)';
            $stmtCheck = $this->db->prepare($sqlCheck);
            $stmtCheck->execute([':nombre' => trim($this->nombre)]);
            $exists = $stmtCheck->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            if ($exists > 0) {
                return ['success' => false, 'message' => 'Ya existe un rol con ese nombre.'];
            }

            // 3. Insertar si todo está correcto
            $sql = 'INSERT INTO t_rol (nombre, descripcion_rol, estatus) 
                    VALUES (:nombre, :descripcion, :estatus)';
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([
                ':nombre' => trim($this->nombre),
                ':descripcion' => trim($this->descripcion),
                ':estatus' => $this->estatus
            ]);

            if ($ok) {
                return ['success' => true, 'message' => 'Rol registrado exitosamente.'];
            } else {
                return ['success' => false, 'message' => 'No se pudo registrar el rol.'];
            }

        } catch (PDOException $e) {
            error_log("Error en registerRol (PDO): " . $e->getMessage());
            return ['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }

    // Actualizar rol
    public function update() {
        try {
            // Validaciones de campos obligatorios
            if (empty($this->id) || empty($this->nombre)) {
                return [
                    'success' => false,
                    'message' => 'El ID y el nombre del rol son obligatorios.'
                ];
            }

            // Verificar si el rol existe
            $checkSql = 'SELECT COUNT(*) FROM t_rol WHERE id_rol = :id';
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute([':id' => $this->id]);
            if ($checkStmt->fetchColumn() == 0) {
                return [
                    'success' => false,
                    'message' => 'El rol especificado no existe.'
                ];
            }

            // Verificar si el nuevo nombre ya existe (en otro rol)
            $dupSql = 'SELECT COUNT(*) FROM t_rol WHERE nombre = :nombre AND id_rol != :id';
            $dupStmt = $this->db->prepare($dupSql);
            $dupStmt->execute([
                ':nombre' => $this->nombre,
                ':id' => $this->id
            ]);
            if ($dupStmt->fetchColumn() > 0) {
                return [
                    'success' => false,
                    'message' => 'Ya existe un rol con ese nombre.'
                ];
            }

            // Actualizar registro
            $sql = 'UPDATE t_rol 
                    SET nombre = :nombre, 
                        descripcion_rol = :descripcion, 
                        estatus = :estatus 
                    WHERE id_rol = :id';
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute([
                ':id' => $this->id,
                ':nombre' => trim($this->nombre),
                ':descripcion' => trim($this->descripcion),
                ':estatus' => (int)$this->estatus
            ]);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Rol actualizado correctamente.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'No se pudo actualizar el rol.'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error en update (PDO): " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error de base de datos: ' . $e->getMessage()
            ];
        }
    }

    // Eliminar rol
    public function delete()
    {
        try {
            // Validación de ID
            if (empty($this->id)) {
                return [
                    'success' => false,
                    'message' => 'El ID del rol es obligatorio para eliminarlo.'
                ];
            }

            //  Verificar si el rol existe
            $checkSql = 'SELECT COUNT(*) FROM t_rol WHERE id_rol = :id';
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute([':id' => $this->id]);
            if ($checkStmt->fetchColumn() == 0) {
                return [
                    'success' => false,
                    'message' => 'El rol especificado no existe o ya fue eliminado.'
                ];
            }

            // Intentar eliminar
            $sql = 'DELETE FROM t_rol WHERE id_rol = :id';
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute([':id' => $this->id]);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Rol eliminado correctamente.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'No se pudo eliminar el rol.'
                ];
            }

        } catch (PDOException $e) {
            error_log("Error en delete (PDO): " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error de base de datos: ' . $e->getMessage()
            ];
        }
    }

}
