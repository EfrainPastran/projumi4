<?php
use PHPUnit\Framework\TestCase;
use App\Models\EnviosModel;

class EnviosModelTest extends TestCase
{
    private $enviosModel;
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
        if (!defined('BD_SEGURIDAD')) {
            define('BD_SEGURIDAD', 'seguridad');
        }

        $this->db = new PDO(DB_DSN_PROJUMI, DB_USER, DB_PASS, DB_OPTIONS);
        $this->enviosModel = new EnviosModel();
    }

    // === CASOS DE REGISTRO DE ENVÍO ===

    public function testRegistrarEnvioExitoso()
    {
        try {
            // Preparar datos de prueba
            $direccion = "Calle Test 123, Ciudad Test";
            $estatus = "Pendiente";
            $numeroSeguimiento = "TRACK" . uniqid();
            $empresaEnvio = 1; // ID de empresa de envío existente
            $pedido = $this->crearPedidoTest(); // Crear pedido de prueba

            $this->enviosModel->setEnvioData(
                null,
                $direccion,
                $estatus,
                $numeroSeguimiento,
                $empresaEnvio,
                $pedido
            );

            $idEnvio = $this->enviosModel->registrarEnvio();

            $this->assertIsNumeric($idEnvio);
            $this->assertGreaterThan(0, $idEnvio);

        } finally {
           
        }
    }

    public function testRegistrarEnvioSinDatosObligatorios()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Error al registrar envío');

        // No establecer datos obligatorios
        $this->enviosModel->setEnvioData(null, null, null, null, null, null);
        $this->enviosModel->registrarEnvio();
    }

    // === CASOS DE ACTUALIZACIÓN DE ENVÍO ===

    public function testActualizarEnvioExitoso()
    {
        try {
            // Crear envío de prueba
            $envioId = $this->crearEnvioTest();

            $this->enviosModel->setEnvioData(
                $envioId,
                "Nueva Dirección 456",
                "En proceso",
                "NEWTRACK" . uniqid(),
                2, // Otra empresa de envío
                $this->crearPedidoTest() // Nuevo pedido
            );

            $result = $this->enviosModel->actualizarEnvio();

            $this->assertTrue($result);

            // Verificar que se actualizó
            $envioActualizado = $this->enviosModel->obtenerEnvioPorId($envioId);
            $this->assertEquals("Nueva Dirección 456", $envioActualizado['direccion_envio']);

        } finally {
           
        }
    }

    // === CASOS DE ACTUALIZACIÓN NÚMERO SEGUIMIENTO ===

    public function testActualizarNroSeguimientoExitoso()
    {
        try {
            // Crear envío y pedido de prueba
            $pedidoId = $this->crearPedidoTest();
            $envioId = $this->crearEnvioTest($pedidoId);

            $nuevoNumeroSeguimiento = "SEGUIMIENTO" . uniqid();
            $nuevoEstatus = "En proceso";

            $this->enviosModel->setEnvioData(
                $envioId,
                null,
                $nuevoEstatus,
                $nuevoNumeroSeguimiento,
                null,
                null
            );

            $result = $this->enviosModel->actualizarNroSeguimiento();

            $this->assertTrue($result);

            // Verificar que se actualizó el envío
            $envioActualizado = $this->enviosModel->obtenerEnvioPorId($envioId);
            $this->assertEquals($nuevoNumeroSeguimiento, $envioActualizado['numero_seguimiento']);
            $this->assertEquals($nuevoEstatus, $envioActualizado['estatus']);

        } finally {
           
        }
    }

    public function testActualizarNroSeguimientoEnvioInexistente()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No se encontró un pedido asociado al envío');

        $this->enviosModel->setEnvioData(
            99999, // ID que no existe
            null,
            "En proceso",
            "TRACK123",
            null,
            null
        );

        $this->enviosModel->actualizarNroSeguimiento();
    }

    public function testActualizarNroSeguimientoEstatusEntregado()
    {
        try {
            $pedidoId = $this->crearPedidoTest();
            $envioId = $this->crearEnvioTest($pedidoId);

            $this->enviosModel->setEnvioData(
                $envioId,
                null,
                "Entregado",
                "DELIVERED" . uniqid(),
                null,
                null
            );

            $result = $this->enviosModel->actualizarNroSeguimiento();

            $this->assertTrue($result);

        } finally {
           
        }
    }

    // === CASOS DE CONSULTA DE ENVÍOS ===

    public function testObtenerEnvioPorIdExitoso()
    {
        try {
            $envioId = $this->crearEnvioTest();

            $envio = $this->enviosModel->obtenerEnvioPorId($envioId);

            $this->assertIsArray($envio);
            $this->assertEquals($envioId, $envio['id_envio']);
            $this->assertArrayHasKey('direccion_envio', $envio);
            $this->assertArrayHasKey('estatus', $envio);

        } finally {
           
        }
    }

    public function testObtenerEnvioPorIdInexistente()
    {
        $envio = $this->enviosModel->obtenerEnvioPorId(99999);
        $this->assertFalse($envio);
    }


    public function testObtenerTodosLosEnvios()
    {
        try {
            $this->crearEnvioTest();
            $this->crearEnvioTest();

            $envios = $this->enviosModel->obtenerTodosLosEnvios();

            $this->assertIsArray($envios);
            $this->assertGreaterThanOrEqual(2, count($envios));

        } finally {
           
        }
    }

    // === CASOS DE ENVÍOS POR EMPRENDEDOR ===

    public function testObtenerEnviosPorEmprendedor()
    {
        try {
            $cedulaEmprendedor = "12345678"; // Cédula de prueba
            $this->crearEmprendedorTest($cedulaEmprendedor);

            $envios = $this->enviosModel->obtenerEnviosPorEmprendedor($cedulaEmprendedor);

            $this->assertIsArray($envios);

        } finally {
           
        }
    }

    public function testObtenerEnviosPorEmprendedorConFechas()
    {
        try {
            $cedulaEmprendedor = "12345678";
            $this->crearEmprendedorTest($cedulaEmprendedor);

            $dateFrom = '2024-01-01';
            $dateTo = '2024-12-31';

            $envios = $this->enviosModel->obtenerEnviosPorEmprendedor(
                $cedulaEmprendedor, 
                $dateFrom, 
                $dateTo
            );

            $this->assertIsArray($envios);

        } finally {
           
        }
    }

    public function testContarEnviosPorEmprendedor()
    {
        try {
            $cedulaEmprendedor = "12345678";
            $this->crearEmprendedorTest($cedulaEmprendedor);

            $total = $this->enviosModel->contarEnviosPorEmprendedor($cedulaEmprendedor);

            $this->assertIsNumeric($total);
            $this->assertGreaterThanOrEqual(0, $total);

        } finally {
           
        }
    }

    public function testObtenerCantidadEnviosPorEmprendedor()
    {
        try {
            $cedulaEmprendedor = "12345678";
            $this->crearEmprendedorTest($cedulaEmprendedor);

            $cantidad = $this->enviosModel->obtenerCantidadEnviosPorEmprendedor($cedulaEmprendedor);

            $this->assertIsNumeric($cantidad);
            $this->assertGreaterThanOrEqual(0, $cantidad);

        } finally {
           
        }
    }

    // === CASOS DE ENVÍOS POR CLIENTE ===

    public function testObtenerEnviosPorCliente()
    {
        try {
            $cedulaCliente = "87654321";
            $this->crearClienteTest($cedulaCliente);

            $envios = $this->enviosModel->obtenerEnviosPorCliente($cedulaCliente);

            $this->assertIsArray($envios);

        } finally {
           
        }
    }

    // === CASOS DE CONTEO GENERAL ===

    public function testContarEnviosTotales()
    {
        try {
            $this->crearEnvioTest();
            $this->crearEnvioTest();

            $total = $this->enviosModel->contarEnviosTotales();

            $this->assertIsNumeric($total);
            $this->assertGreaterThanOrEqual(2, $total);

        } finally {
           
        }
    }

    // === CASOS DE ELIMINACIÓN ===

    public function testEliminarEnvioExitoso()
    {
        try {
            $envioId = $this->crearEnvioTest();

            $result = $this->enviosModel->eliminarEnvio($envioId);

            $this->assertTrue($result);

            // Verificar que ya no existe
            $envioEliminado = $this->enviosModel->obtenerEnvioPorId($envioId);
            $this->assertFalse($envioEliminado);

        } finally {
           
        }
    }

    public function testEliminarEnvioInexistente()
    {
        $result = $this->enviosModel->eliminarEnvio(99999);
        $this->assertIsBool($result);
    }

    // === MÉTODOS AUXILIARES ===

    private function crearEnvioTest($pedidoId = null)
    {
        if (!$pedidoId) {
            $pedidoId = $this->crearPedidoTest();
        }

        $this->enviosModel->setEnvioData(
            null,
            "Calle Test " . uniqid(),
            "Pendiente",
            "TRACK" . uniqid(),
            1, // Empresa de envío por defecto
            $pedidoId
        );

        return $this->enviosModel->registrarEnvio();
    }

    private function crearPedidoTest()
    {
        try {
            // Insertar un pedido de prueba
            $sql = "INSERT INTO t_pedidos (fecha_pedido, estatus, fk_cliente) 
                    VALUES (NOW(), 'Pendiente', ?)";
            $stmt = $this->db->prepare($sql);
            
            // Crear cliente de prueba si no existe
            $clienteId = $this->crearClienteTest("CLI" . uniqid());
            
            $stmt->execute([$clienteId]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return 1; // ID por defecto para pruebas
        }
    }

    private function crearClienteTest($cedula = null)
    {
        if (!$cedula) {
            $cedula = "CLI" . uniqid();
        }

        try {
            // Verificar si el cliente ya existe
            $sqlCheck = "SELECT id_cliente FROM t_cliente WHERE cedula = ?";
            $stmtCheck = $this->db->prepare($sqlCheck);
            $stmtCheck->execute([$cedula]);
            $existing = $stmtCheck->fetch();

            if ($existing) {
                return $existing['id_cliente'];
            }

            // Insertar cliente
            $sql = "INSERT INTO t_cliente (cedula) VALUES (?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$cedula]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return 1; // ID por defecto
        }
    }

    private function crearEmprendedorTest($cedula)
    {
        try {
            // Verificar si el emprendedor ya existe
            $sqlCheck = "SELECT id_emprededor FROM t_emprendedor WHERE cedula = ?";
            $stmtCheck = $this->db->prepare($sqlCheck);
            $stmtCheck->execute([$cedula]);
            $existing = $stmtCheck->fetch();

            if ($existing) {
                return $existing['id_emprededor'];
            }

            // Insertar emprendedor
            $sql = "INSERT INTO t_emprendedor (cedula) VALUES (?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$cedula]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return 1; // ID por defecto
        }
    }

    private function limpiarDatosTest()
    {
        try {
            // Limpiar envíos de prueba
            $stmt = $this->db->prepare("
                DELETE FROM t_envio 
                WHERE direccion_envio LIKE 'Calle Test%' 
                   OR numero_seguimiento LIKE 'TRACK%'
                   OR numero_seguimiento LIKE 'NEWTRACK%'
                   OR numero_seguimiento LIKE 'SEGUIMIENTO%'
                   OR numero_seguimiento LIKE 'DELIVERED%'
            ");
            $stmt->execute();

            // Limpiar pedidos de prueba
            $stmt = $this->db->prepare("
                DELETE FROM t_pedidos 
                WHERE id_pedidos NOT IN (SELECT DISTINCT fk_pedido FROM t_envio)
            ");
            $stmt->execute();

        } catch (PDOException $e) {
            // No fallar la prueba por errores de limpieza
            error_log('Error limpiando datos test: ' . $e->getMessage());
        }
    }

    protected function tearDown(): void
    {
        $this->enviosModel = null;
        $this->db = null;
    }
}