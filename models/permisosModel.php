<?php
namespace App\Models;
use PDO;
use PDOException;
use Exception;
use App\Model;
class permisosModel extends Model {

    private $id;
    private $fk_rol;
    private $fk_modulo;
    private $fk_permiso;

    public function __construct() {
        try {
            $this->db = new PDO(DB_DSN, DB_USER, DB_PASS, DB_OPTIONS);
        } catch (PDOException $e) {
            error_log('Error de conexión: ' . $e->getMessage());
            throw new Exception('Error al conectar con la base de datos');
        }
    }

    public function setData($id, $fk_rol, $fk_modulo, $fk_permiso) {
        $this->id = $id;
        $this->fk_rol = $fk_rol;
        $this->fk_modulo = $fk_modulo;
        $this->fk_permiso = $fk_permiso;
    }

    // Obtener todos los permisos asignados
    public function obtenerTodos() {
        try {
            $stmt = $this->query("SELECT * FROM t_permiso_rol_modulo");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Obtener permisos por rol y módulo
    public function obtenerPorRolYModulo($fk_rol, $fk_modulo) {
        try {
            $sql = "SELECT p.nombre 
                    FROM t_permiso_rol_modulo prm
                    INNER JOIN t_permisos p ON prm.fk_permiso = p.id_permisos
                    WHERE prm.fk_rol = :rol AND prm.fk_modulo = :modulo";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':rol' => $fk_rol, ':modulo' => $fk_modulo]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error en obtenerPorRolYModulo (PDO): " . $e->getMessage());
            return false;
        }
    }

    // Asignar permiso a rol y módulo
    public function asignarPermiso($fk_rol, $fk_modulo, $fk_permiso, $estatus)
    {
        try {
            // Validaciones de campos
            if (empty($fk_rol) || empty($fk_modulo) || empty($fk_permiso)) {
                return [
                    'success' => false,
                    'message' => 'Todos los campos (rol, módulo y permiso) son obligatorios.'
                ];
            }

            // Verificar si el rol existe
            $sqlRol = "SELECT COUNT(*) FROM t_rol WHERE id_rol = :id";
            $stmtRol = $this->db->prepare($sqlRol);
            $stmtRol->execute([':id' => $fk_rol]);
            if ($stmtRol->fetchColumn() == 0) {
                return [
                    'success' => false,
                    'message' => 'El rol especificado no existe.'
                ];
            }

            // Verificar si el módulo existe
            $sqlModulo = "SELECT COUNT(*) FROM t_modulo WHERE id_modulo = :id";
            $stmtModulo = $this->db->prepare($sqlModulo);
            $stmtModulo->execute([':id' => $fk_modulo]);
            if ($stmtModulo->fetchColumn() == 0) {
                return [
                    'success' => false,
                    'message' => 'El módulo especificado no existe.'
                ];
            }

            // Verificar si el permiso existe
            $sqlPermiso = "SELECT COUNT(*) FROM t_permisos WHERE id_permisos = :id";
            $stmtPermiso = $this->db->prepare($sqlPermiso);
            $stmtPermiso->execute([':id' => $fk_permiso]);
            if ($stmtPermiso->fetchColumn() == 0) {
                return [
                    'success' => false,
                    'message' => 'El permiso especificado no existe.'
                ];
            }

            // Verificar si ya existe la asignación (rol + módulo + permiso)
            $sqlDup = "SELECT COUNT(*) FROM t_permiso_rol_modulo 
                    WHERE fk_rol = :rol AND fk_modulo = :modulo AND fk_permiso = :permiso";
            $stmtDup = $this->db->prepare($sqlDup);
            $stmtDup->execute([
                ':rol' => $fk_rol,
                ':modulo' => $fk_modulo,
                ':permiso' => $fk_permiso
            ]);
            if ($stmtDup->fetchColumn() > 0) {
                return [
                    'success' => false,
                    'message' => 'Este permiso ya está asignado a ese rol y módulo.'
                ];
            }

            // Insertar asignación
            $valorEstatus = $estatus ? 1 : 0;

            $sql = "INSERT INTO t_permiso_rol_modulo (fk_rol, fk_modulo, fk_permiso, estatus) 
                    VALUES (:rol, :modulo, :permiso, :estatus)";
            
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([
                ':rol'     => $fk_rol,
                ':modulo'  => $fk_modulo,
                ':permiso' => $fk_permiso,
                ':estatus' => $valorEstatus
            ]);


            if ($ok) {
                return [
                    'success' => true,
                    'message' => 'Permiso asignado exitosamente.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'No se pudo asignar el permiso.'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error en asignarPermiso (PDO): " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error de base de datos: ' . $e->getMessage()
            ];
        }
    }


    public function obtenerMapaPermisos() {
        $stmt = $this->db->query("SELECT id_permisos, nombre FROM t_permisos");
        $mapa = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $mapa[$row['nombre']] = $row['id_permisos'];
        }
        return $mapa;
    }

    // Eliminar un permiso asignado
    public function eliminarPermiso() {
        try {
            $sql = 'DELETE FROM t_permiso_rol_modulo 
                    WHERE fk_rol = :rol AND fk_modulo = :modulo AND fk_permiso = :permiso';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':rol' => $this->fk_rol,
                ':modulo' => $this->fk_modulo,
                ':permiso' => $this->fk_permiso
            ]);
        } catch(PDOException $e) {
            error_log("Error en eliminarPermiso (PDO): " . $e->getMessage());
            return false;
        }
    }

    // Obtener todos los módulos y permisos de un rol
    public function obtenerPermisosPorRol($fk_rol) {
        try {
            $sql = "SELECT 
                        m.id_modulo AS modulo_id,
                        m.nombre AS modulo,
                        p.nombre AS permiso
                    FROM t_permiso_rol_modulo prm
                    INNER JOIN t_modulo m ON prm.fk_modulo = m.id_modulo
                    INNER JOIN t_permisos p ON prm.fk_permiso = p.id_permisos
                    WHERE prm.fk_rol = :rol order by m.nombre, p.nombre DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':rol' => $fk_rol]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error en obtenerPermisosPorRol (PDO): " . $e->getMessage());
            return false;
        }
    }
    
    public function eliminarTodosPorRol($fk_rol) {
        try {
            $sql = "DELETE FROM t_permiso_rol_modulo WHERE fk_rol = :rol";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':rol' => $fk_rol]);
        } catch (PDOException $e) {
            error_log("Error en eliminarTodosPorRol: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerPermisosPorRolYModulo($nombre_rol, $nombre_modulo) {
        $sql = "SELECT p.nombre 
                FROM t_permiso_rol_modulo prm
                INNER JOIN t_modulo m ON prm.fk_modulo = m.id_modulo
                INNER JOIN t_permisos p ON prm.fk_permiso = p.id_permisos
                INNER JOIN t_rol r ON prm.fk_rol = r.id_rol
                WHERE r.nombre = :rol 
                AND m.nombre = :modulo 
                AND prm.estatus = 1 AND m.estatus=1 ";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':rol' => $nombre_rol, ':modulo' => $nombre_modulo]);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Estructura por defecto
        $mapa = [
            'consultar' => false,
            'registrar' => false,
            'actualizar' => false,
            'eliminar'  => false
        ];

        foreach ($resultados as $row) {
            $mapa[strtolower($row['nombre'])] = true;
        }

        return $mapa;
    }
    
}
