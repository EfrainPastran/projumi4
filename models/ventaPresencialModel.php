<?php
namespace App\Models;

use App\Model;
use App\ValidadorTrait;
use PDO;
use Exception;

class VentaPresencialModel extends Model {
    use ValidadorTrait;

    protected $connectionKey = 'projumi'; // conexión a la BD projumi

    // ==================================================
    // VALIDACIÓN DE CAMPOS
    // ==================================================
    private function validarCampos($id_cliente, $productos, $metodosPago): array {
        $this->errores = [];

        // === VALIDAR CLIENTE ===
        $resultadoCliente = $this->validarNumerico($id_cliente, 'cliente', 1, 11);
        if ($resultadoCliente !== true) {
            $this->errores['id_cliente'] = $resultadoCliente;
        }

        // === VALIDAR PRODUCTOS ===
        if (!empty($productos) && is_array($productos)) {
            foreach ($productos as $index => $item) {
                if (isset($item['id_producto'])) {
                    $r = $this->validarNumerico($item['id_producto'], "producto[$index]", 1, 11);
                    if ($r !== true) $this->errores["producto_id_$index"] = $r;
                }
                if (isset($item['cantidad'])) {
                    $r = $this->validarNumerico($item['cantidad'], "cantidad[$index]", 1, 11);
                    if ($r !== true) $this->errores["cantidad_$index"] = $r;
                }
                if (isset($item['precio_unitario'])) {
                    $r = $this->validarDecimal($item['precio_unitario'], "precio_unitario[$index]", 1, 10000000);
                    if ($r !== true) $this->errores["precio_unitario_$index"] = $r;
                }
            }
        } else {
            $this->errores['productos'] = 'Debe incluir un arreglo válido de productos.';
        }

        // === VALIDAR MÉTODOS DE PAGO ===
            foreach ($metodosPago as $i => $pago) {
                if (isset($pago['id_metodo_pago'])) {
                    $r = $this->validarNumerico($pago['id_metodo_pago'], "método_pago[$i]", 1, 11);
                    if ($r !== true) $this->errores["metodo_pago_$i"] = 'El método de pago no es válido.';
                }
                if (isset($pago['monto'])) {
                    $r = $this->validarDecimal($pago['monto'], "monto[$i]", 1, 10000000);
                    if ($r !== true) $this->errores["monto_$i"] = $r;
                }
                if (isset($pago['referencia']) && strlen($pago['referencia']) < 3) {
                    $this->errores["referencia_$i"] = 'La referencia debe tener al menos 3 caracteres.';
                }
            }
        

        // === RESULTADO ===
        if (!$this->sinErrores()) {
                return [
                    'success' => false,
                    'message' => implode(' | ', $this->obtenerErrores()),
                    'errors' => $this->obtenerErrores()
                ];
        }

        return ['success' => true];
    }

    // ==================================================
    // OBTENER VENTAS POR EMPRENDEDOR
    // ==================================================
    public function getVentasPorEmprendedor($cedula): array {
        try {
            $resultado = $this->validarNumerico($cedula, 'cedula', 7, 11);
            if ($resultado !== true) {
                return ['success' => false, 'message' => $resultado];
            }

            $sql = "
            SELECT 
            p.id_pedidos,
            p.fecha_pedido,
            p.estatus,
            u.cedula,
            CONCAT(u.nombre, ' ', u.apellido) AS cliente,
            emp.id_emprededor,
            CONCAT(u_emp.nombre, ' ', u_emp.apellido) AS emprendedor,
            SUM(dp.cantidad * dp.precio_unitario) AS total_comprado
        FROM t_pedidos p
        LEFT JOIN t_envio e ON p.id_pedidos = e.fk_pedido
        LEFT JOIN t_delivery d ON p.id_pedidos = d.fk_pedido
        INNER JOIN t_cliente c ON c.id_cliente = p.fk_cliente
        INNER JOIN ".BD_SEGURIDAD.".t_usuario u ON u.cedula = c.cedula
        -- JOINs para llegar al emprendedor
        INNER JOIN t_detalle_pedido dp ON dp.pedidos_ID_PEDIDO = p.id_pedidos
        INNER JOIN t_producto prod ON prod.id_producto = dp.producto_ID_PRODUCTO
        INNER JOIN t_emprendedor emp ON emp.id_emprededor = prod.fk_emprendedor
        INNER JOIN ".BD_SEGURIDAD.".t_usuario u_emp ON u_emp.cedula = emp.cedula
        -- Filtro: pedidos que NO están ni en envio ni en delivery
        WHERE e.fk_pedido IS NULL 
        AND d.fk_pedido IS NULL
        AND emp.cedula = :cedula
        GROUP BY 
            p.id_pedidos,
            p.fecha_pedido,
            p.estatus,
            u.cedula,
            u.nombre,
            u.apellido,
            emp.id_emprededor,
            u_emp.nombre,
            u_emp.apellido ORDER BY p.fecha_pedido DESC;                    
            ";

            $stmt = $this->query($sql, [':cedula' => $cedula]);
            $this->closeConnection();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en getVentasPorEmprendedor: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al obtener ventas.'];
        }
    }

    // ==================================================
    // REGISTRAR VENTA PRESENCIAL
    // ==================================================
    public function registrarVenta($id_cliente, $productos, $metodosPago): array {
        try {
            // === VALIDAR ENTRADAS ===
            $validacion = $this->validarCampos($id_cliente, $productos, $metodosPago);
            if (!$validacion['success']) {
                return $validacion;
            }

            $this->beginTransaction();

            // === CLIENTE EXISTE ===
            $stmtCliente = $this->query("SELECT id_cliente FROM t_cliente WHERE id_cliente = :id", [':id' => $id_cliente]);
            if (!$stmtCliente->fetch(PDO::FETCH_ASSOC)) {
                $this->rollBack();
                return ['success' => false, 'message' => "El cliente con ID $id_cliente no existe."];
            }

            // === VALIDAR PRODUCTOS ===
            if (!empty($productos) && is_array($productos)) {
                foreach ($productos as $index => $item) {
                    if (isset($item['id_producto'])) {
                        $stmtCheck = $this->query("SELECT * FROM t_producto WHERE id_producto = :id", [
                            ':id' => $item['id_producto']
                        ]);
                        if (!$stmtCheck->fetch(PDO::FETCH_ASSOC)) {
                            $this->rollBack();
                            return ['success' => false, 'message' => "El producto con ID {$item['id_producto']} no existe."];
                        }
                    }
                }
            }

            // === REGISTRAR PEDIDO ===
            $this->query("INSERT INTO t_pedidos (fecha_pedido, estatus, fk_cliente) VALUES (NOW(), 'Completado', :fk_cliente)", [
                ':fk_cliente' => $id_cliente
            ]);
            $idPedido = $this->lastInsertId();

            // === DETALLE DE PRODUCTOS ===
            foreach ($productos as $producto) {
                // Verificar stock
                $stmtStock = $this->query("SELECT stock, nombre FROM t_producto WHERE id_producto = :id", [
                    ':id' => $producto['id_producto']
                ]);
                $productoData = $stmtStock->fetch(PDO::FETCH_ASSOC);

                if (!$productoData) {
                    $this->rollBack();
                    return ['success' => false, 'message' => "El producto con ID {$producto['id_producto']} no existe."];
                }
                if ($productoData['stock'] < $producto['cantidad']) {
                    $this->rollBack();
                    return ['success' => false, 'message' => "Stock insuficiente para '{$productoData['nombre']}'."];
                }

                // Insertar detalle y actualizar stock
                $this->query("
                    INSERT INTO t_detalle_pedido (producto_ID_PRODUCTO, pedidos_ID_PEDIDO, cantidad, precio_unitario)
                    VALUES (:prod, :pedido, :cant, :precio)
                ", [
                    ':prod' => $producto['id_producto'],
                    ':pedido' => $idPedido,
                    ':cant' => $producto['cantidad'],
                    ':precio' => $producto['precio_unitario']
                ]);

                $this->query("UPDATE t_producto SET stock = stock - :cant WHERE id_producto = :id", [
                    ':cant' => $producto['cantidad'],
                    ':id' => $producto['id_producto']
                ]);
            }

            // === REGISTRAR PAGO ===
            $this->query("INSERT INTO t_pagos (estatus, fk_pedido, fecha_pago) VALUES ('Aprobado', :pedido, NOW())", [
                ':pedido' => $idPedido
            ]);
            $idPago = $this->lastInsertId();

            // === DETALLE DE PAGO ===
            foreach ($metodosPago as $pago) {
                $this->query("
                    INSERT INTO t_detalle_pago (fk_pago, fk_detalle_metodo_pago, monto, referencia)
                    VALUES (:fk_pago, :fk_metodo, :monto, :ref)
                ", [
                    ':fk_pago' => $idPago,
                    ':fk_metodo' => $pago['id_metodo_pago'],
                    ':monto' => $pago['monto'],
                    ':ref' => $pago['referencia'] ?? '-'
                ]);
            }

            $this->commit();
            $this->closeConnection();

            return ['success' => true, 'message' => 'Venta registrada correctamente.'];
        } catch (Exception $e) {
            $this->rollBack();
            error_log("Error en registrarVenta: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al registrar la venta.'];
        }
    }
}
