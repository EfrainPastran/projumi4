<?php
use App\Models\loginModel;
use App\middleware;
use App\Models\UserModel;
use App\Models\UsuariosModel;
use App\MailService;

function projumi_json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload);
    exit;
}

function projumi_password_reset_session_key(): string
{
    return 'password_reset';
}

function projumi_password_reset_get_state(): ?array
{
    $state = $_SESSION[projumi_password_reset_session_key()] ?? null;
    if (!is_array($state)) {
        return null;
    }

    if (empty($state['user_id']) || empty($state['cedula']) || empty($state['correo'])) {
        unset($_SESSION[projumi_password_reset_session_key()]);
        return null;
    }

    return $state;
}

function projumi_password_reset_store_state(array $state): void
{
    $_SESSION[projumi_password_reset_session_key()] = $state;
}

function projumi_password_reset_clear_state(): void
{
    unset($_SESSION[projumi_password_reset_session_key()]);
}

function projumi_password_reset_normalize_cedula($value): string
{
    $normalized = preg_replace('/\D+/', '', (string) $value);
    return is_string($normalized) ? $normalized : '';
}

function projumi_password_reset_mask_email(string $email): string
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return $email;
    }

    [$localPart, $domain] = explode('@', $email, 2);
    $visibleChars = min(2, strlen($localPart));
    $maskedLocal = substr($localPart, 0, $visibleChars);
    $maskedLocal .= str_repeat('*', max(1, strlen($localPart) - $visibleChars));

    return $maskedLocal . '@' . $domain;
}

function projumi_password_reset_validate_password(string $password, string $confirmPassword): ?string
{
    if ($password === '' || $confirmPassword === '') {
        return 'Debes completar la nueva contraseña y su confirmacion.';
    }

    if (strlen($password) < 8) {
        return 'La nueva contraseña debe tener al menos 8 caracteres.';
    }

    if ($password !== $confirmPassword) {
        return 'Las contraseñas no coinciden.';
    }

    return null;
}

function principal() {    

    $login = new loginModel();
    $middleware = new middleware();
    $detalles = [];
    // Vista tradicional para GET
    $_SESSION['user']['error'] = 'emprendimiento';
    if (isset($_SESSION['user'])) {
        $cedula = $_SESSION['user']['cedula'];
        $middleware->verificarTipoUsuario($cedula);
    }
    render('home/principalclient', ['detalles' => $detalles]);
}


function api_login() {
    // Endpoint API móvil para iniciar sesión y devolver JWT.
    // Este método acepta JSON con cedula/password y devuelve un token RS256.
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405); // Método no permitido
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
        exit;
    }

    $rateLimit = projumi_rate_limit_check('login', RATE_LIMIT_LOGIN_MAX, RATE_LIMIT_LOGIN_WINDOW);
    if (!$rateLimit['allowed']) {
        projumi_rate_limit_response((int) $rateLimit['retry_after'], 'Demasiados intentos de inicio de sesión. Intenta de nuevo más tarde.');
    }

    $data = get_json_request_body();
    $cedula = trim($data['cedula'] ?? '');
    $password = trim($data['password'] ?? '');

    if ($cedula === '' || $password === '') {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Faltan datos de inicio de sesión.']);
        exit;
    }

    $loginModel = new LoginModel();
    $user = $loginModel->session($cedula, $password);

    if (!$user) {
        header('Content-Type: application/json');
        http_response_code(401); // No autorizado
        echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas.']);
        exit;
    }

    unset($user['password']);

    try {
        $payload = [
            'iss' => APP_URL,
            'aud' => APP_URL,
            'iat' => time(),
            'exp' => time() + JWT_EXPIRATION_SECONDS,
            'sub' => $user['id_usuario'],
            'cedula' => $user['cedula'],
            'correo' => $user['correo'] ?? null
        ];

        $token = jwt_encode_rs256($payload);
        if ($token === false) {
            throw new Exception('No se pudo generar el token.');
        }

        // Iniciar sesión PHP para el acceso web tradicional.
        $_SESSION['user'] = $user;
        $_SESSION['logged_in'] = true;

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'token' => $token,
            'user' => $user
        ]);
        exit;
    } catch (Exception $e) {
        error_log('JWT generation error: ' . $e->getMessage());
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error interno al generar el token.']);
        exit;
    }
}

function request_password_reset_code()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        projumi_json_response(['success' => false, 'message' => 'Metodo no permitido.'], 405);
    }

    $rateLimit = projumi_rate_limit_check('password_reset_request', 5, 900);
    if (!$rateLimit['allowed']) {
        projumi_rate_limit_response((int) $rateLimit['retry_after'], 'Demasiadas solicitudes de recuperacion. Intenta nuevamente en unos minutos.');
    }

    $data = get_json_request_body();
    $cedula = projumi_password_reset_normalize_cedula($data['cedula'] ?? '');

    if ($cedula === '' || strlen($cedula) < 6) {
        projumi_json_response(['success' => false, 'message' => 'Debes indicar una cedula valida.'], 422);
    }

    $currentState = projumi_password_reset_get_state();
    if ($currentState && ($currentState['cedula'] ?? '') === $cedula) {
        $cooldownUntil = (int) ($currentState['cooldown_until'] ?? 0);
        if ($cooldownUntil > time()) {
            projumi_json_response([
                'success' => false,
                'message' => 'Espera unos segundos antes de solicitar un nuevo codigo.',
                'retry_after' => $cooldownUntil - time(),
            ], 429);
        }
    }

    $usuariosModel = new UsuariosModel();
    $user = $usuariosModel->getUsuarioByCedula($cedula);

    if (!$user || empty($user['id_usuario']) || empty($user['correo']) || (int) ($user['estatus'] ?? 0) !== 1) {
        projumi_password_reset_clear_state();
        projumi_json_response([
            'success' => false,
            'message' => 'No encontramos un usuario activo con esa cedula.'
        ], 404);
    }

    $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $fullName = trim((string) (($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? '')));

    try {
        $mailer = new MailService();
        $mailer->sendPasswordResetCode((string) $user['correo'], $fullName, $code);
    } catch (Exception $exception) {
        error_log('Error enviando codigo de recuperacion: ' . $exception->getMessage());
        projumi_json_response([
            'success' => false,
            'message' => 'No se pudo enviar el codigo al correo registrado. Revisa la configuracion del servidor de correo.'
        ], 500);
    }

    projumi_password_reset_store_state([
        'user_id' => (int) $user['id_usuario'],
        'cedula' => $cedula,
        'correo' => (string) $user['correo'],
        'code_hash' => password_hash($code, PASSWORD_DEFAULT),
        'attempts' => 0,
        'expires_at' => time() + PASSWORD_RESET_CODE_TTL,
        'verified' => false,
        'verified_at' => null,
        'verified_expires_at' => null,
        'cooldown_until' => time() + PASSWORD_RESET_RESEND_COOLDOWN,
    ]);

    projumi_json_response([
        'success' => true,
        'message' => 'Enviamos un código de verificación al correo registrado.',
        'masked_email' => projumi_password_reset_mask_email((string) $user['correo']),
        'expires_in' => PASSWORD_RESET_CODE_TTL,
    ]);
}

function verify_password_reset_code()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        projumi_json_response(['success' => false, 'message' => 'Metodo no permitido.'], 405);
    }

    $rateLimit = projumi_rate_limit_check('password_reset_verify', 10, 900);
    if (!$rateLimit['allowed']) {
        projumi_rate_limit_response((int) $rateLimit['retry_after'], 'Demasiados intentos de verificación. Intenta nuevamente más tarde.');
    }

    $data = get_json_request_body();
    $cedula = projumi_password_reset_normalize_cedula($data['cedula'] ?? '');
    $code = trim((string) ($data['codigo'] ?? ''));

    if ($cedula === '' || $code === '') {
        projumi_json_response(['success' => false, 'message' => 'Debes indicar la cedula y el codigo recibido.'], 422);
    }

    $state = projumi_password_reset_get_state();
    if (!$state || ($state['cedula'] ?? '') !== $cedula) {
        projumi_json_response(['success' => false, 'message' => 'Primero solicita un codigo de recuperacion para esa cedula.'], 409);
    }

    if ((int) ($state['expires_at'] ?? 0) < time()) {
        projumi_password_reset_clear_state();
        projumi_json_response(['success' => false, 'message' => 'El codigo ya vencio. Solicita uno nuevo.'], 410);
    }

    $attempts = (int) ($state['attempts'] ?? 0);
    if ($attempts >= PASSWORD_RESET_MAX_ATTEMPTS) {
        projumi_password_reset_clear_state();
        projumi_json_response(['success' => false, 'message' => 'Se agotaron los intentos permitidos. Solicita un nuevo codigo.'], 429);
    }

    if (!password_verify($code, (string) ($state['code_hash'] ?? ''))) {
        $state['attempts'] = $attempts + 1;
        projumi_password_reset_store_state($state);

        $remaining = max(0, PASSWORD_RESET_MAX_ATTEMPTS - (int) $state['attempts']);
        $message = $remaining > 0
            ? 'El codigo es incorrecto. Intentos restantes: ' . $remaining . '.'
            : 'Se agotaron los intentos permitidos. Solicita un nuevo codigo.';

        if ($remaining === 0) {
            projumi_password_reset_clear_state();
        }

        projumi_json_response(['success' => false, 'message' => $message], 422);
    }

    $state['verified'] = true;
    $state['verified_at'] = time();
    $state['verified_expires_at'] = time() + PASSWORD_RESET_VERIFIED_TTL;
    $state['code_hash'] = null;
    $state['attempts'] = 0;
    projumi_password_reset_store_state($state);

    projumi_json_response([
        'success' => true,
        'message' => 'Codigo verificado correctamente. Ya puedes crear tu nueva contraseña.'
    ]);
}

function complete_password_reset()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        projumi_json_response(['success' => false, 'message' => 'Metodo no permitido.'], 405);
    }

    $data = get_json_request_body();
    $cedula = projumi_password_reset_normalize_cedula($data['cedula'] ?? '');
    $password = trim((string) ($data['password'] ?? ''));
    $confirmPassword = trim((string) ($data['confirm_password'] ?? ''));

    if ($cedula === '') {
        projumi_json_response(['success' => false, 'message' => 'Debes indicar la cedula del usuario.'], 422);
    }

    $validationError = projumi_password_reset_validate_password($password, $confirmPassword);
    if ($validationError !== null) {
        projumi_json_response(['success' => false, 'message' => $validationError], 422);
    }

    $state = projumi_password_reset_get_state();
    if (!$state || ($state['cedula'] ?? '') !== $cedula) {
        projumi_json_response(['success' => false, 'message' => 'La sesion de recuperacion no es valida para esa cedula.'], 409);
    }

    if (empty($state['verified']) || (int) ($state['verified_expires_at'] ?? 0) < time()) {
        projumi_password_reset_clear_state();
        projumi_json_response(['success' => false, 'message' => 'Debes verificar un codigo vigente antes de cambiar la contraseña.'], 410);
    }

    $usuariosModel = new UsuariosModel();
    $result = $usuariosModel->updatePasswordById((int) $state['user_id'], $password);

    if (!is_array($result) || !($result['success'] ?? false)) {
        $message = is_array($result) && !empty($result['message'])
            ? $result['message']
            : 'No se pudo actualizar la contraseña.';
        projumi_json_response(['success' => false, 'message' => $message], 422);
    }

    projumi_password_reset_clear_state();
    projumi_json_response([
        'success' => true,
        'message' => 'Tu contraseña fue actualizada correctamente.'
    ]);
}

function index() {
    $_SESSION['user']['error'] = 'home';
    render('home/index');
}

function principal2() {
    $l = new loginModel();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
            $l->set_cedula($_POST['cedula']);
            $l->set_password($_POST['password']);
            
            // Obtener valores
            $cedula = $l->get_cedula();
            $password = $l->get_password();
        
        $loginModel = new loginModel();
        $user = $loginModel->session($cedula, $password);
       
        if ($user) {
            $_SESSION['user'] = $user;
            $_SESSION['logged_in'] = true;
            
            // Redirige a una página de éxito, no a la página de error
            header("Location: " . APP_URL . "/home/principal");
            exit;
        } else {
            echo "Credenciales incorrectas. Intente nuevamente.";
        }
    }

    render('home/principal');

}

function logout(){

        if (isset($_POST["logout"])){
            session_unset();
            session_destroy();
            header('Location: ' . APP_URL);
            exit();
        }

      
    
}

function perfil() {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
        if (isset($_POST["actualizar"])) {
            $id_usuario = $_POST['id_usuario'];
            $cedula = $_POST['cedula'];
            $nombre = $_POST['nombre'];
            $email = $_POST['email'];
            
            // Llamar al modelo para actualizar
            $userModel = new UserModel();
            $result = $userModel->updateProfile($id_usuario, $cedula, $nombre, $email);
            
            if ($result) {
               echo "USUARO ACTUALIZADO";
               // header('Location: ' . BASE_URL . 'user/consultar');
            } else {
                // Mostrar error
                echo "Error al actualizar el usuario";
            }
        }

        if (isset($_POST["Cambiarpassword"])) {
            $id_usuario = $_POST['id_usuario'];
            $pass_actual = $_POST['pass'];
            $nueva_pass = $_POST['nuevopass'];
            $confirm_pass = $_POST['confirmpass'];
            
            // Validaciones básicas
            if (empty($pass_actual)) {
                echo "Debe ingresar la contraseña actual";
                return;
            }
            
            if ($nueva_pass !== $confirm_pass) {
                echo "Las contraseñas nuevas no coinciden";
                return;
            }
            
            if (strlen($nueva_pass) < 8) {
                echo "La nueva contraseña debe tener al menos 8 caracteres";
                return;
            }

            
            
            // Llamar al modelo para actualizar
            $userModel = new UserModel();
            $result = $userModel->updatePass($id_usuario, $pass_actual, $nueva_pass);
             
            if ($result === true) {
                echo "Contraseña actualizada correctamente";
                // Opcional: cerrar sesión y redirigir a login
                // header('Location: ' . BASE_URL . 'user/logout');
            } else {
                // Mostrar error específico
                echo $result ?? "Error al actualizar la contraseña";
            }
        }


    }
    $cedula = $_SESSION['user']['cedula'];
    $middleware = new middleware();

    $tipoUsuario = $middleware->verificarTipoUsuario($cedula);
    //Vista para el emprendedor
    if ('emprendedor' == $tipoUsuario[0]) {
        $menu = "headerEmprendedor";
    }
    //Vista para el cliente
    else if ('cliente' == $tipoUsuario[0]) {
        $menu = "headerCliente";
    }
    //Vista para el usuario
    else {
        $menu = "headeradmin";
    }
    render('user/perfil', ['menu' => $menu]);
}

