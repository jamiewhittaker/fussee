<?php
/**
 * Created by PhpStorm.
 * User: jamiewhittaker
 * Date: 2020-01-22
 * Time: 13:50
 */

namespace App\models;

class User{

    private $firstName;
    private $password;
    private $email;

    public function __construct($firstName,$password,$email){
        $this->firstName = $firstName;
        $this->password = $password;
        $this->email = $email;
    }
    public function getFirstName() {
        return $this->firstName;
    }
    public function getPassword() {
        return $this->password;
    }
    public function getEmail(){
        return $this->email;
    }
}