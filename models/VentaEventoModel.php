<?php
namespace App\Models;

use App\Model;
use App\ValidadorTrait;
use PDO;
use Exception;

class VentaEventoModel extends Model {
    use ValidadorTrait;

    protected $connectionKey = 'projumi'; // conexión a la BD projumi

    // ==================================================
    // VALIDAR CAMPOS DE ENTRADA
    // ==================================================
    private function validarCamposVentaEvento($eventoId, $productos, $desglose): array {
        $this->errores = [];

        // === VALIDAR EVENTO ===
        $r = $this->validarNumerico($eventoId, 'evento', 1, 11);
        if ($r !== true) {
            $this->errores['evento'] = $r;
        }

        // === VALIDAR PRODUCTOS ===
        if (!empty($productos) && is_array($productos)) {
            foreach ($productos as $i => $producto) {
                if (isset($producto['id_producto'])) {
                    $r = $this->validarNumerico($producto['id_producto'], "producto[$i]", 1, 11);
                    if ($r !== true) $this->errores["producto_$i"] = $r;
                }
                if (isset($producto['cantidad'])) {
                    $r = $this->validarNumerico($producto['cantidad'], "cantidad[$i]", 1, 11);
                    if ($r !== true) $this->errores["cantidad_$i"] = $r;
                }
                if (isset($producto['precio_unitario'])) {
                    $r = $this->validarDecimal($producto['precio_unitario'], "precio_unitario[$i]", 1, 10000000);
                    if ($r !== true) $this->errores["precio_unitario_$i"] = $r;
                }
            }
        } else {
            $this->errores['productos'] = 'Debe proporcionar un arreglo válido de productos.';
        }

        // === VALIDAR MÉTODO DE PAGO ===
        if (!empty($desglose) && is_array($desglose)) {
            foreach ($desglose as $i => $pago) {
                if (isset($pago['id_metodo_pago'])) {
                    $r = $this->validarNumerico($pago['id_metodo_pago'], "metodo_pago[$i]", 1, 11);
                    if ($r !== true) $this->errores["metodo_pago_$i"] = $r;
                }
            }
        } else {
            $this->errores['desglose'] = 'Debe incluir al menos un método de pago válido.';
        }

        // === RESULTADO FINAL ===
        if (!$this->sinErrores()) {
            return ['success' => false, 'errors' => $this->obtenerErrores()];
        }

        return ['success' => true];
    }

    // ==================================================
    // OBTENER VENTAS POR EMPRENDEDOR
    // ==================================================
    public function getVentasPorEmprendedor($cedula): array {
        try {
            /*$r = $this->validarNumerico($cedula, 'cedula', 7, 11);
            if ($r !== true) {
                return ['success' => false, 'message' => $r];
            }*/

            $sql = "
                SELECT 
                    e.id_eventos AS id_evento,
                    e.nombre AS nombre_evento,
                    e.fecha_inicio,
                    e.fecha_fin,
                    e.status AS status_evento,
                    e.direccion,
                    SUM(ve.cantidad * p.precio) AS monto_total
                FROM t_evento e
                JOIN t_venta_evento ve ON ve.fk_evento = e.id_eventos
                JOIN t_producto p ON p.id_producto = ve.fk_producto
                JOIN t_emprendedor emp ON emp.id_emprededor = p.fk_emprendedor
                WHERE emp.cedula = :cedula
                GROUP BY e.id_eventos, e.nombre, e.fecha_inicio, e.fecha_fin, e.status, e.direccion
            ";

            $stmt = $this->query($sql, [':cedula' => $cedula]);            
            $this->closeConnection();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en getVentasPorEmprendedor: " . $e->getMessage());
            $this->closeConnection();
            return ['success' => false, 'message' => 'Error al obtener las ventas por emprendedor.'];
        }
    }

    // ==================================================
    // DETALLE DE VENTAS POR EVENTO
    // ==================================================
    public function getDetalleVentasPorEvento($cedula, $id_evento): array {
        try {
            // --- Validaciones ---
            $r1 = $this->validarNumerico($cedula, 'cedula', 7, 11);
            $r2 = $this->validarNumerico($id_evento, 'id_evento', 1, 11);
            if ($r1 !== true) return ['success' => false, 'message' => $r1];
            if ($r2 !== true) return ['success' => false, 'message' => $r2];

            // --- Validar existencia del emprendedor ---
            $emp = $this->query(
                "SELECT id_emprededor FROM t_emprendedor WHERE cedula = :cedula",
                [':cedula' => $cedula]
            )->fetch(PDO::FETCH_ASSOC);

            if (!$emp) {
                $this->closeConnection();
                return ['success' => false, 'message' => "No existe un emprendedor con la cédula {$cedula}."];
            }

            // --- Validar existencia del evento ---
            $evento = $this->query(
                "SELECT id_eventos, nombre AS nombre_evento, direccion, fecha_inicio, fecha_fin 
                FROM t_evento WHERE id_eventos = :id",
                [':id' => $id_evento]
            )->fetch(PDO::FETCH_ASSOC);

            if (!$evento) {
                $this->closeConnection();
                return ['success' => false, 'message' => "El evento con ID {$id_evento} no existe."];
            }

            // --- Obtener detalle de productos vendidos ---
            $sql = "
                SELECT 
                    p.id_producto,
                    p.nombre AS nombre_producto,
                    p.precio,
                    ve.cantidad,
                    (ve.cantidad * p.precio) AS total_producto,
                    ve.id_venta_eventos
                FROM t_venta_evento ve
                JOIN t_producto p ON p.id_producto = ve.fk_producto
                JOIN t_emprendedor emp ON emp.id_emprededor = p.fk_emprendedor
                WHERE emp.cedula = :cedula AND ve.fk_evento = :evento
            ";

            $stmt = $this->query($sql, [':cedula' => $cedula, ':evento' => $id_evento]);
            $detalle = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->closeConnection();

            // --- Calcular total ---
            $monto_total = 0;
            foreach ($detalle as $d) {
                $monto_total += floatval($d['total_producto']);
            }

            // --- Agregar el monto total al objeto del evento ---
            $evento['monto_total'] = $monto_total;

            return [
                'success' => true,
                'message' => 'Detalle de ventas obtenido correctamente.',
                'evento' => $evento,
                'detalle_productos' => $detalle
            ];

        } catch (Exception $e) {
            error_log("Error en getDetalleVentasPorEvento: " . $e->getMessage());
            $this->closeConnection();
            return ['success' => false, 'message' => 'Error al obtener el detalle de ventas.'];
        }
    }


    // ==================================================
    // REGISTRAR VENTA DE EVENTO
    // ==================================================
    public function registrarVentaEvento($eventoId, $productos, $desglose): array {
        try {
            // === VALIDAR DATOS ===
            $validacion = $this->validarCamposVentaEvento($eventoId, $productos, $desglose);
            if (!$validacion['success']) return $validacion;

            $this->beginTransaction();

            // === VALIDAR EVENTO EXISTENTE ===
            $evento = $this->query("SELECT id_eventos FROM t_evento WHERE id_eventos = :id", [':id' => $eventoId])->fetch(PDO::FETCH_ASSOC);
            if (!$evento) {
                $this->rollBack();
                return ['success' => false, 'message' => "El evento con ID {$eventoId} no existe."];
            }

            // === VALIDAR PRODUCTOS ===
            foreach ($productos as $producto) {
                $info = $this->query("SELECT stock, nombre FROM t_producto WHERE id_producto = :id", [':id' => $producto['id_producto']])->fetch(PDO::FETCH_ASSOC);
                if (!$info) {
                    $this->rollBack();
                    return ['success' => false, 'message' => "El producto con ID {$producto['id_producto']} no existe."];
                }
                if ($info['stock'] < $producto['cantidad']) {
                    $this->rollBack();
                    return ['success' => false, 'message' => "Stock insuficiente para '{$info['nombre']}'."];
                }

                // Insertar venta
                $this->query("
                    INSERT INTO t_venta_evento (cantidad, monto, fk_evento, fk_producto, fk_metodo_pago)
                    VALUES (:cantidad, :monto, :evento, :producto, :metodo)
                ", [
                    ':cantidad' => $producto['cantidad'],
                    ':monto' => $producto['subtotal'],
                    ':evento' => $eventoId,
                    ':producto' => $producto['id_producto'],
                    ':metodo' => $desglose[0]['id_metodo_pago']
                ]);

                // Actualizar stock
                $this->query("UPDATE t_producto SET stock = stock - :cantidad WHERE id_producto = :id", [
                    ':cantidad' => $producto['cantidad'],
                    ':id' => $producto['id_producto']
                ]);
            }

            $this->commit();
            $this->closeConnection();

            return ['success' => true, 'message' => 'Venta registrada correctamente.'];
        } catch (Exception $e) {
            $this->rollBack();
            error_log("Error en registrarVentaEvento: " . $e->getMessage());
            $this->closeConnection();
            return ['success' => false, 'message' => 'Error al registrar la venta.'];
        }
    }
}
