<?php
/**
 * Controller for retrieving the parameters from the CircuitStatus class and sending it over to the database
 */


namespace App\controllers;

use PDO;
use App\models\User;


class UserDatabaseWrapper
{
    private $database;
    private $user;

    public function insertUser(){
        $user = $this->user = new User(
            $_POST['registerFirstName'],
            $_POST['registerPassword'],
            $_POST['registerEmail']);

        $firstName = $user->getFirstName();
        $password = $user->getPassword();
        $email = $user->getEmail();

        try{
            $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
            $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $findStatement = "SELECT COUNT(*) FROM `users` WHERE email = :email";

            $sql = $this->database->prepare($findStatement);

            $sql->bindParam(':email', $email);

            if ($sql->execute() === false) {
                throw new \PDOException("Error");
            } else {
                $count = $sql->fetchColumn();

                if ($count > 0) {
                    return false;
                } else {

                    $insertStatement = "INSERT into `users` (firstName, password, email) VALUES (:firstName, :password, :email)";

                    $sql = $this->database->prepare($insertStatement);

                    $sql->bindParam(':firstName', $firstName);
                    $sql->bindParam(':password', password_hash($password, PASSWORD_DEFAULT));
                    $sql->bindParam(':email', $email);

                    if ($sql->execute() === false) {
                        throw new \PDOException ("Error");
                    }

                    session_start();
                    $_SESSION["email"] = $user->getEmail();
                    $_SESSION["firstName"] = $user->getFirstName();
                    $_SESSION["loggedIn"] = TRUE;

                    return true;

                }
            }

        } catch (\PDOException $e){
            echo $e->getMessage();
        }

    }




}
