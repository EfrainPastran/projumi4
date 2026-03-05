<?php
use App\Models\EmprendedorModel;
use App\Models\UsuariosModel;
use App\Models\ClienteModel;
use App\Middleware;
function index() {
    if (!isset($_SESSION['user']['cedula'])) {
        header('Location: ../home/index');
        exit;
    }

    $cedula = $_SESSION['user']['cedula'];
    $middleware = new Middleware();
    $tipoUsuario = $middleware->verificarTipoUsuario($cedula);
    if ($tipoUsuario[0] === 'emprendedor' || $tipoUsuario[0] === 'cliente') {
        header('Location: ../home/index');
        exit;
    }
    $model = new EmprendedorModel();
    $municipios = $model->obtenerMunicipios();
    $municipiosConParroquias = $model->obtenerMunicipiosConParroquias();
    $data = $model->listarEmprendedores();

    $rol = $_SESSION['user']['rol'];
    $permisos = $middleware->obtenerPermisosDinamicos($rol['rol'], 'Emprendedores');

    if (!$permisos['consultar']) {
            header('Location: ../home/principal');
            exit;
    }


    render('emprendedor/index', [
        'data' => $data, 
        'municipios' => $municipios, 
        'municipiosConParroquias' => $municipiosConParroquias,
        'permisos' => $permisos
    ]);
}

function mostrarEmprendedores() {
    $Emprendedor = new EmprendedorModel();
    //$Emprendedor = loadModel('Emprendedor');

    $emprendedores = $Emprendedor->getEmprendedores();

    $detalles = [];

    foreach ($emprendedores as $emprendedor) {
        $id = $emprendedor['id_emprededor'];
        if (!isset($detalles[$id])) {
            $detalles[$id] = [
                'id_emprendedor' => $id,
                'nombre_completo' => $emprendedor['nombre'] . ' ' . $emprendedor['apellido'],
                'emprendimiento' => $emprendedor['emprendimiento'],
                'imagen' => $emprendedor['imagen_emprendedor'],
                'categorias' => []
            ];
        }

        $detalles[$id]['categorias'][] = [
            'id_categoria' => $emprendedor['id_categoria'],
            'nombre' => $emprendedor['nombre_categoria'],
            'descripcion' => $emprendedor['descripcion_categoria'],
            'cantidad_productos' => $emprendedor['cantidad_productos']
        ];
    }

    // Convertimos a array indexado
    $resultado = array_values($detalles);
    echo json_encode($resultado);
}

function registrar() {
if (!isset($_SESSION['user']['cedula'])) {
        header('Location: ../home/index');
        exit;
    }

    $cedula = $_SESSION['user']['cedula'];
    $Middleware = new Middleware();
    $tipoUsuario = $Middleware->verificarTipoUsuario($cedula);
    if ($tipoUsuario[0] === 'emprendedor' || $tipoUsuario[0] === 'cliente') {
        header('Location: ../home/index');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $clienteModel = new ClienteModel();
        $emprendedorModel = new EmprendedorModel();

        $cedula = $_POST['cedula'];
        $correo = $_POST['correo'];

        try {
            
            $cedula = $_POST['cedula'];
            $correo = $_POST['correo'];
            if ($emprendedorModel->getByCedula($cedula)) {
                $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'El emprendedor ya está registrado.'];
                redirect('emprendedor/index');
            }

            $rutaImagen = null;
            if (!empty($_FILES['imagen']['tmp_name'])) {
                $nombreArchivo = basename($_FILES['imagen']['name']);
                $codigoUnico = uniqid();
                $rutaDestino = "public/emprendedores/{$codigoUnico}_{$nombreArchivo}";
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
                    $rutaImagen = $rutaDestino;
                }
            }
            // Crear cliente-usuario
            if (!$clienteModel->getByCedula($cedula)) {

                $clienteData = [
                    'cedula' => $cedula,
                    'nombre' => $_POST['nombre'],
                    'apellido' => $_POST['apellido'],
                    'correo' => $correo,
                    'telefono' => $_POST['telefono'] ?? '',
                    'direccion' => $_POST['direccion'] ?? '',
                    'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null
                ];

                $resultadoCliente = $clienteModel->registerCliente($clienteData);

                if (!$resultadoCliente['success']) {
                    $_SESSION['mensaje'] = [
                        'tipo' => 'danger',
                        'texto' => 'Error al registrar como cliente: ' . $resultadoCliente['message']
                    ];
                    redirect('emprendedor/index');
                }
            }

            $emprendedorModel->setData(
                null,
                $cedula,
                $_POST['lugar_nacimiento'] ?? '',
                $_POST['estado_civil'] ?? '',
                $_POST['nacionalidad'] ?? '',
                $_POST['rif'] ?? '',
                $_POST['sexo'] ?? '',
                $_POST['alergia_medicamento'] ?? '',
                $_POST['alergia_alimento'] ?? '',
                $_POST['operado'] ?? '',
                $_POST['sacramento'] ?? '',
                $_POST['grupo_sangre'] ?? '',
                $_POST['religion'] ?? '',
                $_POST['grupo_activo'] ?? '',
                $_POST['cantidad_hijos'] ?? '',
                $_POST['carga_familiar'] ?? '',
                $_POST['casa_propia'] ?? '',
                $_POST['alquiler'] ?? '',
                $_POST['titulo_academico'] ?? '',
                $_POST['profesion'] ?? '',
                $_POST['oficio'] ?? '',
                $_POST['hobby'] ?? '',
                $_POST['conocimiento_projumi'] ?? '',
                $_POST['motivo_projumi'] ?? '',
                $_POST['aporte_projumi'] ?? '',
                $rutaImagen,
                $_POST['emprendimiento'] ?? '',
                0,
                $_POST['fk_parroquia'] ?? null
            );
            $resultado = $emprendedorModel->registrarEmprendedor();

            if (!$resultado['success']) {
                $_SESSION['mensaje'] = [
                    'tipo' => 'danger',
                    'texto' => $resultado['message']
                ];
            } else {
                $_SESSION['mensaje'] = [
                    'tipo' => 'success',
                    'texto' => $resultado['message']
                ];
            }
        } catch (Exception $e) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => $e->getMessage()];
        }
        $municipios = $emprendedorModel->obtenerMunicipios();
        $municipiosConParroquias = $emprendedorModel->obtenerMunicipiosConParroquias();
        $data = $emprendedorModel->listarEmprendedores();
        render('emprendedor/index', [
            'data' => $data, 
            'municipios' => $municipios, 
            'municipiosConParroquias' => $municipiosConParroquias, 
            'menu' => $menu
        ]);
    }
}

function aprobar() {
    try {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $emprendedorModel = new EmprendedorModel();
            $id_emprendedor = $_POST['id_emprendedor'];
            $res = $emprendedorModel->aprobarEmprendedor($id_emprendedor);

            if ($res) {
                $_SESSION['flash_success'] = 'Emprendedor aprobado correctamente';
            } else {
                throw new Exception('Error al aprobar emprendedor');
            }
            header('Location: ' . APP_URL . '/emprendedor/index');
            exit;
        }
    } catch(Exception $e) {
        $_SESSION['flash_error'] = $e->getMessage();
        header('Location: ' . APP_URL . '/emprendedor/index');
        exit;
    }
}

function rechazar() {
    try {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $emprendedorModel = new EmprendedorModel();
            $id_emprendedor = $_POST['id_emprendedor'];
            $res = $emprendedorModel->rechazarEmprendedor($id_emprendedor);

            if ($res) {
                $_SESSION['flash_success'] = 'Solicitud de emprendedor rechazada correctamente';
            } else {
                throw new Exception('Error al rechazar emprendedor');
            }
            header('Location: ' . APP_URL . '/emprendedor/index');
            exit;
        }
    } catch(Exception $e) {
        $_SESSION['flash_error'] = $e->getMessage();
        header('Location: ' . APP_URL . '/emprendedor/index');
        exit;
    }
}

function obtenerMunicipios() {
    try {
        $emprendedorModel = new EmprendedorModel(); 
        $municipios = $emprendedorModel->obtenerMunicipios();
        echo json_encode(['success' => true, 'data' => $municipios]);
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function obtenerParroquias() {
    try {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id_municipio'])) {
            $emprendedorModel = new EmprendedorModel(); 
            $parroquias = $emprendedorModel->obtenerParroquiasPorMunicipio($_GET['id_municipio']);
            echo json_encode(['success' => true, 'data' => $parroquias]);
        }
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
