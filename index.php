<?php
// listo
session_start();
require_once 'config/config.php';
require_once 'libs/database.php';
//require_once 'libs/UserSession.php';
require_once 'libs/functions.php';

// Carga del helper JWT para firma/verificación RSA en las API móviles.
require_once 'libs/jwt.php';
//require_once 'libs/app.php';
require_once 'libs/validator.php';

//include_once 'models/productosModel.php';

// Autoload para clases de Composer
require_once 'vendor/autoload.php';

use App\App;

$app = new App(); //