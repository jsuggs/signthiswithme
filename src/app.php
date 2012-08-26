<?php

require_once __DIR__ . '/bootstrap.php';

$app = new Silex\Application();

$debug = false;

$twigOptions = array();
if ($debug) {
    $twigOptions['cache'] = __DIR__ . '/../cache';
}

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/templates',
    'twig.options' => $twigOptions,
));

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html.twig');
});

$app->error(function (\Exception $e, $code) {
    var_dump($e);
    return new Response('We are sorry, but something went terribly wrong.', $code);
});

return $app;
