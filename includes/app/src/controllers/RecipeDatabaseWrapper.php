<?php
/**
 * Controller for retrieving the parameters from the CircuitStatus class and sending it over to the database
 */


namespace App\controllers;

use PDO;


class RecipeDatabaseWrapper
{
    private $database;

    public function getBrowse($start) {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $findStatement = "SELECT * FROM recipes ORDER BY recipeID ASC LIMIT :start, 20";
        $sql = $this->database->prepare($findStatement);
        $sql->bindValue(':start', (int) trim($start), PDO::PARAM_INT);
        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

    public function getBrowseCount() {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $findStatement = "SELECT COUNT(*) FROM `recipes`";
        $sql = $this->database->prepare($findStatement);
        $sql->execute();

        $count = $sql->fetchColumn();
        return $count;
    }

    public function recipeExists($id) {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $findStatement = "SELECT COUNT(*) FROM `recipes` WHERE `recipeID` = :recipeID";
        $sql = $this->database->prepare($findStatement);
        $sql->bindParam('recipeID', $id);
        $sql->execute();

        $count = $sql->fetchColumn();

        if ($count > 0){
            return TRUE;
        } else {
            return FALSE;
        }
    }



    public function getFeatured(){
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "WITH resultSet AS (
            SELECT DISTINCT `recipeID` from `featured` ORDER BY timeAdded DESC LIMIT 5
            )
            
            SELECT * FROM recipes, resultSet WHERE recipes.recipeID = resultSet.recipeID";
        $sql = $this->database->prepare($query);
        $sql->bindParam('userID', $_SESSION['userID']);
        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

    public function getRandomRecipes() {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "SELECT * FROM recipes ORDER BY RAND() LIMIT 3";
        $sql = $this->database->prepare($query);
        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;

    }

    public function addFavourite($userID, $recipeID){
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $findStatement = "SELECT COUNT(*) FROM `favourites` WHERE userID = :userID AND recipeID = :recipeID";
        $sql = $this->database->prepare($findStatement);
        $sql->bindParam(':userID', $userID);
        $sql->bindParam(':recipeID', $recipeID);
        $sql->execute();

        $count = $sql->fetchColumn();

        if ($count < 1) {
            $insertStatement = "INSERT into `favourites` (userID, recipeID) VALUES (:userID, :recipeID)";

            $sql = $this->database->prepare($insertStatement);

            $sql->bindParam(':userID', $userID);
            $sql->bindParam(':recipeID', $recipeID);

            $sql->execute();
        }

    }

    public function removeFavourite($userID, $recipeID){
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $findStatement = "SELECT COUNT(*) FROM `favourites` WHERE userID = :userID AND recipeID = :recipeID";
        $sql = $this->database->prepare($findStatement);
        $sql->bindParam(':userID', $userID);
        $sql->bindParam(':recipeID', $recipeID);
        $sql->execute();

        $count = $sql->fetchColumn();

        if ($count > 0) {
            $deleteStatement = "DELETE FROM `favourites` WHERE userID = :userID AND recipeID = :recipeID";

            $sql = $this->database->prepare($deleteStatement);

            $sql->bindParam(':userID', $userID);
            $sql->bindParam(':recipeID', $recipeID);

            $sql->execute();
        }

    }


    public function getFavourites($start){
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "WITH resultSet AS (
            SELECT DISTINCT `recipeID` from `favourites` WHERE `userID` = :userID ORDER BY recipeID ASC LIMIT :start, 20
            )
            
            SELECT * FROM recipes, resultSet WHERE recipes.recipeID = resultSet.recipeID";
        $sql = $this->database->prepare($query);
        $sql->bindParam('userID', $_SESSION['userID']);
        $sql->bindValue(':start', (int) trim($start), PDO::PARAM_INT);
        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

    public function getFavouritesCount(){
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "WITH resultSet AS (
            SELECT DISTINCT `recipeID` from `favourites` WHERE `userID` = :userID
            )
            
            SELECT COUNT(*) FROM recipes, resultSet WHERE recipes.recipeID = resultSet.recipeID";
        $sql = $this->database->prepare($query);
        $sql->bindParam('userID', $_SESSION['userID']);
        $sql->execute();

        $count = $sql->fetchColumn();
        return $count;
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

    public function searchRecipesThreeYesOneNo($yesTags, $noTags) {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        WITH finalResultSet AS (
        
        WITH yesResultSet AS (
            WITH resultSet2 AS (
            WITH resultSet AS (SELECT * FROM `ingredients` WHERE `ingredient` LIKE concat('%', :yesTag1, '%'))
            
            SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet
            WHERE ingredients.recipeID = resultSet.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag2, '%') 
            )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet2
        WHERE ingredients.recipeID = resultSet2.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag3, '%')
            
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
        $sql->bindParam('yesTag3', $yesTags[2]);
        $sql->bindParam('noTag1', $noTags[0]);
        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }
    public function searchRecipesThreeYesTwoNo($yesTags, $noTags) {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        WITH finalResultSet AS (
        
        WITH yesResultSet AS (
            WITH resultSet2 AS (
            WITH resultSet AS (SELECT * FROM `ingredients` WHERE `ingredient` LIKE concat('%', :yesTag1, '%'))
            
            SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet
            WHERE ingredients.recipeID = resultSet.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag2, '%') 
            )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet2
        WHERE ingredients.recipeID = resultSet2.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag3, '%')
            
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
        $sql->bindParam('yesTag3', $yesTags[2]);
        $sql->bindParam('noTag1', $noTags[0]);
        $sql->bindParam('noTag2', $noTags[1]);
        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }
    public function searchRecipesThreeYesThreeNo($yesTags, $noTags) {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        WITH finalResultSet AS (
        
        WITH yesResultSet AS (
            WITH resultSet2 AS (
            WITH resultSet AS (SELECT * FROM `ingredients` WHERE `ingredient` LIKE concat('%', :yesTag1, '%'))
            
            SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet
            WHERE ingredients.recipeID = resultSet.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag2, '%') 
            )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet2
        WHERE ingredients.recipeID = resultSet2.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag3, '%')
            
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
        $sql->bindParam('yesTag3', $yesTags[2]);
        $sql->bindParam('noTag1', $noTags[0]);
        $sql->bindParam('noTag2', $noTags[1]);
        $sql->bindParam('noTag3', $noTags[2]);
        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }
    public function searchRecipesThreeYesFourNo($yesTags, $noTags) {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        WITH finalResultSet AS (
        
        WITH yesResultSet AS (
            WITH resultSet2 AS (
            WITH resultSet AS (SELECT * FROM `ingredients` WHERE `ingredient` LIKE concat('%', :yesTag1, '%'))
            
            SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet
            WHERE ingredients.recipeID = resultSet.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag2, '%') 
            )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet2
        WHERE ingredients.recipeID = resultSet2.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag3, '%')
            
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
        $sql->bindParam('yesTag3', $yesTags[2]);
        $sql->bindParam('noTag1', $noTags[0]);
        $sql->bindParam('noTag2', $noTags[1]);
        $sql->bindParam('noTag3', $noTags[2]);
        $sql->bindParam('noTag4', $noTags[3]);

        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }
    public function searchRecipesThreeYesFiveNo($yesTags, $noTags) {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        WITH finalResultSet AS (
        
        WITH yesResultSet AS (
            WITH resultSet2 AS (
            WITH resultSet AS (SELECT * FROM `ingredients` WHERE `ingredient` LIKE concat('%', :yesTag1, '%'))
            
            SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet
            WHERE ingredients.recipeID = resultSet.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag2, '%') 
            )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet2
        WHERE ingredients.recipeID = resultSet2.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag3, '%')
            
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
        $sql->bindParam('yesTag3', $yesTags[2]);
        $sql->bindParam('noTag1', $noTags[0]);
        $sql->bindParam('noTag2', $noTags[1]);
        $sql->bindParam('noTag3', $noTags[2]);
        $sql->bindParam('noTag4', $noTags[3]);
        $sql->bindParam('noTag5', $noTags[4]);


        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

    public function searchRecipesFourYesOneNo($yesTags, $noTags) {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        WITH finalResultSet AS (
        
        WITH yesResultSet AS (
        WITH resultSet3 AS (
        WITH resultSet2 AS (
        WITH resultSet AS (SELECT * FROM `ingredients` WHERE `ingredient` LIKE concat('%', :yesTag1, '%'))
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet
        WHERE ingredients.recipeID = resultSet.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag2, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet2
        WHERE ingredients.recipeID = resultSet2.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag3, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet3
        WHERE ingredients.recipeID = resultSet3.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag4, '%') 
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
        $sql->bindParam('yesTag3', $yesTags[2]);
        $sql->bindParam('yesTag4', $yesTags[3]);


        $sql->bindParam('noTag1', $noTags[0]);
        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }
    public function searchRecipesFourYesTwoNo($yesTags, $noTags) {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        WITH finalResultSet AS (
        
        WITH yesResultSet AS (
        WITH resultSet3 AS (
        WITH resultSet2 AS (
        WITH resultSet AS (SELECT * FROM `ingredients` WHERE `ingredient` LIKE concat('%', :yesTag1, '%'))
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet
        WHERE ingredients.recipeID = resultSet.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag2, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet2
        WHERE ingredients.recipeID = resultSet2.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag3, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet3
        WHERE ingredients.recipeID = resultSet3.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag4, '%') 
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
        $sql->bindParam('yesTag3', $yesTags[2]);
        $sql->bindParam('yesTag4', $yesTags[3]);


        $sql->bindParam('noTag1', $noTags[0]);
        $sql->bindParam('noTag2', $noTags[1]);

        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }
    public function searchRecipesFourYesThreeNo($yesTags, $noTags) {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        WITH finalResultSet AS (
        
        WITH yesResultSet AS (
        WITH resultSet3 AS (
        WITH resultSet2 AS (
        WITH resultSet AS (SELECT * FROM `ingredients` WHERE `ingredient` LIKE concat('%', :yesTag1, '%'))
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet
        WHERE ingredients.recipeID = resultSet.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag2, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet2
        WHERE ingredients.recipeID = resultSet2.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag3, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet3
        WHERE ingredients.recipeID = resultSet3.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag4, '%') 
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
        $sql->bindParam('yesTag3', $yesTags[2]);
        $sql->bindParam('yesTag4', $yesTags[3]);


        $sql->bindParam('noTag1', $noTags[0]);
        $sql->bindParam('noTag2', $noTags[1]);
        $sql->bindParam('noTag3', $noTags[2]);

        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }
    public function searchRecipesFourYesFourNo($yesTags, $noTags) {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        WITH finalResultSet AS (
        
        WITH yesResultSet AS (
        WITH resultSet3 AS (
        WITH resultSet2 AS (
        WITH resultSet AS (SELECT * FROM `ingredients` WHERE `ingredient` LIKE concat('%', :yesTag1, '%'))
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet
        WHERE ingredients.recipeID = resultSet.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag2, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet2
        WHERE ingredients.recipeID = resultSet2.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag3, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet3
        WHERE ingredients.recipeID = resultSet3.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag4, '%') 
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
        $sql->bindParam('yesTag3', $yesTags[2]);
        $sql->bindParam('yesTag4', $yesTags[3]);


        $sql->bindParam('noTag1', $noTags[0]);
        $sql->bindParam('noTag2', $noTags[1]);
        $sql->bindParam('noTag3', $noTags[2]);
        $sql->bindParam('noTag4', $noTags[3]);


        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }
    public function searchRecipesFourYesFiveNo($yesTags, $noTags) {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        WITH finalResultSet AS (
        
        WITH yesResultSet AS (
        WITH resultSet3 AS (
        WITH resultSet2 AS (
        WITH resultSet AS (SELECT * FROM `ingredients` WHERE `ingredient` LIKE concat('%', :yesTag1, '%'))
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet
        WHERE ingredients.recipeID = resultSet.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag2, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet2
        WHERE ingredients.recipeID = resultSet2.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag3, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet3
        WHERE ingredients.recipeID = resultSet3.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag4, '%') 
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
        $sql->bindParam('yesTag3', $yesTags[2]);
        $sql->bindParam('yesTag4', $yesTags[3]);


        $sql->bindParam('noTag1', $noTags[0]);
        $sql->bindParam('noTag2', $noTags[1]);
        $sql->bindParam('noTag3', $noTags[2]);
        $sql->bindParam('noTag4', $noTags[3]);
        $sql->bindParam('noTag5', $noTags[4]);



        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

    public function searchRecipesFiveYesOneNo($yesTags, $noTags) {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        WITH finalResultSet AS (
        
        WITH yesResultSet AS (
        WITH resultSet4 AS (
        WITH resultSet3 AS (
        WITH resultSet2 AS (
        WITH resultSet AS (SELECT * FROM `ingredients` WHERE `ingredient` LIKE concat('%', :yesTag1, '%'))
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet
        WHERE ingredients.recipeID = resultSet.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag2, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet2
        WHERE ingredients.recipeID = resultSet2.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag3, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet3
        WHERE ingredients.recipeID = resultSet3.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag4, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet4
        WHERE ingredients.recipeID = resultSet4.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag5, '%') 
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
        $sql->bindParam('yesTag3', $yesTags[2]);
        $sql->bindParam('yesTag4', $yesTags[3]);
        $sql->bindParam('yesTag5', $yesTags[4]);

        $sql->bindParam('noTag1', $noTags[0]);
        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }
    public function searchRecipesFiveYesTwoNo($yesTags, $noTags) {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        WITH finalResultSet AS (
        
        WITH yesResultSet AS (
        WITH resultSet4 AS (
        WITH resultSet3 AS (
        WITH resultSet2 AS (
        WITH resultSet AS (SELECT * FROM `ingredients` WHERE `ingredient` LIKE concat('%', :yesTag1, '%'))
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet
        WHERE ingredients.recipeID = resultSet.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag2, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet2
        WHERE ingredients.recipeID = resultSet2.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag3, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet3
        WHERE ingredients.recipeID = resultSet3.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag4, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet4
        WHERE ingredients.recipeID = resultSet4.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag5, '%') 
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
        $sql->bindParam('yesTag3', $yesTags[2]);
        $sql->bindParam('yesTag4', $yesTags[3]);
        $sql->bindParam('yesTag5', $yesTags[4]);

        $sql->bindParam('noTag1', $noTags[0]);
        $sql->bindParam('noTag2', $noTags[1]);


        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }
    public function searchRecipesFiveYesThreeNo($yesTags, $noTags) {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        WITH finalResultSet AS (
        
        WITH yesResultSet AS (
        WITH resultSet4 AS (
        WITH resultSet3 AS (
        WITH resultSet2 AS (
        WITH resultSet AS (SELECT * FROM `ingredients` WHERE `ingredient` LIKE concat('%', :yesTag1, '%'))
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet
        WHERE ingredients.recipeID = resultSet.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag2, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet2
        WHERE ingredients.recipeID = resultSet2.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag3, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet3
        WHERE ingredients.recipeID = resultSet3.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag4, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet4
        WHERE ingredients.recipeID = resultSet4.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag5, '%') 
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
        $sql->bindParam('yesTag3', $yesTags[2]);
        $sql->bindParam('yesTag4', $yesTags[3]);
        $sql->bindParam('yesTag5', $yesTags[4]);

        $sql->bindParam('noTag1', $noTags[0]);
        $sql->bindParam('noTag2', $noTags[1]);
        $sql->bindParam('noTag3', $noTags[2]);



        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }
    public function searchRecipesFiveYesFourNo($yesTags, $noTags) {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        WITH finalResultSet AS (
        
        WITH yesResultSet AS (
        WITH resultSet4 AS (
        WITH resultSet3 AS (
        WITH resultSet2 AS (
        WITH resultSet AS (SELECT * FROM `ingredients` WHERE `ingredient` LIKE concat('%', :yesTag1, '%'))
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet
        WHERE ingredients.recipeID = resultSet.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag2, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet2
        WHERE ingredients.recipeID = resultSet2.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag3, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet3
        WHERE ingredients.recipeID = resultSet3.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag4, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet4
        WHERE ingredients.recipeID = resultSet4.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag5, '%') 
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
        $sql->bindParam('yesTag3', $yesTags[2]);
        $sql->bindParam('yesTag4', $yesTags[3]);
        $sql->bindParam('yesTag5', $yesTags[4]);

        $sql->bindParam('noTag1', $noTags[0]);
        $sql->bindParam('noTag2', $noTags[1]);
        $sql->bindParam('noTag3', $noTags[2]);
        $sql->bindParam('noTag4', $noTags[3]);



        $sql->execute();

        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }
    public function searchRecipesFiveYesFiveNo($yesTags, $noTags) {
        $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        WITH finalResultSet AS (
        
        WITH yesResultSet AS (
        WITH resultSet4 AS (
        WITH resultSet3 AS (
        WITH resultSet2 AS (
        WITH resultSet AS (SELECT * FROM `ingredients` WHERE `ingredient` LIKE concat('%', :yesTag1, '%'))
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet
        WHERE ingredients.recipeID = resultSet.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag2, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet2
        WHERE ingredients.recipeID = resultSet2.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag3, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet3
        WHERE ingredients.recipeID = resultSet3.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag4, '%') 
        )
        
        SELECT DISTINCT ingredients.recipeID FROM ingredients, resultSet4
        WHERE ingredients.recipeID = resultSet4.recipeID AND ingredients.ingredient LIKE concat('%', :yesTag5, '%') 
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
        $sql->bindParam('yesTag3', $yesTags[2]);
        $sql->bindParam('yesTag4', $yesTags[3]);
        $sql->bindParam('yesTag5', $yesTags[4]);

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
