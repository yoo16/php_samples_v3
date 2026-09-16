<?php

require_once dirname(__DIR__) . '/bootstrap.php';

$autoloadPaths = [
    __DIR__ . '/vendor/autoload.php',
    dirname(__DIR__) . '/vendor/autoload.php',
];

foreach ($autoloadPaths as $autoloadPath) {
    if (is_file($autoloadPath)) {
        require_once $autoloadPath;
        break;
    }
}

define('PDF_SAMPLE_DIR', __DIR__);
