<?php
namespace App\Models;
use PDO;
use PDOException;
use Exception;
use App\Model;
use App\Models\UsuariosModel;
class ClienteModel extends Model {

    private $id_cliente;
    private $cedula;
    private $fecha_registro;
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

    function setData($id_cliente, $cedula,$fecha_registro, $estatus) {
        $this->id_cliente = $id_cliente;
        $this->cedula = $cedula;
        $this->fecha_registro = $fecha_registro;
        $this->estatus = $estatus;
    }

    // Métodos CRUD
    public function getAll() {
        try {
            $stmt = $this->query("SELECT * FROM t_cliente c INNER JOIN ".BD_SEGURIDAD.".t_usuario u ON c.cedula=u.cedula;");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            // Puedes manejar el error específico aquí o dejar que se propague
            throw $e;
        }
    }

    public function clienteExiste($id_cliente) {
        $sql = 'SELECT COUNT(*) FROM t_cliente WHERE id_cliente = :id_cliente';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_cliente' => $id_cliente]);
        return $stmt->fetchColumn() > 0;
    }    

    public function getById($id_cliente) {
        try {
            $sql = 'SELECT * FROM t_cliente WHERE id_cliente = :id_cliente LIMIT 1';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id_cliente' => $id_cliente]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error en getById (PDO): " . $e->getMessage());
            return false;
        }
    }

    public function getByCedula($cedula) {
        try {
            $sql = 'SELECT id_cliente FROM t_cliente WHERE cedula = :cedula LIMIT 1';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':cedula' => $cedula]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error en getByCedula (PDO): " . $e->getMessage());
            return false;
        }
    }

    public function getData($cedula) {
        try {
            $sql = 'SELECT u.cedula, u.nombre, u.apellido, u.correo, u.telefono, u.direccion, u.fecha_nacimiento FROM '.BD_PROJUMI.'.t_cliente c INNER JOIN '.BD_SEGURIDAD.'.t_usuario u ON c.cedula=u.cedula WHERE u.cedula = :cedula LIMIT 1';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':cedula' => $cedula]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error en getByCedula (PDO): " . $e->getMessage());
            return false;
        }
    }

    //FUNCION AGREGADA EN EL HOST
    /*public function existeCliente($id_cedula) {
        $sql = "SELECT COUNT(*) FROM t_cliente WHERE cedula = ? AND estatus = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_cedula]);
        return $stmt->fetchColumn() > 0;
    }*/

    public function registerCliente($clienteData)
    {
        try {
            $Usuario = new UsuariosModel();

            // Validar que se obtenga un cédula válido
            if (!ctype_digit($clienteData['cedula'])) {
                return [
                    'success' => false,
                    'message' => 'La cédula debe ser numérica.'
                ];
            }
            // 1. Verificar si el usuario existe por cédula
            $usuarioExistente = $Usuario->getUsuarioByCedula($clienteData['cedula']);

            
            if (!$usuarioExistente) {
                // Crear usuario básico con rol de cliente (3)
                $Usuario->setData(
                    $clienteData['cedula'],
                    $clienteData['nombre'],
                    $clienteData['apellido'],
                    $clienteData['correo'],
                    12345678,
                    $clienteData['direccion'] ?? '',
                    $clienteData['telefono'] ?? '',
                    date('Y-m-d H:i:s'),
                    $clienteData['fecha_nacimiento'] ?? null,
                    1, // activo
                    3  // rol cliente
                );

                // Aquí se valida todo automáticamente dentro del modelo de usuario
                $resultadoUsuario = $Usuario->registerUsuario();

                if (!$resultadoUsuario['success']) {
                    // Si falló alguna validación (correo, cédula, etc.)
                    return $resultadoUsuario;
                }
            }

            // 2. Verificar si ya está registrado como cliente
            $sqlCheck = "SELECT id_cliente FROM t_cliente WHERE cedula = :cedula";
            $stmt = $this->db->prepare($sqlCheck);
            $stmt->execute([':cedula' => $clienteData['cedula']]);
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($cliente) {
                return [
                    'success' => true,
                    'id_cliente' => $cliente['id_cliente'],
                    'message' => 'Cliente ya registrado previamente.'
                ];
            }

            
            //  3. Registrar el cliente si no existe
            $sqlInsert = "INSERT INTO t_cliente (cedula, fecha_registro, estatus)
                          VALUES (:cedula, :fecha_registro, :estatus)";
            $stmt = $this->db->prepare($sqlInsert);
            $stmt->execute([
                ':cedula' => $clienteData['cedula'],
                ':fecha_registro' => date('Y-m-d'),
                ':estatus' => 1
            ]);

            $idCliente = $this->db->lastInsertId();

            return [
                'success' => true,
                'id_cliente' => $idCliente,
                'message' => 'Cliente registrado exitosamente.'
            ];

        } catch (PDOException $e) {
            error_log("Error en registrarClienteConUsuario (PDO): " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al registrar el cliente: ' . $e->getMessage()
            ];
        }
    }

    public function update() {
        try {
            $sql = 'UPDATE t_cliente 
                    SET cedula = :cedula,
                        fecha_registro = :fecha_registro, 
                        estatus = :estatus 
                    WHERE id_cliente = :id_cliente';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id_cliente' => $this->id_cliente,
                ':cedula' => $this->cedula,
                ':fecha_registro' => $this->fecha_registro,
                ':estatus' => $this->estatus
            ]);
        } catch(PDOException $e) {
            error_log("Error en update (PDO): " . $e->getMessage());
            return false;
        }
    }

    public function delete() {
        try {
            $sql = 'DELETE FROM t_cliente WHERE id_cliente = :id_cliente';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id_cliente' => $this->id_cliente]);
        } catch(PDOException $e) {
            error_log("Error en delete (PDO): " . $e->getMessage());
            return false;
        }
    }
}