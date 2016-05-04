<?php

/**
 * File: RoceanDB.php
 * Created by rocean
 * Date: 17/04/16
 * Time: 01:17
 * DB Class
 * Info για την κρυπτογράφηση στην σελίδα http://php.net/manual/en/faq.passwords.php
 */


class RoceanDB
{

    public static $connStr = CONNSTR;
    public static $DBuser = DBUSER;
    public static $DBpass = DBPASS;

    public static $conn = NULL;

    private static $KeyForPasswords=PRIVATE_KEY;
    


    // Εκτελεί ένα sql query
    function ExecuteSQL($sql, $sqlParams)
    {

        $this->CreateConnection();

        $stmt = self::$conn->prepare($sql);

        $stmt->execute($sqlParams);

        $inserted_id=self::$conn->lastInsertId();

        return $inserted_id;

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

        $sql='SELECT * FROM user WHERE username=?';

        $stmt = self::$conn->prepare($sql);

        $stmt->execute(array($username));



        if($item=$stmt->fetch(PDO::FETCH_ASSOC))
        {
            $HashThePassword=$password.self::$KeyForPasswords;

            $sql='SELECT * FROM salts WHERE user_id=?';
            $salt = self::$conn->prepare($sql);
            $salt->execute(array($item['user_id']));

            if($salt_item=$salt->fetch(PDO::FETCH_ASSOC)) {
                $combined_password=$salt_item['algo'].$salt_item['cost'].$salt_item['salt'].$item['password'];
                echo '<p>hashed pass '.$HashThePassword.'</p>';
                echo '<p>combined pass '.$combined_password.'</p>';

            }

            if (password_verify($HashThePassword, $combined_password)) {


                $_SESSION["username"]=$item['username'];

                echo '<p>Βρέθηκε ο χρήστης: '.$_SESSION["username"].'</p>';

    
            }
            else echo "Λάθος Password";

        }
        else echo 'Δεν υπάρχεις';




    }

    function CreateUser($username, $email, $password, $agent)
    {
        $this->CreateConnection();

        $sql = 'INSERT INTO user(username, email, password, agent) VALUES(?,?,?,?)';
        
        $crypto = new Crypto();

        $hashed_array=$crypto->EncryptPassword($password);

        echo '<p>'.$hashed_array['hashed_password'].' | '.$hashed_array['algo'].' | '.$hashed_array['cost'].' | '.$hashed_array['salt'].'</p>';

        $EncryptedPassword=$hashed_array['hashed_password'];

        $arrayParams = array($username, $email, $EncryptedPassword, $agent);

        if($inserted_id=$this->ExecuteSQL($sql, $arrayParams)) {
            $sql = 'INSERT INTO salts(user_id, salt, algo, cost) VALUES(?,?,?,?)';

            $saltArray = array($inserted_id, $hashed_array['salt'], $hashed_array['algo'], $hashed_array['cost'] );

            $this->ExecuteSQL($sql, $saltArray);

        }

        echo "You are sign in";
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



}

