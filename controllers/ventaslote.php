<?php
use App\Models\MetodoModel;
use App\Models\MonedaModel;
use App\Models\ProductosModel;
use App\Models\VentaEventoModel;
use App\Models\eventosModel;
use App\Middleware;
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
        $evento =  new eventosModel();
        $eventos = $evento->cargarEventosActuales();
    }    
    else {
        header('Location: ../home/index');
        exit;
    }


    $rol = $_SESSION['user']['rol'];
    $permisos = $middleware->obtenerPermisosDinamicos($rol['rol'], 'Ventas por lote');

    if (!$permisos['consultar']) {
            header('Location: ../home/principal');
            exit;
    }

    render('ventas/lote', [
           'metodos' => $metodos,
           'eventos' => $eventos,
           'permisos' => $permisos
    ]);
}

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

function mostrarVentas() {
    $venta = new VentaEventoModel();
    $ventas = $venta->getVentasPorEmprendedor($_SESSION['user']['cedula']);
    echo json_encode($ventas);
    return;
}

function detalleVentaEvento()
{
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Método no permitido. Use POST.'
        ]);
        return;
    }

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id_evento'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Faltan datos: se requiere el ID del evento.'
        ]);
        return;
    }

    if (empty($_SESSION['user']['cedula'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Sesión no válida o usuario no autenticado.'
        ]);
        return;
    }

    $cedula = $_SESSION['user']['cedula'];
    $id_evento = $data['id_evento'];

    try {
        $ventaModel = new VentaEventoModel();
        $resultado = $ventaModel->getDetalleVentasPorEvento($cedula, $id_evento);

        // --- Asegurar estructura consistente ---
        if (!is_array($resultado)) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error interno: el modelo devolvió un formato inesperado.'
            ]);
            return;
        }

        // Si el modelo devuelve un array con success => false
        if (isset($resultado['success']) && $resultado['success'] === false) {
            http_response_code(400);
            echo json_encode($resultado);
            return;
        }

        // Si devuelve datos vacíos
        if (isset($resultado['data']) && empty($resultado['data'])) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => $resultado['message'] ?? 'No se encontraron ventas para este evento.',
                'data' => []
            ]);
            return;
        }

        // Caso exitoso con datos
        http_response_code(200);
        echo json_encode($resultado);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error interno del servidor: ' . $e->getMessage()
        ]);
    }
}



function registrarVentaEvento()
{
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
        return;
    }

    $data = json_decode(file_get_contents("php://input"), true);

    // Validar estructura base
    if (empty($data['selectEvento']) || empty($data['productos']) || empty($data['desglose'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Faltan datos necesarios (evento, productos o desglose).']);
        return;
    }

    try {
        $ventaModel = new VentaEventoModel();
        $resultado = $ventaModel->registrarVentaEvento(
            $data['selectEvento'],
            $data['productos'],
            $data['desglose']
        );

        if (!$resultado['success']) {
            // Si los errores vienen en array, los concatenamos
            if (!empty($resultado['errors'])) {
                $mensajeError = is_array($resultado['errors'])
                ? implode("\n", $resultado['errors'])
                : $resultado['errors'];
            }
            else {
                $mensajeError = $resultado['message'];
            }

            echo json_encode([
                'success' => false,
                'message' => $mensajeError
            ]);
            exit;
        }
        else {
            echo json_encode([
                'success' => true,
                'message' => 'Venta registrada correctamente.'
            ]);
            exit;
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error inesperado: ' . $e->getMessage()]);
    }
}




?>