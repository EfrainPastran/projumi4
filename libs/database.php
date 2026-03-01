<?php
// listo
namespace App;

use PDO;
use PDOException;
use Exception;

class Database {
    private static $instances = [];

    private function __construct() {} // Evita instanciación directa

    /**
     * Obtiene una instancia PDO de la base de datos indicada
     * @param string $key  Identificador de la conexión ("default" o "projumi")
     * @return PDO
     * @throws Exception
     */
    public static function getInstance(string $key = 'default'): PDO {
        // Si ya existe la conexión, la reutilizamos
        if (isset(self::$instances[$key])) {
            return self::$instances[$key];
        }

        try {
            // Determinar a qué base de datos conectar
            switch ($key) {
                case 'projumi':
                    $dsn = DB_DSN_PROJUMI;
                    break;
                default:
                    $dsn = DB_DSN;
                    break;
            }

            $pdo = new PDO($dsn, DB_USER, DB_PASS, DB_OPTIONS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec("SET NAMES utf8mb4");

            // Guardar instancia
            self::$instances[$key] = $pdo;
            return $pdo;

        } catch (PDOException $e) {
            error_log("Error de conexión ($key): " . $e->getMessage());
            throw new Exception("No se pudo conectar a la base de datos '$key'");
        }
    }

    /**
     * Cierra una conexión específica
     */
    public static function close(string $key = 'default'): void {
        if (isset(self::$instances[$key])) {
            //error_log("Conexión '$key' cerrada correctamente.");
            self::$instances[$key] = null;
            unset(self::$instances[$key]);
        }else {
            //error_log("Conexión '$key' ya estaba cerrada o no existía.");
        }
    }

    /**
     * Cierra todas las conexiones abiertas
     */
    public static function closeAll(): void {
        foreach (array_keys(self::$instances) as $key) {
            self::close($key);
        }
    }
}
