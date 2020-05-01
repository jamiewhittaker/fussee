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
    if (isset($_SESSION['loggedIn'])) {
        $arr["firstName"] = $_SESSION["firstName"];
        return $this->view->render($response, 'user-cp-loggedin.html.twig', $arr);
    } else {
        session_destroy();
        return $this->view->render($response, 'user-cp-loggedout.html.twig');
    }
})->setName('/user-cp' );


$app->post('/user-cp', function(Request $request, Response $response){

    $db = new UserDatabaseWrapper();

    if (isset($_POST['changeEmailSubmit'])) {
        $changeEmailResult = $db->changeEmail();

        if ($changeEmailResult === true){
            $arr["success"] = "You have successfully changed your email address";
            $arr["firstName"] = $_SESSION['firstName'];
            return $this->view->render($response, 'user-cp-success.html.twig', $arr);
        } else {
            $arr["error"] = $changeEmailResult;
            $arr["firstName"] = $_SESSION['firstName'];
            return $this->view->render($response, 'user-cp-error.html.twig', $arr);
        }
    }

    if (isset($_POST['changePasswordSubmit'])) {
        $changePasswordResult = $db->changePassword();

        if ($changePasswordResult === true){
            $arr["success"] = "You have successfully changed your password";
            $arr["firstName"] = $_SESSION['firstName'];
            return $this->view->render($response, 'user-cp-success.html.twig', $arr);
        } else {
            $arr["error"] = $changePasswordResult;
            $arr["firstName"] = $_SESSION['firstName'];
            return $this->view->render($response, 'user-cp-error.html.twig', $arr);
        }
    }

    if (isset($_POST['changeFirstNameSubmit'])) {
        $changeFirstNameResult = $db->changeFirstName();

        if ($changeFirstNameResult === true){
            $arr["success"] = "You have successfully changed your first name that is displayed on the website.";
            $arr["firstName"] = $_SESSION['firstName'];
            return $this->view->render($response, 'user-cp-success.html.twig', $arr);
        } else {
            $arr["error"] = $changeFirstNameResult;
            $arr["firstName"] = $_SESSION['firstName'];
            return $this->view->render($response, 'user-cp-error.html.twig', $arr);
        }
    }



});