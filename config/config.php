<?php
// Configuración de la aplicación
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

define('APP_URL', $protocol . $host . $path);
define('APP_PATH', dirname(__FILE__, 2));
define('DEFAULT_CONTROLLER', 'home');
define('DEFAULT_ACTION', 'index');

// Configuración de la base de datos HOST
/*define('BD_HOST', 'sql306.infinityfree.com');
define('BD_SEGURIDAD', 'if0_38376431_seguridad');
define('BD_PROJUMI', 'if0_38376431_projumi');
define('DB_DSN', 'mysql:host='.BD_HOST.'; dbname='.BD_SEGURIDAD.';charset=utf8');
define('DB_DSN_PROJUMI', 'mysql:host='.BD_HOST.'; dbname='.BD_PROJUMI.';charset=utf8');
define('DB_USER', 'if0_38376431');
define('DB_PASS', 'w5zCH5a8SZcid');
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
]);*/

// Configuración de la base de datos LOCALHOST
define('BD_SEGURIDAD', 'seguridad');
define('BD_PROJUMI', 'projumi');
define('DB_DSN', 'mysql:host=localhost;dbname=' . BD_SEGURIDAD . ';charset=utf8');
define('DB_DSN_PROJUMI', 'mysql:host=localhost;dbname=' . BD_PROJUMI . ';charset=utf8');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
]);
// Configuración de seguridad
//define('HASH_ALGO', 'sha256');
//define('HASH_KEY', 'hash777');
//define('HASH_PASS_KEY', '5254');