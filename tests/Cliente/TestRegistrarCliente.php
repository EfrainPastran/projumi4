<?php
use PHPUnit\Framework\TestCase;
use App\Models\ClienteModel;

class TestRegistrarCliente extends TestCase
{
    private $cliente;
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
        $this->cliente = new ClienteModel();
    }

    // ========================================
    // CASO DE ÉXITO
    // ========================================

    public function testRegistrarClienteNuevo()
    {
        $correo = "clientenueve@test.com";

        $clienteData = [
            'cedula' => '39123456',
            'nombre' => 'Andrés',
            'apellido' => 'Torres',
            'correo' => $correo,
            'direccion' => 'Av. Bolívar',
            'telefono' => '04125551234',
            'fecha_nacimiento' => '1995-05-10'
        ];

        $resultado = $this->cliente->registerCliente($clienteData);

        $this->assertIsArray($resultado);
        $this->assertTrue($resultado['success']);
        $this->assertArrayHasKey('id_cliente', $resultado);
        $this->assertStringContainsString('registrado', strtolower($resultado['message']));
    }

    // ========================================
    // CASO: CLIENTE YA EXISTENTE
    // ========================================

    public function testClienteYaRegistrado()
    {
        $cedula = '21123456';
        $correo = "cliente_existente{$cedula}@test.com";

        $clienteData = [
            'cedula' => $cedula,
            'nombre' => 'María',
            'apellido' => 'López',
            'correo' => $correo,
            'direccion' => 'Calle Sucre',
            'telefono' => '04125550000',
            'fecha_nacimiento' => '1990-02-02'
        ];

        // Primer registro
        $this->cliente->registerCliente($clienteData);

        // Segundo intento con la misma cédula
        $resultado = $this->cliente->registerCliente($clienteData);

        $this->assertIsArray($resultado);
        $this->assertTrue($resultado['success']);
        $this->assertStringContainsString('registrado', strtolower($resultado['message']));
    }

    // ========================================
    // CASO: FALTA DE DATOS
    // ========================================

    public function testRegistrarClienteSinCedula()
    {
        $clienteData = [
            'cedula' => '',
            'nombre' => 'Pedro',
            'apellido' => 'Sánchez',
            'correo' => 'pedro@test.com'
        ];

        $resultado = $this->cliente->registerCliente($clienteData);

        $this->assertIsArray($resultado);
        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('cédula', strtolower($resultado['message']));
    }

    public function testRegistrarClienteSinCorreo()
    {
        $clienteData = [
            'cedula' => '23123456',
            'nombre' => 'María',
            'apellido' => 'López',
            'correo' => '',
            'direccion' => 'Calle Sucre',
            'telefono' => '04125550000',
            'fecha_nacimiento' => '1990-02-02'
        ];
        $resultado = $this->cliente->registerCliente($clienteData);

        $this->assertIsArray($resultado);
        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('correo', strtolower($resultado['message']));
    }

}
