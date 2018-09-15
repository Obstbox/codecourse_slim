<?php

require 'vendor/autoload.php';

session_start();

$app = new Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);

$container = $app->getContainer();

// csrf routine
$container['csrf'] = function ($c) {
    return new \Slim\Csrf\Guard;
};

// Register component on container
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . '/resources/views', [
        'cache' => false
    ]);

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $container->get('request')->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container->get('router'), $basePath));

    return $view;
};

$app->add($container->get('csrf'));

$app->get('/', function($request, $response, $args) {
    $nameKey  = $this->csrf->getTokenNameKey();
    $valueKey = $this->csrf->getTokenValueKey();
    $name  = $request->getAttribute($nameKey);
    $value = $request->getAttribute($valueKey);

    return $this->view->render($response, 'home.twig', [

        // should be with 'csrf_' prefix
        'nameKey'  => $nameKey,
        'valueKey' => $valueKey,
        'name'  => $name,
        'value' => $value
    ]);
});

$app->post('/update', function() {
    return 'Подписка обновлена';
})->setName('update');

$app->run();

