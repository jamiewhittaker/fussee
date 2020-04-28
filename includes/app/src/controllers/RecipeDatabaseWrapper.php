<?php
/**
 * Controller for retrieving the parameters from the CircuitStatus class and sending it over to the database
 */


namespace App\controllers;

use PDO;


class RecipeDatabaseWrapper
{
    private $database;


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

            array_push($featured, $row);
        }

        return $featured;
    }


    public function searchRecipesOneTag($tag){
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query =
            "WITH resultSet AS (
            SELECT DISTINCT `recipeID` from `ingredients` WHERE `ingredient` LIKE concat('%', :tag, '%')
            )
            
            SELECT * FROM recipes, resultSet WHERE recipes.recipeID = resultSet.recipeID";
        $sql = $this->database->prepare($query);
        $sql->bindParam('tag', $tag);
        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }


    public function searchRecipesTwoTags($tags){
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query =
            "WITH resultSet2 AS (
            
            WITH resultSet AS (
            SELECT * FROM `ingredients` WHERE `ingredient` LIKE concat('%', :tag1, '%')
            )
            
            SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet
            WHERE ingredients.recipeID = resultSet.recipeID AND ingredients.ingredient LIKE concat('%', :tag2, '%')
            )
            
            SELECT * FROM recipes, resultSet2 WHERE recipes.recipeID = resultSet2.recipeID";

        $sql = $this->database->prepare($query);
        $sql->bindParam('tag1', $tags[0]);
        $sql->bindParam('tag2', $tags[1]);
        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }


    public function searchRecipesThreeTags($tags){
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "WITH resultSet3 AS (
        WITH resultSet2 AS (
        WITH resultSet AS (SELECT * FROM `ingredients` WHERE `ingredient` LIKE concat('%', :tag1, '%'))
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet
        WHERE ingredients.recipeID = resultSet.recipeID AND ingredients.ingredient LIKE concat('%', :tag2, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet2
        WHERE ingredients.recipeID = resultSet2.recipeID AND ingredients.ingredient LIKE concat('%', :tag3, '%') 
        )
        
        SELECT * FROM recipes, resultSet3 WHERE recipes.recipeID = resultSet3.recipeID
        ";

        $sql = $this->database->prepare($query);
        $sql->bindParam('tag1', $tags[0]);
        $sql->bindParam('tag2', $tags[1]);
        $sql->bindParam('tag3', $tags[2]);
        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }


    public function searchRecipesFourTags($tags){
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        WITH resultSet4 AS (
        WITH resultSet3 AS (
        WITH resultSet2 AS (
        WITH resultSet AS (SELECT * FROM `ingredients` WHERE `ingredient` LIKE concat('%', :tag1, '%'))
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet
        WHERE ingredients.recipeID = resultSet.recipeID AND ingredients.ingredient LIKE concat('%', :tag2, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet2
        WHERE ingredients.recipeID = resultSet2.recipeID AND ingredients.ingredient LIKE concat('%', :tag3, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet3
        WHERE ingredients.recipeID = resultSet3.recipeID AND ingredients.ingredient LIKE concat('%', :tag4, '%') 
        )
        
        SELECT * FROM recipes, resultSet4 WHERE recipes.recipeID = resultSet4.recipeID
        ";

        $sql = $this->database->prepare($query);
        $sql->bindParam('tag1', $tags[0]);
        $sql->bindParam('tag2', $tags[1]);
        $sql->bindParam('tag3', $tags[2]);
        $sql->bindParam('tag4', $tags[3]);
        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }


    public function searchRecipesFiveTags($tags){
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        WITH resultSet5 AS (
        WITH resultSet4 AS (
        WITH resultSet3 AS (
        WITH resultSet2 AS (
        WITH resultSet AS (SELECT * FROM `ingredients` WHERE `ingredient` LIKE concat('%', :tag1, '%'))
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet
        WHERE ingredients.recipeID = resultSet.recipeID AND ingredients.ingredient LIKE concat('%', :tag2, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet2
        WHERE ingredients.recipeID = resultSet2.recipeID AND ingredients.ingredient LIKE concat('%', :tag3, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet3
        WHERE ingredients.recipeID = resultSet3.recipeID AND ingredients.ingredient LIKE concat('%', :tag4, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet4
        WHERE ingredients.recipeID = resultSet4.recipeID AND ingredients.ingredient LIKE concat('%', :tag5, '%') 
        )
        
        SELECT * FROM recipes, resultSet5 WHERE recipes.recipeID = resultSet5.recipeID
        ";

        $sql = $this->database->prepare($query);
        $sql->bindParam('tag1', $tags[0]);
        $sql->bindParam('tag2', $tags[1]);
        $sql->bindParam('tag3', $tags[2]);
        $sql->bindParam('tag4', $tags[3]);
        $sql->bindParam('tag5', $tags[4]);
        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }


    public function searchRecipesOneYesOneNo($yesTags, $noTags) {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        WITH finalResultSet AS (
        
        WITH yesResultSet AS (
        SELECT DISTINCT `recipeID` from `ingredients` WHERE `ingredient` LIKE concat('%', :yesTag1, '%')
        ), noResultSet AS (
        SELECT DISTINCT `recipeID` from `ingredients` WHERE `ingredient` LIKE concat('%', :noTag1, '%')
        )
        
        SELECT DISTINCT `recipeID` FROM yesResultSet WHERE `recipeID` NOT IN (SELECT recipeID FROM noResultSet)
        )
        
        SELECT * FROM recipes, finalResultSet WHERE recipes.recipeID = finalResultSet.recipeID
        ";

        $sql = $this->database->prepare($query);
        $sql->bindParam('yesTag1', $yesTags[0]);
        $sql->bindParam('noTag1', $noTags[0]);
        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }


    public function searchRecipesOneYesTwoNo($yesTags, $noTags) {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        WITH finalResultSet AS (
        
        WITH yesResultSet AS (
        SELECT DISTINCT `recipeID` from `ingredients` WHERE `ingredient` LIKE concat('%', :yesTag1, '%')
        ), noResultSet AS (
        SELECT DISTINCT `recipeID` from `ingredients` WHERE `ingredient` LIKE concat('%', :noTag1, '%') OR `ingredient` LIKE concat('%', :noTag2, '%')
        )
        
        SELECT DISTINCT `recipeID` FROM yesResultSet WHERE `recipeID` NOT IN (SELECT recipeID FROM noResultSet)
        )
        
        SELECT * FROM recipes, finalResultSet WHERE recipes.recipeID = finalResultSet.recipeID
        ";

        $sql = $this->database->prepare($query);
        $sql->bindParam('yesTag1', $yesTags[0]);
        $sql->bindParam('noTag1', $noTags[0]);
        $sql->bindParam('noTag2', $noTags[1]);
        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }


    public function searchRecipesOneYesThreeNo($yesTags, $noTags) {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        WITH finalResultSet AS (
        
        WITH yesResultSet AS (
        SELECT DISTINCT `recipeID` from `ingredients` WHERE `ingredient` LIKE concat('%', :yesTag1, '%')
        ), noResultSet AS (
        SELECT DISTINCT `recipeID` from `ingredients` WHERE `ingredient` LIKE concat('%', :noTag1, '%') OR `ingredient` LIKE concat('%', :noTag2, '%') OR `ingredient` LIKE concat('%', :noTag3, '%')
        )
        
        SELECT DISTINCT `recipeID` FROM yesResultSet WHERE `recipeID` NOT IN (SELECT recipeID FROM noResultSet)
        )
        
        SELECT * FROM recipes, finalResultSet WHERE recipes.recipeID = finalResultSet.recipeID
        ";

        $sql = $this->database->prepare($query);
        $sql->bindParam('yesTag1', $yesTags[0]);
        $sql->bindParam('noTag1', $noTags[0]);
        $sql->bindParam('noTag2', $noTags[1]);
        $sql->bindParam('noTag3', $noTags[2]);

        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }


    public function searchRecipesOneYesFourNo($yesTags, $noTags) {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        WITH finalResultSet AS (
        
        WITH yesResultSet AS (
        SELECT DISTINCT `recipeID` from `ingredients` WHERE `ingredient` LIKE concat('%', :yesTag1, '%')
        ), noResultSet AS (
        SELECT DISTINCT `recipeID` from `ingredients` WHERE `ingredient` LIKE concat('%', :noTag1, '%') OR `ingredient` LIKE concat('%', :noTag2, '%') OR `ingredient` LIKE concat('%', :noTag3, '%') OR `ingredient` LIKE concat('%', :noTag4, '%')
        )
        
        SELECT DISTINCT `recipeID` FROM yesResultSet WHERE `recipeID` NOT IN (SELECT recipeID FROM noResultSet)
        )
        
        SELECT * FROM recipes, finalResultSet WHERE recipes.recipeID = finalResultSet.recipeID
        ";

        $sql = $this->database->prepare($query);
        $sql->bindParam('yesTag1', $yesTags[0]);
        $sql->bindParam('noTag1', $noTags[0]);
        $sql->bindParam('noTag2', $noTags[1]);
        $sql->bindParam('noTag3', $noTags[2]);
        $sql->bindParam('noTag4', $noTags[3]);


        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }


    public function searchRecipesOneYesFiveNo($yesTags, $noTags) {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        WITH finalResultSet AS (
        
        WITH yesResultSet AS (
        SELECT DISTINCT `recipeID` from `ingredients` WHERE `ingredient` LIKE concat('%', :yesTag1, '%')
        ), noResultSet AS (
        SELECT DISTINCT `recipeID` from `ingredients` WHERE `ingredient` LIKE concat('%', :noTag1, '%') OR `ingredient` LIKE concat('%', :noTag2, '%') OR `ingredient` LIKE concat('%', :noTag3, '%') OR `ingredient` LIKE concat('%', :noTag4, '%') OR `ingredient` LIKE concat('%', :noTag5, '%')
        )
        
        SELECT DISTINCT `recipeID` FROM yesResultSet WHERE `recipeID` NOT IN (SELECT recipeID FROM noResultSet)
        )
        
        SELECT * FROM recipes, finalResultSet WHERE recipes.recipeID = finalResultSet.recipeID
        ";

        $sql = $this->database->prepare($query);
        $sql->bindParam('yesTag1', $yesTags[0]);
        $sql->bindParam('noTag1', $noTags[0]);
        $sql->bindParam('noTag2', $noTags[1]);
        $sql->bindParam('noTag3', $noTags[2]);
        $sql->bindParam('noTag4', $noTags[3]);
        $sql->bindParam('noTag5', $noTags[4]);



        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }


    public function searchRecipesTwoYesOneNo($yesTags, $noTags) {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        WITH finalResultSet AS (
        
        WITH yesResultSet AS (
            WITH resultSet AS (
            SELECT * FROM `ingredients` WHERE `ingredient` LIKE concat('%', :yesTag1, '%')
            )
            
            SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet
            WHERE ingredients.recipeID = resultSet.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag2, '%')
            
        ), noResultSet AS (
        SELECT DISTINCT `recipeID` from `ingredients` WHERE `ingredient` LIKE concat('%', :noTag1, '%')
        )
        
        SELECT DISTINCT `recipeID` FROM yesResultSet WHERE `recipeID` NOT IN (SELECT recipeID FROM noResultSet)
        )
        
        SELECT * FROM recipes, finalResultSet WHERE recipes.recipeID = finalResultSet.recipeID
        ";

        $sql = $this->database->prepare($query);
        $sql->bindParam('yesTag1', $yesTags[0]);
        $sql->bindParam('yesTag2', $yesTags[1]);
        $sql->bindParam('noTag1', $noTags[0]);
        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }


    public function searchRecipesTwoYesTwoNo($yesTags, $noTags) {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        WITH finalResultSet AS (
        
        WITH yesResultSet AS (
            WITH resultSet AS (
            SELECT * FROM `ingredients` WHERE `ingredient` LIKE concat('%', :yesTag1, '%')
            )
            
            SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet
            WHERE ingredients.recipeID = resultSet.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag2, '%')
            
        ), noResultSet AS (
        SELECT DISTINCT `recipeID` from `ingredients` WHERE `ingredient` LIKE concat('%', :noTag1, '%') OR `ingredient` LIKE concat('%', :noTag2, '%')
        )
        
        SELECT DISTINCT `recipeID` FROM yesResultSet WHERE `recipeID` NOT IN (SELECT recipeID FROM noResultSet)
        )
        
        SELECT * FROM recipes, finalResultSet WHERE recipes.recipeID = finalResultSet.recipeID
        ";

        $sql = $this->database->prepare($query);
        $sql->bindParam('yesTag1', $yesTags[0]);
        $sql->bindParam('yesTag2', $yesTags[1]);
        $sql->bindParam('noTag1', $noTags[0]);
        $sql->bindParam('noTag2', $noTags[1]);

        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }


    public function searchRecipesTwoYesThreeNo($yesTags, $noTags) {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        WITH finalResultSet AS (
        
        WITH yesResultSet AS (
            WITH resultSet AS (
            SELECT * FROM `ingredients` WHERE `ingredient` LIKE concat('%', :yesTag1, '%')
            )
            
            SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet
            WHERE ingredients.recipeID = resultSet.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag2, '%')
            
        ), noResultSet AS (
        SELECT DISTINCT `recipeID` from `ingredients` WHERE `ingredient` LIKE concat('%', :noTag1, '%') OR `ingredient` LIKE concat('%', :noTag2, '%') OR `ingredient` LIKE concat('%', :noTag3, '%')
        )
        
        SELECT DISTINCT `recipeID` FROM yesResultSet WHERE `recipeID` NOT IN (SELECT recipeID FROM noResultSet)
        )
        
        SELECT * FROM recipes, finalResultSet WHERE recipes.recipeID = finalResultSet.recipeID
        ";

        $sql = $this->database->prepare($query);
        $sql->bindParam('yesTag1', $yesTags[0]);
        $sql->bindParam('yesTag2', $yesTags[1]);
        $sql->bindParam('noTag1', $noTags[0]);
        $sql->bindParam('noTag2', $noTags[1]);
        $sql->bindParam('noTag3', $noTags[2]);


        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }


    public function searchRecipesTwoYesFourNo($yesTags, $noTags) {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        WITH finalResultSet AS (
        
        WITH yesResultSet AS (
            WITH resultSet AS (
            SELECT * FROM `ingredients` WHERE `ingredient` LIKE concat('%', :yesTag1, '%')
            )
            
            SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet
            WHERE ingredients.recipeID = resultSet.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag2, '%')
            
        ), noResultSet AS (
        SELECT DISTINCT `recipeID` from `ingredients` WHERE `ingredient` LIKE concat('%', :noTag1, '%') OR `ingredient` LIKE concat('%', :noTag2, '%') OR `ingredient` LIKE concat('%', :noTag3, '%') OR `ingredient` LIKE concat('%', :noTag4, '%')
        )
        
        SELECT DISTINCT `recipeID` FROM yesResultSet WHERE `recipeID` NOT IN (SELECT recipeID FROM noResultSet)
        )
        
        SELECT * FROM recipes, finalResultSet WHERE recipes.recipeID = finalResultSet.recipeID
        ";

        $sql = $this->database->prepare($query);
        $sql->bindParam('yesTag1', $yesTags[0]);
        $sql->bindParam('yesTag2', $yesTags[1]);
        $sql->bindParam('noTag1', $noTags[0]);
        $sql->bindParam('noTag2', $noTags[1]);
        $sql->bindParam('noTag3', $noTags[2]);
        $sql->bindParam('noTag4', $noTags[3]);


        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }


    public function searchRecipesTwoYesFiveNo($yesTags, $noTags) {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        WITH finalResultSet AS (
        
        WITH yesResultSet AS (
            WITH resultSet AS (
            SELECT * FROM `ingredients` WHERE `ingredient` LIKE concat('%', :yesTag1, '%')
            )
            
            SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet
            WHERE ingredients.recipeID = resultSet.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag2, '%')
            
        ), noResultSet AS (
        SELECT DISTINCT `recipeID` from `ingredients` WHERE `ingredient` LIKE concat('%', :noTag1, '%') OR `ingredient` LIKE concat('%', :noTag2, '%') OR `ingredient` LIKE concat('%', :noTag3, '%') OR `ingredient` LIKE concat('%', :noTag4, '%') OR `ingredient` LIKE concat('%', :noTag5, '%')
        )
        
        SELECT DISTINCT `recipeID` FROM yesResultSet WHERE `recipeID` NOT IN (SELECT recipeID FROM noResultSet)
        )
        
        SELECT * FROM recipes, finalResultSet WHERE recipes.recipeID = finalResultSet.recipeID
        ";

        $sql = $this->database->prepare($query);
        $sql->bindParam('yesTag1', $yesTags[0]);
        $sql->bindParam('yesTag2', $yesTags[1]);
        $sql->bindParam('noTag1', $noTags[0]);
        $sql->bindParam('noTag2', $noTags[1]);
        $sql->bindParam('noTag3', $noTags[2]);
        $sql->bindParam('noTag4', $noTags[3]);
        $sql->bindParam('noTag5', $noTags[4]);



        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }





}
