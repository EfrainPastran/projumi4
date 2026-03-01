<?php
namespace App\Models;

use App\Model;
use App\ValidadorTrait;
use PDO;
use Exception;

class CategoriasModel extends Model {
    use ValidadorTrait;
    // Esta clase usa la conexión "projumi"
    protected $connectionKey = 'projumi';

    private $id;
    private $nombre;
    private $descripcion;
    private $estatus;

    public function setData($id, $nombre, $descripcion, $estatus): array
    {
        $this->errores = []; // limpiar errores previos

        // Validaciones
        if (!empty($id) && !is_numeric($id)) {
            $this->errores['id'] = "El ID debe ser numérico.";
        }

        $resultadoNombre = $this->validarTexto($nombre, 'nombre', 3, 100);
        if ($resultadoNombre !== true) {
            $this->errores['nombre'] = $resultadoNombre;
        }

        $resultadoDesc = $this->validarDescripcion($descripcion, 'descripción', 3, 255);
        if ($resultadoDesc !== true) {
            $this->errores['descripcion'] = $resultadoDesc;
        }

        $resultadoEstatus = $this->validarStatus($estatus);
        if ($resultadoEstatus !== true) {
            $this->errores['estatus'] = $resultadoEstatus;
        }

        // Si hay errores, no asignar nada y devolverlos
        if (!$this->sinErrores()) {
            return [
                'success' => false,
                'errors' => $this->obtenerErrores()
            ];
        }

        // Si todo es válido, asignar los valores
        $this->id = $id;
        $this->nombre = trim($nombre);
        $this->descripcion = trim($descripcion);
        $this->estatus = trim($estatus);

        return ['success' => true];
    }

    public function setId($id): array {
        $this->errores = [];
        // Validaciones
        if (empty($id) || !is_numeric($id)) {
            $this->errores['id'] = "El ID debe ser numérico.";
        }
        // Si hay errores, no asignar nada y devolverlos
        if (!$this->sinErrores()) {
            return [
                'success' => false,
                'errors' => $this->obtenerErrores()
            ];
        }
        $this->id = $id;
        // Si todo es válido, asignar los valores
        return ['success' => true];
    }
    // === MÉTODOS CRUD ===

    public function getCategorias() {
        $stmt = $this->query("SELECT * FROM t_categoria");
        //error_log("Conexión '{$this->connectionKey}' consultando categorias y devolviendo categorias para cerrar.");
        $this->closeConnection();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByName(string $nombre) {
        $stmt = $this->query(
            "SELECT * FROM t_categoria WHERE nombre = :nombre LIMIT 1",
            [':nombre' => $nombre]
        );
        $this->closeConnection();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function registerCategoria(): array {
        try {
            if (empty($this->nombre)) {
                return ['success' => false, 'message' => 'El nombre de la categoría es obligatorio.'];
            }

            if (strlen($this->nombre) > 100) {
                return ['success' => false, 'message' => 'El nombre no puede superar los 100 caracteres.'];
            }

            if (!in_array((string)$this->estatus, ['1', '0'])) {
                return ['success' => false, 'message' => 'El estatus debe ser 1 o 0.'];
            }

            // Verificar duplicado
            $exists = $this->getByName($this->nombre);
            if ($exists) {
                return ['success' => false, 'message' => 'Ya existe una categoría con ese nombre.'];
            }

            // Insertar
            $this->query(
                "INSERT INTO t_categoria (nombre, descripcion, estatus)
                 VALUES (:nombre, :descripcion, :estatus)",
                [
                    ':nombre' => $this->nombre,
                    ':descripcion' => $this->descripcion,
                    ':estatus' => $this->estatus
                ]
            );
            $this->closeConnection();

            return [
                'success' => true,
                'message' => 'Categoría registrada correctamente.',
                'categoria_id' => $this->lastInsertId()
            ];
        } catch (Exception $e) {
            error_log("Error en registerCategoria: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al registrar la categoría.'];
        }
    }

    public function updateCategoria(): array {
        try {
            if (empty($this->id) || !is_numeric($this->id)) {
                return ['success' => false, 'message' => 'El ID es inválido.'];
            }

            $this->query(
                "UPDATE t_categoria
                 SET nombre = :nombre, descripcion = :descripcion, estatus = :estatus
                 WHERE id_categoria = :id",
                [
                    ':id' => $this->id,
                    ':nombre' => $this->nombre,
                    ':descripcion' => $this->descripcion,
                    ':estatus' => $this->estatus
                ]
            );
            $this->closeConnection();

            return ['success' => true, 'message' => 'Categoría actualizada correctamente.'];

        } catch (Exception $e) {
            error_log("Error en updateCategoria: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al actualizar la categoría.'];
        }
    }

    public function delete(): array {
        try {

            // Verificar existencia
            $stmt = $this->query(
                "SELECT id_categoria, nombre FROM t_categoria WHERE id_categoria = :id",
                [':id' => $this->id]
            );
            $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$categoria) {
                return ['success' => false, 'message' => 'La categoría no existe.'];
            }

            // Verificar relaciones
            $stmt = $this->query(
                "SELECT COUNT(*) AS total FROM t_producto WHERE fk_categoria = :id",
                [':id' => $this->id]
            );
            $relacion = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($relacion && $relacion['total'] > 0) {
                return ['success' => false, 'message' => 'No se puede eliminar: tiene productos asociados.'];
            }

            // Eliminar
            $this->query("DELETE FROM t_categoria WHERE id_categoria = :id", [':id' => $this->id]);
            return ['success' => true, 'message' => 'Categoría eliminada correctamente.'];

            $this->closeConnection();
        } catch (Exception $e) {
            error_log("Error en deleteCategoria: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al eliminar la categoría.'];
        }
    }

    public function existeCategoria($id_categoria): bool {
        $stmt = $this->query(
            "SELECT COUNT(*) FROM t_categoria WHERE id_categoria = :id AND estatus = 1",
            [':id' => $id_categoria]
        );
        $this->closeConnection();
        return $stmt->fetchColumn() > 0;
    }
}
