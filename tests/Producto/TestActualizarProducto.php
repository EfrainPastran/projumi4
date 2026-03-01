<?php
use PHPUnit\Framework\TestCase;
use App\Models\ProductosModel;

class TestActualizarProducto extends TestCase
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

    public function testActualizarProductoExitoso()
    {
        $this->producto->setData(
            23, // id_producto existente
            "Producto Actualizado " . rand(1, 10000),
            75.00,
            15,
            "Producto actualizado correctamente",
            1,
            1, // fk_categoria válida
            1  // fk_emprendedor válido
        );

        $resultado = $this->producto->updateProduc([
            "imagenes/producto_actualizado1.jpg",
            "imagenes/producto_actualizado2.jpg"
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
        $this->producto->setData(23, "a", 20, 10, "desc", 1, 1, 1);
        $res = $this->producto->updateProduc();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('nombre', strtolower($res['message']));
    }

    public function testPrecioInvalido()
    {
        $this->producto->setData(23, "Zapatos", -10, 10, "desc", 1, 1, 1);
        $res = $this->producto->updateProduc();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('precio', strtolower($res['message']));
    }

    public function testStockInvalido()
    {
        $this->producto->setData(23, "Camisa", 20, -5, "desc", 1, 1, 1);
        $res = $this->producto->updateProduc();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('stock', strtolower($res['message']));
    }

    public function testDescripcionVacia()
    {
        $this->producto->setData(23, "Camisa", 20, 5, "", 1, 1, 1);
        $res = $this->producto->updateProduc();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('descripción', strtolower($res['message']));
    }

    public function testStatusInvalido()
    {
        $this->producto->setData(23, "Camisa", 20, 5, "desc", 2, 1, 1);
        $res = $this->producto->updateProduc();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('estado', strtolower($res['message']));
    }

    // ========================================
    // VALIDACIONES DE EXISTENCIA Y RELACIONES
    // ========================================

    public function testProductoInexistente()
    {
        $this->producto->setData(9999, "Producto Fantasma", 20, 5, "desc", 1, 1, 1);
        $res = $this->producto->updateProduc();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('no existe', strtolower($res['message']));
    }

    public function testCategoriaInvalida()
    {
        $this->producto->setData(23, "Producto sin categoría", 10, 5, "desc", 1, 9999, 1);
        $res = $this->producto->updateProduc();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('categoría', strtolower($res['message']));
    }

    public function testEmprendedorInvalido()
    {
        $this->producto->setData(23, "Producto sin emprendedor", 10, 5, "desc", 1, 1, 9999);
        $res = $this->producto->updateProduc();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('emprendedor', strtolower($res['message']));
    }


    // ========================================
    // VALIDACIONES DE DUPLICADOS
    // ========================================

    public function testNombreDuplicado()
    {
        $nombreDuplicado = "ProductoDuplicado_" . rand(1, 1000);

        // Insertar un producto inicial
        $this->producto->setData(null, $nombreDuplicado, 10, 5, "desc", 1, 1, 1);
        $this->producto->registerProduc();

        // Intentar actualizar otro con el mismo nombre
        $this->producto->setData(23, $nombreDuplicado, 15, 10, "desc", 1, 1, 1);
        $res = $this->producto->updateProduc();

        $this->assertFalse($res['success']);
        $this->assertStringContainsString('ya existe', strtolower($res['message']));
    }

    // ========================================
    // VALIDACIONES DE IMÁGENES
    // ========================================

    public function testImagenRutaInvalida()
    {
        $this->producto->setData(23, "Producto Imagen", 20, 5, "desc", 1, 1, 1);
        $res = $this->producto->updateProduc([123, null, ""]);
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('imagen', strtolower($res['message']));
    }

    // ========================================
    // VALIDACIONES ESPECIALES
    // ========================================

    public function testActualizarConNuevasImagenes()
    {
        $this->producto->setData(23,"Producto Con Imágenes Nuevas",25,8,"Actualizado con imágenes",1,1,1);

        $res = $this->producto->updateProduc([
            "imagenes/nueva1.png",
            "imagenes/nueva2.png"
        ]);

        $this->assertIsArray($res);
        $this->assertTrue($res['success']);
        $this->assertArrayHasKey('id_producto', $res);
    }
}
