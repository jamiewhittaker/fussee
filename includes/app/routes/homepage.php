<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;



$app->get('/', function(Request $request, Response $response)
{
    if ($_SESSION["loggedIn"] === TRUE) {
        $arr["firstName"] = $_SESSION["firstName"];
        return $this->view->render($response, 'homepage-loggedin.html.twig', $arr);
    } else {
        session_destroy();
        return $this->view->render($response, 'homepage-loggedout.html.twig');
    }
})->setName('/' );

$app->get('/homepage', function(Request $request, Response $response)
{
    if ($_SESSION["loggedIn"] === TRUE) {
        $arr["firstName"] = $_SESSION["firstName"];
        return $this->view->render($response, 'homepage-loggedin.html.twig', $arr);

    } else {
        session_destroy();
        return $this->view->render($response, 'homepage-loggedout.html.twig');
    }
})->setName('/homepage' );