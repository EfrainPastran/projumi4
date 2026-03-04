<?php
use App\Models\UsuariosModel;
use App\Models\ClienteModel;
use App\Models\rolesModel;
use App\Middleware;
function index() {
    // 1. Verificar sesión
    if (!isset($_SESSION['user']['cedula'])) {
        header('Location: ../home/index');
        exit;
    }

    // 2. Verificar permisos y tipo de usuario
    $cedula = $_SESSION['user']['cedula'];
    $middleware = new Middleware();
    $tipoUsuario = $middleware->verificarTipoUsuario($cedula);

    
    $rol = $_SESSION['user']['rol'];
    $permisos = $middleware->obtenerPermisosDinamicos($rol['rol'], 'Usuarios');

    if (!$permisos['consultar']) {
            header('Location: ../home/principal');
            exit;
    }

    // 4. Instanciar Modelos (Corregido el nombre a UsuariosModel)
    $model = new UsuariosModel(); 
    $rol = new rolesModel();
    
    // 5. Obtener Datos
    $roles = $rol->getRoles();
    $data = $model->getUsuarios();

    // 6. Renderizar enviando permisos
    render('user/index', [
        'data'     => $data, 
        'roles'    => $roles,
        'permisos' => $permisos // <-- Pasamos el mapa de permisos a la vista
    ]);
}

function register()
{
    header('Content-Type: application/json');
    $Usuarios = new UsuariosModel();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Recoger y limpiar los datos de entrada
            $Usuarios->setData(
                $_POST['cedula'] ?? '',
                $_POST['nombre'] ?? '',
                $_POST['apellido'] ?? '',
                $_POST['correo'] ?? '',
                $_POST['password'] ?? '',
                $_POST['direccion'] ?? '',
                $_POST['telefono'] ?? '',
                $_POST['fecha_registro'] ?? '',
                $_POST['fecha_nacimiento'] ?? '',
                $_POST['estatus'] ?? 1,
                $_POST['fk_rol'] ?? 2,
            );

            $resultado = $Usuarios->registerUsuario();

            echo json_encode($resultado);
            return;

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Método no permitido'
        ]);
    }
}

function update()
{
    header('Content-Type: application/json');

    try {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $Usuario = new UsuariosModel();

            // Saneamiento de datos
            $Usuario->setData(
                $_POST['cedula'] ?? '',
                $_POST['nombre'] ?? '',
                $_POST['apellido'] ?? '',
                $_POST['correo'] ?? '',
                $_POST['password'] ?? '',
                $_POST['direccion'] ?? '',
                $_POST['telefono'] ?? '',
                $_POST['fecha_registro'] ?? '',
                $_POST['fecha_nacimiento'] ?? '',
                $_POST['estatus'] ?? 1,
                $_POST['fk_rol'] ?? 2,
                $_POST['id_usuario'] ?? null
            );

            $res = $Usuario->updateUsuario();
            echo json_encode($res);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Método no permitido.'
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Excepción: ' . $e->getMessage()
        ]);
    }
}

function delete()
{
    header('Content-Type: application/json');
    $Usuario = new UsuariosModel();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $id = $_POST['id_usuario'] ?? null;

            if (empty($id)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID de usuario no recibido.'
                ]);
                return;
            }

            $resultado = $Usuario->deleteUsuario($id);
            echo json_encode($resultado);
            return;

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Excepción: ' . $e->getMessage()
            ]);
            return;
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Método no permitido.'
        ]);
        return;
    }
}

function cambiar_estatus() {
    try {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new UsuariosModel();
            
            $id = $_POST['id_usuario'];
            $nuevo_estatus = $_POST['estatus'] == 1 ? 0 : 1; // Alternar estatus
            
            $result = $model->cambiarEstatus($id, $nuevo_estatus);
            
            if ($result) {
                $_SESSION['flash_success'] = 'Estatus actualizado correctamente';
            } else {
                throw new Exception('Error al cambiar estatus');
            }
            
            header('Location: ' . APP_URL . '/usuarios/index');
            exit;
        }
    } catch(Exception $e) {
        $_SESSION['flash_error'] = $e->getMessage();
        header('Location: ' . APP_URL . '/usuarios/index');
        exit;
    }
}

// Función adicional para ver detalles de usuario
function view() {
    try {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            throw new Exception('ID de usuario no especificado');
        }
        
        $model = new UsuariosModel();
        $data['usuario'] = $model->getUsuarioById($id);
        
        if (!$data['usuario']) {
            throw new Exception('Usuario no encontrado');
        }
        
        render('usuarios/view', $data);
    } catch(Exception $e) {
        $_SESSION['flash_error'] = $e->getMessage();
        header('Location: ' . APP_URL . '/usuarios/index');
        exit;
    }
}