<?php

use function PHPSTORM_META\map;
use App\Models\PedidoModel;
use App\Models\EmpresaEnvioModel;
use App\Models\notificacionModel;
use App\Middleware;
use App\Models\ClienteModel;
use App\Models\ProductosModel;  

function index() {
    if (!isset($_SESSION['user']['cedula'])) {
        header('Location: ../home/index');
        exit;
    }
    $Pedido = new PedidoModel();
    $empresa = new EmpresaEnvioModel();
    $empresas = $empresa->getAll();
    $cedula = $_SESSION['user']['cedula'];
    $middleware = new Middleware();
    $tipoUsuario = $middleware->verificarTipoUsuario($cedula);
    //Vista para el emprendedor
    if ('emprendedor' == $_SESSION['user']['tipo'][0]) {
        $title = "Pedidos solicitados";        
    }
    //Vista para el cliente
    else if ('cliente' == $_SESSION['user']['tipo'][0]) {
        $title = "Mis Pedidos";        
    }
    //Vista para el usuario
    else {
        $title = "Gestión de Pedidos";        
    }

    $rol = $_SESSION['user']['rol'];
    $permisos = $middleware->obtenerPermisosDinamicos($rol['rol'], 'Pedidos');

    if (!$permisos['consultar']) {
            header('Location: ../home/principal');
            exit;
    }



    render('pedidos/index' , [
           'empresas' => $empresas, 
           'title' => $title,
           'permisos' => $permisos
    ]);
}

 function registrar()
{
    header('Content-Type: application/json; charset=utf-8');

    try {
        // === Verificar método HTTP ===
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
            return;
        }

        // === Detectar tipo de contenido ===
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (stripos($contentType, 'application/json') !== false) {
            // Petición JSON (como las de JMeter o Postman)
            $data = json_decode(file_get_contents('php://input'), true);
        } else {
            // Petición tipo form-data o x-www-form-urlencoded
            $data = $_POST;
            foreach (['detallePedido', 'detallePago', 'detalleEnvio'] as $key) {
                if (isset($data[$key]) && is_string($data[$key])) {
                    $decoded = json_decode($data[$key], true);
                    if ($decoded) $data[$key] = $decoded;
                }
            }
        }

        // === Validar datos mínimos ===
        $cedula = $data['cedula'] ?? null;
        $detallePedido = $data['detallePedido'] ?? null;
        $detallePago   = $data['detallePago'] ?? null;
        $detalleEnvio  = $data['detalleEnvio'] ?? null;

        if (!$cedula || !$detallePedido || !$detallePago || !$detalleEnvio) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos.']);
            return;
        }

        // === Normalizar estructuras ===

        // Estructura del detallePedido
        if (isset($detallePedido[0]) && !isset($detallePedido['detalle'])) {
            $detallePedido = ['detalle' => $detallePedido];
        }

        // Convertir claves de productos 
        if (isset($detallePedido['detalle']) && is_array($detallePedido['detalle'])) {
            foreach ($detallePedido['detalle'] as &$item) {
                // Cambiar id_producto → id
                if (isset($item['id_producto']) && !isset($item['id'])) {
                    $item['id'] = $item['id_producto'];
                }

                // Cambiar precio_unitario → precio
                if (isset($item['precio_unitario']) && !isset($item['precio'])) {
                    $item['precio'] = $item['precio_unitario'];
                }
            }
            unset($item);
        }

        // Estructura del detallePago
        if (isset($detallePago['fk_detalle_metodo_pago']) && !isset($detallePago['detalles'])) {
            // Caso de un solo pago como objeto
            $detallePago = ['detalles' => [$detallePago]];
        } elseif (isset($detallePago[0]) && is_array($detallePago[0]) && !isset($detallePago['detalles'])) {
            // Caso de array de pagos (como el que envía tu frontend)
            $detallePago = ['detalles' => $detallePago];
        }


        // === Buscar cliente por cédula ===
        $clienteModel = new ClienteModel();
        $clienteData = $clienteModel->getByCedula($cedula);

        if (empty($clienteData['id_cliente'])) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Cliente no encontrado.']);
            return;
        }

        // === Crear y registrar pedido ===
        $pedidoModel = new PedidoModel();
        $fk_cliente = $clienteData['id_cliente'] ?? null;

        // Validar campos antes de registrar
        $resultado = $pedidoModel->registrarPedidoCompleto(
            $fk_cliente,  
            $detallePedido,
            $detalleEnvio,
            $detallePago
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
        // === Respuesta final ===
        http_response_code($resultado['success'] ? 200 : 400);
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    } catch (Throwable $e) {
        error_log('Error en registrar pedido: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error interno del servidor.'
        ]);
    }
}



function mostrarPedidos() {
    $Pedido = new PedidoModel();

    try {
        $cedula = $_SESSION['user']['cedula'];
        $Middleware = new Middleware();
        $tipoUsuario = $Middleware->verificarTipoUsuario($cedula);
        //Vista para el emprendedor
        if ('emprendedor' == $tipoUsuario[0]) {
            $pedidos = $Pedido->mostrarPedidosPorEmprendedor($cedula); 
            $tipo = 'emprendedor';
        }
        //Vista para el cliente
        else if ('cliente' == $tipoUsuario[0]) {
            $pedidos = $Pedido->mostrarPedidosPorCliente($cedula);       
            $tipo = 'cliente';
        }
        //Vista para el usuario
        else {
            $pedidos = $Pedido->mostrarPedidos();
            $tipo = 'usuario';
        }

        if ($pedidos) {
            echo json_encode([
                'status' => 'success',
                'data' => $pedidos, 
                'tipo' => $tipo
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'No se encontraron pedidos'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error al obtener los pedidos: ' . $e->getMessage()
        ]);
    }
}

function consultarPedido()
{
    header('Content-Type: application/json; charset=utf-8');

    try {
        if (!isset($_GET['idPedido'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Debe proporcionar un ID de pedido.']);
            return;
        }

        $idPedido = intval($_GET['idPedido']);
        $pedidoModel = new PedidoModel();

        $resultado = $pedidoModel->consultarPedidoCompleto($idPedido);

        if (!$resultado['success']) {
            http_response_code(404);
            echo json_encode($resultado);
            return;
        }

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'pedido' => $resultado['pedido'],
            'detalle_productos' => $resultado['detalle']
        ]);
    } catch (Exception $e) {
        error_log("Error en controlador consultarPedido(): " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error interno del servidor.']);
    }
}
