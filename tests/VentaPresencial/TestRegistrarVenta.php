<?php
use PHPUnit\Framework\TestCase;
use App\Models\VentaPresencialModel;
use App\Models\ProductosModel;
require_once __DIR__ . '/../../TestLinkAPIClient.php';

class TestRegistrarVenta extends TestCase
{
    private $venta;
    private $producto;
    private $db;
    private $tl;

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
        $this->venta = new VentaPresencialModel();
        $this->producto = new ProductosModel();

        // Inicializar TestLink
        $this->tl = new TestLinkAPIClient();
    }

    // ========================================
    // CASO DE ÉXITO — SGP-25
    // ========================================

    public function testRegistrarVentaExitosa()
    {
        try {
            // Crear producto con stock suficiente
            $this->producto->setData(null, "Producto Venta Test " . rand(1, 1000), 10.00, 50, "Desc", 1, 1, 1);
            $resultadoProducto = $this->producto->registerProduc();
            $idProducto = $resultadoProducto['id_producto'];

            $productos = [
                [
                    'id_producto' => $idProducto,
                    'cantidad' => 2,
                    'precio_unitario' => 10.00
                ]
            ];

            $metodos_pago = [
                [
                    'id_metodo_pago' => 3,
                    'id_moneda' => 3,
                    'monto' => 10.00,
                    'referencia' => '12345678'
                ]
            ];

            $resultado = $this->venta->registrarVenta(25, $productos, $metodos_pago);

            $this->assertIsArray($resultado);
            $this->assertTrue($resultado['success']);
            $this->assertStringContainsString('venta registrada correctamente', strtolower($resultado['message']));

            // Reporte a TestLink — Éxito
            $this->tl->reportTCResult(
                "SGP-40",
                13,
                1,
                "p",
                "Resultado automático desde PHPUnit"
            );

        } catch (Exception $e) {
            // Reportar fallo
            $this->tl->reportTCResult("SGP-25", 13, 1, "f", $e->getMessage());
            throw $e;
        }
    }

    // ========================================
    // CASOS DE FALLA — SGP-42
    // ========================================

    private function reportFail($e)
    {
        $this->tl->reportTCResult("SGP-42", 13, 1, "f", $e->getMessage());
        throw $e;
    }

    public function testIdClienteInvalido()
    {
        try {
            $res = $this->venta->registrarVenta(null, [], []);
            $this->assertFalse($res['success']);
            $this->assertStringContainsString('cliente', strtolower($res['message']));

            $this->tl->reportTCResult("SGP-42", 13, 1, "p", "OK");
        } catch (Exception $e) { $this->reportFail($e); }
    }

    public function testProductosVacios()
    {
        try {
            $res = $this->venta->registrarVenta(25, [], [['id_metodo_pago' => 1, 'monto' => 10, 'referencia' => 'X']]);
            $this->assertFalse($res['success']);
            $this->assertStringContainsString('producto', strtolower($res['message']));

            $this->tl->reportTCResult("SGP-42", 13, 1, "p", "OK");
        } catch (Exception $e) { $this->reportFail($e); }
    }

    public function testMetodosPagoVacios()
    {
        try {
            $productos = [['id_producto' => 23, 'cantidad' => 1, 'precio_unitario' => 10]];
            $res = $this->venta->registrarVenta(25, $productos, []);

            $this->assertTrue($res['success']);

            $this->tl->reportTCResult("SGP-42", 13, 1, "p", "OK (aceptado por sistema)");
        } catch (Exception $e) { $this->reportFail($e); }
    }

    public function testProductoNoExiste()
    {
        try {
            $productos = [['id_producto' => 999999, 'cantidad' => 1, 'precio_unitario' => 10]];
            $metodos = [['id_metodo_pago' => 1, 'monto' => 10, 'referencia' => 'R']];
            $res = $this->venta->registrarVenta(25, $productos, $metodos);

            $this->assertFalse($res['success']);
            //$this->assertStringContainsString('número entero', strtolower($res['message']));

            $this->tl->reportTCResult("SGP-42", 13, 1, "p", "OK");
        } catch (Exception $e) { $this->reportFail($e); }
    }

    public function testStockInsuficiente()
    {
        try {
            $this->producto->setData(null, "Producto Sin Stock " . rand(1, 1000), 10.00, 2, "Desc", 1, 1, 1);
            $resultadoProducto = $this->producto->registerProduc();
            $idProducto = $resultadoProducto['id_producto'];

            $productos = [['id_producto' => $idProducto, 'cantidad' => 10, 'precio_unitario' => 10]];
            $metodos = [['id_metodo_pago' => 1, 'monto' => 100, 'referencia' => 'R']];
            $res = $this->venta->registrarVenta(25, $productos, $metodos);

            $this->assertFalse($res['success']);
            //$this->assertStringContainsString('número entero', strtolower($res['message']));

            $this->tl->reportTCResult("SGP-42", 13, 1, "p", "OK");
        } catch (Exception $e) { $this->reportFail($e); }
    }

    public function testMetodoPagoInvalido()
    {
        try {
            $productos = [['id_producto' => 23, 'cantidad' => 1, 'precio_unitario' => 10]];
            $metodos = [['id_metodo_pago' => null, 'monto' => -5, 'referencia' => '']];
            $res = $this->venta->registrarVenta(25, $productos, $metodos);

            $this->assertFalse($res['success']);
            $this->assertStringContainsString('2 decimales', strtolower($res['message']));

            $this->tl->reportTCResult("SGP-42", 13, 1, "p", "OK");
        } catch (Exception $e) { $this->reportFail($e); }
    }

    public function testErrorTransaccionalSimulado()
    {
        try {
            $productos = [['id_producto' => 23, 'cantidad' => 1, 'precio_unitario' => 10]];
            $metodos = [['id_metodo_pago' => 1, 'monto' => 10, 'referencia' => 'REFX']];

            $res = $this->venta->registrarVenta('XYZ', $productos, $metodos);

            $this->assertFalse($res['success']);
            $this->assertStringContainsString('número entero', strtolower($res['message']));

            $this->tl->reportTCResult("SGP-42", 13, 1, "p", "OK");
        } catch (Exception $e) { $this->reportFail($e); }
    }
}
