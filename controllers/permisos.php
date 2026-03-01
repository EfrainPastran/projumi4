<?php
use App\Models\enviosModel;
use App\Models\PedidoModel;
use App\Models\notificacionModel;
use App\Middleware;
use App\Models\ClienteModel;
use App\Models\ProductosModel;
use App\Models\permisosModel;
function inicio() {
    if (!isset($_SESSION['user']['cedula'])) {
        header('Location: ../home/index');
        exit;
    }
    $cedula = $_SESSION['user']['cedula'];
    $Middleware = new Middleware();
    $tipoUsuario = $Middleware->verificarTipoUsuario($cedula);
    if ('emprendedor' == $tipoUsuario[0] || 'cliente' == $tipoUsuario[0]) {
        header('Location: ../home/index');
        exit;
    }
    $modelo = new permisosModel();
    $permisos = $modelo->obtenerTodos();
    render('permisos/index', ['permisos' => $permisos]);
}

function asignar()
{
    $Permiso = new permisosModel();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $fk_rol = $_POST['fk_rol'] ?? null;
        $fk_modulo = $_POST['fk_modulo'] ?? null;
        $fk_permiso = $_POST['fk_permiso'] ?? null;

        $resultado = $Permiso->asignarPermiso($fk_rol, $fk_modulo, $fk_permiso, 1);

        if ($resultado['success']) {
            $_SESSION['flash_success'] = $resultado['message'];
        } else {
            $_SESSION['flash_error'] = $resultado['message'];
        }

        header('Location: ' . APP_URL . '/permisos/inicio');
        exit;
    }
}


function eliminar() {
    try {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $Permiso = new permisosModel();
            $Permiso->setData(
                null,
                $_POST['fk_rol'],
                $_POST['fk_modulo'],
                $_POST['fk_permiso']
            );

            $eliminado = $Permiso->eliminarPermiso();

            if ($eliminado) {
                $_SESSION['flash_success'] = 'Permiso eliminado correctamente';
            } else {
                $_SESSION['flash_error'] = 'Error al eliminar el permiso';
            }

            header('Location: ' . APP_URL . '/permisos/inicio');
            exit;
        }
    } catch(Exception $e) {
        $_SESSION['flash_error'] = $e->getMessage();
        header('Location: ' . APP_URL . '/permisos/inicio');
        exit;
    }
}

function obtenerPorRolYModulo() {
    try {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['fk_rol']) && isset($_GET['fk_modulo'])) {
            $modelo = new permisosModel();
            $permisos = $modelo->obtenerPorRolYModulo($_GET['fk_rol'], $_GET['fk_modulo']);

            echo json_encode([
                'success' => true,
                'data' => $permisos
            ]);
        }
    } catch(Exception $e) {
        echo json_encode([
            'success' => false,
            'mensaje' => $e->getMessage()
        ]);
    }
}

function obtenerPermisosPorRol() {
    try {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['fk_rol'])) {
            $modelo = new permisosModel();
            $data = $modelo->obtenerPermisosPorRol($_GET['fk_rol']);

            echo json_encode([
                'success' => true,
                'data' => $data
            ]);
        }
    } catch(Exception $e) {
        echo json_encode([
            'success' => false,
            'mensaje' => $e->getMessage()
        ]);
    }
}

function guardarPermisosPorRol() {
    // 1. Limpiar cualquier salida previa para evitar JSON inválido
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        return;
    }

    $PermisoModel = new permisosModel();
    $data = $_POST;

    // Soporte para fetch con JSON body si fuera necesario
    if (empty($data)) {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true) ?? [];
    }

    $fk_rol = isset($data['fk_rol']) ? intval($data['fk_rol']) : null;
    $permisos = isset($data['permisos']) ? $data['permisos'] : [];

    if (!$fk_rol) {
        echo json_encode(['success' => false, 'message' => 'ID de rol no proporcionado']);
        return;
    }

    try {
    $PermisoModel->eliminarTodosPorRol($fk_rol);
    $mapaPermisos = $PermisoModel->obtenerMapaPermisos();

    // Log para depurar (mira el error_log de tu servidor para ver qué llega)
    error_log("Permisos recibidos: " . print_r($permisos, true));

    foreach ($permisos as $id_modulo => $acciones) {
        foreach ($acciones as $nombre_permiso => $valor) {
            
            // Ajuste: El checkbox puede llegar como "1", 1, o "on"
            $estaMarcado = ($valor == 1 || $valor === 'on');

            if ($estaMarcado && isset($mapaPermisos[$nombre_permiso])) {
                $id_permiso = $mapaPermisos[$nombre_permiso];
                $PermisoModel->asignarPermiso($fk_rol, $id_modulo, $id_permiso, $estaMarcado);
            }
        }
    }
    
    // Si llegamos aquí, devolvemos éxito
    echo json_encode(['success' => true]);

} catch (Exception $e) {
        error_log('Error al asignar permisos: ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error en el servidor: ' . $e->getMessage()
        ]);
    }
}



// Mapea el nombre del permiso al ID según tu base de datos
/*function obtenerIdPermisoPorNombre($nombre) {
    $mapa = [
        'consultar'  => 1,
        'registrar'  => 2,
        'actualizar' => 3,
        'eliminar'   => 4
    ];
    $PermisoModel = new permisosModel();
    $mapaPermisos = $PermisoModel->obtenerMapaPermisos();
    return $mapaPermisos[$nombre] ?? null;
}*/
