<?php
//listo
use App\Models\UserModel;
use App\Models\UsuariosModel;
use App\Models\ClienteModel;

function register() { //esta demas el POST solo redirecciona
    $data = ['title' => 'Registro', 'error' => ''];
    $o = new UserModel(); // Este modelo solo manipula datos del formulario

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Preparar datos
            $o->set_cedula($_POST['cedula']);
            $o->set_fecha_nacimiento($_POST['fecha_nacimiento']);
            $o->set_nombre($_POST['nombre']);
            $o->set_apellido($_POST['apellido']);
            $o->set_telefono($_POST['telefono']);
            $o->set_email($_POST['email']);
            $o->set_direccion($_POST['direccion']);
            $o->set_password($_POST['password']);

            // Recoger los datos
            $cedula = $o->get_cedula();
            $fecha_nacimiento = $o->get_fecha_nacimiento();
            $nombre = $o->get_nombre();
            $apellido = $o->get_apellido();
            $telefono = $o->get_telefono();
            $email = $o->get_email();
            $direccion = $o->get_direccion();
            $password = $o->get_password();

            // REGISTRO DEL USUARIO
            $Usuario = new UsuariosModel();
            $Usuario->setData(
                NULL,
                $cedula,
                $nombre,
                $apellido,
                $email,
                $password,
                $direccion,
                $telefono,
                date('Y-m-d H:i:s'), // Fecha registro
                $fecha_nacimiento,
                1, // Estatus activo
                4  // Rol de cliente
            );
            $Usuario->registerUsuario();

            // REGISTRO DEL CLIENTE
            $Cliente = new ClienteModel();
            $Cliente->setData(
                null,
                $cedula,
                date('Y-m-d'), // fecha de registro cliente
                1 // estatus activo
            );
            $result = $Cliente->registerCliente();

            if ($result === true) {
                $_SESSION['registro_exitoso'] = true;
                header('Location: ' . APP_URL . '/auth/register'); // Redirigir a la misma vista
                exit;
            }
            


        } catch (Exception $e) {
            $data['error'] = $e->getMessage();
            $data['form_data'] = [
                'cedula' => htmlspecialchars($cedula ?? ''),
                'correo' => htmlspecialchars($email ?? '')
            ];
        }
    }

    render('user/register', $data);
}

function registrarUsuarioCliente() {
    try {
        header('Content-Type: application/json');
        $Usuario = new UsuariosModel();
        $Cliente = new ClienteModel();

        $verificarUsuario = $Usuario->getUsuarioByCedula($_POST['cedula']);

        if (!$verificarUsuario) {
            // Establecer datos para el usuario
            $Usuario->setData(
                $_POST['cedula'],
                $_POST['nombre'],
                $_POST['apellido'],
                $_POST['email'],
                $_POST['password'],
                $_POST['direccion'],
                $_POST['telefono'],
                date('Y-m-d'),             // fecha_registro
                $_POST['fecha_nacimiento'],
                1,                                // estatus activo
                4                                 // rol cliente
            );

            $Usuario->registerUsuario();
        }

        $verificarCliente = $Cliente->getByCedula($_POST['cedula']);
        if ($verificarCliente && is_array($verificarCliente)) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Cliente ya existe'
            ]);
            exit;
        }

        // Establecer datos para el cliente
        $Cliente->setData(
            null,
            $_POST['cedula'],
            date('Y-m-d'),
            1 // estatus
        );

        // Registrar en DB
        $id_cliente = $Cliente->registerCliente();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'id_cliente' => $id_cliente
        ]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}


function consultar() {
    // Cargar el modelo de usuario
    $userModel = new UserModel();
    
    // Obtener los usuarios
    $usuarios = $userModel->getUsers();
    
    // Si es una petición AJAX, devolver JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['data' => $usuarios]);
        exit;
    }
        
    // Renderizar la vista normal si no es AJAX
    render('user/admin', ['usuarios' => $usuarios]);
}

function getUser($id) {
    $userModel = new UserModel();
    $user = $userModel->getUserById($id);
    
    header('Content-Type: application/json');
    echo json_encode($user);
    exit;
}

function admin() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $userModel = new UserModel();
            $result = $userModel->deleteUser($_POST['id_usuario']);
            
            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $userModel = new UserModel();
            
            // Recoger todos los datos del POST
            $data = [
                'id_usuario' => $_POST['id_usuario'],
                'nombre' => $_POST['nombre'],
                'apellido' => $_POST['apellido'] ?? null,
                'email' => $_POST['email'],
                'rol' => $_POST['rol'] ?? 'user',
                'estado' => $_POST['estado'] ?? 'activo',
                'pass' => $_POST['pass'] ?? null
            ];
            
            // Validaciones
            if (empty($data['nombre']) || empty($data['email'])) {
                throw new Exception('Nombre y email son obligatorios');
            }
            
            // Actualizar
            $result = $userModel->updateUser($data);
            
            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
    
    render('user/admin');
}

function ayuda() {
    if (!isset($_SESSION['user']['cedula'])) {
        header('Location: ../home/index');
        exit;
    }
        render('ayuda/index');
}

function logout() {
    if(isset($_SESSION['user'])){
        session_destroy();
        render('home/index');
        exit;
    }
}









