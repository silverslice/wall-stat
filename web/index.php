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
            'name' => $app['config']['group.name'],
            'defaultQuery' => $app['config']['default_search_text'],
        ]
    );
});

$app->get('/stat', function () use ($app, $wall) {
    $query     = $app['request']->get('query', '');
    $startDate = $app['request']->get('startDate', 0);
    $endDate   = $app['request']->get('endDate', 0);

    $stat = $wall->getStatistics($query, $startDate, $endDate);

    return $app['twig']->render('stat.html.twig', ['stat' => $stat]);
});

$app->get('/detail', function () use ($app, $wall) {
    $userId    = $app['request']->get('userId');
    $query     = $app['request']->get('query', '');
    $startDate = $app['request']->get('startDate', 0);
    $endDate   = $app['request']->get('endDate', 0);

    $posts = $wall->getUserPosts($userId, $query, $startDate, $endDate);

    return $app['twig']->render('detail.html.twig', ['posts' => $posts]);
});

$app->run();
