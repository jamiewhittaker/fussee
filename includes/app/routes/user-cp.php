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
    $arr = [];
    if (isset($_SESSION['loggedIn'])) {
        $arr["firstName"] = $_SESSION["firstName"];
        $arr["loggedIn"] = true;
        if (isset($_SESSION["isAdmin"])){
            if ($_SESSION["isAdmin"] === true){
                $arr["isAdmin"] = true;
            }
        }
    } else {
        session_destroy();
    }
    return $this->view->render($response, 'user-cp.html.twig', $arr);
})->setName('/user-cp' );


$app->post('/user-cp', function(Request $request, Response $response){

    $db = new UserDatabaseWrapper();
    $arr["loggedIn"] = true;

    if (isset($_SESSION["isAdmin"])){
        if ($_SESSION["isAdmin"] === true){
            $arr["isAdmin"] = true;
        }
    }

    if (isset($_POST['changeEmailSubmit'])) {
        $changeEmailResult = $db->changeEmail();

        if ($changeEmailResult === true){
            $arr["success"] = "You have successfully changed your email address";
            $arr["firstName"] = $_SESSION['firstName'];
        } else {
            $arr["error"] = $changeEmailResult;
            $arr["firstName"] = $_SESSION['firstName'];
        }
    }

    if (isset($_POST['changePasswordSubmit'])) {
        $changePasswordResult = $db->changePassword();

        if ($changePasswordResult === true){
            $arr["success"] = "You have successfully changed your password";
            $arr["firstName"] = $_SESSION['firstName'];
        } else {
            $arr["error"] = $changePasswordResult;
            $arr["firstName"] = $_SESSION['firstName'];
        }
    }

    if (isset($_POST['changeFirstNameSubmit'])) {
        $changeFirstNameResult = $db->changeFirstName();

        if ($changeFirstNameResult === true){
            $arr["success"] = "You have successfully changed your first name that is displayed on the website.";
            $arr["firstName"] = $_SESSION['firstName'];
        } else {
            $arr["error"] = $changeFirstNameResult;
            $arr["firstName"] = $_SESSION['firstName'];
        }
    }


    if ($_SESSION["isAdmin"] === TRUE) {

        if (isset($_POST['makeAdminSubmit'])) {
            $makeAdminResult = $db->makeAdmin();

            if ($makeAdminResult === true) {
                $arr["success"] = "You have successfully given that account administrative privileges.";
                $arr["firstName"] = $_SESSION['firstName'];
                return $this->view->render($response, 'user-cp.html.twig', $arr);
            } else {
                $arr["error"] = $makeAdminResult;
                $arr["firstName"] = $_SESSION['firstName'];
                return $this->view->render($response, 'user-cp.html.twig', $arr);
            }
        }

        if (isset($_POST['removeAdminSubmit'])) {
            $removeAdminResult = $db->removeAdmin();

            if ($removeAdminResult === true) {
                $arr["success"] = "You have successfully removed that account's administrative privileges.";
                $arr["firstName"] = $_SESSION['firstName'];
                return $this->view->render($response, 'user-cp.html.twig', $arr);
            } else {
                $arr["error"] = $removeAdminResult;
                $arr["firstName"] = $_SESSION['firstName'];
                return $this->view->render($response, 'user-cp.html.twig', $arr);
            }
        }

        if (isset($_POST['recipeSubmit'])) {
            $recipeSubmitResult = $db->recipeSubmit();

            if ($recipeSubmitResult === true) {
                $arr["success"] = "You have successfully added that recipe to the database.";
                $arr["firstName"] = $_SESSION['firstName'];
                return $this->view->render($response, 'user-cp.html.twig', $arr);
            } else {
                $arr["error"] = $recipeSubmitResult;
                $arr["firstName"] = $_SESSION['firstName'];
                return $this->view->render($response, 'user-cp.html.twig', $arr);
            }
        }
    }

    return $this->view->render($response, 'user-cp.html.twig', $arr);

});