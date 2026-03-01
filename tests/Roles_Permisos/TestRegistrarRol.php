<?php

use App\Models\rolesModel;
use PHPUnit\Framework\TestCase;

class TestRegistrarRol extends TestCase
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

    public function testRegistrarRolExitoso()
    {
        $nombre = "Rol Test " . rand(1, 10000);
        $this->rol->setData(null, $nombre, "Rol de prueba funcional", 1);

        $resultado = $this->rol->registerRol();

        $this->assertIsArray($resultado);
        $this->assertTrue($resultado['success']);
        $this->assertStringContainsString('registrado', strtolower($resultado['message']));
    }

    // ===================================================
    // VALIDACIONES DE CAMPOS REQUERIDOS
    // ===================================================

    public function testNombreVacio()
    {
        $this->rol->setData(null, "", "Descripción válida", 1);
        $resultado = $this->rol->registerRol();

        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('nombre', strtolower($resultado['message']));
    }

    public function testDescripcionVacia()
    {
        $this->rol->setData(null, "Rol sin descripción", "", 1);
        $resultado = $this->rol->registerRol();

        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('descripción', strtolower($resultado['message']));
    }

    public function testEstatusVacio()
    {
        $this->rol->setData(null, "Rol sin estatus", "Rol de prueba", "");
        $resultado = $this->rol->registerRol();

        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('estatus', strtolower($resultado['message']));
    }

    // ===================================================
    // VALIDACIONES DE DUPLICADOS
    // ===================================================

    public function testNombreDuplicado()
    {
        $nombreDuplicado = "Rol Duplicado " . rand(1, 1000);

        // Registrar el primero
        $this->rol->setData(null, $nombreDuplicado, "Primer rol duplicado", 1);
        $this->rol->registerRol();

        // Intentar registrar otro igual
        $this->rol->setData(null, $nombreDuplicado, "Segundo intento duplicado", 1);
        $resultado = $this->rol->registerRol();

        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('ya existe', strtolower($resultado['message']));
    }

}
