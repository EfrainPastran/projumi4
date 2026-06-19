<?php

/*
 * Helper JWT/RSA para la app móvil.
 * - Genera claves RSA en config/keys si no existen
 * - Firma tokens JWT con RS256
 * - Verifica tokens JWT con la clave pública
 * - Soporta lectura de JSON en el body de la petición
 * - Extrae token Bearer del header Authorization
 */

if (!function_exists('jwt_ensure_keys')) {
    function jwt_ensure_keys(): bool {
        if (!file_exists(JWT_KEYS_DIR)) {
            if (!mkdir(JWT_KEYS_DIR, 0755, true) && !is_dir(JWT_KEYS_DIR)) {
                return false;
            }
        }

        if (!file_exists(JWT_PRIVATE_KEY_FILE) || !file_exists(JWT_PUBLIC_KEY_FILE)) {
            // Genera las claves RSA si no existen.
            $config = [
                'private_key_bits' => 2048,
                'private_key_type' => OPENSSL_KEYTYPE_RSA,
            ];

            $keypair = openssl_pkey_new($config);
            if ($keypair === false) {
                return false;
            }

            if (!openssl_pkey_export($keypair, $privateKey)) {
                return false;
            }

            $details = openssl_pkey_get_details($keypair);
            if ($details === false || empty($details['key'])) {
                return false;
            }

            file_put_contents(JWT_PRIVATE_KEY_FILE, $privateKey);
            file_put_contents(JWT_PUBLIC_KEY_FILE, $details['key']);
        }

        return true;
    }
}

if (!function_exists('jwt_load_private_key')) {
    function jwt_load_private_key() {
        // Carga la clave privada RSA usada para firmar tokens.
        if (!jwt_ensure_keys()) {
            return false;
        }

        $key = file_get_contents(JWT_PRIVATE_KEY_FILE);
        if ($key === false) {
            return false;
        }

        return openssl_pkey_get_private($key);
    }
}

if (!function_exists('jwt_load_public_key')) {
    function jwt_load_public_key() {
        // Carga la clave pública RSA usada para validar tokens.
        if (!jwt_ensure_keys()) {
            return false;
        }

        $key = file_get_contents(JWT_PUBLIC_KEY_FILE);
        if ($key === false) {
            return false;
        }

        return openssl_pkey_get_public($key);
    }
}

if (!function_exists('jwt_base64url_encode')) {
    function jwt_base64url_encode(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}

if (!function_exists('jwt_base64url_decode')) {
    function jwt_base64url_decode(string $data): string {
        $pad = 4 - (strlen($data) % 4);
        if ($pad < 4) {
            $data .= str_repeat('=', $pad);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }
}

if (!function_exists('jwt_encode_rs256')) {
    function jwt_encode_rs256(array $payload) {
        // Genera un JWT firmado con RS256.
        $privateKey = jwt_load_private_key();
        if ($privateKey === false) {
            return false;
        }

        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $segments = [
            jwt_base64url_encode(json_encode($header, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)),
            jwt_base64url_encode(json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE))
        ];

        $signingInput = implode('.', $segments);
        $signature = '';
        $success = openssl_sign($signingInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        openssl_pkey_free($privateKey);

        if (!$success) {
            return false;
        }

        $segments[] = jwt_base64url_encode($signature);
        return implode('.', $segments);
    }
}

if (!function_exists('jwt_decode_rs256')) {
    function jwt_decode_rs256(string $jwt) {
        // Valida un JWT RS256 y devuelve el payload si es correcto.
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return false;
        }

        [$encodedHeader, $encodedPayload, $encodedSignature] = $parts;
        $header = json_decode(jwt_base64url_decode($encodedHeader), true);
        $payload = json_decode(jwt_base64url_decode($encodedPayload), true);
        $signature = jwt_base64url_decode($encodedSignature);

        if (!is_array($header) || !is_array($payload) || $signature === false) {
            return false;
        }

        if (($header['alg'] ?? '') !== 'RS256') {
            return false;
        }

        $publicKey = jwt_load_public_key();
        if ($publicKey === false) {
            return false;
        }

        $valid = openssl_verify($encodedHeader . '.' . $encodedPayload, $signature, $publicKey, OPENSSL_ALGO_SHA256);
        openssl_pkey_free($publicKey);

        if ($valid !== 1) {
            return false;
        }

        $now = time();
        if (isset($payload['exp']) && $now >= $payload['exp']) {
            return false;
        }

        return $payload;
    }
}

if (!function_exists('get_json_request_body')) {
    function get_json_request_body(): array {
        // Lee el cuerpo JSON de la petición, o usa $_POST si no hay JSON.
        $raw = file_get_contents('php://input');
        if (empty($raw)) {
            return $_POST;
        }

        $data = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
            return $data;
        }

        return $_POST;
    }
}

if (!function_exists('get_bearer_token')) {
    function get_bearer_token(): ?string {
        // Extrae el token Bearer del header Authorization.
        $headers = [];
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
        }

        if (empty($headers) && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers['Authorization'] = $_SERVER['HTTP_AUTHORIZATION'];
        }
        if (empty($headers) && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }

        if (!empty($headers['Authorization'])) {
            if (preg_match('/Bearer\s+(.*)$/i', trim($headers['Authorization']), $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
}

if (!function_exists('projumi_rate_limit_client_ip')) {
    function projumi_rate_limit_client_ip(): string {
        $candidates = [
            $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null,
            $_SERVER['HTTP_CLIENT_IP'] ?? null,
            $_SERVER['REMOTE_ADDR'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            if (!$candidate) {
                continue;
            }

            $parts = explode(',', $candidate);
            $ip = trim($parts[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }

        return 'unknown';
    }
}

if (!function_exists('projumi_rate_limit_storage_path')) {
    function projumi_rate_limit_storage_path(): string {
        return defined('RATE_LIMIT_STORAGE_FILE')
            ? RATE_LIMIT_STORAGE_FILE
            : APP_PATH . '/storage/rate_limits.json';
    }
}

if (!function_exists('projumi_rate_limit_check')) {
    function projumi_rate_limit_check(string $scope, int $maxAttempts, int $windowSeconds, ?string $identifier = null): array {
        if ($maxAttempts <= 0 || $windowSeconds <= 0) {
            return ['allowed' => true, 'remaining' => PHP_INT_MAX, 'retry_after' => 0];
        }

        $identifier = $identifier ?: projumi_rate_limit_client_ip();
        $key = hash('sha256', $scope . '|' . $identifier);
        $now = time();
        $path = projumi_rate_limit_storage_path();
        $dir = dirname($path);

        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            return ['allowed' => true, 'remaining' => $maxAttempts, 'retry_after' => 0];
        }

        $data = [];
        if (is_file($path)) {
            $raw = file_get_contents($path);
            if ($raw !== false) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $data = $decoded;
                }
            }
        }

        $bucket = $data[$key] ?? [];
        $bucket = array_values(array_filter($bucket, static function ($timestamp) use ($now, $windowSeconds) {
            return is_numeric($timestamp) && ((int) $timestamp) > ($now - $windowSeconds);
        }));

        if (count($bucket) >= $maxAttempts) {
            $oldest = min($bucket);
            $retryAfter = max(1, ($oldest + $windowSeconds) - $now);

            $data[$key] = $bucket;
            file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);

            return [
                'allowed' => false,
                'remaining' => 0,
                'retry_after' => $retryAfter,
            ];
        }

        $bucket[] = $now;
        $data[$key] = $bucket;
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);

        return [
            'allowed' => true,
            'remaining' => max(0, $maxAttempts - count($bucket)),
            'retry_after' => 0,
        ];
    }
}

if (!function_exists('projumi_rate_limit_response')) {
    function projumi_rate_limit_response(int $retryAfter, string $message = 'Demasiadas solicitudes. Intenta de nuevo mas tarde.'): void {
        http_response_code(429);
        header('Content-Type: application/json; charset=utf-8');
        header('Retry-After: ' . max(1, $retryAfter));
        echo json_encode([
            'success' => false,
            'message' => $message,
            'retry_after' => max(1, $retryAfter),
        ]);
        exit;
    }
}
