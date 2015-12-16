<?php

$local = file_exists(__DIR__ . '/local');

$app = new Silex\Application();

$app['debug'] = $local;

$app['config'] = include __DIR__ . '/app/config/main.php';

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/app/templates',
    'twig.options' => [
        'cache' => __DIR__ . '/cache/twig',
        'auto_reload' => true,
    ],
]);
$app['twig']->addExtension(new Twig_Extensions_Extension_Text());

$app['db'] = $app->share(function () {
    $configFile = __DIR__ . '/app/config/db.php';
    $configLocalFile = __DIR__ . '/app/config/db-local.php';
    if (file_exists($configLocalFile)) {
        $configFile = $configLocalFile;
    }
    $dbOptions = include $configFile;

    return new Silverslice\EasyDb\Database($dbOptions);
});

return $app;
