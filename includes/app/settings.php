<?php

define('DIRSEP', DIRECTORY_SEPARATOR);
define('WSDL_URL', 'https://m2mconnect.ee.co.uk/orange-soap/services/MessageServiceByCountry?wsdl');
define('M2M_USERNAME', '19_JamieW'); // set M2M username here
define('M2M_PASSWORD', 'DMUcoursework1'); // set M2M password here
define('SMS_ID', 'circuit123'); // To distinguish our SMS format from others

define('db_host', 'localhost'); // set database host here
define('db_name', 'm2m_slim'); // set database name here
define('db_username', 'm2mslim'); // set database username here
define('db_password', 'DMUcoursework1'); // set database password here

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
