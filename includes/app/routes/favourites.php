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

        if (empty($favouritesResult)){
            return $this->view->render($response, 'favourites-empty.html.twig', $arr);
        }

        return $this->view->render($response, 'favourites-loggedin.html.twig', $arr);
    } else {
        session_destroy();
        return $this->view->render($response, 'favourites-loggedout.html.twig');
    }
})->setName('/favourites' );

$app->post('/addfavourite', function(Request $request, Response $response) {
    $userID = $_SESSION['userID'];
    $recipeID = $_POST['recipeID'];

    $db = new RecipeDatabaseWrapper();

    $db->addFavourite($userID, $recipeID);

});
$app->post('/removefavourite', function(Request $request, Response $response) {
    $userID = $_SESSION['userID'];
    $recipeID = $_POST['recipeID'];

    $db = new RecipeDatabaseWrapper();

    $db->removeFavourite($userID, $recipeID);

});

