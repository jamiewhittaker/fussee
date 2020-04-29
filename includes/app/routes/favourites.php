<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\controllers\RecipeDatabaseWrapper;

$app->get('/favourites', function(Request $request, Response $response)
{
    if ($_SESSION["loggedIn"] === TRUE) {

        $db = new RecipeDatabaseWrapper();
        $favouritesResult = $db->getFavourites();

        $arr["favourites"] = $favouritesResult;
        $arr["firstName"] = $_SESSION["firstName"];
        return $this->view->render($response, 'favourites-loggedin.html.twig', $arr);
    } else {
        session_destroy();
        return $this->view->render($response, 'favourites-loggedout.html.twig');
    }
})->setName('/browse' );
