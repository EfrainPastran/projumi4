<?php
// Configuración de la aplicación
function projumi_load_env_file(string $file): array {
    if (!is_file($file)) {
        return [];
    }

    $env = [];
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return [];
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || substr($line, 0, 1) === '#') {
            continue;
        }

        $position = strpos($line, '=');
        if ($position === false) {
            continue;
        }

        $key = trim(substr($line, 0, $position));
        $value = trim(substr($line, $position + 1));

        if ($value !== '') {
            $first = $value[0];
            $last = substr($value, -1);
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        $env[$key] = $value;
        putenv($key . '=' . $value);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }

    return $env;
}

function projumi_env_value(array $env, string $key, $default = null) {
    if (array_key_exists($key, $env)) {
        return $env[$key];
    }

    $value = getenv($key);
    if ($value !== false) {
        return $value;
    }

    return $default;
}

function projumi_resolve_path(string $path): string {
    if ($path === '') {
        return $path;
    }

    $normalized = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
    if (preg_match('/^[A-Za-z]:\\\\/', $normalized) || substr($normalized, 0, 1) === DIRECTORY_SEPARATOR) {
        return $normalized;
    }

    return APP_PATH . DIRECTORY_SEPARATOR . ltrim($normalized, DIRECTORY_SEPARATOR);
}

define('APP_PATH', dirname(__FILE__, 2));
$env = projumi_load_env_file(APP_PATH . '/.env');

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$path = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
$defaultAppUrl = $protocol . $host . $path;

$configuredAppUrl = projumi_env_value($env, 'APP_URL', $defaultAppUrl);
define('APP_URL', rtrim($configuredAppUrl, '/'));
define('DEFAULT_CONTROLLER', 'home');
define('DEFAULT_ACTION', 'index');

// Rate limiting básico por IP para proteger login y endpoints sensibles.
define('RATE_LIMIT_STORAGE_FILE', projumi_resolve_path(projumi_env_value($env, 'RATE_LIMIT_STORAGE_FILE', 'storage/rate_limits.json')));
define('RATE_LIMIT_LOGIN_MAX', (int) projumi_env_value($env, 'RATE_LIMIT_LOGIN_MAX', 5));
define('RATE_LIMIT_LOGIN_WINDOW', (int) projumi_env_value($env, 'RATE_LIMIT_LOGIN_WINDOW', 900));
define('RATE_LIMIT_API_MAX', (int) projumi_env_value($env, 'RATE_LIMIT_API_MAX', 60));
define('RATE_LIMIT_API_WINDOW', (int) projumi_env_value($env, 'RATE_LIMIT_API_WINDOW', 60));

// Configuracion de correo SMTP y recuperacion de contrasena.
define('MAIL_HOST', projumi_env_value($env, 'MAIL_HOST', 'smtp.gmail.com'));
define('MAIL_PORT', (int) projumi_env_value($env, 'MAIL_PORT', 587));
define('MAIL_USERNAME', projumi_env_value($env, 'MAIL_USERNAME', ''));
define('MAIL_PASSWORD', projumi_env_value($env, 'MAIL_PASSWORD', ''));
define('MAIL_ENCRYPTION', strtolower((string) projumi_env_value($env, 'MAIL_ENCRYPTION', 'tls')));
define('MAIL_FROM_ADDRESS', projumi_env_value($env, 'MAIL_FROM_ADDRESS', ''));
define('MAIL_FROM_NAME', projumi_env_value($env, 'MAIL_FROM_NAME', 'PROJUMI'));
define('MAIL_TIMEOUT', (int) projumi_env_value($env, 'MAIL_TIMEOUT', 15));
define('PASSWORD_RESET_CODE_TTL', (int) projumi_env_value($env, 'PASSWORD_RESET_CODE_TTL', 600));
define('PASSWORD_RESET_VERIFIED_TTL', (int) projumi_env_value($env, 'PASSWORD_RESET_VERIFIED_TTL', 900));
define('PASSWORD_RESET_RESEND_COOLDOWN', (int) projumi_env_value($env, 'PASSWORD_RESET_RESEND_COOLDOWN', 60));
define('PASSWORD_RESET_MAX_ATTEMPTS', (int) projumi_env_value($env, 'PASSWORD_RESET_MAX_ATTEMPTS', 5));

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
$dbHost = projumi_env_value($env, 'DB_HOST', 'localhost');
define('BD_SEGURIDAD', projumi_env_value($env, 'BD_SEGURIDAD', 'seguridad'));
define('BD_PROJUMI', projumi_env_value($env, 'BD_PROJUMI', 'projumi'));
define('DB_DSN', 'mysql:host=' . $dbHost . ';dbname=' . BD_SEGURIDAD . ';charset=utf8');
define('DB_DSN_PROJUMI', 'mysql:host=' . $dbHost . ';dbname=' . BD_PROJUMI . ';charset=utf8');
define('DB_USER', projumi_env_value($env, 'DB_USER', 'root'));
define('DB_PASS', projumi_env_value($env, 'DB_PASS', ''));
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
]);

// Configuración de JWT/RSA para autenticación de la app móvil.
// Se usan claves RSA para firmar y validar tokens JWT con algoritmo RS256.
define('JWT_KEYS_DIR', APP_PATH . '/config/keys');
define('JWT_PRIVATE_KEY_FILE', projumi_resolve_path(projumi_env_value($env, 'JWT_PRIVATE_KEY_FILE', 'config/keys/jwt_private.pem')));
define('JWT_PUBLIC_KEY_FILE', projumi_resolve_path(projumi_env_value($env, 'JWT_PUBLIC_KEY_FILE', 'config/keys/jwt_public.pem')));
define('JWT_ALGORITHM', projumi_env_value($env, 'JWT_ALGORITHM', 'RS256'));
define('JWT_EXPIRATION_SECONDS', (int) projumi_env_value($env, 'JWT_EXPIRATION_SECONDS', 3600));
// Configuración de seguridad
//define('HASH_ALGO', 'sha256');
//define('HASH_KEY', 'hash777');
//define('HASH_PASS_KEY', '5254');
