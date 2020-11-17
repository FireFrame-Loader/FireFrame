<?php
$dir = __DIR__ . '/';

$dirs = array(
    $dir.'general/*.php',
    $dir.'auth/*.php',
    $dir.'loader/*.php',
    $dir.'module/*.php'
);

foreach($dirs as $_dir) {
    foreach (glob($_dir) as $php_file) {
        require $php_file;
    }
}
