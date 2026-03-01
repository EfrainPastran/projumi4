<?php
use PHPUnit\Framework\TestCase;
use App\Models\eventosModel;

class EventosModelTest extends TestCase
{
    private $eventosModel;

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

        // SOLUCIÓN: Solo crear el modelo, NO crear PDO aquí
        $this->eventosModel = new eventosModel();
    }

    // === PRUEBAS PARA registerEventos() ===

    public function testRegistroFechaInicioVacia()
    {
        $this->eventosModel->setData('', '2024-12-05', 'Evento Test', 'Calle Test', 1, null);
        $res = $this->eventosModel->registerEventos();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('fecha de inicio', strtolower($res['message']));
    }

    public function testRegistroFechaFinVacia()
    {
        $this->eventosModel->setData('2024-12-01', '', 'Evento Test', 'Calle Test', 1, null);
        $res = $this->eventosModel->registerEventos();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('fecha de fin', strtolower($res['message']));
    }

    public function testRegistroNombreVacio()
    {
        $this->eventosModel->setData('2024-12-01', '2024-12-05', '', 'Calle Test', 1, null);
        $res = $this->eventosModel->registerEventos();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('nombre', strtolower($res['message']));
    }

    public function testRegistroDireccionVacia()
    {
        $this->eventosModel->setData('2024-12-01', '2024-12-05', 'Evento Test', '', 1, null);
        $res = $this->eventosModel->registerEventos();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('dirección', strtolower($res['message']));
    }

    public function testRegistroStatusVacio()
    {
        $this->eventosModel->setData('2024-12-01', '2024-12-05', 'Evento Test', 'Calle Test', '', null);
        $res = $this->eventosModel->registerEventos();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('status', strtolower($res['message']));
    }

    public function testRegistroFechaInicioMayorQueFin()
    {
        $this->eventosModel->setData('2024-12-10', '2024-12-05', 'Evento Test', 'Calle Test', 1, null);
        $res = $this->eventosModel->registerEventos();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('mayor', strtolower($res['message']));
    }

    public function testRegistroNombreDuplicado()
    {
        // Primero crear un evento
        $nombre = 'Evento Test Duplicado ' . uniqid();
        $this->eventosModel->setData('2024-12-01', '2024-12-05', $nombre, 'Calle Test 1', 1);
        $this->eventosModel->registerEventos();

        // Intentar crear otro con el mismo nombre
        $this->eventosModel->setData('2024-12-02', '2024-12-06', $nombre, 'Calle Test 2', 1);
        $res = $this->eventosModel->registerEventos();
        
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('ya existe', strtolower($res['message']));
        
        $this->limpiarEventosTest($nombre);
    }

     public function testRegistroEventoExitoso()
     {
         $nombre = 'Evento Test ' . uniqid();
         $this->eventosModel->setData('2024-12-01', '2024-12-05', $nombre, 'Calle Test 123', 1);
         $res = $this->eventosModel->registerEventos();
        
         $this->assertTrue($res['success']);
         $this->assertStringContainsString('correctamente', strtolower($res['message']));
        
        $this->limpiarEventosTest($nombre);
     }

    // === PRUEBAS PARA update() ===

    public function testUpdateIdVacio()
    {
        $this->eventosModel->setData('2024-12-01', '2024-12-05', 'Evento Test', 'Calle Test', 1, '');
        $res = $this->eventosModel->update();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('id', strtolower($res['message']));
    }

    // public function testUpdateEventoInexistente()
    // {
    //     $this->eventosModel->setData(99999, '2024-12-01', '2024-12-05', 'Evento Test', 'Calle Test', 1);
    //     $res = $this->eventosModel->update();
    //     $this->assertFalse($res['success']);
    // }

    public function testUpdateNombreDuplicadoEnOtroEvento()
    {
        // Crear dos eventos
        $nombre1 = 'Evento Test 1 ' . uniqid();
        $nombre2 = 'Evento Test 2 ' . uniqid();
        
        $this->eventosModel->setData('2024-12-01', '2024-12-05', $nombre1, 'Calle Test 1', 1);
        $this->eventosModel->registerEventos();
        $idEvento1 = $this->obtenerIdEventoPorNombre($nombre1);
        
        $this->eventosModel->setData('2024-12-02', '2024-12-06', $nombre2, 'Calle Test 2', 1);
        $this->eventosModel->registerEventos();
        $idEvento2 = $this->obtenerIdEventoPorNombre($nombre2);

        // Intentar cambiar el nombre del segundo evento al del primero
        $this->eventosModel->setData( '2024-12-02', '2024-12-06', $nombre1, 'Calle Test 2', 1, $idEvento2);
        $res = $this->eventosModel->update();
        
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('ya existe', strtolower($res['message']));
        
        $this->limpiarEventosTest([$nombre1, $nombre2]);
    }

    // public function testUpdateMismoNombreExitoso()
    // {
    //     $nombre = 'Evento Test Update ' . uniqid();
        
    //     // Crear evento
    //     $this->eventosModel->setData(null, '2024-12-01', '2024-12-05', $nombre, 'Calle Test', 1);
    //     $this->eventosModel->registerEventos();
    //     $idEvento = $this->obtenerIdEventoPorNombre($nombre);

    //     // Actualizar manteniendo el mismo nombre
    //     $this->eventosModel->setData($idEvento, '2024-12-02', '2024-12-06', $nombre, 'Nueva Dirección', 0);
    //     $res = $this->eventosModel->update();
        
    //     $this->assertTrue($res['success']);
    //     $this->assertStringContainsString('actualizado', strtolower($res['message']));
        
    //     $this->limpiarEventosTest($nombre);
    // }

    // === PRUEBAS PARA delete() ===

    public function testDeleteIdVacio()
    {
        $this->eventosModel->setData('2024-12-01', '2024-12-05', 'Evento Test', 'Calle Test', 1, '');
        $res = $this->eventosModel->delete();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('id', strtolower($res['message']));
    }

    public function testDeleteIdNoNumerico()
    {
        $this->eventosModel->setData('2024-12-01', '2024-12-05', 'Evento Test', 'Calle Test', 1, 'abc');
        $res = $this->eventosModel->delete();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('numérico', strtolower($res['message']));
    }

    public function testDeleteIdNegativo()
    {
        $this->eventosModel->setData('2024-12-01', '2024-12-05', 'Evento Test', 'Calle Test', 1, -1);
        $res = $this->eventosModel->delete();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('positivo', strtolower($res['message']));
    }

    public function testDeleteEventoInexistente()
    {
        $this->eventosModel->setData('2024-12-01', '2024-12-05', 'Evento Test', 'Calle Test', 1, 99999);
        $res = $this->eventosModel->delete();
        $this->assertFalse($res['success']);
    }

    public function testDeleteEventoExitoso()
    {
        // Crear evento para eliminar
        $nombre = 'Evento Test Delete ' . uniqid();
        $this->eventosModel->setData('2024-12-01', '2024-12-05', $nombre, 'Calle Test', 1);
        $this->eventosModel->registerEventos();
        $idEvento = $this->obtenerIdEventoPorNombre($nombre);

        $this->eventosModel->setData(null, null, null, null, null, $idEvento);
        $res = $this->eventosModel->delete();
        
        $this->assertTrue($res['success']);
        $this->assertStringContainsString('eliminado', strtolower($res['message']));
    }

    // === PRUEBAS PARA obtenerEvento() ===

    public function testObtenerEventoExistente()
    {
        $nombre = 'Evento Test Obtener ' . uniqid();
        $this->eventosModel->setData('2024-12-01', '2024-12-05', $nombre, 'Calle Test', 1);
        $this->eventosModel->registerEventos();
        $idEvento = $this->obtenerIdEventoPorNombre($nombre);

        $evento = $this->eventosModel->obtenerEvento($idEvento);
        
        $this->assertIsArray($evento);
        $this->assertEquals($nombre, $evento['nombre']);
        
        $this->limpiarEventosTest($nombre);
    }

    public function testObtenerEventoInexistente()
    {
        $evento = $this->eventosModel->obtenerEvento(99999);
        $this->assertFalse($evento);
    }

    // === PRUEBAS PARA getAll() ===

    public function testGetAllEventos()
    {
        $eventos = $this->eventosModel->getAll();
        $this->assertIsArray($eventos);
    }

    // === MÉTODOS AUXILIARES ===

    private function obtenerIdEventoPorNombre($nombre)
    {
        try {
            // Usar una conexión temporal solo para consultas auxiliares
            $db = new PDO(DB_DSN_PROJUMI, DB_USER, DB_PASS, DB_OPTIONS);
            $sql = 'SELECT id_eventos FROM t_evento WHERE nombre = :nombre LIMIT 1';
            $stmt = $db->prepare($sql);
            $stmt->execute([':nombre' => $nombre]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? $resultado['id_eventos'] : null;
        } catch (PDOException $e) {
            return null;
        }
    }

    private function limpiarEventosTest($nombres)
    {
        try {
            // Usar una conexión temporal solo para limpieza
            $db = new PDO(DB_DSN_PROJUMI, DB_USER, DB_PASS, DB_OPTIONS);
            if (is_array($nombres)) {
                foreach ($nombres as $nombre) {
                    $stmt = $db->prepare("DELETE FROM t_evento WHERE nombre = ?");
                    $stmt->execute([$nombre]);
                }
            } else {
                $stmt = $db->prepare("DELETE FROM t_evento WHERE nombre = ?");
                $stmt->execute([$nombres]);
            }
        } catch (PDOException $e) {
            // Ignorar errores de limpieza
        }
    }

    protected function tearDown(): void
    {
        $this->eventosModel = null;
    }
}