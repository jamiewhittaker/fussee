<?php
/**
 * Controller for retrieving the parameters from the CircuitStatus class and sending it over to the database
 */


namespace App\Controllers;

use DateTime;
use Exception;
use PDO;
use App\Models\CircuitStatus;
use App\Controllers\MetadataValidator;


class DatabaseWrapper
{
    private $database;

    public function insertBoardStatus($status)
    {
        $val = new MetadataValidator();
        if (!$val->checkDateRecieved($status)) {

            try {

                /**
                 * Create a new PDO object connecting to the database
                 */
                $this->database = new PDO('mysql:host='. db_host .';dbname='. db_name,db_username, db_password);

                $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                /**
                 * Getting the metadata and circuit board status using get methods from the CircuitStatus class
                 * to store in variables.
                 */
                $date = $status->getDate();
                $name = $status->getName();
                $email = $status->getEmail();
                $msisdn = $status->getMsisdn();
                $s1 = $status->getSwitch1();
                $s2 = $status->getSwitch2();
                $s3 = $status->getSwitch3();
                $s4 = $status->getSwitch4();
                $fan = $status->getFan();
                $temp = $status->getTemp();
                $keypad = $status->getKeypad();


                /**
                 * Preparing sql statement which will be used to insert data to the board_status table
                 */
                $sql = $this->database->prepare("INSERT INTO `board_status` (date, name, email, msisdn, switch1, switch2, switch3, switch4, fan, temp, keypad)
            VALUES (:date, :name , :email, :msisdn, :switch1, :switch2, :switch3, :switch4, :fan, :temp, :keypad)");

                /**
                 * Binding parameters so that they can be used in the sql statement which was prepared
                 */

                $sql->bindParam(':date', $date);
                $sql->bindParam(':email', $email);
                $sql->bindParam(':name', $name);
                $sql->bindParam(':msisdn', $msisdn);
                $sql->bindParam(':switch1', $s1);
                $sql->bindParam(':switch2', $s2);
                $sql->bindParam(':switch3', $s3);
                $sql->bindParam(':switch4', $s4);
                $sql->bindParam(':fan', $fan);
                $sql->bindParam(':temp', $temp);
                $sql->bindParam(':keypad', $keypad);

                /**
                 * If execute() returns false a new exception will be thrown
                 */
                if (
                    $sql->execute() === false) {
                    throw new exception("Unable to add to database");
                }

            } catch (\PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }
    }

}
