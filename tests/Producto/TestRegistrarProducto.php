<?php
use PHPUnit\Framework\TestCase;
use App\Models\ProductosModel;

class TestRegistrarProducto extends TestCase
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

    public function testRegistrarProductoExitoso()
    {
        $this->producto->setData(
            null,
            "Producto Test " . rand(1, 10000),
            50.00,
            10,
            "Producto de prueba con datos válidos",
            1,
            1, // fk_categoria válida
            1  // fk_emprendedor válido
        );

        $resultado = $this->producto->registerProduc([
            "imagenes/producto1.jpg",
            "imagenes/producto2.jpg"
        ]);

        $this->assertIsArray($resultado);
        $this->assertTrue($resultado['success']);
        $this->assertArrayHasKey('id_producto', $resultado);
    }

    // ========================================
    // VALIDACIONES DE CAMPOS BÁSICOS
    // ========================================

    public function testNombreInvalido()
    {
        $this->producto->setData(null, "a", 20, 10, "desc", 1, 1, 1);
        $res = $this->producto->registerProduc();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('nombre', strtolower($res['message']));
    }

    public function testPrecioInvalido()
    {
        $this->producto->setData(null, "Zapatos", -10, 10, "desc", 1, 1, 1);
        $res = $this->producto->registerProduc();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('precio', strtolower($res['message']));
    }

    public function testStockInvalido()
    {
        $this->producto->setData(null, "Camisa", 20, -5, "desc", 1, 1, 1);
        $res = $this->producto->registerProduc();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('stock', strtolower($res['message']));
    }

    public function testDescripcionVacia()
    {
        $this->producto->setData(null, "Camisa", 20, 5, "", 1, 1, 1);
        $res = $this->producto->registerProduc();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('descripción', strtolower($res['message']));
    }

    public function testStatusInvalido()
    {
        $this->producto->setData(null, "Camisa", 20, 5, "desc", 2, 1, 1);
        $res = $this->producto->registerProduc();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('estado', strtolower($res['message']));
    }

    // ========================================
    // VALIDACIONES RELACIONALES
    // ========================================

    public function testCategoriaInvalida()
    {
        $this->producto->setData(null, "Producto sin categoría", 10, 5, "desc", 1, 9999, 1);
        $res = $this->producto->registerProduc();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('categoría', strtolower($res['message']));
    }

    public function testEmprendedorInvalido()
    {
        $this->producto->setData(null, "Producto sin emprendedor", 10, 5, "desc", 1, 1, 9999);
        $res = $this->producto->registerProduc();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('emprendedor', strtolower($res['message']));
    }

    // ========================================
    // VALIDACIONES DE DUPLICADOS
    // ========================================

    public function testNombreDuplicado()
    {
        // Primero insertar uno válido
        $nombreDuplicado = "Producto Duplicado " . rand(1, 1000);
        $this->producto->setData(null, $nombreDuplicado, 10, 5, "desc", 1, 1, 1);
        $this->producto->registerProduc();

        // Intentar registrar otro con el mismo nombre y emprendedor
        $this->producto->setData(null, $nombreDuplicado, 10, 5, "desc", 1, 1, 1);
        $res = $this->producto->registerProduc();

        $this->assertFalse($res['success']);
        $this->assertStringContainsString('ya existe', strtolower($res['message']));
    }

    // ========================================
    // VALIDACIONES DE IMÁGENES
    // ========================================

    public function testImagenRutaInvalida()
    {
        $this->producto->setData(null, "Producto Imagen", 20, 5, "desc", 1, 1, 1);
        $res = $this->producto->registerProduc([123, null, ""]);
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('imagen', strtolower($res['message']));
    }
}
