<?php
namespace App\Models;
use PDO;
use PDOException;
use Exception;
use App\Model;
class DetallePedidoModel extends Model {
    protected $db;
    private $pedidoId;
    private $productoId;
    private $cantidad;
    private $precioUnitario;

    public function __construct() {
        try {
            $this->db = new PDO(DB_DSN_PROJUMI, DB_USER, DB_PASS, DB_OPTIONS);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log('Error de conexión: ' . $e->getMessage());
            throw new Exception('Error al conectar con la base de datos');
        }
    }
    

    public function setPedidoId($id) {
        $this->pedidoId = $id;
    }

    public function setProductoId($id) {
        $this->productoId = $id;
    }

    public function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }

    public function setPrecioUnitario($precio) {
        $this->precioUnitario = $precio;
    }
    public function guardar() {
        try {
            $stmt = $this->db->prepare("INSERT INTO t_detalle_pedido (producto_ID_PRODUCTO, pedidos_ID_PEDIDO, cantidad, precio_unitario) 
                                        VALUES (:producto_id, :pedido_id, :cantidad, :precio)");
            $stmt->execute([
                ':producto_id' => $this->productoId,
                ':pedido_id' => $this->pedidoId,
                ':cantidad' => $this->cantidad,
                ':precio' => $this->precioUnitario
            ]);
        } catch (PDOException $e) {
            error_log('Error al guardar detalle de pedido: ' . $e->getMessage());
            throw new Exception('No se pudo guardar el detalle del pedido');
        }
    }
    

    public function verificarYActualizarStock() {
        try {
            $sql = "SELECT stock FROM t_producto WHERE id_producto = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $this->productoId]);
            $stockActual = $stmt->fetchColumn();
    
            if ($stockActual === false || $stockActual < $this->cantidad) {
                throw new Exception("Stock insuficiente para el producto ID {$this->productoId}");
            }
    
            $sqlUpdate = "UPDATE t_producto SET stock = stock - :cantidad WHERE id_producto = :id";
            $stmt = $this->db->prepare($sqlUpdate);
            $stmt->execute([
                ':cantidad' => $this->cantidad,
                ':id' => $this->productoId
            ]);
        } catch (PDOException $e) {
            error_log('Error al verificar/actualizar stock: ' . $e->getMessage());
            throw new Exception('Error al verificar o actualizar el stock');
        }
    }
    
}
