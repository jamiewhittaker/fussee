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

$app->get('/admin-cp', function(Request $request, Response $response)
{
    if ($_SESSION["loggedIn"] === TRUE){
        if ($_SESSION["isAdmin"] === TRUE) {
            $arr["firstName"] = $_SESSION["firstName"];
            return $this->view->render($response, 'admin-cp.html.twig', $arr);
        } else {
            $arr["firstName"] = $_SESSION["firstName"];
            return $this->view->render($response, 'admin-cp-nopermission.html.twig', $arr);
        }
    } else {
        session_destroy();
        return $this->view->render($response, 'admin-cp-loggedout.html.twig');
    }

})->setName('/admin-cp' );
