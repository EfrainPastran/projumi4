<?php
use App\Models\pagosModel;
use App\Models\PedidoModel;
use App\Models\notificacionModel;
use App\Middleware;
use App\Models\MonedaModel;

function index() {
    if (!isset($_SESSION['user']['cedula'])) {
        header('Location: ../home/index');
        exit;
    }
    $cedula = $_SESSION['user']['cedula'];
    $Middleware = new Middleware();
    $tipoUsuario = $_SESSION['user']['tipo'][0];
    //Vista para el emprendedor
    if ('emprendedor' == $tipoUsuario) {
        $title = "Pagos recibidos";        
    }
    //Vista para el cliente
    else if ('cliente' == $tipoUsuario) {
        $title = "Mis Pagos";        
    }
    //Vista para el usuario
    else {
        $title = "Gestión de Pagos";        
    }
    render('pagos/index', ['title' => $title]);
}

function obtenerEstatusPago() {
    $Pago = new pagosModel();

    $estadoEnProceso = $Pago->obtenerEstatusPago("Pendiente");
    $estadoEnAbierto = $Pago->obtenerEstatusPago("Aprobado");
    $estadoEnRechazado = $Pago->obtenerEstatusPago("Rechazado");

    // Aseguramos que todo sea un array de arrays
    $estadoEnProceso = is_array($estadoEnProceso) && isset($estadoEnProceso[0]) ? $estadoEnProceso : ($estadoEnProceso ? [$estadoEnProceso] : []);
    $estadoEnAbierto = is_array($estadoEnAbierto) && isset($estadoEnAbierto[0]) ? $estadoEnAbierto : ($estadoEnAbierto ? [$estadoEnAbierto] : []);
    $estadoEnRechazado = is_array($estadoEnRechazado) && isset($estadoEnRechazado[0]) ? $estadoEnRechazado : ($estadoEnRechazado ? [$estadoEnRechazado] : []);

    // Unimos todo
    $resultados = array_merge($estadoEnProceso, $estadoEnAbierto, $estadoEnRechazado);

    // Respuesta
    if (!empty($resultados)) {
        echo json_encode($resultados);
    } else {
        echo json_encode(['error' => 'No se encontró el pago']);
    }
}

// Actualizar estado de pago
function actualizarEstadoPago()
{
    header('Content-Type: application/json; charset=utf-8');

    try {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data || empty($data['estatus']) || empty($data['id_pagos']) || empty($data['id_pedido'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }

        $estatus   = $data['estatus'];
        $id_pagos  = $data['id_pagos'];
        $id_pedido = $data['id_pedido'];

        $Pago = new pagosModel();

        $resultado = $Pago->aprobar($estatus, $id_pagos, $id_pedido);

        http_response_code($resultado['success'] ? 200 : 400);
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error interno: ' . $e->getMessage()
        ]);
    }
}



//Mostrar todos los pagos registrados
function mostrarPagos() {
    $Pago = new pagosModel();

    try {
        $cedula = $_SESSION['user']['cedula'];
        $Middleware = new Middleware();
        $tipoUsuario = $Middleware->verificarTipoUsuario($cedula);
        //Vista para el emprendedor
        if ('emprendedor' == $tipoUsuario[0]) {
            $pagos = $Pago->mostrarPagosPorEmprendedor($cedula); 
            $tipo = 'emprendedor';
        }
        //Vista para el cliente
        else if ('cliente' == $tipoUsuario[0]) {
            $pagos = $Pago->mostrarPagosPorCliente($cedula);       
            $tipo = 'cliente';
        }
        //Vista para el usuario
        else {
            $pagos = $Pago->mostrarPagos();
            $tipo = 'usuario';
        }
        if ($pagos) {
            echo json_encode([
                'status' => 'success',
                'data' => $pagos,
                'tipo' => $tipo
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'No se encontraron pagos'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error al obtener los pagos: ' . $e->getMessage()
        ]);
    }
}

//Obtener monedas de acuerdo al idMetodo
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

//Mostrar detalles del pedido de acuerdo al id pedido seleecionado
function detallesPedido() {
    $idPedido = intval($_GET['idPedido']);
    $Pedido = new PedidoModel();
    $detalleProductos = $Pedido->obtenerDetallePedido($idPedido);
    // Calcular total del pedido
    $totalPedido = 0;
    foreach ($detalleProductos as &$producto) {
        $producto['total_producto'] = $producto['cantidad'] * $producto['precio_unitario'];
        $totalPedido += $producto['total_producto'];
    }

    header('Content-Type: application/json');
    echo json_encode($detalleProductos);
}

function consultarPago() {
    $idPago = intval($_GET['idPago']);
    $Pago = new pagosModel();

    $pagoInfo = $Pago->obtenerPago($idPago);
    $detallePagos = $Pago->obtenerDetallePago($idPago);

    if (!$pagoInfo) {
        http_response_code(404);
        echo json_encode(['error' => 'Pago no encontrado']);
        return;
    }

    // Calcular total pagado
    $totalPagado = 0;
    foreach ($detallePagos as &$detalle) {
        $totalPagado += $detalle['monto'];
    }

    $response = [
        'pago' => [
            'id_pedido' => $pagoInfo['id_pedidos'],
            'id' => $pagoInfo['id_pagos'],
            'fecha' => $pagoInfo['fecha_pago'],
            'estatus' => $pagoInfo['estatus_pago'],
            'total_pagado' => $totalPagado,
            'cliente' => [
                'id' => $pagoInfo['id_cliente'],
                'nombre' => $pagoInfo['cliente_nombre'],
                'apellido' => $pagoInfo['cliente_apellido'],
                'correo' => $pagoInfo['correo'],
            ],
        ],
        'detalle_pago' => $detallePagos
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
}



?>