<?php

use Silex\Application;
use App\Models\WallStat;

require_once __DIR__ . '/../vendor/autoload.php';

/** @var Application $app */
$app = require __DIR__ . '/../bootstrap.php';

$wall = new WallStat($app['db'], $app['config']['group.domain']);

$app->get('/', function () use ($app, $wall) {
    $lastUpdate = $wall->getLastUpdate();

    return $app['twig']->render(
        'index.html.twig',
        [
            'lastUpdate' => $lastUpdate,
            'name' => $app['config']['group.name']
        ]
    );
});

$app->run();
