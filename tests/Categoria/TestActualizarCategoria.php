<?php
use PHPUnit\Framework\TestCase;
use App\Models\categoriasModel;

class TestActualizarCategoria extends TestCase
{
    private $pedido;
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
        $this->pedido = new categoriasModel();
    }

    public function testActualizarCategoriaExitosa()
    {
        $categoria = new categoriasModel();
        $categoria->setData(9, 'Bebida modificada' , 'Productos liquidos', 1);
        $resultado = $categoria->updateCategoria();

        $this->assertIsArray($resultado);
        $this->assertTrue($resultado['success']);
    }

    public function testActualizarIdInvalido()
    {
        $categoria = new categoriasModel();
        $categoria->setData(99, 'Bebida modificada' , 'Productos líquidos', 1);
        $resultado = $categoria->updateCategoria();

        $this->assertIsArray($resultado);
        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('categoría no existe', strtolower($resultado['message']));
    }
    

    public function testActualizarCategoriaDuplicada()
    {
        $categoria = new categoriasModel();
        $categoria->setData(9, 'Bisuteria', 'Otra descripción', 1);
        $resultado = $categoria->updateCategoria();

        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('ya existe', strtolower($resultado['message']));
    }

    public function testActualizarCategoriaSinNombre()
    {
        $categoria = new categoriasModel();
        $categoria->setData(null, null, 'Otra descripción', 1);
        $resultado = $categoria->updateCategoria();

        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('el id de la categoría es inválido', strtolower($resultado['message']));
    }

}
