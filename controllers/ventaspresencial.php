<?php
use App\Models\MetodoModel;
use App\Models\MonedaModel;
use App\Models\ProductosModel;
use App\Models\VentaPresencialModel;
use App\Models\PedidoModel;
use App\Models\UsuariosModel;
use App\Models\ClienteModel;
use App\Middleware;
//Mostrar el modulo de venta presencial
function index() {
    if (!isset($_SESSION['user']['cedula'])) {
        header('Location: ../home/index');
        exit;
    }
    $cedula = $_SESSION['user']['cedula'];
    $middleware = new Middleware();
    $tipoUsuario = $middleware->verificarTipoUsuario($cedula);
    $MetodoPago = new MetodoModel();
    $metodos = $MetodoPago->obtenerMetodos();
    //Vista para el emprendedor
    if ('emprendedor' == $tipoUsuario[0] || 'super_usuario' == $tipoUsuario[0]) {
        $cliente =  new ClienteModel();
        $clientes = $cliente->getAll();
    }    
    else {
        header('Location: ../home/index');
        exit;
    }


    $rol = $_SESSION['user']['rol'];
    $permisos = $middleware->obtenerPermisosDinamicos($rol['rol'], 'Ventas presencial');

    if (!$permisos['consultar']) {
            header('Location: ../home/principal');
            exit;
    }


    render('ventas/presencial', [
           'metodos'  => $metodos,
           'clientes' => $clientes,
           'permisos' => $permisos
    ]);
}

/*function registrarUsuario() {
    try {
        $Usuario = new UsuariosModel(); // Asegúrate de que "usuario" coincida con tu modelo

        $Usuario->setData(
            NULL,
            $_POST['cedula'],
            $_POST['nombre'],
            $_POST['apellido'],
            $_POST['correo'],
            $_POST['password'],
            $_POST['direccion'],
            $_POST['telefono'],
            date('Y-m-d'),
            $_POST['fecha_nacimiento'],
            1, // Estatus activo
            3 //Rol de cliente
        );

        $validarUsuario = $Usuario->getUsuarioByCedula($_POST['cedula']);
        if ($validarUsuario) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['message' => 'El usuario ya existe']);
            return;
        }

        $Cliente = new ClienteModel();
        $Cliente->setData(
            null,
            $_POST['cedula'],
            date('Y-m-d'), 
            $_POST['estatus']
        );
        $Usuario->registerUsuario();
        $id_cliente = $Cliente->registerCliente();

        echo json_encode(['success' => true, 'id_cliente' => $id_cliente]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}*/

function monedasPorMetodo() {
    $idMetodo = intval($_GET['idMetodo']);
    $Moneda = new MonedaModel();
    try {
        $monedas = $Moneda->obtenerPorMetodo($idMetodo);

        if ($monedas) {
            echo json_encode([
                'status' => 'success',
                'data' => $monedas
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'No se encontraron monedas'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error al obtener las monedas: ' . $e->getMessage()
        ]);
    }
}

function consultarCliente() {
    header('Content-Type: application/json');
    $cedula = intval($_GET['cedula']);
    $Cliente = new ClienteModel();
    try {
        $data = $Cliente->getData($cedula);
        if (is_array($data) && !empty($data)) {
            echo json_encode([
                'status' => 'success',
                'data' => $data
            ]);
            exit;
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Cédula no encontrada, por favor llene los campos con los datos del cliente'
            ]);            
            exit;
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error al obtener la cedula: ' . $e->getMessage()
        ]);
        exit;
    }
}

function mostrarVentas() {
    $venta = new VentaPresencialModel();
    $ventas = $venta->getVentasPorEmprendedor($_SESSION['user']['cedula']);
    echo json_encode($ventas);
    return;
}

function detalleVenta()
{
    header('Content-Type: application/json');

    try {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id_pedido']) || empty($data['id_pedido'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Debe proporcionar el ID del pedido.']);
            return;
        }

        $idPedido = intval($data['id_pedido']);
        $Pedido = new PedidoModel();

        // Obtener datos del pedido y detalle
        $pedidoInfo = $Pedido->obtenerPedido($idPedido);
        
        $detalleProductos = $Pedido->obtenerDetallePedido($idPedido);

        // Validar existencia
        if (!$pedidoInfo) {
            http_response_code(404);
            echo json_encode(['error' => 'Pedido no encontrado.']);
            return;
        }

        if (!$detalleProductos || count($detalleProductos) === 0) {
            $detalleProductos = []; // Evita error en frontend
        }

        // Calcular totales
        $totalPedido = 0;
        foreach ($detalleProductos as &$producto) {
            $producto['total_producto'] = $producto['cantidad'] * $producto['precio_unitario'];
            $totalPedido += $producto['total_producto'];
        }

        // Estructura compatible con tu frontend
        $response = [
            'pedido' => [
                'id' => $pedidoInfo['id_pedidos'],
                'fecha' => $pedidoInfo['fecha_pedido'],
                'estatus' => $pedidoInfo['estatus'],
                'total' => $totalPedido,
                'cliente' => [
                    'id' => $pedidoInfo['id_cliente'],
                    'nombre' => $pedidoInfo['cliente_nombre'],
                    'apellido' => $pedidoInfo['cliente_apellido'],
                    'correo' => $pedidoInfo['correo'],
                ],
            ],
            'detalle_productos' => $detalleProductos
        ];

        http_response_code(200);
        echo json_encode($response);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
    }
}

function registrarVenta()
{
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No se recibieron datos.']);
        return;
    }

    if (empty($data['datos_cliente']) || empty($data['productos'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Datos incompletos para registrar la venta.']);
        return;
    }

    $clienteData = $data['datos_cliente'];
    $productos = $data['productos'];
    $metodos_pago = $data['metodos_pago'];

    $Cliente = new ClienteModel();

    // Registrar cliente y usuario si no existen
    $resultadoCliente = $Cliente->registerCliente($clienteData);

    if (!$resultadoCliente['success'] || empty($resultadoCliente['id_cliente'])) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'No se pudo registrar u obtener el cliente. ' . ($resultadoCliente['message'] ?? '')
        ]);
        return;
    }

    // Registrar la venta
    $ventaModel = new VentaPresencialModel();
    $resultadoVenta = $ventaModel->registrarVenta($resultadoCliente['id_cliente'], $productos, $metodos_pago);

        // Si hubo errores de validación, pasarlos a "message"
        if (!$resultadoVenta['success']) {
            // Si los errores vienen en array, los concatenamos
            if (!empty($resultadoVenta['errors'])) {
                $mensajeError = is_array($resultadoVenta['errors'])
                ? implode("\n", $resultadoVenta['errors'])
                : $resultadoVenta['errors'];
            }
            else {
                $mensajeError = $resultadoVenta['message'];
            }

            echo json_encode([
                'success' => false,
                'message' => $mensajeError
            ]);
            exit;
        }
    http_response_code($resultadoVenta['success'] ? 200 : 400);
    echo json_encode($resultadoVenta);
}




?>