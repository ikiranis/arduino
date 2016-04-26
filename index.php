<?php

/**
 * File: index.php
 * Project: arduino
 * Created by rocean
 * Date: 17/04/16
 * Time: 01:17
 */

require_once ('login.php');
require_once ('libraries/Page.php');
require_once ('libraries/Session.php');
require_once ('libraries/RoceanDB.php');

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

print_r(get_included_files());

?>