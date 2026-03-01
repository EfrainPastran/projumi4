<?php
namespace App\Models;
use PDO;
use PDOException;
use Exception;
use App\Model;
use App\ValidadorTrait;
class PedidoModel extends Model {
    use ValidadorTrait;
    protected $connectionKey = 'projumi';
    private $id_pedidos;
    private $fecha_pedido;
    private $estatus;
    private $fk_cliente;

    // SETTERS
    public function set_estatus($estatus) { $this->estatus = $estatus; }
    
    // ======================
    // Validar campos (formato)
    // ======================
    private function validarCampos($fk_cliente, $detallePedido, $detalleEnvio, $detallePago)
    {
        $this->errores = [];

        // =========================
        // Validar cliente
        // =========================
        $result = $this->validarNumerico($fk_cliente, 'cliente', 1, 11);
        if ($result !== true) $this->errores['fk_cliente'] = $result;

        // =========================
        // Validar detalle del pedido
        // =========================
        if (!empty($detallePedido['detalle']) && is_array($detallePedido['detalle'])) {
            foreach ($detallePedido['detalle'] as $index => $item) {
                if (isset($item['id'])) {
                    $result = $this->validarNumerico($item['id'], "producto[$index]", 1, 11);
                    if ($result !== true) $this->errores["producto_id_$index"] = $result;
                }

                if (isset($item['cantidad'])) {
                    $result = $this->validarNumerico($item['cantidad'], "cantidad[$index]", 1, 11);
                    if ($result !== true) $this->errores["cantidad_$index"] = $result;
                }

                if (isset($item['precio'])) {
                    $result = $this->validarDecimal($item['precio'], "precio[$index]", 0.5, 999999999);
                    if ($result !== true) $this->errores["precio_$index"] = $result;
                }
            }
        } else {
            $this->errores['detallePedido'] = 'Debe enviar un arreglo válido con los productos.';
        }

        // =========================
        // Validar detalle de envío
        // =========================
        if (!empty($detalleEnvio['modoEntrega'])) {
            $modo = strtolower(trim($detalleEnvio['modoEntrega']));

            if ($modo === 'delivery') {
                // Destinatario
                $result = $this->validarTexto($detalleEnvio['destinatario'] ?? '', 'destinatario', 5, 60);
                if ($result !== true) $this->errores['destinatario'] = $result;

                // Teléfono
                $result = $this->validarTelefono($detalleEnvio['telefono_destinatario'] ?? '');
                if ($result !== true) $this->errores['telefono_destinatario'] = $result;

                // Correo
                /*$result = $this->validarCorreo($detalleEnvio['correo_destinatario'] ?? '');
                if ($result !== true) $this->errores['correo_destinatario'] = $result;*/

                // Dirección exacta
                $result = $this->validarDescripcion($detalleEnvio['direccion_exacta'] ?? '', 'direccion_exacta', 5, 60);
                if ($result !== true) $this->errores['direccion_exacta'] = $result;
            }

            if ($modo === 'envio nacional') {
                // Empresa de envío
                $result = $this->validarNumerico($detalleEnvio['empresaEnvio'] ?? '', 'empresa de envío', 1, 11);
                if ($result !== true) $this->errores['empresaEnvio'] = $result;

                // Dirección de envío
                $result = $this->validarDescripcion($detalleEnvio['direccionEnvio'] ?? '', 'direccionEnvio', 5, 60);
                if ($result !== true) $this->errores['direccionEnvio'] = $result;
            }
        } else {
            $this->errores['modoEntrega'] = 'Debe indicar un modo de entrega.';
        }

        // =========================
        // Validar detalle de pago
        // =========================
        $pagos = $detallePago['detalles'] ?? $detallePago;

        if (!empty($pagos) && is_array($pagos)) {
            foreach ($pagos as $i => $pago) {
                // Método de pago
                if (isset($pago['fk_detalle_metodo_pago'])) {
                    $result = $this->validarNumerico($pago['fk_detalle_metodo_pago'], "detallePago[$i]", 1, 11);
                    if ($result !== true) $this->errores["metodo_pago_$i"] = $result;
                }

                // Monto
                if (isset($pago['monto'])) {
                    $result = $this->validarDecimal($pago['monto'], "monto[$i]", 0.5, 999999999);
                    if ($result !== true) $this->errores["monto_$i"] = $result;
                }

                // Referencia (opcional)
                if (!empty($pago['referencia'])) {
                    $result = $this->validarNumerico($pago['referencia'], 'referencia', 4, 15);
                    if ($result !== true) $this->errores["referencia_$i"] = $result;
                }
            }
        } else {
            $this->errores['detallePago'] = 'Debe enviar al menos un método de pago válido.';
        }

        // =========================
        // Devolver resultado
        // =========================
        if (!$this->sinErrores()) {
            return [
                'success' => false,
                'errors' => $this->obtenerErrores()
            ];
        }

        return ['success' => true];
    }

    public function registrarPedidoCompleto($fk_cliente, $detallePedido, $detalleEnvio, $detallePago)
    {
        try {
            $validacion = $this->validarCampos($fk_cliente, $detallePedido, $detalleEnvio, $detallePago);
            if (!$validacion['success']) {
                return $validacion;
            }
            // ==============================
            // VALIDACIONES LÓGICAS
            // ==============================

            // Verificar cliente
            $Cliente = new ClienteModel();
            if (!$Cliente->clienteExiste($fk_cliente)) {
                return ['success' => false, 'message' => 'El cliente no existe en el sistema.'];
            }

            // Verificar productos
            $Producto = new ProductosModel();
            foreach ($detallePedido['detalle'] as $item) {
                $Producto->set_id_producto($item['id']);
                $producto = $Producto->getProducto($item['id']);
                if (!$producto) {
                    return ['success' => false, 'message' => "Producto con ID {$item['id']} no encontrado."];
                }
                if ($producto['stock'] < $item['cantidad']) {
                    return ['success' => false, 'message' => "Stock insuficiente para '{$producto['nombre']}'."];
                }
            }

            // Verificar métodos de pago
            $pagos = $detallePago['detalles'] ?? $detallePago;

            if (!is_array($pagos) || empty($pagos)) {
                throw new Exception("El formato de detallePago no es válido.");
            }

            foreach ($pagos as $pago) {

                $stmt = $this->query("
                    SELECT dmp.id_detalle_metodo_pago 
                    FROM t_detalle_metodo_pago dmp
                    JOIN t_metodo_pago mp ON mp.id_metodo_pago = dmp.fk_metodo_pago
                    JOIN t_moneda m ON m.id_moneda = dmp.fk_moneda
                    WHERE dmp.id_detalle_metodo_pago = :id
                    LIMIT 1
                ", [':id' => $pago['fk_detalle_metodo_pago']]);
                $this->closeConnection();
                $metodo = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$metodo) {
                    return ['success' => false, 'message' => "El detalle de método de pago con ID {$pago['fk_detalle_metodo_pago']} no existe."];
                }
            }

            // ==============================
            //  INICIO DE TRANSACCIÓN
            // ==============================
            $this->beginTransaction();

            // ==============================
            //  REGISTRAR PEDIDO
            // ==============================
            $this->query("
                INSERT INTO t_pedidos (fecha_pedido, estatus, fk_cliente)
                VALUES (NOW(), 'Pendiente', :fk_cliente)
            ", [':fk_cliente' => $fk_cliente]);

            $stmt = $this->query("SELECT LAST_INSERT_ID() AS id");
            $pedidoId = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

            // ==============================
            //  DETALLE DEL PEDIDO
            // ==============================
            foreach ($detallePedido['detalle'] as $item) {
                $this->query("
                    INSERT INTO t_detalle_pedido (producto_ID_PRODUCTO, pedidos_ID_PEDIDO, cantidad, precio_unitario)
                    VALUES (:producto_id, :pedido_id, :cantidad, :precio)
                ", [
                    ':producto_id' => $item['id'],
                    ':pedido_id'   => $pedidoId,
                    ':cantidad'    => $item['cantidad'],
                    ':precio'      => $item['precio']
                ]);

                // Actualizar stock
                $this->query("
                    UPDATE t_producto SET stock = stock - :cantidad WHERE id_producto = :id
                ", [
                    ':cantidad' => $item['cantidad'],
                    ':id'       => $item['id']
                ]);
            }

            // ==============================
            //  REGISTRAR ENVÍO / DELIVERY
            // ==============================
            $modoEntrega = strtolower(trim($detalleEnvio['modoEntrega'] ?? ''));

            if ($modoEntrega === 'delivery') {
                $this->query("
                    INSERT INTO t_delivery (fk_pedido, destinatario, telefono_destinatario, correo_destinatario, direccion_exacta, estatus)
                    VALUES (:fk_pedido, :destinatario, :telefono, :correo, :direccion, 'Pendiente')
                ", [
                    ':fk_pedido'   => $pedidoId,
                    ':destinatario'=> $detalleEnvio['destinatario'],
                    ':telefono'    => $detalleEnvio['telefono_destinatario'],
                    ':correo'      => $detalleEnvio['correo_destinatario'],
                    ':direccion'   => $detalleEnvio['direccion_exacta']
                ]);
            } elseif ($modoEntrega === 'envio nacional') {
                $this->query("
                    INSERT INTO t_envio (fk_pedido, fk_empresa_envio, direccion_envio, estatus)
                    VALUES (:fk_pedido, :empresa, :direccion, 'Pendiente')
                ", [
                    ':fk_pedido' => $pedidoId,
                    ':empresa'   => $detalleEnvio['empresaEnvio'],
                    ':direccion' => $detalleEnvio['direccionEnvio']
                ]);
            }

            // ==============================
            //  REGISTRAR PAGO
            // ==============================
            $this->query("
                INSERT INTO t_pagos (fk_pedido, estatus, fecha_pago)
                VALUES (:fk_pedido, 'Pendiente', NOW())
            ", [':fk_pedido' => $pedidoId]);

            $stmt = $this->query("SELECT LAST_INSERT_ID() AS id");
            $pagoId = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

            foreach ($detallePago['detalles'] as $pago) {
                $this->query("
                    INSERT INTO t_detalle_pago (fk_pago, fk_detalle_metodo_pago, monto, referencia, comprobante)
                    VALUES (:fk_pago, :fk_detalle_metodo_pago, :monto, :referencia, :comprobante)
                ", [
                    ':fk_pago'                => $pagoId,
                    ':fk_detalle_metodo_pago' => $pago['fk_detalle_metodo_pago'],
                    ':monto'                  => $pago['monto'],
                    ':referencia'             => $pago['referencia'] ?? '',
                    ':comprobante'            => $pago['comprobante'] ?? null
                ]);
            }

            // ==============================
            //  FINALIZAR TRANSACCIÓN
            // ==============================
            $this->commit();
            $this->closeConnection();
            return [
                'success' => true,
                'message' => 'Pedido registrado correctamente.',
                'pedido_id' => $pedidoId
            ];

        } catch (Exception $e) {
            $this->rollBack();
            return ['success' => false, 'message' => 'Error al registrar pedido: ' . $e->getMessage()];
        }

    }
        
    public function consultarPedidoCompleto($idPedido)
    {
         if (empty($idPedido)) {
            return ['success' => false, 'message' => 'El ID del pedido es obligatorio.'];
        }

        if (!is_numeric($idPedido) || intval($idPedido) <= 0) {
            return ['success' => false, 'message' => 'El ID del pedido debe ser un número positivo.'];
        }

        try {
            $pedido = $this->obtenerPedido($idPedido);
            if (!$pedido) {
                return ['success' => false, 'message' => 'El pedido no existe.'];
            }

            $detalle = $this->obtenerDetallePedido($idPedido);
            if (empty($detalle)) {
                return ['success' => false, 'message' => 'El pedido no tiene productos asociados.'];
            }

            // Calcular total del pedido
            $total = 0;
            foreach ($detalle as &$prod) {
                if (!isset($prod['cantidad'], $prod['precio_unitario'])) {
                    return ['success' => false, 'message' => 'Error en los datos de los productos del pedido.'];
                }

                $prod['total_producto'] = $prod['cantidad'] * $prod['precio_unitario'];
                $total += $prod['total_producto'];
            }

            $pedido['total'] = $total;

            return [
                'success' => true,
                'pedido' => $pedido,
                'detalle' => $detalle
            ];
        } catch (Exception $e) {
            error_log("Error en consultarPedidoCompleto(): " . $e->getMessage());
            return ['success' => false, 'message' => 'Ocurrió un error al consultar el pedido.'];
        }
    }

    /**
     * Obtiene la cabecera del pedido y la información del cliente
     */
    public function obtenerPedido($idPedido)
    {
        try {
            $sql = "SELECT 
                        c.id_cliente AS id_cliente, 
                        p.id_pedidos AS id_pedidos, 
                        p.fecha_pedido AS fecha_pedido, 
                        p.estatus AS estatus, 
                        u.id_usuario AS id_usuario,
                        u.nombre AS cliente_nombre, 
                        u.apellido AS cliente_apellido, 
                        u.correo AS correo
                    FROM t_pedidos p
                    INNER JOIN t_cliente c ON p.fk_cliente = c.id_cliente
                    INNER JOIN " . BD_SEGURIDAD . ".t_usuario u ON u.cedula = c.cedula
                    WHERE p.id_pedidos = :id_pedido
                    LIMIT 1";

            $stmt = $this->query($sql, [':id_pedido' => $idPedido]);
            $this->closeConnection();

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Error en obtenerPedido(): " . $e->getMessage());
            throw new Exception("No se pudo obtener la información del pedido.");
        }
    }

    /**
     * Obtiene los productos asociados al pedido
     */
    public function obtenerDetallePedido($idPedido)
    {
        try {
            $sql = "SELECT 
                        dp.cantidad, 
                        dp.precio_unitario, 
                        pr.nombre AS nombre_producto, 
                        c.nombre AS categoria
                    FROM t_detalle_pedido dp
                    INNER JOIN t_producto pr ON pr.id_producto = dp.producto_ID_PRODUCTO
                    INNER JOIN t_categoria c ON pr.fk_categoria = c.id_categoria
                    WHERE dp.pedidos_ID_PEDIDO = ?";
            $stmt = $this->query($sql, [$idPedido]);
            $this->closeConnection();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerDetallePedido(): " . $e->getMessage());
            throw new Exception("No se pudo obtener el detalle del pedido.");
        }
    }

    public function detalleVenta($idPedido)
    {
        try {
            if (empty($idPedido) || !is_numeric($idPedido)) {
                return ['success' => false, 'message' => 'Debe proporcionar un ID de pedido válido.'];
            }

            $pedido = $this->obtenerPedido($idPedido);
            if (!$pedido['success']) {
                return $pedido;
            }

            $detalle = $this->obtenerDetallePedido($idPedido);
            if (empty($detalle)) {
                return ['success' => false, 'message' => 'No existen detalles para este pedido.'];
            }

            $total = 0;
            foreach ($detalle as &$p) {
                $p['total_producto'] = $p['cantidad'] * $p['precio_unitario'];
                $total += $p['total_producto'];
            }

            return [
                'success' => true,
                'message' => 'Detalle de venta cargado correctamente.',
                'data' => [
                    'pedido' => $pedido['data'],
                    'productos' => $detalle,
                    'total' => $total
                ]
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al obtener detalle: ' . $e->getMessage()];
        }
    }
    
    public function mostrarPedidos() {
        try {
            $stmt = $this->query("SELECT 
            p.id_pedidos, 
            p.fecha_pedido, 
            p.estatus, 
            CONCAT(u_e.nombre, ' ', u_e.apellido) AS emprendedor_nombre, 
            CONCAT(u_c.nombre, ' ', u_c.apellido) AS cliente_nombre, 
            SUM(d.cantidad * d.precio_unitario) AS total_pedido
        FROM t_pedidos p
        INNER JOIN t_cliente c ON p.fk_cliente = c.id_cliente
        INNER JOIN ".BD_SEGURIDAD.".t_usuario u_c ON u_c.cedula = c.cedula
        
        INNER JOIN t_detalle_pedido d ON p.id_pedidos = d.pedidos_ID_PEDIDO
        INNER JOIN t_producto pr ON pr.id_producto = d.producto_ID_PRODUCTO
        INNER JOIN t_emprendedor e ON e.id_emprededor = pr.fk_emprendedor
        INNER JOIN ".BD_SEGURIDAD.".t_usuario u_e ON u_e.cedula = e.cedula
        
        GROUP BY 
            p.id_pedidos, 
            p.fecha_pedido, 
            p.estatus, 
            u_e.nombre, u_e.apellido, 
            u_c.nombre, u_c.apellido
        ORDER BY p.fecha_pedido DESC;
        ");
            $this->closeConnection();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Total (monto $-Bs) de ventas por emprendimiento
    public function calcularTotalVentasPorEmprendedor($cedula_emprendedor) {
        try {
            $sql = "SELECT 
                SUM(d.cantidad * d.precio_unitario) AS total_dolares,
                SUM(d.cantidad * d.precio_unitario * tc.tasa_cambio) AS total_bolivares
            FROM t_pedidos p
            INNER JOIN t_detalle_pedido d ON d.pedidos_ID_PEDIDO = p.id_pedidos
            INNER JOIN t_producto pr ON pr.id_producto = d.producto_ID_PRODUCTO
            INNER JOIN t_emprendedor e ON e.id_emprededor = pr.fk_emprendedor
            INNER JOIN t_cambio tc ON tc.fecha_cambio = (
                SELECT MAX(tc_sub.fecha_cambio)
                FROM t_cambio tc_sub
                WHERE tc_sub.fecha_cambio <= p.fecha_pedido
            )
            WHERE e.cedula = :cedula_emprendedor;
            ";
    
            $stmt = $this->query($sql, [':cedula_emprendedor' => $cedula_emprendedor]);
            $this->closeConnection();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            return [
                'total_dolares' => $result['total_dolares'] ?? 0,
                'total_bolivares' => $result['total_bolivares'] ?? 0
            ];
        } catch (PDOException $e) {
            throw new Exception("Error al calcular el total de ventas: " . $e->getMessage());
        }
    }
       
    // Total (monto $-Bs) de ventas de todos los emprendimientos
    public function calcularTotalVentasGlobales() {
        try {
            $sql = "SELECT 
                        SUM(d.cantidad * d.precio_unitario) AS total_dolares,
                        SUM(d.cantidad * d.precio_unitario * tc.tasa_cambio) AS total_bolivares
                    FROM t_pedidos p
                    INNER JOIN t_detalle_pedido d ON d.pedidos_ID_PEDIDO = p.id_pedidos
                    INNER JOIN t_producto pr ON pr.id_producto = d.producto_ID_PRODUCTO
                    INNER JOIN t_emprendedor e ON e.id_emprededor = pr.fk_emprendedor
                    INNER JOIN t_cambio tc ON tc.fecha_cambio = (
                        SELECT MAX(tc_sub.fecha_cambio)
                        FROM t_cambio tc_sub
                        WHERE tc_sub.fecha_cambio <= p.fecha_pedido
                    )";
    
            $stmt = $this->query($sql);
            $this->closeConnection();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            return [
                'total_dolares' => $result['total_dolares'] ?? 0,
                'total_bolivares' => $result['total_bolivares'] ?? 0
            ];
        } catch (PDOException $e) {
            throw new Exception("Error al calcular las ventas globales: " . $e->getMessage());
        }
    }
    
    //Generar reporte de pedidos por emprendimiento
    public function mostrarPedidosPorEmprendedor($cedula, $dateFrom = null, $dateTo = null) {
        try {
            $dateFrom = empty($dateFrom) ? null : $dateFrom;
            $dateTo = empty($dateTo) ? null : $dateTo;
            $stmt = $this->query("CALL mostrarPedidosPorEmprendedor(?, ?, ?)", [$cedula, $dateFrom, $dateTo]);
            $this->closeConnection();
            $result = $stmt->fetchAll();
            return $result;
        } catch (Exception $e) {
            throw $e;
        }
    }    

    //Generar reporte de pedidos globales
    public function mostrarTodasLasVentas($dateFrom = null, $dateTo = null) {
        try {
            $query = "SELECT * FROM vista_todas_las_ventas WHERE 1=1";
    
            if (!empty($dateFrom)) {
                $query .= " AND DATE(fecha_pedido) >= :dateFrom";
            }
    
            if (!empty($dateTo)) {
                $query .= " AND DATE(fecha_pedido) <= :dateTo";
            }
    
            $query .= " ORDER BY fecha_pedido DESC";
    
            $stmt = $this->query($query);
            $this->closeConnection();
    
            if (!empty($dateFrom)) {
                $stmt->bindValue(':dateFrom', $dateFrom);
            }
    
            if (!empty($dateTo)) {
                $stmt->bindValue(':dateTo', $dateTo);
            }
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Depuración
            if (empty($result)) {
                error_log("Consulta sin resultados. Fecha desde: $dateFrom - hasta: $dateTo");
            }
    
            return $result;
    
        } catch (Exception $e) {
            error_log("Error al obtener todas las ventas: " . $e->getMessage());
            return false;
        }
    }
    
       
    // Total de clientes por emprendimiento
    public function contarClientesPorEmprendedor($cedula_emprendedor) {
        try {
            $sql = "SELECT COUNT(DISTINCT c.id_cliente) AS total_clientes
                    FROM t_pedidos p
                    INNER JOIN t_cliente c ON p.fk_cliente = c.id_cliente
                    INNER JOIN t_detalle_pedido d ON p.id_pedidos = d.pedidos_ID_PEDIDO
                    INNER JOIN t_producto pr ON pr.id_producto = d.producto_ID_PRODUCTO
                    INNER JOIN t_emprendedor e ON e.id_emprededor = pr.fk_emprendedor
                    WHERE e.cedula = :cedula_emprendedor";
    
            $stmt = $this->query($sql, [':cedula_emprendedor' => $cedula_emprendedor]);
            $this->closeConnection();
    
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total_clientes'] ?? 0;
        } catch (PDOException $e) {
            throw new Exception("Error al contar clientes por emprendedor: " . $e->getMessage());
        }
    }
    
    // Total de clientes globales
    public function contarClientesGlobales() {
        try {
            $sql = "SELECT COUNT(DISTINCT c.id_cliente) AS total_clientes
                    FROM t_pedidos p
                    INNER JOIN t_cliente c ON p.fk_cliente = c.id_cliente
                    INNER JOIN t_detalle_pedido d ON p.id_pedidos = d.pedidos_ID_PEDIDO
                    INNER JOIN t_producto pr ON pr.id_producto = d.producto_ID_PRODUCTO
                    INNER JOIN t_emprendedor e ON e.id_emprededor = pr.fk_emprendedor";
    
            $stmt = $this->query($sql);
            $this->closeConnection();
    
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total_clientes'] ?? 0;
        } catch (PDOException $e) {
            throw new Exception("Error al contar clientes globales: " . $e->getMessage());
        }
    }
    
    //Contar productos vendidos por emprendimiento
    public function contarProductosVendidosPorEmprendedor($cedula_emprendedor) {
        try {
            $sql = "SELECT SUM(d.cantidad) AS total_productos_vendidos
                    FROM t_pedidos p
                    INNER JOIN t_detalle_pedido d ON p.id_pedidos = d.pedidos_ID_PEDIDO
                    INNER JOIN t_producto pr ON pr.id_producto = d.producto_ID_PRODUCTO
                    INNER JOIN t_emprendedor e ON e.id_emprededor = pr.fk_emprendedor
                    WHERE e.cedula = :cedula_emprendedor";
    
            $stmt = $this->query($sql, [':cedula_emprendedor' => $cedula_emprendedor]);
            $this->closeConnection();
    
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total_productos_vendidos'] ?? 0;
        } catch (PDOException $e) {
            throw new Exception("Error al contar productos vendidos por emprendedor: " . $e->getMessage());
        }
    }    

    // Total de productos vendidos globalmente
    public function contarProductosVendidosGlobales() {
        try {
            $sql = "SELECT SUM(d.cantidad) AS total_productos_vendidos
                    FROM t_pedidos p
                    INNER JOIN t_detalle_pedido d ON p.id_pedidos = d.pedidos_ID_PEDIDO
                    INNER JOIN t_producto pr ON pr.id_producto = d.producto_ID_PRODUCTO
                    INNER JOIN t_emprendedor e ON e.id_emprededor = pr.fk_emprendedor";
    
            $stmt = $this->query($sql);
            $this->closeConnection();
    
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total_productos_vendidos'] ?? 0;
        } catch (PDOException $e) {
            throw new Exception("Error al contar productos vendidos globalmente: " . $e->getMessage());
        }
    }    

    //Ventas mensuales por emprendimiento
    public function obtenerVentasMensuales($cedula_emprendedor) {
        $stmt = $this->query("CALL sp_obtener_ventas_mensuales(?)", [$cedula_emprendedor]);
        $this->closeConnection();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }    

    //Ventas mensuales por emprendimiento
    public function obtenerVentasMensualesPorEmprendedor() {
        $sql = "
            SELECT 
                MONTH(p.fecha_pedido) AS mes,
                e.emprendimiento AS emprendedor,
                SUM(d.cantidad * d.precio_unitario) AS total_dolares
            FROM t_pedidos p
            INNER JOIN t_detalle_pedido d ON d.pedidos_ID_PEDIDO = p.id_pedidos
            INNER JOIN t_producto pr ON pr.id_producto = d.producto_ID_PRODUCTO
            INNER JOIN t_emprendedor e ON e.id_emprededor = pr.fk_emprendedor
            GROUP BY mes, emprendedor
            ORDER BY mes ASC
        ";
        
        $stmt = $this->query($sql);
        $this->closeConnection();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    //Productos vendidos por cada emprendedor
    public function obtenerProductosMasVendidos($cedula_emprendedor) {
        $stmt = $this->query("CALL obtenerProductosMasVendidos(?)", [$cedula_emprendedor]);
        $this->closeConnection();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    //Productos vendidos globales
    public function obtenerProductosMasVendidosGlobal() {
        $stmt = $this->query("SELECT * FROM vista_productos_mas_vendidos_global");
        $this->closeConnection();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }        
    
    //Envíos mensuales por emprendimiento (reporte grafico)
    public function obtenerEnviosMensuales($cedula_emprendedor) {
        $stmt = $this->query("CALL sp_obtener_envios_mensuales(?)", [$cedula_emprendedor]);
        $this->closeConnection();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
        
    //Envíos mensuales globales (reporte grafico)
    public function obtenerEnviosMensualesGlobal() {
        $sql = "
            SELECT 
                MONTH(p.fecha_pedido) AS mes,
                COUNT(DISTINCT e.id_envio) AS cantidad_envios,
                em.emprendimiento AS emprendedor
            FROM t_envio e
            INNER JOIN t_pedidos p ON p.id_pedidos = e.fk_pedido
            INNER JOIN t_detalle_pedido dp ON dp.pedidos_ID_PEDIDO = p.id_pedidos
            INNER JOIN t_producto pr ON pr.id_producto = dp.producto_ID_PRODUCTO
            INNER JOIN t_emprendedor em ON em.id_emprededor = pr.fk_emprendedor
            GROUP BY mes, emprendedor
            ORDER BY mes ASC
        ";
    
        $stmt = $this->query($sql);
        $this->closeConnection();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }    

    function mostrarPedidosPorCliente($id_cliente) {
        $sql = "SELECT 
                p.id_pedidos,
                p.fecha_pedido,
                p.estatus,
                uc.nombre AS cliente_nombre,
                uc.apellido AS cliente_apellido,
                ue.nombre AS emprendedor_nombre,
                ue.apellido AS emprendedor_apellido,
                SUM(d.cantidad * d.precio_unitario) AS total_pedido
            FROM t_pedidos p
            INNER JOIN t_cliente c ON p.fk_cliente = c.id_cliente
            INNER JOIN ".BD_SEGURIDAD.".t_usuario uc ON uc.cedula = c.cedula
            INNER JOIN t_detalle_pedido d ON p.id_pedidos = d.pedidos_ID_PEDIDO
            INNER JOIN t_producto pr ON d.producto_ID_PRODUCTO = pr.id_producto
            INNER JOIN t_emprendedor e ON pr.fk_emprendedor = e.id_emprededor
            INNER JOIN ".BD_SEGURIDAD.".t_usuario ue ON ue.cedula = e.cedula
            WHERE c.cedula = :id_cliente
            GROUP BY 
                p.id_pedidos,
                p.fecha_pedido,
                p.estatus,
                uc.nombre,
                uc.apellido,
                ue.nombre,
                ue.apellido
            ORDER BY p.fecha_pedido DESC;    
        ";
        $stmt = $this->query($sql, [$id_cliente]);
        $this->closeConnection();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function pedidoExiste($id_pedido) {
        $sql = "SELECT COUNT(*) FROM t_pedidos WHERE id_pedidos = :id_pedido";
        $stmt = $this->query($sql, [$id_pedido]);
        $this->closeConnection();
        return $stmt->fetchColumn() > 0;
    }    
}