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

if (isset($_SESSION["username"]))
{
    session_regenerate_id(true);

    echo '<p>User Loged in: '.$_SESSION["username"].'</p>';


}


showLoginWindow();

$MainPage->showFooter();



?>