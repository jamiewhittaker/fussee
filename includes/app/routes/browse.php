<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\controllers\RecipeDatabaseWrapper;

$app->get('/browse', function(Request $request, Response $response)
{
    if ($_SESSION["loggedIn"] === TRUE) {

        $db = new RecipeDatabaseWrapper();
        $featuredResult = $db->getFeatured();

        $arr["featured"] = $featuredResult;
        $arr["firstName"] = $_SESSION["firstName"];
        return $this->view->render($response, 'browse-loggedin.html.twig', $arr);
    } else {
        session_destroy();
        return $this->view->render($response, 'browse-loggedout.html.twig');
    }
})->setName('/browse' );

$app->post('/browse', function(Request $request, Response $response) {
    $yesTags = $_POST['yesIngredients'];
    $yesTagsArray = explode (",", $yesTags);

    $noTags = $_POST['noIngredients'];
    $noTagsArray = explode (",", $noTags);

    $db = new RecipeDatabaseWrapper();

    if ($noTags === "") {
        if (count($yesTagsArray) === 1){
        $searchResult = $db->searchRecipesOneTag($yesTagsArray[0]);
        }

        if (count($yesTagsArray) === 2){
            $searchResult = $db->searchRecipesTwoTags($yesTagsArray);
        }

        if (count($yesTagsArray) === 3){
            $searchResult = $db->searchRecipesThreeTags($yesTagsArray);
        }

        if (count($yesTagsArray) === 4){
            $searchResult = $db->searchRecipesFourTags($yesTagsArray);
        }

        if (count($yesTagsArray) === 5){
            $searchResult = $db->searchRecipesFiveTags($yesTagsArray);
        }
    } else {
        if ((count($yesTagsArray) === 1) AND (count($noTagsArray) === 1)) {
            $searchResult = $db->searchRecipesOneYesOneNo($yesTagsArray, $noTagsArray);
        }

        if ((count($yesTagsArray) === 1) AND (count($noTagsArray) === 2)) {
            $searchResult = $db->searchRecipesOneYesTwoNo($yesTagsArray, $noTagsArray);
        }

        if ((count($yesTagsArray) === 1) AND (count($noTagsArray) === 3)) {
            $searchResult = $db->searchRecipesOneYesThreeNo($yesTagsArray, $noTagsArray);
        }

        if ((count($yesTagsArray) === 1) AND (count($noTagsArray) === 4)) {
            $searchResult = $db->searchRecipesOneYesFourNo($yesTagsArray, $noTagsArray);
        }

        if ((count($yesTagsArray) === 1) AND (count($noTagsArray) === 5)) {
            $searchResult = $db->searchRecipesOneYesFiveNo($yesTagsArray, $noTagsArray);
        }

        if ((count($yesTagsArray) === 2) AND (count($noTagsArray) === 1)) {
            $searchResult = $db->searchRecipesTwoYesOneNo($yesTagsArray, $noTagsArray);
        }

        if ((count($yesTagsArray) === 2) AND (count($noTagsArray) === 2)) {
            $searchResult = $db->searchRecipesTwoYesTwoNo($yesTagsArray, $noTagsArray);
        }

        if ((count($yesTagsArray) === 2) AND (count($noTagsArray) === 3)) {
            $searchResult = $db->searchRecipesTwoYesThreeNo($yesTagsArray, $noTagsArray);
        }

        if ((count($yesTagsArray) === 2) AND (count($noTagsArray) === 4)) {
            $searchResult = $db->searchRecipesTwoYesFourNo($yesTagsArray, $noTagsArray);
        }

        if ((count($yesTagsArray) === 2) AND (count($noTagsArray) === 5)) {
            $searchResult = $db->searchRecipesTwoYesFiveNo($yesTagsArray, $noTagsArray);
        }
    }





    $arr["resultNumber"] = count($searchResult);
    $arr["yesSearched"] = $yesTags;
    $arr["noSearched"] = $noTags;
    $arr["search"] = $searchResult;
    $arr["firstName"] = $_SESSION["firstName"];

    return $this->view->render($response, 'search.html.twig', $arr);
});