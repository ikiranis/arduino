<?php

/**
 * File: RoceanDB.php
 * Created by rocean
 * Date: 17/04/16
 * Time: 01:17
 * DB Class
 * Info για την κρυπτογράφηση στην σελίδα http://php.net/manual/en/faq.passwords.php
 */

//require_once ('Session.php');

class RoceanDB
{

    public static $connStr = 'mysql:host=localhost;dbname=arduino_db';
    public static $DBuser = 'root';
    public static $DBpass = 'documents2015';

    public static $conn = NULL;

    private static $KeyForPasswords='ckY85^8nL%W4U5&38Zb0';


    // Εκτελεί ένα sql query
    function ExecuteSQL($sql, $sqlParams)
    {

        $this->CreateConnection();

        $stmt = self::$conn->prepare($sql);

        $stmt->execute($sqlParams);

    }

    // Ψάχνει αν υπάρχουν users στηνν βάση. Επιστρέφει true or false.
    function CheckIfThereIsUsers () {

        $this->CreateConnection();

        $sql='SELECT user_id FROM user';

        $stmt = self::$conn->prepare($sql);

        $stmt->execute();

        if($item=$stmt->fetch(PDO::FETCH_ASSOC))
            return true;
        else return false;

    }

    // Ελέγχει αν ο χρήστης υπάρχει στην βάση και είναι σωστά τα username, password που έχει δώσει
    function CheckLogin($username, $password) {

        $this->CreateConnection();

        $sql='SELECT username, password, email FROM user WHERE username=?';

        $stmt = self::$conn->prepare($sql);

        $stmt->execute(array($username));



        if($item=$stmt->fetch(PDO::FETCH_ASSOC))
        {
            $HashThePassword=$password.self::$KeyForPasswords;

            if (password_verify($HashThePassword, $item['password'])) {

                $_SESSION["username"]=$item['username'];

                echo '<p>Βρέθηκε ο χρήστης: '.$_SESSION["username"].'</p>';

    
            }
            else echo "Λάθος Password";

        }
        else echo 'Δεν υπάρχεις';




    }

    // Άνοιγμα της σύνδεσης στην βάση
    function CreateConnection(){
        if (!self::$conn) {
            try {
                self::$conn = new PDO(self::$connStr, self::$DBuser, self::$DBpass,
                    array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
            } catch (PDOException $pe) {
                die('Could not connect to the database because: ' .
                    $pe->getMessage());
            }
        }
    }

    // Υπολογίζει το cost για την κρυπτογράφηση
    function FindCost () {
        $timeTarget = 0.05; // 50 milliseconds 

        $cost = 8;
        do {
            $cost++;
            $start = microtime(true);
            password_hash("test", PASSWORD_BCRYPT, ["cost" => $cost]);
            $end = microtime(true);
        } while (($end - $start) < $timeTarget);

        return $cost;
    }

    // Κρυπτογραφεί ένα password προστέθοντας σε αυτό και ένα έξτρα key self::$KeyForPasswords
    function EncryptPassword ($password) {
        $cost=$this->FindCost();

        $options = ['cost' => $cost];

        $HashThePassword=$password.self::$KeyForPasswords;
        
        return password_hash($HashThePassword, PASSWORD_BCRYPT, $options);
    }

}

