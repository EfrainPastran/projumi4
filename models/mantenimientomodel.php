<?php
namespace App\Models;
use PDO;
use PDOException;
use Exception;
use InvalidArgumentException;
use RuntimeException;
use App\Model;
class mantenimientomodel extends Model {

 // Declara las propiedades para las conexiones
   // private $db;         // Conexión a la base de datos principal
    private $db_projumi; // Conexión a la base de datos Projumi

    public function __construct() {
        try {
            // Conexión a la base de datos principal
            $this->db = new PDO(DB_DSN, DB_USER, DB_PASS, DB_OPTIONS);
            
            // Conexión a la base de datos Projumi
            $this->db_projumi = new PDO(DB_DSN_PROJUMI, DB_USER, DB_PASS, DB_OPTIONS);
            
            // Configurar ambos PDO para lanzar excepciones
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db_projumi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
        } catch (PDOException $e) {
            error_log('Error de conexión: ' . $e->getMessage());
            throw new Exception('Error al conectar con la base de datos');
        }
    }


public function generateBackup($database = 'main') {
    // ... (Validación y conexión, sin cambios) ...
    $db = ($database === 'projumi') ? $this->db_projumi : $this->db;
    
    // Configuración recomendada para prevenir problemas de codificación y memoria
    $db->exec("SET NAMES 'utf8'"); 
    $db->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL); 

    try {
        // Obtener todas las tablas
        $allTables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($allTables)) {
            throw new RuntimeException("No se encontraron tablas en la base de datos {$database}");
        }

        // 1. Definir el orden de las tablas principales que deben crearse primero
        // T_ROL es padre de T_USUARIO. T_USUARIO es padre de T_BITACORA.
        // Las tablas que no están en esta lista se procesarán alfabéticamente después.
        $orderedTables = [
            't_rol',       // Padre de t_usuario
            't_permisos',  // Padre de t_permiso_rol_modulo
            't_modulo',    // Padre de t_permiso_rol_modulo
            't_usuario',   // Padre de t_bitacora, t_mantenimiento, t_notificacion, t_personal
        ];

        // Crear la lista de tablas a procesar: primero las ordenadas, luego las demás.
        $tables = array_unique(array_merge(
            array_intersect($orderedTables, $allTables),
            array_diff($allTables, $orderedTables)
        ));
        // Nota: array_intersect solo incluye las de $orderedTables que existen.
        // array_diff añade el resto de tablas que no estaban en $orderedTables.
        
        // 2. Iniciar el script
        $sqlScript = "-- Backup de la base de datos " . strtoupper($database) . "\n";
        $sqlScript .= "-- Generado el: " . date('Y-m-d H:i:s') . "\n\n";
        
        // --- 3. DESHABILITAR RESTRICCIONES DE LLAVES FORÁNEAS (Importante para datos) ---
        $sqlScript .= "SET FOREIGN_KEY_CHECKS = 0;\n\n"; 
        
        // 4. Generar ESTRUCTURAS y DATOS en el orden deseado
        foreach ($tables as $table) {
            // ... (Lógica de obtención de estructura SHOW CREATE TABLE `$table`) ...
            $createResult = $db->query("SHOW CREATE TABLE `$table`");
            if (!$createResult) {
                error_log("No se pudo obtener estructura para la tabla {$table}");
                continue;
            }
            
            $createTable = $createResult->fetch(PDO::FETCH_ASSOC);
            $createTableSQL = $createTable['Create Table'] ?? null;
            
            if (!$createTableSQL) {
                error_log("Estructura no válida para la tabla {$table}");
                continue;
            }
            
            // Estructura de la tabla (CREATE TABLE)
            $sqlScript .= "\n\n-- --------------------------------------------------------\n";
            $sqlScript .= "-- Estructura para tabla `$table`\n";
            $sqlScript .= "DROP TABLE IF EXISTS `$table`;\n";
            $sqlScript .= $createTableSQL . ";\n\n";
            
            // Datos de la tabla (INSERT INTO)
            $rows = $db->query("SELECT * FROM `$table`", PDO::FETCH_NUM)->fetchAll();
            
            if (!empty($rows)) {
                $sqlScript .= "-- Datos para la tabla `$table`\n";
                
                // Preparar INSERTs
                $columnsResult = $db->query("SHOW COLUMNS FROM `$table`");
                $columns = $columnsResult->fetchAll(PDO::FETCH_COLUMN); 
                
                $chunks = array_chunk($rows, 100); 
                
                foreach ($chunks as $chunk) {
                    $sqlScript .= "INSERT INTO `$table` (`" . implode('`,`', $columns) . "`) VALUES \n";
                    
                    $values = [];
                    foreach ($chunk as $row) {
                        $rowValues = array_map(function($value) use ($db) {
                            if ($value === null) {
                                return 'NULL';
                            }
                            return $db->quote($value); 
                        }, $row); 
                        
                        $values[] = "(" . implode(',', $rowValues) . ")";
                    }
                    
                    $sqlScript .= implode(",\n", $values) . ";\n";
                }
            }
        }
        
        // --- 5. HABILITAR RESTRICCIONES DE LLAVES FORÁNEAS ---
        $sqlScript .= "\nSET FOREIGN_KEY_CHECKS = 1;\n";

        // ... (Lógica de creación de directorio y guardado de archivo, sin cambios) ...
        $backupDir = 'backups';
        if (!file_exists($backupDir)) {
            if (!mkdir($backupDir, 0755, true)) {
                throw new RuntimeException("No se pudo crear el directorio de backups: {$backupDir}");
            }
        }
        
        $backupFile = $backupDir . '/backup_' . $database . '_' . date('Y-m-d_H-i-s') . '.sql';
        
        if (file_put_contents($backupFile, $sqlScript) === false) {
            throw new RuntimeException("No se pudo escribir el archivo de backup en: {$backupFile}");
        }
        
        return $backupFile;
    } catch (PDOException $e) {
        error_log("Error en backup (" . $database . "): " . $e->getMessage());
        throw new RuntimeException("Error al generar el backup: " . $e->getMessage());
    } catch (Exception $e) {
        error_log("Error general en backup: " . $e->getMessage());
        throw $e;
    }
}

private function appendTableBackup(PDO $db, string $table, string $whereClause = '', array $params = []): string {
    $sql = "\n\n-- --------------------------------------------------------\n";
    $sql .= "-- Estructura para tabla `{$table}`\n";

    $createResult = $db->query("SHOW CREATE TABLE `{$table}`");
    if (!$createResult) {
        error_log("No se pudo obtener estructura para la tabla {$table}");
        return '';
    }

    $createTable = $createResult->fetch(PDO::FETCH_ASSOC);
    $createTableSQL = $createTable['Create Table'] ?? null;
    if (!$createTableSQL) {
        error_log("Estructura no válida para la tabla {$table}");
        return '';
    }

    $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
    $sql .= $createTableSQL . ";\n\n";

    $query = "SELECT * FROM `{$table}`";
    if ($whereClause !== '') {
        $query .= " WHERE {$whereClause}";
    }

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_NUM);

    if (!empty($rows)) {
        $sql .= "-- Datos para la tabla `{$table}`\n";
        $columnsResult = $db->query("SHOW COLUMNS FROM `{$table}`");
        $columns = $columnsResult->fetchAll(PDO::FETCH_COLUMN);

        $chunks = array_chunk($rows, 100);
        foreach ($chunks as $chunk) {
            $sql .= "INSERT INTO `{$table}` (`" . implode('`,`', $columns) . "`) VALUES \n";
            $values = [];
            foreach ($chunk as $row) {
                $rowValues = array_map(function($value) use ($db) {
                    if ($value === null) {
                        return 'NULL';
                    }
                    return $db->quote($value);
                }, $row);
                $values[] = "(" . implode(',', $rowValues) . ")";
            }
            $sql .= implode(",\n", $values) . ";\n";
        }
    }

    return $sql;
}

public function generatePartialBackup($database = 'projumi', string $dateFrom, string $dateTo) {
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
        throw new InvalidArgumentException('Las fechas deben tener el formato YYYY-MM-DD.');
    }

    if ($dateFrom > $dateTo) {
        throw new InvalidArgumentException('La fecha inicial no puede ser mayor que la fecha final.');
    }

    $db = ($database === 'projumi') ? $this->db_projumi : $this->db;
    $db->exec("SET NAMES 'utf8'");
    $db->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL);

    try {
        $sqlScript = "-- Backup parcial de la base de datos " . strtoupper($database) . "\n";
        $sqlScript .= "-- Rango: {$dateFrom} a {$dateTo}\n";
        $sqlScript .= "-- Generado el: " . date('Y-m-d H:i:s') . "\n\n";
        $sqlScript .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        $tables = [
            't_pedidos' => 'DATE(fecha_pedido) BETWEEN :from AND :to',
            't_detalle_pedido' => 'pedidos_ID_PEDIDO IN (SELECT id_pedidos FROM t_pedidos WHERE DATE(fecha_pedido) BETWEEN :from AND :to)',
            't_delivery' => 'fk_pedido IN (SELECT id_pedidos FROM t_pedidos WHERE DATE(fecha_pedido) BETWEEN :from AND :to)',
            't_envio' => 'fk_pedido IN (SELECT id_pedidos FROM t_pedidos WHERE DATE(fecha_pedido) BETWEEN :from AND :to)',
            't_pagos' => 'fk_pedido IN (SELECT id_pedidos FROM t_pedidos WHERE DATE(fecha_pedido) BETWEEN :from AND :to)',
            't_detalle_pago' => 'fk_pago IN (SELECT id_pagos FROM t_pagos WHERE fk_pedido IN (SELECT id_pedidos FROM t_pedidos WHERE DATE(fecha_pedido) BETWEEN :from AND :to))',
            't_bitacora' => 'DATE(fecha_registro) BETWEEN :from AND :to',
        ];

        foreach ($tables as $table => $where) {
            $sqlScript .= $this->appendTableBackup($db, $table, $where, [':from' => $dateFrom, ':to' => $dateTo]);
        }

        $sqlScript .= "\nSET FOREIGN_KEY_CHECKS = 1;\n";

        $backupDir = 'backups';
        if (!file_exists($backupDir)) {
            if (!mkdir($backupDir, 0755, true)) {
                throw new RuntimeException("No se pudo crear el directorio de backups: {$backupDir}");
            }
        }

        $backupFile = $backupDir . '/backup_partial_' . $database . '_' . $dateFrom . '_to_' . $dateTo . '.sql';
        if (file_put_contents($backupFile, $sqlScript) === false) {
            throw new RuntimeException("No se pudo escribir el archivo de backup parcial en: {$backupFile}");
        }

        return $backupFile;
    } catch (PDOException $e) {
        error_log("Error en backup parcial (" . $database . "): " . $e->getMessage());
        throw new RuntimeException("Error al generar el backup parcial: " . $e->getMessage());
    }
}

public function generateScheduledBackup($database = 'projumi') {
    $weekday = (int) date('N');
    if ($weekday === 5) {
        return $this->generateBackup($database);
    }

    $today = date('Y-m-d');
    return $this->generatePartialBackup($database, $today, $today);
}

public function restoreDatabase($filePath, $database = 'main') {
    // Validar parámetro de base de datos
    if (!in_array($database, ['main', 'projumi'])) {
        throw new InvalidArgumentException("Base de datos no válida. Use 'main' o 'projumi'");
    }

    // Seleccionar la conexión adecuada
    $db = ($database === 'projumi') ? $this->db_projumi : $this->db;
    
    if (!file_exists($filePath)) {
        throw new RuntimeException("El archivo SQL para la restauración no existe: {$filePath}");
    }

    try {
        // Leer todo el contenido del archivo SQL
        $sqlContent = file_get_contents($filePath);
        
        // La clave es dividir el script en sentencias individuales (separadas por ;)
        // Usamos una expresión regular simple para esto, manejando posibles espacios en blanco.
        $statements = array_filter(array_map('trim', explode(';', $sqlContent)));

        // Iniciar transacción (opcional pero recomendado para restauración)
        $db->beginTransaction();
        
        // Ejecutar cada sentencia SQL
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                // Ejecutar la consulta. PDO::exec() es ideal para comandos que no devuelven resultados (CREATE, INSERT, DROP, etc.)
                $db->exec($statement); 
            }
        }
        
        // Si todo salió bien, confirmar la transacción
        $db->commit();

        return true;
    } catch (PDOException $e) {
        // En caso de error, deshacer todos los cambios
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        error_log("Error de restauración ({$database}): " . $e->getMessage());
        throw new RuntimeException("Error al restaurar la base de datos {$database}: " . $e->getMessage());
    } catch (Exception $e) {
        // Otros errores, como problemas de lectura de archivos
        throw $e;
    }
}


}
