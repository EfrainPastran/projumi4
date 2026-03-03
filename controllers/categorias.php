<?php
use App\Models\CategoriasModel;
use App\Middleware;
//CATEGORIA
function index()
{
    // 1. Verificación de sesión
    if (!isset($_SESSION['user']['cedula'])) {
        header('Location: ../home/index');
        exit;
    }

    $cedula = $_SESSION['user']['cedula'];
    $middleware = new Middleware();
    $tipoUsuario = $middleware->verificarTipoUsuario($cedula);

    // 2. Control de Permisos para el módulo Categorías
    // Verifica en tu BD si el ID del módulo categorías es el 19
    $idModuloCategorias = 19; 
    
    // Extraemos los permisos que el Middleware guardó en la sesión
    $listaPermisos = $_SESSION['user']['rol']['permisos'][$idModuloCategorias] ?? [];

    // Convertimos a mapa: ['registrar' => true, 'actualizar' => true, etc.]
    $permisosMap = array_fill_keys($listaPermisos, true);

    // 3. Carga de datos
    $model = new CategoriasModel();
    $data = $model->getCategorias();

    // 4. Renderizado con envío de permisos y datos de interfaz
    render('categorias/index', [
        'data'     => $data,
        'permisos' => $permisosMap,
        'title'    => 'Gestión de Categorías',
        'menu'     => ('administrador' == $tipoUsuario[0]) ? 'headeradmin' : 'navbar'
    ]);
}

function register()
{
    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['flash_error'] = 'Método no permitido.';
            header('Location: ' . APP_URL . '/categorias/index');
            exit;
        }

        $nombre      = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $estado      = trim($_POST['estado'] ?? '');

        $categoria = new CategoriasModel();

        // Validar y asignar los datos
        $validacion = $categoria->setData(null, $nombre, $descripcion, $estado);

        // Si hubo errores de validación, los mostramos
        if (!$validacion['success']) {
            $errores = implode('<br>', $validacion['errors']);
            $_SESSION['flash_error'] = "Error de validación:<br>$errores";
            header('Location: ' . APP_URL . '/categorias/index');
            exit;
        }

        // Si pasa las validaciones, intentar registrar
        $resultado = $categoria->registerCategoria();

        if ($resultado['success']) {
            $_SESSION['flash_success'] = '¡Categoría registrada correctamente!';
        } else {
            $_SESSION['flash_error'] = $resultado['message'] ?? 'Error al registrar la categoría.';
        }

        header('Location: ' . APP_URL . '/categorias/index');
        exit;

    } catch (Throwable $e) {
        error_log('Error en register categoria: ' . $e->getMessage());
        $_SESSION['flash_error'] = 'Error interno del servidor.';
        header('Location: ' . APP_URL . '/categorias/index');
        exit;
    }
}

function categorias_update()
{
    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['flash_error'] = 'Método no permitido.';
            header('Location: ' . APP_URL . '/categorias/index');
            exit;
        }

        $id          = $_POST['id'] ?? null;
        $nombre      = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $estado      = trim($_POST['estado'] ?? '');

        $categoria = new CategoriasModel();

        // Validar y asignar los datos
        $validacion = $categoria->setData($id, $nombre, $descripcion, $estado);

        if (!$validacion['success']) {
            $errores = implode('<br>', $validacion['errors']);
            $_SESSION['flash_error'] = "Error de validación:<br>$errores";
            header('Location: ' . APP_URL . '/categorias/index');
            exit;
        }

        // Si las validaciones pasan, ejecutar el update
        $resultado = $categoria->updateCategoria();

        if ($resultado['success']) {
            $_SESSION['flash_success'] = '¡Categoría actualizada correctamente!';
        } else {
            $_SESSION['flash_error'] = $resultado['message'] ?? 'Error al actualizar la categoría.';
        }

        header('Location: ' . APP_URL . '/categorias/index');
        exit;

    } catch (Throwable $e) {
        error_log('Error en categorias_update: ' . $e->getMessage());
        $_SESSION['flash_error'] = 'Error interno del servidor.';
        header('Location: ' . APP_URL . '/categorias/index');
        exit;
    }
}

function categorias_delete()
{
    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['flash_error'] = 'Método no permitido.';
            header('Location: ' . APP_URL . '/categorias/index');
            exit;
        }

        $id = $_POST['id'] ?? null;

        $categoria = new CategoriasModel();

        // Validar el ID antes de eliminar
        $validacion = $categoria->setId($id);

        if (!$validacion['success']) {
            $errores = implode('<br>', $validacion['errors']);
            $_SESSION['flash_error'] = "Error de validación:<br>$errores";
            header('Location: ' . APP_URL . '/categorias/index');
            exit;
        }

        // Proceder con el borrado
        $resultado = $categoria->delete();

        if ($resultado['success']) {
            $_SESSION['flash_success'] = '¡Categoría eliminada correctamente!';
        } else {
            $_SESSION['flash_error'] = $resultado['message'] ?? 'Error al eliminar la categoría.';
        }

        header('Location: ' . APP_URL . '/categorias/index');
        exit;

    } catch (Throwable $e) {
        error_log('Error en categorias_delete: ' . $e->getMessage());
        $_SESSION['flash_error'] = 'Error interno del servidor.';
        header('Location: ' . APP_URL . '/categorias/index');
        exit;
    }
}

