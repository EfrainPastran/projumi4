<?php
use PHPUnit\Framework\TestCase;
use App\Models\PedidoModel;

class TestObtenerVenta extends TestCase
{
    private $db;
    private $pedidoModel;

    protected function setUp(): void
    {
        if (!defined('DB_DSN_PROJUMI')) {
            define('DB_DSN_PROJUMI', 'mysql:host=localhost;dbname=projumi;charset=utf8');
            define('DB_USER', 'root');
            define('DB_PASS', '');
            define('DB_OPTIONS', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        }

        if (!defined('BD_SEGURIDAD')) {
            define('BD_SEGURIDAD', 'seguridad');
        }

        $this->db = new PDO(DB_DSN_PROJUMI, DB_USER, DB_PASS, DB_OPTIONS);

        $this->pedidoModel = new PedidoModel($this->db);
    }

    // ========================================
    // CASOS DE ÉXITO
    // ========================================

    public function testObtenerPedidoExistente()
    {
        $idPedidoExistente = 24; 

        $resultado = $this->pedidoModel->obtenerPedido($idPedidoExistente);

        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('id_pedidos', $resultado);
        $this->assertArrayHasKey('cliente_nombre', $resultado);
        $this->assertArrayHasKey('correo', $resultado);
        $this->assertNotEmpty($resultado['cliente_nombre']);
    }

    public function testObtenerDetallePedidoExistente()
    {
        $idPedidoExistente = 24;

        $detalle = $this->pedidoModel->obtenerDetallePedido($idPedidoExistente);

        $this->assertIsArray($detalle);
        $this->assertNotEmpty($detalle);
        $this->assertArrayHasKey('nombre_producto', $detalle[0]);
        $this->assertArrayHasKey('cantidad', $detalle[0]);
    }

    // ========================================
    // CASOS DE ERROR / VALIDACIÓN
    // ========================================

    public function testObtenerPedidoInexistente()
    {
        $idInexistente = 999999;
        $resultado = $this->pedidoModel->obtenerPedido($idInexistente);
        $this->assertNull($resultado);
    }

    public function testObtenerPedidoConIdInvalido()
    {
        $resultado = $this->pedidoModel->obtenerPedido('abc');
        $this->assertNull($resultado);
    }

    public function testObtenerDetallePedidoSinProductos()
    {
        $idPedidoSinDetalle = 2; // Asegúrate de tener un pedido sin productos
        $detalle = $this->pedidoModel->obtenerDetallePedido($idPedidoSinDetalle);

        $this->assertIsArray($detalle);
        $this->assertCount(0, $detalle);
    }

    public function testObtenerDetallePedidoConIdInvalido()
    {
        $detalle = $this->pedidoModel->obtenerDetallePedido(null);
        $this->assertIsArray($detalle);
        $this->assertCount(0, $detalle);
    }
}
