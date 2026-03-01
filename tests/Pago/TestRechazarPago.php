<?php
use PHPUnit\Framework\TestCase;
use App\Models\pagosModel;

class TestRechazarPago extends TestCase
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

    /** Caso exitoso: pago rechazado correctamente */
    public function testRechazarPagoExistente()
    {
        $resultado = $this->pago->rechazar('Rechazado', 24, 24);

        $this->assertIsArray($resultado);
        $this->assertTrue($resultado['success']);
        $this->assertStringContainsString('pago rechazado', strtolower($resultado['message']));
    }

    /** Caso: pago no existe */
    public function testRechazarPagoInexistente()
    {
        $resultado = $this->pago->rechazar('Rechazado', 9999, 24);

        $this->assertIsArray($resultado);
        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('no existe', strtolower($resultado['message']));
    }

    /** Caso: pedido no existe */
    public function testRechazarPedidoInexistente()
    {
        $resultado = $this->pago->rechazar('Rechazado', 24, 9999);

        $this->assertIsArray($resultado);
        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('pedido asociado no existe', strtolower($resultado['message']));
    }

    /** Caso: estatus inválido */
    public function testEstatusInvalido()
    {
        $resultado = $this->pago->rechazar('Cancelado', 1, 1);

        $this->assertIsArray($resultado);
        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('estatus de pago inválido', strtolower($resultado['message']));
    }

    /** Caso: campos incompletos */
    public function testCamposIncompletos()
    {
        $resultado = $this->pago->rechazar('', '', '');

        $this->assertIsArray($resultado);
        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('faltan campos requeridos', strtolower($resultado['message']));
    }
}
