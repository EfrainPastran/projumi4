<?php
use PHPUnit\Framework\TestCase;
use App\Models\categoriasModel;

class CategoriasModelTest extends TestCase
{
    private $categoria;

    // Se ejecuta antes de cada test

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

        $this->categoria = new categoriasModel();
    }


    public function testRegistrarCategoria()
    {
        $this->categoria->setData(null, "Ropa", "Prendas de vestir", 1);
        $resultado = $this->categoria->registerCategoria();

        // Si registerCategoria devuelve true, es que se insertó bien
        $this->assertEquals(true, $resultado);
    }

    public function testObtenerPorNombre()
    {
        $nombre = "Ropa";
        $data = $this->categoria->getByName($nombre);

        // Debería devolver un array con los datos
        $this->assertIsArray($data);
        $this->assertEquals($nombre, $data["nombre"]);
    }

    public function testActualizarCategoria()
    {
        $this->categoria->setData(1, "Ropa Actualizada", "Nueva descripción", 1);
        $resultado = $this->categoria->update();

        $this->assertEquals(true, $resultado);
    }

    public function testEliminarCategoria()
    {
        $this->categoria->setData(8, "Ropa Actualizada", "Nueva descripción", 1);
        $resultado = $this->categoria->delete();

        $this->assertEquals(true, $resultado);
    }

    public function testExisteCategoria()
    {
        $resultado = $this->categoria->existeCategoria(1);
        $this->assertIsBool($resultado);
    }
}
