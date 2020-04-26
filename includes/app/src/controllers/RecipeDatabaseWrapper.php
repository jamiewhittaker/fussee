<?php
/**
 * Controller for retrieving the parameters from the CircuitStatus class and sending it over to the database
 */


namespace App\controllers;

use PDO;


class RecipeDatabaseWrapper
{
    private $database;
    private $user;


    public function getFeatured(){
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "select * from `featured`";
        $sql = $this->database->prepare($query);
        $sql->execute();

        $featured = [];

        foreach ($sql as $recipe) {
            $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
            $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "select * from `recipes` WHERE `recipeID`=:recipeID";
            $sql = $this->database->prepare($query);
            $sql->bindParam('recipeID', $recipe["recipeID"]);
            $sql->execute();

            $row = $sql->fetch(PDO::FETCH_ASSOC);

            $recipeInfo = array(
                "recipeTitle" => $row['recipeTitle'],
                "recipePrepTime" => $row['recipePrepTime'],
                "recipeCookTime" => $row['recipeCookTime'],
                "recipeServings" => $row['recipeServings'],
                "recipeSource" => $row['recipeSource'],
                "recipeURL" => $row['recipeURL'],
                "recipeImageURL" => $row['recipeImageURL'],
            );

            array_push($featured, $recipeInfo);
        }

        return $featured;
    }

    public function getRecipeInfo($recipeID){

        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "select * from `recipes` WHERE `recipeID`=:recipeID";
        $sql = $this->database->prepare($query);
        $sql->bindParam('recipeID', $recipeID);
        $sql->execute();

        $row = $sql->fetch(PDO::FETCH_ASSOC);

        $recipeInfo = array(
            "recipeTitle" => $row['recipeTitle'],
            "recipePrepTime" => $row['recipePrepTime'],
            "recipeCookTime" => $row['recipeCookTime'],
            "recipeServings" => $row['recipeServings'],
            "recipeSource" => $row['recipeSource'],
            "recipeURL" => $row['recipeURL'],
            "recipeImageURL" => $row['recipeImageURL'],
        );

        return $recipeInfo;

    }
}
