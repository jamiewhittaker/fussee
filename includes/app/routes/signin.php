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
    if ($_SESSION["loggedIn"] === TRUE) {
        $arr["firstName"] = $_SESSION["firstName"];
        return $this->view->render($response, 'signin-loggedin.html.twig', $arr);
    } else {
        session_destroy();
        return $this->view->render($response, 'signin-loggedout.html.twig');
    }
})->setName('/signin' );


$app->post('/signin', function(Request $request, Response $response){

    $db = new UserDatabaseWrapper();

    if (isset($_POST['registerSubmit'])) {
       if ($db->insertUser() === true){
           return $this->view->render($response->withRedirect('about'), 'about-loggedin.twig');
    }}

    if (isset($_POST['loginSubmit'])) {
        if ($db->findUser() === true){
            return $this->view->render($response->withRedirect('about'), 'about-loggedin.twig');
        }
    }



});