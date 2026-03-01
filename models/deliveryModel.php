<?php
namespace App\Models;
use App\ValidadorTrait;
use PDO;
use PDOException;
use Exception;
use App\Model;

class DeliveryModel extends Model {
    use ValidadorTrait;
    // Esta clase usa la conexión "projumi"
    protected $connectionKey = 'projumi';

    private $id_delivery;
    private $direccion_exacta;
    private $destinatario;
    private $telefono_destinatario;
    private $correo_destinatario;
    private $telefono_delivery;
    private $fk_pedido;
    private $estatus;

    // Setter general con validaciones
    public function setDeliveryData($id_delivery, $direccion_exacta, $destinatario, $telefono_destinatario, $correo_destinatario, $telefono_delivery, $fk_pedido, $estatus): array
    {
        $this->errores = []; // limpiar errores previos

        // Validar ID delivery
        if (!empty($id_delivery) && !is_numeric($id_delivery)) {
            $this->errores['id_delivery'] = "El ID de delivery debe ser numérico.";
        }

        // Validar dirección exacta
        $resultadoDireccion = $this->validarDireccionExacta($direccion_exacta, 'dirección exacta', 10, 255);
        if ($resultadoDireccion !== true) {
            $this->errores['direccion_exacta'] = $resultadoDireccion;
        }

        // Validar destinatario
        $resultadoDestinatario = $this->validarNombreDestinatario($destinatario, 'destinatario', 3, 100);
        if ($resultadoDestinatario !== true) {
            $this->errores['destinatario'] = $resultadoDestinatario;
        }

        // Validar teléfono destinatario
        $resultadoTelefonoDestinatario = $this->validarTelefonodestino($telefono_destinatario, 'teléfono destinatario', true);
        if ($resultadoTelefonoDestinatario !== true) {
            $this->errores['telefono_destinatario'] = $resultadoTelefonoDestinatario;
        }

        // Validar correo destinatario
        $resultadoCorreo = $this->validarCorreodestino($correo_destinatario, 'correo destinatario', true);
        if ($resultadoCorreo !== true) {
            $this->errores['correo_destinatario'] = $resultadoCorreo;
        }

        // Validar teléfono delivery (opcional)
        if (!empty($telefono_delivery)) {
            $resultadoTelefonoDelivery = $this->validarTelefono($telefono_delivery, 'teléfono delivery', false);
            if ($resultadoTelefonoDelivery !== true) {
                $this->errores['telefono_delivery'] = $resultadoTelefonoDelivery;
            }
        }

         if (!empty($fk_pedido) && !is_numeric($fk_pedido)) {
            $this->errores['fk_pedido'] = "El ID de del pedido debe ser numérico.";
        }


        // Validar estatus delivery
        $resultadoEstatus = $this->validarEstatusDelivery($estatus);
        if ($resultadoEstatus !== true) {
            $this->errores['estatus'] = $resultadoEstatus;
        }

        // Si hay errores, no asignar nada y devolverlos
        if (!$this->sinErrores()) {
            return [
                'success' => false,
                'errors' => $this->obtenerErrores()
            ];
        }

        // Si todo es válido, SANITIZAR y asignar los valores
        $this->id_delivery = $id_delivery;
        $this->direccion_exacta = trim($direccion_exacta);
        $this->destinatario = trim($destinatario);
        $this->telefono_destinatario = trim($telefono_destinatario);
        $this->correo_destinatario = trim($correo_destinatario);
        $this->telefono_delivery = $telefono_delivery ? trim($telefono_delivery) : null;
        $this->fk_pedido = trim($fk_pedido);
        $this->estatus = trim($estatus);

        return ['success' => true];
    }

    // En el DeliveryModel
public function setDataAprobarDelivery($id_delivery, $telefono_delivery): array
{
    $this->errores = []; // limpiar errores previos

    // Validar ID delivery
    if (empty($id_delivery) || !is_numeric($id_delivery)) {
        $this->errores['id_delivery'] = "El ID de delivery debe ser numérico y no vacío.";
    }

    // Validar teléfono delivery (requerido para aprobar)
    $resultadoTelefonoDelivery = $this->validarTelefono($telefono_delivery, 'teléfono delivery', true);
    if ($resultadoTelefonoDelivery !== true) {
        $this->errores['telefono_delivery'] = $resultadoTelefonoDelivery;
    }

    // Si hay errores, no asignar nada y devolverlos
    if (!$this->sinErrores()) {
        return [
            'success' => false,
            'errors' => $this->obtenerErrores()
        ];
    }

    // Si todo es válido, asignar solo los valores necesarios
    $this->id_delivery = trim($id_delivery);
    $this->telefono_delivery = trim($telefono_delivery);
    $this->estatus = 'En proceso';

    return ['success' => true];
}

    // Insertar nuevo delivery
    public function registrarDelivery() {
        try {
            $this->query(
                "INSERT INTO t_delivery (direccion_exacta, destinatario, telefono_destinatario, correo_destinatario, telefono_delivery, fk_pedido, estatus)
                 VALUES (:direccion_exacta, :destinatario, :telefono_destinatario, :correo_destinatario, :telefono_delivery, :fk_pedido, :estatus)",
                [
                    ':direccion_exacta' => $this->direccion_exacta,
                    ':destinatario' => $this->destinatario,
                    ':telefono_destinatario' => $this->telefono_destinatario,
                    ':correo_destinatario' => $this->correo_destinatario,
                    ':telefono_delivery' => $this->telefono_delivery,
                    ':fk_pedido' => $this->fk_pedido,
                    ':estatus' => $this->estatus
                ]
            );
            return $this->lastInsertId();
        } catch (Exception $e) {
            throw new Exception("Error al registrar delivery: " . $e->getMessage());
        }
    }

    // Actualizar delivery
    public function actualizarDelivery() {
        try {
            $this->query(
                "UPDATE t_delivery 
                 SET direccion_exacta = :direccion_exacta, destinatario = :destinatario,
                     telefono_destinatario = :telefono_destinatario, correo_destinatario = :correo_destinatario,
                     telefono_delivery = :telefono_delivery, estatus = :estatus
                 WHERE id_delivery = :id_delivery",
                [
                    ':direccion_exacta' => $this->direccion_exacta,
                    ':destinatario' => $this->destinatario,
                    ':telefono_destinatario' => $this->telefono_destinatario,
                    ':correo_destinatario' => $this->correo_destinatario,
                    ':telefono_delivery' => $this->telefono_delivery,
                    ':estatus' => $this->estatus,
                    ':id_delivery' => $this->id_delivery
                ]
            );
            return true;
        } catch (Exception $e) {
            throw new Exception("Error al actualizar delivery: " . $e->getMessage());
        }
    }

    // Aprobar delivery
    public function aprobarDelivery() {
        try {
            // Iniciar la transacción
            $this->beginTransaction();
    
            // 1. Actualizar t_delivery con estatus y teléfono del delivery
            $this->query(
                "UPDATE t_delivery 
                 SET telefono_delivery = :telefono_delivery,
                     estatus = 'En proceso'
                 WHERE id_delivery = :id_delivery",
                [
                    ':telefono_delivery' => $this->telefono_delivery,
                    ':id_delivery' => $this->id_delivery
                ]
            );
    
            // 2. Obtener el fk_pedido asociado a este delivery
            $result = $this->query(
                "SELECT fk_pedido FROM t_delivery WHERE id_delivery = :id_delivery",
                [':id_delivery' => $this->id_delivery]
            )->fetch(PDO::FETCH_ASSOC);
    
            if (!$result || !isset($result['fk_pedido'])) {
                $this->rollBack();
                throw new Exception("No se encontró el pedido relacionado con este delivery.");
            }
    
            $id_pedido = $result['fk_pedido'];
    
            // 3. Actualizar el estatus del pedido a 'En Proceso'
            $this->query(
                "UPDATE t_pedidos SET estatus = 'En Proceso' WHERE id_pedidos = :id_pedido",
                [':id_pedido' => $id_pedido]
            );
    
            // Confirmar transacción
            $this->commit();
            return true;
    
        } catch (Exception $e) {
            $this->rollBack();
            throw new Exception("Error al aprobar delivery: " . $e->getMessage());
        }
    }    

    // Obtener un delivery por ID
    public function obtenerDeliveryPorId($id_delivery) {
        try {
            $stmt = $this->query(
                "SELECT * FROM t_delivery WHERE id_delivery = :id_delivery",
                [':id_delivery' => $id_delivery]
            );
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al obtener delivery: " . $e->getMessage());
        }
    }

    // Eliminar delivery
    public function eliminarDelivery($id_delivery) {
        try {
            $this->query(
                "DELETE FROM t_delivery WHERE id_delivery = :id_delivery",
                [':id_delivery' => $id_delivery]
            );
            return true;
        } catch (Exception $e) {
            throw new Exception("Error al eliminar delivery: " . $e->getMessage());
        }
    }

    // Obtener todos los deliveries
    public function obtenerTodosLosDeliveries() {
        try {
            $stmt = $this->query("SELECT 
                d.estatus, 
                d.id_delivery, p.fecha_pedido,
                p.id_pedidos AS id_pedido, 
                
                u_c.nombre AS nombre_cliente, 
                u_c.apellido AS apellido_cliente, 
                
                u_e.nombre AS nombre_emprendedor, 
                u_e.apellido AS apellido_emprendedor, 
                
                d.direccion_exacta, 
                d.destinatario, 
                d.telefono_destinatario, 
                d.correo_destinatario, 
                d.telefono_delivery
            
            FROM t_delivery d
            JOIN t_pedidos p ON d.fk_pedido = p.id_pedidos
            
            JOIN t_cliente c ON p.fk_cliente = c.id_cliente
            JOIN ".BD_SEGURIDAD.".t_usuario u_c ON u_c.cedula = c.cedula
            
            JOIN t_detalle_pedido dp ON dp.pedidos_ID_PEDIDO = p.id_pedidos
            JOIN t_producto pr ON pr.id_producto = dp.producto_ID_PRODUCTO
            
            JOIN t_emprendedor e ON pr.fk_emprendedor = e.id_emprededor
            JOIN ".BD_SEGURIDAD.".t_usuario u_e ON u_e.cedula = e.cedula
            
            GROUP BY 
                d.id_delivery, p.fecha_pedido, p.id_pedidos, 
                u_c.nombre, u_c.apellido, 
                u_e.nombre, u_e.apellido, 
                d.direccion_exacta, d.destinatario, 
                d.telefono_destinatario, d.correo_destinatario, 
                d.telefono_delivery, d.estatus;
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al listar deliveries: " . $e->getMessage());
        }
    }

    // Consultar deliveries por cliente
    public function obtenerDeliveriesPorCliente($id_cliente) {
        try {
            $stmt = $this->query(
                "SELECT 
                    d.estatus, p.fecha_pedido,
                    d.id_delivery, 
                    p.id_pedidos AS id_pedido, 
                    
                    u_c.nombre AS nombre_cliente, 
                    u_c.apellido AS apellido_cliente, 
                    
                    u_e.nombre AS nombre_emprendedor, 
                    u_e.apellido AS apellido_emprendedor, 
                    
                    d.direccion_exacta, 
                    d.destinatario, 
                    d.telefono_destinatario, 
                    d.correo_destinatario, 
                    d.telefono_delivery
                
                FROM t_delivery d
                JOIN t_pedidos p ON d.fk_pedido = p.id_pedidos
                JOIN t_cliente c ON p.fk_cliente = c.id_cliente
                JOIN ".BD_SEGURIDAD.".t_usuario u_c ON u_c.cedula = c.cedula
                
                JOIN t_detalle_pedido dp ON dp.pedidos_ID_PEDIDO = p.id_pedidos
                JOIN t_producto pr ON pr.id_producto = dp.producto_ID_PRODUCTO
                JOIN t_emprendedor e ON pr.fk_emprendedor = e.id_emprededor
                JOIN ".BD_SEGURIDAD.".t_usuario u_e ON u_e.cedula = e.cedula
                
                WHERE c.cedula = :id_cliente
                
                GROUP BY 
                    d.id_delivery, p.fecha_pedido, p.id_pedidos, 
                    u_c.nombre, u_c.apellido, 
                    u_e.nombre, u_e.apellido, 
                    d.direccion_exacta, d.destinatario, 
                    d.telefono_destinatario, d.correo_destinatario, 
                    d.telefono_delivery, d.estatus;",
                [':id_cliente' => $id_cliente]
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al obtener deliveries por cliente: " . $e->getMessage());
        }
    }

    // Consultar deliveries por emprendedor
    public function obtenerDeliveriesPorEmprendedor($id_emprendedor) {
        try {
            $stmt = $this->query(
                "SELECT 
                    d.estatus, p.fecha_pedido,
                    d.id_delivery, 
                    p.id_pedidos AS id_pedido, 
                    
                    u_c.nombre AS nombre_cliente, 
                    u_c.apellido AS apellido_cliente, 
                    
                    u_e.nombre AS nombre_emprendedor, 
                    u_e.apellido AS apellido_emprendedor, 
                    
                    d.direccion_exacta, 
                    d.destinatario, 
                    d.telefono_destinatario, 
                    d.correo_destinatario, 
                    d.telefono_delivery
                
                FROM t_delivery d
                JOIN t_pedidos p ON d.fk_pedido = p.id_pedidos
                JOIN t_cliente c ON p.fk_cliente = c.id_cliente
                JOIN ".BD_SEGURIDAD.".t_usuario u_c ON u_c.cedula = c.cedula
                
                JOIN t_detalle_pedido dp ON dp.pedidos_ID_PEDIDO = p.id_pedidos
                JOIN t_producto pr ON pr.id_producto = dp.producto_ID_PRODUCTO
                JOIN t_emprendedor e ON pr.fk_emprendedor = e.id_emprededor
                JOIN ".BD_SEGURIDAD.".t_usuario u_e ON u_e.cedula = e.cedula
                
                WHERE e.cedula = :id_emprendedor
                
                GROUP BY 
                    d.id_delivery, p.fecha_pedido, p.id_pedidos, 
                    u_c.nombre, u_c.apellido, 
                    u_e.nombre, u_e.apellido, 
                    d.direccion_exacta, d.destinatario, 
                    d.telefono_destinatario, d.correo_destinatario, 
                    d.telefono_delivery, d.estatus;",
                [':id_emprendedor' => $id_emprendedor]
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al obtener deliveries por emprendedor: " . $e->getMessage());
        }
    }
}