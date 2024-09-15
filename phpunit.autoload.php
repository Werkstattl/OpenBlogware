<?php
declare(strict_types=1);

include_once __DIR__ . '/vendor/autoload.php';

$classLoader = new Composer\Autoload\ClassLoader();
$classLoader->addPsr4('Werkl\\OpenBlogware\\', __DIR__ . '/src', true);
$classLoader->addPsr4('OpenBlogware\\Tests\\', __DIR__ . '/tests/PHPUnit', true);
$classLoader->register();
