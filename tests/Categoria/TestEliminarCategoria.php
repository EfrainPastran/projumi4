<?php
use PHPUnit\Framework\TestCase;
use App\Models\categoriasModel;

class TestEliminarCategoria extends TestCase
{
    private $categoria;
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
        $this->categoria = new categoriasModel();
    }

    /**
     * Caso 1: Eliminación exitosa de una categoría existente
     */
    public function testEliminarCategoriaExitosa()
    {
        $categoria = new categoriasModel();
        $categoria->setData(9, 'Temporal', 'Categoría de prueba', 1);
        $resultado = $categoria->delete();

        $this->assertIsArray($resultado);
        $this->assertTrue($resultado['success']);
        $this->assertStringContainsString('eliminada', strtolower($resultado['message']));
    }

    /**
     * Caso 2: Intentar eliminar una categoría que no existe
     */
    public function testEliminarCategoriaInexistente()
    {
        $categoria = new categoriasModel();
        $categoria->setData(9999, 'No existe', 'Prueba de inexistente', 1);
        $resultado = $categoria->delete();

        $this->assertIsArray($resultado);
        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('no existe', strtolower($resultado['message']));
    }

    /**
     * Caso 3: Intentar eliminar una categoría que tiene productos asociados
     */
    public function testEliminarCategoriaConProductosAsociados()
    {
        $categoria = new categoriasModel();
        // Usa un ID de categoría que sepas tiene productos en BD (ajusta según tus datos reales)
        $categoria->setData(1, 'Con Productos', 'Categoría usada', 1);
        $resultado = $categoria->delete();

        $this->assertIsArray($resultado);
        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('productos asociados', strtolower($resultado['message']));
    }

    /**
     * Caso 4: Intentar eliminar con ID inválido o vacío
     */
    public function testEliminarCategoriaIdInvalido()
    {
        $categoria = new categoriasModel();
        $categoria->setData(null, 'Sin ID', 'Descripción', 1);
        $resultado = $categoria->delete();

        $this->assertIsArray($resultado);
        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('numérico', strtolower($resultado['message']));
    }
}
