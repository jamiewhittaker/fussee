<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;



$app->get('/contact', function(Request $request, Response $response)
{



    return $this->view->render($response, 'contact.html.twig');
})->setName('/contact' );