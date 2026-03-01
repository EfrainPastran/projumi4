<?php
use PHPUnit\Framework\TestCase;
use App\Models\ProductosModel;

class TestMostrarProductos extends TestCase
{
    private $producto;
    private $db;

    /**
     * Configuración inicial antes de cada prueba
     */
    protected function setUp(): void
    {
        if (!defined('BD_PROJUMI')) define('BD_PROJUMI', 'projumi');
        if (!defined('BD_SEGURIDAD')) define('BD_SEGURIDAD', 'seguridad');
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

    /**
     * Caso exitoso: obtener lista de productos con datos válidos
     */
    public function testObtenerProductosExitoso()
    {
        $resultado = $this->producto->getProductos();

        $this->assertIsArray($resultado, "El resultado debe ser un array");
        if (!empty($resultado)) {
            $primer = $resultado[0];
            $this->assertArrayHasKey('id_producto', $primer);
            $this->assertArrayHasKey('nombre', $primer);
            $this->assertArrayHasKey('precio', $primer);
            $this->assertArrayHasKey('categoria', $primer);
            $this->assertArrayHasKey('imagenes', $primer);
        }
    }

    // ========================================
    // CASOS SIN DATOS
    // ========================================

    /**
     * Caso: No hay productos registrados en la base de datos
     */
    public function testSinProductosRegistrados()
    {
        // Simulamos tabla vacía temporalmente
        $this->db->exec("SET FOREIGN_KEY_CHECKS=0");
        $this->db->exec("TRUNCATE TABLE t_producto");
        $this->db->exec("SET FOREIGN_KEY_CHECKS=1");

        $resultado = $this->producto->getProductos();

        $this->assertIsArray($resultado);
        $this->assertEmpty($resultado, "Debe retornar un array vacío si no hay productos");
    }

    // ========================================
    // CASOS DE ERROR / EXCEPCIÓN
    // ========================================

    /**
     * Caso: Error en la consulta (simulación)
     */
    public function testErrorEnConsulta()
    {
        // Creamos un mock del modelo que lanza una excepción al ejecutar query()
        $mockProducto = $this->getMockBuilder(ProductosModel::class)
                             ->onlyMethods(['query'])
                             ->getMock();

        $mockProducto->method('query')
                     ->will($this->throwException(new Exception('Error simulado de base de datos')));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Error simulado de base de datos');
        
        $mockProducto->getProductos();
    }

    // ========================================
    // CASOS DE VALIDACIÓN DE DATOS DEVUELTOS
    // ========================================

    /**
     * Caso: Validar que cada producto tenga estructura esperada
     */
    public function testEstructuraDeProducto()
    {
        $productos = $this->producto->getProductos();

        $this->assertIsArray($productos, "Debe retornar un array");

        if (empty($productos)) {
            $this->assertEmpty($productos, "Debe retornar array vacío si no hay productos");
            return;
        }

        foreach ($productos as $p) {
            $this->assertArrayHasKey('id_producto', $p);
            $this->assertArrayHasKey('nombre', $p);
            $this->assertArrayHasKey('precio', $p);
            $this->assertArrayHasKey('stock', $p);
            $this->assertArrayHasKey('categoria', $p);
            $this->assertArrayHasKey('status', $p);
        }
    }

}
