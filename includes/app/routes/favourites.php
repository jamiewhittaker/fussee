<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\controllers\RecipeDatabaseWrapper;

$app->get('/favourites', function(Request $request, Response $response)
{
    if (isset($_SESSION['loggedIn'])) {
        $start = $request->getQueryParam('start');
        $db = new RecipeDatabaseWrapper();

        $count = $db->getFavouritesCount();
        $arr["loggedIn"] = true;


        if ($count <= 0) {
            $arr["firstName"] = $_SESSION['firstName'];
            $arr["error"] = "The favourites list is empty.";
            return $this->view->render($response, 'favourites.html.twig', $arr);
        }

        if ($start === null){
            $favouritesResult = $db->getFavourites(0);
            $arr["previousPage"] = "favourites?start=0";
        } else {
            if (is_numeric($start)) {
                if ( ( ($start % 20) == 0) AND ($start >= 0) ){


                    if ($start >= $count) {
                        $arr['error'] = "Start offset greater than number of results";
                        $arr["firstName"] = $_SESSION['firstName'];
                        return $this->view->render($response, 'favourites.html.twig', $arr);
                    }
                    $favouritesResult = $db->getFavourites($start);
                } else {
                    $arr['error'] = "Start number is invalid";
                    $arr["firstName"] = $_SESSION['firstName'];
                    return $this->view->render($response, 'favourites.html.twig', $arr);
                }
            } else {
                $arr['error'] = "Start parameter is invalid";
                $arr["firstName"] = $_SESSION['firstName'];
                return $this->view->render($response, 'favourites.html.twig', $arr);
            }
        }

        $count = $db->getFavouritesCount();

        if ($count <= 0) {
            $arr["firstName"] = $_SESSION['firstName'];
            return $this->view->render($response, 'favourites.html.twig', $arr);
        }

        $arr["favouritesResult"] = $favouritesResult;
        $arr["start"] = $start;
        $arr["count"] = $count;

        if ($start <= 0) {
            $arr["previousPage"] = "favourites?start=0";
        } else {
            $arr["previousPage"] = "favourites?start=" . ((string) ($start - 20));
        }


        if (($start + 20) > $count) {
            $arr["nextPage"] = "favourites?start=" . ((string) ($start));
        } else {
            $arr["nextPage"] = "favourites?start=" . ((string) ($start + 20));
        }

        $arr["firstName"] = $_SESSION["firstName"];
        return $this->view->render($response, 'favourites.html.twig', $arr);

    } else {
        session_destroy();
        return $this->view->render($response, 'favourites.html.twig');
    }
})->setName('/favourites' );

$app->post('/addfavourite', function(Request $request, Response $response) {

    if ($_SESSION["loggedIn"] === TRUE) {
        $userID = $_SESSION['userID'];
        $recipeID = $_POST['recipeID'];

        if ((isset($_SESSION['userID'])) === FALSE){
            return;
        }

        if((is_numeric($userID)) === FALSE) {
            return;
        }

        if((is_numeric($recipeID)) === FALSE) {
            return;
        }

        $db = new RecipeDatabaseWrapper();

        if ($db->recipeExists($recipeID)) {
            $db->addFavourite($userID, $recipeID);
        }

    }


});

$app->post('/removefavourite', function(Request $request, Response $response) {
    if ($_SESSION["loggedIn"] === TRUE){

        $userID = $_SESSION['userID'];
        $recipeID = $_POST['recipeID'];

        if ((isset($_SESSION['userID'])) === FALSE){
            return;
        }

        if((is_numeric($userID)) === FALSE) {
            return;
        }

        if((is_numeric($recipeID)) === FALSE) {
            return;
        }

        $db = new RecipeDatabaseWrapper();

        $db->removeFavourite($userID, $recipeID);

    }


});

