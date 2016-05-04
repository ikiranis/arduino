<?php

/**
 * File: Crypto.php
 * Created by rocean
 * Date: 04/05/16
 * Time: 23:04
 * Class for Crypto Methods
 */

class Crypto
{
    private static $KeyForPasswords=PRIVATE_KEY;

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

        $password_hashed=password_hash($HashThePassword, PASSWORD_BCRYPT, $options);

        $password_algo = substr($password_hashed, 0, 4);
        $password_cost = substr($password_hashed, 4, 3);
        $salt = substr($password_hashed, 7, 22);
        $hashed_password = substr($password_hashed, 29, 31);

        $hashed_array = array('algo'=>$password_algo, 'cost'=>$password_cost, 'salt'=>$salt, 'hashed_password'=>$hashed_password);
        
        return $hashed_array;
    }
}