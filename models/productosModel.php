<?php
namespace App\Models;
use PDO;
use PDOException;
use Exception;
use App\ValidadorTrait;
use App\Model;
use App\Models\EmprendedorModel;
use App\Models\CategoriasModel;
class ProductosModel extends Model {
    use ValidadorTrait;
    protected $connectionKey = 'projumi';
    private $id_producto;
    private $nombre;
	private $precio;
	private $stock;
	private $descripcion;
	private $status;
    private $imagen;
    private $fk_emprendedor;
    private $fk_categoria;

    function setData($id_producto, $nombre, $precio, $stock, $descripcion, $status, $fk_categoria, $fk_emprendedor) {
        $this->errores = [];

        // Validaciones
        if (!empty($id_producto) && !is_numeric($id_producto)) {
            $this->errores['id_producto'] = "El ID debe ser numérico.";
        }

        $valNombre = $this->validarDescripcion($nombre, 'nombre', 3, 100);
        if ($valNombre !== true) $this->errores['nombre'] = $valNombre;

        $valDesc = $this->validarDescripcion($descripcion, 'descripción', 3, 255);
        if ($valDesc !== true) $this->errores['descripcion'] = $valDesc;

        $valPrecio = $this->validarDecimal($precio, 'precio', 0.50, 10000);
        if ($valPrecio !== true) $this->errores['precio'] = $valPrecio;

        $valStock = $this->validarNumerico($stock, 'stock', 1, 100);
        if ($valStock !== true) $this->errores['stock'] = $valStock;

        $valCategoria = $this->validarCodigoSelect($fk_categoria, 'categoría');
        if ($valCategoria !== true) $this->errores['categoria'] = $valCategoria;

        $valStatus = $this->validarStatus($status);
        if ($valStatus !== true) $this->errores['estatus'] = $valStatus;

        if (!$this->sinErrores()) {
            return [
                'success' => false,
                'errors' => $this->obtenerErrores()
            ];
        }

        // Asignación segura
        $this->id_producto = $id_producto;
        $this->nombre = $nombre;
        $this->precio = $precio;
        $this->stock = $stock;
        $this->descripcion = $descripcion;
        $this->status = $status;
        $this->fk_emprendedor = $fk_emprendedor;
        $this->fk_categoria = $fk_categoria;
        return ['success' => true];
    }

    function set_id_producto($id_producto){
        $errores = [];
        if (empty($id_producto) || !is_numeric($id_producto)) {
            $errores['id_producto'] = "El ID debe ser numérico.";
        }
        if (!$this->sinErrores()) {
            return [
                'success' => false,
                'errors' => $this->obtenerErrores()
            ];
        }
        $this->id_producto = $id_producto;
        return ['success' => true];
    }


    public function eliminarProducto()
    {
        try {
            if (empty($this->id_producto) || !is_numeric($this->id_producto)) {
                return ['success' => false, 'message' => 'El id del producto no es válido.'];
            }

            // Verificar existencia
            $stmtCheck = $this->query("SELECT id_producto FROM t_producto WHERE id_producto = :id", [
                ':id' => $this->id_producto
            ]);
            if ($stmtCheck->rowCount() === 0) {
                $this->closeConnection();
                return ['success' => false, 'message' => 'El producto que intenta eliminar no existe.'];
            }

            // Verificar pedidos
            if ($this->porDetallePedido($this->id_producto)) {
                $this->closeConnection();
                return ['success' => false, 'message' => 'No se puede eliminar este producto porque está en un pedido.'];
            }

            $this->openConnection();
            $this->beginTransaction();

            // Obtener rutas de imágenes
            $stmtImgs = $this->query("SELECT ruta FROM t_galeria WHERE fk_producto = :id", [
                ':id' => $this->id_producto
            ]);
            $imagenes = $stmtImgs->fetchAll(PDO::FETCH_COLUMN);

            foreach ($imagenes as $ruta) {
                if (!empty($ruta) && file_exists($ruta)) unlink($ruta);
            }

            // Eliminar galería y producto
            $this->query("DELETE FROM t_galeria WHERE fk_producto = :id", [':id' => $this->id_producto]);
            $this->query("DELETE FROM t_producto WHERE id_producto = :id", [':id' => $this->id_producto]);

            $this->commit();
            $this->closeConnection();
            return ['success' => true, 'message' => 'Producto eliminado correctamente.'];
        } catch (Exception $e) {
            $this->rollBack();
            return ['success' => false, 'message' => 'Error al eliminar producto: ' . $e->getMessage()];
        }
    }

    public function registerProduc($imagenes = [])
    {
        try {
            // Validaciones de claves foráneas
            if (empty($this->fk_categoria) || !is_numeric($this->fk_categoria)) {
                return ['success' => false, 'message' => 'Debe seleccionar una categoría válida.'];
            }
            if (empty($this->fk_emprendedor) || !is_numeric($this->fk_emprendedor)) {
                return ['success' => false, 'message' => 'Debe estar asociado a un emprendedor válido.'];
            }

            $Categoria = new CategoriasModel();
            if (!$Categoria->existeCategoria($this->fk_categoria)) {
                return ['success' => false, 'message' => 'La categoría especificada no existe o está inactiva.'];
            }

            $Emprendedor = new EmprendedorModel();
            if (!$Emprendedor->existeEmprendedor($this->fk_emprendedor)) {
                return ['success' => false, 'message' => 'El emprendedor especificado no existe o está inactivo.'];
            }

            if ($this->existeProductoConNombre($this->nombre, $this->fk_emprendedor)) {
                return ['success' => false, 'message' => 'Ya existe un producto con este nombre registrado por el mismo emprendedor.'];
            }

            $this->openConnection();
            $this->beginTransaction();

            $this->query("
                INSERT INTO t_producto (nombre, precio, stock, descripcion, status, fk_categoria, fk_emprendedor)
                VALUES (:nombre, :precio, :stock, :descripcion, :status, :fk_categoria, :fk_emprendedor)
            ", [
                ':nombre' => $this->nombre,
                ':precio' => $this->precio,
                ':stock' => $this->stock,
                ':descripcion' => $this->descripcion,
                ':status' => $this->status,
                ':fk_categoria' => $this->fk_categoria,
                ':fk_emprendedor' => $this->fk_emprendedor
            ]);

            $idProducto = $this->lastInsertId();

            // Insertar imágenes
            if (!empty($imagenes)) {
                foreach ($imagenes as $ruta) {
                    $this->query("INSERT INTO t_galeria (fk_producto, ruta) VALUES (:id, :ruta)", [
                        ':id' => $idProducto,
                        ':ruta' => $ruta
                    ]);
                }
            }

            $this->commit();
            $this->closeConnection();

            return [
                'success' => true,
                'message' => 'Producto registrado exitosamente.',
                'id_producto' => $idProducto
            ];
        } catch (Exception $e) {
            $this->rollBack();
            return ['success' => false, 'message' => 'Error al registrar producto: ' . $e->getMessage()];
        }
    }

    public function getProductos()
    {
        try {
            $stmt = $this->query("
                SELECT 
                    e.id_emprededor, 
                    CONCAT(u.nombre, ' ', u.apellido) AS emprendedor,
                    p.id_producto, 
                    p.nombre, 
                    p.precio, 
                    p.descripcion, 
                    p.stock,
                    p.fecha_ingreso, 
                    c.nombre AS categoria, 
                    c.id_categoria, 
                    p.status
                FROM " . BD_PROJUMI . ".t_producto p
                INNER JOIN " . BD_PROJUMI . ".t_categoria c ON p.fk_categoria = c.id_categoria
                INNER JOIN " . BD_PROJUMI . ".t_emprendedor e ON e.id_emprededor = p.fk_emprendedor
                INNER JOIN " . BD_SEGURIDAD . ".t_usuario u ON u.cedula = e.cedula
                ORDER BY p.fecha_ingreso DESC
            ");

            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Cargar imágenes asociadas
            foreach ($productos as &$producto) {
                $stmtImg = $this->query(
                    "SELECT ruta FROM t_galeria WHERE fk_producto = :id",
                    [':id' => $producto['id_producto']]
                );
                $producto['imagenes'] = $stmtImg->fetchAll(PDO::FETCH_COLUMN);
            }

            $this->closeConnection();
            return $productos;
        } catch (Exception $e) {
            error_log('Error en getProductos: ' . $e->getMessage());
            return false;
        }
    }

    public function getProducto()
    {
        try {
            $stmt = $this->query("
                SELECT 
                    e.id_emprededor, 
                    CONCAT(u.nombre, ' ', u.apellido) AS emprendedor, 
                    p.id_producto, 
                    p.nombre, 
                    p.precio, 
                    p.descripcion, 
                    p.stock, 
                    c.nombre AS categoria
                FROM t_producto p
                INNER JOIN t_categoria c ON p.fk_categoria = c.id_categoria
                INNER JOIN t_emprendedor e ON e.id_emprededor = p.fk_emprendedor
                INNER JOIN " . BD_SEGURIDAD . ".t_usuario u ON u.cedula = e.cedula
                WHERE p.id_producto = :id
            ", [':id' => $this->id_producto]);

            $this->closeConnection();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Error en getProducto: ' . $e->getMessage());
            return false;
        }
    }

    public function getAllProductos($dateFrom = null, $dateTo = null)
    {
        try {
            $query = "
                SELECT 
                    e.id_emprededor, 
                    e.emprendimiento AS emprendedor,
                    p.id_producto, 
                    p.nombre, 
                    p.precio, 
                    p.descripcion, 
                    p.stock, 
                    p.fecha_ingreso, 
                    p.status, 
                    c.id_categoria, 
                    c.nombre AS nombre_categoria, 
                    c.descripcion AS descripcion_categoria
                FROM " . BD_PROJUMI . ".t_producto p
                INNER JOIN " . BD_PROJUMI . ".t_categoria c ON p.fk_categoria = c.id_categoria
                INNER JOIN " . BD_PROJUMI . ".t_emprendedor e ON e.id_emprededor = p.fk_emprendedor
                INNER JOIN " . BD_SEGURIDAD . ".t_usuario u ON u.cedula = e.cedula
                WHERE 1 = 1
            ";

            $params = [];
            if ($dateFrom && $dateTo) {
                $query .= " AND DATE(p.fecha_ingreso) BETWEEN :dateFrom AND :dateTo";
                $params[':dateFrom'] = $dateFrom;
                $params[':dateTo'] = $dateTo;
            } elseif ($dateFrom) {
                $query .= " AND DATE(p.fecha_ingreso) >= :dateFrom";
                $params[':dateFrom'] = $dateFrom;
            } elseif ($dateTo) {
                $query .= " AND DATE(p.fecha_ingreso) <= :dateTo";
                $params[':dateTo'] = $dateTo;
            }

            $query .= " ORDER BY p.fecha_ingreso DESC";
            $stmt = $this->query($query, $params);

            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($productos as &$producto) {
                $stmtImg = $this->query("SELECT ruta FROM t_galeria WHERE fk_producto = :id", [
                    ':id' => $producto['id_producto']
                ]);
                $producto['imagenes'] = $stmtImg->fetchAll(PDO::FETCH_COLUMN);
            }

            $this->closeConnection();
            return $productos;
        } catch (Exception $e) {
            error_log('Error en getAllProductos: ' . $e->getMessage());
            return false;
        }
    }

    public function getProductosPorEmprendedor($cedula, $dateFrom = null, $dateTo = null)
    {
        try {
            $query = "
                SELECT 
                    e.id_emprededor, 
                    CONCAT(u.nombre, ' ', u.apellido) AS emprendedor,
                    p.id_producto, 
                    p.nombre, 
                    p.precio, 
                    p.descripcion, 
                    p.stock, 
                    p.fecha_ingreso, 
                    p.status, 
                    c.id_categoria, 
                    c.nombre AS nombre_categoria, 
                    c.descripcion AS descripcion_categoria
                FROM " . BD_PROJUMI . ".t_producto p
                INNER JOIN " . BD_PROJUMI . ".t_categoria c ON p.fk_categoria = c.id_categoria
                INNER JOIN " . BD_PROJUMI . ".t_emprendedor e ON e.id_emprededor = p.fk_emprendedor
                INNER JOIN " . BD_SEGURIDAD . ".t_usuario u ON u.cedula = e.cedula
                WHERE e.cedula = :cedula
            ";

            $params = [':cedula' => $cedula];
            if ($dateFrom && $dateTo) {
                $query .= " AND DATE(p.fecha_ingreso) BETWEEN :dateFrom AND :dateTo";
                $params[':dateFrom'] = $dateFrom;
                $params[':dateTo'] = $dateTo;
            } elseif ($dateFrom) {
                $query .= " AND DATE(p.fecha_ingreso) >= :dateFrom";
                $params[':dateFrom'] = $dateFrom;
            } elseif ($dateTo) {
                $query .= " AND DATE(p.fecha_ingreso) <= :dateTo";
                $params[':dateTo'] = $dateTo;
            }

            $query .= " ORDER BY p.fecha_ingreso DESC";
            $stmt = $this->query($query, $params);

            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($productos as &$producto) {
                $stmtImg = $this->query("SELECT ruta FROM t_galeria WHERE fk_producto = :id", [
                    ':id' => $producto['id_producto']
                ]);
                $producto['imagenes'] = $stmtImg->fetchAll(PDO::FETCH_COLUMN);
            }

            $this->closeConnection();
            return $productos;
        } catch (Exception $e) {
            error_log('Error en getProductosPorEmprendedor: ' . $e->getMessage());
            return false;
        }
    }

    public function getProductosPorIdEmprendedor($id)
    {
        try {
            $stmt = $this->query("
                SELECT 
                    e.id_emprededor, 
                    CONCAT(u.nombre, ' ', u.apellido) AS emprendedor,
                    p.id_producto, 
                    p.nombre, 
                    p.precio, 
                    p.descripcion, 
                    p.stock, 
                    p.fecha_ingreso, 
                    p.status, 
                    c.id_categoria, 
                    c.nombre AS nombre_categoria, 
                    c.descripcion AS descripcion_categoria
                FROM " . BD_PROJUMI . ".t_producto p
                INNER JOIN " . BD_PROJUMI . ".t_categoria c ON p.fk_categoria = c.id_categoria
                INNER JOIN " . BD_PROJUMI . ".t_emprendedor e ON e.id_emprededor = p.fk_emprendedor
                INNER JOIN " . BD_SEGURIDAD . ".t_usuario u ON u.cedula = e.cedula
                WHERE e.id_emprededor = :id
                ORDER BY p.fecha_ingreso DESC
            ", [':id' => $id]);

            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($productos as &$producto) {
                $stmtImg = $this->query("SELECT ruta FROM t_galeria WHERE fk_producto = :id", [
                    ':id' => $producto['id_producto']
                ]);
                $producto['imagenes'] = $stmtImg->fetchAll(PDO::FETCH_COLUMN);
            }

            $this->closeConnection();
            return $productos;
        } catch (Exception $e) {
            error_log('Error en getProductosPorIdEmprendedor: ' . $e->getMessage());
            return false;
        }
    }

    public function getProductosStockBajo($id)
    {
        try {
            $stmt = $this->query("
                SELECT 
                    p.id_producto, 
                    p.nombre, 
                    p.descripcion, 
                    p.stock
                FROM " . BD_PROJUMI . ".t_producto p
                INNER JOIN " . BD_PROJUMI . ".t_emprendedor e ON e.id_emprededor = p.fk_emprendedor
                INNER JOIN " . BD_SEGURIDAD . ".t_usuario u ON u.cedula = e.cedula
                WHERE u.id_usuario = :id 
                AND p.stock < 5 
                AND p.status = 1
                ORDER BY p.fecha_ingreso DESC
            ", [':id' => $id]);

            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->closeConnection();
            return $productos;
        } catch (Exception $e) {
            error_log('Error en getProductosStockBajo: ' . $e->getMessage());
            return false;
        }
    }
    
    public function updateProduc($imagenes = [])
    {
        try {
            // ===========================
            // Validaciones iniciales
            // ===========================
            if (empty($this->id_producto) || !is_numeric($this->id_producto)) {
                return ['success' => false, 'message' => 'El ID del producto es inválido.'];
            }

            // Verificar existencia del producto
            $stmtCheck = $this->query("SELECT * FROM t_producto WHERE id_producto = :id", [
                ':id' => $this->id_producto
            ]);

            $productoExistente = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            if (!$productoExistente) {
                $this->closeConnection();
                return ['success' => false, 'message' => 'El producto que intenta actualizar no existe.'];
            }

            // ===========================
            // Validaciones relacionales
            // ===========================
            $categoriaModel = new \App\Models\CategoriasModel();
            $emprendedorModel = new \App\Models\EmprendedorModel();

            if (!$categoriaModel->existeCategoria($this->fk_categoria)) {
                return ['success' => false, 'message' => 'La categoría seleccionada no existe o está inactiva.'];
            }

            if (!$emprendedorModel->existeEmprendedor($this->fk_emprendedor)) {
                return ['success' => false, 'message' => 'El emprendedor especificado no existe.'];
            }

            // ===========================
            // Validar duplicado
            // ===========================
            $stmtDup = $this->query("
                SELECT COUNT(*) 
                FROM t_producto 
                WHERE nombre = :nombre 
                AND fk_emprendedor = :fk_emprendedor 
                AND id_producto != :id_producto
            ", [
                ':nombre' => $this->nombre,
                ':fk_emprendedor' => $this->fk_emprendedor,
                ':id_producto' => $this->id_producto
            ]);

            if ($stmtDup->fetchColumn() > 0) {
                $this->closeConnection();
                return ['success' => false, 'message' => 'Ya existe un producto con este nombre.'];
            }

            // ===========================
            // Validar imágenes
            // ===========================
            if (!empty($imagenes)) {
                foreach ($imagenes as $img) {
                    if (empty($img) || !is_string($img)) {
                        return ['success' => false, 'message' => 'Alguna imagen proporcionada es inválida.'];
                    }
                }
            }

            // ===========================
            // Ejecución de la actualización
            // ===========================
            $this->beginTransaction();

            $this->query("
                UPDATE t_producto 
                SET nombre = :nombre, 
                    precio = :precio, 
                    stock = :stock, 
                    descripcion = :descripcion, 
                    status = :status, 
                    fk_categoria = :fk_categoria, 
                    fk_emprendedor = :fk_emprendedor
                WHERE id_producto = :id_producto
            ", [
                ':nombre' => $this->nombre,
                ':precio' => $this->precio,
                ':stock' => $this->stock,
                ':descripcion' => $this->descripcion,
                ':status' => $this->status,
                ':fk_categoria' => $this->fk_categoria,
                ':fk_emprendedor' => $this->fk_emprendedor,
                ':id_producto' => $this->id_producto
            ]);

            // ===========================
            // Actualización de imágenes
            // ===========================
            if (!empty($imagenes)) {
                $this->query("DELETE FROM t_galeria WHERE fk_producto = :id", [
                    ':id' => $this->id_producto
                ]);

                foreach ($imagenes as $ruta) {
                    $this->query("
                        INSERT INTO t_galeria (fk_producto, ruta) 
                        VALUES (:fk_producto, :ruta)
                    ", [
                        ':fk_producto' => $this->id_producto,
                        ':ruta' => $ruta
                    ]);
                }
            }

            $this->commit();
            $this->closeConnection();

            return [
                'success' => true,
                'message' => 'Producto actualizado correctamente.',
                'id_producto' => $this->id_producto
            ];

        } catch (Exception $e) {
            $this->rollBack();
            error_log('Error en updateProduc: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al actualizar producto: ' . $e->getMessage()];
        }
    }

    private function existeProductoConNombre($nombre, $fk_emprendedor, $id_actual = null)
    {
        try {
            $query = "
                SELECT id_producto 
                FROM t_producto 
                WHERE LOWER(nombre) = LOWER(:nombre)
                AND fk_emprendedor = :fk_emprendedor
            ";

            $params = [
                ':nombre' => $nombre,
                ':fk_emprendedor' => $fk_emprendedor
            ];

            if ($id_actual) {
                $query .= " AND id_producto != :id_actual";
                $params[':id_actual'] = $id_actual;
            }

            $stmt = $this->query($query, $params);
            $existe = $stmt->rowCount() > 0;

            $this->closeConnection();
            return $existe;
        } catch (Exception $e) {
            error_log('Error en existeProductoConNombre: ' . $e->getMessage());
            return false;
        }
    }

    public function porDetallePedido($idProducto)
    {
        try {
            $stmt = $this->query("
                SELECT COUNT(*) 
                FROM t_detalle_pedido 
                WHERE producto_ID_PRODUCTO = :id
            ", [':id' => $idProducto]);

            $count = $stmt->fetchColumn();
            $this->closeConnection();
            return $count > 0;
        } catch (Exception $e) {
            error_log('Error en porDetallePedido: ' . $e->getMessage());
            return false;
        }
    }
}