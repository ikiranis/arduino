<?php
/**
 * File: UsersManagement.php
 * Project: arduinoDB
 * Created by rocean
 * Date: 24/04/16
 * Time: 21:45
 */

require_once ('login.php');
require_once ('libraries/Page.php');
require_once ('libraries/Session.php');
require_once ('libraries/RoceanDB.php');

session_start();

$UsersPage = new Page();

// Τίτλος της σελίδας
$UsersPage->tittle = "Arduino Users Page";


$UsersPage->showHeader();

if (isset($_SESSION["username"]))
    echo 'User Loged in: '.$_SESSION["username"];

if (isset($_GET['RegisterUser']))
    ShowRegisterUser();
else {
    $CheckDB = new RoceanDB();
    if($CheckDB->CheckIfThereIsUsers())
        DisplayUsers();
    else ShowRegisterUser();
}


?>

<div id="InsertNewUser">
    <a href="UsersManagement.php?RegisterUser=true">Πρόσθεσε νέο χρήστη</a>
</div>

<?php

$UsersPage->showFooter();
