<?php

$local = file_exists(__DIR__ . '/local');

$app = new Silex\Application();

$app['debug'] = $local;

$app['config'] = include __DIR__ . '/app/config/main.php';

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/app/templates',
    'twig.options' => [
        'cache' => __DIR__ . '/cache/twig'
    ],
]);

$app['db'] = $app->share(function () {
    $dbOptions = include __DIR__ . '/app/config/db.php';
    return new Silverslice\EasyDb\Database($dbOptions);
});

return $app;
