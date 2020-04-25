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

    public function insertUser()
    {
        $user = $this->user = new User(
            trim($_POST['registerFirstName']),
            trim($_POST['registerPassword']),
            trim($_POST['registerEmail']));

        $firstName = $user->getFirstName();
        $password = $user->getPassword();
        $email = $user->getEmail();

        if (preg_match('/[\'^£$%&*()}{#~?><>,|=_+¬-]/ ', $email)) {
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


            try {
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

            } catch (\PDOException $e) {
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

                            if($row['isAdmin'] > 0) {
                                $_SESSION['isAdmin'] = TRUE;
                            }

                            $_SESSION['loggedIn'] = TRUE;
                            return TRUE;
                        } else {
                            return "Incorrect email address or password, please try again.";
                        }
                    } else {
                        return "Incorrect email address or password, please try again.";
                    }
                } catch (PDOException $e) {
                    echo "Error : " . $e->getMessage();
                }
            }
        }
    }

    public function changeEmail()
    {
        $email = $_SESSION['email'];
        $newEmail = trim($_POST['newEmail']);
        $password = trim($_POST['password']);

        if (preg_match('/[\'^£$%&*()}{#~?><>,|=_+¬-]/ ', $newEmail)) {
            return "Account information cannot contain special characters";
        } elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/ ', $password)) {
            return "Account information cannot contain special characters";
        } elseif (isset($newEmail) === true && $newEmail === '') {
            return "Please enter an email address";
        } elseif (isset($password) === true && $password === '') {
            return "Please enter a password";
        } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            return "Please enter a valid email address";
        } else {

            $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
            $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $findStatement = "SELECT COUNT(*) FROM `users` WHERE email = :newEmail";

            $sql = $this->database->prepare($findStatement);

            $sql->bindParam(':newEmail', $newEmail);

            if ($sql->execute() === false) {
                throw new \PDOException("Error");
            } else {
                $count = $sql->fetchColumn();

                if ($count > 0) {
                    return "An account is already registered using that email address";
                } else {

                    if ($newEmail != "" && $password != "") {
                        $query = "select * from `users` where `email`=:email";
                        $sql = $this->database->prepare($query);
                        $sql->bindParam('email', $email, PDO::PARAM_STR);
                        $sql->execute();
                        $count = $sql->rowCount();
                        $row = $sql->fetch(PDO::FETCH_ASSOC);

                        if ($count == 1 && !empty($row)) {
                            if (password_verify($password, $row['password'])) {
                                $query = "UPDATE `users` SET email = :newEmail WHERE email= :email";
                                $sql = $this->database->prepare($query);
                                $sql->bindParam('email', $email, PDO::PARAM_STR);
                                $sql->bindParam('newEmail', $newEmail, PDO::PARAM_STR);
                                $sql->execute();
                                $_SESSION['email'] = $newEmail;
                                return TRUE;
                            } else {
                                return "Incorrect password, please try again.";
                            }
                        } else {
                            return "Incorrect password, please try again.";
                        }
                    }
                }
            }
        }
    }

    public function changePassword()
    {
        $email = $_SESSION['email'];
        $oldPassword = trim($_POST['oldPassword']);
        $newPassword = trim($_POST['newPassword']);

        if (preg_match('/[\'^£$%&*()}{#~?><>,|=_+¬-]/ ', $oldPassword)) {
            return "Account information cannot contain special characters";
        } elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/ ', $newPassword)) {
            return "Account information cannot contain special characters";
        } elseif (isset($oldPassword) === true && $oldPassword === '') {
            return "Please enter a password";
        } elseif (isset($newPassword) === true && $newPassword === '') {
            return "Please enter a password";
        } else {

                $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
                $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                if ($email != "" && $oldPassword != "") {
                        $query = "select * from `users` where `email`=:email";
                        $sql = $this->database->prepare($query);
                        $sql->bindParam('email', $email, PDO::PARAM_STR);
                        $sql->execute();
                        $count = $sql->rowCount();
                        $row = $sql->fetch(PDO::FETCH_ASSOC);

                        if ($count == 1 && !empty($row)) {
                            if (password_verify($oldPassword, $row['password'])) {
                                $query = "UPDATE `users` SET password = :newPassword WHERE email = :email";
                                $sql = $this->database->prepare($query);
                                $sql->bindParam('newPassword', password_hash($newPassword, PASSWORD_DEFAULT), PDO::PARAM_STR);
                                $sql->bindParam('email', $_SESSION['email'], PDO::PARAM_STR);
                                $sql->execute();
                                return TRUE;
                            } else {
                                return "Incorrect password, please try again.";
                            }
                        } else {
                            return "Incorrect password, please try again.";
                        }
                }
        }
    }

    public function changeFirstName()
    {
        $newFirstName = trim($_POST['newFirstName']);

        if (preg_match('/[\'^£$%&*()}{#~?><>,|=_+¬-]/ ', $newFirstName)) {
            return "Account information cannot contain special characters";
        } elseif (isset($newFirstName) === true && $newFirstName === '') {
            return "Please enter a first name";
        } else {
            $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
            $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "UPDATE `users` SET firstName = :newFirstName WHERE email = :email";
            $sql = $this->database->prepare($query);
            $sql->bindParam('newFirstName', $newFirstName, PDO::PARAM_STR);
            $sql->bindParam('email', $_SESSION['email'], PDO::PARAM_STR);
            $sql->execute();
            $_SESSION['firstName'] = $newFirstName;
            return TRUE;
        }
    }

    public function makeAdmin(){
        $email = trim($_POST['email']);

        if (preg_match('/[\'^£$%&*()}{#~?><>,|=_+¬-]/ ', $email)) {
            return "Account information cannot contain special characters";
        } elseif (isset($email) === true && $email === '') {
            return "Please enter an email address";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Please enter a valid email address";
        } else {
            $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
            $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "select * from `users` where `email`=:email";
            $sql = $this->database->prepare($query);
            $sql->bindParam('email', $email, PDO::PARAM_STR);
            $sql->execute();
            $count = $sql->rowCount();

            if ($count > 0) {
                $query = "UPDATE `users` SET isAdmin = 1 WHERE email = :email";
                $sql = $this->database->prepare($query);
                $sql->bindParam('email', $email, PDO::PARAM_STR);
                $sql->execute();
                return TRUE;
            }
            else{
                return "User does not exist";
            }
        }
    }

    public function removeAdmin(){
        $email = trim($_POST['email']);

        if (preg_match('/[\'^£$%&*()}{#~?><>,|=_+¬-]/ ', $email)) {
            return "Account information cannot contain special characters";
        } elseif (isset($email) === true && $email === '') {
            return "Please enter an email address";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Please enter a valid email address";
        } else {
            $this->database = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_username, db_password);
            $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "select * from `users` where `email`=:email";
            $sql = $this->database->prepare($query);
            $sql->bindParam('email', $email, PDO::PARAM_STR);
            $sql->execute();
            $count = $sql->rowCount();

            if ($count > 0) {
                $query = "UPDATE `users` SET isAdmin = 0 WHERE email = :email";
                $sql = $this->database->prepare($query);
                $sql->bindParam('email', $email, PDO::PARAM_STR);
                $sql->execute();
                return TRUE;
            }
            else{
                return "User does not exist";
            }
        }
    }
}
