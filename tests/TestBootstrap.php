<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$classLoader = new Composer\Autoload\ClassLoader();

$classLoader->addPsr4('Werkl\\OpenBlogware\\Tests\\Unit\\', __DIR__ . '/tests/unit', true);
$classLoader->register();
