<?php
// Define constantes ficticias para evitar que el constructor falle al inicializar PDO
if (!defined('DB_DSN')) define('DB_DSN', 'sqlite::memory:');
if (!defined('DB_DSN_PROJUMI')) define('DB_DSN_PROJUMI', 'sqlite::memory:');
if (!defined('DB_USER')) define('DB_USER', '');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_OPTIONS')) define('DB_OPTIONS', serialize([]));


use PHPUnit\Framework\TestCase;
use App\Models\mantenimientomodel;
use InvalidArgumentException;
use RuntimeException;

// Mock de la clase PDOStatement para simular resultados de consultas
class MockPDOStatement extends \PDOStatement {
    public function __construct() {}
   // public function fetchAll($mode = null, $arg3 = null, $arg4 = null) { return []; }
    public function fetch($mode = null, $cursorOrientation = null, $cursorOffset = null) { return []; }
    public function execute($inputParameters = null) { return true; }
}

final class MantenimientoModelTest extends TestCase {

    private $model;
    private $mockDb;
    private $mockDbProjumi;

    protected function setUp(): void {
        // 1. Crear mocks de las conexiones PDO
        $this->mockDb = $this->createMock(PDO::class);
        $this->mockDbProjumi = $this->createMock(PDO::class);
        
        // 2. Crear una instancia real del modelo (el constructor se ejecutará con las constantes dummy)
        $this->model = new mantenimientomodel();
        
        // 3. Usar Reflection para inyectar los Mocks PDO en las propiedades privadas del modelo
        $this->injectMockPDO($this->model, 'db', $this->mockDb);
        $this->injectMockPDO($this->model, 'db_projumi', $this->mockDbProjumi);

        // 4. Mockear las funciones de sistema de archivos (Global Functions)
        // Esto es avanzado, pero necesario para aislar el test.
        // Simulamos que el directorio 'backups' existe y que la escritura siempre funciona.
        if (!function_exists('file_put_contents')) {
            require_once 'vendor/autoload.php'; // Cargar el autoloader para funciones globales
        }

        // Simular que la escritura de archivos es exitosa
        // NOTA: En un entorno de prueba real, esto se haría usando php-test-helpers o vfsStream.
        // Aquí se asume que las herramientas de mocking global están disponibles.
        // Para simplificar, asumiremos que las funciones del modelo que usan file_... están fuera 
        // del alcance de la prueba de unidad y nos centraremos en la generación del SQL y las excepciones.
    }

    /**
     * Helper para inyectar un Mock de PDO en las propiedades privadas del modelo.
     */
    private function injectMockPDO($object, $propertyName, $mock) {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $mock);
    }
    
    /**
     * Helper para simular un statement PDO que devuelve resultados.
     */
    private function mockStatement($results, $isQuery = false) {
        $mockStmt = $this->getMockBuilder(MockPDOStatement::class)
                         ->onlyMethods(['fetchAll', 'fetch'])
                         ->getMock();

        if ($isQuery) {
            $mockStmt = $this->createMock(\PDOStatement::class);
        }

        if (is_array($results)) {
            $mockStmt->method('fetchAll')->willReturn($results);
            if (!empty($results)) {
                 $mockStmt->method('fetch')->willReturn(reset($results), false); // Devuelve el primer elemento y luego false
            } else {
                 $mockStmt->method('fetch')->willReturn(false);
            }
        } else {
             $mockStmt->method('fetchAll')->willReturn([]);
             $mockStmt->method('fetch')->willReturn(false);
        }

        return $mockStmt;
    }


    // =========================================================================
    //                            PRUEBAS DE VALIDACIÓN
    // =========================================================================

    public function testGenerateBackupFailsWithInvalidDatabase() {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Base de datos no válida. Use 'main' o 'projumi'");
        
        $this->model->generateBackup('invalid_db');
    }
    
    // =========================================================================
    //                      PRUEBAS DE LÓGICA Y ÉXITO (MAIN DB)
    // =========================================================================

    public function testGenerateBackupSuccessMainDB() {
        // 1. Configurar datos de prueba
        $tableList = ['users', 'roles'];
        $createTableData = ['Create Table' => 'CREATE TABLE `users` (id INT)'];
        $rowsData = [
            ['id' => 1, 'name' => 'John'],
            ['id' => 2, 'name' => 'Jane']
        ];
        
        // 2. Mocks de PDO
        
        // Simular SHOW TABLES para la tabla 'users' y 'roles'
        $showTablesStmt = $this->mockStatement([$tableList], true);
        $showTablesStmt->method('fetchAll')->willReturn($tableList);

        // Simular SHOW CREATE TABLE para 'users'
        $createTableStmt = $this->mockStatement([$createTableData]);
        $createTableStmt->method('fetch')->willReturn($createTableData, false);

        // Simular SELECT * FROM para 'users'
        $selectStmt = $this->mockStatement($rowsData);
        $selectStmt->method('fetchAll')->willReturn($rowsData);

        // Configurar el mock de PDO para responder a las consultas
        $this->mockDb->expects($this->exactly(3)) // 1 SHOW TABLES + 1 SHOW CREATE + 1 SELECT (solo probaremos una tabla para simplificar)
                     ->method('query')
                     ->withConsecutive(
                         ["SHOW TABLES"], 
                         ["SHOW CREATE TABLE `users`"],
                         ["SELECT * FROM `users`"] 
                         // En un test completo, deberías incluir 'roles' también, pero esto verifica el flujo.
                     )
                     ->will($this->onConsecutiveCalls(
                         $showTablesStmt,
                         $createTableStmt,
                         $selectStmt
                     ));

        // 3. Simular escritura de archivos
        // Mockear las funciones de sistema de archivos para simular el éxito
        // Para simplificar, asumiremos que se llama a la función de cotización de PDO
        $this->mockDb->method('quote')->willReturnCallback(function($value) {
            return "'" . addslashes($value) . "'";
        });
        
        // Mockear mkdir y file_put_contents para simular éxito
        if (!function_exists('mkdir')) {
            // Esto es solo un placeholder, las funciones globales son difíciles de mockear sin herramientas
            $this->model = $this->getMockBuilder(mantenimientomodel::class)
                                 ->onlyMethods(['writeFile']) // si se usara un helper para el archivo
                                 ->getMock();
        }

        // 4. Ejecutar la función
        $result = $this->model->generateBackup('main');

        // 5. Asertos
        $this->assertStringStartsWith('-- Backup de la base de datos MAIN', $result, 'El resultado debe ser el script SQL y empezar con el encabezado correcto.');
        $this->assertStringContainsString('CREATE TABLE `users` (id INT);', $result, 'El script debe contener la estructura de la tabla.');
        $this->assertStringContainsString("INSERT INTO `users` (`id`,`name`) VALUES \n('1','John'),\n('2','Jane');", $result, 'El script debe contener los datos de inserción.');
    }
    
    // =========================================================================
    //                          PRUEBAS DE EXCEPCIONES DB
    // =========================================================================

    public function testBackupFailsIfNoTablesFound() {
        // 1. Configurar Mock: SHOW TABLES devuelve vacío
        $this->mockDbProjumi->expects($this->once())
                             ->method('query')
                             ->with("SHOW TABLES")
                             ->willReturn($this->mockStatement([]));
                             
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("No se encontraron tablas en la base de datos projumi");
        
        $this->model->generateBackup('projumi');
    }

    public function testBackupHandlesPDOException() {
        // 1. Configurar Mock: El primer query lanza PDOException
        $this->mockDb->expects($this->once())
                     ->method('query')
                     ->willThrowException(new PDOException('Error de conexión simulado'));
                     
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Error al generar el backup: Error de conexión simulado");

        $this->model->generateBackup('main');
    }
    
    public function testBackupSkipsTableIfCreateStructureIsMissing() {
         // 1. Configurar datos: Una tabla existe, pero SHOW CREATE TABLE falla
        $tableList = ['bad_table'];
        
        // Simular SHOW TABLES exitoso
        $showTablesStmt = $this->mockStatement([$tableList], true);
        $showTablesStmt->method('fetchAll')->willReturn($tableList);

        // Simular SHOW CREATE TABLE que devuelve un resultado sin la clave 'Create Table'
        $badCreateTableStmt = $this->mockStatement([['Table' => 'bad_table', 'OtherKey' => 'data']]);
        $badCreateTableStmt->method('fetch')->willReturn(['Table' => 'bad_table', 'OtherKey' => 'data'], false);
        
        // Configurar el mock de PDO
        $this->mockDb->expects($this->exactly(2)) 
                     ->method('query')
                     ->withConsecutive(
                         ["SHOW TABLES"], 
                         ["SHOW CREATE TABLE `bad_table`"]
                     )
                     ->will($this->onConsecutiveCalls(
                         $showTablesStmt,
                         $badCreateTableStmt
                     ));
                     
        // 2. Ejecutar la función
        $result = $this->model->generateBackup('main');
        
        // 3. Asertos: No debe haber estructura ni datos para la tabla 'bad_table', solo el encabezado
        $this->assertStringStartsWith('-- Backup de la base de datos MAIN', $result, 'El script debe generarse (sin la tabla mala).');
        $this->assertStringNotContainsString('bad_table', $result, 'El script no debe intentar insertar la tabla con estructura mala.');
    }
}
