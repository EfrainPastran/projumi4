<?php
use PHPUnit\Framework\TestCase;
use App\Models\empresaEnvioModel;

class EmpresaEnvioModelTest extends TestCase
{
    private $empresaModel;

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

        $this->empresaModel = new empresaEnvioModel();
    }

    // === PRUEBAS PARA registerEmpresaEnvio() ===

    public function testRegistroNombreVacio()
    {
        $this->empresaModel->setData(null, '', '1234567890', 'Calle Test', 1);
        $res = $this->empresaModel->registerEmpresaEnvio();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('nombre', strtolower($res['message']));
    }

    public function testRegistroTelefonoVacio()
    {
        $this->empresaModel->setData(null, 'Empresa Test', '', 'Calle Test', 1);
        $res = $this->empresaModel->registerEmpresaEnvio();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('teléfono', strtolower($res['message']));
    }

    public function testRegistroDireccionVacia()
    {
        $this->empresaModel->setData(null, 'Empresa Test', '1234567890', '', 1);
        $res = $this->empresaModel->registerEmpresaEnvio();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('dirección', strtolower($res['message']));
    }

    public function testRegistroEstatusVacio()
    {
        $this->empresaModel->setData(null, 'Empresa Test', '1234567890', 'Calle Test', '');
        $res = $this->empresaModel->registerEmpresaEnvio();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('estatus', strtolower($res['message']));
    }

    public function testRegistroNombreDuplicado()
    {
        // Primero crear una empresa
        $nombre = 'Empresa Test Duplicado ' . uniqid();
        $this->empresaModel->setData(null, $nombre, '1234567890', 'Calle Test 1', 1);
        $this->empresaModel->registerEmpresaEnvio();

        // Intentar crear otra con el mismo nombre
        $this->empresaModel->setData(null, $nombre, '0987654321', 'Calle Test 2', 1);
        $res = $this->empresaModel->registerEmpresaEnvio();
        
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('ya existe', strtolower($res['message']));
        
        $this->limpiarEmpresasTest($nombre);
    }

    public function testRegistroEmpresaExitosa()
    {
        $nombre = 'Empresa Test ' . uniqid();
        $this->empresaModel->setData(null, $nombre, '1234567890', 'Calle Test 123', 1);
        $res = $this->empresaModel->registerEmpresaEnvio();
        
        $this->assertTrue($res['success']);
        $this->assertStringContainsString('correctamente', strtolower($res['message']));
        
        $this->limpiarEmpresasTest($nombre);
    }

    // === PRUEBAS PARA update() ===

    public function testUpdateIdVacio()
    {
        $this->empresaModel->setData('', 'Empresa Test', '1234567890', 'Calle Test', 1);
        $res = $this->empresaModel->update();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('id', strtolower($res['message']));
    }

    public function testUpdateIdNoNumerico()
    {
        $this->empresaModel->setData('abc', 'Empresa Test', '1234567890', 'Calle Test', 1);
        $res = $this->empresaModel->update();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('numérico', strtolower($res['message']));
    }

    public function testUpdateEmpresaInexistente()
    {
        $this->empresaModel->setData(99999, 'Empresa Test', '1234567890', 'Calle Test', 1);
        $res = $this->empresaModel->update();
        $this->assertFalse($res['success']);
    }

    public function testUpdateNombreDuplicadoEnOtraEmpresa()
    {
        // Crear dos empresas
        $nombre1 = 'Empresa Test 1 ' . uniqid();
        $nombre2 = 'Empresa Test 2 ' . uniqid();
        
        $this->empresaModel->setData(null, $nombre1, '1111111111', 'Calle Test 1', 1);
        $this->empresaModel->registerEmpresaEnvio();
        $idEmpresa1 = $this->obtenerIdEmpresaPorNombre($nombre1);
        
        $this->empresaModel->setData(null, $nombre2, '2222222222', 'Calle Test 2', 1);
        $this->empresaModel->registerEmpresaEnvio();
        $idEmpresa2 = $this->obtenerIdEmpresaPorNombre($nombre2);

        // Intentar cambiar el nombre de la segunda empresa al de la primera
        $this->empresaModel->setData($idEmpresa2, $nombre1, '2222222222', 'Calle Test 2', 1);
        $res = $this->empresaModel->update();
        
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('ya existe', strtolower($res['message']));
        
        $this->limpiarEmpresasTest([$nombre1, $nombre2]);
    }

    public function testUpdateMismoNombreExitoso()
    {
        $nombre = 'Empresa Test Update ' . uniqid();
        
        // Crear empresa
        $this->empresaModel->setData(null, $nombre, '1234567890', 'Calle Test', 1);
        $this->empresaModel->registerEmpresaEnvio();
        $idEmpresa = $this->obtenerIdEmpresaPorNombre($nombre);

        // Actualizar manteniendo el mismo nombre
        $this->empresaModel->setData($idEmpresa, $nombre, '0987654321', 'Nueva Dirección', 1);
        $res = $this->empresaModel->update();
        
        $this->assertTrue($res['success']);
        $this->assertStringContainsString('actualizada', strtolower($res['message']));
        
        $this->limpiarEmpresasTest($nombre);
    }

    // === PRUEBAS PARA delete() ===

    public function testDeleteIdVacio()
    {
        $this->empresaModel->setData('', 'Empresa Test', '1234567890', 'Calle Test', 1);
        $res = $this->empresaModel->delete();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('id', strtolower($res['message']));
    }

    public function testDeleteIdNoNumerico()
    {
        $this->empresaModel->setData('abc', 'Empresa Test', '1234567890', 'Calle Test', 1);
        $res = $this->empresaModel->delete();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('numérico', strtolower($res['message']));
    }

    public function testDeleteIdNegativo()
    {
        $this->empresaModel->setData(-1, 'Empresa Test', '1234567890', 'Calle Test', 1);
        $res = $this->empresaModel->delete();
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('positivo', strtolower($res['message']));
    }

    public function testDeleteEmpresaInexistente()
    {
        $this->empresaModel->setData(99999, 'Empresa Test', '1234567890', 'Calle Test', 1);
        $res = $this->empresaModel->delete();
        $this->assertFalse($res['success']);
    }

    public function testDeleteEmpresaExitosa()
    {
        // Crear empresa para eliminar
        $nombre = 'Empresa Test Delete ' . uniqid();
        $this->empresaModel->setData(null, $nombre, '1234567890', 'Calle Test', 1);
        $this->empresaModel->registerEmpresaEnvio();
        $idEmpresa = $this->obtenerIdEmpresaPorNombre($nombre);

        $this->empresaModel->setData($idEmpresa, null, null, null, null);
        $res = $this->empresaModel->delete();
        
        $this->assertTrue($res['success']);
        $this->assertStringContainsString('eliminada', strtolower($res['message']));
    }

    // === PRUEBAS PARA getById() ===

    public function testGetByIdExistente()
    {
        $nombre = 'Empresa Test Get ' . uniqid();
        $this->empresaModel->setData(null, $nombre, '1234567890', 'Calle Test', 1);
        $this->empresaModel->registerEmpresaEnvio();
        $idEmpresa = $this->obtenerIdEmpresaPorNombre($nombre);

        $empresa = $this->empresaModel->getById($idEmpresa);
        
        $this->assertIsArray($empresa);
        $this->assertEquals($nombre, $empresa['nombre']);
        
        $this->limpiarEmpresasTest($nombre);
    }

    public function testGetByIdInexistente()
    {
        $empresa = $this->empresaModel->getById(99999);
        $this->assertFalse($empresa);
    }

    // === PRUEBAS PARA getByName() ===

    public function testGetByNameExistente()
    {
        $nombre = 'Empresa Test Name ' . uniqid();
        $this->empresaModel->setData(null, $nombre, '1234567890', 'Calle Test', 1);
        $this->empresaModel->registerEmpresaEnvio();

        $empresa = $this->empresaModel->getByName($nombre);
        
        $this->assertIsArray($empresa);
        $this->assertEquals($nombre, $empresa['nombre']);
        
        $this->limpiarEmpresasTest($nombre);
    }

    public function testGetByNameInexistente()
    {
        $empresa = $this->empresaModel->getByName('Empresa Inexistente');
        $this->assertFalse($empresa);
    }

    // === PRUEBAS PARA getAll() ===

    public function testGetAllEmpresas()
    {
        $empresas = $this->empresaModel->getAll();
        $this->assertIsArray($empresas);
    }

    // === MÉTODOS AUXILIARES ===

    private function obtenerIdEmpresaPorNombre($nombre)
    {
        try {
            $db = new PDO(DB_DSN_PROJUMI, DB_USER, DB_PASS, DB_OPTIONS);
            $sql = 'SELECT id_empresa_envio FROM t_empresa_envio WHERE nombre = :nombre LIMIT 1';
            $stmt = $db->prepare($sql);
            $stmt->execute([':nombre' => $nombre]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? $resultado['id_empresa_envio'] : null;
        } catch (PDOException $e) {
            return null;
        }
    }

    private function limpiarEmpresasTest($nombres)
    {
        try {
            $db = new PDO(DB_DSN_PROJUMI, DB_USER, DB_PASS, DB_OPTIONS);
            if (is_array($nombres)) {
                foreach ($nombres as $nombre) {
                    $stmt = $db->prepare("DELETE FROM t_empresa_envio WHERE nombre = ?");
                    $stmt->execute([$nombre]);
                }
            } else {
                $stmt = $db->prepare("DELETE FROM t_empresa_envio WHERE nombre = ?");
                $stmt->execute([$nombres]);
            }
        } catch (PDOException $e) {
            // Ignorar errores de limpieza
        }
    }

    protected function tearDown(): void
    {
        $this->empresaModel = null;
    }
}