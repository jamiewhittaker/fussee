<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\controllers\RecipeDatabaseWrapper;

$app->get('/favourites', function(Request $request, Response $response)
{
    if (isset($_SESSION['loggedIn'])) {
        $start = $request->getQueryParam('start');
        $db = new RecipeDatabaseWrapper();

        if ($start === null){
            $favouritesResult = $db->getFavourites(0);
            $arr["previousPage"] = "favourites?start=0";
        } else {
            if (is_numeric($start)) {
                if ( ( ($start % 20) == 0) AND ($start >= 0) ){
                    $count = $db->getFavouritesCount();
                    if ($start >= $count) {
                        $arr['error'] = "Start offset greater than number of results";
                        $arr["firstName"] = $_SESSION['firstName'];
                        return $this->view->render($response, 'favourites-error-loggedin.html.twig', $arr);
                    }
                    $favouritesResult = $db->getFavourites($start);
                } else {
                    $arr['error'] = "Start number is invalid";
                    $arr["firstName"] = $_SESSION['firstName'];
                    return $this->view->render($response, 'favourites-error-loggedin.html.twig', $arr);
                }
            } else {
                $arr['error'] = "Start parameter is invalid";
                $arr["firstName"] = $_SESSION['firstName'];
                return $this->view->render($response, 'favourites-error-loggedin.html.twig', $arr);
            }
        }

        $count = $db->getFavouritesCount();

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
        return $this->view->render($response, 'favourites-loggedin.html.twig', $arr);

    } else {
        session_destroy();
        return $this->view->render($response, 'favourites-loggedout.html.twig');
    }
})->setName('/favourites' );

$app->post('/addfavourite', function(Request $request, Response $response) {
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

    $db->addFavourite($userID, $recipeID);

});

$app->post('/removefavourite', function(Request $request, Response $response) {
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

});

