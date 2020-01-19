<?php

// Register component on container
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(
        $container['settings']['view']['template_path'],
        $container['settings']['view']['twig']
    );

    // Instantiate and add Slim\Twig specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));
    return $view;
};


$container['statusController'] = function ($container) {
    return new \App\Controllers\StatusController();
};

$container['databaseWrapper'] = function ($container) {
    $databaseWrapper = new \App\Controllers\DatabaseWrapper();
    return $databaseWrapper;
};