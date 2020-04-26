<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\controllers\RecipeDatabaseWrapper;

$app->get('/browse', function(Request $request, Response $response)
{
    if ($_SESSION["loggedIn"] === TRUE) {

        $db = new RecipeDatabaseWrapper();
        $featuredResult = $db->getFeatured();

        $arr["featured"] = $featuredResult;
        $arr["firstName"] = $_SESSION["firstName"];
        return $this->view->render($response, 'browse-loggedin.html.twig', $arr);
    } else {
        session_destroy();
        return $this->view->render($response, 'browse-loggedout.html.twig');
    }
})->setName('/browse' );

$app->post('/browse', function(Request $request, Response $response) {
    $tags = $_POST['ingredients'];
    $tagsArray = explode (",", $tags);

    $db = new RecipeDatabaseWrapper();


    if (count($tagsArray) === 1){
        $searchResult = $db->searchRecipesOneTag($tagsArray[0]);
    }


    $arr["searched"] = $tags;
    $arr["search"] = $searchResult;
    $arr["firstName"] = $_SESSION["firstName"];

    return $this->view->render($response, 'search.html.twig', $arr);
});