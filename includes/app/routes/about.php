<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;



$app->get('/about', function(Request $request, Response $response)
{



    return $this->view->render($response, 'about.html.twig');
})->setName('/about' );