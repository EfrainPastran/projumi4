<?php
use App\Models\tasaModel;
use App\Middleware;
function index() {
    if (!isset($_SESSION['user']['cedula'])) {
        header('Location: ../home/index');
        exit;
    }

    $Cambio = new tasaModel();

    $cedula = $_SESSION['user']['cedula'];
    $Middleware = new Middleware();
    $tipoUsuario = $Middleware->verificarTipoUsuario($cedula);

    if ($tipoUsuario[0] != 'emprendedor' && $tipoUsuario[0] != 'cliente') {
        $cambios = $Cambio->obtenerTodosLosCambios();
        $title = "Gestión de Tasa de Cambio";
    } else {
        header('Location: ../home/index');
        exit;
    }

    render('cambio/index', ['cambios' => $cambios, 'title' => $title]);
}

function mostrarCambios() {
    try {
        $Cambio = new tasaModel();
        $cambios = $Cambio->obtenerTodosLosCambios();

        echo json_encode(['success' => true, 'data' => $cambios]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    return;
}

function registrarTasaBcv() {
    try {
        $json = file_get_contents("https://ve.dolarapi.com/v1/dolares/oficial");
        $data = json_decode($json, true);
        
        if (!isset($data['promedio'], $data['fechaActualizacion'])) {
            throw new Exception("Formato de respuesta no válido");
        }

        $tasa = $data['promedio'];
        $fecha = date('Y-m-d H:i:s', strtotime($data['fechaActualizacion']));

        $CambioModel = new tasaModel();

        if ($CambioModel->yaExisteTasaEnFecha($fecha)) {
            throw new Exception("La tasa ya fue registrada para esta fecha");
        }

        $CambioModel->setDatos($tasa, $fecha);
        $CambioModel->registrarTasa();

        echo json_encode([
            'success' => true,
            'message' => 'Tasa registrada correctamente',
            'tasa' => $tasa,
            'fecha' => $fecha
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function consultarTasaBcv() {
    try {
        $CambioModel = new tasaModel();
        $tasa = $CambioModel->obtenerUltimaTasa();

        if (!$tasa) {
            throw new Exception("No hay registros disponibles");
        }

        echo json_encode(['success' => true, 'data' => $tasa]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
