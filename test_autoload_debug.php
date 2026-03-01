<?php
$autoload = __DIR__ . '/vendor/autoload.php';

if (!file_exists($autoload)) {
    echo "NO ENCUENTRO AUTOLOAD: $autoload\n";
    exit;
}

echo "AUTOLOAD ENCONTRADO\n";

require $autoload;

echo "Clases cargadas:\n";
print_r(get_declared_classes());
