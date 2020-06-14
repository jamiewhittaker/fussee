<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\controllers\RecipeDatabaseWrapper;




$app->get('/', function(Request $request, Response $response)
{
    $db = new RecipeDatabaseWrapper();

    $featuredResult = $db->getFeatured();
    $arr["featured"] = $featuredResult;

    $randomResult = $db->getRandomRecipes();
    $arr["random"] = $randomResult;

    if (isset($_SESSION['loggedIn'])) {
        $arr["firstName"] = $_SESSION["firstName"];
        $arr["loggedIn"] = true;
        return $this->view->render($response, 'homepage-loggedout.html.twig', $arr);
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

    $randomResult = $db->getRandomRecipes();
    $arr["random"] = $randomResult;

    if (isset($_SESSION['loggedIn'])) {
        $arr["firstName"] = $_SESSION["firstName"];
        $arr["loggedIn"] = true;
        return $this->view->render($response, 'homepage-loggedout.html.twig', $arr);
    } else {
        session_destroy();
        return $this->view->render($response, 'homepage-loggedout.html.twig', $arr);
    }
})->setName('/homepage' );