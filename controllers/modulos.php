<?php
use App\Models\ModulosModel;
use App\Middleware;
function index() {
    $cedula = $_SESSION['user']['cedula'];
    $Middleware = new Middleware(); 
    //Validar que sea un SuperUsuario de lo contrario no se puede acceder a la vista
    $tipoUsuario = $Middleware->verificarTipoUsuario($cedula);
    if ('super_usuario' != $tipoUsuario[0]) {
        header('Location: ../home/index'); //Redirigir a la página de inicio
        exit;
    }
    $modulosModel = new ModulosModel();
    $data = $modulosModel->getModulos();
    
    render('modulos/index', ['data' => $data]);
}

?>