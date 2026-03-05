<?php
use App\Middleware;
use App\Models\eventosModel;
function index() {
    if (!isset($_SESSION['user']['cedula'])) {
        header('Location: ../home/index');
        exit;
    }

    $cedula = $_SESSION['user']['cedula'];
    $middleware = new Middleware();
    $tipoUsuario = $middleware->verificarTipoUsuario($cedula);

    // Vista para el emprendedor
    if ($tipoUsuario[0] === 'emprendedor') {
        $title = "Mis envíos";
        $menu = "headerEmprendedor";
    }
    // Vista para el cliente
    else if ($tipoUsuario[0] === 'cliente') {
        $title = "Mis entregas";
        $menu = "headerCliente";
    }
    // Vista para admin
    else {
        $title = "Gestión de envíos";
        $menu = "headeradmin";
    }

    $rol = $_SESSION['user']['rol'];
    $permisos = $middleware->obtenerPermisosDinamicos($rol['rol'], 'Eventos');

    if (!$permisos['consultar']) {
            header('Location: ../home/principal');
            exit;
    }
$model = new eventosModel();

$data = $model->getAll();

    render('eventos/index', [
           'data'     => $data,
           'permisos' => $permisos
    ]);
}


function register() {
    // Solo aceptar solicitudes POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Método no permitido.');
        }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fecha_inicio      = trim($_POST['fecha_inicio'] ?? '');
        $fecha_fin = trim($_POST['fecha_fin'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $direccion      = trim($_POST['direccion'] ?? '');
        $status      = trim($_POST['status'] ?? '');

         $Evento = new eventosModel();

        // Validar y asignar los datos
        $validacion = $Evento->setData(null, $fecha_inicio, $fecha_fin, $nombre, $direccion, $status);

        // Si hubo errores de validación, los mostramos
        if (!$validacion['success']) {
            $errores = implode('<br>', $validacion['errors']);
            $_SESSION['flash_error'] = "Error de validación:<br>$errores";
            header('Location: ' . APP_URL . '/eventos/index');
            exit;
        }

        // Si pasa las validaciones, intentar registrar
            $result = $Evento->registerEventos();
            
           if (!empty($result['success']) && $result['success'] === true) {
            $_SESSION['flash_success'] = $result['message'] ?? 'Empresa Registrada correctamente!';
             header('Location: ' . APP_URL . '/eventos/index');
            exit;
        } else {
            $_SESSION['flash_error'] = $result['message'] ?? 'Error al Registrar la Empresa.';
        }
        }

        header('Location: ' . APP_URL . '/eventos/index');
        exit;
    }


function eventos_update() {
    try {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_eventos      = trim($_POST['id_eventos'] ?? '');
            $fecha_inicio      = trim($_POST['fecha_inicio'] ?? '');
            $fecha_fin = trim($_POST['fecha_fin'] ?? '');
            $nombre = trim($_POST['nombre'] ?? '');
            $direccion      = trim($_POST['direccion'] ?? '');
            $status      = trim($_POST['status'] ?? '');

       $eventos = new eventosModel();

        // Validar y asignar los datos
        $validacion = $eventos->setData($id_eventos, $fecha_inicio, $fecha_fin, $nombre, $direccion, $status);

        // Si hubo errores de validación, los mostramos
        if (!$validacion['success']) {
            $errores = implode('<br>', $validacion['errors']);
            $_SESSION['flash_error'] = "Error de validación:<br>$errores";
            header('Location: ' . APP_URL . '/eventos/index');
            exit;
        }


            $res = $eventos->update();

           if (!empty($res['success']) && $res['success'] === true) {
            $_SESSION['flash_success'] = $res['message'] ?? '¡Evento Actualizado correctamente!';
        } else {
            $_SESSION['flash_error'] = $res['message'] ?? 'Error al Actualizar el evento.';
        }
        }
    } catch(Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
     header('Location: ' . APP_URL . '/eventos/index');
    exit;
}


function eventos_delete() {
    try {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_eventos      = trim($_POST['id_eventos'] ?? '');
            $fecha_inicio      = trim($_POST['fecha_inicio'] ?? '');
            $fecha_fin = trim($_POST['fecha_fin'] ?? '');
            $nombre = trim($_POST['nombre'] ?? '');
            $direccion      = trim($_POST['direccion'] ?? '');
            $status      = trim($_POST['status'] ?? '');

             $model = new eventosModel();
             // Validar y asignar los datos
        $val = $model->setData($id_eventos, $fecha_inicio, $fecha_fin, $nombre, $direccion, $status);

        // Si hubo errores de validación, los mostramos
        if (!$val['success']) {
            $errores = implode('<br>', $val['errors']);
            $_SESSION['flash_error'] = "Error de validación:<br>$errores";
            header('Location: ' . APP_URL . '/eventos/index');
            exit;
        }
           
                $del = $model->delete($id_eventos);
            
            if (!empty($del['success']) && $del['success'] === true) {
            $_SESSION['flash_success'] = $del['message'] ?? '¡Evento Eliminado correctamente!';
        } else {
            $_SESSION['flash_error'] = $del['message'] ?? 'Error al Eliminar el evento.';
        }
        }
    } catch(Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    header('Location: ' . APP_URL . '/eventos/index');
    exit;
}