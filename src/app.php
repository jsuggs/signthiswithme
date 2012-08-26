<?php

require_once __DIR__ . '/bootstrap.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app['debug'] = false; //in_array($app['request']->server->get('REMOTE_ADDR'), array('127.0.0.1', '::1'));

$twigOptions = array();
if ($app['debug']) {
    $twigOptions['cache'] = __DIR__ . '/../cache';
}

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/templates',
    'twig.options' => $twigOptions,
));

$app->match('/create', function () use ($app) {
    return $app['twig']->render('create.html.twig');
});

$app->post('/save', function (Request $request) {
    // Get the data
    $imageData = $GLOBALS['HTTP_RAW_POST_DATA'];
    $filteredData = substr($imageData, strpos($imageData, ",") + 1);
    $unencodedData = base64_decode($filteredData);

    // Generate a unique ID (
    $id = new \MongoId();
    $filename = sprintf('%s/%s.png', __DIR__ . '/../uploads', $id);

    $fh = fopen($filename, 'w+b');
    fwrite($fh, $unencodedData);
    fclose($fh);

    $data = array(
        'success' => true,
        'id' => $id
    );

    return new Response(json_encode($data), 200, array(
        'Content-type' => 'application/json'
    ));
});

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html.twig');
});

$app->error(function (\Exception $e, $code) {
    if (in_array($app['request']->server->get('REMOTE_ADDR'), array('127.0.0.1', '::1'))) {
        var_dump($e);
    }
    return new Response('We are sorry, but something went terribly wrong.', $code);
});

return $app;
