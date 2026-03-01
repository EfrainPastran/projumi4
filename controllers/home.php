<?php
use App\Models\loginModel;
use App\middleware;
use App\Models\UserModel;

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
    // Solo aceptar POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405); // Método no permitido
        echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
        exit;
    }

    // Verificar que haya datos
    if (empty($_POST['cedula']) || empty($_POST['password'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Faltan datos de inicio de sesión.']);
        exit;
    }


    $cedula = $_POST['cedula'];
    $password = $_POST['password'];

    $loginModel = new LoginModel();
    $user = $loginModel->session($cedula, $password);

    if ($user) {
        $_SESSION['user'] = $user;
        $_SESSION['logged_in'] = true;
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    } else {
        header('Content-Type: application/json');
        http_response_code(401); // No autorizado
        echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas.']);
        exit;
    }
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

