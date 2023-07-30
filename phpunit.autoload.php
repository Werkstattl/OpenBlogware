<?php declare(strict_types=1);

include_once __DIR__ . '/vendor/autoload.php';

$classLoader = new \Composer\Autoload\ClassLoader();
$classLoader->addPsr4('Sas\\BlogModule\\', __DIR__ . '/src', true);
$classLoader->addPsr4('BlogModule\\Tests\\', __DIR__ . '/tests/PHPUnit', true);
$classLoader->register();
