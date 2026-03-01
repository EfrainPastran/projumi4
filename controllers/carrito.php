<?php
use App\Models\carritoModel;
use App\Models\productosModel;
use App\Models\metodoModel;
use App\Models\empresaEnvioModel;
use App\Middleware;
function index() {
    if (!isset($_SESSION['user'])) {
        header('Location: ../home/index');
        exit;
    }
    $MetodoPago = new MetodoModel();
    $EmpresaEnvio = new EmpresaEnvioModel();
    $metodos = $MetodoPago->obtenerMetodos();
    $empresas = $EmpresaEnvio->getAll();
    $cedula = $_SESSION['user']['cedula'];
    $middleware = new Middleware();
    $tipoUsuario = $middleware->verificarTipoUsuario($cedula);
    render('carrito/index', ['metodos' => $metodos, 'empresas' => $empresas, 'tipoUsuario' => $tipoUsuario]);
}

//Funcion para cargar los productos del carrito
function cargarCarrito()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    header('Content-Type: application/json');

    if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
        echo json_encode([
            'success' => true,
            'carrito' => array_values($_SESSION['carrito']) 
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'carrito' => []
        ]);
    }
}

//Funcion para agregar un producto al carrito
function agregar()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405); // Método no permitido
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        return;
    }

    // Leer el cuerpo de la petición (esperamos solo el ID y cantidad)
    $input = json_decode(file_get_contents('php://input'), true);

    // Validar los datos
    if (
        !$input ||
        !isset($input['id']) ||
        !is_numeric($input['id']) ||
        !isset($input['cantidad']) ||
        !is_numeric($input['cantidad']) ||
        $input['cantidad'] <= 0
    ) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
        return;
    }

    // Obtener los datos del producto
    $idProducto = (int)$input['id'];
    $cantidad = (int)$input['cantidad'];

    // Iniciar sesión si no está activa
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    // Cargar el modelo de productos
    $productoModel = new ProductosModel();

    // Obtener el producto desde la BD
    $producto = $productoModel->getProducto($idProducto);

    if (!$producto) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
        return;
    }

    // Validar stock (opcional)
    if ((int)$producto['stock'] < $cantidad) {
        echo json_encode([
            'success' => false,
            'message' => 'Stock insuficiente. Solo quedan ' . $producto['stock'] . ' unidades.'
        ]);
        return;
    }

    // Preparar el carrito
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    $id = $producto['id_producto'];
    $nombre = $producto['nombre'];
    $precio = $producto['precio'];
    $imagen = $producto['imagen'];
    $categoria = $producto['categoria'];
    $descripcion = $producto['descripcion'];
    $stock = $producto['stock'];

    // Agregar o actualizar en el carrito
    if (isset($_SESSION['carrito'][$id])) {
        $_SESSION['carrito'][$id]['cantidad'] += $cantidad;
    } else {
        $_SESSION['carrito'][$id] = [
            'id' => $id,
            'name' => $nombre,
            'price' => $precio,
            'category' => $categoria,
            'description' => $descripcion,
            'image' => $imagen,
            'stock' => $stock,
            'cantidad' => $cantidad
        ];
    }
    // Respuesta con el resultado
    echo json_encode([
        'success' => true,
        'message' => 'Producto agregado al carrito correctamente',
        'carrito' => $_SESSION['carrito']
    ]);
}

//metodo para vaciar el carrito
function vaciar()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    // Verificar si el carrito está vacío
    if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
        echo json_encode([
            'success' => false,
            'message' => 'El carrito ya está vacío'
        ]);
        return;
    }

    // Vaciar el carrito
    unset($_SESSION['carrito']);

    echo json_encode([
        'success' => true,
        'message' => 'Carrito vaciado correctamente'
    ]);
}   

//Funcion para eliminar un producto del carrito
function eliminar()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['id']) || !is_numeric($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID de producto inválido']);
        return;
    }

    $id = (int)$input['id'];

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    // Verificar si existe en la BD
    $productoModel = new ProductosModel();
    $producto = $productoModel->getProducto($id);

    if (!$producto) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'El producto no existe en la base de datos'
        ]);
        return;
    }

    // Verificar si existe en el carrito
    if (!isset($_SESSION['carrito'][$id])) {
        echo json_encode([
            'success' => false,
            'message' => 'Producto no encontrado en el carrito'
        ]);
        return;
    }

    unset($_SESSION['carrito'][$id]);

    echo json_encode([
        'success' => true,
        'message' => 'Producto eliminado del carrito',
        'carrito' => $_SESSION['carrito']
    ]);
}
