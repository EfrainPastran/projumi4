<?php
namespace App\Models;
use App\ValidadorTrait;
use PDO;
use PDOException;
use Exception;
use App\Model;

class EnviosModel extends Model {
    use ValidadorTrait;
    // Esta clase usa la conexión "projumi"
    protected $connectionKey = 'projumi';

    private $id_envio;
    private $direccion_envio;
    private $estatus;
    private $numero_seguimiento;
    private $fk_empresa_envio;
    private $fk_pedido;

    // Setter general con validaciones completas
    public function setData($id_envio, $direccion_envio, $estatus, $numero_seguimiento, $fk_empresa_envio, $fk_pedido): array
    {
        $this->errores = []; // limpiar errores previos

        // Validar ID envío
        if (!empty($id_envio) && !is_numeric($id_envio)) {
            $this->errores['id_envio'] = "El ID de envío debe ser numérico.";
        }

        // Validar dirección de envío
        $resultadoDireccion = $this->validarDireccionEnvio($direccion_envio, 'dirección de envío', 5, 255);
        if ($resultadoDireccion !== true) {
            $this->errores['direccion_envio'] = $resultadoDireccion;
        }

        // Validar estatus
        $resultadoEstatus = $this->validarEstatusEnvio($estatus);
        if ($resultadoEstatus !== true) {
            $this->errores['estatus'] = $resultadoEstatus;
        }

        // Validar número de seguimiento
        $resultadoSeguimiento = $this->validarNumeroSeguimiento($numero_seguimiento, 'número de seguimiento', false);
        if ($resultadoSeguimiento !== true) {
            $this->errores['numero_seguimiento'] = $resultadoSeguimiento;
        }


        // if (!empty($fk_empresa_envio) && !is_numeric($fk_empresa_envio)) {
        //     $this->errores['fk_empresa_envio'] = "El ID de la empresa debe ser numérico.";
        // }

         if (!empty($fk_pedido) && !is_numeric($fk_pedido)) {
            $this->errores['fk_pedido'] = "El ID del pedido debe ser numérico.";
        }


        // Si hay errores, no asignar nada y devolverlos
        if (!$this->sinErrores()) {
            return [
                'success' => false,
                'errors' => $this->obtenerErrores()
            ];
        }

        // Si todo es válido, SANITIZAR y asignar los valores
        $this->id_envio = $id_envio;
        $this->direccion_envio = trim($direccion_envio);
        $this->estatus = trim($estatus);
        $this->numero_seguimiento = $numero_seguimiento ? trim($numero_seguimiento) : null;
        $this->fk_empresa_envio = trim($fk_empresa_envio);
        $this->fk_pedido = trim($fk_pedido);

        return ['success' => true];
    }

    // Crear nuevo envío
    public function registrarEnvio() {
        try {
            $this->query(
                "INSERT INTO t_envio (direccion_envio, estatus, numero_seguimiento, fk_empresa_envio, fk_pedido)
                VALUES (:direccion_envio, :estatus, :numero_seguimiento, :fk_empresa_envio, :fk_pedido)",
                [
                    ':direccion_envio' => $this->direccion_envio,
                    ':estatus' => $this->estatus,
                    ':numero_seguimiento' => $this->numero_seguimiento,
                    ':fk_empresa_envio' => $this->fk_empresa_envio,
                    ':fk_pedido' => $this->fk_pedido
                ]
            );

            return [
                'success' => true,
                'message' => 'Envio registrado correctamente.',
                'id_envio' => $this->lastInsertId()
            ];
        } catch (Exception $e) {
            error_log("Error en registrarEnvio: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al registrar el envio.'];
        }
    }

    // Actualizar envío
    public function actualizarEnvio() {
        try {
            $this->query(
                "UPDATE t_envio 
                 SET direccion_envio = :direccion_envio, estatus = :estatus, 
                     numero_seguimiento = :numero_seguimiento, fk_empresa_envio = :fk_empresa_envio,
                     fk_pedido = :fk_pedido
                 WHERE id_envio = :id_envio",
                [
                    ':direccion_envio' => $this->direccion_envio,
                    ':estatus' => $this->estatus,
                    ':numero_seguimiento' => $this->numero_seguimiento,
                    ':fk_empresa_envio' => $this->fk_empresa_envio,
                    ':fk_pedido' => $this->fk_pedido,
                    ':id_envio' => $this->id_envio
                ]
            );

            return ['success' => true, 'message' => 'Envio actualizado correctamente.'];

        } catch (Exception $e) {
            error_log("Error en actualizarEnvio: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al actualizar el envio.'];
        }
    }

    public function actualizarNroSeguimiento() {
        try {
            // Iniciar transacción
            $this->beginTransaction();
    
            // 1. Actualizar t_envio con número de seguimiento y estatus
            $this->query(
                "UPDATE t_envio 
                 SET estatus = :estatus_envio, 
                     numero_seguimiento = :numero_seguimiento 
                 WHERE id_envio = :id_envio",
                [
                    ':estatus_envio' => $this->estatus,
                    ':numero_seguimiento' => $this->numero_seguimiento,
                    ':id_envio' => $this->id_envio
                ]
            );
    
            // 2. Obtener el ID del pedido asociado al envío
            $stmt = $this->query(
                "SELECT fk_pedido FROM t_envio WHERE id_envio = :id_envio",
                [':id_envio' => $this->id_envio]
            );
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result || !isset($result['fk_pedido'])) {
                $this->rollBack();
                throw new Exception("No se encontró un pedido asociado al envío.");
            }
            
            $id_pedido = $result['fk_pedido'];
            if ($this->estatus === 'En proceso') {
                $estatus = 'En Tránsito';
            } else if ($this->estatus === 'Entregado') {
                $estatus = 'Completado';
            } else {
                $estatus = 'Anulado';
            }

            // 3. Actualizar el estatus del pedido
            $this->query(
                "UPDATE t_pedidos SET estatus = :estatus WHERE id_pedidos = :id_pedido",
                [
                    ':estatus' => $estatus, 
                    ':id_pedido' => $id_pedido
                ]
            );

            // Confirmar transacción
            $this->commit();
            return ['success' => true, 'message' => 'Envio actualizado correctamente.'];
    
        } catch (Exception $e) {
            $this->rollBack();
            error_log("Error en actualizarNroSeguimiento: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error en la transacción: ' . $e->getMessage()];
        }
    }

    // Obtener envío por ID
    public function obtenerEnvioPorId($id_envio) {
        try {
            $stmt = $this->query(
                "SELECT * FROM t_envio WHERE id_envio = :id_envio",
                [':id_envio' => $id_envio]
            );
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerEnvioPorId: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerEnvios($id) {
        try {
            $stmt = $this->query(
                "SELECT 
                    c.id_cliente, 
                    e.fk_pedido, 
                    e.direccion_envio, 
                    p.estatus, 
                    u.id_usuario,
                    u.nombre AS cliente_nombre, 
                    u.apellido AS cliente_apellido, 
                    u.correo
                FROM t_envio e
                INNER JOIN t_pedidos p ON e.fk_pedido = p.id_pedidos
                INNER JOIN t_cliente c ON p.fk_cliente = c.id_cliente
                INNER JOIN ".BD_SEGURIDAD.".t_usuario u ON u.cedula = c.cedula
                WHERE e.id_envio = ?",
                [$id]
            );
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerEnvios: " . $e->getMessage());
            return false;
        }
    }

    public function eliminarEnvio($id_envio) {
        try {
            $this->query(
                "DELETE FROM t_envio WHERE id_envio = :id_envio", 
                [':id_envio' => $id_envio]
            );
            return ['success' => true, 'message' => 'Envio eliminado correctamente.'];
        } catch (Exception $e) {
            error_log("Error en eliminarEnvio: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al eliminar el envio.'];
        }
    }

    // Obtener todos los envíos
    public function obtenerTodosLosEnvios() {
        try {
            $stmt = $this->query("
                SELECT 
                    e.id_envio, 
                    p.fecha_pedido,
                    p.id_pedidos AS id_pedido, 
                    
                    u_c.nombre AS nombre_cliente, 
                    u_c.apellido AS apellido_cliente, 
                    
                    u_e.nombre AS nombre_emprendedor, 
                    u_e.apellido AS apellido_emprendedor, 
                    
                    emp.nombre AS nombre_empresa_envio, 
                    emp.telefono AS telefono_empresa_envio, 
                    emp.direccion AS direccion_empresa_envio, 
                    
                    e.direccion_envio, 
                    e.numero_seguimiento, 
                    e.estatus AS estatus_envio
                
                FROM t_envio e
                JOIN t_pedidos p ON e.fk_pedido = p.id_pedidos
                JOIN t_cliente c ON p.fk_cliente = c.id_cliente
                JOIN ".BD_SEGURIDAD.".t_usuario u_c ON u_c.cedula = c.cedula
                
                JOIN t_empresa_envio emp ON e.fk_empresa_envio = emp.id_empresa_envio
                
                JOIN t_detalle_pedido dp ON dp.pedidos_ID_PEDIDO = p.id_pedidos
                JOIN t_producto pr ON pr.id_producto = dp.producto_ID_PRODUCTO
                JOIN t_emprendedor em ON pr.fk_emprendedor = em.id_emprededor
                JOIN ".BD_SEGURIDAD.".t_usuario u_e ON u_e.cedula = em.cedula
                
                GROUP BY 
                    e.id_envio, p.id_pedidos, 
                    u_c.nombre, u_c.apellido, 
                    u_e.nombre, u_e.apellido, 
                    emp.nombre, emp.telefono, emp.direccion, 
                    e.direccion_envio, e.numero_seguimiento, e.estatus;
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerTodosLosEnvios: " . $e->getMessage());
            return [];
        }
    }

    // Envíos realizados por un emprendedor
    public function obtenerEnviosPorEmprendedor($cedula_emprendedor, $dateFrom = null, $dateTo = null) {
        try {
            $sql = "SELECT 
                    e.id_envio, p.fecha_pedido,
                    p.id_pedidos AS id_pedido, 
                    
                    u_c.nombre AS nombre_cliente, 
                    u_c.apellido AS apellido_cliente, 
                    u_c.telefono AS telefono_cliente,
                    u_e.nombre AS nombre_emprendedor, 
                    u_e.apellido AS apellido_emprendedor, 
                    
                    emp.nombre AS nombre_empresa_envio, 
                    emp.telefono AS telefono_empresa_envio, 
                    emp.direccion AS direccion_empresa_envio, 
                    
                    e.direccion_envio, 
                    e.numero_seguimiento, 
                    e.estatus AS estatus_envio
                
                FROM t_envio e
                JOIN t_pedidos p ON e.fk_pedido = p.id_pedidos
                
                JOIN t_cliente c ON p.fk_cliente = c.id_cliente
                JOIN ".BD_SEGURIDAD.".t_usuario u_c ON u_c.cedula = c.cedula
                
                JOIN t_empresa_envio emp ON e.fk_empresa_envio = emp.id_empresa_envio
                
                JOIN t_detalle_pedido dp ON dp.pedidos_ID_PEDIDO = p.id_pedidos
                JOIN t_producto pr ON pr.id_producto = dp.producto_ID_PRODUCTO
                
                JOIN t_emprendedor em ON pr.fk_emprendedor = em.id_emprededor
                JOIN ".BD_SEGURIDAD.".t_usuario u_e ON u_e.cedula = em.cedula
                
                WHERE em.cedula = :cedula";

            // Agregar filtros de fecha opcionales
            $params = [':cedula' => $cedula_emprendedor];
            
            if ($dateFrom && $dateTo) {
                $sql .= " AND DATE(p.fecha_pedido) BETWEEN :dateFrom AND :dateTo";
                $params[':dateFrom'] = $dateFrom;
                $params[':dateTo'] = $dateTo;
            } elseif ($dateFrom) {
                $sql .= " AND DATE(p.fecha_pedido) >= :dateFrom";
                $params[':dateFrom'] = $dateFrom;
            } elseif ($dateTo) {
                $sql .= " AND DATE(p.fecha_pedido) <= :dateTo";
                $params[':dateTo'] = $dateTo;
            }

            $sql .= " GROUP BY 
                    e.id_envio, p.fecha_pedido, p.id_pedidos, 
                    u_c.nombre, u_c.apellido, 
                    u_e.nombre, u_e.apellido, 
                    emp.nombre, emp.telefono, emp.direccion, 
                    e.direccion_envio, e.numero_seguimiento, e.estatus";

            $stmt = $this->query($sql, $params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerEnviosPorEmprendedor: " . $e->getMessage());
            return [];
        }
    }

    public function contarEnviosPorEmprendedor($cedula_emprendedor) {
        try {
            $stmt = $this->query(
                "SELECT COUNT(DISTINCT e.id_envio) AS total_envios
                 FROM t_envio e
                 JOIN t_pedidos p ON e.fk_pedido = p.id_pedidos
                 JOIN t_cliente c ON p.fk_cliente = c.id_cliente
                 JOIN ".BD_SEGURIDAD.".t_usuario u_c ON u_c.cedula = c.cedula
                 JOIN t_empresa_envio emp ON e.fk_empresa_envio = emp.id_empresa_envio
                 JOIN t_detalle_pedido dp ON dp.pedidos_ID_PEDIDO = p.id_pedidos
                 JOIN t_producto pr ON pr.id_producto = dp.producto_ID_PRODUCTO
                 JOIN t_emprendedor em ON pr.fk_emprendedor = em.id_emprededor
                 JOIN ".BD_SEGURIDAD.".t_usuario u_e ON u_e.cedula = em.cedula
                 WHERE em.cedula = :cedula",
                [':cedula' => $cedula_emprendedor]
            );
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total_envios'] ?? 0;
        } catch (Exception $e) {
            error_log("Error en contarEnviosPorEmprendedor: " . $e->getMessage());
            return 0;
        }
    }

    public function contarEnviosTotales() {
        try {
            $stmt = $this->query(
                "SELECT COUNT(DISTINCT e.id_envio) AS total_envios
                 FROM t_envio e
                 JOIN t_pedidos p ON e.fk_pedido = p.id_pedidos
                 JOIN t_cliente c ON p.fk_cliente = c.id_cliente
                 JOIN ".BD_SEGURIDAD.".t_usuario u_c ON u_c.cedula = c.cedula
                 JOIN t_empresa_envio emp ON e.fk_empresa_envio = emp.id_empresa_envio
                 JOIN t_detalle_pedido dp ON dp.pedidos_ID_PEDIDO = p.id_pedidos
                 JOIN t_producto pr ON pr.id_producto = dp.producto_ID_PRODUCTO
                 JOIN t_emprendedor em ON pr.fk_emprendedor = em.id_emprededor
                 JOIN ".BD_SEGURIDAD.".t_usuario u_e ON u_e.cedula = em.cedula"
            );
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total_envios'] ?? 0;
        } catch (Exception $e) {
            error_log("Error en contarEnviosTotales: " . $e->getMessage());
            return 0;
        }
    }

    public function obtenerEnviosPorCliente($cedula_cliente) {
        try {
            $stmt = $this->query(
                "SELECT 
                    e.id_envio, p.fecha_pedido,
                    p.id_pedidos AS id_pedido, 
                    
                    u_c.nombre AS nombre_cliente, 
                    u_c.apellido AS apellido_cliente, 
                    u_c.telefono AS telefono_cliente,
                    
                    u_e.nombre AS nombre_emprendedor, 
                    u_e.apellido AS apellido_emprendedor, 
                    
                    emp.nombre AS nombre_empresa_envio, 
                    emp.telefono AS telefono_empresa_envio, 
                    emp.direccion AS direccion_empresa_envio, 
                    
                    e.direccion_envio, 
                    e.numero_seguimiento, 
                    e.estatus AS estatus_envio
                
                FROM t_envio e
                JOIN t_pedidos p ON e.fk_pedido = p.id_pedidos
                
                JOIN t_cliente c ON p.fk_cliente = c.id_cliente
                JOIN ".BD_SEGURIDAD.".t_usuario u_c ON u_c.cedula = c.cedula
                
                JOIN t_empresa_envio emp ON e.fk_empresa_envio = emp.id_empresa_envio
                
                JOIN t_detalle_pedido dp ON dp.pedidos_ID_PEDIDO = p.id_pedidos
                JOIN t_producto pr ON pr.id_producto = dp.producto_ID_PRODUCTO
                
                JOIN t_emprendedor em ON pr.fk_emprendedor = em.id_emprededor
                JOIN ".BD_SEGURIDAD.".t_usuario u_e ON u_e.cedula = em.cedula
                
                WHERE c.cedula = :cedula
                
                GROUP BY 
                    e.id_envio, p.fecha_pedido, p.id_pedidos, 
                    u_c.nombre, u_c.apellido, 
                    u_e.nombre, u_e.apellido, 
                    emp.nombre, emp.telefono, emp.direccion, 
                    e.direccion_envio, e.numero_seguimiento, e.estatus;",
                [':cedula' => $cedula_cliente]
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerEnviosPorCliente: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerCantidadEnviosPorEmprendedor($cedula_emprendedor) {
        try {
            $stmt = $this->query(
                "SELECT COUNT(DISTINCT env.id_envio) AS cantidad_envios
                 FROM t_emprendedor e
                 JOIN t_producto p ON p.fk_emprendedor = e.id_emprededor
                 JOIN t_detalle_pedido dp ON dp.producto_ID_PRODUCTO = p.id_producto
                 JOIN t_pedidos pe ON pe.id_pedidos = dp.pedidos_ID_PEDIDO
                 JOIN t_envio env ON env.fk_pedido = pe.id_pedidos
                 WHERE e.cedula = :cedula",
                [':cedula' => $cedula_emprendedor]
            );
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            error_log("Error en obtenerCantidadEnviosPorEmprendedor: " . $e->getMessage());
            return 0;
        }
    }
}