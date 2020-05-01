<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\controllers\RecipeDatabaseWrapper;




$app->get('/', function(Request $request, Response $response)
{
    $db = new RecipeDatabaseWrapper();
    $featuredResult = $db->getFeatured();
    $arr["featured"] = $featuredResult;

    if ($_SESSION["loggedIn"] === TRUE) {
        $arr["firstName"] = $_SESSION["firstName"];
        return $this->view->render($response, 'homepage-loggedin.html.twig', $arr);
    } else {
        session_destroy();
        return $this->view->render($response, 'homepage-loggedout.html.twig', $arr);
    }
})->setName('/' );

$app->get('/homepage', function(Request $request, Response $response)
{
    $db = new RecipeDatabaseWrapper();
    $featuredResult = $db->getFeatured();
    $arr["featured"] = $featuredResult;

    if ($_SESSION["loggedIn"] === TRUE) {
        $arr["firstName"] = $_SESSION["firstName"];
        return $this->view->render($response, 'homepage-loggedin.html.twig', $arr);

    } else {
        session_destroy();
        return $this->view->render($response, 'homepage-loggedout.html.twig', $arr);
    }
})->setName('/homepage' );