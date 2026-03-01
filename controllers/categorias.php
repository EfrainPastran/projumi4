<?php
use App\Models\CategoriasModel;
use App\Middleware;

function index()
{
    $model = new CategoriasModel();
    $data = $model->getCategorias();
    $cedula = $_SESSION['user']['cedula'];
    $middleware = new Middleware();
    $tipoUsuario = $middleware->verificarTipoUsuario($cedula);
    
    render('categorias/index', ['data' => $data]);
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

