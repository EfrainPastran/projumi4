<?php
use App\Models\rolesModel;
use App\Middleware;
use App\Models\modulosModel;
function index() {
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
    $model = new rolesModel();
    $modulo = new modulosModel();
    $modulos =$modulo->getModulos();
    $roles = $model->getRoles();
    render('roles/index', ['roles' => $roles, 'modulos' => $modulos]);
}

function register() {
    $Rol = new rolesModel();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $Rol->setData(
            null,
            $_POST['nombre'] ?? '',
            $_POST['descripcion_rol'] ?? '',
            $_POST['estatus'] ?? ''
        );

        $result = $Rol->registerRol();

        if ($result['success']) {
            $_SESSION['flash_success'] = $result['message'];
        } else {
            $_SESSION['flash_error'] = $result['message'];
        }

        header('Location: ' . APP_URL . '/roles/index');
        exit;
    }
}


function update() {
    try {
        //Asegurar que la petición sea POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Método de solicitud no permitido.');
        }

        //Validar que existan los datos requeridos
        if (empty($_POST['id_rol']) || empty($_POST['nombre'])) {
            throw new Exception('El ID del rol y el nombre son obligatorios.');
        }

        //Instanciar el modelo y asignar datos
        $Rol = new rolesModel();
        $Rol->setData(
            $_POST['id_rol'],
            trim($_POST['nombre']),
            trim($_POST['descripcion_rol'] ?? ''),
            $_POST['estatus'] ?? 1
        );

        //Ejecutar la actualización
        $res = $Rol->update();

        //Evaluar respuesta del modelo (espera array estructurado)
        if (is_array($res)) {
            if ($res['success']) {
                $_SESSION['flash_success'] = $res['message'];
            } else {
                $_SESSION['flash_error'] = $res['message'];
            }
        } else {
            // Si el modelo devuelve algo inesperado
            $_SESSION['flash_error'] = 'Error inesperado al actualizar el rol.';
        }

    } catch (Exception $e) {
        //Captura de errores generales
        error_log("Error en RolesController::update() → " . $e->getMessage());
        $_SESSION['flash_error'] = 'Ocurrió un error: ' . $e->getMessage();
    }

    //Redirigir siempre al listado
    header('Location: ' . APP_URL . '/roles/index');
    exit;
}


function delete()
{
    try {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $Rol = new rolesModel();

            $Rol->setData(
                $_POST['id_rol'] ?? null,
                '', // Nombre no necesario
                '', // Descripción no necesaria
                0   // Estatus no necesario
            );

            $resultado = $Rol->delete();

            if ($resultado['success']) {
                $_SESSION['flash_success'] = $resultado['message'];
                header('Location: ' . APP_URL . '/roles/index');
                exit;
            } else {
                $_SESSION['flash_error'] = $resultado['message'];
                header('Location: ' . APP_URL . '/roles/index');
                exit;
            }
        }
    } catch (Exception $e) {
        $_SESSION['flash_error'] = 'Error inesperado: ' . $e->getMessage();
        header('Location: ' . APP_URL . '/roles/index');
        exit;
    }
}


// Función adicional para obtener un rol por nombre (opcional AJAX o uso en formularios)
function getRolByName() {
    try {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['nombre'])) {
            $model = new rolesModel();
            $rol = $model->getByName($_GET['nombre']);

            if ($rol) {
                echo json_encode([
                    'success' => true,
                    'data' => $rol
                ]);
            } else {
                throw new Exception('Rol no encontrado');
            }
        }
    } catch(Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

