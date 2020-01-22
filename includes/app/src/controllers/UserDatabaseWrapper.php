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
            $_POST['registerUsername'],
            $_POST['registerPassword'],
            $_POST['registerEmail']);

        $username = $user->getUsername();
        $password = $user->getPassword();
        $email = $user->getEmail();

        try{
            $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
            $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $findStatement = "SELECT COUNT(*) FROM `users` WHERE username = :username OR email = :email";

            $sql = $this->database->prepare($findStatement);

            $sql->bindParam(':username', $username);
            $sql->bindParam(':email', $email);

            if ($sql->execute() === false) {
                throw new \PDOException("Error");
            } else {
                $count = $sql->fetchColumn();

                if ($count > 0) {
                    return false;
                } else {

                    $insertStatement = "INSERT into `users` (username, password, email) VALUES (:username, :password, :email)";

                    $sql = $this->database->prepare($insertStatement);

                    $sql->bindParam(':username', $username);
                    $sql->bindParam(':password', $password);
                    $sql->bindParam(':email', $email);

                    if ($sql->execute() === false) {
                        throw new \PDOException ("Error");
                    }

                    return true;
                }
            }

        } catch (\PDOException $e){
            echo $e->getMessage();
        }

    }




}
