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

$app->get('/logout', function(Request $request, Response $response)
{
    session_destroy();
    return $this->view->render($response->withRedirect('homepage'), 'homepage-loggedout.html.twig');
})->setName('/logout' );