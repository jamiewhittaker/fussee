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

$app->get('/signin', function(Request $request, Response $response)
{
    if (isset($_SESSION['loggedIn'])) {
        $arr["firstName"] = $_SESSION["firstName"];
        $arr["loggedIn"] = true;
        return $this->view->render($response, 'signin.html.twig', $arr);
    } else {
        session_destroy();
        return $this->view->render($response, 'signin.html.twig');
    }
})->setName('/signin' );


$app->post('/signin', function(Request $request, Response $response){

    $db = new UserDatabaseWrapper();

    if (isset($_POST['registerSubmit'])) {
        $registerResult = $db->insertUser();

        if ($registerResult === true){
           return $this->view->render($response->withRedirect('homepage'), 'homepage.html.twig');
        } else {
            $arr["error"] = $registerResult;
            return $this->view->render($response, 'signin.html.twig', $arr);
        }
    }

    if (isset($_POST['loginSubmit'])) {
        $loginResult = $db->findUser();

        if ($loginResult === true){
            return $this->view->render($response->withRedirect('homepage'), 'homepage.html.twig');
        } else {
            $arr["error"] = $loginResult;
            return $this->view->render($response, 'signin.html.twig', $arr);
        }
    }



});