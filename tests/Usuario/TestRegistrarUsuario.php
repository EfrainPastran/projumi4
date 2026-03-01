<?php
use PHPUnit\Framework\TestCase;
use App\Models\UsuariosModel;

class TestRegistrarUsuario extends TestCase
{
    private $usuario;
    private $db;

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

        if (!defined('DB_DSN')) {
            define('DB_DSN', 'mysql:host=localhost;dbname=seguridad;charset=utf8');
        }

        $this->db = new PDO(DB_DSN_PROJUMI, DB_USER, DB_PASS, DB_OPTIONS);
        $this->usuario = new UsuariosModel();
    }

    // ========================================
    // CASO DE ÉXITO
    // ========================================

    public function testRegistroUsuarioExitoso()
    {
        $correo = "usuarionuveo1@correo.com";

        $this->usuario->setData(
            "93245884",
            "Carlos",
            "Pérez",
            $correo,
            "password123",
            "Av. Principal",
            "04125551234",
            date('Y-m-d H:i:s'),
            "1990-05-15",
            1,
            2 // Rol válido
        );

        $resultado = $this->usuario->registerUsuario();

        $this->assertIsArray($resultado);
        $this->assertTrue($resultado['success']);
        $this->assertArrayHasKey('id_usuario', $resultado);
        $this->assertStringContainsString('registrado', strtolower($resultado['message']));
    }

    // ========================================
    // VALIDACIONES DE CAMPOS
    // ========================================

    public function testCedulaInvalida()
    {
        $this->usuario->setData(
            "12", "Ana", "Lopez", "ana@example.com",
            "clave123", "Calle Falsa 123", "04125550000",
            date('Y-m-d H:i:s'), "1990-01-01", 1, 2
        );

        $res = $this->usuario->registerUsuario();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('cédula', strtolower($res['message']));
    }

    public function testCorreoInvalido()
    {
        $this->usuario->setData(
           "12345678", "Pedro", "Gomez", "correo_invalido",
            "clave123", "Av. 1", "04121112222",
            date('Y-m-d H:i:s'), "1992-10-20", 1, 2
        );

        $res = $this->usuario->registerUsuario();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('correo', strtolower($res['message']));
    }

    public function testPasswordCorta()
    {
        $this->usuario->setData(
            "12345678", "Luis", "Martinez", "luis@example.com",
            "123", "Dirección 1", "04125551234",
            date('Y-m-d H:i:s'), "1995-04-10", 1, 2
        );

        $res = $this->usuario->registerUsuario();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('contraseña', strtolower($res['message']));
    }

    public function testTelefonoInvalido()
    {
        $this->usuario->setData(
            "12345678", "Maria", "Diaz", "maria@example.com",
            "password123", "Calle 5", "ABC123",
            date('Y-m-d H:i:s'), "1991-03-21", 1, 2
        );

        $res = $this->usuario->registerUsuario();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('teléfono', strtolower($res['message']));
    }

    // ========================================
    // VALIDACIONES DE DUPLICADOS
    // ========================================

    public function testCedulaDuplicada()
    {
        $cedula = strval(rand(1000000, 9999999));
        $correo = "duplicado{$cedula}@test.com";

        // Primer registro (válido)
        $this->usuario->setData(
            $cedula, "Juan", "Mendoza", $correo,
            "clave123", "Av. Bolívar", "04124445555",
            date('Y-m-d H:i:s'), "1994-07-01", 1, 2
        );
        $this->usuario->registerUsuario();

        // Segundo intento con la misma cédula
        $this->usuario->setData(
            $cedula, "Juan", "Mendoza", "otrocorreo@test.com",
            "clave123", "Av. Bolívar", "04124445555",
            date('Y-m-d H:i:s'), "1994-07-01", 1, 2
        );
        $res = $this->usuario->registerUsuario();

        $this->assertFalse($res['success']);
        $this->assertStringContainsString('cédula', strtolower($res['message']));
    }

    public function testCorreoDuplicado()
    {
        $correo = "repetido" . rand(1000, 9999) . "@test.com";
        $cedula1 = strval(rand(1000000, 9999999));
        $cedula2 = strval(rand(1000000, 9999999));

        // Primer registro válido
        $this->usuario->setData(
            $cedula1, "Laura", "Suarez", $correo,
            "clave123", "Av. Principal", "04127773333",
            date('Y-m-d H:i:s'), "1998-08-09", 1, 2
        );
        $this->usuario->registerUsuario();

        // Segundo intento con el mismo correo
        $this->usuario->setData(
            $cedula2, "Laura", "Suarez", $correo,
            "clave123", "Av. Principal", "04127773333",
            date('Y-m-d H:i:s'), "1998-08-09", 1, 2
        );
        $res = $this->usuario->registerUsuario();

        $this->assertFalse($res['success']);
        $this->assertStringContainsString('correo', strtolower($res['message']));
    }

    // ========================================
    // VALIDACIÓN DE ROL
    // ========================================

    public function testRolInvalido()
    {
        $this->usuario->setData(
            "12345678", "Julio", "Pérez", "julio@test.com",
            "password123", "Calle 10", "04123334444",
            date('Y-m-d H:i:s'), "1990-01-01", 1, 0 // Rol inválido
        );

        $res = $this->usuario->registerUsuario();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('rol', strtolower($res['message']));
    }
}
