<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;



$app->get('/about', function(Request $request, Response $response)
{
    if (isset($_SESSION['loggedIn'])) {
        $arr["firstName"] = $_SESSION["firstName"];
        $arr["loggedIn"] = true;
    } else {
        session_destroy();
    }

    return $this->view->render($response, 'about.html.twig', $arr);

})->setName('/about' );