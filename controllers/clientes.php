<?php
use App\Models\ClienteModel;
use App\Middleware;
function index() {
    // 1. Verificación de sesión
    if (!isset($_SESSION['user']['cedula'])) {
        header('Location: ../home/index');
        exit;
    }

    $cedula = $_SESSION['user']['cedula'];
    $middleware = new Middleware();
    $tipoUsuario = $middleware->verificarTipoUsuario($cedula);

    $rol = $_SESSION['user']['rol'];
    $permisos = $middleware->obtenerPermisosDinamicos($rol['rol'], 'Clientes');

    if (!$permisos['consultar']) {
            header('Location: ../home/principal');
            exit;
    }
    // 3. Carga de datos y renderizado
    $model = new ClienteModel();
    $data = $model->getAll();

    render('clientes/index', [
        'data'     => $data,
        'title'    => $title,
        'permisos' => $permisos // Pasamos el mapa de permisos a la vista
    ]);
}

function register() {
    $Cliente = new ClienteModel();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $Cliente->setData(
            null,
            $_POST['cedula'],
            $_POST['nombre'],
            $_POST['apellido'],
            $_POST['correo'],
            $_POST['telefono'],
            $_POST['fecha_nacimiento'],
            date('Y-m-d'), // Fecha de registro actual
            $_POST['estatus']
        );

        $result = $Cliente->registerCliente();

        if ($result === true) {
            $_SESSION['flash_success'] = '¡Cliente registrado exitosamente!';
        } else {
            $_SESSION['flash_error'] = 'Error al registrar el cliente.';
        }

        header('Location: ' . APP_URL . '/clientes/index');
        exit;
    }
}

function clientes_update() {
    try {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $Cliente = new ClienteModel();
            $Cliente->setData(
                $_POST['id_cliente'],
                $_POST['cedula'],
                $_POST['nombre'],
                $_POST['apellido'],
                $_POST['correo'],
                $_POST['telefono'],
                $_POST['fecha_nacimiento'],
                $_POST['fecha_registro'],
                $_POST['estatus']
            );
            
            $res = $Cliente->update();

            if ($res) {
                $_SESSION['flash_success'] = '¡Cliente actualizado correctamente!';
                header('Location: ' . APP_URL . '/clientes/index');
                exit;
            } else {
                throw new Exception('Error al actualizar cliente');
            }
        }
    } catch(Exception $e) {
        $_SESSION['flash_error'] = $e->getMessage();
        header('Location: ' . APP_URL . '/clientes/index');
        exit;
    }
}

function clientes_delete() {
    try {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new ClienteModel();
            
            // Solo necesitamos el ID para eliminar
            $model->setData(
                $_POST['id_cliente'],
                '', '', '', '', '', '', '', 0
            );
            
            $del = $model->delete();
            
            if ($del) {
                $_SESSION['flash_success'] = 'Cliente eliminado correctamente';
                header('Location: ' . APP_URL . '/clientes/index');
                exit;
            } else {
                throw new Exception('Error al eliminar cliente');
            }
        }
    } catch(Exception $e) {
        $_SESSION['flash_error'] = $e->getMessage();
        header('Location: ' . APP_URL . '/clientes/index');
        exit;
    }
}

function getByCedula() {
    try {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['cedula'])) {
            $model = new ClienteModel();
            $cliente = $model->getByCedula($_GET['cedula']);
            
            if ($cliente) {
                echo json_encode([
                    'success' => true,
                    'data' => $cliente
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Cliente no encontrado'
                ]);
            }
        }
    } catch(Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}