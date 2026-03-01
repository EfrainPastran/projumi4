<?php
namespace App\Models;
use App\ValidadorTrait;
use PDO;
use PDOException;
use Exception;
use App\Model;

class PagosModel extends Model {
    use ValidadorTrait;
    // Esta clase usa la conexión "projumi"
    protected $connectionKey = 'projumi';

    private $fk_pedido;    
    private $estatus;
    private $fecha_pago;

    // Setter general con validaciones
    public function setData($fk_pedido, $estatus, $fecha_pago = null): array
    {
        $this->errores = []; // limpiar errores previos

        // Validar pedido
        $resultadoPedido = $this->validarNumerico($fk_pedido, 'pedido', true);
        if ($resultadoPedido !== true) {
            $this->errores['fk_pedido'] = $resultadoPedido;
        }

        // Validar estatus de pago
        $resultadoEstatus = $this->validarEstatusPago($estatus);
        if ($resultadoEstatus !== true) {
            $this->errores['estatus'] = $resultadoEstatus;
        }

        // Validar fecha de pago (opcional)
        if (!empty($fecha_pago)) {
            $resultadoFecha = $this->validarFechaPago($fecha_pago, 'fecha de pago', false);
            if ($resultadoFecha !== true) {
                $this->errores['fecha_pago'] = $resultadoFecha;
            }
        }

        // Si hay errores, no asignar nada y devolverlos
        if (!$this->sinErrores()) {
            return [
                'success' => false,
                'errors' => $this->obtenerErrores()
            ];
        }

        // Si todo es válido, SANITIZAR y asignar los valores
        $this->fk_pedido = trim($fk_pedido);
        $this->estatus = trim($estatus);
        $this->fecha_pago = $fecha_pago ? trim($fecha_pago) : null;

        return ['success' => true];
    }

    // Setters individuales (mantener compatibilidad)
    function set_estatus($estatus) { 
        $this->estatus = trim($estatus); 
    }
    
    function set_fk_pedido($fk_pedido) { 
        $this->fk_pedido = trim($fk_pedido); 
    }
    
    function set_fecha_pago($fecha_pago) { 
        $this->fecha_pago = trim($fecha_pago); 
    }

    public function mostrarPagos() {
        try {
            $stmt = $this->query('
            SELECT 
                pg.id_pagos,
                p.id_pedidos AS pedido_id,
                pg.fecha_pago,
                ue.nombre AS emprendedor_nombre,
                ue.apellido AS emprendedor_apellido,
                uc.nombre AS cliente_nombre,
                uc.apellido AS cliente_apellido,
                SUM(dp.cantidad * dp.precio_unitario) AS total_pedido,
                pg.estatus AS estado_pago
            FROM t_pagos pg
            INNER JOIN t_pedidos p ON pg.fk_pedido = p.id_pedidos
            INNER JOIN t_cliente c ON p.fk_cliente = c.id_cliente
            INNER JOIN '.BD_SEGURIDAD.'.t_usuario uc ON uc.cedula = c.cedula
            INNER JOIN t_detalle_pedido dp ON dp.pedidos_ID_PEDIDO = p.id_pedidos
            INNER JOIN t_producto pr ON pr.id_producto = dp.producto_ID_PRODUCTO
            INNER JOIN t_emprendedor e ON e.id_emprededor = pr.fk_emprendedor
            INNER JOIN '.BD_SEGURIDAD.'.t_usuario ue ON ue.cedula = e.cedula
            GROUP BY 
                p.id_pedidos,
                pg.fecha_pago,
                ue.nombre,
                ue.apellido,
                uc.nombre,
                uc.apellido,
                pg.estatus
            ORDER BY pg.fecha_pago DESC;
            ');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en mostrarPagos: " . $e->getMessage());
            return [];
        }
    }

    public function mostrarPagosPorEmprendedor($id_emprendedor) {
        try {
            // Validar ID emprendedor
            $resultado = $this->validarNumerico($id_emprendedor, 'emprendedor', true);
            if ($resultado !== true) {
                throw new Exception("ID de emprendedor inválido: " . $resultado);
            }

            $stmt = $this->query(
                "SELECT 
                    pg.id_pagos, 
                    p.id_pedidos AS pedido_id, 
                    pg.fecha_pago, 
                    CONCAT(u_e.nombre, ' ', u_e.apellido) AS emprendedor_nombre, 
                    CONCAT(u_c.nombre, ' ', u_c.apellido) AS cliente_nombre, 
                    SUM(dp.cantidad * dp.precio_unitario) AS total_pedido, 
                    pg.estatus AS estado_pago
                FROM t_pagos pg
                INNER JOIN t_pedidos p ON pg.fk_pedido = p.id_pedidos
                INNER JOIN t_cliente c ON p.fk_cliente = c.id_cliente
                INNER JOIN ".BD_SEGURIDAD.".t_usuario u_c ON u_c.cedula = c.cedula
                INNER JOIN t_detalle_pedido dp ON dp.pedidos_ID_PEDIDO = p.id_pedidos
                INNER JOIN t_producto pr ON pr.id_producto = dp.producto_ID_PRODUCTO
                INNER JOIN t_emprendedor e ON e.id_emprededor = pr.fk_emprendedor
                INNER JOIN ".BD_SEGURIDAD.".t_usuario u_e ON u_e.cedula = e.cedula
                WHERE e.cedula = :id_emprendedor
                GROUP BY 
                    p.id_pedidos, 
                    pg.fecha_pago, 
                    u_e.nombre, u_e.apellido, 
                    u_c.nombre, u_c.apellido, 
                    pg.id_pagos, pg.estatus
                ORDER BY pg.fecha_pago DESC;",
                [':id_emprendedor' => $id_emprendedor]
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en mostrarPagosPorEmprendedor: " . $e->getMessage());
            return [];
        }
    }

    public function mostrarPagosPorCliente($id_cliente) {
        try {
            // Validar ID cliente
            $resultado = $this->validarNumerico($id_cliente, 'cliente', true);
            if ($resultado !== true) {
                throw new Exception("ID de cliente inválido: " . $resultado);
            }

            $stmt = $this->query(
                "SELECT 
                    pg.id_pagos, 
                    p.id_pedidos AS pedido_id, 
                    pg.fecha_pago, 
                    u_e.nombre AS emprendedor_nombre, 
                    u_e.apellido AS emprendedor_apellido, 
                    u_c.nombre AS cliente_nombre, 
                    u_c.apellido AS cliente_apellido, 
                    SUM(dp.cantidad * dp.precio_unitario) AS total_pedido, 
                    pg.estatus AS estado_pago
                FROM t_pagos pg
                INNER JOIN t_pedidos p ON pg.fk_pedido = p.id_pedidos
                INNER JOIN t_cliente c ON p.fk_cliente = c.id_cliente
                INNER JOIN ".BD_SEGURIDAD.".t_usuario u_c ON u_c.cedula = c.cedula
                INNER JOIN t_detalle_pedido dp ON dp.pedidos_ID_PEDIDO = p.id_pedidos
                INNER JOIN t_producto pr ON pr.id_producto = dp.producto_ID_PRODUCTO
                INNER JOIN t_emprendedor e ON e.id_emprededor = pr.fk_emprendedor
                INNER JOIN ".BD_SEGURIDAD.".t_usuario u_e ON u_e.cedula = e.cedula
                WHERE c.cedula = :id_cliente
                GROUP BY 
                    p.id_pedidos, pg.fecha_pago, 
                    u_e.nombre, u_e.apellido, 
                    u_c.nombre, u_c.apellido, 
                    pg.id_pagos, pg.estatus
                ORDER BY pg.fecha_pago DESC;",
                [':id_cliente' => $id_cliente]
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en mostrarPagosPorCliente: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerPagoId($referencia) {
        try {
            // Validar referencia
            $resultado = $this->validarReferenciaPago($referencia, 'referencia', true);
            if ($resultado !== true) {
                throw new Exception("Referencia de pago inválida: " . $resultado);
            }

            $stmt = $this->query(
                "SELECT * FROM t_pagos WHERE referencia = :referencia",
                [':referencia' => $referencia]
            );
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerPagoId: " . $e->getMessage());
            return false;
        }
    } 

    // Registrar solo el pago
    public function registrarPago() {
        try {
            // Validar datos antes de registrar
            if (empty($this->fk_pedido) || empty($this->estatus)) {
                throw new Exception("Datos incompletos para registrar el pago");
            }

            $this->query(
                "INSERT INTO t_pagos (fk_pedido, estatus, fecha_pago) 
                 VALUES (:fk_pedido, :estatus, NOW())",
                [
                    ':fk_pedido' => $this->fk_pedido,
                    ':estatus' => $this->estatus
                ]
            );
            return $this->lastInsertId();
        } catch (Exception $e) {
            error_log("Error en registrarPago: " . $e->getMessage());
            throw new Exception("Error al registrar el pago: " . $e->getMessage());
        }
    }

    // Registrar pago + detalles
    public function registrarPagoDetalle($detalle) {
        try {
            // Validar detalle
            if (empty($detalle) || !is_array($detalle)) {
                throw new Exception("El detalle del pago debe ser un array no vacío");
            }

            // Validar cada item del detalle
            foreach ($detalle as $index => $item) {
                $resultado = $this->validarDetallePago($item, $index);
                if ($resultado !== true) {
                    throw new Exception($resultado);
                }
            }

            $this->beginTransaction();

            $pagoId = $this->registrarPago();

            foreach ($detalle as $item) {
                $this->query(
                    "INSERT INTO t_detalle_pago (fk_pago, fk_detalle_metodo_pago, monto, referencia) 
                     VALUES (:fk_pago, :fk_detalle_metodo_pago, :monto, :referencia)",
                    [
                        ':fk_pago' => $pagoId,
                        ':fk_detalle_metodo_pago' => $item['fk_detalle_metodo_pago'],
                        ':monto' => $item['monto'],
                        ':referencia' => $item['referencia']
                    ]
                );
            }

            $this->commit();
            return $pagoId;

        } catch (Exception $e) {
            $this->rollBack();
            throw new Exception("No se pudo registrar el pago: " . $e->getMessage());
        }
    }

    public function obtenerEstatusPago($estatus) {
        try {
            // Validar estatus
            $resultado = $this->validarEstatusPago($estatus);
            if ($resultado !== true) {
                throw new Exception("Estatus de pago inválido: " . $resultado);
            }

            $stmt = $this->query(
                "SELECT 
                    p.*,
                    ped.*,
                    c.*,
                    p.id_pagos,
                    u.nombre AS nombre,
                    u.apellido AS apellido,
                    p.estatus AS estatus_pago
                FROM t_pagos p
                JOIN t_pedidos ped ON p.fk_pedido = ped.id_pedidos
                JOIN t_cliente c ON ped.fk_cliente = c.id_cliente
                JOIN ".BD_SEGURIDAD.".t_usuario u ON u.cedula = c.cedula
                WHERE p.estatus = :estatus;",
                [':estatus' => $estatus]
            );
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerEstatusPago: " . $e->getMessage());
            return false;
        }
    }

    public function aprobar($estatusPago, $id_pagos, $id_pedidos) {
        try {
            // Validaciones previas usando las funciones del trait
            if (empty($estatusPago) || empty($id_pagos) || empty($id_pedidos)) {
                return [
                    'success' => false,
                    'message' => 'Datos incompletos: faltan campos requeridos.'
                ];
            }

            // Validar estatus usando la función del trait
            $resultadoEstatus = $this->validarEstatusPagoAprobacion($estatusPago);
            if ($resultadoEstatus !== true) {
                return [
                    'success' => false,
                    'message' => $resultadoEstatus
                ];
            }

            // Validar IDs usando la función del trait
            $resultadoPago = $this->validarNumerico($id_pagos, 'pago', true);
            if ($resultadoPago !== true) {
                return [
                    'success' => false,
                    'message' => 'ID de pago inválido.'
                ];
            }

            $resultadoPedido = $this->validarNumerico($id_pedidos, 'pedido', true);
            if ($resultadoPedido !== true) {
                return [
                    'success' => false,
                    'message' => 'ID de pedido inválido.'
                ];
            }

            // Verificar si el pago existe
            $stmt = $this->query(
                "SELECT id_pagos FROM t_pagos WHERE id_pagos = :id_pagos",
                [':id_pagos' => $id_pagos]
            );
            if ($stmt->rowCount() === 0) {
                return [
                    'success' => false,
                    'message' => 'El pago especificado no existe.'
                ];
            }

            // Verificar si el pedido existe
            $stmt = $this->query(
                "SELECT id_pedidos FROM t_pedidos WHERE id_pedidos = :id_pedidos",
                [':id_pedidos' => $id_pedidos]
            );
            if ($stmt->rowCount() === 0) {
                return [
                    'success' => false,
                    'message' => 'El pedido asociado no existe.'
                ];
            }

            // Iniciar la transacción
            $this->beginTransaction();

            // Actualizar el estatus del pago
            $this->query(
                "UPDATE t_pagos SET estatus = :estatusPago WHERE id_pagos = :id_pagos",
                [
                    ':estatusPago' => $estatusPago,
                    ':id_pagos' => $id_pagos
                ]
            );

            // Actualizar el estatus del pedido
            $nuevoEstatusPedido = ($estatusPago === 'Aprobado') ? 'En proceso' : 'Pendiente';
            $this->query(
                "UPDATE t_pedidos SET estatus = :estatusPedido WHERE id_pedidos = :id_pedidos",
                [
                    ':estatusPedido' => $nuevoEstatusPedido,
                    ':id_pedidos' => $id_pedidos
                ]
            );

            // Confirmar la transacción
            $this->commit();

            return [
                'success' => true,
                'message' => "Pago {$estatusPago} y pedido actualizado correctamente."
            ];

        } catch (Exception $e) {
            $this->rollBack();
            error_log("Error en aprobar: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error en la transacción: ' . $e->getMessage()
            ];
        }
    }

    public function rechazar($estatusPago, $id_pagos, $id_pedidos) {
        try {
            // === 1. Validaciones previas ===
            if (empty($estatusPago) || empty($id_pagos) || empty($id_pedidos)) {
                return [
                    'success' => false,
                    'message' => 'Datos incompletos: faltan campos requeridos.'
                ];
            }

            if ($estatusPago !== 'Rechazado') {
                return [
                    'success' => false,
                    'message' => 'Estatus de pago inválido. Solo se permite "Rechazado".'
                ];
            }

            // === 2. Verificar existencia de pago ===
            $stmt = $this->query(
                "SELECT id_pagos FROM t_pagos WHERE id_pagos = :id_pagos",
                [':id_pagos' => $id_pagos]
            );
            if ($stmt->rowCount() === 0) {
                return [
                    'success' => false,
                    'message' => 'El pago especificado no existe.'
                ];
            }

            // === 3. Verificar existencia del pedido ===
            $stmt = $this->query(
                "SELECT id_pedidos FROM t_pedidos WHERE id_pedidos = :id_pedidos",
                [':id_pedidos' => $id_pedidos]
            );
            if ($stmt->rowCount() === 0) {
                return [
                    'success' => false,
                    'message' => 'El pedido asociado no existe.'
                ];
            }

            // === 4. Obtener los productos del pedido ===
            $stmt = $this->query(
                "SELECT producto_ID_PRODUCTO AS id_producto, cantidad 
                 FROM t_detalle_pedido 
                 WHERE pedidos_ID_PEDIDO = :id_pedidos",
                [':id_pedidos' => $id_pedidos]
            );
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($productos)) {
                return [
                    'success' => false,
                    'message' => 'El pedido no tiene productos asociados.'
                ];
            }

            // === 5. Iniciar transacción ===
            $this->beginTransaction();

            // 5.1 Actualizar el estatus del pago
            $this->query(
                "UPDATE t_pagos SET estatus = :estatusPago WHERE id_pagos = :id_pagos",
                [
                    ':estatusPago' => $estatusPago,
                    ':id_pagos' => $id_pagos
                ]
            );

            // 5.2 Actualizar el estatus del pedido
            $this->query(
                "UPDATE t_pedidos SET estatus = 'Anulado' WHERE id_pedidos = :id_pedidos",
                [':id_pedidos' => $id_pedidos]
            );

            // 5.3 Devolver stock de cada producto
            foreach ($productos as $producto) {
                $this->query(
                    "UPDATE t_producto SET stock = stock + :cantidad WHERE id_producto = :id_producto",
                    [
                        ':cantidad' => $producto['cantidad'],
                        ':id_producto' => $producto['id_producto']
                    ]
                );
            }

            // 5.4 Confirmar transacción
            $this->commit();

            return [
                'success' => true,
                'message' => 'Pago rechazado y pedido anulado correctamente. Stock devuelto.'
            ];

        } catch (Exception $e) {
            $this->rollBack();
            error_log("Error en rechazar: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error en la transacción de rechazo: ' . $e->getMessage()
            ];
        }
    }

    public function obtenerPago($idPago) {
        try {
            // Validar ID pago
            $resultado = $this->validarNumerico($idPago, 'pago', true);
            if ($resultado !== true) {
                throw new Exception("ID de pago inválido: " . $resultado);
            }

            $stmt = $this->query(
                "SELECT 
                    pe.id_pedidos,
                    p.id_pagos,
                    p.estatus AS estatus_pago,
                    p.fecha_pago,
                    c.id_cliente,
                    u.nombre AS cliente_nombre,
                    u.apellido AS cliente_apellido,
                    u.correo
                FROM t_pagos p
                INNER JOIN t_pedidos pe ON p.fk_pedido = pe.id_pedidos
                INNER JOIN t_cliente c ON pe.fk_cliente = c.id_cliente
                INNER JOIN ".BD_SEGURIDAD.".t_usuario u ON u.cedula = c.cedula
                INNER JOIN t_detalle_pago dp ON dp.fk_pago = p.id_pagos
                WHERE p.id_pagos = ?
                LIMIT 1;",
                [$idPago]
            );
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerPago: " . $e->getMessage());
            return false;
        }
    }
    
    public function obtenerDetallePago($idPago) {
        try {
            // Validar ID pago
            $resultado = $this->validarNumerico($idPago, 'pago', true);
            if ($resultado !== true) {
                throw new Exception("ID de pago inválido: " . $resultado);
            }

            $stmt = $this->query(
                "SELECT 
                    mp.nombre AS metodo_pago,
                    m.nombre AS moneda,
                    m.simbolo,
                    dp.monto,
                    dp.referencia,
                    dp.comprobante
                FROM t_detalle_pago dp
                INNER JOIN t_detalle_metodo_pago dmp ON dp.fk_detalle_metodo_pago = dmp.id_detalle_metodo_pago
                INNER JOIN t_metodo_pago mp ON dmp.fk_metodo_pago = mp.id_metodo_pago
                INNER JOIN t_moneda m ON dmp.fk_moneda = m.id_moneda
                WHERE dp.fk_pago = ?",
                [$idPago]
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerDetallePago: " . $e->getMessage());
            return [];
        }
    }
}
?>