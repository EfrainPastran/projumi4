<?php
use PHPUnit\Framework\TestCase;
use App\Models\rolesModel;

class TestActualizarRol extends TestCase
{
    private $rol;
    private $db;

    /**
     * Configuración inicial antes de cada prueba
     */
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

    // ===================================================
    // CASOS DE ÉXITO
    // ===================================================

    public function testActualizarRolExitoso()
    {
        // Actualizarlo
        $this->rol->setData(3, "Rol Actualizado " . rand(1000, 9999), "Descripción modificada", 1);
        $resultado = $this->rol->update();

        $this->assertIsArray($resultado);
        $this->assertTrue($resultado['success']);
        $this->assertStringContainsString('actualizado', strtolower($resultado['message']));
    }

    // ===================================================
    // VALIDACIONES DE CAMPOS OBLIGATORIOS
    // ===================================================

    public function testActualizarRolSinID()
    {
        $this->rol->setData(null, "Rol sin ID", "Descripción", 1);
        $resultado = $this->rol->update();

        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('id', strtolower($resultado['message']));
    }

    public function testActualizarRolSinNombre()
    {
        $this->rol->setData(1, "", "Descripción", 1);
        $resultado = $this->rol->update();

        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('nombre', strtolower($resultado['message']));
    }

    // ===================================================
    // ROL INEXISTENTE
    // ===================================================

    public function testActualizarRolInexistente()
    {
        $this->rol->setData(99999, "Rol Inexistente", "Desc", 1);
        $resultado = $this->rol->update();

        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('no existe', strtolower($resultado['message']));
    }

    // ===================================================
    // ROL DUPLICADO
    // ===================================================

    public function testActualizarRolDuplicado()
    {
        // Intentar cambiar el nombre del segundo por el primero
        $this->rol->setData(3, "Administrador", "Desc cambiado", 1);
        $resultado = $this->rol->update();

        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('ya existe', strtolower($resultado['message']));
    }
}
