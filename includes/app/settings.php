<?php

define('DIRSEP', DIRECTORY_SEPARATOR);

define('db_host', 'localhost'); // set database host here
define('db_name', 'fussee'); // set database name here
define('db_username', 'root'); // set database username here
define('db_password', ''); // set database password here

$settings = [
    "settings" => [
        'displayErrorDetails' => true,
        'addContentLengthHeader' => false,
        'mode' => 'development',
        'debug' => true,
        'class_path' => __DIR__ . '/src/',
        'view' => [
            'template_path' => __DIR__ . '/templates/',
            'twig' => [
                'cache' => false,
                'auto_reload' => true
            ],
        ],
    ]
];

return $settings;
