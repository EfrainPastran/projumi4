<?php
use App\Models\DeliveryModel;
use App\Middleware;

function index() {
    if (!isset($_SESSION['user']['cedula'])) {
        header('Location: ../home/index');
        exit;
    }

    $cedula = $_SESSION['user']['cedula'];
    $Middleware = new Middleware();
    $tipoUsuario = $Middleware->verificarTipoUsuario($cedula);
    $Delivery = new DeliveryModel();
    if ('cliente' == $tipoUsuario[0]) {
        $title = "Mis entregas";
        $menu = "headerCliente";
        $deliveries = $Delivery->obtenerDeliveriesPorCliente($cedula);

    } else if ('emprendedor' == $tipoUsuario[0]) {
        $title = "Entregas de mis productos";
        $menu = "headerEmprendedor";
        $deliveries = $Delivery->obtenerDeliveriesPorEmprendedor($cedula);

    } else {
        $title = "Gestión de entregas";
        $menu = "headeradmin";
        $deliveries = $Delivery->obtenerTodosLosDeliveries();
    }

    // Render con data
    render('delivery/index', [
        'title' => $title,
        'menu' => $menu,
        'deliveries' => $deliveries
    ]);
}

function obtenerDeliveries() {


    $cedula = $_SESSION['user']['cedula'];
    $Middleware = new Middleware();
    $tipoUsuario = $Middleware->verificarTipoUsuario($cedula);
    $Delivery = new DeliveryModel();

    try {
        if ('cliente' == $tipoUsuario[0]) {
            $deliveries = $Delivery->obtenerDeliveriesPorCliente($cedula);
            $tipo = 'cliente';
        } else if ('emprendedor' == $tipoUsuario[0]) {
            $deliveries = $Delivery->obtenerDeliveriesPorEmprendedor($cedula);
            $tipo = 'emprendedor';
        } else {
            $deliveries = $Delivery->obtenerTodosLosDeliveries();
        }

        echo json_encode([
            'success' => true,
            'data' => $deliveries,
            'tipo' => $tipo
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

function registrarDelivery() {
    try {
        $Delivery = new DeliveryModel();

        // Validar y asignar los datos
        $validacion = $Delivery->setDeliveryData(
            null,
            $_POST['direccion_exacta'] ?? '',
            $_POST['destinatario'] ?? '',
            $_POST['telefono_destinatario'] ?? '',
            $_POST['correo_destinatario'] ?? '',
            null,
            $_POST['fk_pedido'] ?? '',
            'Pendiente'
        );

        // Si hubo errores de validación, los mostramos
        if (!$validacion['success']) {
            $errores = implode('<br>', $validacion['errors']);
            echo json_encode(['success' => false, 'message' => "Error de validación: $errores"]);
            return;
        }

        $id = $Delivery->registrarDelivery();

        echo json_encode(['success' => true, 'id_delivery' => $id]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function actualizarDelivery() {
    try {
        $Delivery = new DeliveryModel();

        // Validar y asignar los datos
        $validacion = $Delivery->setDeliveryData(
            $_POST['id_delivery'] ?? '',
            $_POST['direccion_exacta'] ?? '',
            $_POST['destinatario'] ?? '',
            $_POST['telefono_destinatario'] ?? '',
            $_POST['correo_destinatario'] ?? '',
            null,
            $_POST['fk_pedido'] ?? '',
            'Pendiente'
        );

        // Si hubo errores de validación, los mostramos
        if (!$validacion['success']) {
            $errores = implode('<br>', $validacion['errors']);
            echo json_encode(['success' => false, 'message' => "Error de validación: $errores"]);
            return;
        }

        $Delivery->actualizarDelivery();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function aprobarDelivery() {
    try {
        if (!isset($_POST['id_delivery'], $_POST['telefono_delivery'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }

        $Delivery = new DeliveryModel();

        // Validar y asignar solo los datos necesarios para aprobar
        $validacion = $Delivery->setDataAprobarDelivery(
            $_POST['id_delivery'],
            $_POST['telefono_delivery']
        );

        // Si hubo errores de validación, los mostramos
        if (!$validacion['success']) {
            $errores = implode('<br>', $validacion['errors']);
            echo json_encode(['success' => false, 'message' => "Error de validación: $errores"]);
            return;
        }

        $Delivery->aprobarDelivery();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function consultarDelivery() {
    try {
        $Delivery = new DeliveryModel();
        $idDelivery = filter_input(INPUT_GET, 'idDelivery', FILTER_VALIDATE_INT);

        if (!$idDelivery) {
            throw new Exception("ID de pedido no válido");
        }

        $delivery = $Delivery->obtenerDeliveryPorId($idDelivery);

        if (!$delivery) {
            throw new Exception("No se encontró delivery para este pedido");
        }

        echo json_encode(['success' => true, 'data' => $delivery]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function eliminarDelivery() {
    try {
        $Delivery = new DeliveryModel();
        $idDelivery = filter_input(INPUT_GET, 'idDelivery', FILTER_VALIDATE_INT);

        if (!$idDelivery) {
            throw new Exception("ID de pedido no válido");
        }

        $Delivery->eliminarDelivery($idDelivery);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
