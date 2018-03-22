<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

// Main route
$app->match('/', function (Application $app, Request $request) {
    return $app['twig']->render('index.html', array(
        'title' => 'Bot exercise',
        'name' => 'Paloma',
        'message' => $request->get('message')
    ));
});

/*
---- example on how to return json -----
$app->get('/', function (Application $app, Request $request) {
    $data = array(
        'success' => true,s
        'message' => 'test'
    );
    return $app->json($data);
});

-- example on how to use session
$app['session']->set('data', $value);
$app['session']->get('data');
*/
