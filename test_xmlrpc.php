<?php
require __DIR__ . '/vendor/autoload.php';

foreach (get_declared_classes() as $c) {
    if (strpos($c, 'PhpXmlRpc') !== false) {
        echo $c . PHP_EOL;
    }
}
