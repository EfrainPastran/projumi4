<?php
namespace App;
use App\Models\permisosModel;
use Exception;
use PDO;

class Middleware extends Model
{
    /**
     * Verifica y carga el tipo de usuario y sus permisos.
     */
    public function verificarTipoUsuario($cedula): array
    {
        // Si ya está en sesión, evitar volver a consultar
        if (!empty($_SESSION['user']['tipo']) && !empty($_SESSION['user']['rol'])) {
            return $_SESSION['user']['tipo'];
        }

        try {
            $conn_projumi = Database::getInstance('projumi');
            $conn_super = Database::getInstance('default');
        } catch (Exception $e) {
            error_log('Error al obtener conexión: ' . $e->getMessage());
            return ['usuario'];
        }

        $tipo = ['usuario'];

        // === SUPER USUARIO ===
        $querySuper = $conn_super->prepare("
            SELECT u.id_usuario, r.id_rol, r.nombre AS rol
            FROM t_usuario u
            INNER JOIN t_rol r ON u.fk_rol = r.id_rol
            WHERE u.cedula = ? AND r.nombre = 'Super Usuario'
        ");
        $querySuper->execute([$cedula]);
        $super = $querySuper->fetch(PDO::FETCH_ASSOC);
        if ($super) {
            $_SESSION['user']['tipo'] = ['super_usuario'];
            $_SESSION['user']['rol'] = $this->obtenerPermisosPorRol($conn_super, $super['id_rol']);
            return $_SESSION['user']['tipo'];
        }

        // === ADMINISTRADOR ===
        $queryAdmin = $conn_super->prepare("
            SELECT u.id_usuario, r.id_rol, r.nombre AS rol
            FROM t_usuario u
            INNER JOIN t_rol r ON u.fk_rol = r.id_rol
            WHERE u.cedula = ? AND r.nombre = 'Administrador'
        ");
        $queryAdmin->execute([$cedula]);
        $admin = $queryAdmin->fetch(PDO::FETCH_ASSOC);
        if ($admin) {
            $_SESSION['user']['tipo'] = ['administrador'];
            $_SESSION['user']['rol'] = $this->obtenerPermisosPorRol($conn_super, $admin['id_rol']);
            return $_SESSION['user']['tipo'];
        }

        // === EMPRENDEDOR ===
        $queryEmp = $conn_super->prepare("
            SELECT u.id_usuario, r.id_rol, r.nombre AS rol
            FROM t_usuario u
            INNER JOIN t_rol r ON u.fk_rol = r.id_rol
            WHERE u.cedula = ? AND r.nombre = 'Emprendedor'
        ");
        $queryEmp->execute([$cedula]);
        $Emp = $queryEmp->fetch(PDO::FETCH_ASSOC);
        if ($Emp) {
            $_SESSION['user']['tipo'] = ['emprendedor'];
            $_SESSION['user']['rol'] = $this->obtenerPermisosPorRol($conn_super, $Emp['id_rol']);
            return $_SESSION['user']['tipo'];
        }

        // === CLIENTE ===
        $queryCli = $conn_super->prepare("
            SELECT u.id_usuario, r.id_rol, r.nombre AS rol
            FROM t_usuario u
            INNER JOIN t_rol r ON u.fk_rol = r.id_rol
            WHERE u.cedula = ? AND r.nombre = 'Cliente'
        ");
        $queryCli->execute([$cedula]);
        $Cli = $queryCli->fetch(PDO::FETCH_ASSOC);
        if ($Cli) {
            $_SESSION['user']['tipo'] = ['cliente'];
            $_SESSION['user']['rol'] = $this->obtenerPermisosPorRol($conn_super, $Cli['id_rol']);
            return $_SESSION['user']['tipo'];
        }

        // Valor visitante
        $_SESSION['user']['tipo'] = ['visitante'];
        $_SESSION['user']['rol'] = $this->obtenerPermisosPorRol($conn_super, 5);
        return $_SESSION['user']['tipo'];
    }

    /**
     * Obtiene todos los permisos y módulos asociados a un rol.
     */
    private function obtenerPermisosPorRol(PDO $conn, int $idRol): array
    {
        try {
            $sql = "
                SELECT 
                r.id_rol,
                r.nombre AS rol,
                m.id_modulo,
                m.nombre AS modulo,
                m.ruta,
                m.icono,
                m.orden,
                m.menu_padre,
                p.id_permisos,
                p.nombre AS permiso
            FROM t_permiso_rol_modulo prm
            INNER JOIN t_rol r ON prm.fk_rol = r.id_rol
            INNER JOIN t_modulo m ON prm.fk_modulo = m.id_modulo
            INNER JOIN t_permisos p ON prm.fk_permiso = p.id_permisos
            WHERE prm.fk_rol = ? AND prm.estatus = 1
            ORDER BY m.orden ASC;
        ";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$idRol]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $rolData = [
                'rol' => null,
                'modulos' => [],
                'permisos' => []
            ];

            foreach ($result as $row) {
                $rolData['rol'] = $row['rol'];
                $rolData['modulos'][$row['id_modulo']] = [
                    'nombre' => $row['modulo'],
                    'ruta' => $row['ruta'],
                    'icono' => $row['icono'],
                    'orden' => $row['orden'],
                    'menu_padre' => $row['menu_padre']
                ];
                $rolData['permisos'][$row['id_modulo']][] = $row['permiso'];
            }
            // Reordenar permisos por módulo
            $ordenPermisos = ['consultar','registrar','actualizar','eliminar'];
            foreach ($rolData['permisos'] as $moduloId => $permisos) {
                $rolData['permisos'][$moduloId] = array_values(array_intersect($ordenPermisos, $permisos));
            }

            return $rolData;
        } catch (Exception $e) {
            error_log('Error al obtener permisos de rol: ' . $e->getMessage());
            return ['rol' => null, 'modulos' => [], 'permisos' => []];
        }
    }

    /**
     * Obtiene los datos del emprendimiento por cédula
     */
    public function obtenerDatosEmprendimiento($cedula): ?array {
        try {
            $conn_projumi = Database::getInstance('projumi');
            $stmt = $conn_projumi->prepare("
                SELECT emprendimiento, imagen 
                FROM t_emprendedor 
                WHERE cedula = ?
            ");
            $stmt->execute([$cedula]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                return [
                    'emprendimiento' => $data['emprendimiento'],
                    'logo' => $data['imagen']
                ];
            }

            return null;
        } catch (Exception $e) {
            error_log('Error al obtener datos del emprendimiento: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Limpieza al finalizar
     */
    public function __destruct() {
        Database::closeAll();
    }

    public function obtenerPermisosDinamicos($nombre_rol, $nombre_modulo) {
        $model = new permisosModel();
        // Consultamos a la BD el mapa de permisos activo para este rol en este módulo
        $permisos = $model->obtenerPermisosPorRolYModulo($nombre_rol, $nombre_modulo);
        
        // Retornamos un array asociativo como el que usábamos antes
        // Ej: ['consultar' => true, 'registrar' => false, ...]
        return $permisos;
    }
}

