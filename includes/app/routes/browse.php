<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\controllers\RecipeDatabaseWrapper;

$app->get('/browse', function(Request $request, Response $response)
{
    $start = $request->getQueryParam('start');
    $db = new RecipeDatabaseWrapper();

    if (isset($_SESSION['loggedIn'])) {
        $arr["loggedIn"] = true;
        $arr["firstName"] = $_SESSION['firstName'];
    }

    if ($start === null){
        $browseResult = $db->getBrowse(0);
        $arr["previousPage"] = "browse?start=0";
    } else {
        if (is_numeric($start)) {
            if ( ( ($start % 20) == 0) AND ($start >= 0) ){
                $count = $db->getBrowseCount();

                if ($start >= $count){
                    $arr['error'] = "Start offset greater than number of results";
                    return $this->view->render($response, 'browse.html.twig', $arr);
                }

                $browseResult = $db->getBrowse($start);

            } else {
                $arr['error'] = "Start number is invalid";
                return $this->view->render($response, 'browse.html.twig', $arr);
            }
        } else {
            $arr['error'] = "Start parameter is invalid";
            return $this->view->render($response, 'browse.html.twig', $arr);
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

    return $this->view->render($response, 'browse.html.twig', $arr);

})->setName('/browse' );


