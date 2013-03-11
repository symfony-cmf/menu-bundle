<?php

$file = __DIR__.'/../vendor/autoload.php';
if (!file_exists($file)) {
    throw new RuntimeException('Run "composer update --dev" to run test suite.');
}

require_once $file;
