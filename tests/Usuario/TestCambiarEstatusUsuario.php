<?php
use PHPUnit\Framework\TestCase;
use App\Models\UsuariosModel;

class TestCambiarEstatusUsuario extends TestCase
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

        // Crear usuario temporal
        $correo = "user_status_" . rand(1000, 9999) . "@test.com";
        $this->usuario->setData(
            "99" . rand(100000, 999999),
            "Luis",
            "Ramírez",
            $correo,
            "clave123",
            "Calle 7",
            "04126667777",
            date('Y-m-d H:i:s'),
            "1995-05-15",
            1,
            2
        );
        $res = $this->usuario->registerUsuario();
        $this->assertTrue($res['success']);
        $this->usuarioId = $res['id_usuario'];
    }

    public function testCambiarEstatusExitoso()
    {
        $res = $this->usuario->cambiarEstatus($this->usuarioId, 0);
        $this->assertTrue($res['success']);
        $this->assertEquals(0, $res['nuevo_estatus']);
    }

    public function testCambiarEstatusUsuarioInexistente()
    {
        $res = $this->usuario->cambiarEstatus(9999999, 1);
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('no existe', strtolower($res['message']));
    }

    public function testCambiarEstatusIdInvalido()
    {
        $res = $this->usuario->cambiarEstatus('abc', 1);
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('inválido', strtolower($res['message']));
    }

    public function testCambiarEstatusSinCambios()
    {
        $this->usuario->cambiarEstatus($this->usuarioId, 1);
        $res = $this->usuario->cambiarEstatus($this->usuarioId, 1);
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('ya tiene ese estatus', strtolower($res['message']));
    }
}
