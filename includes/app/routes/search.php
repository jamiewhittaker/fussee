<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\controllers\RecipeDatabaseWrapper;

$app->post('/search', function(Request $request, Response $response) {
    $yesTags = $_POST['yesIngredients'];
    $yesTagsArray = explode (",", $yesTags);

    $noTags = $_POST['noIngredients'];
    $noTagsArray = explode (",", $noTags);

    $db = new RecipeDatabaseWrapper();

    if (isset($_SESSION['loggedIn'])) {
        $arr["loggedIn"] = true;
        $arr["firstName"] = $_SESSION['firstName'];
    }

    foreach ($yesTagsArray as $tag) {
        if ( (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/ ', $tag) ) ) {
            $arr["error"] = "One or more search tags contain invalid special character(s)";
            return $this->view->render($response, 'search.html.twig', $arr);
        }
    }

    foreach ($noTagsArray as $tag) {
        if ( (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/ ', $tag) ) ) {
            $arr["error"] = "One or more search tags contain invalid special character(s)";
            return $this->view->render($response, 'search.html.twig', $arr);
        }
    }

    if (($yesTags === "") AND ($noTags === "")) {
        $arr["error"] = "Search fields empty, please enter some search tags";
        return $this->view->render($response, 'search.html.twig', $arr);
    }

    if (count($yesTagsArray) > 5) {
        $arr["error"] = "Maximum of 5 tags allowed in each field";
        return $this->view->render($response, 'search.html.twig', $arr);
    }

    if (count($noTagsArray) > 5) {
        $arr["error"] = "Maximum of 5 tags allowed in each field";
        return $this->view->render($response, 'search.html.twig', $arr);
    }

    foreach ($yesTagsArray as $tag){
        if ($tag !== "") {
            if ((ctype_alpha($tag) === FALSE)) {
                $arr["error"] = "Tags can only contain alphabetic characters";
                return $this->view->render($response, 'search.html.twig', $arr);
            }
        }
    }

    foreach ($noTagsArray as $tag){
        if ($tag !== "") {
            if ((ctype_alpha($tag) === FALSE)) {
                $arr["error"] = "Tags can only contain alphabetic characters";
                return $this->view->render($response, 'search.html.twig', $arr);
            }
        }
    }

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

        if ((count($yesTagsArray) === 3) AND (count($noTagsArray) === 1)) {
            $searchResult = $db->searchRecipesThreeYesOneNo($yesTagsArray, $noTagsArray);
        }
        if ((count($yesTagsArray) === 3) AND (count($noTagsArray) === 2)) {
            $searchResult = $db->searchRecipesThreeYesTwoNo($yesTagsArray, $noTagsArray);
        }
        if ((count($yesTagsArray) === 3) AND (count($noTagsArray) === 3)) {
            $searchResult = $db->searchRecipesThreeYesThreeNo($yesTagsArray, $noTagsArray);
        }
        if ((count($yesTagsArray) === 3) AND (count($noTagsArray) === 4)) {
            $searchResult = $db->searchRecipesThreeYesFourNo($yesTagsArray, $noTagsArray);
        }
        if ((count($yesTagsArray) === 3) AND (count($noTagsArray) === 5)) {
            $searchResult = $db->searchRecipesThreeYesFiveNo($yesTagsArray, $noTagsArray);
        }

        if ((count($yesTagsArray) === 4) AND (count($noTagsArray) === 1)) {
            $searchResult = $db->searchRecipesFourYesOneNo($yesTagsArray, $noTagsArray);
        }
        if ((count($yesTagsArray) === 4) AND (count($noTagsArray) === 2)) {
            $searchResult = $db->searchRecipesFourYesTwoNo($yesTagsArray, $noTagsArray);
        }
        if ((count($yesTagsArray) === 4) AND (count($noTagsArray) === 3)) {
            $searchResult = $db->searchRecipesFourYesThreeNo($yesTagsArray, $noTagsArray);
        }
        if ((count($yesTagsArray) === 4) AND (count($noTagsArray) === 4)) {
            $searchResult = $db->searchRecipesFourYesFourNo($yesTagsArray, $noTagsArray);
        }
        if ((count($yesTagsArray) === 4) AND (count($noTagsArray) === 5)) {
            $searchResult = $db->searchRecipesFourYesFiveNo($yesTagsArray, $noTagsArray);
        }

        if ((count($yesTagsArray) === 5) AND (count($noTagsArray) === 1)) {
            $searchResult = $db->searchRecipesFiveYesOneNo($yesTagsArray, $noTagsArray);
        }
        if ((count($yesTagsArray) === 5) AND (count($noTagsArray) === 2)) {
            $searchResult = $db->searchRecipesFiveYesTwoNo($yesTagsArray, $noTagsArray);
        }
        if ((count($yesTagsArray) === 5) AND (count($noTagsArray) === 3)) {
            $searchResult = $db->searchRecipesFiveYesThreeNo($yesTagsArray, $noTagsArray);
        }
        if ((count($yesTagsArray) === 5) AND (count($noTagsArray) === 4)) {
            $searchResult = $db->searchRecipesFiveYesFourNo($yesTagsArray, $noTagsArray);
        }
        if ((count($yesTagsArray) === 5) AND (count($noTagsArray) === 5)) {
            $searchResult = $db->searchRecipesFiveYesFiveNo($yesTagsArray, $noTagsArray);
        }
    }


    $arr["resultNumber"] = count($searchResult);
    $arr["yesSearched"] = $yesTags;
    $arr["noSearched"] = $noTags;
    $arr["search"] = $searchResult;

    if (count($searchResult) === 0)
    {
        $arr["error"] = "No results found, try using different tags";
        return $this->view->render($response, 'search.html.twig', $arr);
    }


    return $this->view->render($response, 'search.html.twig', $arr);
    
});