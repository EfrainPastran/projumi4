<?php
use PHPUnit\Framework\TestCase;
use App\Models\pagosModel;

class TestAprobarPago extends TestCase
{
    private $pago;
    private $db;

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

        $this->db = new PDO(DB_DSN_PROJUMI, DB_USER, DB_PASS, DB_OPTIONS);
        $this->pago = new pagosModel();
    }

    /** Caso exitoso: aprobar un pago existente */
    public function testAprobarPagoExistente()
    {
        $resultado = $this->pago->aprobar('Aprobado', 24, 24);
        $this->assertIsArray($resultado);
        $this->assertTrue($resultado['success']);
        $this->assertStringContainsString('Pago Aprobado', $resultado['message']);
    }

    /** Caso: ID de pago inexistente */
    public function testPagoNoExiste()
    {
        $resultado = $this->pago->aprobar('Aprobado', 9999, 1);
        $this->assertIsArray($resultado);
        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('no existe', strtolower($resultado['message']));
    }

    /** Caso: ID de pedido inexistente */
    public function testPedidoNoExiste()
    {
        $resultado = $this->pago->aprobar('Aprobado', 24, 9999);
        $this->assertIsArray($resultado);
        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('pedido asociado no existe', strtolower($resultado['message']));
    }

    /** Caso: estatus inválido */
    public function testEstatusInvalido()
    {
        $resultado = $this->pago->aprobar('Cancelado', 1, 1);
        $this->assertIsArray($resultado);
        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('estatus de pago inválido', strtolower($resultado['message']));
    }

    /** Caso: campos incompletos */
    public function testCamposIncompletos()
    {
        $resultado = $this->pago->aprobar('', '', '');
        $this->assertIsArray($resultado);
        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('faltan campos requeridos', strtolower($resultado['message']));
    }
}
