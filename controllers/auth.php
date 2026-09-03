<?php
//listo
use App\Models\UserModel;
use App\Models\UsuariosModel;
use App\Models\ClienteModel;

function auth_json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload);
    exit;
}

function auth_only_digits($value): string
{
    return preg_replace('/\D+/', '', (string) $value) ?? '';
}

function auth_calculate_age(string $date): ?int
{
    try {
        $birthDate = new DateTime($date);
        $today = new DateTime('today');

        if ($birthDate > $today) {
            return null;
        }

        return (int) $birthDate->diff($today)->y;
    } catch (Exception $e) {
        return null;
    }
}

function auth_validate_cliente_payload(array $data): array
{
    $errors = [];
    $cedula = auth_only_digits($data['cedula'] ?? '');
    $nombre = trim((string) ($data['nombre'] ?? ''));
    $apellido = trim((string) ($data['apellido'] ?? ''));
    $email = trim((string) ($data['email'] ?? ''));
    $telefono = auth_only_digits($data['telefono'] ?? '');
    $direccion = trim((string) ($data['direccion'] ?? ''));
    $fechaNacimiento = trim((string) ($data['fecha_nacimiento'] ?? ''));
    $password = (string) ($data['password'] ?? '');
    $confirmPassword = (string) ($data['confirm_pass'] ?? '');

    if (!preg_match('/^\d{7,10}$/', $cedula)) {
        $errors[] = 'La cedula debe tener entre 7 y 10 digitos.';
    }

    $edad = auth_calculate_age($fechaNacimiento);
    if ($edad === null || $edad < 18) {
        $errors[] = 'El cliente debe ser mayor de edad.';
    }

    if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,45}$/u', $nombre)) {
        $errors[] = 'El nombre debe contener solo letras y tener entre 2 y 45 caracteres.';
    }

    if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,45}$/u', $apellido)) {
        $errors[] = 'El apellido debe contener solo letras y tener entre 2 y 45 caracteres.';
    }

    if (!preg_match('/^04(12|22|16|26|14|24)\d{7}$/', $telefono)) {
        $errors[] = 'El telefono debe tener un codigo valido y 7 digitos adicionales.';
    }

    if (strlen($email) > 45 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'El correo debe ser valido y no superar 45 caracteres.';
    }

    if (strlen($direccion) < 5 || strlen($direccion) > 60) {
        $errors[] = 'La direccion debe tener entre 5 y 60 caracteres.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'La contrasena debe tener minimo 8 caracteres.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Las contrasenas no coinciden.';
    }

    return [
        'success' => empty($errors),
        'errors' => $errors,
        'data' => [
            'cedula' => $cedula,
            'nombre' => preg_replace('/\s+/', ' ', $nombre),
            'apellido' => preg_replace('/\s+/', ' ', $apellido),
            'email' => $email,
            'telefono' => $telefono,
            'direccion' => $direccion,
            'fecha_nacimiento' => $fechaNacimiento,
            'password' => $password,
        ],
    ];
}

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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            auth_json_response([
                'success' => false,
                'message' => 'Metodo no permitido.'
            ], 405);
        }

        $validation = auth_validate_cliente_payload($_POST);
        if (!$validation['success']) {
            auth_json_response([
                'success' => false,
                'message' => implode(' ', $validation['errors']),
                'errors' => $validation['errors']
            ], 422);
        }

        $payload = $validation['data'];
        $Usuario = new UsuariosModel();
        $Cliente = new ClienteModel();

        $verificarCliente = $Cliente->getByCedula($payload['cedula']);
        if ($verificarCliente && is_array($verificarCliente)) {
            auth_json_response([
                'success' => false,
                'message' => 'Cliente ya existe'
            ], 409);
        }

        $verificarUsuario = $Usuario->getUsuarioByCedula($payload['cedula']);

        if (!$verificarUsuario) {
            $Usuario->setData(
                $payload['cedula'],
                $payload['nombre'],
                $payload['apellido'],
                $payload['email'],
                $payload['password'],
                $payload['direccion'],
                $payload['telefono'],
                date('Y-m-d H:i:s'),
                $payload['fecha_nacimiento'],
                1,
                4
            );

            $resultadoUsuario = $Usuario->registerUsuario();
            if (!is_array($resultadoUsuario) || !($resultadoUsuario['success'] ?? false)) {
                auth_json_response([
                    'success' => false,
                    'message' => $resultadoUsuario['message'] ?? 'No se pudo registrar el usuario.'
                ], 422);
            }
        }

        $resultadoCliente = $Cliente->registerCliente([
            'cedula' => $payload['cedula'],
            'nombre' => $payload['nombre'],
            'apellido' => $payload['apellido'],
            'correo' => $payload['email'],
            'direccion' => $payload['direccion'],
            'telefono' => $payload['telefono'],
            'fecha_nacimiento' => $payload['fecha_nacimiento'],
        ]);

        if (!is_array($resultadoCliente) || !($resultadoCliente['success'] ?? false)) {
            auth_json_response([
                'success' => false,
                'message' => $resultadoCliente['message'] ?? 'No se pudo registrar el cliente.'
            ], 422);
        }

        auth_json_response([
            'success' => true,
            'message' => $resultadoCliente['message'] ?? 'Cliente registrado exitosamente.',
            'id_cliente' => $resultadoCliente['id_cliente'] ?? null
        ]);
    } catch (Exception $e) {
        auth_json_response([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
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









