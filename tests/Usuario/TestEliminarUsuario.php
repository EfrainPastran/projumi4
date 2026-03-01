<?php
use PHPUnit\Framework\TestCase;
use App\Models\UsuariosModel;

class TestEliminarUsuario extends TestCase
{
    private $usuario;
    private $usuarioId;

    protected function setUp(): void
    {
        if (!defined('DB_DSN_PROJUMI')) {
            define('DB_DSN_PROJUMI', 'mysql:host=localhost;dbname=projumi;charset=utf8');
            define('DB_DSN', 'mysql:host=localhost;dbname=seguridad;charset=utf8');
            define('DB_USER', 'root');
            define('DB_PASS', '');
            define('DB_OPTIONS', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        }

        $this->usuario = new UsuariosModel();

        // Crear usuario temporal para pruebas de eliminación
        $correo = "user_delete_" . rand(1000, 9999) . "@test.com";
        $this->usuario->setData(
            "88" . rand(100000, 999999),
            "Ana",
            "Pérez",
            $correo,
            "password123",
            "Av. Lara",
            "04125551234",
            date('Y-m-d H:i:s'),
            "1992-03-10",
            1,
            2
        );
        $res = $this->usuario->registerUsuario();
        $this->assertTrue($res['success']);
        $this->usuarioId = $res['id_usuario'];
    }

    // Eliminación exitosa
    public function testEliminarUsuarioExitoso()
    {
        $res = $this->usuario->deleteUsuario($this->usuarioId);
        $this->assertTrue($res['success']);
        $this->assertStringContainsString('eliminado', strtolower($res['message']));
    }

    // Intentar eliminar usuario inexistente
    public function testEliminarUsuarioInexistente()
    {
        $res = $this->usuario->deleteUsuario(999999);
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('no existe', strtolower($res['message']));
    }

    // Intentar eliminar con ID inválido
    public function testEliminarConIdInvalido()
    {
        $res = $this->usuario->deleteUsuario('abc');
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('inválido', strtolower($res['message']));
    }

    // Intentar eliminar dos veces
    public function testEliminarDosVeces()
    {
        // Primera eliminación
        $this->usuario->deleteUsuario($this->usuarioId);

        // Segunda eliminación debe fallar
        $res = $this->usuario->deleteUsuario($this->usuarioId);
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('ya está eliminado', strtolower($res['message']));
    }
}
