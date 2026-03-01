<?php
// listo
namespace App;
class App {
    public function __construct() {
        $url = isset($_GET['url']) ? $_GET['url'] : null;
        $url = rtrim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = explode('/', $url);
        
        // Controlador por defecto
        $controllerName = empty($url[0]) ? DEFAULT_CONTROLLER : $url[0];
        $controllerFile = 'controllers/' . $controllerName . '.php';
        //echo "<pre>";
        //print_r($controllerName);
        //echo "</pre>";
        //die;
        if (file_exists($controllerFile)) {

            require_once $controllerFile;
            
            // Acción por defecto
            $action = empty($url[1]) ? DEFAULT_ACTION : $url[1];
            
            // Verificar si la función existe
            if (function_exists($action)) {
                // Llamar a la función del controlador
                call_user_func_array($action, array_slice($url, 2));
            } else {
                // Función no existe, mostrar error
                renderError('Acción no encontrada', 404);
            }
        } else {
            // Controlador no existe, mostrar error
            renderError('Controlador no encontrado', 404);
        }
    }
}