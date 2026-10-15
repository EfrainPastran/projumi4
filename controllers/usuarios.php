<?php
use App\Models\UsuariosModel;
use App\Models\ClienteModel;
use App\Models\rolesModel;
use App\Middleware;

function usuarios_guardar_imgperfil(string $fieldName): array
{
    if (empty($_FILES[$fieldName]) || ($_FILES[$fieldName]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'message' => 'No se recibió una imagen.'];
    }

    $file = $_FILES[$fieldName];
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'No se pudo subir la imagen.'];
    }

    $maxBytes = 3 * 1024 * 1024;
    if (($file['size'] ?? 0) > $maxBytes) {
        return ['success' => false, 'message' => 'La imagen no debe superar 3 MB.'];
    }

    $tmpName = $file['tmp_name'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($tmpName);
    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($allowedTypes[$mime])) {
        return ['success' => false, 'message' => 'Solo se permiten imágenes JPG, PNG o WEBP.'];
    }

    $uploadDir = dirname(__DIR__) . '/public/img/perfiles/';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true)) {
        return ['success' => false, 'message' => 'No se pudo crear la carpeta de perfiles.'];
    }

    $fileName = 'perfil_' . date('YmdHis') . '_' . bin2hex(random_bytes(6)) . '.' . $allowedTypes[$mime];
    $destination = $uploadDir . $fileName;

    if (!move_uploaded_file($tmpName, $destination)) {
        return ['success' => false, 'message' => 'No se pudo guardar la imagen.'];
    }

    return [
        'success' => true,
        'path' => '/public/img/perfiles/' . $fileName,
    ];
}

function usuarios_normalizar_imgperfil(?string $path): string
{
    $path = trim((string) $path);
    return $path !== '' ? $path : '/public/img/default_profile.png';
}
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

function api_perfil()
{
    header('Content-Type: application/json');

    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
            return;
        }

        $token = get_bearer_token();
        if (!$token) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Token no enviado.']);
            return;
        }

        $payload = jwt_decode_rs256($token);
        if ($payload === false || empty($payload['cedula'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Token inválido o expirado.']);
            return;
        }

        $Usuario = new UsuariosModel();
        $usuario = $Usuario->getUsuarioByCedula($payload['cedula']);

        if (!$usuario) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Usuario no encontrado.']);
            return;
        }

        unset($usuario['password']);
        $usuario['imgperfil'] = usuarios_normalizar_imgperfil($usuario['imgperfil'] ?? '');
        $usuario['imagen'] = $usuario['imgperfil'];

        echo json_encode([
            'success' => true,
            'user' => $usuario,
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Excepción: ' . $e->getMessage(),
        ]);
    }
}

function api_actualizar_perfil()
{
    header('Content-Type: application/json');

    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
            return;
        }

        $token = get_bearer_token();
        if (!$token) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Token no enviado.']);
            return;
        }

        $payload = jwt_decode_rs256($token);
        if ($payload === false || empty($payload['cedula'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Token inválido o expirado.']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            $data = $_POST;
        }

        $Usuario = new UsuariosModel();
        $usuarioActual = $Usuario->getUsuarioByCedula($payload['cedula']);

        if (!$usuarioActual) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Usuario no encontrado.']);
            return;
        }

        $Usuario->setData(
            $usuarioActual['cedula'],
            trim((string) ($data['nombre'] ?? $usuarioActual['nombre'] ?? '')),
            trim((string) ($data['apellido'] ?? $usuarioActual['apellido'] ?? '')),
            trim((string) ($data['correo'] ?? $usuarioActual['correo'] ?? '')),
            trim((string) ($data['password'] ?? '')),
            trim((string) ($data['direccion'] ?? $usuarioActual['direccion'] ?? '')),
            preg_replace('/\D+/', '', (string) ($data['telefono'] ?? $usuarioActual['telefono'] ?? '')),
            $usuarioActual['fecha_registro'] ?? date('Y-m-d'),
            $usuarioActual['fecha_nacimiento'] ?? null,
            (int) ($usuarioActual['estatus'] ?? 1),
            (int) ($usuarioActual['fk_rol'] ?? 4),
            $usuarioActual['id_usuario']
        );

        $res = $Usuario->updateUsuario();

        if (!$res['success']) {
            http_response_code(422);
            echo json_encode($res);
            return;
        }

        $usuarioActualizado = $Usuario->getUsuarioByCedula($usuarioActual['cedula']);
        unset($usuarioActualizado['password']);
        $usuarioActualizado['imgperfil'] = usuarios_normalizar_imgperfil($usuarioActualizado['imgperfil'] ?? '');
        $usuarioActualizado['imagen'] = $usuarioActualizado['imgperfil'];

        echo json_encode([
            'success' => true,
            'message' => $res['message'],
            'user' => $usuarioActualizado,
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Excepción: ' . $e->getMessage(),
        ]);
    }
}

function api_actualizar_foto_perfil()
{
    header('Content-Type: application/json');

    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
            return;
        }

        $token = get_bearer_token();
        if (!$token) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Token no enviado.']);
            return;
        }

        $payload = jwt_decode_rs256($token);
        if ($payload === false || empty($payload['cedula'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Token inválido o expirado.']);
            return;
        }

        $Usuario = new UsuariosModel();
        $usuarioActual = $Usuario->getUsuarioByCedula($payload['cedula']);

        if (!$usuarioActual) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Usuario no encontrado.']);
            return;
        }

        $upload = usuarios_guardar_imgperfil('imgperfil');
        if (!$upload['success']) {
            http_response_code(422);
            echo json_encode($upload);
            return;
        }

        $res = $Usuario->updateImgPerfil($usuarioActual['id_usuario'], $upload['path']);
        if (!$res['success']) {
            http_response_code(422);
            echo json_encode($res);
            return;
        }

        $usuarioActualizado = $Usuario->getUsuarioByCedula($payload['cedula']);
        unset($usuarioActualizado['password']);
        $usuarioActualizado['imgperfil'] = usuarios_normalizar_imgperfil($usuarioActualizado['imgperfil'] ?? '');
        $usuarioActualizado['imagen'] = $usuarioActualizado['imgperfil'];

        echo json_encode([
            'success' => true,
            'message' => $res['message'],
            'user' => $usuarioActualizado,
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Excepción: ' . $e->getMessage(),
        ]);
    }
}

function api_cambiar_password()
{
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Metodo no permitido.']);
        return;
    }

    $token = get_bearer_token();
    $payload = $token ? jwt_decode_rs256($token) : false;
    if ($payload === false || empty($payload['cedula'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Token no enviado, invalido o expirado.']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $currentPassword = (string) ($data['current_password'] ?? '');
    $newPassword = (string) ($data['new_password'] ?? '');
    $confirmPassword = (string) ($data['confirm_password'] ?? '');

    if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Debes completar todos los campos de contrasena.']);
        return;
    }

    if ($newPassword !== $confirmPassword) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Las contrasenas no coinciden.']);
        return;
    }

    $Usuario = new UsuariosModel();
    $usuario = $Usuario->getUsuarioByCedula($payload['cedula']);
    if (!$usuario || !password_verify($currentPassword, $usuario['password'])) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'La contrasena actual es incorrecta.']);
        return;
    }

    $resultado = $Usuario->updatePasswordById($usuario['id_usuario'], $newPassword);
    http_response_code($resultado['success'] ? 200 : 422);
    echo json_encode($resultado);
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
