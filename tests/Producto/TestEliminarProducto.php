<?php
use PHPUnit\Framework\TestCase;
use App\Models\ProductosModel;

class TestEliminarProducto extends TestCase
{
    private $producto;
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

        $this->db = new PDO(DB_DSN_PROJUMI, DB_USER, DB_PASS, DB_OPTIONS);
        $this->producto = new ProductosModel();
    }

    // ========================================
    // CASOS DE ÉXITO
    // ========================================

    public function testEliminarProductoExitoso()
    {
        // Crear un producto temporal para eliminar
        $nombreTemp = "Producto Temporal " . rand(1000, 9999);
        $this->producto->setData(null, $nombreTemp, 20.0, 5, "Producto de prueba para eliminar", 1, 1, 1);
        $resInsert = $this->producto->registerProduc();

        $this->assertTrue($resInsert['success']);
        $idProducto = $resInsert['id_producto'];

        // Intentar eliminarlo
        $this->producto->set_id_producto($idProducto);
        $res = $this->producto->eliminarProducto();

        $this->assertIsArray($res);
        $this->assertTrue($res['success']);
        $this->assertStringContainsString('eliminado', strtolower($res['message']));
    }

    // ========================================
    // VALIDACIONES DE EXISTENCIA
    // ========================================

    public function testProductoInexistente()
    {
        $this->producto->set_id_producto(999999);
        $res = $this->producto->eliminarProducto();

        $this->assertFalse($res['success']);
        $this->assertStringContainsString('no existe', strtolower($res['message']));
    }

    // ========================================
    // VALIDACIONES RELACIONALES
    // ========================================

    public function testProductoEnPedido()
    {
        $idProducto = 23;

        $this->producto->set_id_producto($idProducto);
        $res = $this->producto->eliminarProducto();

        $this->assertFalse($res['success']);
        $this->assertStringContainsString('pedido', strtolower($res['message']));
    }

    // ========================================
    // ERRORES DE BASE DE DATOS / EXCEPCIONES
    // ========================================

    /**
     * Caso: Se intenta eliminar con un ID inválido (simulación de error de validación)
     */
    public function testErrorTransaccionalSimulado()
    {
        $this->producto->set_id_producto("INVALIDO_@@");
        $res = $this->producto->eliminarProducto();

        $this->assertIsArray($res);
        $this->assertFalse($res['success']);
        // El mensaje debería contener "id" o "válido"
        $this->assertThat(
            strtolower($res['message']),
            $this->logicalOr(
                $this->stringContains('id'),
                $this->stringContains('válido')
            )
        );
    }
}
