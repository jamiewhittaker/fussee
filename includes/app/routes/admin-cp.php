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

$app->post('/admin-cp', function(Request $request, Response $response) {

    if ($_SESSION["isAdmin"] === TRUE) {

        $db = new UserDatabaseWrapper();

        if (isset($_POST['makeAdminSubmit'])) {
            $makeAdminResult = $db->makeAdmin();

            if ($makeAdminResult === true) {
                $arr["success"] = "You have successfully given that account administrative privileges.";
                $arr["firstName"] = $_SESSION['firstName'];
                return $this->view->render($response, 'admin-cp-success.html.twig', $arr);
            } else {
                $arr["error"] = $makeAdminResult;
                $arr["firstName"] = $_SESSION['firstName'];
                return $this->view->render($response, 'admin-cp-error.html.twig', $arr);
            }
        }

        if (isset($_POST['removeAdminSubmit'])) {
            $removeAdminResult = $db->removeAdmin();

            if ($removeAdminResult === true) {
                $arr["success"] = "You have successfully removed that account's administrative privileges.";
                $arr["firstName"] = $_SESSION['firstName'];
                return $this->view->render($response, 'admin-cp-success.html.twig', $arr);
            } else {
                $arr["error"] = $removeAdminResult;
                $arr["firstName"] = $_SESSION['firstName'];
                return $this->view->render($response, 'admin-cp-error.html.twig', $arr);
            }
        }

        if (isset($_POST['recipeSubmit'])) {
            $recipeSubmitResult = $db->recipeSubmit();

            if ($recipeSubmitResult === true) {
                $arr["success"] = "You have successfully added that recipe to the database.";
                $arr["firstName"] = $_SESSION['firstName'];
                return $this->view->render($response, 'admin-cp-success.html.twig', $arr);
            } else {
                $arr["error"] = $recipeSubmitResult;
                $arr["firstName"] = $_SESSION['firstName'];
                return $this->view->render($response, 'admin-cp-error.html.twig', $arr);
            }
        }
    }


});