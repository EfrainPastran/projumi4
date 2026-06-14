<?php
namespace App;

use PDOException;
use Exception;

class Model {
    protected $db;
    protected $connectionKey = 'default'; // conexión por defecto

    /**
     * Abre la conexión a la base de datos, si no está abierta
     */
    protected function openConnection(): void {
        if (!$this->db) {
            $this->db = Database::getInstance($this->connectionKey);
            //error_log("Conexión '{$this->connectionKey}' abriendo conexion");
        }
    }

    /**
     * Cierra la conexión a la base de datos
     */
    public function closeConnection(): void {
        if ($this->db) {
            Database::close($this->connectionKey);
            $this->db = null;
        }
    }

    /**
     * Ejecuta una consulta SQL segura
     */
    public function query(string $sql, array $params = []) {
        $this->openConnection(); // asegura que hay conexión activa
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Error en consulta: {$e->getMessage()} | SQL: {$sql}");
            throw new Exception("Error en la operación de base de datos");
        }
    }

    /**
     * Devuelve el último ID insertado
     */
    public function lastInsertId(): string {
        $this->openConnection();
        return $this->db->lastInsertId();
    }

    /**
     * Inicia una transacción
     */
    public function beginTransaction(): void {
        $this->openConnection();
        $this->db->beginTransaction();
    }

    /**
     * Confirma una transacción
     */
    public function commit(): void {
        $this->openConnection();
        $this->db->commit();
    }

    /**
     * Revierte una transacción
     */
    public function rollBack(): void {
        $this->openConnection();
        $this->db->rollBack();
    }

    /**
     * Ejecuta una consulta con bloqueo de filas FOR UPDATE
     */
    public function queryForUpdate(string $sql, array $params = []) {
        return $this->query($sql . ' FOR UPDATE', $params);
    }

    /**
     * Ejecuta una consulta con bloqueo de lectura en modo compartido
     */
    public function queryLockInShareMode(string $sql, array $params = []) {
        return $this->query($sql . ' LOCK IN SHARE MODE', $params);
    }

    /**
     * Bloquea tablas explícitamente en la conexión actual.
     * Ejemplo: ['t_producto' => 'WRITE', 't_pedidos' => 'READ']
     */
    public function lockTables(array $tables): void {
        $this->openConnection();
        $parts = [];
        foreach ($tables as $table => $mode) {
            $mode = strtoupper(trim($mode));
            if (!in_array($mode, ['READ', 'WRITE'], true)) {
                throw new Exception("Modo de bloqueo inválido: {$mode}");
            }
            $parts[] = "`{$table}` {$mode}";
        }
        $this->db->exec('LOCK TABLES ' . implode(', ', $parts));
    }

    /**
     * Desbloquea todas las tablas.
     */
    public function unlockTables(): void {
        $this->openConnection();
        $this->db->exec('UNLOCK TABLES');
    }

    /**
     * Obtiene la instancia de PDO (para casos específicos)
     */
    public function getDb() {
        $this->openConnection();
        return $this->db;
    }
}