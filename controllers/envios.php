<?php
use App\Middleware;
use App\Models\EnviosModel;
use App\Models\PedidoModel;
function index() {
    if (!isset($_SESSION['user']['cedula'])) {
        header('Location: ../home/index');
        exit;
    }

    $cedula = $_SESSION['user']['cedula'];
    $middleware = new Middleware();
    $tipoUsuario = $middleware->verificarTipoUsuario($cedula);
//$cedula4 = $_SESSION['user'];
//echo "<pre>";
//print_r($cedula4);
//echo "</pre>";
//die;
    // Vista para el emprendedor
    if ($tipoUsuario[0] === 'emprendedor') {
        $title = "Mis envíos";
    }
    // Vista para el cliente
    else if ($tipoUsuario[0] === 'cliente') {
        $title = "Mis Envios";
    }
    // Vista para admin
    else {
        $title = "Gestión de envíos";
    }

    render('envios/index', ['title' => $title]);
}

// Obtener todos los envíos según tipo de usuario
function mostrarEnvios() {
    try {
        $Envio = new EnviosModel();

        $cedula = $_SESSION['user']['cedula'];
        $Middleware = new Middleware();
        $tipoUsuario = $Middleware->verificarTipoUsuario($cedula);

        if ($tipoUsuario[0] === 'cliente') {
            $envios = $Envio->obtenerEnviosPorCliente($cedula);
            $tipo = 'cliente';
        } else if ($tipoUsuario[0] === 'emprendedor') {
            $envios = $Envio->obtenerEnviosPorEmprendedor($cedula);
            $tipo = 'emprendedor';
        } else {
            $envios = $Envio->obtenerTodosLosEnvios();
        }

        echo json_encode(['success' => true, 'data' => $envios, 'tipo' => $tipo]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    return;
}

// Consultar un envío por ID de pedido
function consultarEnvio() {
    try {
        $Envio = new EnviosModel();
        $Pedido = new PedidoModel();

        $idPedido = filter_input(INPUT_GET, 'idPedido', FILTER_VALIDATE_INT);

        if (!$idPedido) {
            throw new Exception("ID de pedido no válido");
        }

        $envio = $Envio->obtenerEnvioPorId($idPedido);

        if (!$envio) {
            throw new Exception("No se encontró información de envío para este pedido");
        }

        $detallePedido = $Pedido->obtenerDetallePedido($idPedido);

        $response = [
            'success' => true,
            'data' => [
                'envio' => $envio,
                'detalle_pedido' => $detallePedido
            ]
        ];

    } catch (Exception $e) {
        $response = [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    return;
}

function actualizarEnvio() {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (!$data || !isset($data['id_envio'], $data['numero_seguimiento'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
        return;
    }

    try {
        $Envio = new EnviosModel();

        // Obtener datos del envío y del pedido relacionado
        $data_envios = $Envio->obtenerEnvios($data['id_envio']);
        if (!$data_envios) {
            throw new Exception('No se encontró el envío.');
        }

        // Validar y asignar los datos usando setData
        $validacion = $Envio->setData(
            $data['id_envio'],
            $data['direccion_envio'] ?? $data_envios['direccion_envio'],
            $data['estatus_envio'] ?? 'En proceso',
            $data['numero_seguimiento'],
            $data['empresa_envio'] ?? $data_envios['fk_empresa_envio'],
            $data_envios['fk_pedido']
        );

        // Si hubo errores de validación, los mostramos
        if (!$validacion['success']) {
            $errores = implode('<br>', $validacion['errors']);
            echo json_encode(['success' => false, 'message' => "Error de validación: $errores"]);
            return;
        }

        // Actualizar envío
        $resultado = $Envio->actualizarNroSeguimiento();

        echo json_encode([
            'success' => $resultado['success'] ?? $resultado,
            'message' => $resultado['message'] ?? ($resultado ? 'Envío actualizado correctamente' : 'Error al actualizar envío')
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}