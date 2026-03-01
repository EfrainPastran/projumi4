<?php
namespace App\Models;
use PDO;
use PDOException;
use Exception;
use App\Model;
use App\Models\UsuariosModel;
class EmprendedorModel extends Model {
    protected $db;
    private $id_emprededor;
    private $cedula;
    private $lugar_nacimiento;
    private $estado_civil;
    private $nacionalidad;
    private $rif;
    private $sexo;
    private $alergia_medicamento;
    private $alergia_alimento;
    private $operado;
    private $sacramento;
    private $grupo_sangre;
    private $religion;
    private $grupo_activo;
    private $cantidad_hijos;
    private $carga_familiar;
    private $casa_propia;
    private $alquiler;
    private $titulo_academico;
    private $profesion;
    private $oficio;
    private $hobby;
    private $conocimiento_projumi;
    private $motivo_projumi;
    private $aporte_projumi;
    private $imagen;
    private $emprendimiento;
    private $estatus;
    private $fk_parroquia;

    public function __construct() {
        try {
            $this->db = new PDO(DB_DSN_PROJUMI, DB_USER, DB_PASS, DB_OPTIONS);
        } catch (PDOException $e) {
            error_log('Error de conexión EmprendedorModel: ' . $e->getMessage());
            throw new Exception('No se pudo conectar al modelo de emprendedor');
        }
    }

public function setData(
    $id_emprededor,
    $cedula,
    $lugar_nacimiento,
    $estado_civil,
    $nacionalidad,
    $rif,
    $sexo,
    $alergia_medicamento,
    $alergia_alimento,
    $operado,
    $sacramento,
    $grupo_sangre,
    $religion,
    $grupo_activo,
    $cantidad_hijos,
    $carga_familiar,
    $casa_propia,
    $alquiler,
    $titulo_academico,
    $profesion,
    $oficio,
    $hobby,
    $conocimiento_projumi,
    $motivo_projumi,
    $aporte_projumi,
    $imagen,
    $emprendimiento,
    $estatus,
    $fk_parroquia
) {
    $this->id_emprededor = $id_emprededor;
    $this->cedula = $cedula;
    $this->lugar_nacimiento = $lugar_nacimiento;
    $this->estado_civil = $estado_civil;
    $this->nacionalidad = $nacionalidad;
    $this->rif = $rif;
    $this->sexo = $sexo;
    $this->alergia_medicamento = $alergia_medicamento;
    $this->alergia_alimento = $alergia_alimento;
    $this->operado = $operado;
    $this->sacramento = $sacramento;
    $this->grupo_sangre = $grupo_sangre;
    $this->religion = $religion;
    $this->grupo_activo = $grupo_activo;
    $this->cantidad_hijos = $cantidad_hijos;
    $this->carga_familiar = $carga_familiar;
    $this->casa_propia = $casa_propia;
    $this->alquiler = $alquiler;
    $this->titulo_academico = $titulo_academico;
    $this->profesion = $profesion;
    $this->oficio = $oficio;
    $this->hobby = $hobby;
    $this->conocimiento_projumi = $conocimiento_projumi;
    $this->motivo_projumi = $motivo_projumi;
    $this->aporte_projumi = $aporte_projumi;
    $this->imagen = $imagen;
    $this->emprendimiento = $emprendimiento;
    $this->estatus = $estatus;
    $this->fk_parroquia = $fk_parroquia;
}

    public function obtenerMunicipios() {
        try {
            $sql = "SELECT id_municipio, nombre FROM t_municipio WHERE estatus = 1 ORDER BY nombre";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error al obtener municipios: ' . $e->getMessage());
            return [];
        }
    }

    public function obtenerParroquiasPorMunicipio($id_municipio) {
        try {
            $sql = "SELECT id_parroquia, parroquia FROM t_parroquia 
                    WHERE fk_municipio = :id_municipio AND estatus = 1 
                    ORDER BY parroquia";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id_municipio' => $id_municipio]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error al obtener parroquias: ' . $e->getMessage());
            return [];
        }
    }

    public function obtenerMunicipiosConParroquias() {
        try {
            $sql = "SELECT m.id_municipio, m.nombre AS municipio, 
                           p.id_parroquia, p.parroquia
                    FROM t_municipio m
                    LEFT JOIN t_parroquia p ON m.id_municipio = p.fk_municipio
                    WHERE m.estatus = 1 AND (p.estatus = 1 OR p.estatus IS NULL)
                    ORDER BY m.nombre, p.parroquia";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error al obtener municipios con parroquias: ' . $e->getMessage());
            return [];
        }
    }

    function existeEmprendedor($idEmprendedor) {
        $sql = "SELECT COUNT(*) FROM t_emprendedor WHERE id_emprededor = ? AND estatus = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idEmprendedor]);
        return $stmt->fetchColumn() > 0;
    }

    public function obtenerIdEmprendedorPorRif($rif) {
        $sql = "SELECT id_emprededor FROM t_emprendedor WHERE cedula = ? AND estatus = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$rif]);
        return $stmt->fetchColumn(); // devuelve id_emprededor
    }  

    public function getByCedula($cedula) {
        $sql = "SELECT * FROM t_emprendedor WHERE cedula = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$cedula]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //agragade en el HOST
    /*public function obtenerCedulaPorId($id) { // mirar la funcion que esta en userModel.php de obtener la cedula por id
        try {
            $query = "SELECT cedula FROM t_emprendedor WHERE id_emprededor = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['cedula'] : null;
        } catch (PDOException $e) {
            error_log("Error al obtener cédula: " . $e->getMessage());
            return null;
        }
    }*/

    public function getEmprendedores() {
        try {
            $stmt = $this->query("
            SELECT 
            e.id_emprededor,
            u.nombre,
            u.apellido,
            e.emprendimiento,
            e.imagen AS imagen_emprendedor,
            c.id_categoria,
            c.nombre AS nombre_categoria,
            c.descripcion AS descripcion_categoria,
            COUNT(p.id_producto) AS cantidad_productos
        FROM ".BD_PROJUMI.".t_producto p
        INNER JOIN ".BD_PROJUMI.".t_categoria c ON p.fk_categoria = c.id_categoria
        INNER JOIN ".BD_PROJUMI.".t_emprendedor e ON e.id_emprededor = p.fk_emprendedor
        INNER JOIN ".BD_SEGURIDAD.".t_usuario u ON u.cedula = e.cedula
        WHERE p.status = 1
        GROUP BY e.id_emprededor, u.nombre, u.apellido, e.emprendimiento, e.imagen, c.id_categoria, c.nombre, c.descripcion;
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw $e;
        }
    }
    
public function registrarEmprendedor() {
    try {
        // Validaciones básicas
        if (empty($this->cedula)) {
            return ['success' => false, 'message' => 'La cédula es obligatoria.'];
        }

        if (!ctype_digit($this->cedula)) {
            return ['success' => false, 'message' => 'La cédula debe ser numérica.'];
        }

        if (empty($this->fk_parroquia)) {
            return ['success' => false, 'message' => 'Debe seleccionar una parroquia.'];
        }

        // Verificar que la parroquia exista
        $sqlCheck = "SELECT id_parroquia FROM t_parroquia WHERE id_parroquia = :id";
        $stmt = $this->db->prepare($sqlCheck);
        $stmt->execute([':id' => $this->fk_parroquia]);
        $parroquia = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$parroquia) {
            return ['success' => false, 'message' => 'La parroquia seleccionada no existe.'];
        }

        // Verificar que no esté ya registrado como emprendedor
        $sqlExists = "SELECT id_emprededor FROM t_emprendedor WHERE cedula = :cedula";
        $stmt = $this->db->prepare($sqlExists);
        $stmt->execute([':cedula' => $this->cedula]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'El emprendedor ya está registrado.'];
        }

        // Insertar emprendedor
        $sql = "INSERT INTO t_emprendedor (
            cedula, lugar_nacimiento, estado_civil, nacionalidad, rif, sexo,
            alergia_medicamento, alergia_alimento, operado, sacramento, grupo_sangre,
            religion, grupo_activo, cantidad_hijos, carga_familiar, casa_propia, alquiler,
            titulo_academico, profesion, oficio, hobby, conocimiento_projumi, motivo_projumi,
            aporte_projumi, imagen, emprendimiento, estatus, fk_parroquia
        ) VALUES (
            :cedula, :lugar_nacimiento, :estado_civil, :nacionalidad, :rif, :sexo,
            :alergia_medicamento, :alergia_alimento, :operado, :sacramento, :grupo_sangre,
            :religion, :grupo_activo, :cantidad_hijos, :carga_familiar, :casa_propia, :alquiler,
            :titulo_academico, :profesion, :oficio, :hobby, :conocimiento_projumi, :motivo_projumi,
            :aporte_projumi, :imagen, :emprendimiento, :estatus, :fk_parroquia
        )";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':cedula' => $this->cedula,
            ':lugar_nacimiento' => $this->lugar_nacimiento,
            ':estado_civil' => $this->estado_civil,
            ':nacionalidad' => $this->nacionalidad,
            ':rif' => $this->rif,
            ':sexo' => $this->sexo,
            ':alergia_medicamento' => $this->alergia_medicamento,
            ':alergia_alimento' => $this->alergia_alimento,
            ':operado' => $this->operado,
            ':sacramento' => $this->sacramento,
            ':grupo_sangre' => $this->grupo_sangre,
            ':religion' => $this->religion,
            ':grupo_activo' => $this->grupo_activo,
            ':cantidad_hijos' => $this->cantidad_hijos,
            ':carga_familiar' => $this->carga_familiar,
            ':casa_propia' => $this->casa_propia,
            ':alquiler' => $this->alquiler,
            ':titulo_academico' => $this->titulo_academico,
            ':profesion' => $this->profesion,
            ':oficio' => $this->oficio,
            ':hobby' => $this->hobby,
            ':conocimiento_projumi' => $this->conocimiento_projumi,
            ':motivo_projumi' => $this->motivo_projumi,
            ':aporte_projumi' => $this->aporte_projumi,
            ':imagen' => $this->imagen,
            ':emprendimiento' => $this->emprendimiento,
            ':estatus' => $this->estatus ?? 0,
            ':fk_parroquia' => $this->fk_parroquia,
        ]);

        $id = $this->db->lastInsertId();

        return ['success' => true, 'id_emprededor' => $id, 'message' => 'Emprendedor registrado correctamente.'];

    } catch (PDOException $e) {
        error_log("Error al registrar emprendedor: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error inesperado: ' . $e->getMessage()];
    }
}


public function listarEmprendedores() {
        try {
            $sql = "
            SELECT 
                e.id_emprededor,
                e.cedula,
                e.emprendimiento,
                e.imagen,
                e.estatus,
                e.aporte_projumi,
                e.rif,
                e.sexo,
                e.titulo_academico,
                e.profesion,
                e.oficio,
                e.hobby,
                e.fk_parroquia,
                -- Añade los que uses en la vista
                u.nombre,
                u.apellido,
                u.correo,
                u.telefono,
                u.fecha_nacimiento
            FROM projumi.t_emprendedor e
            INNER JOIN seguridad.t_usuario u ON e.cedula = u.cedula
            ORDER BY e.id_emprededor ASC
        ";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error al listar emprendedores: " . $e->getMessage());
        return [];
    }
    }
    
    public function aprobarEmprendedor($id_emprendedor) {
        try {
            $this->db->beginTransaction();

            // Obtener la cédula del emprendedor
            $sql = "SELECT cedula FROM t_emprendedor WHERE id_emprededor = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_emprendedor]);
            $cedula = $stmt->fetchColumn();

            if (!$cedula) {
                throw new Exception("No se encontró el emprendedor");
            }

            // Actualizar estatus del emprendedor a 1 (aprobado)
            $sql = "UPDATE t_emprendedor SET estatus = 1 WHERE id_emprededor = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_emprendedor]);

            // Actualizar estatus del usuario a 1 (activo)
            $usuarioModel = new UsuariosModel();
            if (!$usuarioModel->activarUsuarioPorCedula($cedula)) {
                throw new Exception("No se pudo activar el usuario");
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al aprobar emprendedor: " . $e->getMessage());
            return false;
        }
    }

    public function rechazarEmprendedor($id_emprendedor) {
        try {
            $this->db->beginTransaction();

            // Obtener la cédula del emprendedor
            $sql = "SELECT cedula FROM t_emprendedor WHERE id_emprededor = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_emprendedor]);
            $cedula = $stmt->fetchColumn();

            if (!$cedula) {
                throw new Exception("No se encontró el emprendedor");
            }

            // Eliminar el registro del emprendedor
            $sql = "DELETE FROM t_emprendedor WHERE id_emprededor = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_emprendedor]);

            // Desactivar el usuario (pero mantenerlo como cliente)
            $usuarioModel = new UsuariosModel();
            if (!$usuarioModel->desactivarUsuarioPorCedula($cedula)) {
                throw new Exception("No se pudo desactivar el usuario");
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al rechazar emprendedor: " . $e->getMessage());
            return false;
        }
    }

}

