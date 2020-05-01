<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\controllers\RecipeDatabaseWrapper;

$app->get('/browse', function(Request $request, Response $response)
{
    $start = $request->getQueryParam('start');
    $db = new RecipeDatabaseWrapper();

    if ($start === null){
        $browseResult = $db->getBrowse(0);
        $arr["previousPage"] = "browse?start=0";
    } else {
        if (is_numeric($start)) {
            if ( ( ($start % 20) == 0) AND ($start >= 0) ){
                $count = $db->getBrowseCount();
                if ($start >= $count){
                    $arr['error'] = "Start offset greater than number of results";
                    if (isset($_SESSION['loggedIn'])) {
                        $arr["firstName"] = $_SESSION['firstName'];
                        return $this->view->render($response, 'browse-error-loggedin.html.twig', $arr);
                    } else {
                        return $this->view->render($response, 'browse-error-loggedout.html.twig', $arr);
                    }
                }
                $browseResult = $db->getBrowse($start);

            } else {
                $arr['error'] = "Start number is invalid";
                if (isset($_SESSION['loggedIn'])) {
                    $arr["firstName"] = $_SESSION['firstName'];
                    return $this->view->render($response, 'browse-error-loggedin.html.twig', $arr);
                } else {
                    return $this->view->render($response, 'browse-error-loggedout.html.twig', $arr);
                }
            }
        } else {
            $arr['error'] = "Start parameter is invalid";
            if (isset($_SESSION['loggedIn'])) {
                $arr["firstName"] = $_SESSION['firstName'];
                return $this->view->render($response, 'browse-error-loggedin.html.twig', $arr);
            } else {
                return $this->view->render($response, 'browse-error-loggedout.html.twig', $arr);
            }
        }
    }

    $count = $db->getBrowseCount();

    $arr["browseResult"] = $browseResult;
    $arr["start"] = $start;
    $arr["count"] = $count;

    if ($start <= 0) {
        $arr["previousPage"] = "browse?start=0";
    } else {
        $arr["previousPage"] = "browse?start=" . ((string) ($start - 20));
    }


    if (($start + 20) > $count) {
        $arr["nextPage"] = "browse?start=" . ((string) ($start));
    } else {
        $arr["nextPage"] = "browse?start=" . ((string) ($start + 20));
    }

    if (isset($_SESSION['loggedIn'])) {
        $arr["firstName"] = $_SESSION["firstName"];
        return $this->view->render($response, 'browse-loggedin.html.twig', $arr);
    } else {
        session_destroy();
        return $this->view->render($response, 'browse-loggedout.html.twig', $arr);
    }
})->setName('/browse' );


