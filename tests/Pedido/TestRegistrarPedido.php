<?php
use PHPUnit\Framework\TestCase;
use App\Models\PedidoModel;

class TestRegistrarPedido extends TestCase
{
    private $pedido;
    private $db;

    /**
     * Configuración inicial antes de cada prueba
     */
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
        $this->pedido = new PedidoModel();
    }

    // ======================================================
    // CASO DE ÉXITO
    // ======================================================

    public function testRegistrarPedidoExitoso()
    {
        $this->pedido->set_fk_cliente(25);
        $this->pedido->set_estatus('En proceso');

        $detallePedido = [
            'detalle' => [
                ['id' => 23, 'cantidad' => 1, 'precio' => 25.00]
            ]
        ];

        $detalleEnvio = [
            'modoEntrega' => 'presencial'
        ];

        $detallePago = [
            'detalles' => [
                [
                    'fk_detalle_metodo_pago' => 1,
                    'monto' => 25.00,
                    'referencia' => 'ABC123',
                    'comprobante' => 'public/comprobantes/test.jpg'
                ]
            ]
        ];

        $resultado = $this->pedido->registrarPedidoCompleto($detallePedido, $detalleEnvio, $detallePago);

        $this->assertIsArray($resultado);
        $this->assertTrue($resultado['success']);
        $this->assertArrayHasKey('pedido_id', $resultado);
    }

    // ======================================================
    // VALIDACIONES BÁSICAS
    // ======================================================

    public function testClienteInvalido()
    {
        $this->pedido->set_fk_cliente(null);

        $res = $this->pedido->registrarPedidoCompleto([], [], []);
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('cliente', strtolower($res['message']));
    }

    public function testDetallePedidoVacio()
    {
        $this->pedido->set_fk_cliente(1);
        $res = $this->pedido->registrarPedidoCompleto([], ['modoEntrega' => 'presencial'], []);
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('detalle', strtolower($res['message']));
    }

    public function testModoEntregaFaltante()
    {
        $this->pedido->set_fk_cliente(1);
        $res = $this->pedido->registrarPedidoCompleto(['detalle' => [['id' => 1, 'cantidad' => 1, 'precio' => 10]]], [], []);
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('modo de entrega', strtolower($res['message']));
    }

    public function testDetallePagoFaltante()
    {
        $this->pedido->set_fk_cliente(1);
        $res = $this->pedido->registrarPedidoCompleto(['detalle' => [['id' => 1, 'cantidad' => 1, 'precio' => 10]]], ['modoEntrega' => 'presencial'], []);
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('pago', strtolower($res['message']));
    }

    // ======================================================
    // VALIDACIONES RELACIONALES
    // ======================================================

    public function testClienteNoExiste()
    {
        $this->pedido->set_fk_cliente(99999);
        $detallePedido = [
            'detalle' => [['id' => 1, 'cantidad' => 1, 'precio' => 10]]
        ];
        $detalleEnvio = ['modoEntrega' => 'presencial'];
        $detallePago  = ['detalles' => [['fk_detalle_metodo_pago' => 1, 'monto' => 10, 'referencia' => '123']]];

        $res = $this->pedido->registrarPedidoCompleto($detallePedido, $detalleEnvio, $detallePago);
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('cliente no existe', strtolower($res['message']));
    }

    public function testProductoNoExiste()
    {
        $this->pedido->set_fk_cliente(25);
        $detallePedido = [
            'detalle' => [['id' => 9999, 'cantidad' => 1, 'precio' => 10]]
        ];
        $detalleEnvio = ['modoEntrega' => 'presencial'];
        $detallePago  = ['detalles' => [['fk_detalle_metodo_pago' => 1, 'monto' => 10, 'referencia' => 'ABC']]];

        $res = $this->pedido->registrarPedidoCompleto($detallePedido, $detalleEnvio, $detallePago);
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('no encontrado', strtolower($res['message']));
    }

    public function testStockInsuficiente()
    {
        $this->pedido->set_fk_cliente(25);
        $detallePedido = [
            'detalle' => [['id' => 23, 'cantidad' => 99999, 'precio' => 10]]
        ];
        $detalleEnvio = ['modoEntrega' => 'presencial'];
        $detallePago  = ['detalles' => [['fk_detalle_metodo_pago' => 1, 'monto' => 10, 'referencia' => 'ABC']]];

        $res = $this->pedido->registrarPedidoCompleto($detallePedido, $detalleEnvio, $detallePago);
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('stock insuficiente', strtolower($res['message']));
    }

    // ======================================================
    // VALIDACIONES DE MODO DE ENTREGA
    // ======================================================

    public function testModoEntregaInvalido()
    {
        $this->pedido->set_fk_cliente(25);
        $detallePedido = ['detalle' => [['id' => 23, 'cantidad' => 1, 'precio' => 10]]];
        $detalleEnvio = ['modoEntrega' => 'dron'];
        $detallePago = ['detalles' => [['fk_detalle_metodo_pago' => 1, 'monto' => 10, 'referencia' => '123']]];

        $res = $this->pedido->registrarPedidoCompleto($detallePedido, $detalleEnvio, $detallePago);
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('modo de entrega no válido', strtolower($res['message']));
    }

    public function testModoDeliveryFaltanCampos()
    {
        $this->pedido->set_fk_cliente(25);
        $detallePedido = ['detalle' => [['id' => 23, 'cantidad' => 1, 'precio' => 10]]];
        $detalleEnvio = ['modoEntrega' => 'delivery']; // faltan campos requeridos
        $detallePago = ['detalles' => [['fk_detalle_metodo_pago' => 1, 'monto' => 10, 'referencia' => 'XYZ']]];

        $res = $this->pedido->registrarPedidoCompleto($detallePedido, $detalleEnvio, $detallePago);
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('falta el campo', strtolower($res['message']));
    }

    public function testModoEnvioNacionalFaltanDatos()
    {
        $this->pedido->set_fk_cliente(25);
        $detallePedido = ['detalle' => [['id' => 23, 'cantidad' => 1, 'precio' => 10]]];
        $detalleEnvio = ['modoEntrega' => 'envio nacional']; // sin empresa ni dirección
        $detallePago = ['detalles' => [['fk_detalle_metodo_pago' => 1, 'monto' => 10, 'referencia' => 'XYZ']]];

        $res = $this->pedido->registrarPedidoCompleto($detallePedido, $detalleEnvio, $detallePago);
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('faltan datos', strtolower($res['message']));
    }

    // ======================================================
    // VALIDACIONES DE PAGO
    // ======================================================

    public function testPagoInvalido()
    {
        $this->pedido->set_fk_cliente(25);
        $detallePedido = ['detalle' => [['id' => 23, 'cantidad' => 1, 'precio' => 10]]];
        $detalleEnvio = ['modoEntrega' => 'presencial'];
        $detallePago = ['detalles' => [['fk_detalle_metodo_pago' => '', 'monto' => '', 'referencia' => '']]];

        $res = $this->pedido->registrarPedidoCompleto($detallePedido, $detalleEnvio, $detallePago);
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('pago', strtolower($res['message']));
    }
}
