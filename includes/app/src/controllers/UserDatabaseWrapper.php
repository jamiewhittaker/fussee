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
            trim($_POST['registerFirstName']),
            trim($_POST['registerPassword']),
            trim($_POST['registerEmail']));

        $firstName = $user->getFirstName();
        $password = $user->getPassword();
        $email = $user->getEmail();

        if (preg_match('/[\'^£$%&*()}{#~?><>,|=_+¬-]/ ', $email))
        {
            return "Account information cannot contain special characters";
        } elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/ ', $password)) {
            return "Account information cannot contain special characters";
        } elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/ ', $firstName)) {
            return "Account information cannot contain special characters";
        } elseif (isset($email) === true && $email === '') {
            return "Please enter an email address";
        } elseif (isset($password) === true && $password === '') {
            return "Please enter a password";
        } elseif (isset($firstName) === true && $firstName === '') {
            return "Please enter your first name";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Please enter a valid email address";
        } else {


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
                    return "An account is already registered using that email address";
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

    public function findUser()
    {
        $email = trim($_POST['loginEmail']);
        $password = trim($_POST['loginPassword']);

        if (preg_match('/[\'^£$%&*()}{#~?><>,|=_+¬-]/ ', $email)) {
            return "Account information cannot contain special characters";
        } elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/ ', $password)) {
            return "Account information cannot contain special characters";
        } elseif (isset($email) === true && $email === '') {
            return "Please enter an email address";
        } elseif (isset($password) === true && $password === '') {
            return "Please enter a password";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Please enter a valid email address";
        } else {

            $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
            $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if ($email != "" && $password != "") {
                try {
                    $query = "select * from `users` where `email`=:email";
                    $sql = $this->database->prepare($query);
                    $sql->bindParam('email', $email, PDO::PARAM_STR);
                    $sql->execute();
                    $count = $sql->rowCount();
                    $row = $sql->fetch(PDO::FETCH_ASSOC);

                    if ($count == 1 && !empty($row)) {
                        if (password_verify($password, $row['password'])) {
                            session_start();
                            $_SESSION['email'] = $row['email'];
                            $_SESSION['firstName'] = $row['firstName'];
                            $_SESSION['loggedIn'] = TRUE;
                            return TRUE;
                        } else {
                            return "Incorrect email address or password, please try again.";
                        }
                    }
                } catch (PDOException $e) {
                    echo "Error : " . $e->getMessage();
                }
            }
        }
    }

}
