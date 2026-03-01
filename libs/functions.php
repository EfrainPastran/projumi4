<?php
if (!isset($_SESSION['user'])) {
    $_SESSION['user']['cedula']= NULL;
}
// Función para generar URLs
function url($path = '') {
    return APP_URL . ltrim($path, '/');
}
// Función para redireccionar
function redirect($location) {
    header('Location: ' . url($location));
    exit();
}

function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// Función para renderizar vistas
function render($view, $data = []) {
    // Hacer disponible la función url() en todas las vistas
    if (!function_exists('url')) {
        function url($path = '') {
            return APP_URL . ltrim($path, '/');
        }
    }
    
    extract($data);
    
    // Incluir header si existe
    if (file_exists('views/layout/header.php')) {
        require_once 'views/layout/header.php';
    }
    
    // Incluir la vista solicitada
    require_once 'views/' . $view . '.php';
    
    // Incluir footer si existe
    if (file_exists('views/layout/footer.php')) {
        require_once 'views/layout/footer.php';
    }
}


// Función para renderizar errores
function renderError($message, $code = 0) {
    $data = ['error' => $message, 'code'  => $code];
    render('error', $data);
}
