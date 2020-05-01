<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\controllers\RecipeDatabaseWrapper;

$app->get('/browse', function(Request $request, Response $response, $start)
{
    $start = $request->getQueryParam('start');
    $db = new RecipeDatabaseWrapper();

    if ($start === null){
        $browseResult = $db->getBrowse(0);
        $arr["previousPage"] = "browse?start=0";
    } else {
        $browseResult = $db->getBrowse($start);
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

    if ($_SESSION["loggedIn"] === TRUE) {
        $arr["firstName"] = $_SESSION["firstName"];
        return $this->view->render($response, 'browse-loggedin.html.twig', $arr);
    } else {
        session_destroy();
        return $this->view->render($response, 'browse-loggedout.html.twig', $arr);
    }
})->setName('/browse' );


