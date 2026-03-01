<?php
use App\Models\permisosModel;
use App\Models\categoriasModel;
use App\Models\ProductosModel;
use App\Models\bitacoraModel;
use App\Models\EmprendedorModel;
use App\Middleware;
use App\Models\Usermodel;
    function index() {
        if (!isset($_SESSION['user'])) {
            header('Location: ../home/index');
            exit;
        }
        $Categoria = new categoriasModel();
        // Obtener las categorías
        $categorias = $Categoria->getCategorias();
        $cedula = $_SESSION['user']['cedula'];
        $middleware = new Middleware();
        $tipo = $middleware->verificarTipoUsuario($cedula);
        render('productos/index' , ['categorias' => $categorias, 'tipo' => $tipo]);

    }

    function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');

            try {
                // === Instanciar modelos ===
                $Producto = new ProductosModel();
                $Usuario = new UserModel();
                $Emprendedor = new EmprendedorModel();
                $Bitacora = new BitacoraModel();


                if (!isset($_SESSION['user']['id_usuario'])) {
                    throw new Exception('Sesión no válida o expirada.');
                }

                $idUsuario = $_SESSION['user']['id_usuario'];
                $cedulaUsuario = $Usuario->obtenerCedulaPorId($idUsuario);

                if (!$cedulaUsuario) {
                    throw new Exception('No se encontró un usuario válido.');
                }

                // === Obtener emprendedor vinculado al usuario ===
                $idEmprendedor = $Emprendedor->obtenerIdEmprendedorPorRif($cedulaUsuario);
                if (!$idEmprendedor) {
                    throw new Exception('No se encontró un emprendedor activo vinculado a este usuario.');
                }

                // === Procesar imágenes del producto principal ===
                $imagenesRutaFinal = procesarImagenes('imagenes');

                // === Setear datos al modelo ===
                $validaciones = $Producto->setData(
                    null,
                    $_POST['nombre'] ?? '',
                    $_POST['precio'] ?? 0,
                    $_POST['stock'] ?? 0,
                    $_POST['descripcion'] ?? '',
                    $_POST['status'] ?? 'inactivo',
                    $_POST['id_categoria'] ?? null,
                    $idEmprendedor
                );

                // Validar campos antes de registrar
                if (!$validaciones['success']) {
                    // Si los errores vienen en array, los concatenamos
                    $mensajeError = is_array($validaciones['errors'])
                        ? implode("\n", $validaciones['errors'])
                        : $validaciones['errors'];

                    echo json_encode([
                        'success' => false,
                        'message' => $mensajeError
                    ]);
                    exit;
                }

                // === Registrar producto principal ===
                $resultado = $Producto->registerProduc($imagenesRutaFinal);
                if (!$resultado['success']) {
                    echo json_encode($resultado);
                    exit;
                }

                // === Registrar producto por porción (si aplica) ===
                if (!empty($_POST['es_porcion']) && $_POST['es_porcion'] === 'on') {
                    $nombrePorcion = $_POST['nombre'] . " Porción";
                    $precioPorcion = $_POST['precio_porcion'] ?? 0;
                    $cantidadPorciones = $_POST['cantidad_porciones'] ?? 0;

                    $imagenesPorcionRutaFinal = procesarImagenes('imagenes_porcion');

                    $ProductoPorcion = new ProductosModel();
                    $validaciones =$ProductoPorcion->setData(
                        null,
                        $nombrePorcion,
                        $precioPorcion,
                        $cantidadPorciones,
                        $_POST['descripcion'] ?? '',
                        $_POST['status'] ?? 'inactivo',
                        $_POST['id_categoria'] ?? null,
                        $idEmprendedor
                    );

                    // Validar campos antes de registrar
                    if (!$validaciones['success']) {
                        // Si los errores vienen en array, los concatenamos
                        $mensajeError = is_array($validaciones['errors'])
                            ? implode("\n", $validaciones['errors'])
                            : $validaciones['errors'];

                        echo json_encode([
                            'success' => false,
                            'message' => $mensajeError
                        ]);
                        exit;
                    }

                    $resultadoPorcion = $ProductoPorcion->registerProduc($imagenesPorcionRutaFinal);

                    if (!$resultadoPorcion['success']) {
                        echo json_encode($resultadoPorcion);
                        exit;
                    }
                }

                // === Registrar en bitácora ===
                $fecha = date('Y-m-d H:i:s');
                $Bitacora->setData(
                    'Registro de producto',
                    'El usuario ha registrado un nuevo producto: ' . $_POST['nombre'],
                    $fecha,
                    $idUsuario,
                    null
                );
                $Bitacora->registrarBitacora();

                // === Respuesta final ===
                echo json_encode([
                    'success' => true,
                    'message' => '¡Registro exitoso!',
                    'id_producto' => $resultado['id_producto'] ?? null
                ]);
                exit;

            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }
        } else {
            // Si no es POST, podría redirigir o mostrar un error.
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
            exit;
        }
    }

    function producto() {
        $Categoria = new categoriasModel();
        $Producto = new ProductosModel();

        $categorias = $Categoria->getCategorias();
        $productos = $Producto->getProductos();

        $cedula = $_SESSION['user']['cedula'];
        $Middleware = new Middleware();
        $tipoUsuario = $Middleware->verificarTipoUsuario($cedula);

        $idModuloProductos = 2; //Asignación del id del módulo en este caso de "Productos" en t_modulo
        
        // Obtenemos los permisos que el Middleware ya guardó en la sesión
        // Esto devolverá algo como ['consultar', 'registrar']
        $listaPermisos = $_SESSION['user']['rol']['permisos'][$idModuloProductos] ?? [];

        // Lo convertimos a un formato más cómodo para la vista: ['registrar' => true]
        $permisosMap = array_fill_keys($listaPermisos, true);

        if ('emprendedor' == $tipoUsuario[0]) {
            $title = "Pedidos solicitados";
            $menu = "headerEmprendedor";
        }
        else if ('cliente' == $tipoUsuario[0]) {
            $title = "Mis Pedidos";
            $menu = "headerCliente";
        }
        else {
            $title = "Gestión de Pedidos";
            $menu = "headeradmin";
        }

        render('productos/productos' , [
            'categorias' => $categorias,
            'productos'  => $productos, 
            'title'      => $title, 
            'menu'       => $menu,
            'permisos'   => $permisosMap // Pasamos el mapa de permisos
        ]);
    }

    function eliminarProducto()
    {
        $datos = json_decode(file_get_contents('php://input'), true);

        if (!isset($datos['id']) || !is_numeric($datos['id'])) {
            echo json_encode(['success' => false, 'message' => 'ID de producto no recibido o inválido.']);
            return;
        }

        $idProducto = (int)$datos['id'];

        $producto = new ProductosModel();
        $validaciones = $producto->set_id_producto($idProducto);

        if (!$validaciones['success']) {
            echo json_encode([
                'success' => false,
                'message' => $validaciones['errors']
            ]);
            exit;
        }

        // El modelo internamente valida si está en pedido, si existe, etc.
        $resultado = $producto->eliminarProducto();

        echo json_encode($resultado);
    }

    //Productos que se cargan en el catalago para el carrito
    function mostrarProductos() {
        $Producto = new ProductosModel();
        $productos = $Producto->getProductos();
        echo json_encode($productos);
        return;
    }

    //Mostrar productos del emprendedor
    function mostrarProductosPorEmprendedor() {
        
        $Producto = new ProductosModel();
        $cedula = $_SESSION['user']['cedula'];
        $Middleware = new Middleware();
        $tipoUsuario = $Middleware->verificarTipoUsuario($cedula);
        //Vista para el emprendedor
        if ('emprendedor' == $tipoUsuario[0]) {
            $productos = $Producto->getProductosPorEmprendedor($cedula);
        }
        else {
            $productos = $Producto->getProductos();
        }
        echo json_encode($productos);
        return;
    }

    // Productos que se cargan en el catálogo para el carrito por emprendedor
    function mostrarProductosEmprendedor() {
        if (!isset($_SESSION['user'])) {

        }else {
            $cedula = $_SESSION['user']['cedula'];
            $Middleware = new Middleware();
            $tipoUsuario = $Middleware->verificarTipoUsuario($cedula);
        }
        $model = new categoriasModel();
        $data = $model->getCategorias();
        $Producto = new ProductosModel();

        $id_emprendedor = filter_input(INPUT_GET, 'id_emprendedor', FILTER_VALIDATE_INT);
        $nombre_completo = filter_input(INPUT_GET, 'nombre_completo', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if ($id_emprendedor === false || $id_emprendedor === null) {
            echo json_encode(['error' => 'ID de emprendedor inválido']);
            return;
        }

        $productos = $Producto->getProductosPorIdEmprendedor($id_emprendedor);
        
        if ($productos === false) {
            echo json_encode(['error' => 'Error al obtener productos']);
        } else {
            render('productos/ProductosEmprendedor', [
                'productosEmprendedor' => $productos,
                'categorias' => $data,
                'nombre_completo' => $nombre_completo,
                'tipoUsuario' => $tipoUsuario
            ]);
        }
    }

    function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');

            try {
                $Producto = new ProductosModel();
                $Usuario = new UserModel();
                $Emprendedor = new EmprendedorModel();
                $Bitacora = new BitacoraModel();

                if (!isset($_SESSION['user']['id_usuario'])) {
                    throw new Exception('Sesión no válida o expirada.');
                }

                $idUsuario = $_SESSION['user']['id_usuario'];
                $cedulaUsuario = $Usuario->obtenerCedulaPorId($idUsuario);
                if (!$cedulaUsuario) {
                    throw new Exception('No se encontró un usuario válido.');
                }

                $idEmprendedor = $Emprendedor->obtenerIdEmprendedorPorRif($cedulaUsuario);
                if (!$idEmprendedor) {
                    throw new Exception('No se encontró un emprendedor activo vinculado a este usuario.');
                }

                // === Procesar imágenes si se enviaron ===
                $imagenesRutaFinal = [];
                if (isset($_FILES['imagen_edit'])) {
                    $imagenesRutaFinal = procesarImagenes('imagen_edit');
                }

                // === Configurar producto ===
                $validaciones = $Producto->setData(
                    $_POST['id_producto'] ?? null,
                    $_POST['nombre'] ?? '',
                    $_POST['precio'] ?? 0,
                    $_POST['stock'] ?? 0,
                    $_POST['descripcion'] ?? '',
                    $_POST['status'] ?? 0,
                    $_POST['id_categoria'] ?? null,
                    $idEmprendedor
                );

                // Validar campos antes de registrar
                if (!$validaciones['success']) {
                    // Si los errores vienen en array, los concatenamos
                    $mensajeError = is_array($validaciones['errors'])
                        ? implode("\n", $validaciones['errors'])
                        : $validaciones['errors'];

                    echo json_encode([
                        'success' => false,
                        'message' => $mensajeError
                    ]);
                    exit;
                }

                $resultado = $Producto->updateProduc($imagenesRutaFinal);

                if (!$resultado['success']) {
                    echo json_encode($resultado);
                    exit;
                }

                // === Registrar en bitácora ===
                $fecha = date('Y-m-d H:i:s');
                $Bitacora->setData(
                    'Actualización de producto',
                    'El usuario ha actualizado el producto: ' . $_POST['nombre'],
                    $fecha,
                    $idUsuario,
                    null
                );
                $Bitacora->registrarBitacora();

                echo json_encode(['success' => true, 'message' => '¡Producto actualizado exitosamente!']);
                exit;

            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }
        } else {
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
            exit;
        }
    }

    function procesarImagenes($inputName, $carpetaDestino = 'public/') {
        $imagenesRutaFinal = [];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

        if (isset($_FILES[$inputName])) {
            $imagenes = $_FILES[$inputName];

            for ($i = 0; $i < count($imagenes['name']); $i++) {
                if ($imagenes['error'][$i] === UPLOAD_ERR_OK) {
                    if (!in_array($imagenes['type'][$i], $allowedTypes)) {
                        throw new Exception('Tipo de imagen no permitido: ' . $imagenes['name'][$i]);
                    }

                    $imagenNombre = uniqid() . '_' . basename($imagenes['name'][$i]);
                    $rutaFinal = $carpetaDestino . $imagenNombre;

                    if (move_uploaded_file($imagenes['tmp_name'][$i], $rutaFinal)) {
                        $imagenesRutaFinal[] = $rutaFinal;
                    } else {
                        throw new Exception('Error al guardar imagen: ' . $imagenes['name'][$i]);
                    }
                }
            }
        }

        return $imagenesRutaFinal;
    }
