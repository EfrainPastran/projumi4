<?php
use App\Models\bitacoraModel;
use App\Middleware;
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
    //Vista para el usuario
    else {
        $title = "Gestión de Bitacora";
    } 
    render('bitacora/index', ['title' => $title]);
}

function mostrarBitacora() {
    $Bitacora = new bitacoraModel();
    $registros = $Bitacora->getBitacora();
    header('Content-Type: application/json'); 
    echo json_encode($registros);
    return;
}