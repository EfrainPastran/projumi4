<?php
use PHPUnit\Framework\TestCase;
use App\Models\EmprendedorModel;

class TestRegistrarEmprendedor extends TestCase
{
    private $emprendedor;
    private $db;

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
        $this->emprendedor = new EmprendedorModel();
    }

    // ========================================
    // CASOS DE ÉXITO
    // ========================================

    public function testRegistrarEmprendedorExitoso()
    {
        $cedula = rand(10000000, 99999999);
        $this->emprendedor->setData(
            null,
            $cedula,
            'Caracas',
            'Soltero',
            'Venezolano',
            'J-12345678-9',
            'Masculino',
            'Ninguna',
            'Ninguna',
            'No',
            'Sí',
            'O+',
            'Católica',
            'Grupo A',
            0,
            0,
            'No',
            'Sí',
            'TSU',
            'Programador',
            'Desarrollador',
            'Leer',
            'Internet',
            'Superación personal',
            'Innovación tecnológica',
            'imagenes/emprendedor1.jpg',
            'Tienda Virtual',
            1,
            1 // fk_parroquia existente
        );

        $resultado = $this->emprendedor->registrarEmprendedor();

        $this->assertIsArray($resultado);
        $this->assertTrue($resultado['success']);
        $this->assertArrayHasKey('id_emprededor', $resultado);
    }

    // ========================================
    // VALIDACIONES DE CAMPOS BÁSICOS
    // ========================================

    public function testCedulaVacia()
    {
        $this->emprendedor->setData(null, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 1);
        $res = $this->emprendedor->registrarEmprendedor();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('cédula', strtolower($res['message']));
    }

    public function testCedulaNoNumerica()
    {
        $this->emprendedor->setData(null, 'ABC123', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 1);
        $res = $this->emprendedor->registrarEmprendedor();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('numérica', strtolower($res['message']));
    }

    public function testParroquiaVacia()
    {
        $this->emprendedor->setData(null, '12345678', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, null);
        $res = $this->emprendedor->registrarEmprendedor();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('parroquia', strtolower($res['message']));
    }

    // ========================================
    // VALIDACIONES RELACIONALES
    // ========================================

    public function testParroquiaInexistente()
    {
        $this->emprendedor->setData(
            null,
            '12345679',
            'Caracas',
            'Soltero',
            'Venezolano',
            'J-87654321-9',
            'Masculino',
            'Ninguna',
            'Ninguna',
            'No',
            'Sí',
            'O+',
            'Católica',
            'Grupo A',
            0,
            0,
            'No',
            'Sí',
            'TSU',
            'Programador',
            'Desarrollador',
            'Leer',
            'Internet',
            'Superación personal',
            'Innovación tecnológica',
            'imagenes/emprendedor1.jpg',
            'Tienda Virtual',
            1,
            99999 // parroquia inexistente
        );

        $res = $this->emprendedor->registrarEmprendedor();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('no existe', strtolower($res['message']));
    }

    // ========================================
    // VALIDACIONES DE DUPLICADOS
    // ========================================

    public function testCedulaDuplicada()
    {
        $cedulaDuplicada = rand(10000000, 99999999);

        // Registrar uno primero
        $this->emprendedor->setData(
            null,
            $cedulaDuplicada,
            'Caracas',
            'Soltero',
            'Venezolano',
            'J-11111111-1',
            'Masculino',
            'Ninguna',
            'Ninguna',
            'No',
            'Sí',
            'O+',
            'Católica',
            'Grupo A',
            0,
            0,
            'No',
            'Sí',
            'TSU',
            'Programador',
            'Desarrollador',
            'Leer',
            'Internet',
            'Superación personal',
            'Innovación tecnológica',
            'imagenes/emprendedor2.jpg',
            'Tienda Virtual',
            1,
            1
        );
        $this->emprendedor->registrarEmprendedor();

        // Intentar registrar otro con misma cédula
        $this->emprendedor->setData(
            null,
            $cedulaDuplicada,
            'Valencia',
            'Casado',
            'Venezolano',
            'J-22222222-2',
            'Masculino',
            'Ninguna',
            'Ninguna',
            'No',
            'Sí',
            'O+',
            'Católica',
            'Grupo A',
            0,
            0,
            'No',
            'Sí',
            'Licenciado',
            'Abogado',
            'Docente',
            'Fútbol',
            'Internet',
            'Trabajo en grupo',
            'Aprendizaje',
            'imagenes/emprendedor3.jpg',
            'Tienda Física',
            1,
            1
        );

        $res = $this->emprendedor->registrarEmprendedor();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('ya está registrado', strtolower($res['message']));
    }
}
