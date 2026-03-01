<?php
use PHPUnit\Framework\TestCase;
use App\Models\categoriasModel;
require_once __DIR__ . '/../../TestLinkAPIClient.php';

class TestRegistrarCategoria extends TestCase
{
    private $pedido;
    private $db;
    private $tl;
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
                // Inicializar TestLink
        $this->tl = new TestLinkAPIClient();

    }

    public function testRegistrarCategoriaExitosa()
    {
        $categoria = new categoriasModel();
        $categoria->setData(null, 'Tecnologías', 'Productos líquidos', 1);
        $resultado = $categoria->registerCategoria();

        $this->assertIsArray($resultado);
        $this->assertTrue($resultado['success']);
        $this->assertArrayHasKey('categoria_id', $resultado);

        // Reportar resultado a TestLink (SGP-61)
            $this->tl->reportTCResult(
            "SGP-61",
            13,
            1,
            "p",
            "Resultado automático desde PHPUnit"
        );

    }

    public function testRegistrarCategoriaDuplicada()
    {
        $categoria = new categoriasModel();
        $categoria->setData(null, 'Bebidas', 'Otra descripción', 1);
        $resultado = $categoria->registerCategoria();

        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('ya existe', strtolower($resultado['message']));
    }

    public function testRegistrarCategoriaSinNombre()
    {
        $categoria = new categoriasModel();
        $categoria->setData(null, null, 'Otra descripción', 1);
        $resultado = $categoria->registerCategoria();

        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('nombre', strtolower($resultado['message']));
    }

}