<?php
$configDir = __DIR__ . "/keys";
$privateFile = $configDir . "/jwt_private.pem";
$publicFile = $configDir . "/jwt_public.pem";
if (!file_exists($privateFile) || !file_exists($publicFile)) {
    $keypair = openssl_pkey_new(["private_key_bits" => 2048, "private_key_type" => OPENSSL_KEYTYPE_RSA]);
    if ($keypair === false) {
        while ($err = openssl_error_string()) {
            echo "OpenSSL error: $err\n";
        }
        exit(1);
    }
    if (!openssl_pkey_export($keypair, $privateKey)) {
        while ($err = openssl_error_string()) {
            echo "OpenSSL export error: $err\n";
        }
        exit(1);
    }
    $details = openssl_pkey_get_details($keypair);
    if ($details === false) {
        while ($err = openssl_error_string()) {
            echo "OpenSSL details error: $err\n";
        }
        exit(1);
    }
    file_put_contents($privateFile, $privateKey);
    file_put_contents($publicFile, $details["key"]);
    echo "JWT RSA keys generated.\n";
} else {
    echo "JWT RSA keys already exist.\n";
}
