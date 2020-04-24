<?php
/**
 * Created by PhpStorm.
 * User: jamiewhittaker
 * Date: 2020-01-22
 * Time: 13:36
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\controllers\UserDatabaseWrapper;

$app->get('/user-cp', function(Request $request, Response $response)
{
    if ($_SESSION["loggedIn"] === TRUE) {
        $arr["firstName"] = $_SESSION["firstName"];
        return $this->view->render($response, 'user-cp-loggedin.html.twig', $arr);
    } else {
        session_destroy();
        return $this->view->render($response, 'user-cp-loggedout.html.twig');
    }
})->setName('/user-cp' );