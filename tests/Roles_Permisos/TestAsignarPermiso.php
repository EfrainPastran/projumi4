<?php
use PHPUnit\Framework\TestCase;
use App\Models\PermisosModel;

class TestAsignarPermiso extends TestCase
{
    private $permiso;
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
        $this->permiso = new PermisosModel();
    }

    // Caso exitoso
    public function testAsignarPermisoExitoso()
    {
        $fk_rol = 1;      // Rol existente
        $fk_modulo = 1;   // Módulo existente
        $fk_permiso = 1;  // Permiso existente

        $resultado = $this->permiso->asignarPermiso($fk_rol, $fk_modulo, $fk_permiso);

        $this->assertIsArray($resultado);
        $this->assertTrue($resultado['success']);
        $this->assertStringContainsString('exitosamente', strtolower($resultado['message']));
    }

    // Campos vacíos
    public function testCamposVacios()
    {
        $resultado = $this->permiso->asignarPermiso(null, null, null);

        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('obligatorios', strtolower($resultado['message']));
    }

    // Rol inexistente
    public function testRolInexistente()
    {
        $resultado = $this->permiso->asignarPermiso(9999, 1, 1);

        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('rol', strtolower($resultado['message']));
    }

    // Módulo inexistente
    public function testModuloInexistente()
    {
        $resultado = $this->permiso->asignarPermiso(1, 9999, 1);

        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('módulo', strtolower($resultado['message']));
    }

    // Permiso inexistente
    public function testPermisoInexistente()
    {
        $resultado = $this->permiso->asignarPermiso(1, 1, 9999);

        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('permiso', strtolower($resultado['message']));
    }

    // Asignación duplicada
    public function testAsignacionDuplicada()
    {
        $fk_rol = 1;
        $fk_modulo = 1;
        $fk_permiso = 1;

        // Primera inserción
        $this->permiso->asignarPermiso($fk_rol, $fk_modulo, $fk_permiso);
        // Segunda (debería fallar)
        $resultado = $this->permiso->asignarPermiso($fk_rol, $fk_modulo, $fk_permiso);

        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('ya está asignado', strtolower($resultado['message']));
    }

    // Error de base de datos simulado
    public function testErrorDeBaseDeDatos()
    {
        $mock = $this->createMock(PermisosModel::class);
        $mock->method('asignarPermiso')->willReturn([
            'success' => false,
            'message' => 'Error de base de datos: simulación'
        ]);

        $resultado = $mock->asignarPermiso(1, 1, 1);

        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('base de datos', strtolower($resultado['message']));
    }
}
