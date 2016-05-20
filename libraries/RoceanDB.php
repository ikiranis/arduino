<?php

/**
 * File: RoceanDB.php
 * Created by rocean
 * Date: 17/04/16
 * Time: 01:17
 * DB Class
 */


class RoceanDB
{

    public static $connStr = CONNSTR;
    public static $DBuser = DBUSER;
    public static $DBpass = DBPASS;

    public static $conn = NULL;


    // 30 μέρες
    private static $CookieTime=60*60*24*30;
    


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
    function CheckLogin($username, $password, $SavePassword) {

        $this->CreateConnection();

        $sql='SELECT * FROM user WHERE username=?';

        $stmt = self::$conn->prepare($sql);

        $stmt->execute(array($username));


        // Αν ο χρήστης username βρεθεί. Αν υπάρχει δηλαδή στην βάση μας
        if($item=$stmt->fetch(PDO::FETCH_ASSOC))
        {
            $crypto = new Crypto();

            // Προσθέτει το string στο password που έδωσε ο πιθανός χρήστης
            $HashThePassword=$password.Crypto::$KeyForPasswords;

            $sql='SELECT * FROM salts WHERE user_id=?';
            $salt = self::$conn->prepare($sql);
            $salt->execute(array($item['user_id']));

            // Φέρνει το salt από τον πίνακα salts για τον συγκεκριμένο χρήστη. Ενώνει τα 4 κομμάτια του hashed password
            // που είχαμε σπάσει στο αρχικό του ενιαίο
            if($salt_item=$salt->fetch(PDO::FETCH_ASSOC)) {
                $combined_password=$salt_item['algo'].$salt_item['cost'].$salt_item['salt'].$item['password'];

                // Κρατάμε το salt για χρήση παρακάτω
                $user_salt=$salt_item['salt'];

            }

            // Κάνει τον έλεγχο του ενωμένου, πλέον, hashed password με τον hashed password που έδωσε ο πιθανός χρήστης
            // Αν ταιριάζουν τότε ο χρήστης γίνεται authenticated. Αλλιώς επιστρέφει "Λάθος password"
            if (password_verify($HashThePassword, $combined_password)) {



                // Αν ο χρήστης έχει επιλέξει να τον θυμάται η εφαρμογή ότι είναι logged in
                if($SavePassword) {

                    // Χρησιμοποιούμε 2 cookies. Στο ένα έχουμε το username και στο άλλο το salt του χρήστη
                    // Τα Cookies θα μείνουν ανοιχτά για self::$CookieTime χρόνο
                    setcookie('username', $item['username'], time()+self::$CookieTime);
                    setcookie('salt', $user_salt, time()+self::$CookieTime);


                }
                else {
                    if (isset($_COOKIE['username'])) {
                        unset($_COOKIE['username']);
                        setcookie('username','',-1);
                        unset($_COOKIE['salt']);
                        setcookie('salt','',-1);
                    }
                }

                $_SESSION["username"]=$crypto->EncryptText($item['username']);

                // Επιστρέφει την επιτυχία (ή όχι) στο array $result με ανάλογο μήνυμα
                $result = array ('success'=>true, 'message'=>__('user_is_founded'));
                return $result;
//                echo '<p>Βρέθηκε ο χρήστης: '.$crypto->DecryptText($_SESSION["username"]).'</p>';


            }
            else {
                $result = array ('success'=>false, 'message'=>__('wrong_password'));
                return $result;
            }

        }
        else {
            $result = array ('success'=>false, 'message'=>__('user_dont_exist'));
            return $result;
        }




    }

    // Εισάγει τον νέο χρήστη στην βάση
    function CreateUser($username, $email, $password, $agent)
    {
        $this->CreateConnection();

        $sql = 'INSERT INTO user(username, email, password, agent) VALUES(?,?,?,?)';
        
        $crypto = new Crypto();

        $hashed_array=$crypto->EncryptPassword($password);

//        echo '<p>'.$hashed_array['hashed_password'].' | '.$hashed_array['algo'].' | '.$hashed_array['cost'].' | '.$hashed_array['salt'].'</p>';

        $EncryptedPassword=$hashed_array['hashed_password'];

        $arrayParams = array($username, $email, $EncryptedPassword, $agent);

        if($inserted_id=$this->ExecuteSQL($sql, $arrayParams)) {
            $sql = 'INSERT INTO salts(user_id, salt, algo, cost) VALUES(?,?,?,?)';

            $saltArray = array($inserted_id, $hashed_array['salt'], $hashed_array['algo'], $hashed_array['cost'] );

            $this->ExecuteSQL($sql, $saltArray);

        }

        return true;
    }

    // Έλεγχος αν ο χρήστης είναι logged id, αν υπάρχουν cookies. Η function επιστρέφει true or false
    function CheckCookiesForLoggedUser() {

        if (isset($_COOKIE['username'])) {

            $this->CreateConnection();

            // Ψάχνουμε να βρούμε το id του user με το συγκεκριμένο username που έχει στο cookie
            $sql='SELECT user_id FROM user WHERE username=?';

            $stmt = self::$conn->prepare($sql);

            $stmt->execute(array($_COOKIE['username']));

            // Αν βρεθεί το id του user ψάχνουμε τα salt του
            if($item=$stmt->fetch(PDO::FETCH_ASSOC))
            {

                $sql='SELECT salt FROM salts WHERE user_id=?';
                $salt = self::$conn->prepare($sql);
                $salt->execute(array($item['user_id']));

                // Παίρνουμε το salt και το συγκρίνουμε με αυτό που έχει το σχετικό cookie
                if($salt_item=$salt->fetch(PDO::FETCH_ASSOC)) {
                    if($salt_item['salt']==$_COOKIE['salt']) {
                        $crypto= new Crypto();
                        $_SESSION['username']=$crypto->EncryptText($_COOKIE['username']); // Ανοίγει το session για τον συγκεκριμένο χρήστη
                        return true;
                    }
                    else return false;
                }
                // Η function επιστρέφει true αν συμφωνεί το salt που έχει στο cookie, με αυτό που υπάρχει στην βάση
                // Αλλιώς επιστρέφει false
            }
            else return false;
            
        }
        else return false;

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

