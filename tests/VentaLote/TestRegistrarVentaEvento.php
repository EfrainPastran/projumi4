<?php
use PHPUnit\Framework\TestCase;
use App\Models\VentaEventoModel;

class TestRegistrarVentaEvento extends TestCase
{
    private $db;
    private $ventaModel;

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
        $this->ventaModel = new VentaEventoModel($this->db);
    }

    // === CASOS DE VALIDACIÓN ===

    public function testEventoInvalido()
    {
        $res = $this->ventaModel->registrarVentaEvento(null, [], []);
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('evento', strtolower($res['message']));
    }

    public function testProductosEstructuraInvalida()
    {
        $res = $this->ventaModel->registrarVentaEvento(1, ['sin_estructura'], [['id_metodo_pago' => 1]]);
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('estructura de producto inválida', strtolower($res['message']));
    }

    public function testMetodoPagoInvalido()
    {
        $res = $this->ventaModel->registrarVentaEvento(1, [['id_producto' => 1, 'cantidad' => 1, 'precio_unitario' => 10, 'subtotal' => 10]], []);
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('pago', strtolower($res['message']));
    }

    public function testProductoInexistente()
    {
        $res = $this->ventaModel->registrarVentaEvento(1, [['id_producto' => 9999, 'cantidad' => 1, 'precio_unitario' => 10, 'subtotal' => 10]], [['id_metodo_pago' => 1]]);
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('producto no encontrado', strtolower($res['message']));
    }

    public function testStockInsuficiente()
    {
        $res = $this->ventaModel->registrarVentaEvento(1, [['id_producto' => 23, 'cantidad' => 9999, 'precio_unitario' => 10, 'subtotal' => 10]], [['id_metodo_pago' => 1]]);
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('stock insuficiente', strtolower($res['message']));
    }
    public function testRegistrarVentaEventoExitoso()
    {
        $eventoId = 1; 
        $productoId = 23; 
        $metodoPagoId = 1; 

        $productos = [
            [
                'id_producto' => $productoId,
                'cantidad' => 1,
                'precio_unitario' => 2,
                'subtotal' => 2
            ]
        ];

        $desglose = [
            [
                'id_metodo_pago' => $metodoPagoId,
                'monto' => 10,
                'referencia' => 'TEST123'
            ]
        ];

        $res = $this->ventaModel->registrarVentaEvento($eventoId, $productos, $desglose);

        $this->assertIsArray($res);
        $this->assertTrue($res['success']);
        $this->assertStringContainsString('venta registrada correctamente', strtolower($res['message']));
    }

}
?>