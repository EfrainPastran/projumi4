<?php
use App\Middleware;
use App\Models\UsuariosModel;
use App\Models\ClienteModel;
use App\Models\EmpresaEnvioModel;
function index() {
    if (!isset($_SESSION['user']['cedula'])) {
        header('Location: ../home/index');
        exit;
    }
    if (isset($_SESSION['user']['cedula'])) {
        $cedula = $_SESSION['user']['cedula'];
        $Middleware = new Middleware();
        $tipoUsuario = $Middleware->verificarTipoUsuario($cedula);

        $idModuloEmpresa = 18; //Asignación del id del módulo en este caso de "Empresa de envio" en t_modulo
        
        // Obtenemos los permisos que el Middleware ya guardó en la sesión
        // Esto devolverá algo como ['consultar', 'registrar']
        $listaPermisos = $_SESSION['user']['rol']['permisos'][$idModuloEmpresa] ?? [];

        // Lo convertimos a un formato más cómodo para la vista: ['registrar' => true]
        $permisosMap = array_fill_keys($listaPermisos, true);

    }
    $model = new EmpresaEnvioModel();
    $data = $model->getAll();
    render('empresa/index', ['data' => $data, 'permisos' => $permisosMap]);
}

function register() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre      = trim($_POST['nombre'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $estado      = trim($_POST['estado'] ?? '');

        $EmpresaEnvio = new EmpresaEnvioModel();

        // Validar y asignar los datos
        $validacion = $EmpresaEnvio->setData(null, $nombre, $telefono, $direccion, $estado);

        // Si hubo errores de validación, los mostramos
        if (!$validacion['success']) {
            $errores = implode('<br>', $validacion['errors']);
            $_SESSION['flash_error'] = "Error de validación:<br>$errores";
            header('Location: ' . APP_URL . '/empresa/index');
            exit;
        }

        // Si pasa las validaciones, intentar registrar
            $result = $EmpresaEnvio->registerEmpresaEnvio();
            
            if (!empty($del['success']) && $del['success'] === true) {
            $_SESSION['flash_error'] = $del['message'] ?? 'Error al registrar empresa!';
             header('Location: ' . APP_URL . '/empresa/index');
            exit;
        } else {
            $_SESSION['flash_success'] = $del['message'] ?? 'Empresa Registrada correctamente!';
        }
        }

        header('Location: ' . APP_URL . '/empresa/index');
        exit;
    }


function update() {
    try {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             $id      = trim($_POST['id'] ?? '');
            $nombre      = trim($_POST['nombre'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $estado      = trim($_POST['estado'] ?? '');

        $EmpresaEnvio = new EmpresaEnvioModel();

        // Validar y asignar los datos
        $validacion = $EmpresaEnvio->setData(null, $nombre, $telefono, $direccion, $estado);

        // Si hubo errores de validación, los mostramos
        if (!$validacion['success']) {
            $errores = implode('<br>', $validacion['errors']);
            $_SESSION['flash_error'] = "Error de validación:<br>$errores";
            header('Location: ' . APP_URL . '/empresa/index');
            exit;
        }


            $res = $EmpresaEnvio->update();

            if ($res) {
                $_SESSION['flash_success'] = '¡Empresa de envío actualizada correctamente!';
                header('Location: ' . APP_URL . '/empresa/index');
                exit;
            } else {
                throw new Exception('Error al actualizar la empresa de envío');
            }
        }
    } catch(Exception $e) {
        $_SESSION['flash_error'] = $e->getMessage();
        header('Location: ' . APP_URL . '/empresa/index');
        exit;
    }
}

function delete() {
    try {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id      = trim($_POST['id'] ?? '');
            $nombre      = trim($_POST['nombre'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $estado      = trim($_POST['estado'] ?? '');
            
            
            $model = new EmpresaEnvioModel();
           // Validar y asignar los datos
        $validacion = $model->setData($id, $nombre, $telefono, $direccion, $estado);

        // Si hubo errores de validación, los mostramos
        if (!$validacion['success']) {
            $errores = implode('<br>', $validacion['errors']);
            $_SESSION['flash_error'] = "Error de validación:<br>$errores";
            header('Location: ' . APP_URL . '/empresa/index');
            exit;
        }
            $del = $model->delete();
           
            if (!empty($del['success']) && $del['success'] === true) {
            $_SESSION['flash_success'] = $del['message'] ?? 'Empresa Eliminada correctamente!';
             header('Location: ' . APP_URL . '/empresa/index');
            exit;
        } else {
            $_SESSION['flash_error'] = $del['message'] ?? 'Error al Eliminar la Empresa.';
        }
        }
    } catch(Exception $e) {
        $_SESSION['flash_error'] = $e->getMessage();
        header('Location: ' . APP_URL . '/empresa/index');
        exit;
    }
}

// Función adicional para obtener detalles por ID (útil para AJAX)
function getEmpresaEnvioById() {
    try {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            $model = new EmpresaEnvioModel();
            $empresa = $model->getById($_GET['id']);
           
            if ($empresa) {
                echo json_encode([
                    'success' => true,
                    'data' => $empresa
                ]);
            } else {
                throw new Exception('Empresa de envío no encontrada');
            }
        }
    } catch(Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}