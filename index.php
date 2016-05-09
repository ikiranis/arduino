<?php

/**
 * File: index.php
 * Created by rocean
 * Date: 17/04/16
 * Time: 01:17
 */

require_once ('libraries/common.inc.php');
require_once ('login.php');

session_start();


$MainPage = new Page();

// Τίτλος της σελίδας
$MainPage->tittle = "Arduino";


$MainPage->showHeader();

$conn= new RoceanDB();

// Έλεγχος αν υπάρχει cookie. Αν δεν υπάρχει ψάχνει session
if(!$conn->CheckCookiesForLoggedUser()) {
    if (isset($_SESSION["username"]))
    {
        session_regenerate_id(true);

        echo '<p>User Logged in: '.$crypt->DecryptText($_SESSION["username"]).'</p>';


    }
}
else echo '<p>User Logged in: '.$_COOKIE["username"].'</p>';



showLoginWindow();

$MainPage->showFooter();



?>