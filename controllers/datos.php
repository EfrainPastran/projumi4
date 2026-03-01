<?php
use App\Models\DatosModel;
use App\Models\MetodoModel;
use App\Models\EmprendedorModel;
use App\Models\UserModel;
use App\Middleware;
function index() {
    if (!isset($_SESSION['user']['cedula'])) {
        header('Location: ../home/index');
        exit;
    }
    
    // Cargar modelos
    $DatosCuenta = new DatosModel();
    $getmetodo = new MetodoModel();

    // Obtener id del emprendedor
    $Usuario = new UserModel();
    $Emprendedor = new EmprendedorModel();
    $idUsuario = $_SESSION['user']['id_usuario'];
    $cedulaUsuario = $Usuario->obtenerCedulaPorId($idUsuario);

    $idEmprendedor = $Emprendedor->obtenerIdEmprendedorPorRif($cedulaUsuario);
    
    // Obtener datos
    $data['datos_cuentas'] = $DatosCuenta->getmetodosemprededor($idEmprendedor);
    $data['metodo_pago'] = $getmetodo->obtenerMetodos();
    // Verificar tipo de usuario
    $cedula = $_SESSION['user']['cedula'];
    $Middleware = new Middleware(); 
    $tipoUsuario = $Middleware->verificarTipoUsuario($cedula);
    if (!'emprendedor' == $tipoUsuario[0]) {
        header('Location: ../home/index');
        exit;
    }
    render('datos/index', $data);
}

/*$cedula = $_SESSION['user']['cedula'];
    $tipoUsuario = verificarTipoUsuario($cedula);
    $datos = $DatosCuenta->consultarDatos($cedula);
    if ($tipoUsuario[0] === 'emprendedor') {
        $title = "Mis métodos de pago";
        $menu = "headerEmprendedor";
    } else {
        header('Location: ../home/index');
        exit;
    }

    render('datos/index', ['datos' => $datos, 'title' => $title, 'menu' => $menu]);
}*/

function register() {
    if (!isset($_SESSION['user']['cedula'])) {
        header('Location: ../home/index');
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Cargar modelos necesarios
            $DatosCuenta = new DatosModel();
            $Usuario = new UserModel();
            $Emprendedor = new EmprendedorModel();
            
            // Obtener id del emprendedor
            $idUsuario = $_SESSION['user']['id_usuario'];
            $cedulaUsuario = $Usuario->obtenerCedulaPorId($idUsuario);
            
            if (!$cedulaUsuario) {
                throw new Exception('No se encontró un usuario válido.');
            }
            
            $idEmprendedor = $Emprendedor->obtenerIdEmprendedorPorRif($cedulaUsuario);
            
            if (!$idEmprendedor) {
                throw new Exception('No se encontró un emprendedor activo vinculado a este usuario.');
            }
            
            // Validar datos
            if (empty($_POST['telefono']) || empty($_POST['banco']) || empty($_POST['correo']) || 
                empty($_POST['numero_cuenta']) || empty($_POST['fk_metodo_pago'])) {
                throw new Exception('Todos los campos son obligatorios.');
            }
            
            // Configurar datos
            $DatosCuenta->setData(
                null,
                $_POST['telefono'],
                $_POST['banco'],
                $_POST['correo'],
                $_POST['numero_cuenta'],
                $idEmprendedor,
                $_POST['fk_metodo_pago']
            );
            
            // Registrar
            $result = $DatosCuenta->create();
            
            if ($result) {
                $_SESSION['flash_success'] = '¡Datos de cuenta registrados exitosamente!';
            } else {
                $_SESSION['flash_error'] = 'Error al registrar los datos de la cuenta.';
            }
            
        } catch(Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }
        
        header('Location: ' . APP_URL . '/datos/index');
        exit;
    }
}

function update() {
    if (!isset($_SESSION['user']['cedula'])) {
        header('Location: ../home/index');
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $DatosCuenta = new DatosModel();
            $Usuario = new UserModel(); 
            $Emprendedor = new EmprendedorModel();  
            
            // Obtener id del emprendedor
            $idUsuario = $_SESSION['user']['id_usuario'];
            $cedulaUsuario = $Usuario->obtenerCedulaPorId($idUsuario);
            
            if (!$cedulaUsuario) {
                throw new Exception('No se encontró un usuario válido.');
            }
            
            $idEmprendedor = $Emprendedor->obtenerIdEmprendedorPorRif($cedulaUsuario);
            
            if (!$idEmprendedor) {
                throw new Exception('No se encontró un emprendedor activo vinculado a este usuario.');
            }
            
            // Validar que los datos a actualizar pertenezcan al emprendedor
            $datosActuales = $DatosCuenta->getById($_POST['id_datos_cuenta']);
            if ($datosActuales['fk_emprendedor'] != $idEmprendedor) {
                throw new Exception('No tiene permiso para modificar estos datos.');
            }
            
            // Configurar datos
            $DatosCuenta->setData(
                $_POST['id_datos_cuenta'],
                $_POST['telefono'],
                $_POST['banco'],
                $_POST['correo'],
                $_POST['numero_cuenta'],
                $idEmprendedor,
                $_POST['fk_metodo_pago']
            );
            
            // Actualizar
            $result = $DatosCuenta->update();
            
            if ($result) {
                $_SESSION['flash_success'] = '¡Datos de cuenta actualizados exitosamente!';
            } else {
                $_SESSION['flash_error'] = 'Error al actualizar los datos de la cuenta.';
            }
            
        } catch(Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }
        
        header('Location: ' . APP_URL . '/datos/index');
        exit;
    }
}

function delete() {
    if (!isset($_SESSION['user']['cedula'])) {
        header('Location: ../home/index');
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $DatosCuenta = new DatosModel();
            $Usuario = new UserModel();
            $Emprendedor = new EmprendedorModel();
            
            // Obtener id del emprendedor
            $idUsuario = $_SESSION['user']['id_usuario'];
            $cedulaUsuario = $Usuario->obtenerCedulaPorId($idUsuario);
            
            if (!$cedulaUsuario) {
                throw new Exception('No se encontró un usuario válido.');
            }
            
            $idEmprendedor = $Emprendedor->obtenerIdEmprendedorPorRif($cedulaUsuario);
            
            if (!$idEmprendedor) {
                throw new Exception('No se encontró un emprendedor activo vinculado a este usuario.');
            }
            
            // Validar que los datos a eliminar pertenezcan al emprendedor
            $datosActuales = $DatosCuenta->getById($_POST['id']);
            if ($datosActuales['fk_emprendedor'] != $idEmprendedor) {
                throw new Exception('No tiene permiso para eliminar estos datos.');
            }
            
            // Eliminar
            $DatosCuenta->setData($_POST['id'], '', '', '', '', 0, 0);
            $result = $DatosCuenta->delete();
            
            if ($result) {
                $_SESSION['flash_success'] = '¡Datos de cuenta eliminados exitosamente!';
            } else {
                $_SESSION['flash_error'] = 'Error al eliminar los datos de la cuenta.';
            }
            
        } catch(Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }
        
        header('Location: ' . APP_URL . '/datos/index');
        exit;
    }
}


function consultarDatos() {
    try {
        $fk_emprendedor = filter_input(INPUT_GET, 'id_emprendedor', FILTER_VALIDATE_INT);
        $fk_metodo_pago = filter_input(INPUT_GET, 'id_metodo_pago', FILTER_VALIDATE_INT);

        if (!$fk_emprendedor || !$fk_metodo_pago) {
            throw new Exception("Parámetros inválidos");
        }

        $DatosCuenta = new DatosModel();
        $dato = $DatosCuenta->obtenerDatosPagar($fk_emprendedor, $fk_metodo_pago);

        if (!$dato) {
            throw new Exception("No se encontraron datos para este emprendedor y método de pago");
        }

        echo json_encode(['success' => true, 'data' => $dato]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    return;
}