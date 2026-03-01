<?php

use App\Models\PedidoModel;
use PHPUnit\Framework\TestCase;

class TestObtenerPedido extends TestCase
{
    private $pedido;
    private $db;

    /**
     * Configuración inicial antes de cada prueba
     */
    protected function setUp(): void
    {
        if (!defined('BD_SEGURIDAD')) {
            define('BD_SEGURIDAD', 'seguridad');
        }

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
        $this->pedido = new PedidoModel($this->db);
    }

    // ========================================
    // CASO DE ÉXITO
    // ========================================

    public function testConsultarPedidoExitoso()
    {        
        $idPedidoExistente = 24;

        $resultado = $this->pedido->consultarPedidoCompleto($idPedidoExistente);

        $this->assertIsArray($resultado);
        $this->assertTrue($resultado['success']);
        $this->assertArrayHasKey('pedido', $resultado);
        $this->assertArrayHasKey('detalle', $resultado);

        $this->assertArrayHasKey('total', $resultado['pedido']);
        $this->assertGreaterThan(0, $resultado['pedido']['total']);
    }

    // ========================================
    // VALIDACIONES DE ID DE PEDIDO
    // ========================================

    public function testIdPedidoVacio()
    {
        $resultado = $this->pedido->consultarPedidoCompleto('');
        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('obligatorio', strtolower($resultado['message']));
    }

    public function testIdPedidoInvalido()
    {
        $resultado = $this->pedido->consultarPedidoCompleto('abc');
        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('número positivo', strtolower($resultado['message']));
    }

    // ========================================
    // PEDIDO INEXISTENTE
    // ========================================

    public function testPedidoNoExiste()
    {
        $resultado = $this->pedido->consultarPedidoCompleto(999999);
        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('no existe', strtolower($resultado['message']));
    }
}
