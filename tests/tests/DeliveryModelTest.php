<?php
use PHPUnit\Framework\TestCase;
use App\Models\DeliveryModel;

class DeliveryModelTest extends TestCase
{
    private $deliveryModel;
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
        $this->deliveryModel = new DeliveryModel();
    }

    // === CASOS DE REGISTRO DE DELIVERY ===

    public function testRegistrarDeliveryExitoso()
    {
        try {
            $pedidoId = $this->crearPedidoTest();

            $this->deliveryModel->setDeliveryData(
                null,
                "Calle Principal #123, Ciudad Test",
                "Juan Pérez",
                "1234567890",
                "juan@test.com",
                "0987654321",
                $pedidoId,
                "Pendiente"
            );

            $idDelivery = $this->deliveryModel->registrarDelivery();

            $this->assertIsNumeric($idDelivery);
            $this->assertGreaterThan(0, $idDelivery);

            // Verificar que se creó correctamente
            $deliveryCreado = $this->deliveryModel->obtenerDeliveryPorId($idDelivery);
            $this->assertEquals("Juan Pérez", $deliveryCreado['destinatario']);

        } finally {
            $this->limpiarDatosTest();
        }
    }

    public function testRegistrarDeliverySinDatosObligatorios()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Error al registrar delivery');

        $this->deliveryModel->setDeliveryData(null, null, null, null, null, null, null, null);
        $this->deliveryModel->registrarDelivery();
    }

    public function testRegistrarDeliveryConPedidoInexistente()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Error al registrar delivery');

        $this->deliveryModel->setDeliveryData(
            null,
            "Dirección Test",
            "Destinatario Test",
            "1234567890",
            "test@test.com",
            "0987654321",
            99999, // Pedido que no existe
            "Pendiente"
        );

        $this->deliveryModel->registrarDelivery();
    }

    // === CASOS DE ACTUALIZACIÓN DE DELIVERY ===

    public function testActualizarDeliveryExitoso()
    {
        try {
            $deliveryId = $this->crearDeliveryTest();

            $this->deliveryModel->setDeliveryData(
                $deliveryId,
                "Nueva Dirección #456",
                "María García",
                "1111111111",
                "maria@test.com",
                "2222222222",
                $this->crearPedidoTest(), // Nuevo pedido
                "En proceso"
            );

            $result = $this->deliveryModel->actualizarDelivery();

            $this->assertTrue($result);

            // Verificar que se actualizó
            $deliveryActualizado = $this->deliveryModel->obtenerDeliveryPorId($deliveryId);
            $this->assertEquals("María García", $deliveryActualizado['destinatario']);
            $this->assertEquals("En proceso", $deliveryActualizado['estatus']);

        } finally {
            $this->limpiarDatosTest();
        }
    }

    // === CASOS DE APROBACIÓN DE DELIVERY ===

    public function testAprobarDeliveryExitoso()
    {
        try {
            $deliveryId = $this->crearDeliveryTest();

            $this->deliveryModel->setDeliveryData(
                $deliveryId,
                null, // No necesario para aprobar
                null,
                null,
                null,
                "3333333333", // Teléfono del delivery
                null,
                null
            );

            $result = $this->deliveryModel->aprobarDelivery();

            $this->assertTrue($result);

            // Verificar que se actualizó el delivery
            $deliveryAprobado = $this->deliveryModel->obtenerDeliveryPorId($deliveryId);
            $this->assertEquals("En proceso", $deliveryAprobado['estatus']);
            $this->assertEquals("3333333333", $deliveryAprobado['telefono_delivery']);

        } finally {
            $this->limpiarDatosTest();
        }
    }

    public function testAprobarDeliveryInexistente()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No se encontró el pedido relacionado con este delivery');

        $this->deliveryModel->setDeliveryData(
            99999, // ID que no existe
            null,
            null,
            null,
            null,
            "1234567890",
            null,
            null
        );

        $this->deliveryModel->aprobarDelivery();
    }

    public function testAprobarDeliverySinTelefono()
    {
        try {
            $deliveryId = $this->crearDeliveryTest();

            $this->deliveryModel->setDeliveryData(
                $deliveryId,
                null,
                null,
                null,
                null,
                null, // Sin teléfono de delivery
                null,
                null
            );

            $result = $this->deliveryModel->aprobarDelivery();

            $this->assertTrue($result);

        } finally {
            $this->limpiarDatosTest();
        }
    }

    // === CASOS DE CONSULTA DE DELIVERIES ===

    public function testObtenerDeliveryPorIdExitoso()
    {
        try {
            $deliveryId = $this->crearDeliveryTest();

            $delivery = $this->deliveryModel->obtenerDeliveryPorId($deliveryId);

            $this->assertIsArray($delivery);
            $this->assertEquals($deliveryId, $delivery['id_delivery']);
            $this->assertArrayHasKey('direccion_exacta', $delivery);
            $this->assertArrayHasKey('destinatario', $delivery);
            $this->assertArrayHasKey('estatus', $delivery);

        } finally {
            $this->limpiarDatosTest();
        }
    }

    public function testObtenerDeliveryPorIdInexistente()
    {
        $delivery = $this->deliveryModel->obtenerDeliveryPorId(99999);
        $this->assertFalse($delivery);
    }

    public function testObtenerTodosLosDeliveries()
    {
        try {
            $this->crearDeliveryTest();
            $this->crearDeliveryTest();

            $deliveries = $this->deliveryModel->obtenerTodosLosDeliveries();

            $this->assertIsArray($deliveries);
            $this->assertGreaterThanOrEqual(2, count($deliveries));

            // Verificar estructura de los datos
            if (count($deliveries) > 0) {
                $primerDelivery = $deliveries[0];
                $this->assertArrayHasKey('id_delivery', $primerDelivery);
                $this->assertArrayHasKey('nombre_cliente', $primerDelivery);
                $this->assertArrayHasKey('nombre_emprendedor', $primerDelivery);
            }

        } finally {
            $this->limpiarDatosTest();
        }
    }

    // === CASOS DE DELIVERIES POR CLIENTE ===

    public function testObtenerDeliveriesPorCliente()
    {
        try {
            $cedulaCliente = "CLI" . uniqid();
            $this->crearClienteTest($cedulaCliente);
            
            // Crear delivery asociado al cliente
            $this->crearDeliveryTest();

            $deliveries = $this->deliveryModel->obtenerDeliveriesPorCliente($cedulaCliente);

            $this->assertIsArray($deliveries);

        } finally {
            $this->limpiarDatosTest();
        }
    }

    public function testObtenerDeliveriesPorClienteInexistente()
    {
        $deliveries = $this->deliveryModel->obtenerDeliveriesPorCliente("CLIENTE_INEXISTENTE");
        $this->assertIsArray($deliveries);
        $this->assertEmpty($deliveries);
    }

    // === CASOS DE DELIVERIES POR EMPRENDEDOR ===

    public function testObtenerDeliveriesPorEmprendedor()
    {
        try {
            $cedulaEmprendedor = "EMP" . uniqid();
            $this->crearEmprendedorTest($cedulaEmprendedor);
            
            // Crear delivery asociado al emprendedor
            $this->crearDeliveryTest();

            $deliveries = $this->deliveryModel->obtenerDeliveriesPorEmprendedor($cedulaEmprendedor);

            $this->assertIsArray($deliveries);

        } finally {
            $this->limpiarDatosTest();
        }
    }

    public function testObtenerDeliveriesPorEmprendedorInexistente()
    {
        $deliveries = $this->deliveryModel->obtenerDeliveriesPorEmprendedor("EMP_INEXISTENTE");
        $this->assertIsArray($deliveries);
        $this->assertEmpty($deliveries);
    }

    // === CASOS DE ELIMINACIÓN DE DELIVERY ===

    public function testEliminarDeliveryExitoso()
    {
        try {
            $deliveryId = $this->crearDeliveryTest();

            $result = $this->deliveryModel->eliminarDelivery($deliveryId);

            $this->assertTrue($result);

            // Verificar que ya no existe
            $deliveryEliminado = $this->deliveryModel->obtenerDeliveryPorId($deliveryId);
            $this->assertFalse($deliveryEliminado);

        } finally {
            $this->limpiarDatosTest();
        }
    }

    public function testEliminarDeliveryInexistente()
    {
        $result = $this->deliveryModel->eliminarDelivery(99999);
        $this->assertIsBool($result);
    }

    // === CASOS DE ESTADOS Y TRANSICIONES ===

    public function testEstatusDeliveryValidos()
    {
        try {
            $pedidoId = $this->crearPedidoTest();

            // Test con diferentes estatus
            $estatusValidos = ["Pendiente", "En proceso", "Completado", "Cancelado"];
            
            foreach ($estatusValidos as $estatus) {
                $this->deliveryModel->setDeliveryData(
                    null,
                    "Dirección " . $estatus,
                    "Destinatario " . $estatus,
                    "1234567890",
                    "test@test.com",
                    "0987654321",
                    $pedidoId,
                    $estatus
                );

                $idDelivery = $this->deliveryModel->registrarDelivery();
                $this->assertGreaterThan(0, $idDelivery);

                // Verificar que se guardó el estatus correctamente
                $delivery = $this->deliveryModel->obtenerDeliveryPorId($idDelivery);
                $this->assertEquals($estatus, $delivery['estatus']);
            }

        } finally {
            $this->limpiarDatosTest();
        }
    }

  
    // === MÉTODOS AUXILIARES ===

    private function crearDeliveryTest($pedidoId = null)
    {
        if (!$pedidoId) {
            $pedidoId = $this->crearPedidoTest();
        }

        $this->deliveryModel->setDeliveryData(
            null,
            "Calle Test " . uniqid(),
            "Destinatario Test " . uniqid(),
            "1234567890",
            "test" . uniqid() . "@test.com",
            "0987654321",
            $pedidoId,
            "Pendiente"
        );

        return $this->deliveryModel->registrarDelivery();
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
            // Si hay error, intentar obtener un pedido existente
            $stmt = $this->db->query("SELECT id_pedidos FROM t_pedidos LIMIT 1");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['id_pedidos'] : 1;
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
            // Si hay error, intentar obtener un cliente existente
            $stmt = $this->db->query("SELECT id_cliente FROM t_cliente LIMIT 1");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['id_cliente'] : 1;
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
            // Si hay error, intentar obtener un emprendedor existente
            $stmt = $this->db->query("SELECT id_emprededor FROM t_emprendedor LIMIT 1");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['id_emprededor'] : 1;
        }
    }

    private function eliminarPedido($pedidoId)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM t_pedidos WHERE id_pedidos = ?");
            $stmt->execute([$pedidoId]);
        } catch (PDOException $e) {
            // Ignorar errores de eliminación
        }
    }

    private function limpiarDatosTest()
    {
        try {
            // Limpiar deliveries de prueba
            $stmt = $this->db->prepare("
                DELETE FROM t_delivery 
                WHERE direccion_exacta LIKE 'Calle Test%' 
                   OR destinatario LIKE 'Destinatario Test%'
                   OR correo_destinatario LIKE 'test%@test.com'
            ");
            $stmt->execute();

            // Limpiar pedidos de prueba (solo los que no tienen deliveries asociados)
            $stmt = $this->db->prepare("
                DELETE FROM t_pedidos 
                WHERE id_pedidos NOT IN (SELECT DISTINCT fk_pedido FROM t_delivery)
                AND fk_cliente IN (SELECT id_cliente FROM t_cliente WHERE cedula LIKE 'CLI%')
            ");
            $stmt->execute();

            // Limpiar clientes de prueba
            $stmt = $this->db->prepare("
                DELETE FROM t_cliente 
                WHERE cedula LIKE 'CLI%' 
                AND id_cliente NOT IN (SELECT DISTINCT fk_cliente FROM t_pedidos)
            ");
            $stmt->execute();

            // Limpiar emprendedores de prueba
            $stmt = $this->db->prepare("
                DELETE FROM t_emprendedor 
                WHERE cedula LIKE 'EMP%'
            ");
            $stmt->execute();

        } catch (PDOException $e) {
            // No fallar la prueba por errores de limpieza
            error_log('Error limpiando datos test: ' . $e->getMessage());
        }
    }

    protected function tearDown(): void
    {
        $this->deliveryModel = null;
        $this->db = null;
    }
}