<?php
use PHPUnit\Framework\TestCase;
use App\Models\rolesModel;

class TestEliminarRol extends TestCase
{
    private $rol;
    private $db;

    protected function setUp(): void
    {
        if (!defined('DB_DSN')) {
            define('DB_DSN', 'mysql:host=localhost;dbname=seguridad;charset=utf8');
            define('DB_USER', 'root');
            define('DB_PASS', '');
            define('DB_OPTIONS', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        }

        $this->db = new PDO(DB_DSN, DB_USER, DB_PASS, DB_OPTIONS);
        $this->rol = new rolesModel();
    }

    // Caso exitoso
    public function testEliminarRolExitoso()
    {
        $this->rol->setData(4, "", "", 0);
        $resultado = $this->rol->delete();

        $this->assertIsArray($resultado);
        $this->assertTrue($resultado['success']);
        $this->assertStringContainsString('eliminado', strtolower($resultado['message']));
    }

    // Sin ID
    public function testEliminarRolSinID()
    {
        $this->rol->setData(null, "", "", 0);
        $resultado = $this->rol->delete();

        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('obligatorio', strtolower($resultado['message']));
    }

    // Rol inexistente
    public function testEliminarRolInexistente()
    {
        $this->rol->setData(999999, "", "", 0);
        $resultado = $this->rol->delete();

        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('no existe', strtolower($resultado['message']));
    }

    // Error de base de datos simulado
    public function testErrorBaseDeDatos()
    {
        $mock = $this->createMock(rolesModel::class);
        $mock->method('delete')->willReturn([
            'success' => false,
            'message' => 'Error de base de datos: simulación de fallo'
        ]);

        $resultado = $mock->delete();

        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('base de datos', strtolower($resultado['message']));
    }
}
