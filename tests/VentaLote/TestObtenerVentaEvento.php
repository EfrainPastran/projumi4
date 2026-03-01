<?php
use PHPUnit\Framework\TestCase;
use App\Models\VentaEventoModel;

class TestObtenerVentaEvento extends TestCase
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

    public function testCedulaVacia()
    {
        $res = $this->ventaModel->getDetalleVentasPorEvento('', 1);
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('cédula', strtolower($res['message']));
    }

    public function testEventoNoNumerico()
    {
        $res = $this->ventaModel->getDetalleVentasPorEvento(12345, 'abc');
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('evento', strtolower($res['message']));
    }

    public function testEmprendedorInexistente()
    {
        $res = $this->ventaModel->getDetalleVentasPorEvento(999999, 1);
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('no existe un emprendedor', strtolower($res['message']));
    }

    public function testEventoInexistente()
    {
        $res = $this->ventaModel->getDetalleVentasPorEvento(12345, 9999);
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('no existe', strtolower($res['message']));
    }

    public function testEventoSinVentas()
    {
        $res = $this->ventaModel->getDetalleVentasPorEvento(27123456, 1);
        $this->assertTrue($res['success']);
        $this->assertEmpty($res['data']);
    }
}
