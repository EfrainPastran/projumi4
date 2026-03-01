<?php
use PHPUnit\Framework\TestCase;
use App\Models\UsuariosModel;

class TestActualizarUsuario extends TestCase
{
    private $usuario;
    private $db;
    private $usuarioId;

    protected function setUp(): void
    {
        // Reusar las constantes del proyecto si no están definidas
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

        $this->db = new PDO(DB_DSN_PROJUMI, DB_USER, DB_PASS, DB_OPTIONS);
        $this->usuario = new UsuariosModel();

        // Crear un usuario base para las pruebas de actualización
        $correo = "usuario_update_" . rand(1000, 9999) . "@test.com";
        $this->usuario->setData(
            "99" . rand(100000, 999999),
            "Carlos",
            "Ramírez",
            $correo,
            "password123",
            "Av. 10",
            "04125551234",
            date('Y-m-d H:i:s'),
            "1990-01-01",
            1,
            2
        );
        $resultado = $this->usuario->registerUsuario();
        $this->assertTrue($resultado['success']);
        $this->usuarioId = $resultado['id_usuario'];
    }

    // ========================================
    // CASO DE ÉXITO
    // ========================================
    public function testActualizarUsuarioExitoso()
    {
        $this->usuario->setData(
            "99123456", // cédula
            "Carlos",
            "Ramírez",
            "usuario_update_exitoso@test.com",
            "", // no cambia contraseña
            "Av. Bolívar",
            "04123334444",
            date('Y-m-d H:i:s'),
            "1990-01-01",
            1,
            2,
            $this->usuarioId
        );

        $res = $this->usuario->updateUsuario();

        $this->assertIsArray($res);
        $this->assertTrue($res['success']);
        $this->assertStringContainsString('actualizado', strtolower($res['message']));
    }

    // ========================================
    // ACTUALIZACIÓN CON CONTRASEÑA NUEVA
    // ========================================
    public function testActualizarConNuevaPassword()
    {
        $this->usuario->setData(
            "99123457",
            "Carlos",
            "Ramírez",
            "usuario_update_pass@test.com",
            "nuevaClave123", // nueva password válida
            "Av. Las Flores",
            "04124445555",
            date('Y-m-d H:i:s'),
            "1990-01-01",
            1,
            2,
            $this->usuarioId
        );

        $res = $this->usuario->updateUsuario();

        $this->assertIsArray($res);
        $this->assertTrue($res['success']);
        $this->assertStringContainsString('actualizado', strtolower($res['message']));
    }

    // ========================================
    // ERROR: CORREO INVÁLIDO
    // ========================================
    public function testCorreoInvalidoEnActualizacion()
    {
        $this->usuario->setData(
            "99123458",
            "Carlos",
            "Ramírez",
            "correo_invalido",
            "",
            "Av. Los Próceres",
            "04124445555",
            date('Y-m-d H:i:s'),
            "1990-01-01",
            1,
            2,
            $this->usuarioId
        );

        $res = $this->usuario->updateUsuario();

        $this->assertFalse($res['success']);
        $this->assertStringContainsString('correo', strtolower($res['message']));
    }

    // ========================================
    // ERROR: CONTRASEÑA MUY CORTA
    // ========================================
    public function testPasswordCortaEnActualizacion()
    {
        $this->usuario->setData(
            "99123459",
            "Carlos",
            "Ramírez",
            "usuario_update_shortpass@test.com",
            "12", // muy corta
            "Av. Los Pinos",
            "04124445555",
            date('Y-m-d H:i:s'),
            "1990-01-01",
            1,
            2,
            $this->usuarioId
        );

        $res = $this->usuario->updateUsuario();

        $this->assertFalse($res['success']);
        $this->assertStringContainsString('contraseña', strtolower($res['message']));
    }

    // ========================================
    // ERROR: ROL INVÁLIDO
    // ========================================
    public function testRolInvalidoEnActualizacion()
    {
        $this->usuario->setData(
            "99123460",
            "Carlos",
            "Ramírez",
            "usuario_update_rol@test.com",
            "",
            "Av. Las Acacias",
            "04123334444",
            date('Y-m-d H:i:s'),
            "1990-01-01",
            1,
            0, // rol inválido
            $this->usuarioId
        );

        $res = $this->usuario->updateUsuario();

        $this->assertFalse($res['success']);
        $this->assertStringContainsString('rol', strtolower($res['message']));
    }
}
